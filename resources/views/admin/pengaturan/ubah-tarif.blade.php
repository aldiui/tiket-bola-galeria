@extends('layouts.app')

@section('title', 'Ubah Tarif')

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
                    <label for="tarif" class="form-label">Tarif Per Jam <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="tarif" id="tarif"
                        placeholder="Masukkan jumlah tarif per jam" value="{{ $pengaturan->tarif ?? '' }}">
                    <small class="invalid-feedback" id="errortarif"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="denda" class="form-label">Denda <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="denda" id="denda"
                        placeholder="Masukkan nominal denda" value="{{ $pengaturan->denda ?? '' }}">
                    <small class="invalid-feedback" id="errordenda"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="tarif_mengantar" class="form-label">Tarif Mengantar <span
                            class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="tarif_mengantar" id="tarif_mengantar"
                        placeholder="Masukkan nominal Tarif Mengantar" value="{{ $pengaturan->tarif_mengantar ?? '' }}">
                    <small class="invalid-feedback" id="errortarif_mengantar"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="tarif_kaos_kaki" class="form-label">Tarif Kaos Kaki <span
                            class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="tarif_kaos_kaki" id="tarif_kaos_kaki"
                        placeholder="Masukkan nominal Tarif Kaos Kaki" value="{{ $pengaturan->tarif_kaos_kaki ?? '' }}">
                    <small class="invalid-feedback" id="errortarif_kaos_kaki"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="tarif_group_10" class="form-label">Tarif Group 10 <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="tarif_group_10" id="tarif_group_10"
                        placeholder="Masukkan nominal Tarif Kaos Kaki" value="{{ $pengaturan->tarif_group_10 ?? '' }}">
                    <small class="invalid-feedback" id="errortarif_group_10"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="tarif_group_25" class="form-label">Tarif Group 25 <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="tarif_group_25" id="tarif_group_25"
                        placeholder="Masukkan nominal Tarif Kaos Kaki" value="{{ $pengaturan->tarif_group_25 ?? '' }}">
                    <small class="invalid-feedback" id="errortarif_group_25"></small>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="ti ti-pencil me-1"></i>Ubah Tarif</button>
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
                    confirmButtonText: "Ya, Ubah Tarif!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        setButtonLoadingState("#updateData .btn.btn-primary", true, 'Ubah Tarif');
                        const url = `{{ route('ubahTarif') }}`;
                        const data = new FormData(this);

                        const successCallback = function(response) {
                            $('#updateData .form-control').removeClass("is-invalid");
                            $('#updateData .invalid-feedback').html("");
                            setButtonLoadingState("#updateData .btn.btn-primary", false,
                                `<i class="ti ti-pencil me-1"></i>Ubah Tarif`);
                            handleSuccess(response, null, null, "no");
                        };

                        const errorCallback = function(error) {
                            setButtonLoadingState("#updateData .btn.btn-primary", false,
                                `<i class="ti ti-pencil me-1"></i>Ubah Tarif`);
                            handleValidationErrors(error, "updateData", ['tarif', 'denda',
                                'tarif_mengantar', 'tarif_kaos_kaki', 'tarif_group_10',
                                'tarif_group_25'
                            ]);
                        };

                        ajaxCall(url, "POST", data, successCallback, errorCallback);
                    }
                });
            });
        });
    </script>
@endpush
