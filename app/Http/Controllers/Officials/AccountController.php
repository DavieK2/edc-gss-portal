<?php

namespace App\Http\Controllers\Officials;

use App\Models\Account;

class AccountController extends BaseController
{
    public function index()
    {
        return view('officials.accounts.index', [ 'role' => $this->role, 'accounts' => Account::all() ]);
    }

    public function edit($role, Account $account)
    {
        return view('officials.accounts.edit', [ 'role' => $this->role, 'account' => $account ]);
    }

    public function create()
    {
        return view('officials.accounts.create', [ 'role' => $this->role ]);
    }

    public function store($role)
    {
        $data = request()->validate([
            'account_name' => 'required',
            'account_number' => 'required',
            'bank_name' => 'required',
            'account_id' => 'required',
        ]);

        Account::create($data);

        return redirect(route('officials.accounts.index', [$this->role]));
    }

    public function update($role, Account $account)
    {
        $data = request()->validate([
            'account_name' => 'required',
            'account_number' => 'required',
            'bank_name' => 'required',
            'account_id' => 'required',
        ]);

        $account->update($data);

        return redirect(route('officials.accounts.index', [$this->role]));
    }

    public function delete($role, Account $account)
    {
        $account->delete();
        return redirect(route('officials.accounts.index', [$this->role]));
    }
}
