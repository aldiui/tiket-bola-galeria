@extends('layouts.app')

@section('title', 'Toleransi Waktu')

@push('style')
@endpush

@section('main')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title fw-semibold">@yield('title')</h5>
        </div>
        <div class="card-body">
            <form id="updateData" autocomplete="off">
                <div class="form-group mb-3">
                    <label for="toleransi_waktu" class="form-label">Toleransi Waktu <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="toleransi_waktu" id="toleransi_waktu"
                        placeholder="Masukkan jumlah toleransi_waktu" value="{{ $pengaturan->toleransi_waktu ?? '' }}">
                    <small class="invalid-feedback" id="errortoleransi_waktu"></small>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="ti ti-pencil me-1"></i>Ubah Toleransi
                        Waktu</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#updateData").submit(function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "Konfirmasi",
                    text: "Jika Setuju Merubah. Apakah Anda yakin?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, Ubah Toleransi Waktu!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        setButtonLoadingState("#updateData .btn.btn-primary", true,
                            'Ubah Toleransi Waktu');
                        const url = `{{ route('toleransiWaktu') }}`;
                        const data = new FormData(this);

                        const successCallback = function(response) {
                            $('#updateData .form-control').removeClass("is-invalid");
                            $('#updateData .invalid-feedback').html("");
                            setButtonLoadingState("#updateData .btn.btn-primary", false,
                                `<i class="ti ti-pencil me-1"></i>Ubah Toleransi Waktu`);
                            handleSuccess(response, null, null, "no");
                        };

                        const errorCallback = function(error) {
                            setButtonLoadingState("#updateData .btn.btn-primary", false,
                                `<i class="ti ti-pencil me-1"></i>Ubah Toleransi Waktu`);
                            handleValidationErrors(error, "updateData", ["toleransi_waktu"]);
                        };

                        ajaxCall(url, "POST", data, successCallback, errorCallback);
                    }
                });
            });
        });
    </script>
@endpush
