@extends('layouts.app')

@section('title', 'Pengunjung Keluar')

@push('style')
    <link rel="stylesheet" href="{{ asset('libs/datatables/datatables.min.css') }}" />
@endpush

@section('main')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title fw-semibold">Riwayat @yield('title')</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-lg-6">
                    <div class="form-group mb-3">
                        <label for="tanggal_filter" class="form-label">Tanggal</label>
                        <input type="date" name="tanggal_filter" id="tanggal_filter" value="{{ date('Y-m-d') }}"
                            class="form-control">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div id="chart"></div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="pengunjung-keluar-table" class="table table-bordered table-striped" width="100%">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>QR Code</th>
                            <th>Nama Anak</th>
                            <th>Nama Panggilan</th>
                            <th>Nama Orang Tua</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
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
            datatableCall('pengunjung-keluar-table', '{{ route('riwayatPengunjungKeluar') }}', [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'qrcode',
                    name: 'qrcode'
                },
                {
                    data: 'nama_anak',
                    name: 'nama_anak'
                },
                {
                    data: 'nama_panggilan',
                    name: 'nama_panggilan'
                },
                {
                    data: 'nama_orang_tua',
                    name: 'nama_orang_tua'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'tiket',
                    name: 'tiket'
                },
            ]);

            renderData();

            $("#tanggal_filter").on("change", function() {
                $("#pengunjung-keluar-table").DataTable().ajax.reload();
                renderData();
            });
        });

        const renderData = () => {
            const successCallback = function(response) {
                renderPieChart(response.data);
            };

            const errorCallback = function(error) {
                console.error(error);
            };

            const url = `/riwayat-pengunjung-keluar?mode=pie&tanggal=${$("#tanggal_filter").val()}`;
            ajaxCall(url, "GET", null, successCallback, errorCallback);
        };
    </script>
@endpush
