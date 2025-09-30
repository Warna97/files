<?php

namespace App\Http\Controllers;

use App\Models\Complain;
use App\Repositories\ComplainRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

class ComplainController extends Controller
{
    private $repository;

    public function __construct(ComplainRepository $repository)
    {
        $this->repository = $repository;
        //$this->middleware('auth:sanctum')->except(['store']);
    }
    /**
     * @OA\Get(
     *     path="/complains",
     *     tags={"Complaints"},
     *     summary="Get all complaints",
     *     description="Retrieve a list of all complaints",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Complaints retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index()
    {
        return $this->repository->getComplain();
        // return Complain::all();

    }

    /**
     * @OA\Get(
     *     path="/siteComplainsView",
     *     tags={"Complaints"},
     *     summary="Public: Get all complaints",
     *     description="Public endpoint to retrieve a list of all complaints",
     *     @OA\Response(
     *         response=200,
     *         description="Complaints retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function siteIndex()
    {
        return $this->repository->getComplain();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/complains",
     *     tags={"Complaints"},
     *     summary="Create a new complaint",
     *     description="Submit a new complaint to the system",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"complain"},
     *             @OA\Property(property="complain", type="string", example="Water supply issue in our area"),
     *             @OA\Property(property="tele", type="string", example="0771234567"),
     *             @OA\Property(property="imageList", type="array", @OA\Items(type="string", format="binary"), description="Upload up to 3 images")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Complaint submitted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Complaint submitted successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $customMessages = [
            'tele.size' => 'The Telephone number must be 10 digits',
            'complain.required' => 'The complain is compulsory',
            'complain.max' => 'The complain must be maximum of 1000 characters',
            'imageList.image' => 'Each file must be an image file',
            'imageList.mimes' => 'Each file must be a JPEG image',
            'imageList.max' => 'Each image may not be greater than 10 MB',
            'imageList.array' => 'You can only upload a maximum of 3 images',
            'imageList.*.max' => 'Each image may not be greater than 10 MB', // For individual file size
        ];

        $baseRules = [
            'complain' => 'required|max:1000',
            'tele' => 'size:10',
        ];

        if ($request->has('imageList') && $request->file('imageList') !== null) {
            $baseRules['imageList'] = 'array|max:3'; // Limit to a maximum of 3 images
            $baseRules['imageList.*'] = 'image|mimes:jpeg|max:10240'; // Each image should be jpeg and max 10MB
        }

        $validator = Validator::make($request->all(), $baseRules, $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {
            $response = $this->repository->addComplain($request);
            return response($response, 201);
        }
    }


    /**
     * @OA\Get(
     *     path="/complains/{id}",
     *     tags={"Complaints"},
     *     summary="Get a specific complaint",
     *     description="Retrieve details of a specific complaint",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Complaint ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Complaint retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Complaint not found"
     *     )
     * )
     */
    public function show(Complain $Complain)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Complain $Complain)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/complains/{id}",
     *     tags={"Complaints"},
     *     summary="Update a complaint",
     *     description="Update an existing complaint",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Complaint ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="complain", type="string", example="Updated complaint description"),
     *             @OA\Property(property="tele", type="string", example="0771234567"),
     *             @OA\Property(property="status", type="string", example="resolved")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Complaint updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Complaint updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Complaint not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * @OA\Delete(
     *     path="/complains/{id}",
     *     tags={"Complaints"},
     *     summary="Delete a complaint",
     *     description="Delete an existing complaint",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Complaint ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Complaint deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Complaint deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Complaint not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Complaint not found")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $result = $this->repository->deleteComplain($id);

        if ($result) {
            return response()->json(['message' => 'Complain deleted successfully.']);
        }
        return response()->json(['message' => 'Complain not found.'], 404);
    }

    public function getCount()
    {
        $count = Complain::count();
        $response = [
            "count" => $count,
        ];
        return response($response, 200);
    }
}
