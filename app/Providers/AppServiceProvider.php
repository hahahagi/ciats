<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Factory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $pendingCount = 0;
            $activeCount = 0;

            if (Session::has('user')) {
                try {
                    $factory = (new Factory)
                        ->withServiceAccount(config('firebase.credentials'))
                        ->withDatabaseUri(config('firebase.database.url'));
                    $database = $factory->createDatabase();

                    // Count Pending Approvals
                    $pendingRefs = $database->getReference('transactions')
                        ->orderByChild('status')
                        ->equalTo('waiting_approval')
                        ->getValue();
                    $pendingCount = $pendingRefs ? count($pendingRefs) : 0;

                    // Count Active Loans
                    $activeRefs = $database->getReference('transactions')
                        ->orderByChild('status')
                        ->equalTo('active')
                        ->getValue();
                    $activeCount = $activeRefs ? count($activeRefs) : 0;
                } catch (\Exception $e) {
                    // Fail silently
                }
            }

            $view->with('globalCounts', [
                'pending' => $pendingCount,
                'active' => $activeCount
            ]);
        });
    }
}
