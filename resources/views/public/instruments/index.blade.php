@extends('layouts.public')

@section('title', 'Katalog Instrumen — Croma Music')
@section('header-class', 'header-light')

@section('content')
<section class="page-header section bg-light">
    <div class="container">
        <div class="section-header text-center hidden-element">
            <span class="eyebrow">Pilihan Program</span>
            <h2>Katalog Instrumen Musik</h2>
            <p>Pilih instrumen yang ingin kamu kuasai dan jelajahi kurikulum serta mentor pendampingnya.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="instrument-catalog-grid grid-4">
            @foreach($programs as $program)
                <div class="card instrument-card hidden-element {{ $program['delay'] }}">
                    <div class="instrument-img-wrapper">
                        <img src="{{ asset($program['img']) }}" alt="{{ $program['title'] }}" loading="lazy" />
                    </div>
                    <div class="card-body">
                        <span class="eyebrow">{{ $program['family'] }}</span>
                        <h3>{{ $program['title'] }}</h3>
                        <p class="genre-text">{{ $program['genres'] }}</p>
                        <p class="level-text">{{ $program['levels'] }}</p>
                        <div class="card-action-row">
                            <span class="mentor-count"><i class="fa-solid fa-user-graduate"></i> {{ $program['mentors_count'] }} Mentor</span>
                            <a href="{{ route('instruments.show', $program['slug']) }}" class="btn-text">
                                Lihat Detail <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
