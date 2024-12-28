<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DatabaseExportController extends Controller
{
    /**
     * Export and download the database.
     *
     * @OA\Get(
     *     path="/download-database",
     *     summary="Export and download the database",
     *     description="Allows admin to export and download the database as an SQL file.",
     *     @OA\Response(response=200, description="File downloaded successfully."),
     *     @OA\Response(response=401, description="Unauthorized access."),
     *     @OA\Response(response=500, description="Failed to export the database.")
     * )
     */
    public function downloadDatabase()
    {
        if (!auth()->check() || Auth::user()->role != 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access.');
        }else{
            $databaseName = env('DB_DATABASE');
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            $host = env('DB_HOST');

            if (!$databaseName || !$username || !$host) {
                return redirect()->back()->with('error', 'Database configuration is incomplete.');
            }

            $filename = 'database_export_' . date('Y-m-d_H-i-s') . '.sql';
            $tempFilePath = storage_path('app/' . $filename);

            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($databaseName),
                escapeshellarg($tempFilePath)
            );

            $output = [];
            $returnVar = null;
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                if (file_exists($tempFilePath)) {
                    unlink($tempFilePath);
                }
                $errorMessage = implode("\n", $output);
                \Log::error("Database export failed: {$errorMessage}");
                return redirect()->back()->with('error', 'Failed to export the database.');
            }
            return response()->download($tempFilePath, $filename)->deleteFileAfterSend(true);
        }
    }
}
