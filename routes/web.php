<?php

use Illuminate\Support\Facades\Route;
use App\User;
// use JWTAuth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/forgot-password/{email}', function ($email) {
    $user = User::firstWhere('email', $email);
    $token = JWTAuth::fromUser($user);

    $details = [
        'title' => 'Halo ' . $user->name,
        'body' => 'Gunakan link ini untuk resert passwordmu ' . $token,
    ];

    \Mail::to($email)->send(new \App\Mail\MyTestMail($details));

    dd('Email Sudah Terkirim.');
});
