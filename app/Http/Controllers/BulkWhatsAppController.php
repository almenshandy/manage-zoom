<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBulkWhatsAppRequest;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\WhatsAppBulkBatch;
use App\Models\WhatsAppBulkMessage;
use App\Models\ZoomBooking;
use App\Services\BulkWhatsAppService;
use App\Services\WhatsAppService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BulkWhatsAppController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->is_active, 403);
        $input = $request->validate(['batch' => ['nullable', 'integer', 'min:1']]);
        $focusedBatchId = isset($input['batch']) ? (int) $input['batch'] : null;
        $query = WhatsAppBulkBatch::where('user_id', $request->user()->id);
        $page = null;
        if ($focusedBatchId !== null) {
            (clone $query)->findOrFail($focusedBatchId);
            if (! $request->has('page')) {
                $page = intdiv((clone $query)->where('id', '>', $focusedBatchId)->count(), 15) + 1;
            }
        }
        $bulkBatches = $query->orderByDesc('id')->paginate(15, ['*'], 'page', $page)->withQueryString();

        return view('bookings.bulk-whatsapp-index', compact('bulkBatches', 'focusedBatchId'));
    }

    public function create(Request $request, BulkWhatsAppService $service): View
    {
        $input = $request->validate([
            'booking_ids' => ['sometimes', 'array', 'max:50'],
            'booking_ids.*' => ['required', 'integer', 'distinct'],
        ]);
        $bookings = $service->selectedBookings($request->user(), $input['booking_ids'] ?? []);
        $bulkPreviews = $bookings->map(fn (ZoomBooking $booking): array => [
            'id' => $booking->id,
            'message' => $service->invitation($booking),
            'account_preview' => $service->canIncludeAccount($booking)
                ? "\n\nAKUN HOST ZOOM:\nNama: ".$booking->account->name."\nEmail: ".$booking->account->email."\nPassword: ********"
                : null,
        ])->values();
        $canIncludeAccounts = $bookings->isNotEmpty() && $bookings->every(fn (ZoomBooking $booking): bool => $service->canIncludeAccount($booking));
        $accountIssues = $bookings->mapWithKeys(fn (ZoomBooking $booking): array => [$booking->id => $service->accountUnavailableReason($booking)]);
        $bulkCountryCode = SystemSetting::get('wa_country_code', '62') ?: '62';

        return view('bookings.bulk-whatsapp-create', compact('bookings', 'bulkPreviews', 'canIncludeAccounts', 'accountIssues', 'bulkCountryCode'));
    }

    public function contacts(Request $request, WhatsAppService $whatsApp): JsonResponse
    {
        abort_unless($request->user()->is_active, 403);
        $input = $request->validate(['search' => ['nullable', 'string', 'max:100']]);
        $query = User::query()->where('is_active', true)->whereNotNull('no_hp')->where('no_hp', '!=', '');
        if (! $request->user()->isAdmin()) {
            $query->whereKey($request->user()->id);
        }
        if (filled($input['search'] ?? null)) {
            $search = $input['search'];
            $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', '%'.$search.'%')->orWhere('no_hp', 'like', '%'.$search.'%');
            });
        }
        $contacts = $query->orderBy('name')->orderBy('id')->paginate(20, ['id', 'name', 'no_hp']);

        return response()->json([
            'contacts' => $contacts->getCollection()->map(function (User $user) use ($whatsApp): array {
                $phone = $whatsApp->formatTargetNumber($user->no_hp);

                return ['id' => $user->id, 'name' => $user->name, 'phone' => $phone,
                    'valid' => preg_match('/^\+?[0-9\s().-]+$/', trim($user->no_hp)) === 1 && preg_match('/^[1-9][0-9]{7,14}$/', $phone) === 1];
            })->values(),
            'page' => $contacts->currentPage(),
            'last_page' => $contacts->lastPage(),
            'total' => $contacts->total(),
        ]);
    }

    public function store(StoreBulkWhatsAppRequest $request, BulkWhatsAppService $service): JsonResponse
    {
        $batch = $service->enqueue($request->user(), $request->validated());

        return response()->json([
            'message' => 'Antrean bulk WhatsApp berhasil dibuat.',
            'status_url' => route('bookings.whatsapp-bulk.show', $batch),
            'queue_url' => route('bookings.whatsapp-bulk.index', ['batch' => $batch->id]),
            'total_messages' => $batch->total_messages,
            'next_request_id' => (string) Str::uuid(),
        ], 202);
    }

    public function show(Request $request, WhatsAppBulkBatch $batch): JsonResponse
    {
        abort_unless($batch->user_id === $request->user()->id, 404);
        $counts = $batch->messages()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $messages = $batch->messages()->orderBy('id')->paginate(50);

        return response()->json([
            'id' => $batch->id,
            'provider' => $batch->provider,
            'interval_seconds' => $batch->interval_seconds,
            'include_credentials' => $batch->include_credentials,
            'total' => $batch->total_messages,
            'pending' => (int) ($counts['queued'] ?? 0) + (int) ($counts['sending'] ?? 0),
            'accepted' => (int) ($counts['sent'] ?? 0),
            'failed' => (int) ($counts['failed'] ?? 0) + (int) ($counts['unknown'] ?? 0),
            'messages' => $messages->getCollection()->map(fn (WhatsAppBulkMessage $message): array => [
                'topic' => $message->topic,
                'recipient' => $message->recipient,
                'status' => $message->status,
                'status_label' => $message->status_label,
                'error' => $message->error,
            ])->values(),
            'page' => $messages->currentPage(),
            'last_page' => $messages->lastPage(),
        ]);
    }

    public function destroy(Request $request, WhatsAppBulkBatch $batch): JsonResponse
    {
        abort_unless($request->user()->is_active, 403);
        abort_unless($batch->user_id === $request->user()->id || $request->user()->isAdmin(), 403);

        $messageIds = $batch->messages()->pluck('id')->all();

        // Batalkan / update status pesan yang belum selesai
        $batch->messages()->whereIn('status', ['queued', 'sending'])->update([
            'status' => 'failed',
            'error' => 'Antrean dibatalkan / dihapus oleh pengguna.',
            'completed_at' => now(),
        ]);

        // Hapus job dari tabel queue jika masih tersimpan
        if (! empty($messageIds)) {
            DB::table('jobs')
                ->where('queue', 'whatsapp-bulk')
                ->where(function ($q) use ($messageIds) {
                    foreach ($messageIds as $id) {
                        $q->orWhere('payload', 'like', '%"messageId";i:'.$id.';%');
                    }
                })
                ->delete();
        }

        $batch->delete();

        return response()->json([
            'success' => true,
            'message' => 'Antrean bulk WhatsApp berhasil dihapus.',
        ]);
    }
}
