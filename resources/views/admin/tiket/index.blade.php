@extends('layouts.auth')

@section('title', 'E-Tiket')

@push('style')
<link rel="stylesheet" href="{{ asset('libs/datatables/datatables.min.css') }}" />
@endpush

@section('main')
<div class="radial-gradient min-vh-100">
    <div class="container">
        <div class="row justify-content-center py-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title fw-semibold text-center">Riwayat Pengunjung Masuk {{ formatTanggal() }}</h5>
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
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('libs/datatables/datatables.min.js') }}"></script>
<script>
    $(document).ready(function () {
        datatableCall('pengunjung-masuk-table', '{{ route('eTiket.index') }}', [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'nama_anak', name: 'nama_anak' },
            { data: 'nama_panggilan', name: 'nama_panggilan' },
            { data: 'durasi', name: 'durasi' },
            { data: 'nama_orang_tua', name: 'nama_orang_tua' },
            { data: 'tiket', name: 'tiket' },
        ]);

        setInterval(function() {
            $("#pengunjung-masuk-table").DataTable().ajax.reload();
        }, 60000);
    });
</script>
@endpush

