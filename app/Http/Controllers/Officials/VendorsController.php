<?php

namespace App\Http\Controllers\Officials;

use App\Models\Role;
use App\Models\User;
use App\Services\DataTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Schema;

class VendorsController extends BaseController
{
    public function __construct(protected DataTable $datatable){
        parent::__construct();
    }

    public function index()
    {
        $tableHeadings = [
            'checkbox'    =>  '<input type="checkbox" id="checkall" />',
            's/n' => 'S/N',
            'vendor_name' => 'Vendor Name',
            'email' => 'Email',
            'total_registrations' => 'Total Registrations',
            'tokens_purchased' => 'Tokens Purchased',
            'last_purchase' => 'Last Purchase',
            'last_purchase_amount' => 'Last Purchase Amount',
            'status' => 'Status',
            'action' => 'Action',
        ];

        return view('officials.vendors.index', [
            'tableHeadings' => $tableHeadings, 
            'role' => $this->role,
            'name' => 'fullname,email',
            'filters' => [
                'scheme_id' => [ 1 => 'EDC', 2 => 'GSS' ],
                'status' => [ 1 => 'Active', 0 => 'Inactive' ]
            ],
            'page' => 'Vendors',  
            'ajaxUrl' => route('officials.vendors.get', [$this->role])
        ]);
    }


    public function tokens($role, User $vendor)
    {
        $tokens = $vendor->transactions()->where('type', 'credit')->latest()->get();

        return view('officials.vendors.tokens', compact('tokens', 'role'));
    }

    public function registrations($role, User $vendor)
    {
        $registrations = $vendor->registrations;

        return view('officials.vendors.registrations', compact('registrations', 'role'));
    }

    public function activate($role, User $vendor)
    {
        $vendor->update(['status' => $vendor->status ? false : true ]);

        alert('Success', $vendor->status ? 'Active' : 'Inactive');

        return back();
    }

    public function toggleRole($role, User $vendor, $toggle = false)
    {
        $user_role = Role::firstWhere('name', 'sup-vendor');

        $toggle ? $vendor->role()->syncWithoutDetaching($user_role) : $vendor->role()->detach($user_role->id);

        alert('Success', $toggle ? 'Vendor is assigned as supplementary vendor' : 'Vendor is removed as supplementary vendor');

        return back();
    }

    public function getVendors()
    {
        $schemes = auth()->guard($this->role)->user()->schemes->pluck('id')->toArray();
        $role = Role::firstWhere('name', 'vendor');

        $users = collect(Schema::getColumnListing('users'));
        $users_table = 'users';
        $users_columns = $users->mapWithKeys(fn($column, $key) => [$column => $users_table ])->toArray();

        $scheme_users_columns = collect(Schema::getColumnListing('scheme_users'));
        $scheme_users_table = 'scheme_users';
        $scheme_users_columns = $scheme_users_columns->mapWithKeys(fn($column, $key) => [$column => $scheme_users_table ])->toArray();

        $users = User::query()
                    ->join('scheme_users', 'users.id', '=', 'scheme_users.user_id')
                    ->join('role_users', 'users.id', '=', 'role_users.user_id');

        $users = $this->datatable->search($users, ($users_columns + $scheme_users_columns), $users_table, function(Builder|QueryBuilder $query) use($schemes, $role) {
            $query->where(function($query) use($schemes, $role){
                        $query->whereIn('scheme_users.scheme_id', $schemes)
                              ->where('role_users.role_id', $role?->id);
                  });
                  
        });

        $items = collect($users->items())->map(function($user, $index) use($users) {
            
            $transactions = $user->transactions()->where('type', 'credit');

            $total_registrations = $user->registrations()->count();
            $user_registrations_route = route('officials.vendors.registrations', [ $this->role, $user->id ]);
            
            $total_tokens = $transactions->sum('number_of_tokens');
            $user_token_route = route('officials.vendors.tokens', [ $this->role, $user->id ]);

            $is_sup_vendor = in_array('sup-vendor', $user->role->pluck('name')->toArray()) ;

            return [
                'checkbox'    =>  "<input type='checkbox' name='check[]' value='$user->id' class='check' />",
                's/n' => $users->firstItem() + $index, 
                'vendor_name' => $user->fullname, 
                'email' => $user->email,
                'total_registrations' => "<a class='text-primary' href=$user_registrations_route >$total_registrations</a>",
                'tokens_purchased' => "<td style='width: 5%'><a class='text-primary' href=$user_token_route>$total_tokens</a></td>",
                'last_purchase' => $transactions->latest()->first()?->created_at ?? 'N/A',
                'last_purchase_amount' => "&#8358; ".number_format($transactions->latest()->first()?->price ?? 0, 2) ,
                'status' => $user->status ? 'Active' : 'Inactive',
                'action' => [
                    [
                        'title' => $user->status ? 'Deactivate' : 'Activate',
                        'url' => route('officials.vendors.activate', [ $this->role, $user->id ])
                    ],
                    [
                        'title' => $is_sup_vendor ? 'Remove as Supplementary Vendor' : 'Make Supplementary Vendor',
                        'url' =>  $is_sup_vendor ? route('officials.vendors.role.toggle', [ $this->role, $user]) : route('officials.vendors.role.toggle', [ $this->role, $user, true])
                    ],
                    [
                        'title' => 'Change Password',
                        'url' => route('officials.users.password', [$this->role, $user])
                    ],
                    [
                        'title' => $user->hasRole('editor') ? 'Deactivate as Editor' : 'Activate as Editor',
                        'url' => route('officials.vendors.role.editor', [$this->role, $user])
                    ]
                ]

            ];
        });

        return $this->datatable->response($items, $users);
    }


    public function toggleEditStudentData($role, User $vendor)
    {
        $editor_role = Role::firstWhere('name', 'editor');

        if( $vendor->hasRole($editor_role->name) ){

            $vendor->role()->detach($editor_role);

        }else{

            $vendor->role()->syncWithoutDetaching($editor_role);
        }

        return back();
    }

    public function activateVendors($role)
    {

        User::whereIn('id', request('check'))?->update(['status' => true ]);

        alert('Success', 'Vendors Account Successfully Activated');

        return back();
    }
    
    public function deactivateVendors($role)
    {

        User::whereIn('id', request('check'))?->update(['status' => false ]);

        alert('Success', 'Vendors Account Successfully Deactivated');

        return back();
    }


}
