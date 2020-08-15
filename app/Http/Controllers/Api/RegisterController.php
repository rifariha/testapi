<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{

    public function register(Request $request)
    {
        $data = [
            'nama' => $request->name,
            'alamat' => $request->address,
            'email' => $request->email,
            'password' => md5($request->password),
            'nomorTelepon' => $request->phone,
            'level' => 'admin',
        ];
        $users = DB::table('pp_user')->insert($data);
        return response()->json(['status' => true, 'message' => "Registration Success"],200);
    }
}
