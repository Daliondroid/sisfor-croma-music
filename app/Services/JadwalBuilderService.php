<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\ProgramKursus;
use App\Models\Spp;
use App\Models\HonorGuru;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JadwalBuilderService
{
    /**
     * Generate sessions, check time clashes, and persist SPP + Honor + Jadwal records.
     *
     * @param array $data
     * @param int $idAdmin
     * @return int Number of generated sessions
     * @throws \Exception
     */
    public function createBulkJadwal(array $data, int $idAdmin): int
    {
        $dayMapping = [
            'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4,
            'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7
        ];

        return DB::transaction(function () use ($data, $idAdmin, $dayMapping) {
            $program = ProgramKursus::findOrFail($data['id_program']);

            $lastSession = Jadwal::whereHas('spp', function ($q) use ($data) {
                $q->where('id_murid', $data['id_murid'])
                  ->where('id_program', $data['id_program']);
            })->max('sesi_ke') ?? 0;

            $generatedSessions = [];
            $lastDate = Carbon::parse($data['tanggal_mulai'])->subWeek();

            $totalSesi = (int) $data['total_sesi'];
            for ($i = 1; $i <= $totalSesi; $i++) {
                if ($data['tipe_jadwal'] === 'manual') {
                    $tanggal = $data['jadwal_manual'][$i - 1]['tanggal'];
                    $jamMulai = $data['jadwal_manual'][$i - 1]['jam_mulai'];
                    $jamSelesai = $data['jadwal_manual'][$i - 1]['jam_selesai'];
                } else {
                    if ($data['tipe_jadwal'] === 'tetap') {
                        $hari = $data['pola_tunggal']['hari'];
                        $jamMulai = $data['pola_tunggal']['jam_mulai'];
                        $jamSelesai = $data['pola_tunggal']['jam_selesai'];
                    } else {
                        $slotIndex = ($i - 1) % 4;
                        $hari = $data['pola'][$slotIndex]['hari'];
                        $jamMulai = $data['pola'][$slotIndex]['jam_mulai'];
                        $jamSelesai = $data['pola'][$slotIndex]['jam_selesai'];
                    }

                    if ($i === 1) {
                        $tanggalCarbon = Carbon::parse($data['tanggal_mulai']);
                        while ($tanggalCarbon->dayOfWeekIso !== $dayMapping[$hari]) {
                            $tanggalCarbon->addDay();
                        }
                    } else {
                        $tanggalCarbon = $lastDate->copy()->addWeek();
                        $tanggalCarbon->startOfWeek()->addDays($dayMapping[$hari] - 1);
                    }

                    $tanggal = $tanggalCarbon->format('Y-m-d');
                    $lastDate = $tanggalCarbon;
                }

                $generatedSessions[] = [
                    'tanggal'     => $tanggal,
                    'jam_mulai'   => $jamMulai,
                    'jam_selesai' => $jamSelesai,
                    'sesi_ke'     => $lastSession + $i,
                ];
            }

            // Check Time Clashes for Guru and Murid
            foreach ($generatedSessions as $session) {
                if (Jadwal::hasGuruClash($data['id_guru'], $session['tanggal'], $session['jam_mulai'], $session['jam_selesai'])) {
                    throw new \Exception('Guru sudah memiliki jadwal pada ' . $session['tanggal'] . ' jam ' . substr($session['jam_mulai'], 0, 5) . '.');
                }

                if (Jadwal::hasMuridClash($data['id_murid'], $session['tanggal'], $session['jam_mulai'], $session['jam_selesai'], null, true)) {
                    throw new \Exception('Murid sudah memiliki jadwal pada ' . $session['tanggal'] . ' jam ' . substr($session['jam_mulai'], 0, 5) . '.');
                }
            }

            // Chunk sessions into 4-meeting blocks for SPP & Honor creation
            $chunks = array_chunk($generatedSessions, 4);

            foreach ($chunks as $chunk) {
                $firstSessionDate = Carbon::parse($chunk[0]['tanggal']);

                $spp = Spp::create([
                    'id_murid'            => $data['id_murid'],
                    'id_program'          => $data['id_program'],
                    'periode_tagihan'     => $firstSessionDate->copy()->startOfMonth()->format('Y-m-d'),
                    'nominal_tagihan'     => $program->biaya_kursus ?? 0,
                    'tanggal_jatuh_tempo' => $firstSessionDate->format('Y-m-d'),
                    'status_bayar'        => 'Belum Lunas'
                ]);

                $honor = HonorGuru::create([
                    'id_guru'           => $data['id_guru'],
                    'id_admin'          => $idAdmin,
                    'tanggal_pencairan' => null,
                    'jumlah_pertemuan'  => count($chunk),
                    'jumlah_honor'      => 0,
                    'status_bayar'      => 'Belum Lunas',
                    'catatan'           => null
                ]);

                $jadwalsToInsert = [];
                foreach ($chunk as $session) {
                    $jadwalsToInsert[] = [
                        'id_admin'      => $idAdmin,
                        'id_guru'       => $data['id_guru'],
                        'id_spp'        => $spp->id_spp,
                        'id_honor'      => $honor->id_honor,
                        'tanggal'       => $session['tanggal'],
                        'jam_mulai'     => $session['jam_mulai'],
                        'jam_selesai'   => $session['jam_selesai'],
                        'sesi_ke'       => $session['sesi_ke'],
                        'status_jadwal' => 'Sesuai Jadwal',
                        'is_active'     => true,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];
                }
                Jadwal::insert($jadwalsToInsert);
            }

            return count($generatedSessions);
        });
    }
}
