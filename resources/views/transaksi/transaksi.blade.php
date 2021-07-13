@extends('layouts.base')
@section('main')
<style>
    .error {display:none;font-size: 0.75rem;color: red};
</style>
<div class="modal fade" id="modaldetailbeli" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h4 class="modal-title w-100 font-weight-bold"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <form id="formbeli" act="">
                  <div class="modal-body mx-3">
                    <input type="hidden" name="idxdetailbeli" id="idxdetailbeli">
                    <input type="hidden" name="iddetailbeli" id="iddetailbeli">
                    <div class="form-row mb-1">
                        <label class="col-form-label col-md-3" for="noinv">Nama Barang</label>
                        <div class="col-md-9">
                            <input type="text" id="namabarang" name="namabarang" class="form-control form-control-sm validate">
                        </div>
                    </div>
                    <div class="form-row mb-1">
                        <label class="col-md-3 col-form-label">Qty Beli</label>
                        <div class="col-md-9">
                            <input type="text" class="number form-control form-control-sm" name="qtybeli" id="qtybeli">
                        </div>
                    </div>
                    <div class="form-row mb-1">
                        <label class="col-form-label col-md-3" for="satuan">Satuan</label>
                        <div class="col-md-9">
                            <select class="form-control form-control-sm" id="satuan" name="satuan">
                                <option value=""></option>
                                @foreach($satuan as $sat)
                                <option value="{{ $sat->id }}">{{ $sat->satuan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row mb-1">
                        <label class="col-md-3 col-form-label">Nominal Beli</label>
                        <div class="col-md-9">
                            <input type="text" class="number form-control form-control-sm" name="nominalbeli" id="nominalbeli">
                        </div>
                    </div>
                    <div class="form-row mb-1">
                        <label class="col-md-3 col-form-label">Harga Beli Satuan</label>
                        <div class="col-md-9">
                            <input readonly type="text" class="number form-control form-control-sm" name="hargabeli" id="hargabeli">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                @if($readonly != 'readonly')
                <a id="savedetailbeli" class="btn btn-primary">Simpan</a>
                <a class="btn btn-danger" data-dismiss="modal">Batal</a>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modaldetail" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h4 class="modal-title w-100 font-weight-bold"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mx-3">
                <form id="form" act="">
                    <input type="hidden" name="idxdetail" id="idxdetail">
                    <input type="hidden" name="iddetail" id="iddetail">
                    <div class="form-row mb-1">
                        <label class="col-form-label col-md-3" for="jenisdokumen">Jns Dok</label>
                        <div class="col-md-9">
                            <select class="form-control form-control-sm" id="jenisdokumen" name="jenisdokumen">
                                <option value=""></option>
                                @foreach($jenisdokumen as $dok)
                                <option value="{{ $dok->JENISDOKUMEN_ID }}">{{ $dok->URAIAN }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row mb-1">
                        <label class="col-form-label col-md-3" for="noinv">No Inv</label>
                        <div class="col-md-9">
                            <input type="text" id="noinv" name="noinv" class="form-control form-control-sm validate">
                        </div>
                    </div>
                    <div class="form-row mb-1">
                        <label class="col-form-label col-md-3" for="tglinv">Tgl Inv</label>
                        <div class="col-md-9">
                            <input type="text" class="datepicker form-control form-control-sm" name="tglinv" id="tglinv">
                        </div>
                    </div>
                    <div class="form-row mb-1">
                        <label class="col-form-label col-md-3" for="customer">Customer</label>
                        <div class="col-md-9">
                            <select class="form-control form-control-sm" id="customer" name="customer">
                                <option value=""></option>
                                @foreach($customer as $cust)
                                <option alamat="{{ $cust->alamat_customer}}" value="{{ $cust->id_customer }}">{{ $cust->nama_customer }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row mb-1">
                        <label class="col-form-label col-md-3">Alamat</label>
                        <div class="col-md-9">
                            <span id="alamatcustomer"></span>
                        </div>
                    </div>
                    <div class="form-row mb-1">
                        <label class="col-md-3 col-form-label">Qty Jual</label>
                        <div class="col-md-9">
                            <input type="text" class="number form-control form-control-sm" name="qtyjual" id="qtyjual">
                        </div>
                    </div>
                    <div class="form-row mb-1">
                        <label class="col-md-3 col-form-label">Harga</label>
                        <div class="col-md-9">
                            <input type="text" class="number form-control form-control-sm" name="hargajual" id="hargajual">
                        </div>
                    </div>
                    <div class="form-row mb-1">
                        <label class="col-md-3 col-form-label">Nominal Jual</label>
                        <div class="col-md-9">
                            <input readonly type="text" class="number form-control form-control-sm" name="nominaljual" id="nominaljual">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                @if($readonly != 'readonly')
                <a id="savedetail" class="btn btn-primary">Simpan</a>
                <a class="btn btn-danger" data-dismiss="modal">Batal</a>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="card col-md-12 p-0">
        <div class="card-header font-weight-bold">
            <div class="row">
                <div class="col-md-4 py-0 pl-4 mt-1">
                    Form Perekaman Data {{ $header->JOB_ORDER }}
                </div>
                <div class="col-md-8 py-0 pr-4 text-right">
                    @if($readonly == '')
                    <button type="button" id="btnsimpan" class="btn btn-primary btn-sm m-0">Simpan</button>&nbsp;
                    <a href="/" class="btn btn-default btn-sm m-0">Batal</a>&nbsp;
                    @can('transaksi.delete')
                    @if($header->ID != '')
                    <button type="button" id="deletetrans" class="btn btn-danger btn-sm m-0">Hapus</button>
                    <form id="formdelete">
                    @csrf
                    <input {{ $readonly }} type="hidden" name="iddelete" value="{{ $header->ID }}">
                    </form>
                    @endif
                    @endcan
                    @endif
                </div>
            </div>
        </div>
        <form id="transaksi" autocomplete="off">
        <div class="card-body">
            <input {{ $readonly }} type="hidden" value="{{ $header->ID }}" id="idtransaksi" name="idtransaksi">
            <div class="form-row px-2">
                <label class="col-md-1 col-form-label form-control-sm">Job Order</label>
                <div class="col-md-2">
                  <input type="text" readonly class="form-control form-control-sm" name="joborder" id="joborder" value="{{ $header->JOB_ORDER }}">
                </div>
                <div class="col-md-2"></div>
                <label class="col-md-1 col-form-label form-control-sm text-right">Tgl Job</label>
                <div class="col-md-2">
                  <input {{ $readonly }} type="text" class="datepicker form-control form-control-sm" name="tgljob" id="tgljob" value="{{ $header->TGL_JOB }}">
                </div>
            </div>
            <div class="form-row px-2">
                <label class="col-md-1 col-form-label form-control-sm">Jns Dokumen</label>
                <div class="col-md-2">
                    <select {{ $readonly == 'readonly' ? 'disabled' : '' }} class="form-control form-control-sm" id="jenisdokumen" name="jenisdokumen">
                        <option value=""></option>
                        @foreach($jenisdokumen as $jdok)
                        <option @if($header->JENIS_DOK == $jdok->JENISDOKUMEN_ID)selected @endif value="{{ $jdok->JENISDOKUMEN_ID }}">{{ $jdok->KODE }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row px-2">
                <label class="col-md-1 col-form-label form-control-sm text-right">Tgl Tiba Gdg</label>
                <div class="col-md-1">
                    <input {{ $readonly }} autocomplete="off" type="text" class="datepicker{{ $readonly == 'readonly' ? '-readonly' : '' }} form-control form-control-sm" name="tgltiba" value="{{ $header->TGL_TIBA }}" id="tgltiba">
                </div>
                <div class="col-md-1"></div>
                <label class="col-md-1 col-form-label form-control-sm text-right">Total Modal</label>
                <div class="col-md-2">
                    <input {{ $readonly }} type="text" class="number form-control form-control-sm" name="totalmodal" value="{{ $header->TOTAL_MODAL }}" id="totalmodal">
                </div>
                <div class="col-md-1"></div>
            </div>
            <div class="form-row px-2">
                <label class="col-md-1 col-form-label form-control-sm text-right">Total Pembelian</label>
                <div class="col-md-2">
                    <input readonly type="text" id="totalbeli" name="totalbeli" class="number form-control form-control-sm" readonly value="{{ $header->TOTAL_BELI }}">
                </div>
                <label class="col-md-1 col-form-label form-control-sm text-right">Total Penjualan</label>
                <div class="col-md-2">
                    <input readonly type="text" id="totaljual" name="totaljual" class="number form-control form-control-sm" readonly value="{{ $header->TOTAL_JUAL }}">
                </div>
                <label class="col-md-1 col-form-label form-control-sm text-right">Profit</label>
                <div class="col-md-2">
                    <input readonly type="text" id="profit" name="profit" class="number form-control form-control-sm" readonly value="{{ $header->PROFIT }}">
                </div>
            </div>
            <div class="form-row px-2">
                <label class="col-md-1 col-form-label form-control-sm text-right">Total Debet</label>
                <div class="col-md-2">
                    <input readonly type="text" id="totaldebet" name="totaldebet" class="number form-control form-control-sm" readonly value="{{ $header->TOTAL_DEBET }}">
                </div>
                <label class="col-md-1 col-form-label form-control-sm text-right">Total Kredit</label>
                <div class="col-md-2">
                    <input readonly type="text" id="totalkredit" name="totalkredit" class="number form-control form-control-sm" readonly value="{{ $header->TOTAL_KREDIT }}">
                </div>
                <label class="col-md-1 col-form-label form-control-sm text-right">Saldo</label>
                <div class="col-md-2">
                    <input readonly type="text" class="number form-control form-control-sm" name="saldo" value="{{ $header->SALDO }}" id="saldo">
                </div>
            </div>
            <div class="row mt-2">
                <div class="card col-sm-12 col-md-12 p-0">
                    <div class="card-body p-3">
                        <div class="form-row">
                            <div class="col primary-color text-white py-2 px-4">
                                Detail Pembelian
                            </div>
                            <div class="col primary-color text-white text-right p-2" style="text-decoration:underline">
                                @if($readonly == '')
                                <a href="#modaldetailbeli" data-toggle="modal" class="text-white" id="adddetailbeli">Tambah Detail</a>
                                @endif
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col mt-2">
                                <table width="100%" id="griddetailbeli" class="table">
                                    <thead>
                                        <tr>
                                            <th>Nama Barang</th>
                                            <th>Qty</th>
                                            <th>Satuan</th>
                                            <th>Nominal Beli</th>
                                            <th>Harga Satuan</th>
                                            @if($readonly == '')
                                            <th>Opsi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="card col-sm-12 col-md-12 p-0">
                    <div class="card-body p-3">
                        <div class="form-row">
                            <div class="col primary-color text-white py-2 px-4">
                                Detail Penjualan
                            </div>
                            <div class="col primary-color text-white text-right p-2" style="text-decoration:underline">
                                @if($readonly == '')
                                <a href="#modaldetail" data-toggle="modal" class="text-white" id="adddetail">Tambah Detail</a>
                                @endif
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col mt-2">
                                <table width="100%" id="griddetail" class="table">
                                    <thead>
                                        <tr>
                                            <th>Jns Dok</th>
                                            <th>No Inv</th>
                                            <th>Tgl Inv</th>
                                            <th>Customer</th>
                                            <th>Qty</th>
                                            <th>Harga Jual</th>
                                            <th>Nominal</th>
                                            <th>Payment</th>
                                            <th>Hutang</th>
                                            @if($readonly == '')
                                            <th>Opsi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="deletedetailbeli">
            <input type="hidden" name="deletedetailjual">
        </form>
    </div>
</div>
@endsection
@push('stylesheets_end')
    <link href="{{ asset('jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
@endpush
@push('scripts_end')
<script type="text/javascript" src="{{ asset('js/jquery.inputmask.bundle.js') }}"></script>
<script type="text/javascript" src="{{ asset('jquery-ui/jquery-ui.min.js') }}"></script>
<script>

var detail = @json($detailjual);
datadetail = JSON.parse(detail);
var detailbeli = @json($detailbeli);
datadetailbeli = JSON.parse(detailbeli);

$(function(){

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
$(".datepicker").datepicker({dateFormat: "dd-mm-yy"});
$(".number").inputmask("numeric", {
    radixPoint: ".",
    groupSeparator: ",",
    digits: 2,
    autoGroup: true,
    rightAlign: false,
    removeMaskOnSubmit: true,
    oncleared: function () { self.setValue(''); }
});

var tabel = $("#griddetail").DataTable({
    processing: false,
    serverSide: false,
    data: datadetail,
    dom: "t",
    pageLength: 1000,
    rowCallback: function(row, data)
    {
        $('td:eq(4)', row).html(parseFloat(data.QTY).formatMoney(2,"",",","."));
        $('td:eq(5)', row).html(parseFloat(data.HARGA).formatMoney(2,"",",","."));
        var total = parseFloat(data.QTY)*parseFloat(data.HARGA);
        $('td:eq(6)', row).html(total.formatMoney(2,"",",","."));
        $('td:eq(7)', row).html(parseFloat(data.TOTAL_PAYMENT).formatMoney(2,"",",","."));
        $('td:eq(8)', row).html((total - parseFloat(data.TOTAL_PAYMENT)).formatMoney(2,"",",","."));
        @if($readonly == '')
        $('td:eq(9)', row).html('<a href="#modaldetail" class="edit" data-toggle="modal" id="' + data.ID +
                                '"><i class="fa fa-edit"></i></a>' +
                                '&nbsp;&nbsp;<a class="del" id="' + data.ID + '"><i class="fa fa-trash"></i></a>'
                                );
        @endif
    },
    select: 'single',     // enable single row selection
    responsive: false,     // enable responsiveness,
    rowId: 0,
    columns: [
      { target: 0,
          data: "JENISDOKUMEN"
      },
      { target: 1,
          data: "NO_INV",
      },
      { target: 2,
          data: "TGL_INV"
      },
      { target: 3,
          data: "nama_customer"
      },
      { target: 4,
          data: "QTY"
      },
      { target: 5,
          data: "HARGA"
      },
      { target: 6,
          data: null
      },
      { target: 7,
          data: null
      },
      { target: 8,
          data: null
      },
      @if($readonly == '')
      { target: 9,
          data: null
      }
      @endif
     ],
})
var tabelbeli = $("#griddetailbeli").DataTable({
    processing: false,
    serverSide: false,
    data: datadetailbeli,
    dom: "t",
    pageLength: 1000,
    rowCallback: function(row, data)
    {
        $('td:eq(1)', row).html(parseFloat(data.QTY).formatMoney(2,"",",","."));
        $('td:eq(3)', row).html(parseFloat(data.NOMINAL).formatMoney(2,"",",","."));
        var qtybeli = parseFloat(data.QTY);
        $('td:eq(4)', row).html((qtybeli == null || qtybeli == 0 ? 0 : parseFloat(data.NOMINAL)/ qtybeli).formatMoney(2,"",",","."));
        @if($readonly == '')
        $('td:eq(5)', row).html('<a href="#modaldetailbeli" class="editbeli" data-toggle="modal" id="' + data.ID +
                                '"><i class="fa fa-edit"></i></a>' +
                                '&nbsp;&nbsp;<a class="delbeli" id="' + data.ID + '"><i class="fa fa-trash"></i></a>'
                                );
        @endif
    },
    select: 'single',     // enable single row selection
    responsive: false,     // enable responsiveness,
    rowId: 0,
    columns: [
      { target: 0,
          data: "NAMA_BARANG"
      },
      { target: 1,
          data: "QTY",
      },
      { target: 2,
          data: "satuan"
      },
      { target: 3,
          data: "NOMINAL"
      },
      { target: 4,
          data: null
      },
      @if($readonly == '')
      { target: 5,
          data: null
      }
      @endif
     ],
})
@if($readonly == '')
$("#btnsimpan").on("click", function(){
        var detailbeli = [];
        var rows = tabelbeli.rows().data();
        $(rows).each(function(index,elem){
            detailbeli.push(elem);
        })
        var detailjual = [];
        var rows = tabel.rows().data();
        $(rows).each(function(index,elem){
            detailjual.push(elem);
        })
        $(this).prop("disabled", true);
        $(".loader").show()
        $.ajax({
            data: {header: $("#transaksi").serialize(), _token: "{{ csrf_token() }}", detailbeli: detailbeli, detailjual: detailjual },
            url: "/transaksi/crud",
            type: "POST",
            success: function(msg) {
                if (typeof msg.error != 'undefined'){
                    $("#modal .modal-body").html(msg.error);
                    $("#modal").modal("show");
                    setTimeout(function(){
                        $("#modal").modal("hide");
                    }, 5000);
                }
                else {
                    $("#modal .modal-body").html("Penyimpanan berhasil");
                    $('#modal').on('hidden.bs.modal', function (e) {
                        if ($("#idtransaksi").val().trim() == ""){
                            var redirect = "/transaksi";
                            if (typeof msg.id != 'undefined'){
                                redirect = redirect + "/" +msg.id;
                            }
                            document.location.href = redirect;
                        }
                        else {
                            document.location.reload();
                        }
                    })
                    $("#modal").modal("show");
                    setTimeout(function(){
                        $("#modal").modal("hide");
                    }, 5000);
                }
            },
            complete: function(){
                $("#btnsimpan").prop("disabled", false);
                $(".loader").hide();
            }
        })
    /*}
    else {
        return false;
    }*/
})
$("#deletetrans").on("click", function(){
    $("#modal .btn-ok").removeClass("d-none");
    $("#modal .btn-close").html("Batal");
    $("#modal .modal-body").html("Apakah Anda ingin menghapus data ini?");
    $("#modal .btn-ok").html("Ya").on("click", function(){
        $.ajax({
            url: "/transaksi/delete",
            data: $("#formdelete").serialize(),
            type: "POST",
            success: function(msg) {
                $("#modal").modal("hide");
                $("#modal .btn-ok").addClass("d-none");
                if (typeof msg.error != 'undefined'){
                    $("#modal .modal-body").html(msg.error);
                    $("#modal").modal("show");
                    setTimeout(function(){
                        $("#modal").modal("hide");
                    }, 5000);
                }
                else {
                    $("#modal .modal-body").html("Data berhasil dihapus");
                    $("#modal").modal("show");
                    setTimeout(function(){
                        $("#modal").modal("hide");
                    }, 10000);
                    window.location.href = "/transaksi";
                }
            }
        })
    })
    $("#modal").modal("show");
})
function count_total(){
  var totaljual = 0;
  $("#griddetail tbody tr").each(function(index,elem){
      var td = $(elem).find("td:not(.dataTables_empty)").eq(6);
      var subtotal = td.length > 0 ? $(td).html() : "";
      totaljual += subtotal.trim() == "" ? 0 : parseFloat(subtotal.replace(/,/g,""));
  });
  var totalbeli = 0;
  $("#griddetailbeli tbody tr").each(function(index,elem){
      var td = $(elem).find("td:not(.dataTables_empty)").eq(3);
      var subtotal = td.length > 0 ? $(td).html() : "";
      totalbeli += subtotal.trim() == "" ? 0 : parseFloat(subtotal.replace(/,/g,""));
  });
  $("#totaljual").val(totaljual);
  $("#totalbeli").val(totalbeli);
  $("#profit").val(totaljual - totalbeli);
}
$("#savedetail").on("click", function(){
    $(this).prop("disabled", true);
    if ($("#noinv").val().trim() == ""){
        $("#modal .modal-body").html("No Inv Harus Diisi");
        $("#modal").modal("show");
        setTimeout(function(){
            $("#modal").modal("hide");
        }, 5000);
        $("#jenisdokumen").focus();
        return false;
    }
    var jenisdokumen_id = $("#jenisdokumen").val();
    var jenisdokumen = $("#jenisdokumen option:selected").html();
    var customer_id = $("#customer").val();
    var customer = $("#customer option:selected").html();

    var noinv = $("#noinv").val();
    var tglinv = $("#tglinv").val();
    var qty = $("#qtyjual").inputmask('unmaskedvalue');
    var harga = $("#hargajual").inputmask('unmaskedvalue');
    qty = qty.trim() == "" ? 0 : qty;
    harga = harga.trim() == "" ? 0 : harga;
    var act = $("#form").attr("act");
    if (act == "add"){
        tabel.row.add({NO_INV: noinv, TGL_INV: tglinv, JENISDOKUMEN_ID: jenisdokumen_id, JENISDOKUMEN: jenisdokumen, PEMBELI_ID: customer_id, nama_customer: customer, QTY: qty, HARGA: harga }).draw();
        $("#jenisdokumen").val("");
        $("#customer").val("");
        $("#alamatcustomer").html("");
        $("#noinv").val("");
        $("#tglinv").val("");
        $("#qtyjual").val("");
        $("#hargajual").val("");
        $("#nominaljual").val("");
        $("#jenisdokumen").focus();
    }
    else if (act == "edit"){
        var id = $("#iddetail").val();
        var idx = $("#idxdetail").val();
        tabel.row(idx).data({ID: id, NO_INV: noinv, TGL_INV: tglinv, JENISDOKUMEN_ID: jenisdokumen_id, JENISDOKUMEN: jenisdokumen, PEMBELI_ID: customer_id, nama_customer: customer, QTY: qty, HARGA: harga }).draw();
        $("#modaldetail").modal("hide");
    }
    count_total();
    $(this).prop("disabled", false);
});
$("#savedetailbeli").on("click", function(){
    $(this).prop("disabled", true);
    if ($("#namabarang").val().trim() == ""){
        $("#modal .modal-body").html("Nama Barang Harus Diisi");
        $("#modal").modal("show");
        setTimeout(function(){
            $("#modal").modal("hide");
        }, 5000);
        $("#namabarang").focus();
        return false;
    }
    var satuan_id = $("#satuan").val();
    var satuan = $("#satuan option:selected").html();

    var namabarang = $("#namabarang").val();
    var qty = $("#qtybeli").inputmask('unmaskedvalue');
    var nominal = $("#nominalbeli").inputmask('unmaskedvalue');
    qty = qty.trim() == "" ? 0 : qty;
    nominal = nominal.trim() == "" ? 0 : nominal;
    var act = $("#formbeli").attr("act");
    if (act == "add"){
        tabelbeli.row.add({NAMA_BARANG: namabarang, SATUAN_ID: satuan_id, satuan: satuan, QTY: qty, NOMINAL: nominal }).draw();
        $("#nama_barang").val("");
        $("#satuan").val("");
        $("#qty").val("");
        $("#nominalbeli").val("");
    }
    else if (act == "edit"){
        var id = $("#iddetailbeli").val();
        var idx = $("#idxdetailbeli").val();
        tabelbeli.row(idx).data({ID: id, NAMA_BARANG: namabarang, SATUAN_ID: satuan_id, satuan: satuan, QTY: qty, NOMINAL: nominal }).draw();
        $("#modaldetailbeli").modal("hide");
    }
    count_total();
    $(this).prop("disabled", false);
});
$("#adddetail").on("click", function(){
    $("#noinv").val("");
    $("#tglinv").val("");
    $("#jenisdokumen").val("");
    $("#customer").val("");
    $("#alamatcustomer").html("");
    $("#qtyjual").val("");
    $("#hargajual").val("");
    $("#nominaljual").val("");
    $("#modaldetail .modal-title").html("Tambah ");
    $("#jenisdokumen").focus();
    $("#form").attr("act","add");
})
$("#adddetailbeli").on("click", function(){
    $("#namabarang").val("");
    $("#satuan").val("");
    $("#hargabeli").val("");
    $("#qtybeli").val("");
    $("#nominalbeli").val("");
    $("#modaldetailbeli .modal-title").html("Tambah ");
    $("#namabarang").focus();
    $("#formbeli").attr("act","add");
})
$("body").on("click", ".edit", function(){
    var row = $(this).closest("tr");
    var index = tabel.row(row).index();
    var row = tabel.rows(index).data();
    $("#noinv").val(row[0].NO_INV);
    $("#tglinv").val(row[0].TGL_INV);
    $("#jenisdokumen").val(row[0].JENISDOKUMEN_ID);
    $("#customer").val(row[0].PEMBELI_ID);
    $("#alamatcustomer").html(row[0].alamat_customer);
    $("#qtyjual").val(row[0].QTY);
    $("#hargajual").val(row[0].HARGA);
    $("#nominaljual").val(parseFloat(row[0].QTY) * parseFloat(row[0].HARGA));
    $("#idxdetail").val(index);
    $("#iddetail").val(row[0].ID);
    $("#modaldetail .modal-title").html("Edit ");
    $("#form").attr("act","edit");
})
$("body").on("click", ".editbeli", function(){
    var row = $(this).closest("tr");
    var index = tabelbeli.row(row).index();
    var row = tabelbeli.rows(index).data();
    $("#namabarang").val(row[0].NAMA_BARANG);
    $("#satuan").val(row[0].SATUAN_ID);
    $("#qtybeli").val(row[0].QTY);
    var qtybeli = parseFloat(row[0].QTY);
    $("#hargabeli").val(qtybeli == null || qtybeli == 0 ? 0 : parseFloat(row[0].NOMINAL) / qtybeli );
    $("#nominalbeli").val(row[0].NOMINAL);
    $("#idxdetailbeli").val(index);
    $("#iddetailbeli").val(row[0].ID);
    $("#modaldetailbeli .modal-title").html("Edit ");
    $("#formbeli").attr("act","edit");
})
$("body").on("click", ".del", function(){
    var row = $(this).closest("tr");
    var id = tabel.row(row).data().ID;
    if (typeof id != 'undefined'){
        $("input[name='deletedetailjual'").val($("input[name='deletedetailjual'").val() + id + ";");
    }
    var index = tabel.row(row).remove().draw();
    count_total();
})
$("body").on("click", ".delbeli", function(){
    var row = $(this).closest("tr");
    var id = tabelbeli.row(row).data().ID;
    if (typeof id != 'undefined'){
        $("input[name='deletedetailbeli'").val($("input[name='deletedetailbeli'").val() + id + ";");
    }
    var index = tabelbeli.row(row).remove().draw();
    count_total();
})
$("#qtyjual,#hargajual").on("change", function(){
    var qty = $("#qtyjual").inputmask("unmaskedvalue");
    var harga = $("#hargajual").inputmask("unmaskedvalue");
    qty = qty.trim() == "" ? 0 : qty;
    harga = harga.trim() == "" ? 0 : harga;
    var total = parseFloat(qty) * parseFloat(harga);
    $("#nominaljual").val(total);
})
$("#qtybeli,#nominalbeli").on("change", function(){
    var qty = $("#qtybeli").inputmask("unmaskedvalue");
    var nominal = $("#nominalbeli").inputmask("unmaskedvalue");
    qty = parseFloat(qty.trim() == "" ? 0 : qty);
    nominal = parseFloat(nominal.trim() == "" ? 0 : nominal);
    var harga = qty == null || qty == 0 ? 0 : nominal / qty;
    $("#hargabeli").val(harga);
})
$("#customer").on("change", function(){
    var selected = $(this).find("option:selected");
    if ($(selected).val() != ""){
        $("#alamatcustomer").html($(selected).attr("alamat"));
    }
})
$('#modaldetail').on('shown.bs.modal', function (e) {
    $("#savedetail").removeClass("disabled");
    $('#jenisdokumen').focus();
})
$('#modaldetailbeli').on('shown.bs.modal', function (e) {
    $("#savedetailbeli").removeClass("disabled");
    $('#namabarang').focus();
})
@endif
})
</script>
@endpush
