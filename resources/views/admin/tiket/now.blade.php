@extends('layouts.auth')

@section('title', 'E-Tiket')

@push('style')
@endpush

@section('main')
    <div
        class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
        <div class="d-flex align-items-center justify-content-center w-100">
            <div class="row justify-content-center w-100 py-5">
                <div class="col-md-8 col-lg-5 col-xl-4">
                    <div class="card mb-0 border border-primary border-5">
                        <div class="card-body">
                            <h5 class="fw-semibold text-center mb-3">E-Drop Ticket</h5>
                            <div class="mb-3 text-center">
                                <span class="badge bg-info" id="label-tiket">Coba</span>
                            </div>
                            <input type="hidden" id="now">
                            <div id="detail">
                                <div class="text-center py-5">
                                    <i class="ti ti-reload text-danger me-1"></i>
                                    Belum Ada Tiket
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            getTicketNow()

            setInterval(function() {
                getTicketNow()
            }, 30000);
        });
    </script>
@endpush
