<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    protected static $database;

    protected static function getDatabase()
    {
        if (!self::$database) {
            $factory = (new Factory)
                ->withServiceAccount(config('firebase.credentials'))
                ->withDatabaseUri(config('firebase.database.url'));

            self::$database = $factory->createDatabase();
        }
        return self::$database;
    }

    /**
     * Log an action to Firebase 'audit_logs' node.
     *
     * @param string $action The action name (e.g., 'asset_created', 'transaction_approved')
     * @param array $details Additional details about the action (e.    g., asset_id, changes)
     * @param string|null $userId The ID of the user performing the action
     * @param string|null $userName The name of the user performing the action
     * @return void
     */
    public static function log($action, $details = [], $userId = null, $userName = null)
    {
        try {
            $db = self::getDatabase();

            $logEntry = [
                'action' => $action,
                'details' => $details,
                'user_id' => $userId,
                'user_name' => $userName,
                'timestamp' => time(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ];

            $db->getReference('audit_logs')->push($logEntry);
        } catch (\Exception $e) {
            // Fallback to Laravel log if Firebase fails, to ensure we have some record
            Log::error('Audit Log Failed: ' . $e->getMessage(), [
                'action' => $action,
                'details' => $details,
                'user_id' => $userId
            ]);
        }
    }
}
