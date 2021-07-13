<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DataTable;
use App\Models\JenisDokumen;
use App\Models\KodeTransaksi;
use App\Models\Bank;
use App\Models\Rekening;
use App\Models\Customer;
use App\Models\Satuan;

class MasterController extends Controller
{
	public function __construct()
	{
		if (!auth()->user()->can('master.*')){
			abort(403, 'User does not have the right roles.');
		}
	}
	public function kodetransaksi(Request $request)
	{
		$breadcrumb[] = Array("link" => "../", "text" => "Home");
		$breadcrumb[] = Array("text" => "Kode Transaksi");
		return view("master.kodetransaksi", ["breads" => $breadcrumb,
        								   "columns" => Array("Kode","Uraian")]);
	}
	public function getdata_kodetransaksi(Request $request)
	{
		$dataSource = KodeTransaksi::select('KODETRANSAKSI_ID','KODE','URAIAN');
		$dataTable = datatables()->of($dataSource);
		return $dataTable->toJson();
	}
	public function getdata_bank()
	{
			$dataSource = Bank::select('bank_id','bank');
			$dataTable = datatables()->of($dataSource);
			return $dataTable->toJson();
	}
	public function bank()
	{
		$breadcrumb[] = Array("link" => "../", "text" => "Home");
		$breadcrumb[] = Array("text" => "Bank");
		return view("master.bank",   ["breads" => $breadcrumb,
									 "columns" => Array("Bank")]);
	}
	public function getdata_rekening()
	{
		$dataSource = Rekening::select('REKENING_ID','NO_REKENING','NAMA','rekening.BANK_ID','BANK')
							   ->join('bank','bank.bank_id','=','rekening.bank_id');
		$dataTable = datatables()->of($dataSource);
		return $dataTable->toJson();
	}
	public function rekening()
	{
		$breadcrumb[] = Array("link" => "../", "text" => "Home");
		$breadcrumb[] = Array("text" => "Rekening");
		$dtBank = Bank::select('BANK_ID','BANK')->get();
		return view("master.rekening",   ["breads" => $breadcrumb,
									 "columns" => Array("Bank","No Rekening","Nama"),
									 "databank" => $dtBank]);
	}
	public function jenisdokumen()
	{
		$breadcrumb[] = Array("link" => "../", "text" => "Home");
		$breadcrumb[] = Array("text" => "Jenis Dokumen");
		return view("master.jenisdokumen",  ["breads" => $breadcrumb,
									 "columns" => Array("Kode","Jenis Dokumen")]);
	}
	public function getdata_jenisdokumen()
	{
		$dataSource = JenisDokumen::select('jenisdokumen_id','kode','uraian');
		$dataTable = datatables()->of($dataSource);
		return $dataTable->toJson();
	}
	public function satuan()
	{
		$breadcrumb[] = Array("link" => "../", "text" => "Home");
		$breadcrumb[] = Array("text" => "Satuan");
		return view("master.satuan", ["breads" => $breadcrumb,
                               "columns" => Array("Kode","Satuan")]);
	}
	public function getdata_satuan()
	{
		$dataSource = Satuan::select('id','kode','satuan');
		$dataTable = datatables()->of($dataSource);
		return $dataTable->toJson();
	}
	public function customer()
	{
		$breadcrumb[] = Array("link" => "../", "text" => "Home");
		$breadcrumb[] = Array("text" => "Customer");
		$negara = DB::table("plbbandu_app15.tb_country")->select("id_country","country_name")->get();
		return view("master.customer",
									 ["breads" => $breadcrumb, "negara" => $negara,
									 "columns" => Array("Nama","Alamat","Telepon","Negara","Fax","Website","Kode")]);
	}
	public function getdata_customer()
	{
		$dataSource = Customer::select("*", DB::raw("negara.country_name as negara"))
												  ->leftJoin("plbbandu_app15.tb_country as negara", "negara_customer","=","negara.id_country")
													->get();
		$dataTable = datatables()->of($dataSource);
		return $dataTable->toJson();
	}
	public function crud(Request $request)
	{
		$action = $request->input("action");
		$message = Array();
		if ($action){
			$fields = $request->input("input");
			parse_str($fields, $input);
			try {
				switch ($action){
					case "kodetransaksi":
						if ($input["input-action"] == "add"){
							$result = KodeTransaksi::add($input);
						}
						else if ($input["input-action"] == "edit"){
							$result = KodeTransaksi::edit($input);
						}
						else if ($input["input-action"] == "delete"){
							$result = KodeTransaksi::drop($input["id"]);
						}
						break;
					case "satuan":
						if ($input["input-action"] == "add"){
							$result = Satuan::add($input);
						}
						else if ($input["input-action"] == "edit"){
							$result = Satuan::edit($input);
						}
						else if ($input["input-action"] == "delete"){
							$result = Satuan::drop($input["id"]);
						}
						break;
					case "jenisdokumen":
						if ($input["input-action"] == "add"){
							$result = JenisDokumen::add($input);
						}
						else if ($input["input-action"] == "edit"){
							$result = JenisDokumen::edit($input);
						}
						else if ($input["input-action"] == "delete"){
							$result = JenisDokumen::drop($input["id"]);
						}
						break;
					case "bank":
						if ($input["input-action"] == "add"){
							$result = Bank::add($input);
						}
						else if ($input["input-action"] == "edit"){
							$result = Bank::edit($input);
						}
						else if ($input["input-action"] == "delete"){
							$result = Bank::drop($input["id"]);
						}
						break;
					case "rekening":
						if ($input["input-action"] == "add"){
							$result = Rekening::add($input);
						}
						else if ($input["input-action"] == "edit"){
							$result = Rekening::edit($input);
						}
						else if ($input["input-action"] == "delete"){
							$result = Rekening::drop($input["id"]);
						}
						break;
					case "customer":
							if ($input["input-action"] == "add"){
								$result = Customer::add($input);
							}
							else if ($input["input-action"] == "edit"){
								$result = Customer::edit($input);
							}
							else if ($input["input-action"] == "delete"){
								$result = Customer::drop($input["id"]);
							}
					break;

				}
				$message["result"] = $result;
			}
			catch (\Exception $e){
				$message["error"] = $e->getMessage();
			}
		}
		return response()->json($message);
	}
}
