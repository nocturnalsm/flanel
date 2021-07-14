<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Bank;
use App\Models\Rekening;
use App\Models\Customer;
use App\Models\Pembayaran;

class Transaksi extends Model
{
    protected $table  = 'job_order';
    protected $primaryKey = 'ID';
    protected $guarded = ['ID'];
    public $timestamps = false;

    public static function getTransaksi($id, $includeDetail = true)
    {
        if ($id == ""){
            $header = new Transaksi;
            $header->TGL_JOB = Date("d-m-Y");
            $header->JOB_ORDER = "(Baru)";
        }
        else {
            $header = Transaksi::where("ID", $id);
            if (!$header->exists()){
                return false;
            }
            $totalBeli = DB::table("pembelian_detail")
                        ->where("ID_HEADER", $id)
                        ->sum("NOMINAL");
            $totalJual = DB::table("penjualan_detail")
                         ->where("ID_HEADER", $id)
                         ->sum(DB::raw("QTY * HARGA"));
            $totalDebet = DB::table(DB::raw("pembayaran_detail pd"))
                            ->join(DB::raw("pembayaran p"), "pd.ID_HEADER","=","p.ID")
                            ->where("JOB_ORDER_ID", $id)
                            ->where("DK", "D")
                            ->sum("NOMINAL");
            $totalKredit = DB::table(DB::raw("pembayaran_detail pd"))
                            ->join(DB::raw("pembayaran p"), "pd.ID_HEADER","=","p.ID")
                            ->where("JOB_ORDER_ID", $id)
                            ->where("DK", "K")
                            ->sum("NOMINAL");

            $header = $header->first();
            $header->TOTAL_BELI = $totalBeli;
            $header->TOTAL_JUAL = $totalJual;
            $header->PROFIT = $totalJual - $totalBeli;
            $header->TOTAL_KREDIT = $totalKredit;
            $header->TOTAL_DEBET = $totalDebet;
            $header->SALDO = $totalDebet - $totalKredit;
        }
        if ($includeDetail){
            $detailBeli = DB::table(DB::raw("pembelian_detail pd"))
                        ->select("pd.*", "st.satuan")
                        ->leftJoin(DB::raw("satuan st"), function($join){
                            $join->on("st.id","=", "pd.SATUAN_ID");
                        })
                        ->where("pd.ID_HEADER", $id)->get();
            $detailJual = DB::table(DB::raw("penjualan_detail pd"))
                            ->select("pd.*", "pb.nama_customer", "pb.alamat_customer",
                                     DB::raw("jd.URAIAN AS JENISDOKUMEN"),
                                     DB::raw("IFNULL(totalbayar.TOTAL,0) AS TOTAL_PAYMENT"))
                            ->leftJoin(DB::raw("ref_jenis_dokumen jd"), "pd.JENISDOKUMEN_ID","=","jd.JENISDOKUMEN_ID")
                            ->leftJoin(DB::raw("tb_customer pb"), function($join){
                                $join->on("pb.id_customer","=","pd.PEMBELI_ID");
                            })
                            ->leftJoinSub(
                                DB::table(DB::raw("pembayaran_detail pd"))
                                  ->join(DB::raw("pembayaran p"), "pd.ID_HEADER","=","p.ID")
                                  ->where(DB::raw("NO_INV_ID IS NOT NULL AND NO_INV_ID <> ''"))
                                  ->select("NO_INV_ID", DB::raw("SUM(NOMINAL) AS TOTAL"))
                                  ->groupBy("NO_INV_ID"),
                                'totalbayar', function($join){
                                    $join->on("pd.ID","=","totalbayar.NO_INV_ID");
                                }
                             )
                            ->where("pd.ID_HEADER", $id)->get();
        }
        $header->TGL_JOB = $header->TGL_JOB == "" ? "" : Date("d-m-Y", strtotime($header->TGL_JOB));
        $header->TGL_TIBA = $header->TGL_TIBA == "" ? "" : Date("d-m-Y", strtotime($header->TGL_TIBA));
        return Array("header" => $header,
                     "detailBeli" => isset($detailBeli) ? $detailBeli : [],
                     "detailJual" => isset($detailJual) ? $detailJual : []);
    }
    public static function saveTransaksi($action, $header, $detailBeli, $detailJual){

        $arrHeader = Array("JENIS_DOK" => trim($header["jenisdokumen"]) == "" ? NULL : $header["jenisdokumen"],
						               "TGL_JOB" => trim($header["tgljob"]) == "" ? NULL : Date("Y-m-d", strtotime($header["tgljob"])),
                           "TGL_TIBA" => trim($header["tgltiba"]) == "" ? NULL : Date("Y-m-d", strtotime($header["tgltiba"])),
                           "TOTAL_MODAL" => str_replace(",","", $header["totalmodal"])
                          );

        if ($action == "insert"){
            $number = 0;
            $maxNumber = Transaksi::select("JOB_ORDER")->orderBy("JOB_ORDER", "DESC")->take(1);
            if ($maxNumber->exists()){
                $number = intval(str_replace("JO","", $maxNumber->first()->JOB_ORDER));
            }
            $number += 1;
            $arrHeader["JOB_ORDER"] = "JO" .str_pad($number, 6, "0", STR_PAD_LEFT);
            $idtransaksi = Transaksi::insertGetId($arrHeader);

            $arrDetailBeli = Array();
            if (is_array($detailBeli) && count($detailBeli) > 0){
                foreach ($detailBeli as $item){
                    $arrDetailBeli[] = Array("ID_HEADER" => $idtransaksi,
                                            "NAMA_BARANG" => trim($item["NAMA_BARANG"]),
                                            "NOMINAL" => $item["NOMINAL"] != "" ? str_replace(",","",$item["NOMINAL"]) : 0,
                                            "QTY" => $item["QTY"] != "" ? str_replace(",","",$item["QTY"]) : 0,
                                            "SATUAN_ID" => $item["SATUAN_ID"]
                                );
                }
            }
            if (count($arrDetailBeli) > 0){
                DB::table("pembelian_detail")
                    ->insert($arrDetailBeli);
            }
            $arrDetailJual = Array();
            if (is_array($detailJual) && count($detailJual) > 0){
                foreach ($detailJual as $item){
                    $arrDetailJual[] = Array("ID_HEADER" => $idtransaksi,
                                            "NO_INV" => trim($item["NO_INV"]),
                                            "TGL_INV" => trim($item["TGL_INV"]) == "" ? NULL : Date("Y-m-d", strtotime($item["TGL_INV"])),
                                            "HARGA" => $item["HARGA"] != "" ? str_replace(",","",$item["HARGA"]) : 0,
                                            "QTY" => $item["QTY"] != "" ? str_replace(",","",$item["QTY"]) : 0,
                                            "JENISDOKUMEN_ID" => $item["JENISDOKUMEN_ID"],
                                            "PEMBELI_ID" => $item["PEMBELI_ID"]
                                );
                }
            }
            if (count($arrDetailJual) > 0){
                DB::table("penjualan_detail")
                    ->insert($arrDetailJual);
            }
            return response()->json(["id" => $idtransaksi]);
        }
        else if ($action == "update"){
            $idtransaksi = $header["idtransaksi"];
            $data = Transaksi::where("ID", $idtransaksi)
                              ->update($arrHeader);

            if (is_array($detailBeli) && count($detailBeli) > 0){
                foreach ($detailBeli as $item){
                    $arrDetailBeli = Array("NAMA_BARANG" => trim($item["NAMA_BARANG"]),
                                           "NOMINAL" => $item["NOMINAL"] != "" ? str_replace(",","",$item["NOMINAL"]) : 0,
                                           "QTY" => $item["QTY"] != "" ? str_replace(",","",$item["QTY"]) : 0,
                                           "SATUAN_ID" => $item["SATUAN_ID"]
                              );
                    if (!isset($item["ID"])
                        || $item["ID"] == ""){
                        $arrDetailBeli["ID_HEADER"] = $idtransaksi;
                        $insertDetailBeli[] = $arrDetailBeli;
                    }
                    else {
                        DB::table("pembelian_detail")
                            ->where("ID", $item["ID"])
                            ->update($arrDetailBeli);
                    }
                }
            }
            if (isset($insertDetailBeli) && count($insertDetailBeli) > 0){
                DB::table("pembelian_detail")
                    ->insert($insertDetailBeli);
            }
            if ($header["deletedetailbeli"] != ""){
                $iddelete = explode(";", $header["deletedetailbeli"]);
                foreach ($iddelete as $iddel){
                    if ($iddel != ""){
                        DB::table("pembelian_detail")
                            ->where("ID", $iddel)
                            ->delete();
                    }
                }
            }
            if (is_array($detailJual) && count($detailJual) > 0){
                foreach ($detailJual as $item){
                    $arrDetailJual = Array("NO_INV" => trim($item["NO_INV"]),
                                           "TGL_INV" => trim($item["TGL_INV"]) == "" ? NULL : Date("Y-m-d", strtotime($item["TGL_INV"])),
                                           "HARGA" => $item["HARGA"] != "" ? str_replace(",","",$item["HARGA"]) : 0,
                                           "QTY" => $item["QTY"] != "" ? str_replace(",","",$item["QTY"]) : 0,
                                           "JENISDOKUMEN_ID" => $item["JENISDOKUMEN_ID"],
                                           "PEMBELI_ID" => $item["PEMBELI_ID"]
                              );
                    if (!isset($item["ID"])
                        || $item["ID"] == ""){
                        $arrDetailJual["ID_HEADER"] = $idtransaksi;
                        $insertDetailJual[] = $arrDetailJual;
                    }
                    else {
                        DB::table("penjualan_detail")
                            ->where("ID", $item["ID"])
                            ->update($arrDetailJual);
                    }
                }
            }
            if (isset($insertDetailJual) && count($insertDetailJual) > 0){
                DB::table("penjualan_detail")
                    ->insert($insertDetailJual);
            }
            if ($header["deletedetailjual"] != ""){
                $iddelete = explode(";", $header["deletedetailjual"]);
                foreach ($iddelete as $iddel){
                    if ($iddel != ""){
                        DB::table("penjualan_detail")
                            ->where("ID", $iddel)
                            ->delete();
                    }
                }
            }
            return response()->json(["id" => $idtransaksi]);
        }
    }
    public static function deleteTransaksi($id)
    {
        Transaksi::where("ID", $id)->delete();
        DB::table("pembelian_detail")
            ->where("ID_HEADER", $id)
            ->delete();
        DB::table("penjualan_detail")
            ->where("ID_HEADER", $id)
            ->delete();
    }
    public static function deletePembayaran($id)
    {
        Pembayaran::where("ID", $id)->delete();
        DB::table("pembayaran_detail")
            ->where("ID_HEADER", $id)
            ->delete();
    }
    public static function browse($customer, $kategori1, $isikategori1, $kategori2, $dari2, $sampai2)
    {
        $array1 = Array("No Job" => "JOB_ORDER");
        $array2 = Array("Tanggal Tiba" => "TGL_TIBA",
                        "Tanggal Job" => "TGL_JOB");
        $where = " 1 = 1";
        if ($kategori1 != ""){
            if (trim($isikategori1) == ""){
                $where  .=  " AND (" .$array1[$kategori1] ." IS NULL OR " .$array1[$kategori1] ." = '')";
            }
            else {
                $where  .=  " AND (" .$array1[$kategori1] ." LIKE '%" .$isikategori1 ."%')";
            }

        }
        if ($kategori2 != ""){
            if (trim($dari2) == "" && trim($sampai2) == ""){
                $where  .=  " AND (" .$array2[$kategori2] ." IS NULL OR " .$array2[$kategori2] ." = '')";
            }
            else {
                if (trim($dari2) == ""){
                    $dari2 = "0000-00-00";
                }
                if (trim($sampai2) == ""){
                    $sampai2 = "9999-99-99";
                }
                $where  .=  " AND (" .$array2[$kategori2] ." BETWEEN '" .Date("Y-m-d", strtotime($dari2)) ."'
                                            AND '" .Date("Y-m-d", strtotime($sampai2)) ."')";
            }
        }

        $totalJual = DB::table("penjualan_detail")
                       ->select("ID_HEADER",DB::raw("SUM(QTY*HARGA) AS TOTALJUAL"))
                       ->groupBy("ID_HEADER");

        $totalBeli = DB::table("pembelian_detail")
                      ->select("ID_HEADER",DB::raw("SUM(NOMINAL) AS TOTALBELI"))
                      ->groupBy("ID_HEADER");

        $totalDebet = DB::table("pembayaran_detail")
                        ->where("DK", "D")
                        ->select("JOB_ORDER_ID", DB::raw("SUM(NOMINAL) AS TOTALDEBET"))
                        ->groupBy("JOB_ORDER_ID");

        $totalKredit = DB::table("pembayaran_detail")
                        ->where("DK", "K")
                        ->select("JOB_ORDER_ID", DB::raw("SUM(NOMINAL) AS TOTALKREDIT"))
                        ->groupBy("JOB_ORDER_ID");

        $data = DB::table(DB::raw("job_order h"))
                    ->selectRaw("h.ID, JOB_ORDER, TOTAL_MODAL,"
                            ."IFNULL(TOTALBELI,0) AS TOTAL_BELI, "
                            ."IFNULL(TOTALJUAL,0) AS TOTAL_JUAL, "
                            ."IFNULL(TOTALJUAL,0) - IFNULL(TOTALBELI,0) AS PROFIT, "
                            ."IFNULL(TOTALDEBET,0) AS TOTAL_DEBET, "
                            ."IFNULL(TOTALKREDIT,0) AS TOTAL_KREDIT, "
                            ."IFNULL(TOTALDEBET,0) - IFNULL(TOTALKREDIT,0) AS SALDO, "
                            ."IFNULL(DATE_FORMAT(TGL_TIBA, '%d-%m-%Y'),'') AS TGL_TIBA,"
                            ."IFNULL(DATE_FORMAT(TGL_JOB, '%d-%m-%Y'), '') AS TGL_JOB")
                    ->leftJoinSub($totalJual, "totjual", "totjual.ID_HEADER","=","h.ID")
                    ->leftJoinSub($totalBeli, "totbeli", "totbeli.ID_HEADER","=","h.ID")
                    ->leftJoinSub($totalDebet, "totdebet", "totdebet.JOB_ORDER_ID","=","h.ID")
                    ->leftJoinSub($totalKredit, "totkredit", "totkredit.JOB_ORDER_ID","=","h.ID")
                    ->orderBy("JOB_ORDER");
        if (trim($where) != ""){
            $data = $data->whereRaw($where);
        }
        if (trim($customer) != ""){
            $data = $data->whereExists(function($query) use ($customer){
                $query->select("pd.ID_HEADER")
                      ->from(DB::raw("penjualan_detail pd"))
                      ->whereRaw("h.ID = pd.ID_HEADER")
                      ->where(DB::raw("pd.PEMBELI_ID"), trim($customer));
            });
        }
        return $data->get();
    }
    public static function arusKas($customer, $kategori1, $isikategori1, $kategori2, $dari2, $sampai2)
    {
        $array1 = Array("No Job" => "JOB_ORDER", "No Dok" => "NO_DOK");
        $array2 = Array("Tanggal Transaksi" => "TANGGAL",
                        "Tanggal Job" => "TGL_JOB");
        $where = " 1 = 1";
        if ($kategori1 != ""){
            if (trim($isikategori1) == ""){
                $where  .=  " AND (" .$array1[$kategori1] ." IS NULL OR " .$array1[$kategori1] ." = '')";
            }
            else {
                $where  .=  " AND (" .$array1[$kategori1] ." LIKE '%" .$isikategori1 ."%')";
            }

        }
        if ($kategori2 != ""){
            if (trim($dari2) == "" && trim($sampai2) == ""){
                $where  .=  " AND (" .$array2[$kategori2] ." IS NULL OR " .$array2[$kategori2] ." = '')";
            }
            else {
                if (trim($dari2) == ""){
                    $dari2 = "0000-00-00";
                }
                if (trim($sampai2) == ""){
                    $sampai2 = "9999-99-99";
                }
                $where  .=  " AND (" .$array2[$kategori2] ." BETWEEN '" .Date("Y-m-d", strtotime($dari2)) ."'
                                            AND '" .Date("Y-m-d", strtotime($sampai2)) ."')";
            }
        }
        if (trim($customer) != ""){
            $where .= " AND CUSTOMER = '" .$customer ."'";
        }

        $data = DB::table(DB::raw("pembayaran_detail d"))
                    ->selectRaw("p.ID, JOB_ORDER, NO_DOK, t.URAIAN AS TRANSAKSI, DK, NOMINAL,"
                            ."IFNULL(DATE_FORMAT(TANGGAL, '%d-%m-%Y'),'') AS TANGGAL,"
                            ."IFNULL(DATE_FORMAT(TGL_JOB, '%d-%m-%Y'), '') AS TGL_JOB")
                    ->join(DB::raw("pembayaran p"), "p.ID","=","d.ID_HEADER")
                    ->join(DB::raw("job_order h"), "h.ID", "=", "d.JOB_ORDER_ID")
                    ->join(DB::raw("ref_kode_transaksi t"), "t.KODETRANSAKSI_ID", "=", "d.KODE_TRANSAKSI")
                    ->join(DB::raw("tb_customer i"), "h.CUSTOMER", "=", "i.id_customer")
                    ->orderBy("TANGGAL");
        if (trim($where) != ""){
            $data = $data->whereRaw($where);
        }
        return $data->get();
    }
    public static function getPembayaran($id = "")
    {
        if ($id == ""){
            $header = new Pembayaran;
            $detail = [];
        }
        else {
            $header = DB::table(DB::raw("pembayaran h"))
                        ->selectRaw("h.*, (SELECT IFNULL(SUM(NOMINAL),0) FROM pembayaran_detail d "
                                   ."WHERE d.ID_HEADER = h.ID AND DK = 'D') AS TOTAL_DEBET,"
                                   ."(SELECT IFNULL(SUM(NOMINAL),0) FROM pembayaran_detail d "
                                   ."WHERE d.ID_HEADER = h.ID AND DK = 'K') AS TOTAL_KREDIT")
                        ->leftJoin(DB::raw("rekening rek"), "h.REKENING_ID","=", "rek.REKENING_ID")
                        ->where("id", $id);
            if ($header->exists()){
                $header = $header->first();
            }
            else {
                return false;
            }
            $detail = DB::table(DB::raw("pembayaran_detail db"))
                        ->selectRaw("db.*, r.URAIAN as TRANSAKSI, h.JOB_ORDER, h.NO_DOK")
                        ->join(DB::raw("job_order h"), "h.ID", "=", "db.JOB_ORDER_ID")
                        ->leftJoin(DB::raw("ref_kode_transaksi r"), "db.KODE_TRANSAKSI", "=", "r.KODETRANSAKSI_ID")
                        ->where("db.ID_HEADER", $id)
                        ->get();
        }
        if ($header){
            $header->TANGGAL = $header->TANGGAL == "" ? "" : Date("d-m-Y", strtotime($header->TANGGAL));
        }
        return Array("header" => $header, "detail" => $detail);
    }
    public static function savePembayaran($header, $detail)
    {
        $arrHeader = Array("TANGGAL" => trim($header["tanggal"]) == "" ? Date("Y-m-d") : Date("Y-m-d", strtotime($header["tanggal"])),
                           "REKENING_ID" => $header["rekening"]
                         );

        if (trim($header["idtransaksi"]) == ""){
            $idtransaksi = DB::table("pembayaran")->insertGetId($arrHeader);
        }
        else {
            $idtransaksi = $header["idtransaksi"];
            DB::table("pembayaran")->where("ID", $idtransaksi)->update($arrHeader);
            DB::table("pembayaran_detail")->where("ID_HEADER", $idtransaksi)->delete();
        }
        $arrDetail = Array();
        if (is_array($detail) && count($detail) > 0){
            foreach ($detail as $item){
                $arrDetail[] = Array("ID_HEADER" => $idtransaksi,
                                    "JOB_ORDER_ID" => $item["JOB_ORDER_ID"],
                                    "KODE_TRANSAKSI" => $item["KODE_TRANSAKSI"],
                                    "NOMINAL" => $item["NOMINAL"] != "" ? str_replace(",","",$item["NOMINAL"]) : 0,
                                    "DK" => $item["DK"]
                                    );
            }
            DB::table("pembayaran_detail")->insert($arrDetail);
        }
    }
    public static function getFiles($id, $type = 0)
    {
        $dtFiles = DB::table("tbl_files")
                     ->selectRaw("tbl_files.*, jenisfile.JENIS")
                     ->leftJoin("jenisfile", "tbl_files.JENISFILE_ID", "=", "jenisfile.ID")
                     ->where("ID_HEADER", $id)
                     ->where("AKTIF", 'Y')
                     ->where("TYPE", $type);
        return $dtFiles->get();
    }
    public static function getMaxFileId($prefix)
    {
        $data = DB::table("tbl_files")
                    ->selectRaw("MAX(ID) as MAX")
                    ->whereRaw("ID LIKE '$prefix%'");
        if ($data->count() > 0){
            return $data->first()->MAX;
        }
        else {
            return false;
        }
    }
    public static function saveFile($realname, $extension, $type = 0)
    {
        $timestamp = Date("YmdHis");
        $id = Transaksi::getMaxFileId($timestamp);
        if ($id != false){
            $max = str_pad(intval(substr($id,14)) + 1,3,"0",STR_PAD_LEFT);
        }
        else {
            $max = '001';
        }
        $id = $timestamp .$max;
        $array = ["ID" => $id, "FILENAME" => $id ."." .$extension, "FILEREALNAME" => $realname, "TYPE" => $type];
        DB::table("tbl_files")->insert($array);
        return $id;
    }
    public static function deleteFile($id)
    {
        $dtFile = DB::table("tbl_files")->select("SELECT FILENAME")
                    ->where("ID", $id);
        if ($dtFile->count() > 0){
            $filename = storage_path() ."/uploads/" .$dtFile->first()->FILENAME;
            unlink($filename);
            DB::table("tbl_files")->where("ID", $id)->delete();
        }
    }
}
