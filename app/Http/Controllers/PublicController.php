<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicController extends Controller
{
    private function getProgramsData()
    {
        return [
            ['title' => 'Piano', 'slug' => 'piano', 'desc' => 'Klasik, Pop, & Jazz', 'img' => 'images/piano.avif', 'family' => 'Keys', 'genres' => 'Klasik · Pop · Jazz', 'levels' => 'Beginner · Intermediate · Advanced', 'mentors_count' => 3, 'delay' => ''],
            ['title' => 'Gitar', 'slug' => 'gitar', 'desc' => 'Akustik & Elektrik', 'img' => 'images/guitar.avif', 'family' => 'Strings', 'genres' => 'Akustik · Elektrik · Rock', 'levels' => 'Beginner · Intermediate · Advanced', 'mentors_count' => 3, 'delay' => 'delay-100'],
            ['title' => 'Drum', 'slug' => 'drum', 'desc' => 'Rhythm & Percussion', 'img' => 'images/drums.avif', 'family' => 'Percussion', 'genres' => 'Pop · Rock · Jazz', 'levels' => 'Beginner · Intermediate · Advanced', 'mentors_count' => 3, 'delay' => 'delay-200'],
            ['title' => 'Vokal', 'slug' => 'vokal', 'desc' => 'Teknik & Performance', 'img' => 'images/vocals.avif', 'family' => 'Vocals', 'genres' => 'Pop · RnB · Klasik', 'levels' => 'Beginner · Intermediate · Advanced', 'mentors_count' => 3, 'delay' => 'delay-300'],
            ['title' => 'Keyboard', 'slug' => 'keyboard', 'desc' => 'Synthesizer & Arranger', 'img' => 'images/keyboards.avif', 'family' => 'Keys', 'genres' => 'Pop · EDM · Arranger', 'levels' => 'Beginner · Intermediate · Advanced', 'mentors_count' => 3, 'delay' => ''],
            ['title' => 'Bass', 'slug' => 'bass', 'desc' => 'Groove & Slap Technique', 'img' => 'images/bass.avif', 'family' => 'Strings', 'genres' => 'Funk · Rock · Jazz', 'levels' => 'Beginner · Intermediate · Advanced', 'mentors_count' => 3, 'delay' => 'delay-100'],
            ['title' => 'Flute', 'slug' => 'flute', 'desc' => 'Klasik & Orkestra', 'img' => 'images/flute.avif', 'family' => 'Brass', 'genres' => 'Klasik · Orchestral', 'levels' => 'Beginner · Intermediate · Advanced', 'mentors_count' => 3, 'delay' => 'delay-200'],
            ['title' => 'Saxophone', 'slug' => 'saxophone', 'desc' => 'Jazz & Pop Brass', 'img' => 'images/sax.avif', 'family' => 'Brass', 'genres' => 'Jazz · Pop · Blues', 'levels' => 'Beginner · Intermediate · Advanced', 'mentors_count' => 3, 'delay' => 'delay-300'],
        ];
    }

    private function getMentorsData()
    {
        return [
            ['name' => 'Kak Budi', 'slug' => 'kak-budi', 'badge' => 'Piano Expert', 'category' => 'piano', 'featured' => true, 'experience' => '7+ Tahun', 'delay' => ''],
            ['name' => 'Kak Andi', 'slug' => 'kak-andi', 'badge' => 'Classical Piano', 'category' => 'piano', 'featured' => false, 'experience' => '5+ Tahun', 'delay' => 'delay-100'],
            ['name' => 'Kak Siska', 'slug' => 'kak-siska', 'badge' => 'Jazz Piano', 'category' => 'piano', 'featured' => false, 'experience' => '6+ Tahun', 'delay' => 'delay-200'],
            ['name' => 'Kak Sarah', 'slug' => 'kak-sarah', 'badge' => 'Vocal Coach', 'category' => 'vocal', 'featured' => true, 'experience' => '8+ Tahun', 'delay' => 'delay-300'],
            ['name' => 'Kak Maya', 'slug' => 'kak-maya', 'badge' => 'Pop Vocal', 'category' => 'vocal', 'featured' => false, 'experience' => '4+ Tahun', 'delay' => ''],
            ['name' => 'Kak Rio', 'slug' => 'kak-rio', 'badge' => 'RnB Vocal', 'category' => 'vocal', 'featured' => false, 'experience' => '5+ Tahun', 'delay' => 'delay-100'],
            ['name' => 'Kak Reza', 'slug' => 'kak-reza', 'badge' => 'Guitarist', 'category' => 'guitar', 'featured' => true, 'experience' => '9+ Tahun', 'delay' => 'delay-200'],
            ['name' => 'Kak Dinda', 'slug' => 'kak-dinda', 'badge' => 'Acoustic Guitar', 'category' => 'guitar', 'featured' => false, 'experience' => '4+ Tahun', 'delay' => 'delay-300'],
            ['name' => 'Kak Bima', 'slug' => 'kak-bima', 'badge' => 'Electric Guitar', 'category' => 'guitar', 'featured' => false, 'experience' => '6+ Tahun', 'delay' => ''],
            ['name' => 'Kak Doni', 'slug' => 'kak-doni', 'badge' => 'Drummer', 'category' => 'drum', 'featured' => true, 'experience' => '10+ Tahun', 'delay' => 'delay-100'],
            ['name' => 'Kak Eka', 'slug' => 'kak-eka', 'badge' => 'Percussionist', 'category' => 'drum', 'featured' => false, 'experience' => '5+ Tahun', 'delay' => 'delay-200'],
            ['name' => 'Kak Gilang', 'slug' => 'kak-gilang', 'badge' => 'Rock Drummer', 'category' => 'drum', 'featured' => false, 'experience' => '7+ Tahun', 'delay' => 'delay-300'],
            ['name' => 'Kak Rian', 'slug' => 'kak-rian', 'badge' => 'Keyboardist', 'category' => 'keyboard', 'featured' => true, 'experience' => '6+ Tahun', 'delay' => ''],
            ['name' => 'Kak Tika', 'slug' => 'kak-tika', 'badge' => 'Synth Expert', 'category' => 'keyboard', 'featured' => false, 'experience' => '4+ Tahun', 'delay' => 'delay-100'],
            ['name' => 'Kak Kevin', 'slug' => 'kak-kevin', 'badge' => 'Pop Keyboard', 'category' => 'keyboard', 'featured' => false, 'experience' => '5+ Tahun', 'delay' => 'delay-200'],
            ['name' => 'Kak Dika', 'slug' => 'kak-dika', 'badge' => 'Bassist', 'category' => 'bass', 'featured' => true, 'experience' => '8+ Tahun', 'delay' => 'delay-300'],
            ['name' => 'Kak Laras', 'slug' => 'kak-laras', 'badge' => 'Slap Bass', 'category' => 'bass', 'featured' => false, 'experience' => '5+ Tahun', 'delay' => ''],
            ['name' => 'Kak Toni', 'slug' => 'kak-toni', 'badge' => 'Jazz Bass', 'category' => 'bass', 'featured' => false, 'experience' => '7+ Tahun', 'delay' => 'delay-100'],
            ['name' => 'Kak Dina', 'slug' => 'kak-dina', 'badge' => 'Flutist', 'category' => 'flute', 'featured' => true, 'experience' => '6+ Tahun', 'delay' => 'delay-200'],
            ['name' => 'Kak Bayu', 'slug' => 'kak-bayu', 'badge' => 'Classical Flute', 'category' => 'flute', 'featured' => false, 'experience' => '4+ Tahun', 'delay' => 'delay-300'],
            ['name' => 'Kak Nisa', 'slug' => 'kak-nisa', 'badge' => 'Wind Instrument', 'category' => 'flute', 'featured' => false, 'experience' => '5+ Tahun', 'delay' => ''],
            ['name' => 'Kak Aldo', 'slug' => 'kak-aldo', 'badge' => 'Saxophonist', 'category' => 'saxophone', 'featured' => true, 'experience' => '9+ Tahun', 'delay' => 'delay-100'],
            ['name' => 'Kak Fira', 'slug' => 'kak-fira', 'badge' => 'Alto Sax', 'category' => 'saxophone', 'featured' => false, 'experience' => '4+ Tahun', 'delay' => 'delay-200'],
            ['name' => 'Kak Denny', 'slug' => 'kak-denny', 'badge' => 'Tenor Sax', 'category' => 'saxophone', 'featured' => false, 'experience' => '8+ Tahun', 'delay' => 'delay-300'],
        ];
    }

    public function instruments()
    {
        $programs = $this->getProgramsData();

        return view('public.instruments.index', compact('programs'));
    }

    public function instrumentDetail($slug)
    {
        $programs = collect($this->getProgramsData());
        $instrument = $programs->firstWhere('slug', $slug) ?? $programs->first();

        $mentors = collect($this->getMentorsData())
            ->filter(fn ($m) => strtolower($m['category']) === strtolower($instrument['slug']) || $instrument['slug'] === 'piano' && $m['category'] === 'piano')
            ->values()
            ->all();

        if (empty($mentors)) {
            $mentors = array_slice($this->getMentorsData(), 0, 3);
        }

        return view('public.instruments.show', compact('instrument', 'mentors'));
    }

    public function mentors(Request $request)
    {
        $mentors = $this->getMentorsData();

        return view('public.mentors.index', compact('mentors'));
    }

    public function mentorProfile($slug)
    {
        $mentors = collect($this->getMentorsData());
        $mentor = $mentors->firstWhere('slug', $slug) ?? $mentors->first();

        return view('public.mentors.show', compact('mentor'));
    }
}
