<div class="table-responsive">
    <table class="table table-hover align-middle w-100" id="tablePengunjungMsAceh">
        <thead class="table-light">
            <tr>
                <th class="text-center" style="width: 40px;">No</th>
                <th style="min-width: 220px;">Pemohon</th>
                <th class="text-center" style="width: 170px;">Tujuan & Layanan</th>
                <th>Keperluan</th>
                <th class="text-center" style="width: 160px;">Tindakan / WA</th>
                <th class="text-center" style="width: 80px;">Aksi</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@push('scripts')
<script>
    window.tableMsAceh = window.tableMsAceh || null;

    $(document).ready(function() {
        if ($('#tablePengunjungMsAceh').length) {
            window.tableMsAceh = $('#tablePengunjungMsAceh').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('ptsp.pengunjung.index') }}",
                    data: { type: 'ms_aceh' }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'pemohon_html', name: 'pemohon_html' },
                    { data: 'tujuan_layanan_html', name: 'satker.satker_short_name', className: 'text-center' },
                    { data: 'keperluan', name: 'keperluan' },
                    { data: 'kontak_wa_html', name: 'no_hp', className: 'text-center' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ]
            });
        }
    });
</script>
@endpush