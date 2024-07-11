<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Util;
use App\Models\UserAccount;
use App\Models\UserRole;

class UserAccountController extends Controller {

    public function index()
    {
        $size = request()->input('size') ? request()->input('size') : 10;
        $sort = request()->input('sort') ? request()->input('sort') : 'useraccount.id';
        $sortDirection = request()->input('sort') ? (request()->input('desc') ? 'desc' : 'asc') : 'asc';
        $column = request()->input('sc');
        $query = UserAccount::query()
            ->select('useraccount.id', 'useraccount.name', 'useraccount.email', 'useraccount.active')
            ->orderBy($sort, $sortDirection);
        if (Util::IsInvalidSearch($query->getQuery()->columns, $column)) {
            abort(403);
        }
        if (request()->input('sw')) {
            $search = request()->input('sw');
            $operator = Util::getOperator(request()->input('so'));
            if ($operator == 'like') {
                $search = '%'.$search.'%';
            }
            $query->where($column, $operator, $search);
        }
        $userAccounts = $query->paginate($size);
        return ['userAccounts' => $userAccounts->items(), 'last' => $userAccounts->lastPage()];
    }

    public function create()
    {
        $roles = DB::table('role')
            ->select('role.id', 'role.name')
            ->get();
        return ['roles' => $roles];
    }

    public function store()
    {
        $this->validate(request(), [
            'name' => 'unique:UserAccount,name|required|max:50',
            'email' => 'unique:UserAccount,email|required|max:50',
        ]);
        $token = Str::random(40);
        $password = Hash::make(Str::random(10));
        $userAccount = UserAccount::create([
            'password_reset_token' => $token,
            'password' => $password,
            'name' => request()->input('name'),
            'email' => request()->input('email'),
            'active' => Util::getRaw(request()->input('active') == null ? false : request()->input('active'))
        ]);
        $roles = request()->input('role_id');
        if ($roles) {
            foreach ($roles as $role) {
                UserRole::create([
                    'user_id' => $userAccount->id,
                    'role_id' => $role
                ]);
            }
        }
        Util::sentMail('WELCOME', $userAccount->email, $token, $userAccount->name);
    }

    public function show($id)
    {
        $userAccount = UserAccount::query()
            ->select('useraccount.id', 'useraccount.name', 'useraccount.email', 'useraccount.active')
            ->where('useraccount.id', $id)
            ->first();
        $userAccountUserRoles = DB::table('useraccount')
            ->join('userrole', 'useraccount.id', 'userrole.user_id')
            ->join('role', 'userrole.role_id', 'role.id')
            ->select('role.name as role_name')
            ->where('useraccount.id', $id)
            ->get();
        return ['userAccount' => $userAccount, 'userAccountUserRoles' => $userAccountUserRoles];
    }

    public function edit($id)
    {
        $userAccount = UserAccount::query()
            ->select('useraccount.id', 'useraccount.name', 'useraccount.email', 'useraccount.active')
            ->where('useraccount.id', $id)
            ->first();
        $userAccountUserRoles = DB::table('useraccount')
            ->join('userrole', 'useraccount.id', 'userrole.user_id')
            ->select('userrole.role_id')
            ->where('useraccount.id', $id)
            ->get();
        $roles = DB::table('role')
            ->select('role.id', 'role.name')
            ->get();
        return ['userAccount' => $userAccount, 'userAccountUserRoles' => $userAccountUserRoles, 'roles' => $roles];
    }

    public function update($id)
    {
        $this->validate(request(), [
            'name' => 'required|max:50',
            'email' => 'required|max:50',
            'password' => 'max:100',
        ]);
        $userAccount = UserAccount::find($id);
        UserAccount::find($id)->update([
            'name' => request()->input('name'),
            'email' => request()->input('email'),
            'password' => request()->input('password') ? Hash::make(request()->input('password')) : $userAccount->password,
            'active' => Util::getRaw(request()->input('active') == null ? false : request()->input('active'))
        ]);
        DB::table('userrole')
            ->where('userrole.user_id', $id)
            ->delete();
        $roles = request()->input('role_id');
        if ($roles) {
            foreach ($roles as $role) {
                UserRole::create([
                    'user_id' => $id,
                    'role_id' => $role
                ]);
            }
        }
    }

    public function destroy($id)
    {
        UserAccount::find($id)->delete();
    }
}