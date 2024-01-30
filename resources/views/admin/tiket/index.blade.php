@extends('layouts.auth')

@section('title', 'E-Tiket')

@push('style')
@endpush

@section('main')
<div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
    <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100 py-5">
            <div class="col-md-8 col-lg-5 col-xl-4">
                <div class="card mb-0 border border-primary border-5">
                    <div class="card-body">
                        <h5 class="fw-semibold text-center mb-3">E-Drop Ticket Pengunjung</h5>
                        <div class="text-center">
                            <img src="{{ asset('images/check.png') }}">
                        </div>
                        <ul class="timeline-widget mb-0 position-relative mb-3">
                            <li class="timeline-item d-flex position-relative overflow-hidden">
                                <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                    <span class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                    <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                </div>
                                <div class="timeline-desc text-dark mt-n1">{{ $cekTiket->nama_anak }} ({{ $cekTiket->nama_panggilan }})</div>
                            </li>
                            <li class="timeline-item d-flex position-relative overflow-hidden">
                                <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                    <span class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                    <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                </div>
                                <div class="timeline-desc text-dark mt-n1">{{ $cekTiket->jenis_kelamin }}</div>
                            </li>
                            <li class="timeline-item d-flex position-relative overflow-hidden">
                                <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                    <span class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                    <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                </div>
                                <div class="timeline-desc text-dark mt-n1">Metode Pembayaran : {{ $cekTiket->metode_pembayaran }}</div>
                            </li>
                            <li class="timeline-item d-flex position-relative overflow-hidden">
                                <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                    <span class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                    <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                </div>
                                <div class="timeline-desc text-dark mt-n1">Durasi Bermain : {{ $cekTiket->durasi_bermain }} Jam (<span id="countdown"></span>)</div>
                            </li>
                            <li class="timeline-item d-flex position-relative overflow-hidden">
                                <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                    <span class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                </div>
                                <div class="timeline-desc text-dark mt-n1">Tanggal : {{ formatTanggal($cekTiket->created_at, 'j M Y H:i:s') }}</div>
                            </li>
                        </ul>
                        <div class="text-center">
                            <img src="{{ asset('/storage/pengunjung_masuk/'.$cekTiket->qr_code) }}" width="150px">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function calculateEndTime() {
        var createdAt = new Date('{{ $cekTiket->created_at }}');
        var duration = {{ $cekTiket->durasi_bermain }};
        var endTime = new Date(createdAt.getTime() + (duration * 60 * 1000));
        return endTime;
    }

    function updateCountdown() {
        var endTime = calculateEndTime();
        var now = new Date();

        if (endTime > now) {
            var durationInSeconds = Math.floor((endTime - now) / 1000);
            $('#countdown').text(formatTime(durationInSeconds));
        } else {
            $('#countdown').text('00:00:00');
        }
    }

    function formatTime(seconds) {
        var hours = Math.floor(seconds / 3600);
        var minutes = Math.floor((seconds % 3600) / 60);
        var remainingSeconds = seconds % 60;

        return pad(hours) + ':' + pad(minutes) + ':' + pad(remainingSeconds);
    }

    function pad(num) {
        return num < 10 ? '0' + num : num;
    }

    $(document).ready(function () {
        updateCountdown();
    });
</script>
@endpush

