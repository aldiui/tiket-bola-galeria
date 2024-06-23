@extends('layouts.app')

@section('title', 'Transaksi Membership')

@push('style')
    <link rel="stylesheet" href="{{ asset('libs/datatables/datatables.min.css') }}" />
@endpush

@section('main')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-semibold">@yield('title')</h5>
            <button type="button" class="btn btn-primary" onclick="getModal('createModal')">
                <i class="ti ti-plus me-1"></i>Tambah
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="transaksi-membership-table" class="table table-bordered table-striped" width="100%">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>Member</th>
                            <th>Paket</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Akhir</th>
                            <th>Nominal</th>
                            <th>Tanggal dan Waktu</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('admin.transaksi-membership.modal')
@endsection

@push('scripts')
    <script src="{{ asset('libs/datatables/datatables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            datatableCall('transaksi-membership-table', '{{ route('transaksiMembership.index') }}', [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'membership',
                    name: 'membership'
                },
                {
                    data: 'paket_membership',
                    name: 'paket_membership'
                },
                {
                    data: 'pembayaran',
                    name: 'pembayaran'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'start_membership',
                    name: 'start_membership'
                },
                {
                    data: 'end_membership',
                    name: 'end_membership'
                },
                {
                    data: 'nominal',
                    name: 'nominal'
                },
                {
                    data: 'tanggal',
                    name: 'tanggal'
                },
                {
                    data: 'aksi',
                    name: 'aksi'
                },
            ]);

            $("#saveData").submit(function(e) {
                setButtonLoadingState("#saveData .btn.btn-primary", true);
                e.preventDefault();

                const kode = $("#saveData #id").val();
                let url = "{{ route('transaksiMembership.store') }}";
                const data = new FormData(this);

                const successCallback = function(response) {
                    setButtonLoadingState("#saveData .btn.btn-primary", false,
                        `<i class="ti ti-plus me-1"></i>Simpan`);
                    handleSuccess(response, "transaksi-membership-table", "createModal");
                };

                const errorCallback = function(error) {
                    setButtonLoadingState("#saveData .btn.btn-primary", false,
                        `<i class="ti ti-plus me-1"></i>Simpan`);
                    handleValidationErrors(error, "saveData", ["membership_id", "paket_membership_id",
                        "pembayaran_id", "nominal"
                    ]);
                };

                ajaxCall(url, "POST", data, successCallback, errorCallback);
            });

            $("#paket_membership_id").on("change", function() {
                const membership_id = $("#paket_membership_id").val();
                const url = '/paket-membership/' + membership_id;

                const successCallback = function(response) {
                    $("#nominal").val(response.data.tarif);
                };

                const errorCallback = function(error) {
                    console.error(error);
                };

                ajaxCall(url, "GET", null, successCallback, errorCallback);
            })
        });
    </script>
@endpush
