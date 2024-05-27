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
                            <h5 class="fw-semibold text-center mb-3">E-Drop Ticket</h5>
                            <div class="mb-3 text-center">
                                <span class="badge bg-danger"><i class="ti ti-login me-1"></i>Pengunjung Keluar</span>
                            </div>
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
                                    <div class="timeline-desc text-dark mt-n1"> Nomor Tiket :
                                        {{ $pengunjungKeluar->uuid }}
                                </li>
                                <li class="timeline-item d-flex position-relative overflow-hidden">
                                    <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                        <span
                                            class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                        <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                    </div>
                                    <div class="timeline-desc text-dark mt-n1"> Nama Anak :
                                        {{ $pengunjungKeluar->nama_anak }}
                                        (Panggilan : {{ $pengunjungKeluar->nama_panggilan }})
                                </li>
                                <li class="timeline-item d-flex position-relative overflow-hidden">
                                    <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                        <span
                                            class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                        <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                    </div>
                                    <div class="timeline-desc text-dark mt-n1">Orang Tua :
                                        {{ $pengunjungKeluar->nama_orang_tua }}
                                    </div>
                                </li>
                                <li class="timeline-item d-flex position-relative overflow-hidden">
                                    <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                        <span
                                            class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                        <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                    </div>
                                    <div class="timeline-desc text-dark mt-n1">{{ $pengunjungKeluar->jenis_kelamin }}
                                    </div>
                                </li>
                                <li class="timeline-item d-flex position-relative overflow-hidden">
                                    <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                        <span
                                            class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                        <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                    </div>
                                    <div class="timeline-desc text-dark mt-n1">Nomor Telepon :
                                        {{ $pengunjungKeluar->nomor_telepon }}
                                    </div>
                                </li>
                                @if ($pengunjungKeluar->pengunjungMasuk->email)
                                    <li class="timeline-item d-flex position-relative overflow-hidden">
                                        <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                            <span
                                                class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                            <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                        </div>
                                        <div class="timeline-desc text-dark mt-n1">Email :
                                            {{ $pengunjungKeluar->pengunjungMasuk->email }}
                                        </div>
                                    </li>
                                @endif
                                <li class="timeline-item d-flex position-relative overflow-hidden">
                                    <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                        <span
                                            class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                        <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                    </div>
                                    <div class="timeline-desc text-dark mt-n1">Durasi Bermain :
                                        {{ $pengunjungKeluar->durasi_bermain }} Jam </div>
                                </li>
                                @if ($pengunjungKeluar->pengunjungMasuk->diskon)
                                    <li class="timeline-item d-flex position-relative overflow-hidden">
                                        <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                            <span
                                                class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                            <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                        </div>
                                        <div class="timeline-desc text-dark mt-n1">Diskon :
                                            {{ formatRupiah($pengunjungKeluar->pengunjungMasuk->diskon) }}
                                        </div>
                                    </li>
                                    <li class="timeline-item d-flex position-relative overflow-hidden">
                                        <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                            <span
                                                class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                            <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                        </div>
                                        <div class="timeline-desc text-dark mt-n1">Alasan Diskon :
                                            {{ $pengunjungKeluar->pengunjungMasuk->alasan_diskon ?? '-' }}
                                        </div>
                                    </li>
                                @endif
                                <li class="timeline-item d-flex position-relative overflow-hidden">
                                    <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                        <span
                                            class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                        <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                    </div>
                                    @php
                                        $total = $pengunjungKeluar->pengunjungMasuk->durasi_extra
                                            ? $pengunjungKeluar->pengunjungMasuk->tarif +
                                                $pengunjungKeluar->pengunjungMasuk->tarif_extra
                                            : $pengunjungKeluar->pengunjungMasuk->tarif;
                                        $totalDenganDiskon = $total - $pengunjungKeluar->pengunjungMasuk->diskon ?? 0;
                                    @endphp
                                    <div class="timeline-desc text-dark mt-n1">Pembayaran :
                                        {{ formatRupiah($total) }}
                                    </div>
                                </li>
                                <li class="timeline-item d-flex position-relative overflow-hidden">
                                    <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                        <span
                                            class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                        <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                    </div>

                                    <div class="timeline-desc text-dark mt-n1">Total Pembayaran :
                                        {{ formatRupiah($totalDenganDiskon) }}
                                    </div>
                                </li>
                                <li class="timeline-item d-flex position-relative overflow-hidden">
                                    <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                        <span
                                            class="timeline-badge border-2 border border-primary flex-shrink-0 my-8"></span>
                                    </div>
                                    <div class="timeline-desc text-dark mt-n1">Tanggal :
                                        {{ formatTanggal($pengunjungKeluar->created_at, 'j M Y H:i:s') }}</div>
                                </li>

                            </ul>
                            <div class="text-center">
                                <div>
                                    <img src="{{ asset('/storage/pengunjung_keluar/' . $pengunjungKeluar->qr_code) }}"
                                        width="150px">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
