<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TextVerifiedController extends Controller
{
    public function auth(request $request)
    {
        text_verified_auth();
    }

    public function index(request $request)
    {
        $auth = text_verified_auth();

        $services = text_services();

    }
}
