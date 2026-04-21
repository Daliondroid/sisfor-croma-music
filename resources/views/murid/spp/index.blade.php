@extends('layouts.app')
@section('title', 'SPP Saya')
@section('page-title', 'SPP Saya')
@section('sidebar-menu')
    <div class="nav-section-label">Menu</div>
    <a href="{{ route('murid.dashboard') }}" class="nav-item"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a href="{{ route('murid.spp.index') }}" class="nav-item active"><i class="fa-solid fa-file-invoice-dollar"></i> SPP Saya</a>
    <a href="{{ route('murid.profil.edit') }}" class="nav-item"><i class="fa-solid fa-user-pen"></i> Profil Saya</a>
@endsection

@section('content')
<div class="page-header">
    <div><h2>Riwayat SPP</h2><div class="breadcrumb">Murid / <span>SPP</span></div></div>
</div>

<div class="card">
    @forelse($spps as $spp)
    <div style="padding:20px 24px;border-bottom:1px solid #f0f0f0">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px">
            <div>
                <div style="font-weight:700;font-size:1rem">
                    {{ \Carbon\Carbon::parse($spp->bulan_tagihan.'-01')->translatedFormat('F Y') }}
                </div>
                <div style="font-size:.85rem;color:var(--text-light);margin-top:2px">
                    Nominal: <strong>Rp {{ number_format($spp->nominal_tagihan,0,',','.') }}</strong>
                    · Jatuh Tempo: {{ $spp->tanggal_jatuh_tempo->format('d F Y') }}
                </div>
            </div>
            <span class="badge {{ $spp->sudahBayar() ? 'badge-success' : 'badge-danger' }}" style="font-size:.8rem;padding:5px 14px">
                {{ $spp->sudahBayar() ? '✅ Lunas' : '❌ Belum Bayar' }}
            </span>
        </div>

        @if(!$spp->sudahBayar())
        <div style="margin-top:16px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:16px">
            <div style="font-weight:600;font-size:.85rem;margin-bottom:12px;color:#92400e">
                <i class="fa-solid fa-upload" style="margin-right:6px"></i>Upload Bukti Transfer
            </div>
            @if($spp->transaksi && !$spp->sudahBayar())
                <div style="background:#e0f2fe;border:1px solid #7dd3fc;border-radius:6px;padding:10px 14px;font-size:.8rem;margin-bottom:12px">
                    <i class="fa-solid fa-circle-info" style="color:#0ea5e9;margin-right:6px"></i>
                    Bukti sudah dikirim, menunggu konfirmasi admin.
                    <a href="{{ asset('storage/'.$spp->transaksi->file_bukti_transfer) }}" target="_blank" style="margin-left:6px;font-weight:600">Lihat bukti →</a>
                </div>
            @endif
            <form method="POST" action="{{ route('murid.spp.bukti', $spp) }}" enctype="multipart/form-data">
                @csrf
                <div class="form-grid">
                    <div class="form-group" style="margin-bottom:10px">
                        <label class="form-label" style="font-size:.78rem">File Bukti (JPG/PNG/PDF maks 5MB)</label>
                        <input type="file" name="bukti_transfer" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required/>
                    </div>
                    <div class="form-group" style="margin-bottom:10px">
                        <label class="form-label" style="font-size:.78rem">Nominal Dibayar (Rp)</label>
                        <input type="number" name="nominal_bayar" class="form-control" value="{{ $spp->nominal_tagihan }}" required/>
                    </div>
                    <div class="form-group" style="margin-bottom:10px">
                        <label class="form-label" style="font-size:.78rem">Tanggal Bayar</label>
                        <input type="date" name="tanggal_bayar" class="form-control" value="{{ now()->format('Y-m-d') }}" required/>
                    </div>
                </div>
                <button type="submit" class="btn btn-yellow btn-sm">
                    <i class="fa-solid fa-paper-plane"></i> {{ $spp->transaksi ? 'Kirim Ulang' : 'Kirim Bukti' }}
                </button>
            </form>
        </div>
        @elseif($spp->transaksi)
        <div style="margin-top:12px;font-size:.78rem;color:#16a34a">
            <i class="fa-solid fa-check-circle"></i>
            Dikonfirmasi pada {{ $spp->transaksi->tanggal_konfirmasi?->format('d F Y') ?? '—' }}
        </div>
        @endif
    </div>
    @empty
        <div style="text-align:center;padding:40px;color:var(--text-light)">
            <i class="fa-solid fa-receipt" style="font-size:2rem;opacity:.3;margin-bottom:12px"></i>
            <p>Belum ada tagihan SPP.</p>
        </div>
    @endforelse
    @if($spps->hasPages())<div style="padding:16px 24px">{{ $spps->links() }}</div>@endif
</div>
@endsection