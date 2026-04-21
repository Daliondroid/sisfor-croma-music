<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyReport extends Model
{
    protected $table      = 'monthly_reports';
    protected $primaryKey = 'id_report';

    protected $fillable = [
        'id_murid', 'bulan', 'total_hadir', 'total_alpa',
        'total_izin', 'persentase_kehadiran', 'catatan_guru',
    ];

    public function murid() { return $this->belongsTo(Murid::class, 'id_murid'); }

    // Generate/refresh data dari tabel presensi
    public static function generateUntukMurid(int $idMurid, string $bulan): self
    {
        $presensis = Presensi::where('id_murid', $idMurid)
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
            ->get();

        $hadir = $presensis->where('status_murid', 'hadir')->count();
        $alpa  = $presensis->where('status_murid', 'alpa')->count();
        $izin  = $presensis->where('status_murid', 'izin')->count();
        $total = $presensis->count();

        return self::updateOrCreate(
            ['id_murid' => $idMurid, 'bulan' => $bulan],
            [
                'total_hadir'          => $hadir,
                'total_alpa'           => $alpa,
                'total_izin'           => $izin,
                'persentase_kehadiran' => $total > 0 ? round(($hadir / $total) * 100, 2) : 0,
            ]
        );
    }
}