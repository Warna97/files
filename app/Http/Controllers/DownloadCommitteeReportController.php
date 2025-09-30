<?php

namespace App\Http\Controllers;

use App\Models\DownloadCommitteeReport;
use App\Repositories\DownloadRepository;
use App\Http\Requests\StoreDownloadCommitteeReportRequest;
use App\Http\Requests\UpdateDownloadCommitteeReportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Downloads - Committee Reports",
 *     description="API Endpoints for Committee Report downloads"
 * )
 */
class DownloadCommitteeReportController extends Controller
{
    private $repository;
    public function __construct(DownloadRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    /**
     * @OA\Get(
     *     path="/downloadReport",
     *     summary="Get all committee reports",
     *     tags={"Downloads - Committee Reports"},
     *     @OA\Response(
     *         response=200,
     *         description="List of all committee reports",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="AllReports", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="report_year", type="string"),
     *                 @OA\Property(property="report_month", type="string"),
     *                 @OA\Property(property="name_en", type="string"),
     *                 @OA\Property(property="name_si", type="string"),
     *                 @OA\Property(property="name_ta", type="string"),
     *                 @OA\Property(property="file_path_en", type="string"),
     *                 @OA\Property(property="file_path_si", type="string"),
     *                 @OA\Property(property="file_path_ta", type="string")
     *             ))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $reports = DownloadCommitteeReport::select('id', 'report_year','report_month','name_en','name_si','name_ta','file_path_en','file_path_si','file_path_ta')->get();
        $response = [
            "AllReports" => $reports,
        ];
        return response($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
//        \Log::info('Data received for update:', $request->all());
        $customMessages = [
            'reportYear.required' => 'The Reports Year is compulsory',
            'reportMonth.required' => 'The Reports Month is compulsory',
            'nameEn.required' => 'The Name English is compulsory',
            'nameSi.required' => 'The Name Sinhala is compulsory',
            'nameTa.required' => 'The Name Tamil is compulsory',
            'reportFileEn.mimetypes' => 'The file (English) must be a PDF file',
            'reportFileEn.max' => 'The file (English) may not be greater than 25 MB',
            'reportFileSi.mimetypes' => 'The file (Sinhala) must be a PDF file',
            'reportFileSi.max' => 'The file (Sinhala) may not be greater than 25 MB',
            'reportFileTa.mimetypes' => 'The file (Tamil) must be a PDF file',
            'reportFileTa.max' => 'The file (Tamil) may not be greater than 25 MB',
        ];

        $validator = Validator::make($request->all(), [
            'reportYear' => 'required|string',
            'reportMonth' => 'required|string',
            'nameEn' => 'required|string',
            'nameSi' => 'required|string',
            'nameTa' => 'required|string',
        ], $customMessages);

        // Validate English file if it's uploaded
        if ($request->hasFile('reportFileEn')) {
            $validator->sometimes('reportFileEn', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('reportFileEn');
            });
        }

        // Validate Sinhala file if it's uploaded
        if ($request->hasFile('reportFileSi')) {
            $validator->sometimes('reportFileSi', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('reportFileSi');
            });
        }

        // Validate Tamil file if it's uploaded
        if ($request->hasFile('reportFileTa')) {
            $validator->sometimes('reportFileTa', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('reportFileTa');
            });
        }

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {
            $response = $this->repository->addReport($request);
            return response($response, 201);
        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/downloadReport/{id}",
     *     summary="Get a specific committee report by ID",
     *     tags={"Downloads - Committee Reports"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Report ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Report details",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="report_year", type="string"),
     *             @OA\Property(property="report_month", type="string"),
     *             @OA\Property(property="name_en", type="string"),
     *             @OA\Property(property="name_si", type="string"),
     *             @OA\Property(property="name_ta", type="string"),
     *             @OA\Property(property="file_path_en", type="string"),
     *             @OA\Property(property="file_path_si", type="string"),
     *             @OA\Property(property="file_path_ta", type="string")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Report not found")
     * )
     */
    public function show($id)
    {
        $report = DownloadCommitteeReport::select('id', 'report_year','report_month','name_en','name_si','name_ta','file_path_en','file_path_si','file_path_ta')->find($id);
        if (!$report) {
            return response()->json(['error' => 'Report not found.'], 404);
        }
        return response()->json($report, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DownloadCommitteeReport $downloadCommitteeReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $customMessages = [
            'reportYear.required' => 'The Reports Year is compulsory',
            'reportMonth.required' => 'The Reports Month is compulsory',
            'nameEn.required' => 'The Name English is compulsory',
            'nameSi.required' => 'The Name Sinhala is compulsory',
            'nameTa.required' => 'The Name Tamil is compulsory',
            'reportFileEn.mimetypes' => 'The file (English) must be a PDF file',
            'reportFileEn.max' => 'The file (English) may not be greater than 25 MB',
            'reportFileSi.mimetypes' => 'The file (Sinhala) must be a PDF file',
            'reportFileSi.max' => 'The file (Sinhala) may not be greater than 25 MB',
            'reportFileTa.mimetypes' => 'The file (Tamil) must be a PDF file',
            'reportFileTa.max' => 'The file (Tamil) may not be greater than 25 MB',
        ];

        $validator = Validator::make($request->all(), [
            'reportYear' => 'required|string',
            'reportMonth' => 'required|string',
            'nameEn' => 'required|string',
            'nameSi' => 'required|string',
            'nameTa' => 'required|string',
        ], $customMessages);

        // Validate English file if it's uploaded
        if ($request->hasFile('reportFileEn')) {
            $validator->sometimes('reportFileEn', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('reportFileEn');
            });
        }

        // Validate Sinhala file if it's uploaded
        if ($request->hasFile('reportFileSi')) {
            $validator->sometimes('reportFileSi', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('reportFileSi');
            });
        }

        // Validate Tamil file if it's uploaded
        if ($request->hasFile('reportFileTa')) {
            $validator->sometimes('reportFileTa', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('reportFileTa');
            });
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $response = $this->repository->updateReport($id, $request);

        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $response = $this->repository->deleteReport($id);

        if ($response->status() === 204) {
            return response()->json(['message' => 'Reports deleted successfully.'], 200);
        } elseif ($response->status() === 404) {
            return response()->json(['error' => 'Reports not found.'], 404);
        } else {
            return response()->json(['error' => 'Error deleting report.'], 500); // Or any other appropriate status code
        }
    }
}
