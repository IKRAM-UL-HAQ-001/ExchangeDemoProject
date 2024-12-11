<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Auth;
class DatabaseExportController extends Controller
{
    public function downloadDatabase()
    {
        // Set the name for the SQL file
        $filename = 'database_export_' . date('Y-m-d_H-i-s') . '.sql';

        // Command to export the database
        $databaseName = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST');

        $command = "mysqldump --user={$username} --password={$password} --host={$host} {$databaseName}";

        // Execute the command and get the output
        $output = [];
        $returnVar = 0;

        exec($command, $output, $returnVar);

        // Check if the command was successful
        if ($returnVar !== 0) {
            return redirect()->back()->with('error', 'Failed to export database.');
        }

        // Create the SQL file content
        $sqlContent = implode("\n", $output);

        // Return the SQL file as a download
        return Response::make($sqlContent, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
}
