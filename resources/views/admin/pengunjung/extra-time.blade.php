@extends('layouts.app')

@section('title', 'Pengunjung Masuk')

@push('style')
@endpush

@section('main')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title fw-semibold">Extra Time Data @yield('title')</h5>
        </div>
        <div class="card-body">
            <form id="saveData" autocomplete="off">
                <input type="hidden" name="type" id="type" value="{{ $pengunjung->type }}">
                <div class="form-group mb-3">
                    <label for="nama_anak" class="form-label">Nama Anak <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama_anak" id="nama_anak"
                        value="{{ $pengunjung->nama_anak }}" readonly>
                    <small class="invalid-feedback" id="errornama_anak"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="durasi_extra" class="form-label">Durasi Extra <span class="text-danger">*</span></label>
                    <select class="form-control" name="durasi_extra" id="durasi_extra">
                        <option value="">-- Pilih Durasi Bermain --</option>
                        <option value="1">1 Jam</option>
                        <option value="2">2 Jam</option>
                        <option value="3">3 Jam</option>
                        <option value="4">4 Jam</option>
                        <option value="5">5 Jam</option>
                    </select>
                    <small class="invalid-feedback" id="errordurasi_extra"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="nama_anak" class="form-label">Nama Anak <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama_anak" id="nama_anak"
                        value="{{ $pengunjung->nama_anak }}" readonly>
                    <small class="invalid-feedback" id="errornama_anak"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="tarif_extra" class="form-label">Tarif Extra <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="tarif_extra" id="tarif_extra" readonly>
                    <small class="invalid-feedback" id="errortarif_extra"></small>
                    <small class="d-block pt-2">Tarif Extra otomatis berdasarkan waktu yang di pilih</small>
                </div>
                @if ($pengunjung->jumlah_anak > 0)
                    <div class="form-group mb-3">
                        <label for="jumlah_anak" class="form-label">Jumlah Anak <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="jumlah_anak" id="jumlah_anak"
                            value="{{ $pengunjung->jumlah_anak }}" readonly>
                        <small class="invalid-feedback" id="errorjumlah_anak"></small>
                    </div>
                @endif
                @if ($pengunjung->biaya_mengantar > 0)
                    @php
                        $pendamping =
                            $pengunjung->biaya_mengantar / $pengaturan->tarif_mengantar / $pengunjung->durasi_bermain;
                    @endphp
                    <div class="form-group mb-3">
                        <label for="pendamping" class="form-label">Pendamping <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="pendamping" id="pendamping"
                            value="{{ $pendamping }}" readonly>
                        <small class="invalid-feedback" id="errorpendamping"></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="tarif_mengantar_extra" class="form-label">Tarif Mengantar Extra <span
                                class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="tarif_mengantar_extra" id="tarif_mengantar_extra"
                            readonly>
                        <small class="invalid-feedback" id="errortarif_mengantar_extra"></small>
                        <small class="d-block pt-2">Tarif Mengantar Extra otomatis berdasarkan waktu yang di pilih</small>
                    </div>
                @endif
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Extra</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#durasi_extra").on("change", function() {
                const type = $("#type").val();
                if (type == "Perorangan") {
                    const durasi_extra = $("#durasi_extra").val();
                    const calculateTarif_extra = durasi_extra * {{ $pengaturan->tarif ?? 0 }};
                    $("#tarif_extra").val(calculateTarif_extra);
                } else if (type == "Group") {
                    const durasi_extra = $("#durasi_extra").val();
                    @if ($pengunjung->jumlah_anak >= 10)
                        const calculateTarif_extra = durasi_extra * {{ $pengaturan->tarif_group_10 ?? 0 }} *
                            {{ $pengunjung->jumlah_anak ?? 0 }};
                    @elseif ($pengunjung->jumlah_anak >= 25)
                        const calculateTarif_extra = durasi_extra * {{ $pengaturan->tarif_group_25 ?? 0 }} *
                            {{ $pengunjung->jumlah_anak ?? 0 }};
                    @endif
                    $("#tarif_extra").val(calculateTarif_extra);
                } else {
                    $("#tarif_extra").val(0);

                }
                @if ($pengunjung->biaya_mengantar > 0)
                    const durasi_extra = $("#durasi_extra").val();
                    const pendamping = $("#pendamping").val();
                    const calculateTarif_mengantar_extra = pendamping *
                        {{ $pengaturan->tarif_mengantar ?? 0 }} * durasi_extra;
                    $("#tarif_mengantar_extra").val(calculateTarif_mengantar_extra);
                @endif
            });

            $("#saveData").submit(function(e) {
                setButtonLoadingState("#saveData .btn.btn-primary", true);
                e.preventDefault();
                const url = "{{ route('extraTimeUpdate', $pengunjung->uuid) }}";
                const data = new FormData(this);

                const successCallback = function(response) {
                    $('#saveData .form-control').removeClass("is-invalid");
                    $('#saveData .invalid-feedback').html("").val();
                    setButtonLoadingState("#saveData .btn.btn-primary", false,
                        `<i class="ti ti-plus me-1"></i>Extra`);
                    handleSuccess(response, null, null, "/riwayat-pengunjung-masuk");
                };

                const errorCallback = function(error) {
                    setButtonLoadingState("#saveData .btn.btn-primary", false,
                        `<i class="ti ti-plus me-1"></i>Extra`);
                    handleValidationErrors(error, "saveData", ['durasi_extra', 'tarif_extra',
                        'tarif_mengantar_extra'
                    ]);
                };

                ajaxCall(url, "POST", data, successCallback, errorCallback);
            });
        });
    </script>
@endpush
