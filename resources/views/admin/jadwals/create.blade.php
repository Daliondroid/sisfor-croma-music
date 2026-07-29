@extends('layouts.app')
@section('title', 'Tambah Jadwal KBM')
@section('page-title', 'Tambah Jadwal KBM')
@section('sidebar-menu') @include('admin.partials.sidebar') @endsection

@section('content')
<style>
    .radio-toggle {
        display: inline-flex;
        border-radius: 0.375rem;
        border: 1px solid #cbd5e1;
        overflow: hidden;
        background-color: #f8fafc;
    }
    .radio-toggle input[type="radio"] { display: none; }
    .radio-toggle label {
        padding: 0.5rem 1.5rem;
        color: #475569;
        cursor: pointer;
        border-right: 1px solid #cbd5e1;
        font-size: 0.875rem;
        margin: 0;
        transition: background-color 0.2s, color 0.2s;
        white-space: nowrap;
    }
    .radio-toggle label:last-of-type { border-right: none; }
    .radio-toggle input[type="radio"]:checked + label {
        background-color: #3b82f6; 
        color: #ffffff;
        font-weight: 600;
    }
    .locked-input {
        background-color: #e2e8f0;
        pointer-events: none;
    }
</style>

<div class="page-header" style="margin-bottom: 1.5rem;">
    <div>
        <h2>Buat Jadwal Baru</h2>
        <div class="breadcrumb">Admin / Jadwal / <span>Tambah</span></div>
    </div>
    <a href="{{ route('admin.jadwals.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card" style="padding: 2rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 1.5rem; color: #dc2626; background: #fef2f2; padding: 1rem; border-radius: 0.375rem;">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.jadwals.store') }}" method="POST" id="form-jadwal">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Pilih Murid <span style="color: red;">*</span></label>
                <select name="id_murid" class="form-control" required>
                    <option value="">-- Pilih Murid --</option>
                    @foreach($murids as $murid)
                        <option value="{{ $murid->id_murid }}">{{ $murid->nama_murid }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Program Kursus <span style="color: red;">*</span></label>
                <select name="id_program" id="id_program" class="form-control" required onchange="updateTipeLes()">
                    <option value="">-- Pilih Program --</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id_program }}" data-tipe="{{ $program->tipe_les }}">
                            {{ $program->nama_program }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Pilih Guru <span style="color: red;">*</span></label>
                <select name="id_guru" class="form-control" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach($gurus as $guru)
                        <option value="{{ $guru->id_guru }}">{{ $guru->nama_guru }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Total Pertemuan <span style="color: red;">*</span></label>
                <select name="total_sesi" id="total_sesi" class="form-control" required onchange="generateManualRows()">
                    <option value="">-- Pilih Total --</option>
                    <option value="4">4 Pertemuan</option>
                    <option value="8">8 Pertemuan</option>
                    <option value="12">12 Pertemuan</option>
                    <option value="16">16 Pertemuan</option>
                    <option value="20">20 Pertemuan</option>
                    <option value="24">24 Pertemuan</option>
                </select>
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Tipe Les <span style="color: red;">*</span></label>
                <select name="tipe_les" id="tipe_les" class="form-control" required>
                    <option value="" id="opt_placeholder">-- Pilih Program Dulu --</option>
                    <option value="Onsite">On Site (Studio)</option>
                    <option value="Home Private">Off Site (Home Visit)</option>
                </select>
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Tanggal Acuan Pertama <span style="color: red;">*</span></label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
                <small style="color: #64748b; display: block; margin-top: 0.25rem;">Tanggal dasar untuk tagihan dan minggu pertama.</small>
            </div>
            
        </div>

        <div style="border-top: 0.125rem solid #e2e8f0; padding-top: 2rem; margin-bottom: 1.5rem;">
            <h3 id="judul_pengaturan" style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; color: var(--text-dark);">
                Pengaturan Jadwal Tetap
            </h3>

            <div class="radio-toggle" style="margin-bottom: 1.5rem;">
                <input type="radio" name="tipe_jadwal" id="tipe_tetap" value="tetap" checked onchange="updateTipeJadwal()">
                <label for="tipe_tetap">Jadwal Tetap</label>
                
                <input type="radio" name="tipe_jadwal" id="tipe_pola" value="pola" onchange="updateTipeJadwal()">
                <label for="tipe_pola">Pola 4 Pertemuan</label>
                
                <input type="radio" name="tipe_jadwal" id="tipe_manual" value="manual" onchange="updateTipeJadwal()">
                <label for="tipe_manual">Manual</label>
            </div>

            <div id="container-tetap">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; background-color: #f8fafc; padding: 1.5rem; border-radius: 0.375rem; border: 1px solid #e2e8f0;">
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: bold; margin-bottom: 0.5rem;">Hari</label>
                        <select name="pola_tunggal[hari]" class="form-control" required>
                            <option value="">-- Pilih Hari --</option>
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                <option value="{{ $hari }}">{{ $hari }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: bold; margin-bottom: 0.5rem;">Jam Mulai</label>
                        <input type="time" name="pola_tunggal[jam_mulai]" class="form-control" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: bold; margin-bottom: 0.5rem;">Jam Selesai</label>
                        <input type="time" name="pola_tunggal[jam_selesai]" class="form-control" required>
                    </div>
                </div>
            </div>

            <div id="container-pola" style="display: none;">
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @for($index = 0; $index < 4; $index++)
                        <div style="display: grid; grid-template-columns: auto 1fr 1fr 1fr; gap: 1.5rem; align-items: center; background-color: #f8fafc; padding: 1rem; border-radius: 0.375rem; border: 1px solid #e2e8f0;">
                            <span style="font-weight: bold; color: #475569; min-width: 5rem;">Minggu {{ $index + 1 }}</span>
                            <select name="pola[{{ $index }}][hari]" class="form-control" disabled required>
                                <option value="">-- Pilih Hari --</option>
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                    <option value="{{ $hari }}">{{ $hari }}</option>
                                @endforeach
                            </select>
                            <input type="time" name="pola[{{ $index }}][jam_mulai]" class="form-control" disabled required>
                            <input type="time" name="pola[{{ $index }}][jam_selesai]" class="form-control" disabled required>
                        </div>
                    @endfor
                </div>
            </div>

            <div id="container-manual" style="display: none;">
                <div id="manual-rows">
                    <div style="text-align:center; padding:1rem; color:#64748b; background-color: #f8fafc; border-radius: 0.375rem; border: 1px solid #e2e8f0;">
                        Silakan pilih Total Pertemuan pada form di atas terlebih dahulu.
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; border-top: 1px solid #e2e8f0; padding-top: 1.5rem; margin-top: 1rem;">
            <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.5rem;">
                <i class="fa-solid fa-save"></i> Proses Jadwal
            </button>
            <a href="{{ route('admin.jadwals.index') }}" class="btn btn-secondary" style="padding: 0.5rem 1.5rem;">Batal</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        updateTipeJadwal();
    });

    // Otomatis menetapkan Tipe Les berdasarkan Program Kursus secara mutlak
    function updateTipeLes() {
        const selectProgram = document.getElementById('id_program');
        const selectTipeLes = document.getElementById('tipe_les');
        const placeholder = document.getElementById('opt_placeholder');
        
        if (selectProgram.selectedIndex <= 0) {
            selectTipeLes.value = "";
            selectTipeLes.style.pointerEvents = "auto";
            selectTipeLes.style.backgroundColor = "#fff";
            placeholder.innerText = "-- Pilih Program Dulu --";
            return;
        }

        const option = selectProgram.options[selectProgram.selectedIndex];
        const tipe = option.getAttribute('data-tipe');

        if (tipe === 'onsite') {
            selectTipeLes.value = 'Onsite';
            selectTipeLes.style.pointerEvents = "none";
            selectTipeLes.style.backgroundColor = "#e2e8f0";
            placeholder.innerText = "-- Terkunci dari Program --";
        } else if (tipe === 'home_private') {
            selectTipeLes.value = 'Home Private';
            selectTipeLes.style.pointerEvents = "none";
            selectTipeLes.style.backgroundColor = "#e2e8f0";
            placeholder.innerText = "-- Terkunci dari Program --";
        } else {
            // Jika program "keduanya", buka kunci dan minta admin memilih
            selectTipeLes.value = "";
            selectTipeLes.style.pointerEvents = "auto";
            selectTipeLes.style.backgroundColor = "#fff";
            placeholder.innerText = "-- Silakan Pilih Tipe Les --";
        }
    }

    // Pastikan field yang dikunci tetap ikut terkirim saat form di-submit
    document.getElementById('form-jadwal').addEventListener('submit', function() {
        document.getElementById('tipe_les').style.pointerEvents = 'auto';
    });

    // Pastikan field yang dikunci tetap ikut terkirim saat form di-submit
    document.getElementById('form-jadwal').addEventListener('submit', function() {
        document.getElementById('tipe_les').style.pointerEvents = 'auto';
    });

    // Pastikan field readonly tetap bisa di-submit
    document.getElementById('form-jadwal').addEventListener('submit', function() {
        document.getElementById('tipe_les').classList.remove('locked-input');
    });

    function updateTipeJadwal() {
        const tipeElement = document.querySelector('input[name="tipe_jadwal"]:checked');
        if (!tipeElement) return;
        
        const tipe = tipeElement.value;
        const judul = document.getElementById('judul_pengaturan');
        
        const containerTetap = document.getElementById('container-tetap');
        const containerPola = document.getElementById('container-pola');
        const containerManual = document.getElementById('container-manual');

        containerTetap.style.display = 'none';
        containerPola.style.display = 'none';
        containerManual.style.display = 'none';

        containerTetap.querySelectorAll('input, select').forEach(el => el.disabled = true);
        containerPola.querySelectorAll('input, select').forEach(el => el.disabled = true);
        containerManual.querySelectorAll('input, select').forEach(el => el.disabled = true);

        if (tipe === 'tetap') {
            judul.innerText = 'Pengaturan Jadwal Tetap';
            containerTetap.style.display = 'block';
            containerTetap.querySelectorAll('input, select').forEach(el => el.disabled = false);
        } else if (tipe === 'pola') {
            judul.innerText = 'Pengaturan Pola 4 Pertemuan';
            containerPola.style.display = 'block';
            containerPola.querySelectorAll('input, select').forEach(el => el.disabled = false);
        } else if (tipe === 'manual') {
            judul.innerText = 'Input Jadwal Manual';
            containerManual.style.display = 'block';
            containerManual.querySelectorAll('input, select').forEach(el => el.disabled = false);
            generateManualRows(); 
        }
    }

    function generateManualRows() {
        const tipeElement = document.querySelector('input[name="tipe_jadwal"]:checked');
        if (!tipeElement || tipeElement.value !== 'manual') return;

        const total = parseInt(document.getElementById('total_sesi').value) || 0;
        const container = document.getElementById('manual-rows');
        container.innerHTML = '';

        if (total === 0) {
            container.innerHTML = '<div style="text-align:center; padding:1rem; color:#64748b; background-color: #f8fafc; border-radius: 0.375rem; border: 1px solid #e2e8f0;">Silakan pilih Total Pertemuan pada form di atas terlebih dahulu.</div>';
            return;
        }

        for (let i = 0; i < total; i++) {
            container.innerHTML += `
                <div style="display: grid; grid-template-columns: 5rem 1fr 1fr 1fr; gap: 1rem; align-items: center; background-color: #f8fafc; padding: 1rem; border-radius: 0.375rem; border: 1px solid #e2e8f0; margin-bottom: 0.5rem;">
                    <span style="font-weight: bold; color: #475569;">Sesi ${i + 1}</span>
                    <input type="date" name="jadwal_manual[${i}][tanggal]" class="form-control" required>
                    <input type="time" name="jadwal_manual[${i}][jam_mulai]" class="form-control" required>
                    <input type="time" name="jadwal_manual[${i}][jam_selesai]" class="form-control" required>
                </div>
            `;
        }
    }
</script>
@endsection