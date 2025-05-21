<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Service;


class AdminController extends Controller
{
    
public function stats()
{
 return response()->json([  
    'users' => User::count(),
    'services' => Service::count(),
    'providers' => User::where('role', 'provider')->count(),
    ]);
}

}
