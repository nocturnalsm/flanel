<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaksi;

class KodeTransaksi extends Model
{
    protected $table  = 'ref_kode_transaksi';
    protected $primaryKey = 'KODETRANSAKSI_ID';
    protected $guarded = ['KODETRANSAKSI_ID'];
    public $timestamps = false;

    public static function add($fields)
	  {
        $check = KodeTransaksi::select("KODE")
							->where("KODE", $fields["input-kode"])
							->orWhere("URAIAN", $fields["input-uraian"]);
    		if ($check->count() > 0){
    			throw new \Exception("Kode Transaksi sudah ada");
    		}
    		$data = Array("KODE" => strtoupper($fields["input-kode"]),
    					  "URAIAN" => strtoupper($fields["input-uraian"]));
    		KodeTransaksi::create($data);
  	}
  	public static function edit($fields)
  	{
        $check = KodeTransaksi::select("KODE")
                            ->where(function($query) use ($fields){
    		$query->where("KODE", $fields["input-kode"])
    							  ->orWhere("URAIAN", $fields["input-uraian"]);
    					})
              ->where("KODETRANSAKSI_ID" ,"<>", $fields["input-id"]);
    		if ($check->count() > 0){
    			throw new \Exception("Kode Transaksi sudah ada");
    		}
    		$data = Array( "KODE" => strtoupper(trim($fields["input-kode"])),
    					   "URAIAN" => trim($fields["input-uraian"]));
    		KodeTransaksi::where("KODETRANSAKSI_ID", $fields["input-id"])->update($data);
  	}
  	public static function drop($id)
  	{
    		$checkStat = DB::table("pembayaran_detail")
                        ->select("ID")->firstWhere("KODE_TRANSAKSI", $id);
    		if ($checkStat){
    			throw new \Exception("Kode Transaksi tidak dapat dihapus karena sudah dipakai di transaksi");
    		}
    		else {
    			$data = KodeTransaksi::find($id);
    			if ($data){
    				$data->delete();
    			}
    		}
  	}
}
