<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class SystemStatusController extends Controller
{
    public function index()
    {
        $checks = $this->runHealthChecks();
        $logs = $this->getRecentLogs();
        $queueStats = $this->getQueueStats();

        return inertia('Admin/SystemStatus/Index', [
            'checks' => $checks,
            'logs' => $logs,
            'queueStats' => $queueStats,
            'phpVersion' => PHP_VERSION,
            'laravelVersion' => app()->version(),
            'environment' => config('app.env'),
        ]);
    }

    private function runHealthChecks()
    {
        $checks = [];

        // Database
        try {
            DB::connection()->getPdo();
            $checks[] = ['name' => 'Database', 'status' => 'healthy', 'message' => 'Connected successfully'];
        } catch (\Exception $e) {
            $checks[] = ['name' => 'Database', 'status' => 'critical', 'message' => $e->getMessage()];
        }

        // Cache
        try {
            Cache::put('health_check', true, 10);
            Cache::get('health_check');
            $checks[] = ['name' => 'Cache', 'status' => 'healthy', 'message' => 'Working'];
        } catch (\Exception $e) {
            $checks[] = ['name' => 'Cache', 'status' => 'warning', 'message' => $e->getMessage()];
        }

        // Queue
        try {
            Queue::size();
            $checks[] = ['name' => 'Queue', 'status' => 'healthy', 'message' => 'Running'];
        } catch (\Exception $e) {
            $checks[] = ['name' => 'Queue', 'status' => 'warning', 'message' => $e->getMessage()];
        }

        // Storage
        try {
            $writable = is_writable(storage_path());
            $checks[] = ['name' => 'Storage', 'status' => $writable ? 'healthy' : 'warning', 'message' => $writable ? 'Writable' : 'Not writable'];
        } catch (\Exception $e) {
            $checks[] = ['name' => 'Storage', 'status' => 'critical', 'message' => $e->getMessage()];
        }

        // Storage symlink
        $symlinkExists = is_link(public_path('storage'));
        $checks[] = ['name' => 'Storage Symlink', 'status' => $symlinkExists ? 'healthy' : 'critical', 'message' => $symlinkExists ? 'Exists' : 'Missing - run php artisan storage:link'];

        // PHP extensions
        $requiredExtensions = ['openssl', 'pdo', 'mbstring', 'curl', 'gd', 'xml', 'zip'];
        $missing = array_filter($requiredExtensions, fn($ext) => !extension_loaded($ext));
        $checks[] = [
            'name' => 'PHP Extensions',
            'status' => empty($missing) ? 'healthy' : 'critical',
            'message' => empty($missing) ? 'All required extensions installed' : 'Missing: ' . implode(', ', $missing),
        ];

        return $checks;
    }

    private function getRecentLogs()
    {
        $logPath = storage_path('logs/laravel.log');
        if (!file_exists($logPath)) {
            return [];
        }

        $content = file_get_contents($logPath);
        $lines = array_reverse(explode("\n", $content));
        $logs = [];
        $count = 0;

        foreach ($lines as $line) {
            if (empty(trim($line)) || $count >= 50) break;
            if (str_starts_with($line, '[') || str_starts_with($line, '#')) {
                $logs[] = $line;
                $count++;
            }
        }

        return array_reverse($logs);
    }

    private function getQueueStats()
    {
        try {
            return [
                'pending' => Queue::size(),
                'failed' => DB::table('failed_jobs')->count(),
            ];
        } catch (\Exception $e) {
            return ['pending' => 0, 'failed' => 0];
        }
    }
}
