<?php

namespace Database\Seeders;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::today();

        $projects = [
            [
                'name'        => 'Asesmen Calon Pegawai PT Bank Mandiri (Persero) Tbk',
                'code'        => 'PRJ-BM-2026',
                'description' => 'Pelaksanaan asesmen psikotes dan interview online bagi calon officer.',
                'start_date'  => $today->toDateString(),
                'end_date'    => (clone $today)->addDays(14)->toDateString(),
                'status'      => 'active',
            ],
            [
                'name'        => 'Uji Kompetensi Sertifikasi Asesor LSP Batch 4',
                'code'        => 'LSP-ASESOR-B4',
                'description' => 'Ujian sertifikasi teknis dan wawancara portofolio asesor BNSP/LSP.',
                'start_date'  => (clone $today)->addDays(3)->toDateString(),
                'end_date'    => (clone $today)->addDays(10)->toDateString(),
                'status'      => 'active',
            ],
            [
                'name'        => 'Pelatihan & Ujian Kemenkes RI Tenaga Kesehatan',
                'code'        => 'KMK-NAKES-2026',
                'description' => 'Pelaksanaan pembekalan daring serta evaluasi ujian nasional nakes.',
                'start_date'  => (clone $today)->addDays(7)->toDateString(),
                'end_date'    => (clone $today)->addDays(21)->toDateString(),
                'status'      => 'active',
            ],
            [
                'name'        => 'Program Pendampingan Internal Tanoto Foundation',
                'code'        => 'PRJ-TANOTO-26',
                'description' => 'Sesi koordinasi berkala dan evaluasi program beasiswa.',
                'start_date'  => (clone $today)->subDays(10)->toDateString(),
                'end_date'    => (clone $today)->addDays(20)->toDateString(),
                'status'      => 'active',
            ],
            [
                'name'        => 'Workshop ISO 9001:2015 & Audit Internal',
                'code'        => 'WS-ISO-2026',
                'description' => 'Pelatihan standarisasi mutu dan persiapan audit internal semester 1.',
                'start_date'  => (clone $today)->subDays(30)->toDateString(),
                'end_date'    => (clone $today)->subDays(15)->toDateString(),
                'status'      => 'completed',
            ],
        ];

        foreach ($projects as $item) {
            Project::updateOrCreate(
                ['code' => $item['code']],
                $item
            );
        }
    }
}
