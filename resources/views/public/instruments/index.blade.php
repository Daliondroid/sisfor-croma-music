@extends('layouts.public')

@section('title', 'Katalog Instrumen — Croma Music')
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
                        <span class="eyebrow">{{ $program['family'] }}</span>
                        <h3>{{ $program['title'] }}</h3>
                        <p class="genre-text">{{ $program['genres'] }}</p>
                        <p class="level-text">{{ $program['levels'] }}</p>
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
