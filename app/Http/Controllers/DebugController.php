<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class DebugController extends Controller
{
    public function dumpData()
    {
        $users = User::all();
        $roles = Role::all();
        $model_has_roles = DB::table('model_has_roles')->get();

        echo "<h1>Users</h1>";
        dump($users);

        echo "<h1>Roles</h1>";
        dump($roles);

        echo "<h1>Model Has Roles</h1>";
        dump($model_has_roles);
    }
}
