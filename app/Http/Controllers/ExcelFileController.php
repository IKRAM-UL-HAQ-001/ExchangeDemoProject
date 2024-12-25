<?php

namespace App\Http\Controllers;

use App\Models\ExcelFile;
use App\Models\Exchange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;


class ExcelFileController extends Controller
{
    public function index()
    {
        $excelFiles = ExcelFile::paginate(10);
        $exchangeRecords = Exchange::all();

        return view('admin.file.list', compact('excelFiles', 'exchangeRecords'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'exchange_id' => 'required',
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);

        $file = $request->file('excel_file');
        $filePath = $file->store('temp');

        $data = Excel::toArray([], $filePath);

        if (empty($data) || empty($data[0])) {
            return redirect()->back()->with('error', 'The uploaded file is empty or invalid.');
        }

        $rows = array_slice($data[0], 1);

        foreach ($rows as $row) {
            if (isset($row[0], $row[1])) {
                $normalizedPhone = preg_replace('/[^0-9]/', '', $row[1]);

                $exists = ExcelFile::where('customer_phone', $normalizedPhone)->exists();
                if (!$exists) {
                    ExcelFile::insert([
                        'customer_name' => $row[0], // First column
                        'customer_phone' => $normalizedPhone , // Second column
                        'exchange_id' => $request->exchange_id,
                        'created_at' => now(),
                    ]);
                }
            }
        }

        Storage::delete($filePath);

        return redirect()->route('admin.file.list')->with('message', 'Excel file data added successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:excel_files,id'
        ]);


        $excelFile = ExcelFile::findOrFail($request->id);

        Storage::delete('public/excel_files/' . $excelFile->file_name);

        $excelFile->delete();

        return response()->json(['message' => 'Excel file deleted successfully.']);
    }
}
