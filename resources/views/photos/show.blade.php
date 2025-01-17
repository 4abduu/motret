@extends('layouts.app')

@section('content')
    <div class="container mt-5">
    <!-- @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <span id="success-countdown" class="float-end"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
            <span id="error-countdown" class="float-end"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif -->
        <h1 class="my-4">{{ $photo->title }}</h1>
        <div class="row">
            <div class="col-md-8">
                <img src="{{ asset('storage/' . $photo->path) }}" class="img-fluid" alt="{{ $photo->title }}">
            </div>
            <div class="col-md-4">
                <h3>Deskripsi</h3>
                <p>{{ $photo->description }}</p>
                <h3>Hashtags</h3>
                <p>{{ implode(', ', json_decode($photo->hashtags)) }}</p>
                <h3>Diunggah oleh: <a href="{{ route('user.showProfile', $photo->user->username) }}">{{ $photo->user->username }}</a></h3>
                <p>{{ $photo->user->username }}</p>
                <h3>Unduh</h3>
                <form action="{{ route('photos.download', $photo->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download"></i> Unduh
                    </button>
                </form>
                <h3>Laporkan</h3>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#reportModal">
                    <i class="fas fa-exclamation-triangle"></i> Laporkan
                </button>
            </div>
        </div>
    </div>

    <div class="my-4">
            <h3>Jelajahi untuk foto lainnya</h3>
            <div class="row">
                @foreach($randomPhotos as $randomPhoto)
                    <div class="col-md-3 mb-4">
                        <a href="{{ route('photos.show', $randomPhoto->id) }}">
                            <img src="{{ asset('storage/' . $randomPhoto->path) }}" class="img-fluid rounded" alt="{{ $randomPhoto->title }}">
                        </a>
                        <h5 class="mt-2">{{ $randomPhoto->title }}</h5>
                        <p>Hashtags</p>
                        <p>{{ implode(', ', json_decode($randomPhoto->hashtags)) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal Report -->
    <div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">Laporkan Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reportForm" method="POST" action="{{ route('photos.report', $photo->id) }}">
                        @csrf
                        <div class="form-group">
                            <label for="reason">Alasan Melaporkan</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="reason" id="reason1" value="Konten tidak pantas" required>
                                <label class="form-check-label" for="reason1">
                                    Konten tidak pantas
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="reason" id="reason2" value="Spam" required>
                                <label class="form-check-label" for="reason2">
                                    Spam
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="reason" id="reason3" value="Pelecehan" required>
                                <label class="form-check-label" for="reason3">
                                    Pelecehan
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="reason" id="reason4" value="Lainnya" required>
                                <label class="form-check-label" for="reason4">
                                    Lainnya (silakan jelaskan di bawah)
                                </label>
                            </div>
                        </div>
                        <div class="form-group" id="otherReasonGroup" style="display: none;">
                            <label for="other_reason">Alasan Lainnya</label>
                            <input type="text" class="form-control" id="other_reason" name="other_reason">
                        </div>
                        <button type="submit" class="btn btn-danger">Kirim Laporan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('input[name="reason"]').change(function() {
                if ($(this).val() === 'Lainnya') {
                    $('#otherReasonGroup').show();
                    $('#other_reason').attr('required', true);
                } else {
                    $('#otherReasonGroup').hide();
                    $('#other_reason').attr('required', false);
                }
            });

            $('#reportModal').on('hidden.bs.modal', function () {
                $('#reportForm')[0].reset();
                $('#otherReasonGroup').hide();
                $('#other_reason').attr('required', false);
            });
        });
    </script>
@endpush