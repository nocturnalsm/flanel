<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateRolesPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $roles = Array(
                  //['name' => 'Super Admin'],
                  ['name' => 'admin'],
                  ['name' => 'Super Admin']
                );
        $permissions = Array(
                ['name' => 'joborder.transaksi'],
                ['name' => 'joborder.browse'],
                ['name' => 'joborder.delete'],
                ['name' => 'aruskas'],
                ['name' => 'pembayaran.browse'],
                ['name' => 'pembayaran.transaksi'],
                ['name' => 'pembayaran.delete'],
                ['name' => 'master.kodetransaksi.browse'],
                ['name' => 'master.rekening.browse'],
                ['name' => 'master.pembeli.browse'],
                ['name' => 'master.jenisdokumen.browse'],
                ['name' => 'master.satuan.browse'],
                ['name' => 'users.list'],
                ['name' => 'roles.list'],
                ['name' => 'profile']
        );
        foreach($roles as $role){
            Role::create($role);
        }
        foreach($permissions as $perm){
            Permission::create($perm);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles_permissions');
    }
}
