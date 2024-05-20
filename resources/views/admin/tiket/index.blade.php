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
    <div class="min-vh-100 bg-white">
        <div class="text-center">
            <img src="{{ asset('images/logos/logo.png') }}" width="150" class="" alt="" />
        </div>
        <div class="container-fluid">
            <div class="row justify-content-center py-5">
                <div class="col-12">
                    <div class="card mb-1">
                        <div class="card-header">
                            <h5 class="card-title fw-semibold text-center">Riwayat Pengunjung Masuk {{ formatTanggal() }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="pengunjung-masuk-table" class="table table-bordered table-striped"
                                    width="100%">
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
            }, 30000);
        });
    </script>
@endpush
