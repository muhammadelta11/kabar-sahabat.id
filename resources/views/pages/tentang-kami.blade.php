@extends('pages.layout')

@section('page-title', 'Tentang Kami')
@section('page-description', 'Kabar Sahabat adalah media informasi, publikasi, dokumentasi, dan pertukaran gagasan yang tumbuh dari ekosistem PMII UIN Walisongo Semarang dan terbuka untuk publik.')

@section('page-content')
<div class="space-y-4">
    <h2 class="text-2xl font-black text-neutral-900 font-serif leading-tight">
        <span class="text-[#003399]">Kabar</span> <span class="text-[#FFCC00]">Sahabat</span>
    </h2>
    <p class="text-sm font-bold uppercase tracking-wider text-neutral-500">Media Pergerakan, Gagasan, dan Ruang Kolaborasi</p>

    <p>Kabar Sahabat lahir dari semangat untuk membangun ruang publik yang hidup melalui informasi, gagasan, dokumentasi, dan kolaborasi.</p>
    <p>Berangkat dari ekosistem PMII UIN Walisongo Semarang, Kabar Sahabat dikembangkan sebagai media yang dapat mempertemukan berbagai latar belakang dan kepentingan dalam satu ruang informasi: kader, alumni, mahasiswa, akademisi, komunitas, organisasi, lembaga, dan masyarakat luas.</p>
    <p>Kami percaya bahwa organisasi tidak hanya membutuhkan gerakan di lapangan, tetapi juga membutuhkan <strong>dokumentasi, literasi, publikasi, dan produksi pengetahuan</strong> agar setiap proses tidak berhenti menjadi kenangan. Karena itu, Kabar Sahabat berupaya menghadirkan informasi yang aktual, tulisan yang bernas, serta dokumentasi yang dapat menjadi bagian dari arsip perjalanan mahasiswa, alumni, organisasi, dan masyarakat.</p>
    <p>Kabar Sahabat terbuka terhadap kolaborasi dengan berbagai pihak. Selama memiliki semangat membangun pengetahuan, memperluas manfaat, dan menghadirkan informasi yang bertanggung jawab, ruang kolaborasi selalu terbuka.</p>

    <blockquote class="bg-neutral-50 border-l-4 border-[#003399] p-5 rounded-r-lg font-serif text-lg font-bold text-neutral-900">
        Merekam peristiwa. Menyebarkan gagasan. Menghubungkan sahabat.
    </blockquote>

    <h3 class="text-lg font-black text-[#003399] pt-4">Visi</h3>
    <p>Menjadi media independen yang dipercaya masyarakat dalam menyajikan informasi yang akurat, berimbang, dan berkualitas.</p>

    <h3 class="text-lg font-black text-[#003399] pt-2">Misi</h3>
    <ol class="list-decimal pl-5 space-y-1">
        <li>Menyajikan berita dan informasi secara akurat, jujur, dan berimbang.</li>
        <li>Memberikan konteks dan analisis terhadap peristiwa-peristiwa penting.</li>
        <li>Menjadi ruang gagasan, opini, dan dialog yang konstruktif.</li>
        <li>Mendokumentasikan perjalanan mahasiswa, alumni, organisasi, dan masyarakat.</li>
        <li>Menjaga independensi dan integritas jurnalistik sesuai Kode Etik Jurnalistik.</li>
    </ol>

    <p class="pt-4">Kenali tim kami di halaman <a href="{{ route('pages.redaksi') }}" class="text-[#003399] font-bold hover:underline">Redaksi</a>, atau hubungi kami melalui halaman <a href="{{ route('pages.kontak') }}" class="text-[#003399] font-bold hover:underline">Kontak</a>.</p>
</div>
@endsection
