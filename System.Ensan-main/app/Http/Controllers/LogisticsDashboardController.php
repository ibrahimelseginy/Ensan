<?php

declare(strict_types=1);
namespace App\Http\Controllers;

final class LogisticsDashboardController extends Controller
{
    public function index()
    {
        $delegatesCount = \App\Models\Delegate::count();
        $activeDelegatesCount = \App\Models\Delegate::where('active', true)->count();
        
        $routesCount = \App\Models\TravelRoute::count();
        $tripsCount = \App\Models\DelegateTrip::count();
        $recentTrips = \App\Models\DelegateTrip::with('delegate')->latest('date')->take(5)->get();
        
        $deliveriesCount = \App\Models\KafrElSheikhDelivery::count();
        $servicesCount = \App\Models\KafrElSheikhService::count();
        $totalCost = \App\Models\DelegateTrip::sum(\DB::raw('COALESCE(cost, 0) + COALESCE(fuel_cost, 0) + COALESCE(other_expenses, 0)'));

        return view('dashboard.logistics', compact(
            'delegatesCount',
            'activeDelegatesCount',
            'routesCount',
            'tripsCount',
            'recentTrips',
            'deliveriesCount',
            'servicesCount',
            'totalCost'
        ));
    }
}
