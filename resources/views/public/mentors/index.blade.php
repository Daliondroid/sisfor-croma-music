@extends('layouts.public')

@section('title', 'Direktori Mentor — Croma Music')
@section('header-class', 'header-light')

@section('content')
<section class="page-header section bg-light">
    <div class="container">
        <div class="section-header text-center">
            <h2>Direktori Mentor Musik</h2>
            <p class="subtitle-text">Pengajar berpengalaman, praktisi, dan akademisi musik siap membimbing minat musikmu.</p>
            
            <div class="mentor-filter-pills" role="tablist" aria-label="Filter Category Mentor">
                <button type="button" class="filter-pill active" data-filter="all" role="tab" aria-selected="true">Semua Kategori</button>
                <button type="button" class="filter-pill" data-filter="piano" role="tab" aria-selected="false">Piano</button>
                <button type="button" class="filter-pill" data-filter="vocal" role="tab" aria-selected="false">Vokal</button>
                <button type="button" class="filter-pill" data-filter="guitar" role="tab" aria-selected="false">Gitar</button>
                <button type="button" class="filter-pill" data-filter="drum" role="tab" aria-selected="false">Drum</button>
                <button type="button" class="filter-pill" data-filter="keyboard" role="tab" aria-selected="false">Keyboard</button>
                <button type="button" class="filter-pill" data-filter="bass" role="tab" aria-selected="false">Bass</button>
                <button type="button" class="filter-pill" data-filter="flute" role="tab" aria-selected="false">Flute</button>
                <button type="button" class="filter-pill" data-filter="saxophone" role="tab" aria-selected="false">Saxophone</button>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="grid-4 program-flex" id="mentor-grid">
            @foreach($mentors as $mentor)
                <div
                    class="card tutor-card"
                    data-category="{{ $mentor['category'] }}"
                    data-featured="{{ $mentor['featured'] ? 'true' : 'false' }}"
                >
                    <div class="tutor-img-wrapper">
						{{-- TODO: Replace placeholder below with real mentor photo <img> when client provides assets --}}
						<div class="tutor-avatar-placeholder" aria-label="Foto {{ $mentor['name'] }}" role="img">
							{{ strtoupper(substr($mentor['name'], 0, 1)) }}
						</div>
					</div>
                    <div class="card-body">
                        <h4>{{ $mentor['name'] }}</h4>
                        <span class="badge-text">{{ $mentor['badge'] }}</span>
                        <div class="card-action-wrap margin-top-sm">
                            <a href="{{ route('mentors.show', $mentor['slug']) }}" class="btn btn-outline-dark btn-sm full-width">
                                Lihat Profil <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="pricing" class="section bg-light">
	<div class="container">
		<div class="section-header text-center">
			<h2>Pilihan Paket Kursus</h2>
			<p class="subtitle-text">Pilih metode pembelajaran yang paling sesuai dengan kebutuhanmu.</p>
		</div>

		<div class="grid-2 pricing-grid">
			<!-- TODO: Update pricing figures when finalized -->
			<div class="card pricing-card">
				<div class="pricing-header">
					<div class="pricing-icon"><i class="fa-solid fa-building-columns"></i></div>
					<h3>Program Onsite</h3>
					<p class="pricing-subtitle">Belajar langsung di studio Croma Music</p>
				</div>
				<div class="pricing-price">
					<span class="currency">Mulai dari</span>
					<span class="amount">Rp —.——</span>
					<span class="period">/ sesi</span>
				</div>
				<ul class="pricing-benefits">
					<li><i class="fa-solid fa-check text-gold"></i> Fasilitas studio & instrumen lengkap</li>
					<li><i class="fa-solid fa-check text-gold"></i> Pendampingan 1-on-1 mentor pengajar</li>
					<li><i class="fa-solid fa-check text-gold"></i> Jadwal latihan mingguan tetap</li>
					<li><i class="fa-solid fa-check text-gold"></i> Akses modul & partitur digital</li>
				</ul>
				<a href="https://wa.me/628123456789?text=Halo%20Admin,%20saya%20tertarik%20tanya%20paket%20Onsite" target="_blank" rel="noopener noreferrer" class="btn btn-primary full-width">
					Konsultasi Paket Onsite
				</a>
			</div>

			<!-- TODO: Update pricing figures when finalized -->
			<div class="card pricing-card">
				<div class="pricing-header">
					<div class="pricing-icon"><i class="fa-solid fa-house-chimney-user"></i></div>
					<h3>Program Home Visit</h3>
					<p class="pricing-subtitle">Mentor datang langsung to rumahmu</p>
				</div>
				<div class="pricing-price">
					<span class="currency">Mulai dari</span>
					<span class="amount">Rp —.——</span>
					<span class="period">/ sesi</span>
				</div>
				<ul class="pricing-benefits">
					<li><i class="fa-solid fa-check text-gold"></i> Hemat waktu tanpa perlu perjalanan</li>
					<li><i class="fa-solid fa-check text-gold"></i> Kenyamanan latihan di rumah sendiri</li>
					<li><i class="fa-solid fa-check text-gold"></i> Pendampingan 1-on-1 privat</li>
					<li><i class="fa-solid fa-check text-gold"></i> Bebas atur jadwal reschedule</li>
				</ul>
				<a href="https://wa.me/628123456789?text=Halo%20Admin,%20saya%20tertarik%20tanya%20paket%20Home%20Visit" target="_blank" rel="noopener noreferrer" class="btn btn-primary full-width">
					Konsultasi Home Visit
				</a>
			</div>
		</div>
	</div>
</section>

<section id="faq" class="section">
	<div class="container">
		<div class="section-header text-center">
			<h2>Pertanyaan Umum (FAQ)</h2>
			<p class="subtitle-text">Jawaban atas pertanyaan yang paling sering diajukan calon murid.</p>
		</div>

		<div class="faq-accordion">
			<details class="faq-item">
				<summary>Apakah saya harus memiliki instrumen musik sendiri terlebih dahulu?</summary>
				<div class="faq-content">
					<p>Untuk program Onsite, seluruh instrumen telah kami sediakan lengkap di studio. Untuk program Home Visit, disarankan memiliki alat musik pribadi di rumah agar proses belajar mandiri dapat maksimal.</p>
				</div>
			</details>

			<details class="faq-item">
				<summary>Berapa usia minimum calon murid yang bisa mendaftar?</summary>
				<div class="faq-content">
					<p>Kami menerima murid mulai dari usia 5 tahun hingga dewasa. Kurikulum akan disesuaikan dengan tingkat usia dan pengalaman musik peserta.</p>
				</div>
			</details>

			<details class="faq-item">
				<summary>Bagaimana prosedur jika saya perlu mengajukan reschedule latihan?</summary>
				<div class="faq-content">
					<p>Pengajuan jadwal ulang dapat dilakukan minimal 24 jam sebelum sesi dimulai melalui konfirmasi kepada Admin atau via dashboard sistem kami.</p>
				</div>
			</details>

			<details class="faq-item">
				<summary>Berapa durasi waktu untuk setiap sesi latihan?</summary>
				<div class="faq-content">
					<p>Setiap sesi privat berlangsung selama 45-60 menit, terdiri dari pemanasan, latihan materi teknis, dan evaluasi hasil latihan.</p>
				</div>
			</details>

			<details class="faq-item">
				<summary>Apakah ada trial gratis sebelum saya mendaftar secara resmi?</summary>
				<div class="faq-content">
					<p>Ya! Kami menyediakan sesi uji coba 1-on-1 secara gratis agar Anda dapat merasakan metode mengajar mentor dan berkonsultasi langsung.</p>
				</div>
			</details>
		</div>
	</div>
</section>
@endsection
