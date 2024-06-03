@extends('layouts.auth')

@section('title', 'E-Tiket')

@push('style')
    <link rel="stylesheet" href="{{ asset('libs/datatables/datatables.min.css') }}" />
    <style>
        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .blink {
            animation: blink 1s linear infinite;
        }
    </style>
@endpush

@section('main')
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container">
            <a class="navbar-brand mx-auto" href="#">
                <img src="{{ asset('images/logos/logo.png') }}" width="150" alt="Logo">
            </a>
        </div>
    </nav>
    <div class="bg-white pt-5">
        <div class="container">
            <div class="row justify-content-center py-5">
                <div class="col-12">
                    <div class="card mb-1">
                        <div class="card-header">
                            <h1 class="card-title fw-semibold text-center">Riwayat Pengunjung Masuk {{ formatTanggal() }}
                            </h1>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="pengunjung-masuk-table" class="table table-bordered table-striped" width="100%"
                                    style="font-size: 22px">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th>Nama Anak</th>
                                            <th>Sisa Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('libs/datatables/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            datatableCall('pengunjung-masuk-table', '{{ route('eTiket.index') }}', [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'nama_anak',
                    name: 'nama_anak'
                },
                {
                    data: 'durasi',
                    name: 'durasi'
                },
            ]);

            setInterval(function() {
                $("#pengunjung-masuk-table").DataTable().ajax.reload();
            }, 5000);
        });
    </script>
@endpush
