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
                                        <img src="{{ asset('/storage/pengunjung_keluar/' . $pengunjungKeluar->qr_code) }}"
                                            class="img-fluid">
                                    </div>
                                </div>
                                <div class="col-8 mb-2 text-center">
                                    <h5 class="fw-semibold mb-3">E-Drop Ticket</h5>
                                    <div class="mb-2">
                                        <span class="badge bg-danger"><i class="ti ti-login me-1"></i>Pengunjung
                                            Keluar</span>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <tbody>
                                        <tr>
                                            <td class="text-dark fw-semibold">Nomor Tiket</td>
                                            <td class="text-dark">{{ $pengunjungKeluar->uuid }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark fw-semibold">Nama Anak</td>
                                            <td class="text-dark"> {{ $pengunjungKeluar->nama_anak }}
                                                (Panggilan : {{ $pengunjungKeluar->nama_panggilan }})</td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark fw-semibold">Orang Tua</td>
                                            <td class="text-dark">{{ $pengunjungKeluar->nama_orang_tua }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark fw-semibold">Jenis Kelamin</td>
                                            <td class="text-dark">{{ $pengunjungKeluar->jenis_kelamin }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-dark fw-semibold">Nomor Telepon</td>
                                            <td class="text-dark">{{ $pengunjungKeluar->pengunjungMasuk->nomor_telepon }}
                                            </td>
                                        </tr>
                                        @if ($pengunjungKeluar->pengunjungMasuk->email)
                                            <tr>
                                                <td class="text-dark fw-semibold">Email</td>
                                                <td class="text-dark">{{ $pengunjungKeluar->pengunjungMasuk->email }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="text-dark fw-semibold">Keterangan</td>
                                            <td class="text-dark">{{ $pengunjungKeluar->pengunjungMasuk->type }}
                                            </td>
                                        </tr>
                                        @if ($pengunjungKeluar->pengunjungMasuk->type == 'Murid' || $pengunjungKeluar->pengunjungMasuk->type == 'Perorangan')
                                            <tr>
                                                <td class="text-dark fw-semibold">Metode Pembayaran</td>
                                                <td class="text-dark">
                                                    {{ $pengunjungKeluar->pengunjungMasuk->pembayaran_id ? $pengunjungKeluar->pengunjungMasuk->pembayaran->nama_bank : 'Cash' }}j
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-dark fw-semibold">Durasi Bermain</td>
                                                <td class="text-dark">
                                                    {{ $pengunjungKeluar->pengunjungMasuk->durasi_extra ? $pengunjungKeluar->pengunjungMasuk->durasi_bermain + $pengunjungKeluar->pengunjungMasuk->durasi_extra : $pengunjungKeluar->pengunjungMasuk->durasi_bermain }}
                                                    Jam</td>
                                            </tr>
                                            @if ($pengunjungKeluar->pengunjungMasuk->diskon)
                                                <tr>
                                                    <td class="text-dark fw-semibold">Diskon
                                                        ({{ $pengunjungKeluar->pengunjungMasuk->diskon }} %)
                                                    </td>
                                                    <td class="text-dark">
                                                        {{ formatRupiah($pengunjungKeluar->pengunjungMasuk->nominal_diskon) }}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if ($pengunjungKeluar->pengunjungMasuk->alasan_diskon)
                                                <tr>
                                                    <td class="text-dark fw-semibold">Alasan Diskon</td>
                                                    <td class="text-dark">
                                                        {{ $pengunjungKeluar->pengunjungMasuk->alasan_diskon }}</td>
                                                </tr>
                                            @endif
                                            @if ($pengunjungKeluar->pengunjungMasuk->biaya_mengantar)
                                                <tr>
                                                    <td class="text-dark fw-semibold">Biaya Mengantar</td>
                                                    <td class="text-dark">
                                                        {{ formatRupiah($pengunjungKeluar->pengunjungMasuk->biaya_mengantar) }}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if ($pengunjungKeluar->pengunjungMasuk->biaya_kaos_kaki)
                                                <tr>
                                                    <td class="text-dark fw-semibold">Biaya Kaos Kaki</td>
                                                    <td class="text-dark">
                                                        {{ formatRupiah($pengunjungKeluar->pengunjungMasuk->biaya_kaos_kaki) }}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if ($pengunjungKeluar->pengunjungMasuk->denda)
                                                <tr>
                                                    <td class="text-dark fw-semibold">Denda</td>
                                                    <td class="text-dark">
                                                        {{ formatRupiah($pengunjungKeluar->pengunjungMasuk->denda) }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td class="text-dark fw-semibold">Pembayaran</td>
                                                @php
                                                    $total = $pengunjungKeluar->pengunjungMasuk->durasi_extra
                                                        ? $pengunjungKeluar->pengunjungMasuk->tarif +
                                                            $pengunjungKeluar->pengunjungMasuk->tarif_extra
                                                        : $pengunjungKeluar->pengunjungMasuk->tarif;

                                                    $totalDenganDiskon =
                                                        $total -
                                                        $pengunjungKeluar->pengunjungMasuk->nominal_diskon +
                                                        $pengunjungKeluar->pengunjungMasuk->biaya_mengantar +
                                                        $pengunjungKeluar->pengunjungMasuk->biaya_kaos_kaki +
                                                        $pengunjungKeluar->pengunjungMasuk->denda;
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
                                        @endif
                                        <tr>
                                            <td class="text-dark fw-semibold">Tanggal</td>
                                            <td class="text-dark">
                                                {{ formatTanggal($pengunjungKeluar->created_at, 'j M Y H:i:s') }}
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
@endpush
