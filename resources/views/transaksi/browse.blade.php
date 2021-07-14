@extends('layouts.base')
@section('main')
<div class="card">
    <div class="card-header">
        Browse Job Order
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-10">
                <form id="form" method="POST" action="/transaksi/browse?filter=1&export=1">
                    @csrf
                    <div class="row">
                        <label class="col-md-2">Customer</label>
                        <div class="col-md-3">
                            <select class="form-control form-control-sm" id="customer" name="customer">
                                <option value="">Semua</option>
                                @foreach($datacustomer as $cust)
                                <option value="{{ $cust->id_customer }}">{{ $cust->nama_customer }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            Kategori
                        </div>
                        <div class="col-md-3">
                            <select class="form-control form-control-sm" id="kategori1" name="kategori1">
                                <option value=""></option>
                                @foreach($datakategori1 as $kat)
                                <option value="{{ $kat }}">{{ $kat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label class="px-sm-3 col-sm-1">Nilai</label>
                        <div class="col-md-5">
                            <input type="text" id="isikategori1_text" name="isikategori1" class="form-control form-control-sm" style="display:inline;width: 120px">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            Kategori
                        </div>
                        <div class="col-md-3">
                            <select class="form-control form-control-sm" id="kategori2" name="kategori2">
                                <option value=""></option>
                                @foreach($datakategori2 as $kat)
                                <option value="{{ $kat }}">{{ $kat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label class="px-sm-3 col-sm-1">Periode</label>
                        <div class="col-md-5">
                            <input autocomplete="off" type="text" id="dari2" name="dari2" class="datepicker form-control d-inline form-control-sm" style="width: 120px">
                            &nbsp;&nbsp;sampai&nbsp;&nbsp;
                            <input autocomplete="off" type="text" id="sampai2" name="sampai2" class="datepicker form-control d-inline form-control-sm" style="width: 120px">
                        </div>
                    </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 px-sm-2 pt-2">
                <a id="preview" class="btn btn-primary">Filter</a>
                <a id="export" class="btn btn-primary disabled">Export</a>
            </div>
        </div>
        </form>
        <div class="row mt-4 pt-4">
            <div class="col" id="divtable">
                <table width="100%" id="grid" class="table">
                    <thead>
                        <th>Opsi</th>
                        <th>Job Order</th>
                        <th>Tgl Job</th>
                        <th>Tgl Tiba</th>
                        <th>Ttl Modal</th>
                        <th>Tot Beli</th>
                        <th>Tot Jual</th>
                        <th>Profit</th>
                        <th>Tot Debet</th>
                        <th>Tot Kredit</th>
                        <th>Saldo</th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('stylesheets_end')
    <link href="{{ asset('jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
@endpush
@push('scripts_end')
<script type="text/javascript" src="{{ asset('jquery-ui/jquery-ui.min.js') }}"></script>
<script>
    $(function(){
        $(".datepicker").datepicker({dateFormat: "dd-mm-yy"});
        Number.prototype.formatMoney = function(places, symbol, thousand, decimal) {
        	places = !isNaN(places = Math.abs(places)) ? places : 2;
        	symbol = symbol !== undefined ? symbol : "";
        	thousand = thousand || ",";
        	decimal = decimal || ".";
        	var number = this,
        			negative = number < 0 ? "-" : "",
        			i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + "",
        			j = (j = i.length) > 3 ? j % 3 : 0;
        	return symbol + negative + (j ? i.substr(0, j) + thousand : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand) + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : "");
        };
        var columns = [{target: 0, data: null, orderable: false}, {target: 1, data: "JOB_ORDER"},
        {target: 2, data: "TGL_JOB"},
        {target: 3, data: "TGL_TIBA"},
        {target: 4, data: "TOTAL_MODAL"},
        {target: 5, data: "TOTAL_BELI"},
        {target: 6, data: "TOTAL_JUAL"},
        {target: 7, data: "PROFIT"},
        {target: 8, data: "TOTAL_DEBET"},
        {target: 9, data: "TOTAL_KREDIT"},
        {target: 10, data: "SALDO"}
        ];

        var grid = $("#grid").DataTable({responsive: false,
            dom: "rtip",
            "language":
            {
                "lengthMenu": "Menampilkan _MENU_ record per halaman",
                "info": "",
                "infoEmpty": "Data tidak ada",
                "infoFiltered": "",
                "search":         "Cari:",
                "zeroRecords":    "Tidak ada data yang sesuai pencarian",
                "paginate": {
                    "next":       ">>",
                    "previous":   "<<"
                }
            },
            order: [[0, 'asc']],
            columns: columns,
            rowCallback: function(row, data)
            {
                $(row).attr("id-transaksi", data[0]);
                $('td:eq(0)', row).html('<a title="Edit" href="/transaksi/' + data.ID + '"><i class="fa fa-edit"></i></a>');
                $("td:eq(4)", row).html(parseFloat(data.TOTAL_MODAL).formatMoney(2));
                $("td:eq(5)", row).html(parseFloat(data.TOTAL_BELI).formatMoney(2));
                $("td:eq(6)", row).html(parseFloat(data.TOTAL_JUAL).formatMoney(2));
                $("td:eq(7)", row).html(parseFloat(data.PROFIT).formatMoney(2));
                $("td:eq(8)", row).html(parseFloat(data.TOTAL_DEBET).formatMoney(2));
                $("td:eq(9)", row).html(parseFloat(data.TOTAL_KREDIT).formatMoney(2));
                $("td:eq(10)", row).html(parseFloat(data.SALDO).formatMoney(2));
            },
            columnDefs: [
                { "orderable": false, "targets": 0 }
            ]
        });
        $("#preview").on("click", function(){
            $.ajax({
            method: "POST",
            url: "/transaksi/browse?filter=1",
            data: $("#form").serialize(),
            success: function(msg){
                    grid.clear().rows.add(msg);
                    grid.columns.adjust().draw();
                    if (msg.length == 0){
                        $("#export").addClass("disabled");
                    }
                    else {
                        $("#export").removeClass("disabled");
                    }
            }
            });
        })
        $("#form input, select").on("change", function(){
            $("#export").addClass("disabled");
        })
        $("#export").on("click", function(){
            $("#form").submit();
        })
    })
</script>
@endpush
