@extends('layouts.pdf')

@section('title', 'Laporan Keungan')

@push('style')
@endpush

@section('main')
    <div>
        <table width="100%" border="1" cellpadding="2.5" cellspacing="0">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th style="">Nama</th>
                </tr>
            </thead>    
            <tbody valign="top">
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
@endpush