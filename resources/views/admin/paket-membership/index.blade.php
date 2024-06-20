@extends('layouts.app')

@section('title', 'Paket Membership')

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
                <table id="paket-membership-table" class="table table-bordered table-striped" width="100%">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Durasi Hari</th>
                            <th>Tarif</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('admin.paket-membership.modal')
@endsection

@push('scripts')
    <script src="{{ asset('libs/datatables/datatables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            datatableCall('paket-membership-table', '{{ route('paketMembership.index') }}', [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'kode',
                    name: 'kode'
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
            {
                    data: 'durasi_hari',
                    name: 'durasi_hari'
                },
                {
                    data: 'tarif',
                    name: 'tarif'
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
                let url = "{{ route('paketMembership.store') }}";
                const data = new FormData(this);

                if (kode !== "") {
                    data.append("_method", "PUT");
                    url = `/paket-membership/${kode}`;
                }

                const successCallback = function(response) {
                    setButtonLoadingState("#saveData .btn.btn-primary", false,
                        `<i class="ti ti-plus me-1"></i>Simpan`);
                    handleSuccess(response, "paket-membership-table", "createModal");
                };

                const errorCallback = function(error) {
                    setButtonLoadingState("#saveData .btn.btn-primary", false,
                        `<i class="ti ti-plus me-1"></i>Simpan`);
                    handleValidationErrors(error, "saveData", ["kode", "nama", "durasi_hari", "tarif"]);
                };

                ajaxCall(url, "POST", data, successCallback, errorCallback);
            });
        });
    </script>
@endpush
