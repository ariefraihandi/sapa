{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    {{-- 1. URL Statis Publik --}}
    @foreach ($staticUrls as $url)
        <url>
            <loc>{{ $url }}</loc>
            <lastmod>{{ now()->tz('UTC')->toAtomString() }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>1.0</priority>
        </url>
    @endforeach

    {{-- 2. URL Detail Persyaratan per Satker --}}
    @foreach ($satkers as $satker)
        <url>
            <loc>{{ route('public.persyaratan-perkara.detail', ['satker_vshort' => $satker->satker_vshort ?? $satker->id]) }}</loc>
            <lastmod>{{ $satker->updated_at ? $satker->updated_at->tz('UTC')->toAtomString() : now()->tz('UTC')->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach

    {{-- 3. URL Single Persyaratan Perkara (Satker + Jenis Perkara) --}}
    @foreach ($syaratPerkaraActive as $item)
        @if ($item->satker && $item->jenisPerkara)
            <url>
                <loc>{{ route('public.persyaratan-perkara.single', ['satker_vshort' => $item->satker->satker_vshort ?? $item->satker_id, 'jenis_perkara_id' => $item->jenis_perkara_id]) }}</loc>
                <lastmod>{{ now()->tz('UTC')->toAtomString() }}</lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.7</priority>
            </url>
        @endif
    @endforeach

</urlset>