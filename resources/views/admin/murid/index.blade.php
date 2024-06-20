@extends('layouts.app')

@section('title', 'Murid Champs')

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
                <table id="murid-table" class="table table-bordered table-striped" width="100%">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>Nomor Murid</th>
                            <th>Nama</th>
                            <th>Umur</th>
                            <th>Kelas</th>
                            <th>Orang Tua</th>
                            <th>Nomor Telepon</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('admin.murid.modal')
@endsection

@push('scripts')
    <script src="{{ asset('libs/datatables/datatables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            datatableCall('murid-table', '{{ route('murid.index') }}', [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'nomor_murid',
                    name: 'nomor_murid'
                },
                {
                    data: 'nama_anak',
                    name: 'nama_anak'
                },
                {
                    data: 'umur',
                    name: 'umur'
                },
                {
                    data: 'kelas',
                    name: 'kelas'
                },
                {
                    data: 'nama_orang_tua',
                    name: 'nama_orang_tua'
                },
                {
                    data: 'nomor_telepon',
                    name: 'nomor_telepon'
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
                let url = "{{ route('murid.store') }}";
                const data = new FormData(this);

                if (kode !== "") {
                    data.append("_method", "PUT");
                    url = `/murid/${kode}`;
                }

                const successCallback = function(response) {
                    setButtonLoadingState("#saveData .btn.btn-primary", false,
                        `<i class="ti ti-plus me-1"></i>Simpan`);
                    handleSuccess(response, "murid-table", "createModal");
                };

                const errorCallback = function(error) {
                    setButtonLoadingState("#saveData .btn.btn-primary", false,
                        `<i class="ti ti-plus me-1"></i>Simpan`);
                    handleValidationErrors(error, "saveData", ['nomor_murid', 'nama_anak', 'umur',
                        'kelas',
                        'nama_orang_tua', 'nomor_telepon'
                    ]);
                };

                ajaxCall(url, "POST", data, successCallback, errorCallback);
            });
        });
    </script>
@endpush
