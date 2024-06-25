@extends('layouts.app')

@section('title', 'Pengunjung Group')

@push('style')
@endpush

@section('main')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title fw-semibold">Tambah Data @yield('title')</h5>
        </div>
        <div class="card-body">
            <form id="saveData" autocomplete="off">
                @csrf
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
                <div class="form-group mb-3">
                    <label for="durasi_bermain" class="form-label">Durasi Bermain <span class="text-danger">*</span></label>
                    <select class="form-control" name="durasi_bermain" id="durasi_bermain">
                        <option value="">-- Pilih Durasi Bermain --</option>
                        @for ($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }} Jam</option>
                        @endfor
                    </select>
                    <small class="invalid-feedback" id="errordurasi_bermain"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="pembayaran_id" class="form-label">Metode Pembayaran <span
                            class="text-danger">*</span></label>
                    <select class="form-control" name="pembayaran_id" id="pembayaran_id">
                        <option value="">-- Pilih Pembayaran --</option>
                        @foreach ($pembayaran as $row)
                            <option value="{{ $row->id }}">{{ $row->nama_bank }}</option>
                        @endforeach
                    </select>
                    <small class="invalid-feedback" id="errorpembayaran_id"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="jumlah_anak" class="form-label">Jumlah Anak <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="jumlah_anak" id="jumlah_anak">
                    <small class="invalid-feedback" id="errorjumlah_anak"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="tarif" class="form-label">Tarif <span class="text-danger">*</span></label>
                    <input type="number" value="0" class="form-control" name="tarif" id="tarif" readonly>
                    <small class="invalid-feedback" id="errortarif"></small>
                    <small class="d-block pt-2">Tarif otomatis berdasarkan waktu dan jumlah anak</small>
                </div>
                <div class="form-group mb-3">
                    <label for="diskon" class="form-label">Diskon % (Opsional)</label>
                    <input type="number" value="0" class="form-control" name="diskon" id="diskon" min="0"
                        max="100">
                    <small class="invalid-feedback" id="errordiskon"></small>
                    <small class="d-block pt-2">Diskon berupa potongan dengan mengisi persentase dari 0 sampai 100</small>
                </div>
                <div class="form-group mb-3">
                    <label for="nominal_diskon" class="form-label">Nominal Diskon </label>
                    <input type="number" value="0" class="form-control" name="nominal_diskon" id="nominal_diskon"
                        readonly>
                    <small class="invalid-feedback" id="errornominal_diskon"></small>
                </div>
                <div class="form-group mb-3">
                    <label for="tarif_mengantar" class="form-label">Biaya Pendamping (Opsional)</label>
                    <select class="form-control" name="tarif_mengantar" id="tarif_mengantar">
                        <option value="">-- Pilih Jumlah Pendamping --</option>
                        <option value="0">Tanpa Pendamping tambahan</option>
                        <option value="1">1 Pendamping</option>
                        <option value="2">2 Pendamping</option>
                        <option value="3">3 Pendamping</option>
                        <option value="4">4 Pendamping</option>
                    </select>
                    <small class="invalid-feedback" id="errortarif_mengantar"></small>
                    <small class="d-block pt-2">Anak di bawah umur 6 tahun wajib pendamping</small>
                </div>
                <div class="form-group mb-3">
                    <label for="biaya_mengantar" class="form-label">Tarif Pendamping <span
                            class="text-danger">*</span></label>
                    <input type="number" value="0" class="form-control" name="biaya_mengantar"
                        id="biaya_mengantar" readonly>
                    <small class="invalid-feedback" id="errorbiaya_mengantar"></small>
                    <small class="d-block pt-2">Tarif otomatis berdasarkan jumlah pendamping</small>
                </div>
                <div class="form-group mb-3">
                    <label for="tarif_kaos_kaki" class="form-label">Biaya Kaos Kaki (Opsional)</label>
                    <select class="form-control" name="tarif_kaos_kaki" id="tarif_kaos_kaki">
                        <option value="">-- Pilih Jumlah Kaos Kaki --</option>
                        <option value="0">Tanpa Kaos Kaki</option>
                        @for ($i = 0; $i <= 30; $i++)
                            <option value="{{ $i }}">{{ $i }} Pasang Kaos Kaki</option>
                        @endfor
                    </select>
                    <small class="invalid-feedback" id="errortarif_kaos_kaki"></small>
                    <small class="d-block pt-2">Diisi jika ada yang membutuhkan kaos kaki</small>
                </div>
                <div class="form-group mb-3">
                    <label for="biaya_kaos_kaki" class="form-label">Tarif Kaos Kaki <span
                            class="text-danger">*</span></label>
                    <input type="number" value="0" class="form-control" name="biaya_kaos_kaki"
                        id="biaya_kaos_kaki" readonly>
                    <small class="invalid-feedback" id="errorbiaya_kaos_kaki"></small>
                    <small class="d-block pt-2">Tarif otomatis berdasarkan jumlah pembelian</small>
                </div>
                <div class="form-group mb-3">
                    <label for="alasan_diskon" class="form-label">Alasan Diskon (Opsional)</label>
                    <textarea name="alasan_diskon" id="alasan_diskon" cols="20" rows="10" class="form-control"></textarea>
                    <small class="invalid-feedback" id="erroralasan_diskon"></small>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            function calculateNominalDiskon() {
                const tarif = parseFloat($("#tarif").val());
                const biaya_mengantar = parseFloat($("#biaya_mengantar").val());
                const biaya_kaos_kaki = parseFloat($("#biaya_kaos_kaki").val());
                const diskon = parseFloat($("#diskon").val());
                const tarif_total = tarif + biaya_mengantar + biaya_kaos_kaki;
                const nominalDiskon = tarif_total * (diskon / 100);
                $("#nominal_diskon").val(nominalDiskon.toFixed(0));
            }

            $("#durasi_bermain").on("change", function() {
                const durasi_bermain = $("#durasi_bermain").val();
                const jumlah_anak = $("#jumlah_anak").val();
                let calculateTarif = 0;

                if (jumlah_anak >= 25) {
                    calculateTarif = durasi_bermain * {{ $pengaturan->tarif_group_25 ?? 0 }} * jumlah_anak;
                } else if (jumlah_anak > -10) {
                    calculateTarif = durasi_bermain * {{ $pengaturan->tarif_group_10 ?? 0 }} * jumlah_anak;
                } else {
                    calculateTarif = durasi_bermain * {{ $pengaturan->tarif ?? 0 }} * jumlah_anak;
                }

                $("#tarif").val(calculateTarif);
                calculateNominalDiskon();
            });

            $("#jumlah_anak").on("input", function() {
                const durasi_bermain = $("#durasi_bermain").val();
                const jumlah_anak = $("#jumlah_anak").val();
                let calculateTarif = 0;

                if (jumlah_anak >= 25) {
                    calculateTarif = durasi_bermain * {{ $pengaturan->tarif_group_25 ?? 0 }} * jumlah_anak;
                } else if (jumlah_anak >= 10) {
                    calculateTarif = durasi_bermain * {{ $pengaturan->tarif_group_10 ?? 0 }} * jumlah_anak;
                } else {
                    calculateTarif = durasi_bermain * 0 * jumlah_anak;
                }

                $("#tarif").val(calculateTarif);
                calculateNominalDiskon();
            });


            $("#tarif_mengantar").on("change", function() {
                const tarif_mengantar = $("#tarif_mengantar").val();
                const durasi_bermain = $("#durasi_bermain").val();
                const calculateTarif_mengantar = tarif_mengantar * durasi_bermain *
                    {{ $pengaturan->tarif_mengantar ?? 0 }};
                $("#biaya_mengantar").val(calculateTarif_mengantar);
                calculateNominalDiskon();
            });

            $("#tarif_kaos_kaki").on("change", function() {
                const tarif_kaos_kaki = $("#tarif_kaos_kaki").val();
                const calculateTarif_kaos_kaki = tarif_kaos_kaki * {{ $pengaturan->tarif_kaos_kaki ?? 0 }};
                $("#biaya_kaos_kaki").val(calculateTarif_kaos_kaki);
                calculateNominalDiskon();
            });

            $("#diskon").on("input", function() {
                calculateNominalDiskon();
            });

            $("#saveData").submit(function(e) {
                setButtonLoadingState("#saveData .btn.btn-primary", true);
                e.preventDefault();
                const url = "{{ route('pengunjungGroup') }}";
                const data = new FormData(this);

                const successCallback = function(response) {
                    $('#saveData .form-control').removeClass("is-invalid");
                    $('#saveData .invalid-feedback').html("");
                    setButtonLoadingState("#saveData .btn.btn-primary", false,
                        `<i class="ti ti-plus me-1"></i>Simpan`);
                    handleSuccess(response, null, null, "/riwayat-pengunjung-masuk");
                };

                const errorCallback = function(error) {
                    setButtonLoadingState("#saveData .btn.btn-primary", false,
                        `<i class="ti ti-plus me-1"></i>Simpan`);
                    handleValidationErrors(error, "saveData", ["nama_group", 'nama_panggilan',
                        'penanggung_jawab', 'jenis_kelamin', 'nomor_telepon', 'durasi_bermain',
                        'pembayaran_id', 'tarif', 'tarif_mengantar', 'tarif_kaos_kaki', 'email',
                        'diskon', 'alasan_diskon', 'biaya_mengantar', 'biaya_kaos_kaki',
                        'nominal_diskon', 'jumlah_anak'
                    ]);
                };

                ajaxCall(url, "POST", data, successCallback, errorCallback);
            });
        });
    </script>
@endpush
