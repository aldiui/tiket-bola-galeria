@extends('layouts.app')

@section('title', 'Pengunjung Keluar')

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
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            select2ToJsonPengunjungMasuk();

            $("#saveData").submit(function(e) {
                setButtonLoadingState("#saveData .btn.btn-primary", true);
                e.preventDefault();
                const url = "{{ route('pengunjungKeluar') }}";
                const data = new FormData(this);

                const successCallback = function(response) {
                    $('#saveData .form-control').removeClass("is-invalid");
                    $('#saveData .invalid-feedback').html("").val();
                    setButtonLoadingState("#saveData .btn.btn-primary", false);
                    handleSuccess(response, null, null, "/riwayat-pengunjung-keluar");
                };

                const errorCallback = function(error) {
                    setButtonLoadingState("#saveData .btn.btn-primary", false);
                    handleValidationErrors(error, "saveData", ["pengunjung_masuk_id"]);
                };

                ajaxCall(url, "POST", data, successCallback, errorCallback);
            });
        });
    </script>
@endpush
