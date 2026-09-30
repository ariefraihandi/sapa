@forelse($pengunjung as $index => $item)
    @php
        $no_hp_clean = preg_replace('/[^0-9]/', '', $item->no_hp);
        if (str_starts_with($no_hp_clean, '0')) {
            $no_hp_clean = '62' . substr($no_hp_clean, 1);
        }
        $link_wa = "https://wa.me/" . $no_hp_clean;
    @endphp
    <tr>
        <td>{{ $pengunjung->firstItem() + $index }}</td>
        <td>
            <strong class="d-block text-dark fs-15">{{ $item->nama_responden }}</strong>
            <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
                <span class="badge {{ $item->jenis_kelamin == 'L' ? 'bg-primary' : 'bg-danger' }}" style="font-size: 0.7rem;">
                    {{ $item->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                </span>
                @if($item->pekerjaan)
                    <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">
                        <i class="fa-solid fa-briefcase me-1 text-secondary"></i>{{ $item->pekerjaan }}
                    </span>
                @endif
                @if($item->nik)
                    <span class="badge bg-light text-secondary border" style="font-size: 0.7rem;">
                        NIK: {{ $item->nik }}
                    </span>
                @endif
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                <i class="fa-solid fa-clock me-1"></i>{{ $item->created_at->format('d M Y - H:i') }} WIB
            </small>
        </td>

        <td>
            <span class="badge bg-light text-dark border">
                <i class="fa-solid fa-building-columns me-1 text-success"></i>
                {{ $item->satker->satker_short_name ?? $item->satker->satker_name ?? 'MS Aceh' }}
            </span>
        </td>

        <td>
            @if($item->jenis_layanan === 'pesan')
                <span class="badge bg-success text-white fw-semibold px-2 py-1">
                    <i class="fa-brands fa-whatsapp me-1"></i> Pesan
                </span>
            @else
                <span class="badge bg-primary text-white fw-semibold px-2 py-1">
                    <i class="fa-solid fa-phone me-1"></i> Telepon
                </span>
            @endif
        </td>

        <td>
            <div class="p-2 rounded bg-light border text-secondary small text-wrap" style="max-width: 280px;">
                {{ Str::limit($item->keperluan ?: 'Tidak ada catatan keperluan', 70) }}
            </div>
        </td>

        <td class="text-center">
            <a href="{{ $link_wa }}" 
               target="_blank" 
               onclick="markAsFollowedUp('{{ $item->id }}')"
               id="btn-wa-{{ $item->id }}"
               class="btn btn-sm {{ $item->is_tindak_lanjut ? 'btn-success' : 'btn-danger' }} px-3 py-1" 
               style="border-radius: 50px; font-weight: 600; font-size: 0.8rem; white-space: nowrap;">
                <i class="fa-brands fa-whatsapp me-1"></i> {{ $item->no_hp }}
            </a>
        </td>

        <td class="text-center">
            <div class="dropdown">
                <button type="button" class="btn btn-light btn-sm dropdown-toggle border" data-bs-toggle="dropdown">
                    Aksi
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow border-0">
                    <a class="dropdown-item text-primary" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalDetailPengunjung{{ $loop->index }}">
                        <i class="fa-solid fa-eye me-2"></i> Detail
                    </a>
                    <a class="dropdown-item text-warning" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalEditPengunjung{{ $loop->index }}">
                        <i class="fa-solid fa-pen-to-square me-2"></i> Edit
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalDeletePengunjung{{ $loop->index }}">
                        <i class="fa-solid fa-trash me-2"></i> Hapus
                    </a>
                </div>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-5 text-muted">
            <i class="fa-solid fa-folder-open fa-3x mb-3 text-secondary opacity-50 d-block"></i>
            Belum ada data pengunjung PTSP yang terekam.
        </td>
    </tr>
@endforelse