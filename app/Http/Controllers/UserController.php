<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function get_user_roles_and_branches($user_id){
        $user = User::whereId(auth()->user()->id)->first();

        return [
            'roles' => $user->roles,
            'branches' => $user->staff->branches
        ];
    }
}
