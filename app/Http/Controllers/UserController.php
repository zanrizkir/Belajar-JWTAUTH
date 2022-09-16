<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ],
            [
                'required' => ':attribute harus diisi',
                'email' => ":attribute harus menggunakan '@' ",
                'unique' => ':attribute Sudah Terpakai ',
                'max' => ':attribute maximal :max karakter',
                'min' => ':attribute minimal :min karakter',
                'confirmed' => ':attribute tidak valid',
            ],
        );

        if ($validator->fails()) {
            $resp = [
                'metadata' => [
                    'pesan' => $validator->errors()->first(),
                    'kode' => 422,
                ],
            ];
            return response()->json($resp, 422);
            die();
        }

        $user = User::create(array_merge($validator->validated(), ['password' => bcrypt($request->password)]));
        $token = JWTAuth::fromUser($user);

        return response()->json(
            [
                'pesan' => 'Anda Berhasil Daftar',
                'user' => $user,
                'access_token' => $token,
            ],
            201,
        );
    }

    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required|string|min:8',
            ],
            [
                'required' => ':attribute harus diisi',
                'email' => ":attribute harus menggunakan '@' ",
                'min' => ':attribute minimal :min karakter',
            ],
        );

        if ($validator->fails()) {
            $resp = [
                'metadata' => [
                    'pesan' => $validator->errors()->first(),
                    'kode' => 422,
                ],
            ];
            return response()->json($resp, 422);
            die();
        }

        if (!($token = auth()->attempt($validator->validated()))) {
            $resp = [
                'metadata' => [
                    'pesan' => 'Email Atau Password yang Anda Masukan Tidak Sesuai',
                    'code' => 401,
                ],
            ];
            return response()->json($resp, 401);
        } else {
            $token = JWTAuth::fromUser(auth()->user());
            $resp = [
                'metadata' => [
                    'pesan' => 'Anda Berhasil Login',
                    'code' => 200,
                ],
                'response' => [
                    'token_type' => 'bearer',
                    'user' => auth()->user(),
                    'access_token' => $token,
                ],
            ];
            return response()->json($resp);
        }
        // return response()->json(['error' => 'Unauthorized', 401]);

        // $token = JWTAuth::fromUser(auth()->user());

        // return $this->createNewToken($token);
    }

    // public function createNewToken($token)
    // {
    //     // dd($token,);
    //     return response()->json([
    //         'token_type' => 'bearer',
    //         'user' => auth()->user(),
    //         'access_token' => $token,
    //     ]);
    // }

    public function logout()
    {
        Auth()->logout();
        return response()->json(
            [
                'pesan' => 'Anda Berhasil Keluar',
            ],
            201,
        );
    }
}
