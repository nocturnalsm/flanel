<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\User;
use App\Companies;
use App\Documents;
use App\UserSettings;
use App\Notifications;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Validator;
use DB;
use Auth;

class UserController extends Controller {

	public function __construct()
	{
		$this->middleware('permission:users');
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$columns = ["name","email","roles"];
		if ($request->ajax()){
			$data = User::select("id", "name", "email", "roles.roles")
						  ->leftJoin(
							DB::raw("(select model_has_roles.model_id,
									   group_concat(roles.name) as roles
									   from model_has_roles
									   inner join roles on roles.id = model_has_roles.role_id
								 	   group by model_has_roles.model_id) roles"),
							function($join){
								$join->on("users.id","=","roles.model_id");
							});
			$totalData = $data->count();
    	$totalFiltered = $totalData;
			if (!empty($request->search["value"])){
				foreach($columns as $key=>$col){
					if ($key == 0){
						$data->where($col, "LIKE", '%' .$request->search['value'] .'%');
					}
					else {
						$data->orWhere($col, "LIKE", '%' .$request->search['value'] .'%');
					}
				}
				$totalFiltered = $data->count();
			}
			$data->orderBy($columns[$request->order[0]['column']], $request->order[0]['dir'])
  				 ->skip($request->start)
  				 ->take($request->length);
			return ["draw" => intval($request->draw), "recordsTotal" => $totalData,
    					"recordsFiltered" => $totalFiltered,
    					"data" => $data->get()
				   ];
		}
		else {
      $breadcrumb[] = Array("link" => "../", "text" => "Home");
      $breadcrumb[] = Array("text" => "User");
			return view ("admin.users.index", ["breads" => $breadcrumb]);
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$data = new User;
    //$companies = Companies::get();
    $roles = Role::select("name")->get();
		return view('admin.users.form', ["data" => $data, /*"companies" => $companies, "userCompanies" => [],*/
								"roles" => $roles, "action" => "add", "userRoles" => []]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$rules = [
					'name' => 'required|unique:users',
					'email' => 'required|email|unique:users',
					'password' => "required|same:confirm",
		];
		if (!Auth::user()->hasRole('Super Admin')){
			$rules["companies"] = 'required';
		}
		$v = Validator::make($request->all(), $rules);
		if ($v->fails())
		{
			return response()->json(["errors" => $v->errors()]);
		}
		DB::beginTransaction();
		try {
			$user = new User;
			$user->password = Hash::make($request->password);
			$user->name = $request->name;
			$user->email = $request->email;
			$user->save();
      if (isset($request->roles)){
  			foreach($request->roles as $role){
  				$user->assignRole($role);
  			}
      }
			DB::commit();
			return response()->json(["type" => "success", "text" => "Penyimpanan Berhasil"]);
		}
		catch (Exception $e){
			DB::rollBack();
			return response()->json(["type" => "warning", "text" => "Penyimpanan data gagal.<br>" .$e->getMessage()]);
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$data = User::where("id", $id)->first();
		//$companies = Companies::get();
		$roles = Role::select("name")->get();
		$userRoles = DB::table("model_has_roles")
							->select("name")
							->join("roles","model_has_roles.role_id","=","roles.id")
							->where("model_id", $id)
							->get();
		$arrRoles = [];
		foreach($userRoles as $ur){
			$arrRoles[] = $ur->name;
		}
		return view('admin.users.form', ["data" => $data, /*"companies" => $companies,*/
								   "userRoles" => $arrRoles,
								   "roles" => $roles, "action" => "edit"]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, Request $request)
	{
		$v = Validator::make($request->all(), [
			      'name' => 'required|unique:users,name,' .$id,
            'email' => 'required|email|unique:users,email,' .$id,
            'password' => 'same:confirm',
            'roles' => 'required'
		]);
		if ($v->fails()){
			return response()->json(["errors" => $v->errors()]);
		}
		DB::beginTransaction();
		try {
			$user = User::find($id);
			if (!empty($request->password)){
				$user->password = Hash::make($request->password);
			}
			$user->name = $request->name;
			$user->email = $request->email;
			$user->save();

			$user->syncRoles();
			foreach($request->roles as $role){
				$user->assignRole($role);
			}

			DB::commit();
			return response()->json(["type" => "success", "text" => "Update berhasil"]);
		}
		catch (Exception $e){
			DB::rollBack();
			return response()->json(["type" => "error", "text" => $e->getMessage()]);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Request $request)
	{
		DB::beginTransaction();
		try {
			$user = User::find($request->id);
			DB::table('model_has_roles')->where('model_id',$user->id)->delete();
			$data = $user->delete();
			DB::commit();
			return response()->json(["type" => "info", "text" => "Data berhasil dihapus"]);
		}
		catch (Exception $e){
			DB::rollBack();
			return response()->json(["type" => "error", "text" => "Penghapusan data gagal"]);
		}
	}
}
