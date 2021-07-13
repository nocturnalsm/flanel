<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Satuan extends Model
{
    protected $table  = 'satuan';
    protected $guarded = ['id'];
    public $timestamps = false;

    public static function add($fields)
	  {
        $check = Satuan::select("kode")
                            ->where("kode", $fields["input-kode"])
                            ->orWhere("satuan", $fields["input-satuan"]);
    		if ($check->count() > 0){
    			throw new \Exception("Satuan sudah ada");
    		}
    		$data = Array("kode" => strtoupper($fields["input-kode"]),
    					  "satuan" => strtoupper($fields["input-satuan"]));
    		Satuan::create($data);
  	}
  	public static function edit($fields)
  	{
        $check = Satuan::select("kode")
                          ->where(function($query) use ($fields){
                              $query->where("kode", $fields["input-kode"])
                                    ->orWhere("satuan", $fields["input-satuan"]);
                          })
                          ->where("id" ,"<>", $fields["input-id"]);
  		  if ($check->count() > 0){
  			     throw new \Exception("Satuan sudah ada");
        }
        $satuan = Satuan::find($fields["input-id"]);
        if ($satuan){
            $satuan->kode = strtoupper(trim($fields["input-kode"]));
            $satuan->satuan = trim($fields["input-satuan"]);
            $satuan->save();
        }
  	}
  	public static function drop($id)
  	{
    		$checkStat = DB::table("pembelian_detail")
                        ->select("ID")
                        ->firstWhere("SATUAN_ID", $id);
    		if ($checkStat){
    			throw new \Exception("Satuan tidak dapat dihapus karena sudah dipakai di transaksi");
    		}
    		else {
                $data = Satuan::find($id);
                if ($data){
                    $data->delete();
                }
    		}
  	}
}
