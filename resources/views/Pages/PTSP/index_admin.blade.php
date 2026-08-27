@extends('Layouts.app')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--primary-dark, #064e3b);">Monitoring PTSP</h3>
            <p class="text-muted small mb-0">Kelola dan tinjau data operasional PTSP Mahkamah Syar'iyah Se-Aceh.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Satker</th>
                            <th>Penanggung Jawab</th>
                            <th>No. WA Layanan</th>
                            <th>Status WA</th>
                            <th>Panggilan Suara</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($satkers as $index => $item)
                            @php $ptsp = $item->ptspDaerah; @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ asset('assets/images/satker/' . (($item->logo && file_exists(public_path('assets/images/satker/' . $item->logo))) ? $item->logo : 'sapa.png')) }}" 
                                            alt="Logo" style="width: 32px; height: 32px; object-fit: contain;">
                                        <div>
                                            <strong class="d-block">{{ $item->satker_short_name ?? $item->satker_name }}</strong>
                                            <span class="text-muted small">{{ $item->satker_vshort }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $ptsp->nama_pj ?? '-' }}</div>
                                    <span class="text-muted small"><i class="fa-solid fa-phone me-1"></i>{{ $ptsp->no_hp_pj ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($ptsp && $ptsp->no_wa_layanan)
                                        <a href="https://wa.me/{{ $ptsp->no_wa_layanan }}" target="_blank" class="text-success fw-bold text-decoration-none">
                                            <i class="fa-brands fa-whatsapp me-1"></i>{{ $ptsp->no_wa_layanan }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ptsp && $ptsp->has_whatsapp_service)
                                        <span class="badge bg-success text-white px-3 py-2" style="border-radius: 50px;">
                                            <i class="fa-solid fa-check me-1"></i> Siap Digunakan
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark px-3 py-2" style="border-radius: 50px;">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i> Belum Siap / Ada Kendala
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($ptsp && $ptsp->is_call_able)
                                        <span class="badge bg-success text-white px-3 py-2" style="border-radius: 50px;">
                                            <i class="fa-solid fa-headset me-1"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-danger text-white px-3 py-2" style="border-radius: 50px;">
                                            <i class="fa-solid fa-phone-slash me-1"></i> Tidak Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditPtsp{{ $item->id }}">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                </td>
                            </tr>

                            <!-- MODAL EDIT PTSP (PER SATKER) -->
                            <div class="modal fade" id="modalEditPtsp{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content" style="border-radius: 16px;">
                                        <form action="{{ route('ptsp.profil-ptsp.update', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Edit PTSP - {{ $item->satker_short_name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Nama Penanggung Jawab</label>
                                                    <input type="text" name="nama_pj" class="form-control" value="{{ $ptsp->nama_pj ?? '' }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">No. HP Penanggung Jawab</label>
                                                    <input type="text" name="no_hp_pj" class="form-control" value="{{ $ptsp->no_hp_pj ?? '' }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">No. WA Layanan PTSP (Publik)</label>
                                                    <input type="text" name="no_wa_layanan" class="form-control" value="{{ $ptsp->no_wa_layanan ?? '' }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Status Kesiapan WA</label>
                                                    <select name="has_whatsapp_service" class="form-select">
                                                        <option value="1" {{ ($ptsp && $ptsp->has_whatsapp_service) ? 'selected' : '' }}>Ada dan Siap Digunakan</option>
                                                        <option value="0" {{ ($ptsp && !$ptsp->has_whatsapp_service) ? 'selected' : '' }}>Belum Siap / Ada Kendala</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Status Panggilan Suara (Call Able)</label>
                                                    <select name="is_call_able" class="form-select">
                                                        <option value="1" {{ ($ptsp && $ptsp->is_call_able) ? 'selected' : '' }}>Aktif (Headset / Audio Ready)</option>
                                                        <option value="0" {{ ($ptsp && !$ptsp->is_call_able) ? 'selected' : '' }}>Tidak Aktif</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection