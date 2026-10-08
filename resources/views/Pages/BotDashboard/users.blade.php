@extends('Layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="text-success fw-bold mb-1">{{ $title }}</h4>
            <p class="mb-0 text-muted small">Daftar riwayat nomor WhatsApp yang telah berinteraksi dengan bot.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead class="table-light">
                        <tr>
                            <th width="10%">No</th>
                            <th width="45%">Nomor HP</th>
                            <th width="45%">Interaksi Terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user['no_hp']) }}" target="_blank" class="text-decoration-none">
                                    <i class="fa-brands fa-whatsapp text-success me-1"></i> {{ $user['no_hp'] }}
                                </a>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($user['last_chat'])->format('d M Y H:i:s') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Belum ada data interaksi pengguna.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof jQuery !== 'undefined' && $.fn.DataTable) {
            $('.datatable').DataTable({
                "order": [[ 2, "desc" ]]
            });
        }
    });
</script>
@endsection

