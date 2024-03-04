@extends('layouts.auth')

@section('title', 'Login')

@push('style')
@endpush

@section('main')
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <div
            class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-6 col-lg-4 col-xxl-3">
                        <div class="card mb-0">
                            <div class="card-body">
                                <a href="./" class="text-nowrap logo-img mb-3 d-flex justify-content-center">
                                    <img src="{{ asset('images/logos/logo1.PNG') }}" width="180" class=""
                                        alt="" />
                                </a>
                                <form id="login" autocomplete="off">
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input id="email" type="email" class="form-control" name="email">
                                        <small class="invalid-feedback" id="erroremail"></small>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="password" class="form-label">Password <span
                                                class="text-danger">*</span></label>
                                        <input id="password" type="password" class="form-control" name="password">
                                        <small class="invalid-feedback" id="errorpassword"></small>
                                    </div>
                                    <div class="form-group mb-3">
                                        <button type="submit" class="btn d-block w-100 btn-primary"><i
                                                class="ti ti-login me-1"></i>Login</button>
                                    </div>
                                </form>
                                <a href="{{ route('eTiket.index') }}" class="btn d-block w-100 btn-secondary">
                                    <i class="ti ti-layout me-1"></i>
                                    Monitoring Tiket
                                </a>
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
            $("#login").submit(function(e) {
                setButtonLoadingState("#login .btn.btn-primary", true,
                    `Login`);
                e.preventDefault();
                const url = "{{ route('login') }}";
                const data = new FormData(this);

                const successCallback = function(response) {
                    setButtonLoadingState("#login .btn.btn-primary", false,
                        `<i class="ti ti-login me-1"></i>Login`);
                    handleSuccess(response, null, null, "./");
                };

                const errorCallback = function(error) {
                    setButtonLoadingState("#login .btn.btn-primary", false,
                        `<i class="ti ti-login me-1"></i>Login`);
                    handleValidationErrors(error, "login", ["email", "password"]);
                };

                ajaxCall(url, "POST", data, successCallback, errorCallback);
            });
        });
    </script>
@endpush
