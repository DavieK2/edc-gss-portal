<?php

namespace App\Http\Controllers\Officials;

use App\Models\Role;
use App\Models\User;
use App\Services\DataTable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UsersController extends BaseController
{
    public function __construct(protected DataTable $datatable){
        parent::__construct();
    }

    public function index()
    {
        return view('officials.users.index', [
            'tableHeadings' => [
                's/n' => 'S/N',
                'fullname' => 'Name',
                'email' => 'Email',
                'phone_number' => 'Phone Number',
                'role' => 'Role',
                'action' => 'Action',
            ], 
            'filters' => [
                'role_id' => Role::whereNotIn('roles.name', ['superadmin', 'student', 'vendor', 'sup-vendor'])->get()->mapWithKeys(fn($role) => [ $role->id => $role->name ])->toArray()
            ],
            'role' => $this->role, 
            'page' => 'Users', 
            'ajaxUrl' => route('officials.users.search', [$this->role])
        ]);
    }

    public function search()
    {
        $query = User::query()->join('role_users', 'users.id', '=', 'role_users.user_id')->join('roles', 'role_users.role_id', '=', 'roles.id')->select('users.*', 'roles.name as role', 'roles.id as role_id');
        
        $query = $query->fromSub($query, 'users');

        $users_columns = collect(Schema::getColumnListing('users'));
        $users_table = 'users';
        $users_columns = $users_columns->mapWithKeys(fn($column, $key) => [$column => $users_table ])->toArray();

        $roles_columns = collect(Schema::getColumnListing('roles'));
        $roles_table = 'roles';
        $roles_columns = $roles_columns->mapWithKeys(fn($column, $key) => [$column => $roles_table ])->toArray();


        $users = $this->datatable->search($query, $roles_columns + $users_columns, $users_table, closure: function($query){
            return $query->whereNotIn('roles.name', ['superadmin', 'student', 'vendor', 'sup-vendor']);
        });

        $items = collect($users->items())->map(function($user, $index) use($users){
            
            return [
                    's/n' => $users->firstItem() + $index,
                    'fullname' => $user->fullname,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'role' => $user->role,
                    'action' => [
                        [
                            'title' => 'Change Password',
                            'url' => route('officials.users.password', [$this->role, $user])
                        ]
                    ]
                ];
        });

        return $this->datatable->response($items, $users);

    }

    public function changePassword($role, User $user)
    {
        return view('officials.users.password', ['role' => $role, 'user' => $user]);
    }


    public function updatePassword($role, User $user)
    {
        $password = request()->validate(['password' => 'required']);

        $user->update(['password' => Hash::make($password['password'])]);

        dd( $user->active_role );

        return redirect()->route('officials.users.index', [$this->role]);
    }
}
