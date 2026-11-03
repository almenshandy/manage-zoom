@extends('layouts.app')

@php
    $header = 'Master Proyek Kegiatan';
@endphp

@section('content')
<div class="space-y-4">
    <!-- Actions Toolbar -->
    <div class="bg-white p-3.5 rounded border border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('projects.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
            <div class="relative flex-1 sm:flex-initial">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari proyek, kode, dept..."
                    class="pl-8 pr-3 py-1.5 bg-white border border-gray-300 rounded text-xs focus:outline-none focus:border-gray-500 w-full sm:w-56">
            </div>

            <select name="department_id" onchange="this.form.submit()" class="px-2.5 py-1.5 bg-white border border-gray-300 rounded text-xs focus:outline-none focus:border-gray-500">
                <option value="">-- Semua Departemen --</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->nama_departemen }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded border border-gray-300 transition">
                Filter
            </button>

            @if(request()->hasAny(['search', 'department_id', 'status']))
                <a href="{{ route('projects.index') }}" class="px-2.5 py-1.5 bg-white hover:bg-gray-50 text-gray-600 text-xs rounded border border-gray-300 transition" title="Reset filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </form>

        <div class="shrink-0">
            <button type="button" onclick="openCreateModal()" class="w-full md:w-auto inline-flex items-center justify-center gap-1.5 bg-gray-900 hover:bg-black text-white text-xs font-medium px-3.5 py-1.5 rounded transition shadow-xs">
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
                            <td class="px-3 py-3 text-center font-mono text-gray-500 text-[11px]">
                                {{ $projects->firstItem() + $idx }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-gray-900 block text-xs">{{ $p->name }}</span>
                                @if($p->description)
                                    <p class="text-[11px] text-gray-500 line-clamp-1 mt-0.5">{{ $p->description }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($p->department)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="fa-solid fa-sitemap text-[9px]"></i> {{ $p->department->nama_departemen }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic text-[11px]">Lintas / Umum</span>
                                @endif
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
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 font-mono font-semibold px-2 py-0.5 rounded bg-gray-100 text-gray-800 text-[11px]">
                                    <i class="fa-solid fa-video text-[9px] text-gray-500"></i> {{ $p->bookings_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if($p->status === 'active')
                                    <span class="px-2 py-0.5 text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded">
                                        Aktif
                                    </span>
                                @elseif($p->status === 'completed')
                                    <span class="px-2 py-0.5 text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 rounded">
                                        Selesai
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-600 border border-gray-200 rounded">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" onclick="openEditModal({{ json_encode($p) }})" 
                                        class="px-2 py-1 bg-white hover:bg-gray-50 text-gray-700 rounded border border-gray-300 font-medium text-[11px] transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-pencil text-[10px] text-gray-500"></i> Edit
                                    </button>

                                    <form method="POST" action="{{ route('projects.destroy', $p) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data proyek ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 bg-white hover:bg-red-50 text-red-600 rounded border border-red-200 font-medium text-[11px] transition inline-flex items-center gap-1">
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
<div id="projectModal" class="fixed inset-0 z-50 hidden bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded border border-gray-300 max-w-lg w-full shadow-lg overflow-hidden animate-fadeIn">
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

            <!-- Pemilihan Departemen Penanggung Jawab Proyek -->
            <div>
                <label class="block font-medium text-gray-700 mb-1">
                    Departemen / Unit Kerja <span class="text-gray-400 font-normal">(Opsional)</span>
                </label>
                <select name="department_id" id="input_department_id" class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                    <option value="">-- Tidak Terikat Departemen (Lintas / Umum) --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">
                            {{ $dept->nama_departemen }} ({{ $dept->kode_departemen }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-gray-500 mt-1">Pilih departemen yang menaungi proyek agar statistik proyek per unit dapat terhitung.</p>
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
