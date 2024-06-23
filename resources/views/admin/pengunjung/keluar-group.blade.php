@extends('layouts.app')

@section('title', 'Pengunjung Keluar Group')

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@section('main')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title fw-semibold">Tambah Data @yield('title')</h5>
        </div>
        <div class="card-body">
            <form id="saveData" autocomplete="off">
                <div class="form-group mb-3">
                    <label for="pengunjung_masuk_id" class="form-label">Pengunjung Masuk <span
                            class="text-danger">*</span></label>
                    <select class="form-control border" name="pengunjung_masuk_id" id="pengunjung_masuk_id"></select>
                    <small class="invalid-feedback" id="errorpengunjung_masuk_id"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="nama_group" class="form-label">Nama Group <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama_group" id="nama_group">
                    <small class="invalid-feedback" id="errornama_group"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="nama_panggilan" class="form-label">Nama Panggilan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama_panggilan" id="nama_panggilan">
                    <small class="invalid-feedback" id="errornama_panggilan"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="penanggung_jawab" class="form-label">Penanggung Jawab <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="penanggung_jawab" id="penanggung_jawab">
                    <small class="invalid-feedback" id="errorpenanggung_jawab"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="nomor_telepon" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nomor_telepon" id="nomor_telepon">
                    <small class="invalid-feedback" id="errornomor_telepon"></small>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            select2ToJsonPengunjungMasuk(true);

            $("#saveData").submit(function(e) {
                setButtonLoadingState("#saveData .btn.btn-primary", true);
                e.preventDefault();
                const url = "{{ route('pengunjungKeluarGroup') }}";
                const data = new FormData(this);

                const successCallback = function(response) {
                    $('#saveData .form-control').removeClass("is-invalid");
                    $('#saveData .invalid-feedback').html("").val();
                    setButtonLoadingState("#saveData .btn.btn-primary", false,
                        `<i class="ti ti-plus me-1"></i>Simpan`);
                    handleSuccess(response, null, null, "/riwayat-pengunjung-keluar");
                };

                const errorCallback = function(error) {
                    setButtonLoadingState("#saveData .btn.btn-primary", false,
                        `<i class="ti ti-plus me-1"></i>Simpan`);
                    handleValidationErrors(error, "saveData", ["pengunjung_masuk_id", "nama_group",
                        "nama_panggilan",
                        'penanggung_jawab',
                        'nomor_telepon',
                    ]);
                };

                ajaxCall(url, "POST", data, successCallback, errorCallback);
            });

            $("#pengunjung_masuk_id").on("change", function() {
                let cekPengunjungMasukId = $("#pengunjung_masuk_id").val();

                const successCallback = function(response) {
                    $("#nama_group").val(response.data.nama_anak);
                    $("#nama_panggilan").val(response.data.nama_panggilan);
                    $("#penanggung_jawab").val(response.data.nama_orang_tua);
                    $("#nomor_telepon").val(response.data.nomor_telepon);
                };

                const errorCallback = function(error) {
                    console.log(error);
                };
                ajaxCall(`/pengunjung-masuk/${cekPengunjungMasukId}`, "GET", null, successCallback,
                    errorCallback);
            });

        });
    </script>
@endpush
