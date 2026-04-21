@extends('layouts.app')
@section('title', 'Tagihan SPP')
@section('page-title', 'Tagihan SPP')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<div class="page-header">
    <div><h2>Tagihan SPP</h2><div class="breadcrumb">Admin / <span>SPP</span></div></div>
    <button class="btn btn-primary" onclick="document.getElementById('modal-generate').style.display='flex'">
        <i class="fa-solid fa-bolt"></i> Generate Tagihan
    </button>
</div>

<!-- Filter -->
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:16px 24px">
        <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
            <div class="form-group" style="margin:0">
                <label class="form-label">Bulan</label>
                <input type="month" name="bulan" class="form-control" value="{{ request('bulan', now()->format('Y-m')) }}"/>
            </div>
            <div class="form-group" style="margin:0">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">Semua</option>
                    <option value="belum_bayar" {{ request('status')=='belum_bayar'?'selected':'' }}>Belum Bayar</option>
                    <option value="sudah_bayar" {{ request('status')=='sudah_bayar'?'selected':'' }}>Sudah Bayar</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i> Filter</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Murid</th><th>Bulan</th><th>Nominal</th><th>Jatuh Tempo</th><th>Status</th><th>Bukti</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($spps as $i => $spp)
                <tr>
                    <td style="color:var(--text-light)">{{ $spps->firstItem() + $i }}</td>
                    <td><strong>{{ $spp->murid->nama_murid }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($spp->bulan_tagihan.'-01')->translatedFormat('F Y') }}</td>
                    <td>Rp {{ number_format($spp->nominal_tagihan,0,',','.') }}</td>
                    <td>{{ $spp->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $spp->status_bayar=='sudah_bayar' ? 'badge-success' : 'badge-danger' }}">
                            {{ $spp->status_bayar=='sudah_bayar' ? 'Lunas' : 'Belum Bayar' }}
                        </span>
                    </td>
                    <td>
                        @if($spp->transaksi && $spp->transaksi->file_bukti_transfer)
                            <a href="{{ asset('storage/'.$spp->transaksi->file_bukti_transfer) }}" target="_blank" class="btn btn-sm btn-outline">
                                <i class="fa-solid fa-image"></i> Lihat
                            </a>
                        @else
                            <span style="color:var(--text-light);font-size:.78rem">—</span>
                        @endif
                    </td>
                    <td>
                        @if($spp->status_bayar=='belum_bayar' && $spp->transaksi)
                            <form method="POST" action="{{ route('admin.spp.validasi', $spp) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-primary"><i class="fa-solid fa-check"></i> Validasi</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--text-light)">Tidak ada data SPP.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($spps->hasPages())<div style="padding:16px 24px">{{ $spps->links() }}</div>@endif
</div>

<!-- Modal Generate -->
<div id="modal-generate" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:200;align-items:center;justify-content:center">
    <div class="card" style="width:420px;max-width:90vw">
        <div class="card-header">
            <h3>Generate Tagihan SPP</h3>
            <button onclick="document.getElementById('modal-generate').style.display='none'" style="background:none;border:none;font-size:1.2rem;cursor:pointer">&times;</button>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.spp.generate') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Bulan Tagihan <span style="color:red">*</span></label>
                    <input type="month" name="bulan" class="form-control" value="{{ now()->format('Y-m') }}" required/>
                </div>
                <div class="form-group">
                    <label class="form-label">Nominal (Rp) <span style="color:red">*</span></label>
                    <input type="number" name="nominal" class="form-control" placeholder="350000" required/>
                </div>
                <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:12px;font-size:.8rem;margin-bottom:16px">
                    <i class="fa-solid fa-triangle-exclamation" style="color:#d97706;margin-right:6px"></i>
                    Tagihan akan dibuat untuk <strong>semua murid aktif</strong>. Murid yang sudah punya tagihan di bulan ini tidak akan diduplikasi.
                </div>
                <div style="display:flex;gap:10px">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-bolt"></i> Generate</button>
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-generate').style.display='none'">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection