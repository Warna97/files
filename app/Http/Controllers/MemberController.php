<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Http\Controllers\Controller;
use App\Repositories\MemberRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;


class MemberController extends Controller
{
    private $repository;

    public function __construct(MemberRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * @OA\Get(
     *     path="/member",
     *     tags={"Members"},
     *     summary="Get all members",
     *     description="Retrieve a list of all members",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Members retrieved successfully",
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
//        return Member::all();
        return $this->repository->getMembers();

//        return response($members);
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
     *     path="/member",
     *     tags={"Members"},
     *     summary="Create a new member",
     *     description="Add a new member to the system",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nameEn", "nameSi", "nameTa"},
     *             @OA\Property(property="nameEn", type="string", example="John Doe"),
     *             @OA\Property(property="nameSi", type="string", example="ජෝන් ඩෝ"),
     *             @OA\Property(property="nameTa", type="string", example="ஜான் டோ"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="mobileNo", type="string", example="0771234567"),
     *             @OA\Property(property="address", type="string", example="123 Main Street")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Member created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Member created successfully"),
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
            'nameEn.required' => 'The Name English is compulsory',
            'nameSi.required' => 'The Name Sinhala is compulsory',
            'nameTa.required' => 'The Name Tamil is compulsory',
            // 'email.required' => 'The Email is compulsory',
            // 'email.email' => 'The Email must be a valid email address',
            // 'email.unique' => 'The Email has already been taken',
            'tel.required' => 'The Telephone number is compulsory',
            'tel.size' => 'The Telephone number must be 10 digits',
            'division.required' => 'The Division is compulsory',
            'party.required' => 'The Party is compulsory',
            'position.required' => 'The Position is compulsory',
            'position.array' => 'The Position is not an array',
        ];

        // Define the base validation rules
        $baseRules = [
            'nameEn' => 'required|max:250',
            'nameSi' => 'required|max:250',
            'nameTa' => 'required|max:250',
            // 'email' => 'required|email|unique:users,email',
            'tel' => 'required|size:10',
            'division' => 'required',
            'party' => 'required',
            'position' => 'required|array',
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
            $response = $this->repository->createMember($request);
            return response($response, 201);
        }
    }



    /**
     * @OA\Get(
     *     path="/member/{id}",
     *     tags={"Members"},
     *     summary="Get a specific member",
     *     description="Retrieve details of a specific member",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Member ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Member retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Member not found"
     *     )
     * )
     */
    public function show(Member $member)
    {
//
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/member/{id}",
     *     tags={"Members"},
     *     summary="Update a member",
     *     description="Update an existing member",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Member ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nameEn", "nameSi", "nameTa", "email", "tel", "division", "party", "position"},
     *             @OA\Property(property="nameEn", type="string", example="Updated John Doe"),
     *             @OA\Property(property="nameSi", type="string", example="යාවත්කාලීන ජෝන් ඩෝ"),
     *             @OA\Property(property="nameTa", type="string", example="புதுப்பிக்கப்பட்ட ஜான் டோ"),
     *             @OA\Property(property="email", type="string", format="email", example="john.updated@example.com"),
     *             @OA\Property(property="tel", type="string", example="0771234567"),
     *             @OA\Property(property="division", type="string", example="Division 1"),
     *             @OA\Property(property="party", type="string", example="Party A"),
     *             @OA\Property(property="position", type="array", @OA\Items(type="string"), example={"Position 1", "Position 2"}),
     *             @OA\Property(property="img", type="string", format="binary", description="Member image")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Member updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Member updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Member not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $customMessages = [
            'nameEn.required' => 'The Name English is compulsory',
            'nameSi.required' => 'The Name Sinhala is compulsory',
            'nameTa.required' => 'The Name Tamil is compulsory',
            'email.required' => 'The Email is compulsory',
            'email.email' => 'The Email must be a valid email address',
            // 'email.unique' => 'The Email has already been taken',
            'tel.required' => 'The Telephone number is compulsory',
            'tel.size' => 'The Telephone number must be 10 digits',
            'division.required' => 'The Division is compulsory',
            'party.required' => 'The Party is compulsory',
            'position.required' => 'The Position is compulsory',
            'position.array' => 'The Position is not an array',
        ];

        // Define the base validation rules
        $baseRules = [
            'nameEn' => 'required|max:250',
            'nameSi' => 'required|max:250',
            'nameTa' => 'required|max:250',
            'email' => 'required|email  ',
            'tel' => 'required|size:10',
            'division' => 'required',
            'party' => 'required',
            'position' => 'required|array',
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
            $response = $this->repository->updateMember($id, $request);
            return response($response, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->repository->deleteMember($id);

        if ($result) {
            return response()->json(['message' => 'Member deleted successfully.']);
        }
        return response()->json(['message' => 'Member not found.'], 404);
    }

    /**
     * @OA\Get(
     *     path="/members/directory",
     *     tags={"Members"},
     *     summary="Get members directory (Public access)",
     *     description="Retrieve a list of all members for directory viewing. Accessible by admin, officer, and member roles.",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Members directory retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="AllMembers",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="integer", example=1, description="1=Mr, 2=Mrs, 3=Miss, 4=Rev"),
     *                     @OA\Property(property="name_en", type="string", example="Jane Smith"),
     *                     @OA\Property(property="name_si", type="string", example="ජේන් ස්මිත්"),
     *                     @OA\Property(property="name_ta", type="string", example="ஜேன் ஸ்மித்"),
     *                     @OA\Property(property="image", type="string", example="/storage/members/image.jpg"),
     *                     @OA\Property(property="tel", type="string", example="0777654321"),
     *                     @OA\Property(property="divisions_id", type="integer", example=1),
     *                     @OA\Property(property="member_parties_id", type="integer", example=2),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(
     *                         property="division",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="division_en", type="string", example="Colombo District")
     *                     ),
     *                     @OA\Property(
     *                         property="member_party",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="party_en", type="string", example="United National Party")
     *                     ),
     *                     @OA\Property(
     *                         property="member_positions",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="position_en", type="string", example="Councillor")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="email", type="string", example="jane@example.com"),
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
        return $this->repository->getMembers();
    }

    /**
     * @OA\Get(
     *     path="/countMember",
     *     tags={"Members"},
     *     summary="Get member count",
     *     description="Retrieve the total number of members in the system",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Member count retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="count", type="integer", example=150)
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
