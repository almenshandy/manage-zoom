@extends('layouts.app')

@php
    $header = 'Master Proyek Kegiatan';
@endphp

@section('content')
<div class="space-y-4">
    <!-- Actions Toolbar -->
    <div class="mobile-list-toolbar bg-white p-3.5 rounded-sm border border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('projects.index') }}" class="mobile-filter-form flex flex-wrap items-center gap-2 flex-1">
            <div class="relative flex-1 sm:flex-initial">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari proyek, kode, dept..."
                    class="pl-8 pr-3 py-1.5 bg-white border border-gray-300 rounded-sm text-xs focus:outline-none focus:border-brand-600 w-full sm:w-56">
            </div>

            <select name="department_id" onchange="this.form.submit()" class="px-2.5 py-1.5 bg-white border border-gray-300 rounded-sm text-xs focus:outline-none focus:border-brand-600">
                <option value="">-- Semua Departemen --</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->nama_departemen }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-sm border border-gray-300 transition">
                Filter
            </button>

            @if(request()->hasAny(['search', 'department_id', 'status']))
                <a href="{{ route('projects.index') }}" class="px-2.5 py-1.5 bg-white hover:bg-gray-50 text-gray-600 text-xs rounded-sm border border-gray-300 transition" title="Reset filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </form>

        <div class="mobile-toolbar shrink-0">
            <button type="button" onclick="openCreateModal()" class="mobile-primary-action w-full md:w-auto inline-flex items-center justify-center gap-1.5 bg-brand-900 hover:bg-brand-950 text-white text-xs font-medium px-3.5 py-1.5 rounded-sm transition shadow-xs">
                <i class="fa-solid fa-plus text-[10px]"></i> Tambah Proyek Baru
            </button>
        </div>
    </div>

    @if($errors->any())
        <div class="p-3 bg-red-50 border border-red-200 rounded-sm text-xs text-red-700">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Projects Table -->
    <div class="mobile-data-container bg-white rounded-sm border border-gray-200 overflow-hidden">
        @include('projects.mobile-list')
        <div class="hidden md:block overflow-x-auto">
            <table class="mobile-card-table w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="px-3 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">Nama Proyek</th>
                        <th class="px-4 py-3">Departemen</th>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Periode Pelaksanaan</th>
                        <th class="px-4 py-3 text-center">Meeting Terkait</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($projects as $idx => $p)
                        <tr class="hover:bg-gray-50/75 transition-colors">
                            <td data-label="No" class="px-3 py-3 text-center font-mono text-gray-500 text-[11px]">
                                {{ $projects->firstItem() + $idx }}
                            </td>
                            <td data-label="Nama Proyek" class="px-4 py-3">
                                <span class="font-semibold text-gray-900 block text-xs">{{ $p->name }}</span>
                                @if($p->description)
                                    <p class="text-[11px] text-gray-500 line-clamp-1 mt-0.5">{{ $p->description }}</p>
                                @endif
                            </td>
                            <td data-label="Departemen" class="px-4 py-3">
                                @if($p->department)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-sm text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="fa-solid fa-sitemap text-[9px]"></i> {{ $p->department->nama_departemen }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic text-[11px]">Lintas / Umum</span>
                                @endif
                            </td>
                            <td data-label="Kode" class="px-4 py-3 font-mono text-xs text-gray-600">
                                {{ $p->code ?: '-' }}
                            </td>
                            <td data-label="Periode Pelaksanaan" class="px-4 py-3 whitespace-nowrap text-xs">
                                @if($p->start_date && $p->end_date)
                                    <span class="font-medium text-gray-800">{{ $p->start_date->format('d/m/Y') }} - {{ $p->end_date->format('d/m/Y') }}</span>
                                @elseif($p->start_date)
                                    <span class="font-medium text-gray-800">Mulai {{ $p->start_date->format('d/m/Y') }}</span>
                                @else
                                    <span class="text-gray-400 italic">Fleksibel / Tanpa Batas</span>
                                @endif
                            </td>
                            <td data-label="Meeting Terkait" class="px-4 py-3 text-center whitespace-nowrap">
                                <button type="button" onclick="openProjectMeetings(this)"
                                    data-url="{{ route('projects.meetings', $p) }}" data-project-name="{{ $p->name }}"
                                    aria-haspopup="dialog" aria-controls="projectMeetingsModal"
                                    aria-label="Lihat {{ $p->bookings_count }} meeting terkait proyek {{ $p->name }}"
                                    title="Lihat meeting terkait"
                                    class="inline-flex items-center gap-1 font-mono font-semibold px-2 py-0.5 rounded-sm bg-gray-100 text-gray-800 text-[11px] hover:bg-blue-50 hover:text-blue-700 transition">
                                    <i class="fa-solid fa-video text-[9px]" aria-hidden="true"></i> {{ $p->bookings_count }}
                                </button>
                            </td>
                            <td data-label="Status" class="px-4 py-3 text-center whitespace-nowrap">
                                @if($p->status === 'active')
                                    <span class="px-2 py-0.5 text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-sm">
                                        Aktif
                                    </span>
                                @elseif($p->status === 'completed')
                                    <span class="px-2 py-0.5 text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 rounded-sm">
                                        Selesai
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-600 border border-gray-200 rounded-sm">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td data-label="Aksi" class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="mobile-actions inline-flex items-center gap-1.5">
                                    <button type="button" onclick="openEditModal({{ json_encode($p) }})" 
                                        class="px-2 py-1 bg-white hover:bg-gray-50 text-gray-700 rounded-sm border border-gray-300 font-medium text-[11px] transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-pencil text-[10px] text-gray-500"></i> Edit
                                    </button>

                                    <form method="POST" action="{{ route('projects.destroy', $p) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data proyek ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 bg-white hover:bg-red-50 text-red-600 rounded-sm border border-red-200 font-medium text-[11px] transition inline-flex items-center gap-1">
                                            <i class="fa-solid fa-trash-can text-[10px]"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-briefcase text-3xl mb-2 text-gray-300 block"></i>
                                <span class="font-medium text-gray-600 block">Belum ada data master proyek.</span>
                                <span class="text-[11px] text-gray-400">Klik tombol "Tambah Proyek Baru" di atas untuk menambahkan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($projects->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah / Edit Proyek -->
<div id="projectModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-sm border border-gray-300 max-w-lg w-full max-h-[92dvh] flex flex-col shadow-lg overflow-hidden animate-fadeIn">
        <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between bg-white shrink-0">
            <h4 id="modalTitle" class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Tambah Master Proyek</h4>
            <button type="button" onclick="closeProjectModal()" class="w-7 h-7 rounded-sm flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form id="projectForm" method="POST" action="{{ route('projects.store') }}" class="p-5 space-y-3.5 text-xs min-h-0 overflow-y-auto">
            @csrf
            <div id="methodSpoof"></div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">Nama Proyek / Pelaksanaan <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="input_name" required placeholder="Contoh: Asesmen Bank Mandiri 2026"
                    class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded-sm focus:outline-none focus:border-brand-600">
            </div>

            <!-- Pemilihan Departemen Penanggung Jawab Proyek -->
            <div>
                <label class="block font-medium text-gray-700 mb-1">
                    Departemen / Unit Kerja <span class="text-gray-400 font-normal">(Opsional)</span>
                </label>
                <select name="department_id" id="input_department_id" class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded-sm focus:outline-none focus:border-brand-600">
                    <option value="">-- Tidak Terikat Departemen (Lintas / Umum) --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">
                            {{ $dept->nama_departemen }} ({{ $dept->kode_departemen }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-gray-500 mt-1">Pilih departemen yang menaungi proyek agar statistik proyek per unit dapat terhitung.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Tanggal Mulai Pelaksanaan</label>
                    <input type="date" name="start_date" id="input_start_date"
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded-sm focus:outline-none focus:border-brand-600 text-xs">
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Tanggal Selesai Pelaksanaan</label>
                    <input type="date" name="end_date" id="input_end_date"
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded-sm focus:outline-none focus:border-brand-600 text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Kode Proyek</label>
                    <input type="text" name="code" id="input_code" placeholder="Misal: PRJ-2026-01"
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded-sm focus:outline-none focus:border-brand-600 font-mono text-xs">
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Status Proyek <span class="text-red-500">*</span></label>
                    <select name="status" id="input_status" required class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded-sm focus:outline-none focus:border-brand-600">
                        <option value="active">Aktif</option>
                        <option value="completed">Selesai</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">Deskripsi / Keterangan Proyek</label>
                <textarea name="description" id="input_description" rows="2.5" placeholder="Keterangan singkat kegiatan..."
                    class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded-sm focus:outline-none focus:border-brand-600"></textarea>
            </div>

            <div class="pt-3 border-t border-gray-200 flex items-center justify-end gap-2">
                <button type="button" onclick="closeProjectModal()" class="px-4 py-1.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-sm hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 bg-brand-900 hover:bg-brand-950 text-white font-medium rounded-sm transition">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<div id="projectMeetingsModal" role="dialog" aria-modal="true" aria-labelledby="projectMeetingsTitle" aria-describedby="projectMeetingsName"
    class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-sm border border-gray-300 max-w-4xl w-full shadow-lg overflow-hidden flex flex-col max-h-[90vh]">
        <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between gap-3 bg-white">
            <div>
                <h4 id="projectMeetingsTitle" class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Meeting Terkait</h4>
                <p id="projectMeetingsName" class="text-xs text-gray-500 mt-0.5"></p>
            </div>
            <button id="closeProjectMeetingsBtn" type="button" onclick="closeProjectMeetings()" aria-label="Tutup daftar meeting"
                class="w-7 h-7 rounded-sm flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition shrink-0">
                <i class="fa-solid fa-xmark text-sm" aria-hidden="true"></i>
            </button>
        </div>
        <div id="projectMeetingsContent" class="overflow-y-auto" aria-live="polite" aria-busy="false"></div>
    </div>
</div>

@push('scripts')
<script>
    const meetingsModal = document.getElementById('projectMeetingsModal');
    const meetingsContent = document.getElementById('projectMeetingsContent');
    const closeMeetingsButton = document.getElementById('closeProjectMeetingsBtn');
    let meetingsRequest;
    let meetingsTrigger;
    let meetingsUrl;
    let previousBodyOverflow;

    function openProjectMeetings(button) {
        meetingsTrigger = button;
        document.getElementById('projectMeetingsName').textContent = button.dataset.projectName;
        previousBodyOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        meetingsModal.classList.remove('hidden');
        closeMeetingsButton.focus();
        loadProjectMeetings(button.dataset.url);
    }

    async function loadProjectMeetings(url) {
        meetingsRequest?.abort();
        const controller = new AbortController();
        meetingsRequest = controller;
        meetingsUrl = url;
        closeMeetingsButton.focus();
        meetingsContent.setAttribute('aria-busy', 'true');
        meetingsContent.innerHTML = '<p class="p-8 text-center text-xs text-gray-500"><i class="fa-solid fa-spinner fa-spin mr-1" aria-hidden="true"></i> Memuat daftar meeting...</p>';

        try {
            const response = await fetch(url, {
                signal: controller.signal,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
            });
            if (!response.ok || response.redirected) {
                throw new Error('Gagal memuat meeting');
            }
            const html = await response.text();
            if (!controller.signal.aborted) {
                meetingsContent.innerHTML = html;
                meetingsContent.scrollTop = 0;
            }
        } catch (error) {
            if (!controller.signal.aborted) {
                meetingsContent.innerHTML = '<div class="p-8 text-center text-xs"><p class="text-red-600">Daftar meeting gagal dimuat. Silakan coba lagi.</p><button type="button" onclick="loadProjectMeetings(meetingsUrl)" class="mt-2 px-3 py-1.5 bg-white border border-gray-300 rounded-sm hover:bg-gray-50">Coba Lagi</button></div>';
            }
        } finally {
            if (!controller.signal.aborted) {
                meetingsContent.setAttribute('aria-busy', 'false');
            }
        }
    }

    function closeProjectMeetings() {
        meetingsRequest?.abort();
        meetingsModal.classList.add('hidden');
        meetingsContent.setAttribute('aria-busy', 'false');
        document.body.style.overflow = previousBodyOverflow;
        meetingsTrigger?.focus();
    }

    meetingsContent.addEventListener('click', function (event) {
        const link = event.target.closest('[data-meetings-pagination] a[href]');
        if (link) {
            event.preventDefault();
            loadProjectMeetings(link.href);
        }
    });

    meetingsModal.addEventListener('click', function (event) {
        if (event.target === meetingsModal) closeProjectMeetings();
    });

    meetingsModal.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            event.preventDefault();
            closeProjectMeetings();
        } else if (event.key === 'Tab') {
            const controls = Array.from(meetingsModal.querySelectorAll('button:not([disabled]), a[href]'))
                .filter(element => element.getClientRects().length);
            const first = controls[0];
            const last = controls[controls.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });

    const modal = document.getElementById('projectModal');
    const form = document.getElementById('projectForm');
    const modalTitle = document.getElementById('modalTitle');
    const methodSpoof = document.getElementById('methodSpoof');
    const inputName = document.getElementById('input_name');
    const inputDeptId = document.getElementById('input_department_id');
    const inputStartDate = document.getElementById('input_start_date');
    const inputEndDate = document.getElementById('input_end_date');
    const inputCode = document.getElementById('input_code');
    const inputStatus = document.getElementById('input_status');
    const inputDescription = document.getElementById('input_description');

    function openCreateModal() {
        modalTitle.textContent = 'Tambah Master Proyek Baru';
        form.action = "{{ route('projects.store') }}";
        methodSpoof.innerHTML = '';
        inputName.value = '';
        inputDeptId.value = '';
        inputStartDate.value = '';
        inputEndDate.value = '';
        inputCode.value = '';
        inputStatus.value = 'active';
        inputDescription.value = '';
        modal.classList.remove('hidden');
    }

    function openEditModal(project) {
        modalTitle.textContent = 'Edit Data Master Proyek';
        form.action = `/projects/${project.id}`;
        methodSpoof.innerHTML = '@method("PUT")';
        inputName.value = project.name;
        inputDeptId.value = project.department_id || '';
        inputStartDate.value = project.start_date ? project.start_date.substring(0, 10) : '';
        inputEndDate.value = project.end_date ? project.end_date.substring(0, 10) : '';
        inputCode.value = project.code || '';
        inputStatus.value = project.status;
        inputDescription.value = project.description || '';
        modal.classList.remove('hidden');
    }

    function closeProjectModal() {
        modal.classList.add('hidden');
    }

    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeProjectModal();
            }
        });
    }
</script>
@endpush
@endsection
