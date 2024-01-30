@extends('layouts.app')

@section('title', 'Dashboard')

@push('style')
<link rel="stylesheet" href="{{ asset('libs/datatables/datatables.min.css') }}" />
@endpush

@section('main')
<div class="row">
    <div class="col-lg-8">
        <div class="card w-100">
            <div class="card-body">
                <h5 class="card-title fw-semibold">Trafik Minggu Ini</h5>
                <div id="chart"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title mb-3 fw-semibold">Pengunjung Masuk</h5>
                        <h4 class="fw-semibold mb-3">{{ $countMounthPengungjungMasuk }} Anak</h4>
                        <div class="d-flex align-items-center mb-3">
                            <span class="me-1 rounded-circle bg-light-success round-20 d-flex align-items-center justify-content-center">
                                <i class="ti ti-arrow-up-left text-success"></i>
                            </span>
                            <p class="text-dark me-1 fs-3 mb-0">+ {{ $countDayPengunjungMasuk }} Anak</p>
                            <p class="fs-3 mb-0">Hari ini</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="me-4">
                                <span class="round-8 bg-primary rounded-circle me-2 d-inline-block"></span>
                                <span class="fs-2 active-anak">Laki-Laki</span>
                            </div>
                            <div>
                                <span class="round-8 bg-light-primary rounded-circle me-2 d-inline-block"></span>
                                <span class="fs-2">Perempuan</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-flex justify-content-center">
                            <div id="breakup"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row align-items-start">
                    <div class="col-8">
                        <h5 class="card-title mb-3 fw-semibold"> Pengunjung Keluar </h5>
                        <h4 class="fw-semibold mb-3">{{ $countMounthPengungjungKeluar }} Anak</h4>
                        <div class="d-flex align-items-center pb-1">
                            <span class="me-2 rounded-circle bg-light-danger round-20 d-flex align-items-center justify-content-center">
                                <i class="ti ti-arrow-down-right text-danger"></i>
                            </span>
                            <p class="text-dark me-1 fs-3 mb-0">{{ $countDayPengunjungKeluar }} Anak</p>
                            <p class="fs-3 mb-0">Hari ini</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-flex justify-content-end">
                            <div class="text-white bg-secondary rounded-circle p-6 d-flex align-items-center justify-content-center">
                                <i class="ti ti-user fs-6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="earning"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title fw-semibold">Pengunjung Masuk</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="pengunjung-masuk-table" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Nama Anak</th>
                                <th>Nama Panggilan</th>
                                <th>Sisa Waktu</th>
                                <th>Nama Orang Tua</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title fw-semibold">Pengunjung Keluar</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="pengunjung-keluar-table" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Nama Anak</th>
                                <th>Nama Panggilan</th>
                                <th>Nama Orang Tua</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('libs/apexcharts/dist/apexcharts.min.js') }}"></script>
<script src="{{ asset('js/dashboard.js') }}"></script>
<script src="{{ asset('libs/datatables/datatables.min.js') }}"></script>

<script>
$(document).ready(function() {
    datatableCall('pengunjung-masuk-table', '{{ route('riwayatPengunjungMasuk') }}', [
        { data: 'DT_RowIndex', name: 'DT_RowIndex' },
        { data: 'nama_anak', name: 'nama_anak' },
        { data: 'nama_panggilan', name: 'nama_panggilan' },
        { data: 'durasi', name: 'durasi' },
        { data: 'nama_orang_tua', name: 'nama_orang_tua' },
    ]);
    
    datatableCall('pengunjung-keluar-table', '{{ route('riwayatPengunjungKeluar') }}', [
        { data: 'DT_RowIndex', name: 'DT_RowIndex' },
        { data: 'nama_anak', name: 'nama_anak' },
        { data: 'nama_panggilan', name: 'nama_panggilan' },
        { data: 'nama_orang_tua', name: 'nama_orang_tua' },
    ]);
});
</script>
@endpush
