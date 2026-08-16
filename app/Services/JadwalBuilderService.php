<?php

namespace App\Services;

use App\Models\HonorGuru;
use App\Models\Jadwal;
use App\Models\ProgramKursus;
use App\Models\Spp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class JadwalBuilderService
{
    /**
     * Generate sessions, check time clashes, and persist SPP + Honor + Jadwal records.
     *
     * @return int Number of generated sessions
     *
     * @throws \Exception
     */
    public function createBulkJadwal(array $data, int $idAdmin): int
    {
        $dayMapping = [
            'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4,
            'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7,
        ];

        // Distributed atomic lock to prevent concurrent double-booking on the same teacher/student
        $lockKey = 'jadwal_builder_lock_guru_'.$data['id_guru'].'_murid_'.$data['id_murid'];
        $lock = Cache::lock($lockKey, 15);

        return $lock->block(5, function () use ($data, $idAdmin, $dayMapping) {
            return DB::transaction(function () use ($data, $idAdmin, $dayMapping) {
                $program = ProgramKursus::findOrFail($data['id_program']);

                $lastSession = Jadwal::whereHas('spp', function ($q) use ($data) {
                    $q->where('id_murid', $data['id_murid'])
                        ->where('id_program', $data['id_program']);
                })->max('sesi_ke') ?? 0;

                $totalSesi = (int) $data['total_sesi'];
                $generatedSessions = [];

                if ($data['tipe_jadwal'] === 'manual') {
                    for ($i = 1; $i <= $totalSesi; $i++) {
                        $generatedSessions[] = [
                            'tanggal' => $data['jadwal_manual'][$i - 1]['tanggal'],
                            'jam_mulai' => $data['jadwal_manual'][$i - 1]['jam_mulai'],
                            'jam_selesai' => $data['jadwal_manual'][$i - 1]['jam_selesai'],
                            'sesi_ke' => $lastSession + $i,
                        ];
                    }
                } else {
                    // Registration / Starting Month Rule:
                    // If reference date is in Week 2-4 (calendar day > 7), start in the next calendar month.
                    $inputDate = Carbon::parse($data['tanggal_mulai']);
                    if ($inputDate->day > 7) {
                        $startMonth = $inputDate->copy()->addMonthNoOverflow()->startOfMonth();
                    } else {
                        $startMonth = $inputDate->copy()->startOfMonth();
                    }

                    $numMonths = (int) ceil($totalSesi / 4);
                    $sessionCounter = 0;

                    for ($m = 0; $m < $numMonths; $m++) {
                        $monthDate = $startMonth->copy()->addMonthsNoOverflow($m)->startOfMonth();
                        $firstSessionOfMonth = null;

                        for ($week = 0; $week < 4; $week++) {
                            $sessionCounter++;
                            if ($sessionCounter > $totalSesi) {
                                break 2;
                            }

                            if ($data['tipe_jadwal'] === 'tetap') {
                                $hari = $data['pola_tunggal']['hari'];
                                $jamMulai = $data['pola_tunggal']['jam_mulai'];
                                $jamSelesai = $data['pola_tunggal']['jam_selesai'];

                                if ($week === 0) {
                                    $tanggalCarbon = $monthDate->copy();
                                    while ($tanggalCarbon->dayOfWeekIso !== $dayMapping[$hari]) {
                                        $tanggalCarbon->addDay();
                                    }
                                    $firstSessionOfMonth = $tanggalCarbon->copy();
                                } else {
                                    $tanggalCarbon = $firstSessionOfMonth->copy()->addWeeks($week);
                                }
                            } else { // 'pola'
                                $hari = $data['pola'][$week]['hari'];
                                $jamMulai = $data['pola'][$week]['jam_mulai'];
                                $jamSelesai = $data['pola'][$week]['jam_selesai'];

                                if ($week === 0) {
                                    $tanggalCarbon = $monthDate->copy();
                                    while ($tanggalCarbon->dayOfWeekIso !== $dayMapping[$hari]) {
                                        $tanggalCarbon->addDay();
                                    }
                                    $firstSessionOfMonth = $tanggalCarbon->copy();
                                } else {
                                    $tanggalCarbon = $firstSessionOfMonth->copy()->addWeeks($week);
                                    $tanggalCarbon->startOfWeek()->addDays($dayMapping[$hari] - 1);
                                }
                            }

                            $generatedSessions[] = [
                                'tanggal' => $tanggalCarbon->format('Y-m-d'),
                                'jam_mulai' => $jamMulai,
                                'jam_selesai' => $jamSelesai,
                                'sesi_ke' => $lastSession + $sessionCounter,
                            ];
                        }
                    }
                }

                // Check Time Clashes for Guru and Murid
                foreach ($generatedSessions as $session) {
                    if (Jadwal::hasGuruClash($data['id_guru'], $session['tanggal'], $session['jam_mulai'], $session['jam_selesai'])) {
                        throw new \Exception('Guru sudah memiliki jadwal pada '.$session['tanggal'].' jam '.substr($session['jam_mulai'], 0, 5).'.');
                    }

                    if (Jadwal::hasMuridClash($data['id_murid'], $session['tanggal'], $session['jam_mulai'], $session['jam_selesai'], null, true)) {
                        throw new \Exception('Murid sudah memiliki jadwal pada '.$session['tanggal'].' jam '.substr($session['jam_mulai'], 0, 5).'.');
                    }
                }

                // Chunk sessions into 4-meeting blocks for SPP & Honor creation
                $chunks = array_chunk($generatedSessions, 4);

                foreach ($chunks as $chunk) {
                    $firstSessionDate = Carbon::parse($chunk[0]['tanggal']);

                    $spp = Spp::create([
                        'id_murid' => $data['id_murid'],
                        'id_program' => $data['id_program'],
                        'periode_tagihan' => $firstSessionDate->copy()->startOfMonth()->format('Y-m-d'),
                        'nominal_tagihan' => $program->biaya_kursus ?? 0,
                        'tanggal_jatuh_tempo' => $firstSessionDate->format('Y-m-d'),
                        'tipe_les' => $data['tipe_les'] ?? $program->tipe_les ?? 'Onsite',
                        'status_bayar' => 'Belum Lunas',
                    ]);

                    $honor = HonorGuru::create([
                        'id_guru' => $data['id_guru'],
                        'id_admin' => $idAdmin,
                        'tanggal_pencairan' => null,
                        'jumlah_pertemuan' => count($chunk),
                        'jumlah_honor' => 0,
                        'status_bayar' => 'Belum Lunas',
                        'catatan' => null,
                    ]);

                    $jadwalsToInsert = [];
                    foreach ($chunk as $session) {
                        $jadwalsToInsert[] = [
                            'id_admin' => $idAdmin,
                            'id_guru' => $data['id_guru'],
                            'id_spp' => $spp->id_spp,
                            'id_honor' => $honor->id_honor,
                            'tanggal' => $session['tanggal'],
                            'jam_mulai' => $session['jam_mulai'],
                            'jam_selesai' => $session['jam_selesai'],
                            'sesi_ke' => $session['sesi_ke'],
                            'status_jadwal' => 'Sesuai Jadwal',
                            'is_active' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    Jadwal::insert($jadwalsToInsert);
                }

                return count($generatedSessions);
            });
        });
    }
}
