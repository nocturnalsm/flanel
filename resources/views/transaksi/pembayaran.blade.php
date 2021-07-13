@extends('layouts.base')
@section('main')
<style>
    .error {display:none;font-size: 0.75rem;color: red};
</style>
<div class="modal fade" id="modaldetail" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h4 class="modal-title w-100 font-weight-bold"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mx-3">
                <form id="form" act="">
                    @csrf
                    <input {{ $readonly }} type="hidden" name="idxdetail" id="idxdetail">
                    <input {{ $readonly }} type="hidden" name="iddetail" id="iddetail">
                    <div class="form-row mb-1">
                        <label class="col-form-label col-md-3" for="kodebarang">No Job</label>
                        <div class="col-md-9">
                            <input {{ $readonly }} type="text" id="joborder" name="joborder" class="form-control form-control-sm validate">
                            <input {{ $readonly }} type="hidden" id="joborder_id" name="joborder_id">
                        </div>
                    </div>
                    <div class="form-row mb-1">
                        <label class="col-form-label col-md-3">No Dok</label>
                        <div class="col-md-9 pt-2">
                            <span id="formnodok"></span>
                        </div>
                    </div>
                    <div class="form-row mb-1">
                        <label class="col-form-label col-md-3" for="kodetransaksi">Kode Transaksi</label>
                        <div class="col-md-9">
                        <select class="form-control form-control-sm" id="kodetransaksi" name="kodetransaksi">
                            @foreach($kodetransaksi as $kode)
                            <option value="{{ $kode->KODETRANSAKSI_ID }}">{{ $kode->URAIAN }}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>
                    <div class="form-row mb-1">
                        <label class="col-form-label col-md-3" for="nominal">Nominal</label>
                        <div class="col-md-9">
                            <input {{ $readonly }} type="text" id="nominal" name="nominal" class="number form-control form-control-sm validate">
                        </div>
                    </div>
                    <div class="form-row mb-1">
                        <label class="col-form-label col-md-3" for="total">Debet / Kredit</label>
                        <div class="col-md-9 mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" checked type="radio" name="dk" id="dk" value="D">
                                <label class="form-check-label" for="dk">Debet</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="dk" id="dk" value="K">
                                <label class="form-check-label" for="dk">Kredit</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <a id="savedetail" class="btn btn-primary">Simpan</a>
                <a class="btn btn-danger" data-dismiss="modal">Batal</a>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="card col-md-12 p-0">
        <div class="card-header font-weight-bold">
            <div class="row">
                <div class="col-md-4 py-0 pl-4 mt-1">
                    Form Transaksi Pembayaran
                </div>
                @can('pembayaran.transaksi')
                <div class="col-md-8 py-0 pr-4 text-right">
                    <button type="button" id="btnsimpan" class="btn btn-primary btn-sm m-0">Simpan</button>&nbsp;
                    <a href="/" class="btn btn-default btn-sm m-0">Batal</a>&nbsp;
                    @if(isset($header->ID) && $header->ID != null)
                    <a id="deletetransaksi" class="btn btn-warning btn-sm m-0" data-dismiss="modal">Hapus</a>
                    @endif
                </div>
                @endcan
            </div>
        </div>
        <form id="transaksi" autocomplete="off">
        <div class="card-body">
            <input {{ $readonly }} type="hidden" value="{{ $header->ID }}" id="idtransaksi" name="idtransaksi">
            <div class="row px-2">
                <div class="col-md-6 pt-0 col-sm-12">
                    <div class="row">
                        <div class="card col-md-12 p-0 mb-2">
                            <div class="card-body p-3">
                                <div class="form-row px-2 pb-0">
                                    <label class="col-md-3 col-form-label form-control-sm">Tanggal</label>
                                    <div class="col-md-3">
                                        <input {{ $readonly }} autocomplete="off" type="text" class="datepicker{{ $readonly == 'readonly' ? '-readonly' : '' }} form-control form-control-sm" name="tanggal" value="{{ $header->TANGGAL }}" id="tanggal">
                                    </div>
                                </div>
                                <div class="form-row px-2">
                                    <label class="col-md-3 col-form-label form-control-sm">Rekening</label>
                                    <div class="col-md-6">
                                        <select {{ $readonly == 'readonly' ? 'disabled' : '' }} class="form-control form-control-sm" id="rekening" name="rekening" value="{{ $header->REKENING_ID }}">
                                            <option value=""></option>
                                            @foreach($rekening as $rek)
                                            <option @if($header->REKENING_ID == $rek->REKENING_ID) selected @endif value="{{ $rek->REKENING_ID }}">{{ $rek->NAMA }} - {{ $rek->NO_REKENING }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row px-2">
                                    <label class="col-md-3 col-form-label form-control-sm">Total Debet</label>
                                    <div class="col-md-3">
                                        <input type="text" id="totaldebet" name="totaldebet" class="number form-control form-control-sm" readonly value="{{ $header->TOTAL_DEBET }}">
                                    </div>
                                </div>
                                <div class="form-row px-2">
                                    <label class="col-md-3 col-form-label form-control-sm">Total Kredit</label>
                                    <div class="col-md-3">
                                        <input type="text" id="totalkredit" name="totalkredit" class="number form-control form-control-sm" readonly value="{{ $header->TOTAL_KREDIT }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row px-2">
                <div class="col-md-12">
                    <div class="row mb-2">
                        <div class="card col-md-12 p-0">
                            <div class="card-body p-3">
                                <div class="form-row">
                                    <div class="col primary-color text-white py-2 px-4">
                                        Detail Pembayaran
                                    </div>
                                    @can('pembayaran.transaksi')
                                    <div class="col primary-color text-white text-right p-2" style="text-decoration:underline">
                                        <a href="#modaldetail" data-toggle="modal" class="text-white" id="adddetail">Tambah Detail</a>
                                    </div>
                                    @endcan
                                </div>
                                <div class="form-row">
                                    <div class="col mt-2">
                                        <table width="100%" id="griddetail" class="table">
                                            <thead>
                                                <tr>
                                                    <th>No Job</th>
                                                    <th>No Dok</th>
                                                    <th>Kode Trans</th>
                                                    <th>Nominal</th>
                                                    <th>D/K</th>
                                                    @can('pembayaran.transaksi')
                                                    <th>Opsi</th>
                                                    @endcan
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    var detail = @json($detail);
    datadetail = JSON.parse(detail);
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
        });
        var tabel = $("#griddetail").DataTable({
            processing: false,
            serverSide: false,
            data: datadetail,
            dom: "t",
            rowCallback: function(row, data)
            {
                $(row).attr("id-transaksi", data.ID);
                $('td:eq(3)', row).html(parseFloat(data.NOMINAL).formatMoney(2,"",",","."));
                @can('pembayaran.transaksi')
                $('td:eq(5)', row).html('<a href="#modaldetail" class="edit" data-toggle="modal" id="' + data.ID +
                                        '"><i class="fa fa-edit"></i></a>' +
                                        '&nbsp;&nbsp;<a class="del" id="' + data.ID + '"><i class="fa fa-trash"></i></a>'
                                        );
                @endcan
            },
            select: 'single',     // enable single row selection
            responsive: false,     // enable responsiveness,
            rowId: 0,
            columns: [{
                target: 0,
                data: "JOB_ORDER"
            },
            { target: 1,
                data: "NO_DOK"
            },
            { target: 2,
                data: "TRANSAKSI"
            },
            { target: 3,
                data: "NOMINAL"
            },
            { target: 4,
                data: "DK"
            },
            @can('pembayaran.transaksi')
            { target: 5,
                data: null
            }
            @endcan
            ],
        })
        @can('pembayaran.transaksi')
        $("body").on("change","#joborder", function(){
            var joborder = $(this).val().trim();
            $.ajax({
                method: "GET",
                url: "/transaksi/searchjob",
                data: {job: joborder},
                success: function(response){
                    if (typeof response.error == 'undefined'){
                        $("#formnodok").html(response.NO_DOK);
                        $("#joborder_id").val(response.ID);
                    }
                    else {
                        $("#modal .modal-body").html(response.error);
                        $("#formnodok").html("");
                        $("#joborder_id").val("");
                        $("#modal").modal("show");
                        setTimeout(function(){
                            $("#modal").modal("hide");
                        }, 5000);
                    }
                }
            })
        })
        function count_total(){
          var totaldebet = 0;
          var totalkredit = 0;
          $("#griddetail tbody tr").each(function(index,elem){
              var dk = $(elem).find("td:eq(4)").html().trim();
              var nominal = $(elem).find("td:eq(3)").html().trim();
              if (dk == "D"){
                  totaldebet += nominal == "" ? 0 : parseFloat(nominal.replace(/,/g,""));
              }
              else if (dk == "K"){
                  totalkredit += nominal == "" ? 0 : parseFloat(nominal.replace(/,/g,""))
              }
          });
          $("#totaldebet").val(totaldebet);
          $("#totalkredit").val(totalkredit);
        }
        $('#modaldetail').on('shown.bs.modal', function () {
            $('#joborder').focus();
        })
        $("#savedetail").on("click", function(){
            var joborder= $("#joborder").val();
            var joborder_id = $("#joborder_id").val();
            var nodok = $("#formnodok").html();
            var kodetransaksi_id = $("#kodetransaksi option:selected").val();
            var transaksi = $("#kodetransaksi option:selected").html();
            if (!transaksi){
                transaksi = "";
                transaksi_id = "";
            }
            var nominal = $("#nominal").inputmask('unmaskedvalue');
            var dk = $("input[name='dk']:checked").val();
            var act = $("#form").attr("act");

            if (act == "add"){
                tabel.row.add({JOB_ORDER_ID: joborder_id, JOB_ORDER: joborder, NO_DOK: nodok, NOMINAL: nominal, KODE_TRANSAKSI: kodetransaksi_id, TRANSAKSI: transaksi, DK: dk}).draw();
                $("#formnodok").html("");
                $("#joborder").val("");
                $("#joborder_id").val("");
                $("#nominal").val("");
                $("#kodetransaksi").val("");
                $("#joborder").focus();
            }
            else if (act == "edit"){
                var id = $("#iddetail").val();
                var idx = $("#idxdetail").val();
                tabel.row(idx).data({ID: id, JOB_ORDER_ID: joborder_id, JOB_ORDER: joborder, NO_DOK: nodok, NOMINAL: nominal, KODE_TRANSAKSI: kodetransaksi_id, TRANSAKSI: transaksi, DK: dk}).draw();
                $("#modaldetail").modal("hide");
            }
            count_total();
        });

        $("#adddetail").on("click", function(){
            $("#formnodok").html("");
            $("#joborder").val("");
            $("#joborder_id").val("");
            $("#nominal").val("");
            $("#kodetransaksi").val("");
            $("input[name='dk'][value='D']").prop("checked", true);
            $("#joborder").focus();
            $("#modaldetail .modal-title").html("Tambah ");
            $("#form").attr("act","add");
        })
        $("body").on("click", ".edit", function(){
            var row = $(this).closest("tr");
            var index = tabel.row(row).index();
            var row = tabel.rows(index).data();
            $("#formnodok").html(row[0].NO_DOK);
            $("#joborder").val(row[0].JOB_ORDER);
            $("#joborder_id").val(row[0].JOB_ORDER_ID);
            $("#kodetransaksi").val(row[0].KODE_TRANSAKSI);
            $("#nominal").val(row[0].NOMINAL);
            $("input[name='dk'][value='" + row[0].DK + "']").prop("checked", true);
            $("#idxdetail").val(index);
            $("#iddetail").val(row[0].ID);
            $("#modaldetail .modal-title").html("Edit ");
            $("#form").attr("act","edit");
        })
        $("body").on("click", ".del", function(){
            var row = $(this).closest("tr");
            var id = tabel.row(row).data().ID;
            if (typeof id != 'undefined'){
                $("input[name='deletedetail'").val($("input[name='deletedetail'").val() + id + ";");
            }
            var index = tabel.row(row).remove().draw();
            count_total();
        })
        $("#btnsimpan").on("click", function(){

                //if (validate()){
                var detail = [];
                var rows = tabel.rows().data();
                var total = 0;
                $(rows).each(function(index,elem){
                    detail.push(elem);
                })
                $(this).prop("disabled", true);
                $(".loader").show()
                $.ajax({
                    url: "/transaksi/crud",
                    data: {_token: "{{ csrf_token() }}", type: "pembayaran", header: $("#transaksi").serialize(), detail: detail},
                    type: "POST",
                    cache: false,
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
                            $("#modal").on("hidden.bs.modal", function(){
                                window.location.reload();
                            });
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
        @if(isset($header) && $header->ID != null)
        $("a#deletetransaksi").on("click", function(){
            $("#modal .btn-ok").removeClass("d-none");
            $("#modal .btn-close").html("Batal");
            $("#modal .modal-body").html("Apakah Anda ingin menghapus data ini?");
            $("#modal .btn-ok").html("Ya").on("click", function(){
                $.ajax({
                    url: "/transaksi/crud",
                    data: {_token: "{{ csrf_token() }}", type: "pembayaran", delete: "{{ $header->ID}}"},
                    type: "POST",
                    success: function(msg){
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
                            window.location.href = "/transaksi/pembayaran";
                        }
                    }
                })
            });
            $("#modal").modal("show");
        });
        @endif
        @endcan
    })
</script>
@endpush
