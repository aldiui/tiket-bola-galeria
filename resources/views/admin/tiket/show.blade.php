@extends('layouts.auth')

@section('title', 'E-Tiket')

@push('style')
@endpush

@section('main')
    <div
        class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
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
                                        <span
                                            class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                        <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                    </div>
                                    <div class="timeline-desc text-dark mt-n1">{{ $pengunjungMasuk->nama_anak }}
                                        ({{ $pengunjungMasuk->nama_panggilan }})</div>
                                </li>
                                <li class="timeline-item d-flex position-relative overflow-hidden">
                                    <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                        <span
                                            class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                        <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                    </div>
                                    <div class="timeline-desc text-dark mt-n1">{{ $pengunjungMasuk->jenis_kelamin }}
                                    </div>
                                </li>
                                <li class="timeline-item d-flex position-relative overflow-hidden">
                                    <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                        <span
                                            class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                        <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                    </div>
                                    <div class="timeline-desc text-dark mt-n1">Metode Pembayaran :
                                        {{ $pengunjungMasuk->metode_pembayaran }}</div>
                                </li>
                                <li class="timeline-item d-flex position-relative overflow-hidden">
                                    <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                        <span
                                            class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                        <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                    </div>
                                    <div class="timeline-desc text-dark mt-n1">Durasi Bermain :
                                        {{ $pengunjungMasuk->durasi_bermain }} Jam </div>
                                </li>
                                <li class="timeline-item d-flex position-relative overflow-hidden">
                                    <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                        <span
                                            class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                    </div>
                                    <div class="timeline-desc text-dark mt-n1">Tanggal :
                                        {{ formatTanggal($pengunjungMasuk->created_at, 'j M Y H:i:s') }}</div>
                                </li>
                            </ul>
                            <div class="text-center">
                                <span id="countdown" class="badge bg-primary rounded-3 fs-2"></span>
                                <div>
                                    <img src="{{ asset('/storage/pengunjung_masuk/' . $pengunjungMasuk->qr_code) }}"
                                        width="150px">
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
            $(document).ready(function() {
                const targetElement = '#countdown';
                const duration = '{{ $pengunjungMasuk->duration_difference }}';
                updateCountdown(targetElement, duration);
            });
        </script>
    @endpush
