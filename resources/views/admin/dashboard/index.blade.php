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
                        <h4 class="fw-semibold mb-3">{{ $countDayPengunjungMasuk }} Anak</h4>
                        <div class="d-flex align-items-center">
                            <span class="me-2 rounded-circle bg-light-success round-20 d-flex align-items-center justify-content-center">
                                <i class="ti ti-arrow-up-left text-success"></i>
                            </span>
                            <p class="fs-3 mb-0">{{ formatTanggal() }}</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-flex justify-content-end">
                            <div class="text-white bg-success rounded-circle p-6 d-flex align-items-center justify-content-center">
                                <i class="ti ti-user-check fs-6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h5 class="card-title mb-3 fw-semibold"> Pengunjung Keluar </h5>
                        <h4 class="fw-semibold mb-3">{{ $countDayPengunjungKeluar }} Anak</h4>
                        <div class="d-flex align-items-center">
                            <span class="me-2 rounded-circle bg-light-danger round-20 d-flex align-items-center justify-content-center">
                                <i class="ti ti-arrow-down-right text-danger"></i>
                            </span>
                            <p class="fs-3 mb-0">{{ formatTanggal() }}</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-flex justify-content-end">
                            <div class="text-white bg-danger rounded-circle p-6 d-flex align-items-center justify-content-center">
                                <i class="ti ti-user-minus fs-6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                <th>Tiket</th>
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
<script src="{{ asset('libs/datatables/datatables.min.js') }}"></script>

<script>
$(document).ready(function() {
    datatableCall('pengunjung-masuk-table', '{{ route('riwayatPengunjungMasuk') }}', [
        { data: 'DT_RowIndex', name: 'DT_RowIndex' },
        { data: 'nama_anak', name: 'nama_anak' },
        { data: 'nama_panggilan', name: 'nama_panggilan' },
        { data: 'durasi', name: 'durasi' },
        { data: 'nama_orang_tua', name: 'nama_orang_tua' },
        { data: 'tiket', name: 'tiket' },
    ]);
    
    datatableCall('pengunjung-keluar-table', '{{ route('riwayatPengunjungKeluar') }}', [
        { data: 'DT_RowIndex', name: 'DT_RowIndex' },
        { data: 'nama_anak', name: 'nama_anak' },
        { data: 'nama_panggilan', name: 'nama_panggilan' },
        { data: 'nama_orang_tua', name: 'nama_orang_tua' },
    ]);

    renderMultipleChart({!! json_encode($dataMasuk) !!}, {!! json_encode($dataKeluar) !!}, {!! json_encode($labels) !!});
});
</script>
@endpush
