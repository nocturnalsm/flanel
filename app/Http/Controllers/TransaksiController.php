<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use DB;
use DataTable;
use App\Models\Transaksi;
use App\Models\JenisDokumen;
use App\Models\Rekening;
use App\Models\KodeTransaksi;
use App\Models\Satuan;
use App\Models\Customer;
use App\User;

class TransaksiController extends Controller {

	public function rekamData(Request $request, $id = "")
	{
    $breadcrumb[] = Array("link" => "/", "text" => "Home");
		$breadcrumb[] = Array("text" => "Perekaman Data");

		$dtJenisDokumen = JenisDokumen::get();
		$dtCustomer = Customer::select("id_customer","nama_customer","alamat_customer")->get();
		$dtSatuan = Satuan::get();

		$dtTransaksi = Array();
		$dtTransaksi = Transaksi::getTransaksi($id);

		if ($id != ""){
			if ($dtTransaksi === false){
					abort(404, "Data not found");
			}
		}
		$data = [
				"header" => $dtTransaksi["header"], "breads" => $breadcrumb,
				"detailbeli" => isset($dtTransaksi["detailBeli"]) ? json_encode($dtTransaksi["detailBeli"]) : "{}",
				"detailjual" => isset($dtTransaksi["detailJual"]) ? json_encode($dtTransaksi["detailJual"]) : "{}",
				"jenisdokumen" => $dtJenisDokumen, "customer" => $dtCustomer, "satuan" => $dtSatuan,
				"canDelete" => $id != "", "readonly" => auth()->user()->cannot('transaksi.transaksi') ? "readonly" : ""
			];
		return view("transaksi.transaksi", $data);
	}
	public function exportXls($spreadsheet, $prefix)
	{
		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
		return response()->streamDownload(function() use ($writer){
					$writer->save('php://output');
				}, $prefix ."_" .Date("YmdHis") .'.xlsx',
				['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
	}
	public function browse(Request $request)
  {
		if(!auth()->user()->can('transaksi.browse')){
			abort(403, 'User does not have the right roles.');
		}
		$filter = $request->input("filter");
		if ($filter && $filter == "1"){
			$postCustomer = $request->input("customer");
			$postKategori1 = $request->input("kategori1");
			$isikategori1 = $request->input("isikategori1");
			$postKategori2 = $request->input("kategori2");
			$dari2 = $request->input("dari2");
			$sampai2 = $request->input("sampai2");

			$data = Transaksi::browse($postCustomer, $postKategori1,
																	$isikategori1, $postKategori2, $dari2, $sampai2);
			if ($data){
				$export = $request->input("export");
				if ($export == "1"){
					$spreadsheet = new Spreadsheet();
					$sheet = $spreadsheet->getActiveSheet();
					$sheet->setCellValue('A1', 'CUSTOMER');
					if ($postCustomer && trim($postCustomer) != ""){
						$customer = Customer::where("id_customer", $postCustomer);
						$sheet->setCellValue('C1', $customer->first()->nama_customer);
					}
					else {
						$sheet->setCellValue('C1', "Semua");
					}
					$lastrow = 1;
					if ($postKategori1 && trim($postKategori1) != ""){
						$lastrow += 1;
						$sheet->setCellValue('A' .$lastrow, $postKategori1);
						$sheet->setCellValue('C' .$lastrow, $isikategori1);
					}
					if ($postKategori2 && trim($postKategori2) != ""){
						$lastrow += 1;
						$sheet->setCellValue('A' .$lastrow, $postKategori2);
						$sheet->setCellValue('C' .$lastrow, $dari2 == "" ? "-" : Date("d M Y", strtotime($dari2)));
						$sheet->setCellValue('D' .$lastrow, "sampai");
						$sheet->setCellValue('E' .$lastrow, $sampai2 == "" ? "-" : Date("d M Y", strtotime($sampai2)));
					}

					$lastrow += 2;
					$sheet->setCellValue('A' .$lastrow, 'Job Order');
					$sheet->setCellValue('B' .$lastrow, 'Tgl Job');
					$sheet->setCellValue('C' .$lastrow, 'No Dok');
					$sheet->setCellValue('D' .$lastrow, 'Customer');
					$sheet->setCellValue('E' .$lastrow, 'Tgl Tiba');
					$sheet->setCellValue('F' .$lastrow, 'No Aju');
					$sheet->setCellValue('G' .$lastrow, 'Nopen');
			    $sheet->setCellValue('H' .$lastrow, 'Tgl Nopen');
			    $sheet->setCellValue('I' .$lastrow, 'Tgl SPPB');
			    $sheet->setCellValue('J' .$lastrow, 'Ttl Biaya');
					$sheet->setCellValue('K' .$lastrow, 'Ttl Billing');
					$sheet->setCellValue('L' .$lastrow, 'Ttl Payment');

					foreach ($data as $dt){
						$lastrow += 1;
						$sheet->setCellValue('A' .$lastrow, $dt->JOB_ORDER);
						$sheet->setCellValue('B' .$lastrow, $dt->TGL_JOB);
						$sheet->setCellValue('C' .$lastrow, $dt->NO_DOK);
						$sheet->setCellValue('D' .$lastrow, $dt->NAMACUSTOMER);
						$sheet->setCellValue('E' .$lastrow, $dt->TGL_TIBA);
						$sheet->setCellValue('F' .$lastrow, $dt->NOAJU);
						$sheet->setCellValue('G' .$lastrow, $dt->NOPEN);
						$sheet->setCellValue('H' .$lastrow, $dt->TGL_NOPEN);
						$sheet->setCellValue('I' .$lastrow, $dt->TGL_SPPB);
						$sheet->setCellValue('J' .$lastrow, $dt->TOTAL_BIAYA);
						$sheet->setCellValue('K' .$lastrow, $dt->TOTAL_BILLING);
						$sheet->setCellValue('L' .$lastrow, $dt->TOTAL_PAYMENT);
					}
					return $this->exportXls($spreadsheet, "browse");
				}
				else {
					return response()->json($data);
				}
			}
			else {

				return response()->json([]);
			}
		}
		else {
			$breadcrumb[] = Array("link" => "../", "text" => "Home");
			$breadcrumb[] = Array("text" => "Browse Job Order");
			$customer = Customer::select("id_customer","nama_customer")->get();
			return view("transaksi.browse",["breads" => $breadcrumb,
										"datacustomer" => $customer,
										"datakategori1" => Array("No Job","No Dok"),
										"datakategori2" => Array("Tanggal Job","Tanggal Tiba")
										]);
		}
	}
	public function aruskas(Request $request)
  {
		if(!auth()->user()->can('aruskas')){
			abort(403, 'User does not have the right roles.');
		}
		$filter = $request->input("filter");
		if ($filter && $filter == "1"){
			$postCustomer = $request->input("customer");
			$postKategori1 = $request->input("kategori1");
			$isikategori1 = $request->input("isikategori1");
			$postKategori2 = $request->input("kategori2");
			$dari2 = $request->input("dari2");
			$sampai2 = $request->input("sampai2");

			$data = Transaksi::arusKas($postCustomer, $postKategori1,
																	$isikategori1, $postKategori2, $dari2, $sampai2);
			if ($data){
				$export = $request->input("export");
				if ($export == "1"){
					$spreadsheet = new Spreadsheet();
					$sheet = $spreadsheet->getActiveSheet();
					$sheet->setCellValue('A1', 'CUSTOMER');
					if ($postCustomer && trim($postCustomer) != ""){
						$customer = Customer::where("id_customer", $postCustomer);
						$sheet->setCellValue('C1', $customer->first()->nama_customer);
					}
					else {
						$sheet->setCellValue('C1', "Semua");
					}
					$lastrow = 1;
					if ($postKategori1 && trim($postKategori1) != ""){
						$lastrow += 1;
						$sheet->setCellValue('A' .$lastrow, $postKategori1);
						$sheet->setCellValue('C' .$lastrow, $isikategori1);
					}
					if ($postKategori2 && trim($postKategori2) != ""){
						$lastrow += 1;
						$sheet->setCellValue('A' .$lastrow, $postKategori2);
						$sheet->setCellValue('C' .$lastrow, $dari2 == "" ? "-" : Date("d M Y", strtotime($dari2)));
						$sheet->setCellValue('D' .$lastrow, "sampai");
						$sheet->setCellValue('E' .$lastrow, $sampai2 == "" ? "-" : Date("d M Y", strtotime($sampai2)));
					}

					$lastrow += 2;
					$sheet->setCellValue('A' .$lastrow, 'No Job');
					$sheet->setCellValue('B' .$lastrow, 'Tgl Job');
					$sheet->setCellValue('C' .$lastrow, 'No Dok');
					$sheet->setCellValue('D' .$lastrow, 'Tgl Trans');
					$sheet->setCellValue('E' .$lastrow, 'Kode Trans');
					$sheet->setCellValue('F' .$lastrow, 'Nominal');
					$sheet->setCellValue('G' .$lastrow, 'D/K');

					foreach ($data as $dt){
						$lastrow += 1;
						$sheet->setCellValue('A' .$lastrow, $dt->JOB_ORDER);
						$sheet->setCellValue('B' .$lastrow, $dt->TGL_JOB);
						$sheet->setCellValue('C' .$lastrow, $dt->NO_DOK);
						$sheet->setCellValue('D' .$lastrow, $dt->TANGGAL);
						$sheet->setCellValue('E' .$lastrow, $dt->TRANSAKSI);
						$sheet->setCellValue('F' .$lastrow, $dt->NOMINAL);
						$sheet->setCellValue('G' .$lastrow, $dt->DK);
					}
					return $this->exportXls($spreadsheet, "aruskas");
				}
				else {
					return response()->json($data);
				}
			}
			else {

				return response()->json([]);
			}
		}
		else {
			$breadcrumb[] = Array("link" => "../", "text" => "Home");
			$breadcrumb[] = Array("text" => "Browse Arus Kas");
			$customer = Customer::select("id_customer","nama_customer")->get();
			return view("transaksi.aruskas",["breads" => $breadcrumb,
										"datacustomer" => $customer,
										"datakategori1" => Array("No Job","No Dok"),
										"datakategori2" => Array("Tanggal Job","Tanggal Transaksi")
										]);
		}
	}
	public function pembayaran(Request $request)
  {
		$canEdit = auth()->user()->can('pembayaran.transaksi');
		if (!$canEdit){
				abort(403, "User does not have the right roles");
		}
		$dtTransaksi = Array();
		$id = $request->id ?? "";
		$dtTransaksi = Transaksi::getPembayaran($id);
		if (!$dtTransaksi){
				abort(404, 'Data tidak ada');
		}

		$breadcrumb[] = Array("link" => "/", "text" => "Home");

		$breadcrumb[] = Array("text" => "Transaksi Pembayaran");

		$dtRekening = Rekening::orderBy("NAMA")->get();
		$dtKodeTransaksi = KodeTransaksi::orderBy("URAIAN")->get();

		$data = [
				"header" => isset($dtTransaksi["header"]) ? $dtTransaksi["header"] : "{}" , "breads" => $breadcrumb,
				"rekening" => $dtRekening,
				"detail" => isset($dtTransaksi["detail"]) ? json_encode($dtTransaksi["detail"]) : "{}",
				"kodetransaksi" => $dtKodeTransaksi,
				"readonly" => $canEdit ? '' : 'readonly'
			];
		return view("transaksi.pembayaran", $data);
	}
	public function searchjob(Request $request)
	{
		$job = $request->input("job");
		$data = Transaksi::where("JOB_ORDER", $job)->select("ID","NO_DOK");
		if ($data->exists()){
    	return response()->json($data->first());
		}
		else {
			return response()->json(["error" => "Data tidak ada"]);
		}
	}
	public function crud(Request $request)
	{
		$postheader = $request->input("header");
		$type = $request->input("type");
		$message = Array();
		DB::beginTransaction();
		try {
			if (!$type){
				if ($postheader){
					$detailBeli = $request->input("detailbeli");
					$detailJual = $request->input("detailjual");
					parse_str($postheader, $header);
					if ($header["idtransaksi"] == ""){
						$action = "insert";
					}
					else {
						$action = "update";
					}
					$id = Transaksi::saveTransaksi($action, $header, $detailBeli, $detailJual);
				}
				else {
					$id = $request->input("delete");
					if ($id && $id != ""){
						Transaksi::deleteTransaksi($id);
					}
				}
			}
			else if ($type == "pembayaran"){
				if ($postheader){
					$detail = $request->input("detail");
					parse_str($postheader, $header);
					$id = Transaksi::savePembayaran($header, $detail);
				}
				else {
					$id = $request->input("delete");
					if ($id && $id != ""){
						Transaksi::deletePembayaran($id);
					}
				}
			}
			DB::commit();
			$message["result"] = $id;
		}
		catch (\Exception $e){
			DB::rollback();
			$message["error"] = $e->getMessage();
		}

		return response()->json($message);
	}
	public function delete(Request $request)
	{
		if(!auth()->user()->can('transaksi.delete')){
			abort(403, 'User does not have the right roles.');
		}
		$id = $request->input("iddelete");
		$message = Array();
		if ($id && $id != ""){
			DB::beginTransaction();
			try {
				Transaksi::deleteTransaksi($id);
				$message["result"] = $id;
				DB::commit();
			}
			catch (Exception $e){
				$message["error"] = $e->getMessage();
				DB::rollBack();
			}
		}
    return response()->json($message);
	}
	public function upload(Request $request)
	{
		$file = $request->file('file');
		$type = $request->input("filetype");
		if (!$type){
		    $type = 0;
		}
		if ($file->isValid()){
			$realname = $file->getClientOriginalName();
			$extension = $file->getClientOriginalExtension();
			DB::beginTransaction();
			try {
				$id = Transaksi::saveFile($realname, $extension, $type);
				$file->move(storage_path() ."/uploads", $id."." .$extension);
				DB::commit();
				return response()->json(["id" => $id]);
			}
			catch (Exception $e){
				DB::rollBack();
			}
		}
	}
	public function removeFile(Request $request)
	{
		$id = $request->input("id");
		DB::transaction(function() use ($id) {
			Transaksi::deleteFile($id);
		});
	}
	public function getFile(Request $request)
	{
		$file = $request->input("file");
		$dtFile = DB::table("tbl_files")
					 ->select("FILENAME")
					 ->where("ID", $file);
		if ($dtFile->count() > 0){
			$dtFile = $dtFile->first();
			$file = storage_path() .'/uploads/' .$dtFile->FILENAME;
			return response()->download($file, $dtFile->FILENAME);
		}
	}
	public function getPerekamanFiles(Request $request)
	{
		$perekaman_id = $request->input("id");
		$dtFile = DB::table("tbl_files")
					->where("ID_HEADER", $perekaman_id)->get();
		echo json_encode($dtFile);
	}
	public function cron(Request $request)
	{
		$action = $request->input('action');
		if ($action == 'deletefiles'){
			$data = DB::table("tbl_files")
					  ->select("FILENAME")
					  ->whereRaw("ID_HEADER IS NULL")
					  ->get();
			foreach ($data as $dt){
				unlink(storage_path() ."/uploads/" .$dt->FILENAME);
			}
			Transaksi::whereRaw("ID_HEADER IS NULL")->delete();
		}
	}
}
