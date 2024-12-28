<?php

namespace App\Http\Controllers;

use App\Models\ExcelFile;
use App\Models\Exchange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExcelFileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/files",
     *     summary="List all Excel files for admin",
     *     description="Retrieve a paginated list of uploaded Excel files and related exchange records for admin users.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response."
     *     )
     * )
     */
    public function index()
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Please log in.',
            ], 401);
        } else {
            $excelFiles = ExcelFile::paginate(10);
            $exchangeRecords = Exchange::all();

            return view('admin.file.list', compact('excelFiles', 'exchangeRecords'));
        }
    }

    /**
     * @OA\Get(
     *     path="/assistant/files",
     *     summary="List all Excel files for assistant",
     *     description="Retrieve a paginated list of uploaded Excel files and related exchange records for assistant users.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response."
     *     )
     * )
     */
    public function assistantIndex()
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Please log in.',
            ], 401);
        } else {
            $excelFiles = ExcelFile::paginate(10);
            $exchangeRecords = Exchange::all();

            return view('assistant.file.list', compact('excelFiles', 'exchangeRecords'));
        }
    }

    /**
     * @OA\Post(
     *     path="/files",
     *     summary="Upload and process an Excel file",
     *     description="Upload an Excel file, validate its data, and save it to the database.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="exchange_id", type="integer", description="Exchange ID associated with the file."),
     *             @OA\Property(property="excel_file", type="string", format="binary", description="The Excel file to be uploaded.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Excel file processed successfully."
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error processing the file."
     *     )
     * )
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Please log in.',
            ], 401);
        } else {
            $validated = $request->validate([
                'exchange_id' => 'required|exists:exchanges,id',
                'excel_file' => 'required|file|mimes:xlsx,xls'
            ]);

            try {
                $file = $validated['excel_file'];
                $filePath = $file->store('temp');

                // Read data from the uploaded file
                $data = Excel::toArray([], $filePath);

                if (empty($data) || empty($data[0])) {
                    return redirect()->back()->with('error', 'The uploaded file is empty or invalid.');
                }

                $rows = array_slice($data[0], 1); // Skip the header row

                foreach ($rows as $row) {
                    if (!isset($row[0], $row[1])) {
                        continue; // Skip rows with missing data
                    }

                    $normalizedPhone = preg_replace('/[^0-9]/', '', $row[1]);

                    // Check if the phone number already exists
                    if (!ExcelFile::where('customer_phone', $normalizedPhone)->exists()) {
                        ExcelFile::create([
                            'customer_name' => $row[0], // First column
                            'customer_phone' => $normalizedPhone, // Second column
                            'exchange_id' => $validated['exchange_id'],
                            'created_at' => now(),
                        ]);
                    }
                }

                Storage::delete($filePath);

                return redirect()->route('admin.file.list')->with('message', 'Excel file data added successfully.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'An error occurred while processing the file: ' . $e->getMessage());
            }
        }
    }

    /**
     * @OA\Delete(
     *     path="/files/{id}",
     *     summary="Delete an Excel file record",
     *     description="Delete a specific Excel file record by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Excel file deleted successfully."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Excel file not found."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error deleting the file."
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Please log in.',
            ], 401);
        } else {
            $validated = $request->validate([
                'id' => 'required|integer|exists:excel_files,id'
            ]);

            try {
                $excelFile = ExcelFile::findOrFail($validated['id']);
                Storage::delete('public/excel_files/' . $excelFile->file_name);
                $excelFile->delete();

                return response()->json(['message' => 'Excel file deleted successfully.']);
            } catch (\Exception $e) {
                return response()->json(['message' => 'An error occurred while deleting the file: ' . $e->getMessage()], 500);
            }
        }
    }
}
