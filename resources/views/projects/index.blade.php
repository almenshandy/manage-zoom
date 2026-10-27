@extends('layouts.app')

@php
    $header = 'Master Proyek Kegiatan';
@endphp

@section('content')
<div class="space-y-4">
    <!-- Header Indicator & Actions -->
    <div class="bg-white p-4 rounded border border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Daftar Master Proyek / Kegiatan</h3>
            <p class="text-xs text-gray-500 mt-0.5">Kelola daftar nama proyek yang dapat dipilih saat pemohon melakukan booking Zoom multi-hari.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Search Form -->
            <form method="GET" action="{{ route('projects.index') }}" class="flex items-center gap-2">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari proyek / kode..."
                        class="pl-8 pr-3 py-1.5 bg-white border border-gray-300 rounded text-xs focus:outline-none focus:border-gray-500 w-44">
                </div>
                <button type="submit" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded border border-gray-300 transition">
                    Filter
                </button>
            </form>

            <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-1.5 bg-gray-900 hover:bg-black text-white text-xs font-medium px-3.5 py-1.5 rounded transition">
                <i class="fa-solid fa-plus text-[10px]"></i> Tambah Proyek Baru
            </button>
        </div>
    </div>

    @if($errors->any())
        <div class="p-3 bg-red-50 border border-red-200 rounded text-xs text-red-700">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Projects Table -->
    <div class="bg-white rounded border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="px-3 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">Nama Proyek</th>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Periode Pelaksanaan</th>
                        <th class="px-4 py-3">Deskripsi / Keterangan</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($projects as $idx => $p)
                        <tr class="hover:bg-gray-50/75 transition-colors">
                            <td class="px-3 py-3 text-center font-mono text-gray-500 text-[11px]">
                                {{ $projects->firstItem() + $idx }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-gray-900 block text-xs">{{ $p->name }}</span>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-600">
                                {{ $p->code ?: '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs">
                                @if($p->start_date && $p->end_date)
                                    <span class="font-medium text-gray-800">{{ $p->start_date->format('d/m/Y') }} - {{ $p->end_date->format('d/m/Y') }}</span>
                                @elseif($p->start_date)
                                    <span class="font-medium text-gray-800">Mulai {{ $p->start_date->format('d/m/Y') }}</span>
                                @else
                                    <span class="text-gray-400 italic">Fleksibel / Tanpa Batas</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ $p->description ?: '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($p->status === 'active')
                                    <span class="inline-block px-2 py-0.5 text-[10px] font-semibold rounded border bg-emerald-50 text-emerald-700 border-emerald-200">
                                        Aktif
                                    </span>
                                @elseif($p->status === 'completed')
                                    <span class="inline-block px-2 py-0.5 text-[10px] font-semibold rounded border bg-blue-50 text-blue-700 border-blue-200">
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 text-[10px] font-semibold rounded border bg-gray-50 text-gray-700 border-gray-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" 
                                        onclick="openEditModal({{ json_encode($p) }})"
                                        class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded border border-transparent hover:border-gray-200" title="Edit Proyek">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>

                                    <form method="POST" action="{{ route('projects.destroy', $p) }}" onsubmit="return confirm('Hapus master proyek ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded border border-transparent hover:border-red-200" title="Hapus Proyek">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                <i class="fa-solid fa-briefcase text-2xl mb-2 text-gray-300 block"></i>
                                Belum ada master proyek terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($projects->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah / Edit Proyek (Flat) -->
<div id="projectModal" class="hidden fixed inset-0 bg-gray-900/50 z-50 flex items-center justify-center p-4 backdrop-blur-xs">
    <div class="bg-white rounded-lg border border-gray-200 w-full max-w-md overflow-hidden shadow-xl animate-fadeIn">
        <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between bg-white">
            <h4 id="modalTitle" class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Tambah Master Proyek</h4>
            <button type="button" onclick="closeProjectModal()" class="w-7 h-7 rounded flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form id="projectForm" method="POST" action="{{ route('projects.store') }}" class="p-5 space-y-3.5 text-xs">
            @csrf
            <div id="methodSpoof"></div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">Nama Proyek / Pelaksanaan <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="input_name" required placeholder="Contoh: Asesmen Bank Mandiri 2026"
                    class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Tanggal Mulai Pelaksanaan</label>
                    <input type="date" name="start_date" id="input_start_date"
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 text-xs">
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Tanggal Selesai Pelaksanaan</label>
                    <input type="date" name="end_date" id="input_end_date"
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 text-xs">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Kode Proyek</label>
                    <input type="text" name="code" id="input_code" placeholder="Misal: PRJ-2026-01"
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 font-mono text-xs">
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Status Proyek <span class="text-red-500">*</span></label>
                    <select name="status" id="input_status" required class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                        <option value="active">Aktif</option>
                        <option value="completed">Selesai</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">Deskripsi / Keterangan Proyek</label>
                <textarea name="description" id="input_description" rows="2.5" placeholder="Keterangan singkat kegiatan..."
                    class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500"></textarea>
            </div>

            <div class="pt-3 border-t border-gray-200 flex items-center justify-end gap-2">
                <button type="button" onclick="closeProjectModal()" class="px-4 py-1.5 bg-white border border-gray-300 text-gray-700 font-medium rounded hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 bg-gray-900 hover:bg-black text-white font-medium rounded transition">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const modal = document.getElementById('projectModal');
    const form = document.getElementById('projectForm');
    const modalTitle = document.getElementById('modalTitle');
    const methodSpoof = document.getElementById('methodSpoof');
    const inputName = document.getElementById('input_name');
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

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeProjectModal();
        }
    });
</script>
@endpush
@endsection
