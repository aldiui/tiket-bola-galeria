@extends('layouts.app')

@section('title', 'Dashboard')

@push('style')
<link rel="stylesheet" href="{{ asset('libs/datatables/datatables.min.css') }}" />
@endpush

@section('main')
<div class="row">
    <div class="col-12">
        <div class="card w-100">
            <div class="card-header">
                <h5 class="card-title fw-semibold">Trafik Pengunjung Hari Ini</h5>
            </div>
            <div class="card-body">
                <div id="chart"></div>
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
