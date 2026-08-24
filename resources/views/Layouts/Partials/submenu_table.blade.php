<table class="table table-hover table-striped align-middle mb-0">
    <thead class="table-light">
        <tr>
            <th width="8%" class="text-center">No</th>
            <th>Nama Submenu</th>
            <th>URL / Route</th>
            <th width="15%" class="text-center">Urutan</th>
            <th width="15%" class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($submenusGrouped as $menuId => $submenus)
            @php $parentMenu = $submenus->first()->menu; @endphp
            <!-- HEADER GROUPING MENU UTAMA -->
            <tr class="table-secondary font-weight-bold">
                <td colspan="5" class="py-2 px-3 bg-light">
                    <i class="{{ $parentMenu->icon ?? 'fa-solid fa-folder' }} me-2 text-success"></i>
                    <span class="text-dark fw-bold">{{ strtoupper($parentMenu->name ?? 'TANPA PARENT') }}</span>
                    <span class="badge bg-success ms-2">{{ count($submenus) }} Submenu</span>
                </td>
            </tr>

            <!-- ITEM SUBMENU DI BAWAH MENU PARENT -->
            @foreach($submenus as $index => $sub)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <strong class="text-primary">{{ $sub->submenu }}</strong>
                    </td>
                    <td><code>{{ $sub->url }}</code></td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-reorder" 
                                data-id="{{ $sub->id }}" data-type="submenu" data-direction="up" title="Naikkan Urutan">
                                <i class="fa-solid fa-arrow-up"></i>
                            </button>
                            <span class="btn btn-light disabled text-dark fw-bold px-2">{{ $sub->order }}</span>
                            <button type="button" class="btn btn-outline-secondary btn-reorder" 
                                data-id="{{ $sub->id }}" data-type="submenu" data-direction="down" title="Turunkan Urutan">
                                <i class="fa-solid fa-arrow-down"></i>
                            </button>
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger btn-delete-submenu" 
                            data-id="{{ $sub->id }}" data-name="{{ $sub->submenu }}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        @empty
            <tr>
                <td colspan="5" class="text-center py-4 text-muted">
                    <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                    Tidak ada data submenu yang ditemukan.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>