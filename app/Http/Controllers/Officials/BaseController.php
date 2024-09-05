<?php

namespace App\Http\Controllers\Officials;

use App\Http\Controllers\Controller;
use App\Models\Role;

class BaseController extends Controller
{
    protected $role;

    public function __construct(){

        $this->role = Role::where('name', request('role'))->firstOrFail()->name;
    }
}
