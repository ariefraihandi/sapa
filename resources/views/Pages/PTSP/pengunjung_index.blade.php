@extends('Layouts.app')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: #064e3b;">Rekap & Monitoring Pengunjung PTSP</h3>
            <p class="text-muted small mb-0">Daftar pemohon layanan komunikasi (Pesan & Telepon) via Portal SAPA.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Pemohon</th>
                            <th>Satker Tujuan</th>
                            <th>Jenis Layanan</th>
                            <th class="text-center">Aksi / Kontak WA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengunjung as $index => $item)
                            @php
                                // Format nomor WA ke 62xxx
                                $no_hp_clean = preg_replace('/[^0-9]/', '', $item->no_hp);
                                if (str_starts_with($no_hp_clean, '0')) {
                                    $no_hp_clean = '62' . substr($no_hp_clean, 1);
                                }
                                $link_wa = "https://wa.me/" . $no_hp_clean;
                            @endphp
                            <tr>
                                <td>{{ $pengunjung->firstItem() + $index }}</td>
                                <td>
                                    <strong class="d-block text-dark">{{ $item->nama_responden }}</strong>
                                    <small class="text-muted">{{ $item->created_at->format('d M Y - H:i') }} WIB</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $item->satker->satker_short_name ?? $item->satker->satker_name }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->jenis_layanan === 'pesan')
                                        <span class="badge bg-info text-dark">
                                            <i class="fa-brands fa-whatsapp me-1"></i> Pesan
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="fa-solid fa-phone me-1"></i> Telepon
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <!-- Tombol WA: Merah (Belum Followup) -> Hijau (Sudah Followup) -->
                                    <a href="{{ $link_wa }}" 
                                       target="_blank" 
                                       onclick="markAsFollowedUp('{{ $item->id }}')"
                                       id="btn-wa-{{ $item->id }}"
                                       class="btn btn-sm {{ $item->is_tindak_lanjut ? 'btn-success' : 'btn-danger' }} px-3" 
                                       style="border-radius: 50px; font-weight: 600;">
                                        <i class="fa-brands fa-whatsapp me-1"></i> {{ $item->no_hp }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-folder-open fa-2x mb-2 d-block"></i>
                                    Belum ada data pengunjung PTSP yang terekam.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Link -->
            <div class="d-flex justify-content-end mt-3">
                {{ $pengunjung->links() }}
            </div>
        </div>
    </div>
</div>

<script>
function markAsFollowedUp(id) {
    // Kirim request AJAX untuk ubah status di database tanpa reload halaman
    fetch(`/ptsp/pengunjung/${id}/tindak-lanjut`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Ubah class warna tombol dari Merah (btn-danger) ke Hijau (btn-success) secara instant
            const btn = document.getElementById(`btn-wa-${id}`);
            if (btn) {
                btn.classList.remove('btn-danger');
                btn.classList.add('btn-success');
            }
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endsection