<!DOCTYPE html>
<html lang="id">

    <!-- INCLUDE HEAD & STYLES -->
    @include('Pages.Layanan.Partials.head')

    <body>

        <!-- NAVBAR PARTIAL -->
        @include('Pages.Layanan.Partials.navbar')

        <!-- HERO SECTION (Khusus Persyaratan) -->
        <section class="hero">
            <h1 class="hero-title">Informasi & Persyaratan <span>Perkara PTSP</span></h1>
            <p class="hero-subtitle">Pilih Satuan Kerja Mahkamah Syar'iyah untuk melihat daftar rincian dan kelengkapan dokumen persyaratan perkara.</p>
            
            <!-- Search Bar -->
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" onkeyup="filterSatker()" placeholder="Cari nama MS atau Kabupaten/Kota...">
            </div>
        </section>

        <!-- MAIN DIRECTORY GRID -->
        <main class="main-container">
            <div class="grid-container" id="satkerGrid">
                @foreach(collect($daftarSatker)->sortBy('nama_satker') as $item)
                    <!-- CARD SATKER PARTIAL KHUSUS PERSYARATAN -->
                    @include('Pages.Layanan.Partials.card_persyaratan', ['item' => $item])
                @endforeach
            </div>
        </main>

        <!-- FOOTER PARTIAL -->
        @include('Pages.Layanan.Partials.footer')

        <!-- JavaScript Filters -->
        <script>
            function filterSatker() {
                let input = document.getElementById('searchInput').value.toLowerCase();
                let cards = document.getElementsByClassName('satker-card');

                for (let i = 0; i < cards.length; i++) {
                    let titleData = cards[i].getAttribute('data-title');
                    if (titleData.includes(input)) {
                        cards[i].style.display = "flex";
                    } else {
                        cards[i].style.display = "none";
                    }
                }
            }
        </script>
    </body>
</html>