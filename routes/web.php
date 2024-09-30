<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/sms');
Route::redirect('login', '/sms');

Auth::routes(['register' => false, 'login' => false]);
