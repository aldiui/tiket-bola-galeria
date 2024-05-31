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
                    <th>Admin</th>
                </tr>
            </thead>
            <tbody valign="top">
                @foreach ($pengunjungMasuks as $pengunjungMasuk)
                    @php
                        $total = $pengunjungMasuk->durasi_extra
                            ? $pengunjungMasuk->tarif + $pengunjungMasuk->tarif_extra
                            : $pengunjungMasuk->tarif;
                        $totalDenganDiskon = $total - $pengunjungMasuk->diskon ?? 0;
                    @endphp
                    <tr>
                        <td align ="center">{{ $loop->iteration }}</td>
                        <td align="center">{{ $pengunjungMasuk->nama_anak }}</td>
                        <td align="center">
                            {{ $pengunjungMasuk->durasi_extra ? $pengunjungMasuk->durasi_bermain + $pengunjungMasuk->durasi_extra : $pengunjungMasuk->durasi_bermain }}
                            Jam </td>
                        <td align="center">{{ $pengunjungMasuk->nama_orang_tua }}</td>
                        <td align="center">
                            {{ $pengunjungMasuk->pembayaran_id ? $pengunjungMasuk->pembayaran->nama : 'Cash' }}</td>
                        <td align="right">{{ formatRupiah($total) }}
                        </td>
                        <td align="right">{{ formatRupiah($pengunjungMasuk->diskon) }}</td>
                        <td align="right">{{ formatRupiah($totalDenganDiskon) }}</td>
                        <td align="center">{{ formatTanggal($pengunjungMasuk->created_at, 'j M Y H:i:s') }}</td>
                        <td align="center">{{ $pengunjungMasuk->user->nama }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" align="center">Total Pembayaran</td>
                    <td align="right">
                        {{ formatRupiah($pengunjungMasuks->sum('tarif') + $pengunjungMasuks->sum('tarif_extra')) }}</td>
                    <td align="right">{{ formatRupiah($pengunjungMasuks->sum('diskon')) }}</td>
                    <td align="right">
                        {{ formatRupiah($pengunjungMasuks->sum('tarif') + $pengunjungMasuks->sum('tarif_extra') - $pengunjungMasuks->sum('diskon')) }}
                    </td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection

@push('scripts')
@endpush
