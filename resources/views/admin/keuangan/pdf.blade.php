@extends('layouts.pdf')

@section('title', 'Laporan Keuangan ' . formatTanggal($tanggalMulai, 'j M Y') . ' - ' . formatTanggal($tanggalSelesai,
    'j M Y'))

    @push('style')
    @endpush

@section('main')
    <div>
        <table width="100%" border="1" cellpadding="4" cellspacing="0">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>Nama Anak</th>
                    <th>Durasi</th>
                    <th>Orang Tua</th>
                    <th>Metode Pembayaran</th>
                    <th>Pembayaran</th>
                    <th>Diskon</th>
                    <th>Total</th>
                    <th>Tanggal dan Waktu</th>
                    <th>Ket</th>
                    <th>Admin</th>
                </tr>
            </thead>
            <tbody valign="top">
                @php
                    $totalAkhir = 0;
                    $totalDiskonAkhir = 0;
                    $totalPembayaranAkhir = 0;
                @endphp
                @foreach ($finalResults as $pengunjungMasuk)
                    @if ($pengunjungMasuk->type)
                        @php
                            $totalDurasi = $pengunjungMasuk->durasi_extra
                                ? $pengunjungMasuk->tarif + $pengunjungMasuk->tarif_extra
                                : $pengunjungMasuk->tarif;
                            $total =
                                $totalDurasi +
                                $pengunjungMasuk->denda +
                                $pengunjungMasuk->biaya_mengantar +
                                $pengunjungMasuk->biaya_kaos_kaki +
                                $pengunjungMasuk->biaya_mengantar_extra;
                            $totalDenganDiskon = $total - ($pengunjungMasuk->nominal_diskon ?? 0);
                        @endphp
                        <tr>
                            <td align="center">{{ $loop->iteration }}</td>
                            <td>{{ $pengunjungMasuk->nama_anak }}</td>
                            <td>{{ $pengunjungMasuk->durasi_extra ? $pengunjungMasuk->durasi_bermain + $pengunjungMasuk->durasi_extra : $pengunjungMasuk->durasi_bermain }}
                                Jam</td>
                            <td>{{ $pengunjungMasuk->nama_orang_tua }}</td>
                            <td>
                                @if ($pengunjungMasuk->pembayaran_id)
                                    {{ $pengunjungMasuk->pembayaran->nama_bank }}
                                @else
                                    {{ $pengunjungMasuk->type }}
                                @endif
                            </td>
                            <td align="right">{{ formatRupiah($total) }}</td>
                            <td align="right">{{ formatRupiah($pengunjungMasuk->nominal_diskon ?? 0) }}</td>
                            <td align="right">{{ formatRupiah($totalDenganDiskon) }}</td>
                            <td>{{ formatTanggal($pengunjungMasuk->created_at, 'j M Y H:i:s') }}</td>
                            <td>{{ $pengunjungMasuk->type }}</td>
                            <td>{{ $pengunjungMasuk->user->nama }}</td>
                        </tr>
                        @php
                            $totalAkhir += $total;
                            $totalDiskonAkhir += $pengunjungMasuk->nominal_diskon ?? 0;
                            $totalPembayaranAkhir += $totalDenganDiskon;
                        @endphp
                    @else
                        <tr>
                            <td align="center">{{ $loop->iteration }}</td>
                            <td>{{ $pengunjungMasuk->membership->nama_anak }}</td>
                            <td>{{ $pengunjungMasuk->paketMembership->nama }}</td>
                            <td>{{ $pengunjungMasuk->membership->nama_orang_tua }}</td>
                            <td>{{ $pengunjungMasuk->pembayaran->nama_bank }}</td>
                            <td align="right">{{ formatRupiah($pengunjungMasuk->nominal) }}</td>
                            <td align="right">{{ formatRupiah(0) }}</td>
                            <td align="right">{{ formatRupiah($pengunjungMasuk->nominal) }}</td>
                            <td>{{ formatTanggal($pengunjungMasuk->created_at, 'j M Y H:i:s') }}</td>
                            <td>Membership</td>
                            <td>{{ $pengunjungMasuk->user->nama }}</td>
                        </tr>
                        @php
                            $totalAkhir += $pengunjungMasuk->nominal;
                            $totalDiskonAkhir += 0;
                            $totalPembayaranAkhir += $pengunjungMasuk->nominal;
                        @endphp
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" align="center"><strong>Total Pembayaran</strong></td>
                    <td align="right"><strong>{{ formatRupiah($totalAkhir) }}</strong></td>
                    <td align="right"><strong>{{ formatRupiah($totalDiskonAkhir) }}</strong></td>
                    <td align="right"><strong>{{ formatRupiah($totalPembayaranAkhir) }}</strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection

@push('scripts')
@endpush
