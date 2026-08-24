<!DOCTYPE html>
<html lang="id">

    <!-- INCLUDE HEAD & STYLES -->
    @include('Pages.Layanan.Partials.head')

    <body>

        <!-- NAVBAR PARTIAL -->
        @include('Pages.Layanan.Partials.navbar')

        <!-- HERO PARTIAL -->
        @include('Pages.Layanan.Partials.hero')

        <!-- MAIN DIRECTORY GRID -->
        <main class="main-container">
            <div class="grid-container" id="satkerGrid">
                @foreach(collect($daftarSatker)->sortBy('nama_satker') as $item)
                    <!-- CARD SATKER PARTIAL -->
                    @include('Pages.Layanan.Partials.card_satker', ['item' => $item])
                @endforeach
            </div>
        </main>

        <!-- FOOTER PARTIAL -->
        @include('Pages.Layanan.Partials.footer')

        <!-- JavaScript Filters & Alert Confirmations -->
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

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-wa-confirm');
                
                if (btn) {
                    e.preventDefault();

                    const linkWa = btn.getAttribute('data-link');
                    const namaSatker = btn.getAttribute('data-satker');

                    if (typeof Swal === 'undefined') {
                        window.open(linkWa, '_blank');
                        return;
                    }

                    if (!linkWa || linkWa === '#') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Nomor Tidak Tersedia',
                            text: 'Layanan WhatsApp ' + (namaSatker || '') + ' belum dikonfigurasi.',
                            confirmButtonColor: '#0b6e39'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Hubungi ' + namaSatker + '?',
                        html: 'Anda akan diarahkan ke layanan komunikasi WhatsApp PTSP.<br><br><span style="font-size: 0.9em; color: #6c757d;"><b>Perhatian:</b> Harap gunakan layanan komunikasi ini dengan bijak dan bertanggung jawab.</span>',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#25D366',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fa-brands fa-whatsapp me-1"></i> Lanjutkan ke WA',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.open(linkWa, '_blank');
                        }
                    });
                }
            });
        </script>
    </body>
</html>