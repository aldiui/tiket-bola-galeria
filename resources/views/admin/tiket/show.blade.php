@extends('layouts.auth')

@section('title', 'E-Tiket')

@push('style')
@endpush

@section('main')
    <div
        class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
        <div class="d-flex align-items-center justify-content-center w-100">
            <div class="row justify-content-center w-100 py-5">
                <div class="col-md-8 text-center">
                    <img src="{{ asset('images/logo-tiket.jpg') }}" class="img-fluid mb-3">
                    <div class="text-dark mb-3">Durasi Bermain :
                        {{ $pengunjungMasuk->durasi_bermain }} Jam </div>
                    <span id="countdown" class="badge bg-primary rounded-3"></span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const targetElement = '#countdown';
            const duration = '{{ $pengunjungMasuk->duration_difference }}';
            updateCountdown(targetElement, duration);
        });
    </script>
@endpush
