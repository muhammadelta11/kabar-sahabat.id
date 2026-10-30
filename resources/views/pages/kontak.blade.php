@extends('pages.layout')

@section('page-title', 'Kontak')
@section('page-description', 'Hubungi redaksi Kabar Sahabat untuk informasi, hak jawab, kerja sama, dan pengiriman tulisan.')

@section('page-content')
@php
    $email = $siteSettings->email ?? 'kabarsahabat@gmail.com';
    $address = $siteSettings->address ?? 'Gedung Rektorat Kampus 3 UIN Walisongo Semarang, Jl. Prof. Hamka, Tambakaji, Ngaliyan, Kota Semarang, Jawa Tengah 50185';

    $kontakLain = [
        ['lembaga' => 'IKA PMII Komisariat UIN Walisongo Semarang', 'nama' => 'Jamal Luthfi, S.Fil., M.E.', 'whatsapp' => '081390383881'],
        ['lembaga' => 'PMII Komisariat UIN Walisongo Semarang', 'nama' => 'M. Yusrul Rizanul Muna', 'whatsapp' => '089648444904'],
    ];
@endphp

<h2 class="text-2xl font-black text-neutral-900 font-serif leading-tight mb-3">Mari Terhubung</h2>
<p class="mb-6">Memiliki pertanyaan, ingin menyampaikan hak jawab atau koreksi, ingin berkolaborasi, atau membutuhkan informasi? Silakan menghubungi redaksi Kabar Sahabat melalui kontak berikut.</p>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
    <div class="bg-neutral-50 border border-neutral-200 rounded-lg p-5 space-y-3 text-sm">
        <h3 class="font-bold text-neutral-900">Redaksi Kabar Sahabat</h3>
        <p><span class="block text-xs font-bold uppercase tracking-wider text-neutral-500">Email</span><a href="mailto:{{ $email }}" class="text-[#003399] font-medium hover:underline">{{ $email }}</a></p>
        @if(filled($siteSettings->phone ?? null))
        <p><span class="block text-xs font-bold uppercase tracking-wider text-neutral-500">Telepon</span><a href="tel:{{ $siteSettings->phone }}" class="text-[#003399] font-medium hover:underline">{{ $siteSettings->phone }}</a></p>
        @endif
        <p><span class="block text-xs font-bold uppercase tracking-wider text-neutral-500">Alamat</span>{{ $address }}</p>
    </div>

    <div class="bg-neutral-50 border border-neutral-200 rounded-lg p-5 space-y-4 text-sm">
        @foreach($kontakLain as $kontak)
        <div>
            <h3 class="font-bold text-neutral-900">{{ $kontak['lembaga'] }}</h3>
            <p>Ketua: {{ $kontak['nama'] }}</p>
            <p>WhatsApp: <a href="https://wa.me/62{{ ltrim($kontak['whatsapp'], '0') }}" target="_blank" rel="noopener noreferrer" class="text-[#003399] font-medium hover:underline">{{ $kontak['whatsapp'] }}</a></p>
        </div>
        @endforeach
    </div>
</div>

<h3 class="text-lg font-black text-[#003399] mb-2">Kerja Sama &amp; Iklan</h3>
<p class="mb-6">Kabar Sahabat terbuka untuk berkolaborasi dengan perguruan tinggi, alumni, organisasi mahasiswa, komunitas, lembaga, media, akademisi, pelaku usaha, pemerintah, dan masyarakat umum. Kolaborasi dapat berupa publikasi, liputan, diskusi, kampanye sosial, kegiatan pendidikan, dokumentasi, sponsorship, media partner, maupun pemasangan iklan.</p>

<h3 class="text-lg font-black text-[#003399] mb-2">Kirim Tulisan</h3>
<p class="mb-4">Punya cerita atau gagasan? Kirimkan berita, opini, esai, profil tokoh, atau liputan kegiatan Anda. Setiap naskah akan melalui proses seleksi dan penyuntingan redaksi.</p>
<a href="{{ route('submissions.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#003399] text-white text-sm font-bold rounded-lg hover:bg-[#002266] transition-colors">Kirim Tulisan &rarr;</a>

<div class="mt-8">
    <h3 class="text-lg font-black text-[#003399]">Ikuti Kabar Sahabat</h3>
    @include('partials.social-links')
</div>
@endsection
