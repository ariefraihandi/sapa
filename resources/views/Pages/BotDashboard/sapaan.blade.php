@extends('Layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="text-success fw-bold mb-1">{{ $title }}</h4>
            <p class="mb-0 text-muted small">Kelola pesan sapaan awal bot WhatsApp.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-body p-4">
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

                    @if($sapaan)
                        <form action="{{ route('bot.sapaan.update', $sapaan['id']) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="answer" class="form-label fw-bold">Pesan Sapaan Awal</label>
                                <textarea class="form-control" id="answer" name="answer" rows="10" required>{{ $sapaan['answer'] }}</textarea>
                                <div class="form-text">Pesan ini akan dikirim otomatis ke pengguna yang pertama kali chat atau setelah 24 jam.</div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ $sapaan['is_active'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktif</label>
                            </div>
                            <button type="submit" class="btn btn-success"><i class="fa-solid fa-save me-1"></i> Simpan Perubahan</button>
                        </form>
                    @else
                        <div class="alert alert-warning">
                            Sapaan awal (keyword <strong>[SAPAAN_AWAL]</strong>) belum ditemukan di database bot.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

