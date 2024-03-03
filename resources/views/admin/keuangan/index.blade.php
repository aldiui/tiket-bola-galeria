@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@push('style')
    <link rel="stylesheet" href="{{ asset('libs/datatables/datatables.min.css') }}" />
@endpush

@section('main')
    @php
        $bulans = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ];
    @endphp
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-semibold">Rincian @yield('title')</h5>
            <div>
                <a id="cetak_laporan" class="btn btn-primary" target="_blank"><i class="ti ti-file me-1"></i>Cetak</a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-lg-6">
                    <div class="form-group mb-3">
                        <label for="bulan_filter" class="form-label">Bulan</label>
                        <select name="bulan_filter" id="bulan_filter" class="form-control">
                            @foreach ($bulans as $key => $value)
                                <option value="{{ $key + 1 }}" {{ $key + 1 == date('m') ? 'selected' : '' }}>
                                    {{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group mb-3">
                        <label for="tahun_filter" class="form-label">Tahun</label>
                        <select name="tahun_filter" id="tahun_filter" class="form-control">
                            @for ($i = now()->year; $i >= now()->year - 4; $i--)
                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div id="chart"></div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="laporan-keuangan-table" class="table table-bordered table-striped" width="100%">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>Nama Anak</th>
                            <th>Durasi</th>
                            <th>Orang Tua</th>
                            <th>Metode Pembayaran</th>
                            <th>Jumlah Pembayaran</th>
                            <th>Tanggal dan Waktu</th>
                            <th>Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('libs/apexcharts/dist/apexcharts.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            datatableCall('laporan-keuangan-table', '{{ route('laporanKeuangan') }}', [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'nama_anak',
                    name: 'nama_anak'
                },
                {
                    data: 'durasi',
                    name: 'durasi'
                },
                {
                    data: 'nama_orang_tua',
                    name: 'nama_orang_tua'
                },
                {
                    data: 'metode_pembayaran',
                    name: 'metode_pembayaran'
                },
                {
                    data: 'pembayaran',
                    name: 'pembayaran'
                },
                {
                    data: 'tanggal',
                    name: 'tanggal'
                },
                {
                    data: 'admin',
                    name: 'admin'
                },
            ]);

            renderData();

            $("#bulan_filter, #tahun_filter").on("change", function() {
                $("#laporan-keuangan-table").DataTable().ajax.reload();
                renderData();
            });
        });

        const renderData = () => {
            const successCallback = function(response) {
                renderSingleChart(response.data.data, response.data.labels);
            };

            const errorCallback = function(error) {
                console.error(error);
            };

            const url =
                `/laporan-keuangan?mode=single&bulan=${$("#bulan_filter").val()}&tahun=${$("#tahun_filter").val()}`;
            const cetakLaporan =
                `/laporan-keuangan?mode=pdf&bulan=${$("#bulan_filter").val()}&tahun=${$("#tahun_filter").val()}`;
            $("#cetak_laporan").attr("href", cetakLaporan);

            ajaxCall(url, "GET", null, successCallback, errorCallback);
        };
    </script>
@endpush
