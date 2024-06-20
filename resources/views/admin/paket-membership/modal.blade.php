<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"><span id="label-modal"></span> Data
                    @yield('title')</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="saveData" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" id="id">
                    <div class="form-group mb-3">
                        <label for="kode" class="form-label">Kode <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="kode" name="kode">
                        <small class="invalid-feedback" id="errorkode"></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="nama" class="form-label">Nama<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama" name="nama">
                        <small class="invalid-feedback" id="errornama"></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="durasi_hari" class="form-label">Durasi Hari<span
                                class="text-danger">*</span></label>
                        <select name="durasi_hari" id="durasi_hari" class="form-control">
                            <option value="">-- Pilih Durasi Hari --</option>
                            <option value="30">1 Bulan</option>
                            <option value="60">3 Bulan</option>
                            <option value="120">6 Bulan</option>
                        </select>
                        <small class="invalid-feedback" id="errordurasi_hari"></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="tarif" class="form-label">Tarif <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="tarif" name="tarif">
                        <small class="invalid-feedback" id="errortarif"></small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
