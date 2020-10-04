<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

function setting()
{
    $data = DB::table('settings')->where('key', 'like', '%' . 'enable_approved' . '%')->first();
    return $data ? $data->value : 0;
}
