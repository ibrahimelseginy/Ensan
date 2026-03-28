<?php

declare(strict_types=1);
namespace App\Http\Controllers;

final class LogisticsDashboardController extends Controller
{
    public function index() { return view('dashboard.logistics'); }
}
