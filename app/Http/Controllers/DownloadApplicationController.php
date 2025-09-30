<?php

namespace App\Http\Controllers;

use App\Models\DownloadApplication;
use App\Repositories\DownloadRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Downloads - Applications",
 *     description="API Endpoints for Application downloads"
 * )
 */
class DownloadApplicationController extends Controller
{
    private $repository;
    public function __construct(DownloadRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *     path="/downloadApplications",
     *     summary="Get all applications",
     *     tags={"Downloads - Applications"},
     *     @OA\Response(
     *         response=200,
     *         description="List of all applications",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="application_year", type="string"),
     *                 @OA\Property(property="application_month", type="string"),
     *                 @OA\Property(property="name_en", type="string"),
     *                 @OA\Property(property="name_si", type="string"),
     *                 @OA\Property(property="name_ta", type="string"),
     *                 @OA\Property(property="file_path_en", type="string"),
     *                 @OA\Property(property="file_path_si", type="string"),
     *                 @OA\Property(property="file_path_ta", type="string")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return DownloadApplication::select('id','application_year','application_month','name_en','name_si','name_ta','file_path_en','file_path_si','file_path_ta')->get();
    }

    /**
     * @OA\Get(
     *     path="/downloadApplications/{id}",
     *     summary="Get a specific application by ID",
     *     tags={"Downloads - Applications"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Application details"),
     *     @OA\Response(response=404, description="Application not found")
     * )
     */
    public function show($id)
    {
        $application = DownloadApplication::select('id','application_year','application_month','name_en','name_si','name_ta','file_path_en','file_path_si','file_path_ta')->find($id);
        if (!$application) {
            return response()->json(['error' => 'Application not found.'], 404);
        }
        return response()->json($application, 200);
    }

    /**
     * @OA\Post(
     *     path="/downloadApplications",
     *     summary="Create a new application",
     *     tags={"Downloads - Applications"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="applicationYear", type="string"),
     *                 @OA\Property(property="applicationMonth", type="string"),
     *                 @OA\Property(property="nameEn", type="string"),
     *                 @OA\Property(property="nameSi", type="string"),
     *                 @OA\Property(property="nameTa", type="string"),
     *                 @OA\Property(property="applicationFileEn", type="string", format="binary"),
     *                 @OA\Property(property="applicationFileSi", type="string", format="binary"),
     *                 @OA\Property(property="applicationFileTa", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Application created successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $customMessages = [
            'applicationYear.required' => 'The Application Year is compulsory',
            'applicationMonth.required' => 'The Application Month is compulsory',
            'nameEn.required' => 'The Name English is compulsory',
            'nameSi.required' => 'The Name Sinhala is compulsory',
            'nameTa.required' => 'The Name Tamil is compulsory',
            'applicationFileEn.mimetypes' => 'The file (English) must be a PDF file',
            'applicationFileEn.max' => 'The file (English) may not be greater than 25 MB',
            'applicationFileSi.mimetypes' => 'The file (Sinhala) must be a PDF file',
            'applicationFileSi.max' => 'The file (Sinhala) may not be greater than 25 MB',
            'applicationFileTa.mimetypes' => 'The file (Tamil) must be a PDF file',
            'applicationFileTa.max' => 'The file (Tamil) may not be greater than 25 MB',
        ];

        $validator = Validator::make($request->all(), [
            'applicationYear' => 'required|string',
            'applicationMonth' => 'required|string',
            'nameEn' => 'required|string',
            'nameSi' => 'required|string',
            'nameTa' => 'required|string',
        ], $customMessages);

        if ($request->hasFile('applicationFileEn')) {
            $validator->sometimes('applicationFileEn', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('applicationFileEn');
            });
        }
        if ($request->hasFile('applicationFileSi')) {
            $validator->sometimes('applicationFileSi', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('applicationFileSi');
            });
        }
        if ($request->hasFile('applicationFileTa')) {
            $validator->sometimes('applicationFileTa', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('applicationFileTa');
            });
        }

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        }

        return $this->repository->addApplication($request);
    }

    /**
     * @OA\Put(
     *     path="/downloadApplications/{id}",
     *     summary="Update an application",
     *     tags={"Downloads - Applications"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="applicationYear", type="string"),
     *                 @OA\Property(property="applicationMonth", type="string"),
     *                 @OA\Property(property="nameEn", type="string"),
     *                 @OA\Property(property="nameSi", type="string"),
     *                 @OA\Property(property="nameTa", type="string"),
     *                 @OA\Property(property="applicationFileEn", type="string", format="binary"),
     *                 @OA\Property(property="applicationFileSi", type="string", format="binary"),
     *                 @OA\Property(property="applicationFileTa", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Application updated successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, $id)
    {
        $customMessages = [
            'applicationYear.required' => 'The Application Year is compulsory',
            'applicationMonth.required' => 'The Application Month is compulsory',
            'nameEn.required' => 'The Name English is compulsory',
            'nameSi.required' => 'The Name Sinhala is compulsory',
            'nameTa.required' => 'The Name Tamil is compulsory',
            'applicationFileEn.mimetypes' => 'The file (English) must be a PDF file',
            'applicationFileEn.max' => 'The file (English) may not be greater than 25 MB',
            'applicationFileSi.mimetypes' => 'The file (Sinhala) must be a PDF file',
            'applicationFileSi.max' => 'The file (Sinhala) may not be greater than 25 MB',
            'applicationFileTa.mimetypes' => 'The file (Tamil) must be a PDF file',
            'applicationFileTa.max' => 'The file (Tamil) may not be greater than 25 MB',
        ];

        $validator = Validator::make($request->all(), [
            'applicationYear' => 'required|string',
            'applicationMonth' => 'required|string',
            'nameEn' => 'required|string',
            'nameSi' => 'required|string',
            'nameTa' => 'required|string',
        ], $customMessages);

        if ($request->hasFile('applicationFileEn')) {
            $validator->sometimes('applicationFileEn', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('applicationFileEn');
            });
        }
        if ($request->hasFile('applicationFileSi')) {
            $validator->sometimes('applicationFileSi', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('applicationFileSi');
            });
        }
        if ($request->hasFile('applicationFileTa')) {
            $validator->sometimes('applicationFileTa', 'mimetypes:application/pdf,application/x-pdf,application/octet-stream,application/x-download,application/acrobat|max:25600', function ($input) {
                return $input->hasFile('applicationFileTa');
            });
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return $this->repository->updateApplication($id, $request);
    }

    /**
     * @OA\Delete(
     *     path="/downloadApplications/{id}",
     *     summary="Delete an application",
     *     tags={"Downloads - Applications"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Application deleted successfully"),
     *     @OA\Response(response=404, description="Application not found")
     * )
     */
    public function destroy($id)
    {
        $response = $this->repository->deleteApplication($id);
        if ($response->status() === 204) {
            return response()->json(['message' => 'Application deleted successfully.'], 200);
        } elseif ($response->status() === 404) {
            return response()->json(['error' => 'Application not found.'], 404);
        }
        return response()->json(['error' => 'Error deleting application.'], 500);
    }
}


