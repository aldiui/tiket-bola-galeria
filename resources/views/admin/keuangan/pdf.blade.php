@extends('layouts.pdf')

@section('title', 'Laporan Keuangan ' . $bulanTahun)

@push('style')
@endpush

@section('main')
    <div>
        <table width="100%" border="1" cellpadding="2.5" cellspacing="0">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>Nama Anak</th>
                    <th>Durasi</th>
                    <th>Orang Tua</th>
                    <th>Metode Pembayaran</th>
                    <th>Jumlah Pembayaran</th>
                    <th>Tanggal dan Waktu</th>
                    <th>Admin</th>
                </tr>
            </thead>
            <tbody valign="top">
                @foreach ($pengunjungMasuks as $pengunjungMasuk)
                    <tr>
                        <td align ="center">{{ $loop->iteration }}</td>
                        <td>{{ $pengunjungMasuk->nama_anak }}</td>
                        <td>{{ $pengunjungMasuk->durasi_extra ? $pengunjungMasuk->durasi_bermain + $pengunjungMasuk->durasi_extra : $pengunjungMasuk->durasi_bermain }}
                            Jam </td>
                        <td>{{ $pengunjungMasuk->nama_orang_tua }}</td>
                        <td>{{ $pengunjungMasuk->metode_pembayaran }}</td>
                        <td>{{ formatRupiah($pengunjungMasuk->durasi_extra ? $pengunjungMasuk->tarif + $pengunjungMasuk->tarif_extra : $pengunjungMasuk->tarif) }}
                        </td>
                        <td>{{ formatTanggal($pengunjungMasuk->created_at, 'j M Y H:i:s') }}</td>
                        <td>{{ $pengunjungMasuk->user->nama }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" align="center">Total Pembayaran</td>
                    <td>{{ formatRupiah($pengunjungMasuks->sum('tarif') + $pengunjungMasuks->sum('tarif_extra')) }}</td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection

@push('scripts')
@endpush
