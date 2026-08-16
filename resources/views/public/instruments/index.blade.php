@extends('layouts.public')

@section('title', 'Katalog Instrumen')
@section('header-class', 'header-light')

@section('content')
<section class="page-header section bg-light">
    <div class="container">
        <div class="section-header text-center">
            <h2>Katalog Instrumen Musik</h2>
            <p class="subtitle-text">Pilih instrumen yang ingin kamu kuasai dan jelajahi kurikulum serta mentor pendampingnya.</p>

            <div class="instrument-search-filter-wrap">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input 
                        type="text" 
                        id="instrument-search-input" 
                        class="search-input-field" 
                        placeholder="Cari instrumen (cth: Gitar, Biola, Vokal)..."
                        aria-label="Cari instrumen musik"
                    />
                </div>

                <div class="instrument-filter-pills" role="tablist" aria-label="Filter Kategori Instrumen">
                    <button class="filter-pill active" data-category="all" role="tab" aria-selected="true">Semua Kategori</button>
                    <button class="filter-pill" data-category="keys" role="tab" aria-selected="false">Keys / Tuts</button>
                    <button class="filter-pill" data-category="strings" role="tab" aria-selected="false">Strings / Gesek-Petik</button>
                    <button class="filter-pill" data-category="percussion" role="tab" aria-selected="false">Percussion / Drum</button>
                    <button class="filter-pill" data-category="brass" role="tab" aria-selected="false">Brass / Tiup</button>
                    <button class="filter-pill" data-category="vocals" role="tab" aria-selected="false">Vocals / Vokal</button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="instrument-catalog-grid grid-4">
            @foreach($programs as $program)
                <div class="card instrument-card" data-category="{{ strtolower($program['family']) }}" data-title="{{ strtolower($program['title']) }}">
                    <div class="instrument-img-wrapper">
                        <img src="{{ asset($program['img']) }}" alt="{{ $program['title'] }}" loading="lazy" />
                    </div>
                    <div class="card-body">
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <span class="eyebrow">{{ $program['family'] }}</span>
                            <span class="badge" style="font-size:0.7rem;padding:0.2rem 0.5rem;background:#f1f5f9;color:#0f172a;border:1px solid #e2e8f0;border-radius:0.25rem">{{ $program['methods'] ?? 'Onsite & Home Visit' }}</span>
                        </div>
                        <h3>{{ $program['title'] }}</h3>
                        <div style="font-size:1.1rem;font-weight:700;color:var(--text-dark);font-variant-numeric:tabular-nums;margin:0.25rem 0">
                            Rp {{ number_format($program['price'], 0, ',', '.') }} <span style="font-size:0.75rem;font-weight:500;color:var(--text-light)">/ bulan (4 sesi)</span>
                        </div>
                        <p class="level-text">Semua Tingkat Kemahiran</p>
                        <div class="card-meta-row margin-top-xs">
                            <span class="mentor-count"><i class="fa-solid fa-user-graduate"></i> {{ $program['mentors_count'] }} Mentor Pendamping</span>
                        </div>
                        <a href="{{ route('instruments.show', $program['slug']) }}" class="btn btn-outline-dark full-width margin-top-sm">
                            Lihat Detail Program <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
