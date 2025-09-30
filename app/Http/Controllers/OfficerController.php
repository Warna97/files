<?php

namespace App\Http\Controllers;

use App\Models\Officer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\OfficerRepository;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;


class OfficerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $repository;

    public function __construct(OfficerRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *     path="/officer",
     *     tags={"Officers"},
     *     summary="Get all officers (Admin only)",
     *     description="Retrieve a list of all officers with their details, services, grades, positions, and subjects",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Officers retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="AllOfficers",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="integer", example=1, description="1=Mr, 2=Mrs, 3=Miss, 4=Rev"),
     *                     @OA\Property(property="name_en", type="string", example="John Doe"),
     *                     @OA\Property(property="name_si", type="string", example="ජෝන් ඩෝ"),
     *                     @OA\Property(property="name_ta", type="string", example="ஜான் டோ"),
     *                     @OA\Property(property="image", type="string", example="/storage/officers/image.jpg"),
     *                     @OA\Property(property="tel", type="string", example="0771234567"),
     *                     @OA\Property(property="officer_services_id", type="integer", example=1),
     *                     @OA\Property(property="officer_grades_id", type="integer", example=2),
     *                     @OA\Property(property="officer_positions_id", type="integer", example=3),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(
     *                         property="officer_service",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="sname_en", type="string", example="Administrative Service")
     *                     ),
     *                     @OA\Property(
     *                         property="officer_grade",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="grade_en", type="string", example="Grade 2")
     *                     ),
     *                     @OA\Property(
     *                         property="officer_position",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=3),
     *                         @OA\Property(property="position_en", type="string", example="Assistant Secretary")
     *                     ),
     *                     @OA\Property(
     *                         property="officer_subjects",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="officer_subjects_id", type="integer", example=1),
     *                             @OA\Property(property="subject_en", type="string", example="Public Administration")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="email", type="string", example="john@example.com"),
     *                         @OA\Property(property="status", type="string", example="active")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Admin access required"
     *     )
     * )
     */
    public function index()
    {
        // return Officer::all();
        return $this->repository->getOfficers();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // \Log::info('xxxx: ' . $request);
        $customMessages = [
            'nameEn.required' => 'The Name English is compulsory',
            'nameSi.required' => 'The Name Sinhala is compulsory',
            'nameTa.required' => 'The Name Tamil is compulsory',
            'email.required' => 'The Email is compulsory',
            'email.email' => 'The Email must be a valid email address',
            'email.unique' => 'The Email has already been taken',
            'tel.required' => 'The Telephone number is compulsory',
            'tel.size' => 'The Telephone number must be 10 digits',
            'service.required' => 'The Service is compulsory',
            'grade.required' => 'The Grade is compulsory',
            'duty.array' => 'The Duty is compulsory',
            'status.required' => 'The Status is compulsory',
        ];

        // Define the base validation rules
        $baseRules = [
            'nameEn' => 'required|max:250',
            'nameSi' => 'required|max:250',
            'nameTa' => 'required|max:250',
            // 'email' => 'required|email|unique:users,email',
            'tel' => 'required|size:10',
            'service' => 'required',
            'grade' => 'required',
            'duty' => 'array',
        ];

        // If 'img' exists in the request, apply additional validation rules
        if ($request->has('img')&& $request->file('img') !== null) {
            $baseRules['img'] = 'image|mimes:jpeg,jpg,pjpeg,x-jpeg|max:10240';
            $customMessages['img.image'] = 'The Image must be an image file';
            $customMessages['img.mimes'] = 'The Image must be a JPEG file';
            $customMessages['img.max'] = 'The Image may not be greater than 5 MB';
        }

        $validator = Validator::make($request->all(), $baseRules, $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {
            $response = $this->repository->addOfficer($request);
            return response($response, 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Officer  $officer
     * @return \Illuminate\Http\Response
     */
    public function show(Officer $officer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Officer  $officer
     * @return \Illuminate\Http\Response
     */
    public function edit(Officer $officer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Officer  $officer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customMessages = [
            'nameEn.required' => 'The Name English is compulsory',
            'nameSi.required' => 'The Name Sinhala is compulsory',
            'nameTa.required' => 'The Name Tamil is compulsory',
            'email.required' => 'The Email is compulsory',
            'email.email' => 'The Email must be a valid email address',
            'email.unique' => 'The Email has already been taken',
            'tel.required' => 'The Telephone number is compulsory',
            'tel.size' => 'The Telephone number must be 10 digits',
            'service.required' => 'The Service is compulsory',
            'grade.required' => 'The Grade is compulsory',
            'duty.array' => 'The Duty is compulsory',
            'status.required' => 'The Status is compulsory',
        ];

        // Define the base validation rules
        $baseRules = [
            'nameEn' => 'required|max:250',
            'nameSi' => 'required|max:250',
            'nameTa' => 'required|max:250',
            // 'email' => 'required|email|unique:users,email',
            'tel' => 'required|size:10',
            'service' => 'required',
            'grade' => 'required',
            'duty' => 'array',
        ];

         // If 'img' exists in the request, apply additional validation rules
         if ($request->has('img')&& $request->file('img') !== null) {
            $baseRules['img'] = 'image|mimes:jpeg,jpg,pjpeg,x-jpeg|max:10240';
            $customMessages['img.image'] = 'The Image must be an image file';
            $customMessages['img.mimes'] = 'The Image must be a JPEG file';
            $customMessages['img.max'] = 'The Image may not be greater than 5 MB';
        }

        $validator = Validator::make($request->all(), $baseRules, $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {
            $response = $this->repository->updateOfficer($id, $request);
            return response($response, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Officer  $officer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->repository->deleteOfficer($id);

        if ($result) {
            return response()->json(['message' => 'Officer deleted successfully.']);
        }
        return response()->json(['message' => 'Officer not found.'], 404);
    }

    /**
     * @OA\Get(
     *     path="/officers/directory",
     *     tags={"Officers"},
     *     summary="Get officers directory (Public access)",
     *     description="Retrieve a list of all officers for directory viewing. Accessible by admin, officer, and member roles.",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Officers directory retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="AllOfficers",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="integer", example=1, description="1=Mr, 2=Mrs, 3=Miss, 4=Rev"),
     *                     @OA\Property(property="name_en", type="string", example="John Doe"),
     *                     @OA\Property(property="name_si", type="string", example="ජෝන් ඩෝ"),
     *                     @OA\Property(property="name_ta", type="string", example="ஜான் டோ"),
     *                     @OA\Property(property="image", type="string", example="/storage/officers/image.jpg"),
     *                     @OA\Property(property="tel", type="string", example="0771234567"),
     *                     @OA\Property(property="officer_services_id", type="integer", example=1),
     *                     @OA\Property(property="officer_grades_id", type="integer", example=2),
     *                     @OA\Property(property="officer_positions_id", type="integer", example=3),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(
     *                         property="officer_service",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="sname_en", type="string", example="Administrative Service")
     *                     ),
     *                     @OA\Property(
     *                         property="officer_grade",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="grade_en", type="string", example="Grade 2")
     *                     ),
     *                     @OA\Property(
     *                         property="officer_position",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=3),
     *                         @OA\Property(property="position_en", type="string", example="Assistant Secretary")
     *                     ),
     *                     @OA\Property(
     *                         property="officer_subjects",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="officer_subjects_id", type="integer", example=1),
     *                             @OA\Property(property="subject_en", type="string", example="Public Administration")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="email", type="string", example="john@example.com"),
     *                         @OA\Property(property="status", type="string", example="active")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function directory()
    {
        return $this->repository->getOfficers();
    }

    /**
     * @OA\Get(
     *     path="/countOfficer",
     *     tags={"Officers"},
     *     summary="Get officer count",
     *     description="Retrieve the total number of officers in the system",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Officer count retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="count", type="integer", example=25)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function count()
    {
        $count = $this->repository->getCount();
        $response = [
            "count" => $count,
        ];
        return response($response, 200);
    }
}
