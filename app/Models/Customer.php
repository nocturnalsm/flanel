<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table  = 'tb_customer';
    protected $primaryKey = 'id_customer';
    protected $guarded = ['id_customer'];
    public $timestamps = false;

    public static function add($fields)
	  {
      $check = Customer::select("nama_customer")
							->where("nama_customer", $fields["input-nama"]);
  		if ($check->count() > 0){
  			throw new \Exception("Data Customer dengan nama tersebut sudah ada");
  		}
  		$customer = new Customer;
      $customer->nama_customer = trim($fields["input-nama"]);
      $customer->alamat_customer = trim($fields["input-alamat"]);
      $customer->telp_customer = trim($fields["input-telepon"]);
      $customer->fax_customer = trim($fields["input-fax"]);
      $customer->negara_customer = intval($fields["input-negara"]);
      $customer->link_customer = trim($fields["input-link"]);
      $customer->code_customer = trim($fields["input-code"]);
      $customer->status_customer = 1;
      $customer->save();
  	}
  	public static function edit($fields)
  	{
      $check = Customer::select("nama_customer")
                              ->where("nama_customer", $fields["input-nama"])
                              ->where("id_customer" ,"<>", $fields["input-id"]);
  		if ($check->count() > 0){
  			throw new \Exception("Data Customer dengan nama tersebut sudah ada");
  		}
  		$data = Array("nama_customer" => trim($fields["input-nama"]),
  					  "alamat_customer" => trim($fields["input-alamat"]),
  					  "telp_customer" => trim($fields["input-telepon"]),
              "fax_customer" => trim($fields["input-fax"]),
              "negara_customer" => $fields["input-negara"],
              "link_customer" => trim($fields["input-link"]),
              "code_customer" => trim($fields["input-code"])
  					);
  		Customer::where("id_customer", $fields["input-id"])->update($data);
  	}
  	public static function drop($id)
  	{
  		$checkStat = Transaksi::select("ID")->firstWhere("CUSTOMER", $id);
  		if ($checkStat){
  			throw new \Exception("Customer tidak dapat dihapus karena sudah dipakai di transaksi");
  		}
  		else {
  			$data = Customer::find($id);
  			if ($data){
  				$data->delete();
  			}
  		}
  	}
}
