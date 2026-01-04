<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Factory;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        $data = $this->getReportData($request);

        return view('reports.print', [
            'data' => $data,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'user' => $user,
            'title' => 'Laporan ' . ucfirst($request->type)
        ]);
    }

    public function exportCsv(Request $request)
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

        $data = $this->getReportData($request);
        $type = $request->type;

        $response = new StreamedResponse(function () use ($data, $type) {
            $handle = fopen('php://output', 'w');

            // Header
            if ($type == 'transactions') {
                fputcsv($handle, ['ID', 'Asset Name', 'User', 'Status', 'Requested At', 'Return Date']);
            } else {
                fputcsv($handle, ['ID', 'Name', 'Category', 'Serial Number', 'Location', 'Status', 'Created At']);
            }

            // Data
            foreach ($data as $row) {
                if ($type == 'transactions') {
                    fputcsv($handle, [
                        $row['id'] ?? '',
                        $row['asset_name'] ?? '',
                        $row['user_name'] ?? '',
                        $row['status'] ?? '',
                        isset($row['requested_at']) ? date('Y-m-d H:i', $row['requested_at']) : '-',
                        isset($row['expected_return_date']) ? date('Y-m-d', $row['expected_return_date']) : '-'
                    ]);
                } else {
                    fputcsv($handle, [
                        $row['id'] ?? '',
                        $row['name'] ?? '',
                        $row['category'] ?? '',
                        $row['serial_number'] ?? '',
                        $row['location'] ?? '',
                        $row['status'] ?? '',
                        isset($row['created_at']) ? date('Y-m-d H:i', $row['created_at']) : '-'
                    ]);
                }
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="report_' . $type . '_' . date('YmdHis') . '.csv"');

        return $response;
    }

    private function getReportData(Request $request)
    {
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
            $reference = $this->database->getReference('assets');
            $snapshot = $reference->getValue();

            if ($snapshot) {
                foreach ($snapshot as $id => $item) {
                    $itemDate = $item['created_at'] ?? 0;
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

        return $data;
    }
}
