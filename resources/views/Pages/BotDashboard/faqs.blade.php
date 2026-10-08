@extends('Layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="text-success fw-bold mb-0">{{ $title }}</h4>
        <button type="button" class="btn btn-sm btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#addFaqModal">
            <i class="fa-solid fa-plus"></i> Tambah FAQ
        </button>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle datatable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Keyword</th>
                            <th width="50%">Jawaban</th>
                            <th width="10%" class="text-center">Status</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($faqs as $index => $faq)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="badge {{ $faq['keyword'] === '[SAPAAN_AWAL]' ? 'bg-primary' : 'bg-secondary' }}">{{ $faq['keyword'] }}</span></td>
                            <td>{{ Str::limit($faq['answer'], 80) }}</td>
                            <td class="text-center">
                                @if($faq['is_active'])
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Opsi
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <a class="dropdown-item text-primary" href="#" data-bs-toggle="modal" data-bs-target="#editFaqModal{{ $faq['id'] }}">
                                                <i class="fa-solid fa-edit me-2"></i>Edit
                                            </a>
                                        </li>
                                        @if($faq['keyword'] !== '[SAPAAN_AWAL]')
                                        <li>
                                            <form action="{{ route('bot.faqs.destroy', $faq['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus FAQ ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fa-solid fa-trash me-2"></i>Hapus
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editFaqModal{{ $faq['id'] }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content border-0 shadow">
                                    <form action="{{ route('bot.faqs.update', $faq['id']) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit {{ $faq['keyword'] === '[SAPAAN_AWAL]' ? 'Sapaan Awal' : 'FAQ' }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Keyword</label>
                                                <input type="text" class="form-control" name="keyword" value="{{ $faq['keyword'] }}" {{ $faq['keyword'] === '[SAPAAN_AWAL]' ? 'readonly' : 'required' }}>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Jawaban / Balasan</label>
                                                <textarea class="form-control" name="answer" rows="5" required>{{ $faq['answer'] }}</textarea>
                                            </div>
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="is_active_{{ $faq['id'] }}" name="is_active" value="1" {{ $faq['is_active'] ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active_{{ $faq['id'] }}">Aktifkan</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
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

<!-- Add Modal -->
<div class="modal fade" id="addFaqModal" tabindex="-1" aria-labelledby="addFaqModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('bot.faqs.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addFaqModalLabel">Tambah FAQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="keyword" class="form-label">Keyword</label>
                        <input type="text" class="form-control" id="keyword" name="keyword" required placeholder="Contoh: jadwal sidang">
                        <div class="form-text">Gunakan huruf kecil.</div>
                    </div>
                    <div class="mb-3">
                        <label for="answer" class="form-label">Jawaban</label>
                        <textarea class="form-control" id="answer" name="answer" rows="4" required></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof jQuery !== 'undefined' && $.fn.DataTable) {
            $('.datatable').DataTable();
        }
    });
</script>
@endsection

