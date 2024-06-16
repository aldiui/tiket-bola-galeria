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
                            <div class="row mb-2">
                                <div class="col-4 mb-2">
                                    <div>
                                        <img src="{{ asset('/storage/pengunjung_masuk/' . $pengunjungMasuk->qr_code) }}"
                                            class="img-fluid">
                                    </div>
                                </div>
                                <div class="col-8 mb-2 text-center">
                                    <h5 class="fw-semibold mb-3">E-Drop Ticket</h5>
                                    <div class="mb-2">
                                        <span class="badge bg-success"><i class="ti ti-logout me-1"></i>Pengunjung
                                            Masuk</span>
                                    </div>
                                    <div class="mb-2">
                                        @if ($pengunjungMasuk->start_tiket)
                                            @if (!$pengunjungMasuk->pengunjungMasuk)
                                                <span id="countdown" class="badge bg-primary rounded-3 fs-2 mb-2"></span>
                                            @else
                                                <span class="badge bg-danger"><i class="ti ti-clock me-1"></i> Sudah
                                                    Selesai</span>
                                            @endif
                                        @else
                                            <span class="badge bg-danger"><i class="ti ti-clock me-1"></i> Belum
                                                Mulai</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <tbody>
                                        <tr>
                                            <td class="text-dark fw-semibold">Nomor Tiket</td>
                                            <td class="text-dark">{{ $pengunjungMasuk->uuid }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark fw-semibold">Nama Anak</td>
                                            <td class="text-dark"> {{ $pengunjungMasuk->nama_anak }}
                                                (Panggilan : {{ $pengunjungMasuk->nama_panggilan }})</td>
                                        </tr>
                                        @if ($pengunjungMasuk->murid_id)
                                            <tr>
                                                <td class="text-dark fw-semibold">Status Murid</td>
                                                <td class="text-dark"> Murid</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="text-dark fw-semibold">Orang Tua</td>
                                            <td class="text-dark">{{ $pengunjungMasuk->nama_orang_tua }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark fw-semibold">Jenis Kelamin</td>
                                            <td class="text-dark">{{ $pengunjungMasuk->jenis_kelamin }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark fw-semibold">Nomor Telepon</td>
                                            <td class="text-dark">{{ $pengunjungMasuk->nomor_telepon }}</td>
                                        </tr>
                                        @if ($pengunjungMasuk->email)
                                            <tr>
                                                <td class="text-dark fw-semibold">Email</td>
                                                <td class="text-dark">{{ $pengunjungMasuk->email }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="text-dark fw-semibold">Metode Pembayaran</td>
                                            <td class="text-dark">
                                                {{ $pengunjungMasuk->pembayaran_id ? $pengunjungMasuk->pembayaran->nama : 'Cash' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark fw-semibold">Durasi Bermain</td>
                                            <td class="text-dark">
                                                {{ $pengunjungMasuk->pembayaran_id ? $pengunjungMasuk->pembayaran->nama_bank . ' - ' . $pengunjungMasuk->pembayaran->nama_akun . ' ( ' . $pengunjungMasuk->pembayaran->nomor_rekening . ' ) ' : 'Cash' }}
                                            </td>
                                        </tr>
                                        @if ($pengunjungMasuk->diskon)
                                            <tr>
                                                <td class="text-dark fw-semibold">Diskon ({{ $pengunjungMasuk->diskon }} %)
                                                </td>
                                                <td class="text-dark">{{ formatRupiah($pengunjungMasuk->nominal_diskon) }}
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($pengunjungMasuk->alasan_diskon)
                                            <tr>
                                                <td class="text-dark fw-semibold">Alasan Diskon</td>
                                                <td class="text-dark">{{ $pengunjungMasuk->alasan_diskon }}</td>
                                            </tr>
                                        @endif
                                        @if ($pengunjungMasuk->biaya_mengantar)
                                            <tr>
                                                <td class="text-dark fw-semibold">Biaya Mengantar</td>
                                                <td class="text-dark">{{ formatRupiah($pengunjungMasuk->biaya_mengantar) }}
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($pengunjungMasuk->biaya_kaos_kaki)
                                            <tr>
                                                <td class="text-dark fw-semibold">Biaya Kaos Kaki</td>
                                                <td class="text-dark">{{ formatRupiah($pengunjungMasuk->biaya_kaos_kaki) }}
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($pengunjungMasuk->denda)
                                            <tr>
                                                <td class="text-dark fw-semibold">Denda</td>
                                                <td class="text-dark">{{ formatRupiah($pengunjungMasuk->denda) }}</td>
                                            </tr>
                                        @endif

                                        <tr>
                                            <td class="text-dark fw-semibold">Pembayaran</td>
                                            @php
                                                $total = $pengunjungMasuk->durasi_extra
                                                    ? $pengunjungMasuk->tarif + $pengunjungMasuk->tarif_extra
                                                    : $pengunjungMasuk->tarif;
                                                $totalDenganDiskon =
                                                    $total -
                                                    $pengunjungMasuk->nominal_diskon +
                                                    $pengunjungMasuk->biaya_mengantar +
                                                    $pengunjungMasuk->biaya_kaos_kaki +
                                                    $pengunjungMasuk->denda;
                                            @endphp
                                            <td class="text-dark">
                                                {{ formatRupiah($total) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark fw-semibold">Total Pembayaran</td>
                                            <td class="text-dark">
                                                {{ formatRupiah($totalDenganDiskon) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark fw-semibold">Tanggal</td>
                                            <td class="text-dark">
                                                {{ formatTanggal($pengunjungMasuk->created_at, 'j M Y H:i:s') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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
