<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $users = User::all();

        return Inertia::render('AdminDashboard', [
            'users' => $users,
        ]);
    }
}
