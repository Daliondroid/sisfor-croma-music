<?php

namespace Tests\Unit;

use App\Services\LaporanReportService;
use PHPUnit\Framework\TestCase;

class RefactoringServicesTest extends TestCase
{
    public function test_laporan_report_service_date_resolution(): void
    {
        $service = new LaporanReportService;

        [$bulan, $start, $end] = $service->resolveDateRange('2026-05', null, null);
        $this->assertEquals('2026-05', $bulan);
        $this->assertEquals('2026-05-01', $start);
        $this->assertEquals('2026-05-31', $end);

        [$bulan2, $start2, $end2] = $service->resolveDateRange('2026-05', '2026-05-10', '2026-05-20');
        $this->assertEquals('2026-05', $bulan2);
        $this->assertEquals('2026-05-10', $start2);
        $this->assertEquals('2026-05-20', $end2);
    }
}
