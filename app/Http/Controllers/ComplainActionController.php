<?php

namespace App\Http\Controllers;

use App\Models\Complain;
use App\Models\ComplainAction;
use App\Repositories\ComplainRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ComplainActionController extends Controller
{
    private $repository;

    public function __construct(ComplainRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->repository->getComplain();
        // return Complain::all();

    }

    /**
     * @OA\Get(
     *     path="/siteComplainActionsView",
     *     tags={"Complaint Actions"},
     *     summary="Public: Get all complaint actions",
     *     description="Public endpoint to retrieve a list of all complaint actions",
     *     @OA\Response(
     *         response=200,
     *         description="Complaint actions retrieved successfully",
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $customMessages = [
            'action.required' => 'The action is compulsory',
            'action.max' => 'The action must be maximum of 500 characters',
        ];

        $baseRules = [
            'action' => 'required|max:500',
        ];


        $validator = Validator::make($request->all(), $baseRules, $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {
            $response = $this->repository->addAction($request);
            return response($response, 201);
        }
    }

    /**
     * Display the specified resource.
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $customMessages = [
            'action.required' => 'The action is compulsory',
            'action.max' => 'The action must be maximum of 500 characters',
        ];

        $baseRules = [
            'action' => 'required|max:500',
        ];

        $validator = Validator::make($request->all(), $baseRules, $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {
            $response = $this->repository->updateAction($id, $request);
            return response($response, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
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
