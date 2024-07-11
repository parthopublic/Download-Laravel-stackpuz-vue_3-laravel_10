<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Util;

class UserAccount extends Authenticatable implements JWTSubject
{
    protected $table = 'useraccount';
    protected $primaryKey = 'id';
    protected $fillable = [ 'name', 'email', 'password', 'password_reset_token', 'active' ];
    public $incrementing = true;
    public $timestamps = false;
    protected $rememberTokenName = false;

    public function hasRole($role)
    {
        return DB::table('userrole')
            ->join('role', 'role.id', 'userrole.role_id')
            ->where('userrole.user_id', Auth::id())
            ->where('role.name', $role)
            ->exists();
    }

    public function getMenu() {
        $menu = array_filter(config('menu'), function ($e) {
            if (isset($e['roles'])) {
                foreach (explode(',', $e['roles']) as $role) {
                    if ($this->hasRole($role)) {
                        return $e['show'];
                    }
                }
                return false;
            }
            else {
                return $e['show'];
            }
        });
        return array_map(function ($e) {
            return [
                'title' => $e['title'],
                'path' => $e['path']
            ];
        }, array_values($menu));
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}