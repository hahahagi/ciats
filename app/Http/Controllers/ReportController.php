<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Factory;
use Illuminate\Routing\Controller;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->withDatabaseUri(config('firebase.database.url'));

        $this->database = $factory->createDatabase();

        $this->middleware(function ($request, $next) {
            if (!Session::has('user')) {
                return redirect('/login');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $user = Session::get('user');
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Unauthorized');
        }

        return view('reports.index', [
            'user' => $user,
            'title' => 'Laporan'
        ]);
    }

    public function generate(Request $request)
    {
        $user = Session::get('user');
        if (!in_array($user['role'], ['operator', 'admin'])) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:transactions,assets',
            'status' => 'nullable|string'
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay()->timestamp;
        $endDate = Carbon::parse($request->end_date)->endOfDay()->timestamp;
        $type = $request->type;
        $status = $request->status;

        $data = [];

        if ($type == 'transactions') {
            $reference = $this->database->getReference('transactions');
            $snapshot = $reference->getValue();

            if ($snapshot) {
                foreach ($snapshot as $id => $item) {
                    $itemDate = $item['requested_at'] ?? 0;
                    if ($itemDate >= $startDate && $itemDate <= $endDate) {
                        if ($status && ($item['status'] ?? '') != $status) {
                            continue;
                        }
                        $item['id'] = $id;
                        $data[] = $item;
                    }
                }
                // Sort by date desc
                usort($data, function ($a, $b) {
                    return ($b['requested_at'] ?? 0) <=> ($a['requested_at'] ?? 0);
                });
            }
        } else {
            // Assets Report (Snapshot of current state, filtered by creation date if needed, or just all)
            // Usually asset reports are "Current Inventory". Let's assume we want all assets created within range OR just all assets if range is ignored for assets.
            // But the prompt asks for reports, usually transaction history.
            // Let's filter assets by created_at if available, or just list them.

            $reference = $this->database->getReference('assets');
            $snapshot = $reference->getValue();

            if ($snapshot) {
                foreach ($snapshot as $id => $item) {
                    $itemDate = $item['created_at'] ?? 0;
                    // Optional: Filter by date created? Or just show all?
                    // Let's filter by date created for consistency with the form
                    if ($itemDate >= $startDate && $itemDate <= $endDate) {
                        if ($status && ($item['status'] ?? '') != $status) {
                            continue;
                        }
                        $item['id'] = $id;
                        $data[] = $item;
                    }
                }
            }
        }

        return view('reports.print', [
            'data' => $data,
            'type' => $type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'user' => $user,
            'title' => 'Laporan ' . ucfirst($type)
        ]);
    }
}
