@extends('pages.layout')

@section('page-title', 'Redaksi')
@section('page-description', 'Susunan redaksi Kabar Sahabat: pimpinan perusahaan, pimpinan redaksi, dan redaktur.')

@section('page-content')
@php
    $pimpinan = [
        'Komisaris Utama' => 'Jamal Luthfi',
        'Direktur Utama' => 'Iqbal Alaik',
        'Pimpinan Umum' => 'Khusnul Huda',
        'Wakil Pimpinan Umum' => 'Miftakhul Arif',
        'Sekretaris' => 'Arafat Ikhza Mahmud',
        'Pimpinan Redaksi' => 'Muhammad Husni Mushonifin',
    ];

    $redaktur = ['Zaenal Arifin', 'Septy Aisah', 'Ahsan Fauzi', 'Heromando', 'Gigih'];
@endphp

<h2 class="text-2xl font-black text-neutral-900 font-serif leading-tight mb-6">Susunan Redaksi Kabar Sahabat</h2>

<dl class="divide-y divide-neutral-200 border-y border-neutral-200">
    @foreach($pimpinan as $jabatan => $nama)
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4 py-3">
        <dt class="text-xs sm:text-sm font-bold uppercase tracking-wider text-[#003399]">{{ $jabatan }}</dt>
        <dd class="sm:col-span-2 text-base font-semibold text-neutral-900">{{ $nama }}</dd>
    </div>
    @endforeach
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4 py-3">
        <dt class="text-xs sm:text-sm font-bold uppercase tracking-wider text-[#003399]">Redaktur</dt>
        <dd class="sm:col-span-2">
            <ul class="space-y-1 text-base font-semibold text-neutral-900">
                @foreach($redaktur as $nama)
                <li>{{ $nama }}</li>
                @endforeach
            </ul>
        </dd>
    </div>
</dl>

<div class="mt-8 bg-neutral-50 border-l-4 border-[#003399] p-5 rounded-r-lg text-sm space-y-1">
    <p class="font-bold text-neutral-900">Alamat Redaksi</p>
    <p>{{ $siteSettings->address ?? 'Gedung Rektorat Kampus 3 UIN Walisongo Semarang, Jl. Prof. Hamka, Tambakaji, Ngaliyan, Kota Semarang, Jawa Tengah 50185' }}</p>
    <p>Email: <a href="mailto:{{ $siteSettings->email ?? 'kabarsahabat@gmail.com' }}" class="text-[#003399] font-medium hover:underline">{{ $siteSettings->email ?? 'kabarsahabat@gmail.com' }}</a></p>
</div>
@endsection
