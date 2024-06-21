@extends('layouts.app')

@section('title', 'Laporan Bulanan')

@push('style')
    <link rel="stylesheet" href="{{ asset('libs/datatables/datatables.min.css') }}" />
@endpush

@section('main')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-semibold">Rincian @yield('title')</h5>
            <div>
                <a id="cetak_laporan" class="btn btn-primary" target="_blank"><i class="ti ti-file me-1"></i>Cetak</a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-lg-4">
                    <div class="form-group mb-3">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ date('Y-m-d') }}"
                            class="form-control">
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group mb-3">
                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ date('Y-m-d') }}"
                            class="form-control">
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group mb-3">
                        <label for="pembayaran_id" class="form-label">Metode Pembayaran </label>
                        <select class="form-control" name="pembayaran_id" id="pembayaran_id">
                            <option value="Semua">Semua</option>
							        @foreach ($pembayaran as $row)
                                <option value="{{ $row->id }}">{{ $row->nama_akun }}
                                </option>
                            @endforeach
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
                            <th>Pembayaran</th>
                            <th>Diskon</th>
                            <th>Total</th>
                            <th>Tanggal dan Waktu</th>
                            <th>Ket</th>
                            <th>Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-center">Total</th>
                            <td id="pembayaran"></td>
                            <td id="diskon"></td>
                            <td id="total"></td>
                            <td></td>
                            <td></td>
                    </tfoot>
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
                    data: 'diskon',
                    name: 'diskon'
                },
                {
                    data: 'total',
                    name: 'total'
                },
                {
                    data: 'tanggal',
                    name: 'tanggal'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'admin',
                    name: 'admin'
                },
            ]);

            renderData();

            $("#tanggal_mulai, #tanggal_selesai, #pembayaran_id").on("change", function() {
                $("#laporan-keuangan-table").DataTable().ajax.reload();
                renderData();
            });
        });

        const renderData = () => {
            const successCallback = function(response) {
                renderSingleChart(response.data.data, response.data.labels);

                $("#pembayaran").text(response.data.pembayaran);
                $("#diskon").text(response.data.diskon);
                $("#total").text(response.data.total);
            };

            const errorCallback = function(error) {
                console.error(error);
            };

            const url =
                `/laporan-keuangan?mode=single&tanggal_mulai=${$("#tanggal_mulai").val()}&tanggal_selesai=${$("#tanggal_selesai").val()}&pembayaran_id=${$("#pembayaran_id").val()}`;
            const cetakLaporan =
                `/laporan-keuangan?mode=pdf&tanggal_mulai=${$("#tanggal_mulai").val()}&tanggal_selesai=${$("#tanggal_selesai").val()}&pembayaran_id=${$("#pembayaran_id").val()}`;
            $("#cetak_laporan").attr("href", cetakLaporan);

            ajaxCall(url, "GET", null, successCallback, errorCallback);
        };
    </script>
@endpush
