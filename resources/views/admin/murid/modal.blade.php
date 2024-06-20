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
                        <label for="nomor_murid" class="form-label">Nomor Murid <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nomor_murid" name="nomor_murid">
                        <small class="invalid-feedback" id="errornomor_murid"></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="nama_anak" class="form-label">Nama Anak <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_anak" id="nama_anak">
                        <small class="invalid-feedback" id="errornama_anak"></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="umur" class="form-label">Umur <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="umur" name="umur">
                        <small class="invalid-feedback" id="errorumur"></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="kelas" class="form-label">Kelas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="kelas" name="kelas">
                        <small class="invalid-feedback" id="errorkelas"></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="nama_orang_tua" class="form-label">Nama Orang Tua <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_orang_tua" id="nama_orang_tua">
                        <small class="invalid-feedback" id="errornama_orang_tua"></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="nomor_telepon" class="form-label">Nomor Telepon <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nomor_telepon" id="nomor_telepon">
                        <small class="invalid-feedback" id="errornomor_telepon"></small>
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
