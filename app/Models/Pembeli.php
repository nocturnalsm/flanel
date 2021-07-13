<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaksi;
use App\Models\DeliveryOrder;
use DB;

class Pembeli extends Model
{
		protected $table  = 'ref_pembeli';
		protected $primaryKey = "ID";
    protected $guarded = ['ID'];
    public $timestamps = false;

    public static function add($fields)
		{
				$check = Pembeli::select("KODE")
								->where("KODE", "LIKE", '%' .strtoupper(trim($fields['input-kode'])) .'%')
								->orWhere("NAMA", "LIKE", '%' .strtoupper(trim($fields["input-nama"])) .'%');

				if ($check->exists()){

						throw new \Exception("Data sudah ada");
						return false;
				}
	      $data = Array( "KODE" => strtoupper(trim($fields['input-kode'])),
	                     "NAMA" => strtoupper(trim($fields["input-nama"])),
										   "ALAMAT" => strtoupper(trim($fields["input-alamat"])),
										   "KETERANGAN" => strtoupper(trim($fields["input-keterangan"])),
										   "KTPNPWP" => strtoupper(trim($fields["input-ktpnpwp"]))
					  		);
				Pembeli::create($data);
			}
			public static function edit($fields)
			{
					$check = Pembeli::select("KODE")
									->where(function($query) use ($fields){
											$query->where("KODE", "LIKE", '%' .$fields['input-kode'] .'%')
														->orWhere("NAMA", "LIKE", '%' .strtoupper(trim($fields["input-nama"])) .'%');
									})
									->where("ID", "<>", $fields['input-id']);
					if ($check->exists()){
							throw new \Exception('Data sudah ada');
							return false;
					}
					$data = Array( "NAMA" => strtoupper(trim($fields["input-nama"])),
											   "ALAMAT" => trim($fields["input-alamat"]),
											   "KETERANGAN" => trim($fields["input-keterangan"]),
											   "KTPNPWP" => trim($fields["input-ktpnpwp"])
											  );
					Pembeli::where("ID", $fields["input-id"])->update($data);
			}
			public static function drop($id)
			{
			     $checkStat = DB::table("penjualan_detail")
					 								->select("ID")
													->firstWhere("PEMBELI_ID", $id);
					if (!$checkStat){
							$data = Pembeli::find($id);
							if ($data){
								$data->delete();
							}
					}
					else {
							throw new \Exception("Pembeli tidak dapat dihapus karena sudah dipakai di transaksi");
					}
			}
}
