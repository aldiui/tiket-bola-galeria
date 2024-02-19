<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Data Admin</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="saveData" autocomplete="off">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama" name="nama">
                        <small class="invalid-feedback" id="errornama"></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email">
                        <small class="invalid-feedback" id="erroremail"></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small class="invalid-feedback" id="errorpassword"></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password <span
                                class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation"
                            name="password_confirmation">
                        <small class="invalid-feedback" id="errorpassword_confirmation"></small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Hak Akses</label>
                        <div class="d-flex gap-1 flex-wrap">
                            <input type="checkbox" class="btn-check" name="tambah_pengunjung_masuk"
                                id="tambah_pengunjung_masuk" autocomplete="off" value="1">
                            <label class="btn btn-outline-primary" for="tambah_pengunjung_masuk">Tambah Pengunjung
                                Masuk</label>
                            <input type="checkbox" class="btn-check" name="tambah_pengunjung_keluar"
                                id="tambah_pengunjung_keluar" autocomplete="off" value="1">
                            <label class="btn btn-outline-primary" for="tambah_pengunjung_keluar">Tambah Pengunjung
                                Keluar</label>
                            <input type="checkbox" class="btn-check" name="riwayat_pengunjung_masuk"
                                id="riwayat_pengunjung_masuk" autocomplete="off" value="1">
                            <label class="btn btn-outline-primary" for="riwayat_pengunjung_masuk">Riwayat Pengunjung
                                Masuk</label>
                            <input type="checkbox" class="btn-check" name="riwayat_pengunjung_keluar"
                                id="riwayat_pengunjung_keluar" autocomplete="off" value="1">
                            <label class="btn btn-outline-primary" for="riwayat_pengunjung_keluar">Riwayat Pengunjung
                                Keluar</label>
                            <input type="checkbox" class="btn-check" name="laporan_keuangan" id="laporan_keuangan"
                                autocomplete="off" value="1">
                            <label class="btn btn-outline-primary" for="laporan_keuangan">Laporan Keuangan</label>
                            <input type="checkbox" class="btn-check" name="user_management" id="user_management"
                                autocomplete="off" value="1">
                            <label class="btn btn-outline-primary" for="user_management">User Management</label>
                            <input type="checkbox" class="btn-check" name="ubah_tarif" id="ubah_tarif"
                                autocomplete="off" value="1">
                            <label class="btn btn-outline-primary" for="ubah_tarif">Ubah Tarif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
