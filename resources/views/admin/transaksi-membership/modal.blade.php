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
                        <label for="membership_id" class="form-label">Membership <span class="text-danger">*</span></label>
                        <select class="form-control" name="membership_id" id="membership_id">
                            <option value="">-- Pilih Membership --</option>
                            @foreach ($membership as $row)
                                <option value="{{ $row->id }}">{{ $row->nama_anak }}</option>
                            @endforeach
                        </select>
                        <small class="invalid-feedback" id="errormembership_id"></small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="paket_membership_id" class="form-label">Paket <span
                                class="text-danger">*</span></label>
                        <select class="form-control" name="paket_membership_id" id="paket_membership_id">
                            <option value="">-- Pilih Paket --</option>
                            @foreach ($paketMembership as $row)
                                <option value="{{ $row->id }}">
                                    {{ $row->nama }} ({{ $row->durasi_hari }} Hari)
                                </option>
                            @endforeach
                        </select>
                        <small class="invalid-feedback" id="errorpaket_membership_id"></small>
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
                        <label for="nominal" class="form-label">Nominal <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="nominal" name="nominal" readonly>
                        <small class="invalid-feedback" id="errornominal"></small>
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
