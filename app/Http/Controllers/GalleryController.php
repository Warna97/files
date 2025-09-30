<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use App\Repositories\AlbumGalleryRepository;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Gallery",
 *     description="API Endpoints for Gallery management"
 * )
 */
class GalleryController extends Controller
{
    private $repository;
    public function __construct(AlbumGalleryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *     path="/gallery",
     *     summary="Get all galleries",
     *     tags={"Gallery"},
     *     @OA\Response(
     *         response=200,
     *         description="List of all galleries",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="galleries", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="topic_en", type="string"),
     *                 @OA\Property(property="topic_si", type="string"),
     *                 @OA\Property(property="topic_ta", type="string"),
     *                 @OA\Property(property="images", type="array", @OA\Items(type="object"))
     *             ))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return $this->repository->getAllGalleries();
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
     * @OA\Post(
     *     path="/api/gallery",
     *     summary="Create a new gallery",
     *     tags={"Gallery"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="topicEn", type="string"),
     *             @OA\Property(property="topicSi", type="string"),
     *             @OA\Property(property="topicTa", type="string"),
     *             @OA\Property(property="images", type="array", @OA\Items(type="string", format="binary"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Gallery created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="topic_en", type="string"),
     *             @OA\Property(property="topic_si", type="string"),
     *             @OA\Property(property="topic_ta", type="string"),
     *             @OA\Property(property="images", type="array", @OA\Items(type="object"))
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
            'topicEn.required' => 'The topic in English field is required.',
            'topicSi.required' => 'The topic in Sinhala field is required.',
            'topicTa.required' => 'The topic in Tamil field is required.',
            'image_*.*.required' => 'Please upload at least one image.',
            'image_*.*.image' => 'The file must be an image.',
            'image_*.*.mimes' => 'The image must be a JPEG file.',
            'image_*.*.max' => 'The image must be less than 10MB in size.',
        ];

        $rules = [
            'topicEn' => 'required',
            'topicSi' => 'required',
            'topicTa' => 'required',
        ];

        // Dynamically add rules for each image
        foreach ($request->file() as $key => $files) {
            $rules[$key . '.*'] = 'required|image|mimes:jpeg,jpg|max:10240'; // Validate each image separately
        }

        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {
            return $this->repository->createGallery($request);
        }
    }

    /**
     * @OA\Get(
     *     path="/gallery/{id}",
     *     summary="Get specific gallery by ID",
     *     tags={"Gallery"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Gallery ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Gallery details",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="topic_en", type="string"),
     *             @OA\Property(property="topic_si", type="string"),
     *             @OA\Property(property="topic_ta", type="string"),
     *             @OA\Property(property="images", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Gallery not found"
     *     )
     * )
     */
    public function show($id)
    {
        return $this->repository->getGalleryById($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function edit(Gallery $gallery)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/api/gallery/{id}",
     *     summary="Update an existing gallery",
     *     tags={"Gallery"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Gallery ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="topicEn", type="string"),
     *             @OA\Property(property="topicSi", type="string"),
     *             @OA\Property(property="topicTa", type="string"),
     *             @OA\Property(property="images", type="array", @OA\Items(type="string", format="binary"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Gallery updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="topic_en", type="string"),
     *             @OA\Property(property="topic_si", type="string"),
     *             @OA\Property(property="topic_ta", type="string"),
     *             @OA\Property(property="images", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Gallery not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $customMessages = [
            'topicEn.required' => 'The topic in English field is required.',
            'topicSi.required' => 'The topic in Sinhala field is required.',
            'topicTa.required' => 'The topic in Tamil field is required.',
        ];

        $rules = [
            'topicEn' => 'required',
            'topicSi' => 'required',
            'topicTa' => 'required',
        ];

        // Add validation for new images if provided
        foreach ($request->file() as $key => $file) {
            if (strpos($key, 'new_image_') === 0) {
                $rules[$key] = 'image|mimes:jpeg,jpg|max:10240';
            }
        }

        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        } else {
            return $this->repository->updateGallery($id, $request);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/gallery/{id}",
     *     summary="Delete a gallery",
     *     tags={"Gallery"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Gallery ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Gallery deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Gallery not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        return $this->repository->deleteGallery($id);
    }
}
