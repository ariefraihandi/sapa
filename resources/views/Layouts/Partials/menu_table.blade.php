<table class="table table-sm table-hover align-middle mb-0">
    <thead class="table-light">
        <tr>
            <th class="ps-3 py-2" style="width: 40px;">No</th>
            <th class="py-2">Nama Menu</th>
            <th class="py-2">Icon</th>
            <th class="py-2">URL / Route</th>
            <th class="text-center py-2">Tipe</th>
            <th class="text-center py-2" style="width: 120px;">Urutan</th>
            <th class="text-center py-2">Status</th>
            <th class="text-center py-2 pe-3" style="width: 120px;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($menus as $index => $menu)
            <tr>
                <td class="ps-3 fw-bold text-muted small py-2">{{ $index + 1 }}</td>
                <td class="py-2">
                    <div class="text-dark font-weight-bold small">{{ $menu->name }}</div>
                </td>
                <td class="py-2">
                    @if($menu->icon)
                        <i class="{{ $menu->icon }} text-success me-1"></i>
                        <code class="small text-muted">{{ $menu->icon }}</code>
                    @else
                        <span class="text-muted small">-</span>
                    @endif
                </td>
                <td class="py-2">
                    @if($menu->url)
                        <code class="text-primary font-weight-bold" style="font-size: 0.75rem;">{{ $menu->url }}</code>
                    @else
                        <span class="badge bg-light text-muted border" style="font-size: 0.68rem;">Dropdown Menu</span>
                    @endif
                </td>
                <td class="text-center py-2">
                    @if($menu->is_dropdown)
                        <span class="badge bg-soft-info text-info border px-2 py-1" style="font-size: 0.7rem; background-color: #e0f7fa;">Dropdown</span>
                    @else
                        <span class="badge bg-soft-secondary text-secondary border px-2 py-1" style="font-size: 0.7rem;">Single URL</span>
                    @endif
                </td>

                <!-- Tombol Arrow Up & Down untuk Reorder Menu -->
                <td class="text-center py-2">
                    <div class="d-flex align-items-center justify-content-center gap-1">
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1 btn-reorder" 
                                data-id="{{ $menu->id }}" data-type="menu" data-direction="up" title="Naikkan Urutan">
                            <i class="fa-solid fa-arrow-up"></i>
                        </button>
                        <span class="fw-bold text-dark small px-1">{{ $menu->order }}</span>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1 btn-reorder" 
                                data-id="{{ $menu->id }}" data-type="menu" data-direction="down" title="Turunkan Urutan">
                            <i class="fa-solid fa-arrow-down"></i>
                        </button>
                    </div>
                </td>

                <td class="text-center py-2">
                    @if($menu->is_active)
                        <span class="badge bg-soft-success text-success font-weight-bold px-2 py-1" style="font-size: 0.7rem; background-color: #e8f5e9;">
                            Aktif
                        </span>
                    @else
                        <span class="badge bg-soft-danger text-danger font-weight-bold px-2 py-1" style="font-size: 0.7rem; background-color: #ffebee;">
                            Non-Aktif
                        </span>
                    @endif
                </td>
                <td class="text-center pe-3 py-2">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1" title="Edit Menu" data-bs-toggle="modal" data-bs-target="#modalEditMenu{{ $menu->id }}">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        
                        <!-- Tombol Hapus Menu dengan SweetAlert -->
                        <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1 btn-delete-menu" 
                                data-id="{{ $menu->id }}" 
                                data-name="{{ $menu->name }}" 
                                title="Hapus Menu">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="fa-solid fa-list display-4 d-block mb-1 text-muted"></i>
                    Belum ada data Menu yang ditemukan.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>