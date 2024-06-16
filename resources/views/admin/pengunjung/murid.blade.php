@extends('layouts.app')

@section('title', 'Pengunjung Murid')

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
                    <label for="murid_id" class="form-label">Murid <span class="text-danger">*</span></label>
                    <select class="form-control border" name="murid_id" id="murid_id"></select>
                    <small class="invalid-feedback" id="errormurid_id"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="nama_anak" class="form-label">Nama Anak <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama_anak" id="nama_anak">
                    <small class="invalid-feedback" id="errornama_anak"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="nama_panggilan" class="form-label">Nama Panggilan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama_panggilan" id="nama_panggilan">
                    <small class="invalid-feedback" id="errornama_panggilan"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="nama_orang_tua" class="form-label">Nama Orang Tua <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama_orang_tua" id="nama_orang_tua">
                    <small class="invalid-feedback" id="errornama_orang_tua"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select class="form-control" name="jenis_kelamin" id="jenis_kelamin">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-Laki">Laki-Laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                    <small class="invalid-feedback" id="errorjenis_kelamin"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="nomor_telepon" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nomor_telepon" id="nomor_telepon">
                    <small class="invalid-feedback" id="errornomor_telepon"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="email" class="form-label">Email (Opsional)</label>
                    <input type="email" class="form-control" name="email" id="email">
                    <small class="invalid-feedback" id="erroremail"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="durasi_bermain" class="form-label">Durasi Bermain <span class="text-danger">*</span></label>
                    <select class="form-control" name="durasi_bermain" id="durasi_bermain">
                        <option value="">-- Pilih Durasi Bermain --</option>
                        <option value="1">1 Jam</option>
                        <option value="2">2 Jam</option>
                        <option value="3">3 Jam</option>
                        <option value="4">4 Jam</option>
                        <option value="5">5 Jam</option>
                        <option value="6">6 Jam</option>
                        <option value="7">7 Jam</option>
                        <option value="8">8 Jam</option>
                        <option value="9">9 Jam</option>
                        <option value="10">10 Jam</option>
                    </select>
                    <small class="invalid-feedback" id="errordurasi_bermain"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="pembayaran_id" class="form-label">Metode Pembayaran <span
                            class="text-danger">*</span></label>
                    <select class="form-control" name="pembayaran_id" id="pembayaran_id">
                        <option value="">Cash</option>
                        @foreach ($pembayaran as $row)
                            <option value="{{ $row->id }}">{{ $row->nama_bank }} - {{ $row->nama_akun }} (
                                {{ $row->nomor_rekening }} )</option>
                        @endforeach
                    </select>
                    <small class="invalid-feedback" id="errorpembayaran_id"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="tarif" class="form-label">Tarif <span class="text-danger">*</span></label>
                    <input type="number" value="0" class="form-control" name="tarif" id="tarif" readonly>
                    <small class="invalid-feedback" id="errortarif"></small>
                    <small class="d-block pt-2">Tarif otomatis berdasarkan waktu yang di pilih</small>
                </div>
                <div class="form-group mb-3">
                    <label for="nominal_diskon" class="form-label">Nominal Diskon </label>
                    <input type="number" value="0" class="form-control" name="nominal_diskon" id="nominal_diskon"
                        readonly>
                    <small class="invalid-feedback" id="errornominal_diskon"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="biaya_mengantar" class="form-label">Biaya Mengantar (Opsional)</label>
                    <input type="number" value="0" class="form-control" name="biaya_mengantar"
                        id="biaya_mengantar">
                    <small class="invalid-feedback" id="errorbiaya_mengantar"></small>
                    <small class="d-block pt-2">Diisi jika ada yang mengantar bermain bola</small>
                </div>
                <div class="form-group mb-3">
                    <label for="biaya_kaos_kaki" class="form-label">Biaya Kaos Kaki (Opsional)</label>
                    <input type="number" value="0" class="form-control" name="biaya_kaos_kaki"
                        id="biaya_kaos_kaki">
                    <small class="invalid-feedback" id="errorbiaya_kaos_kaki"></small>
                    <small class="d-block pt-2">Diisi jika ada yang mau menggunakan kaos kaki</small>
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
            select2ToJsonMurid();

            $("#durasi_bermain").on("change", function() {
                const durasi_bermain = $("#durasi_bermain").val();
                const calculateTarif = durasi_bermain * {{ $pengaturan->tarif ?? 0 }};
                $("#tarif").val(calculateTarif);
                $("#nominal_diskon").val(calculateTarif);
            });

            $("#saveData").submit(function(e) {
                setButtonLoadingState("#saveData .btn.btn-primary", true);
                e.preventDefault();
                const url = "{{ route('pengunjungMurid') }}";
                const data = new FormData(this);

                const successCallback = function(response) {
                    $('#saveData .form-control').removeClass("is-invalid");
                    $('#saveData .invalid-feedback').html("").val();
                    setButtonLoadingState("#saveData .btn.btn-primary", false,
                        `<i class="ti ti-plus me-1"></i>Simpan`);
                    handleSuccess(response, null, null, "/riwayat-pengunjung-masuk");
                };

                const errorCallback = function(error) {
                    setButtonLoadingState("#saveData .btn.btn-primary", false,
                        `<i class="ti ti-plus me-1"></i>Simpan`);
                    handleValidationErrors(error, "saveData", ["nama_anak", 'nama_panggilan',
                        'nama_orang_tua', 'jenis_kelamin', 'nomor_telepon', 'durasi_bermain',
                        'pembayaran_id', 'tarif', 'email', 'diskon', 'alasan_diskon',
                        'biaya_mengantar', 'biaya_kaos_kaki', 'nominal_diskon'
                    ]);
                };

                ajaxCall(url, "POST", data, successCallback, errorCallback);
            });

            $("#murid_id").on("change", function() {
                let cekMuridId = $("#murid_id").val();
                const fields = ["nama_anak", "nama_orang_tua", "nomor_telepon"];

                const successCallback = function(response) {
                    fields.forEach((field) => {
                        if (response.data[field]) {
                            $(`#${field}`)
                                .val(response.data[field])
                                .trigger("change");
                        }
                    });
                };

                const errorCallback = function(error) {
                    console.log(error);
                };
                ajaxCall(`/murid/${cekMuridId}`, "GET", null, successCallback,
                    errorCallback);
            });
        });
    </script>
@endpush