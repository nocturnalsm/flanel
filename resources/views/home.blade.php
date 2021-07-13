@extends('layouts.body')

@section('content')
<style>
    .list-group-item-action:hover {
        background-color: #c4e2ff;
    }
</style>
<div class="row">
    <div class="col-md-4 col-sm-12 mb-6">
        <div class="card card-sm">
            <div class="card-header">
                <strong>Transaksi</strong>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @can('transaksi.transaksi')
                    <a class="list-group-item list-group-item-action" href="./transaksi">Perekaman Data</a>
                    @endcan
                    @can('pembayaran.transaksi')
                    <a class="list-group-item list-group-item-action" href="./transaksi/pembayaran">Transaksi Pembayaran</a>
                    @endcan
                    @can('transaksi.browse')
                    <a class="list-group-item list-group-item-action" href="./transaksi/browse">Browse Job Order</a>
                    @endif
                    @can('aruskas')
                    <a class="list-group-item list-group-item-action" href="./transaksi/aruskas">Browse Arus Kas</a>
                    @endcan
                </ul>
            </div>
        </div>
    </div>
    @if(auth()->user()->can('master.*'))
    <div class="col-md-4 col-sm-12 mb-6">
        <div class="card card-sm">
            <div class="card-header">
                <strong>Menu Master</strong>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @can('master.satuan.browse')
                    <a class="list-group-item list-group-item-action" href="./master/satuan">Referensi Satuan</a>
                    @endcan
                    @can('master.customer.browse')
                    <a class="list-group-item list-group-item-action" href="./master/customer">Referensi Customer</a>
                    @endcan
                    @can('master.jenisdokumen.browse')
                    <a class="list-group-item list-group-item-action" href="./master/jenisdokumen">Referensi Jenis Dokumen</a>
                    @endcan
                    @can('master.kodetransaksi.browse')
                    <a class="list-group-item list-group-item-action" href="./master/kodetransaksi">Referensi Kode Transaksi</a>
                    @endcan
                    @can('master.rekening.browse')
                    <a class="list-group-item list-group-item-action" href="./master/rekening">Referensi Rekening</a>
                    @endcan
                </ul>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
