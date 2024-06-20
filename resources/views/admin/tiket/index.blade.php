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
                    <marquee scrollamount="10" hspace="3" style="font-family: impact; font-size:30px; color: #FFFFFF;"
                        bgcolor="red" direction="left" scrollamount="2" align="center">Anak dibawah umur 6 tahun wajib
                        didampingi 1 orang penjaga.
                        Mohon penjemputan anak sesuai dengan ketentuan waktu bermain. Jika melewati waktu bermain
                        otomatis dianggap melakukan perpanjangan waktu bermain 1 jam berikutnya, terimakasih.

                        Children under 6 years of age must be accompanied by 1 caretaker. Kindly ensure you pick up
                        your children at the end of the designated play period. If the playtime limit is exceeded,
                        an additional hour will automatically be added to your session. Thank you.</marquee>
                    <div class="card mb-1">
                        <div class="card-header">
                            <h1 class="card-title fw-semibold text-center">Riwayat Pengunjung Masuk
                                {{ formatTanggal() }}
                            </h1>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-2">Data Pengujung : <span id="data-show">0</span></div>
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

            renderData();

            setInterval(function() {
                $("#pengunjung-masuk-table").DataTable().ajax.reload();
                renderData();
            }, 5000);



        });

        const renderData = () => {
            const successCallback = function(response) {
                $("#data-show").html(response.data);
            };

            const errorCallback = function(error) {
                console.log(error);
            };
            ajaxCall('{{ route('eTiket.index') }}', "GET", null, successCallback, errorCallback);
        }
    </script>
@endpush
