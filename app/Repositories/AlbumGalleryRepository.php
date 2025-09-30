<?php

namespace App\Repositories;

use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AlbumGalleryRepository
{
    /**
     * Create a new gallery with images
     */
    public function createGallery(Request $request)
    {
        // Create gallery
        $gallery = Gallery::create([
            'topic_en' => $request->topicEn,
            'topic_si' => $request->topicSi,
            'topic_ta' => $request->topicTa,
        ]);

        $order = 0;
        foreach ($request->file() as $key => $file) {
            if ($request->hasFile($key)) {
                // Generate a unique filename for the image
                $imageName = uniqid() . '_' . $key . '.' . $file->getClientOriginalExtension();

                // Store the image in the 'public' disk under the 'gallery_images' directory
                $file->storeAs('gallery_images', $imageName, 'public');

                // Save the image path to the database with order
                GalleryImage::create([
                    'gallery_id' => $gallery->id,
                    'image_path' => 'gallery_images/' . $imageName,
                    'order' => $order++
                ]);
            }
        }

        return response()->json(['message' => 'Gallery created successfully'], 201);
    }

    /**
     * Get all galleries with their images ordered by order field
     */
    public function getAllGalleries()
    {
        $galleries = Gallery::with(['images' => function ($query) {
            $query->select('id', 'image_path', 'gallery_id', 'order')->orderBy('order');
        }])->select('id', 'topic_en', 'topic_si', 'topic_ta', 'created_at', 'updated_at')->get();

        // Add full URL to each image
        $galleries->each(function ($gallery) {
            $gallery->images->each(function ($image) {
                $image->full_url = asset('storage/' . $image->image_path);
            });
        });

        return response()->json(['AllGalleries' => $galleries], 200);
    }

    /**
     * Get a specific gallery with its images
     */
    public function getGalleryById($id)
    {
        $gallery = Gallery::with(['images' => function ($query) {
            $query->select('id', 'image_path', 'gallery_id', 'order')->orderBy('order');
        }])->find($id);

        if (!$gallery) {
            return response()->json(['error' => 'Gallery not found'], 404);
        }

        // Add full URL to each image
        $gallery->images->each(function ($image) {
            $image->full_url = asset('storage/' . $image->image_path);
        });

        return response()->json(['gallery' => $gallery], 200);
    }

    /**
     * Update gallery information and handle image changes
     */
    public function updateGallery($id, Request $request)
    {
        $gallery = Gallery::findOrFail($id);
        $imagesModified = false;

        // Update gallery information
        $gallery->update([
            'topic_en' => $request->input('topicEn'),
            'topic_si' => $request->input('topicSi'),
            'topic_ta' => $request->input('topicTa'),
        ]);

        // Handle deleted images
        if ($request->has('deleted_images') && !empty($request->input('deleted_images'))) {
            $deletedImages = json_decode($request->input('deleted_images'), true);
            if (is_array($deletedImages)) {
                $this->deleteImages($deletedImages);
                $imagesModified = true;
            }
        }

        // Handle existing images order updates
        if ($request->has('existing_images') && !empty($request->input('existing_images'))) {
            $existingImages = json_decode($request->input('existing_images'), true);
            if (is_array($existingImages)) {
                $this->updateExistingImagesOrder($existingImages);
                $imagesModified = true;
            }
        }

        // Handle new image uploads
        $newImageFiles = [];
        foreach ($request->file() as $key => $file) {
            if (strpos($key, 'new_image_') === 0) {
                $newImageFiles[] = $file;
            }
        }

        if (!empty($newImageFiles)) {
            $this->addNewImages($gallery->id, $newImageFiles);
            $imagesModified = true;
        }

        // Handle images_modified flag and update timestamp if images were changed
        if ($request->has('images_modified') && $request->images_modified === 'true') {
            $imagesModified = true;
        }

        // Update the gallery's updated_at timestamp if images were modified
        if ($imagesModified) {
            $gallery->touch(); // This updates the updated_at field
        }

        return response()->json(['message' => 'Gallery updated successfully'], 200);
    }

    /**
     * Delete individual images
     */
    public function deleteImages(array $imageIds)
    {
        $images = GalleryImage::whereIn('id', $imageIds)->get();
        
        foreach ($images as $image) {
            // Delete physical file
            Storage::disk('public')->delete($image->image_path);
            // Delete database record
            $image->delete();
        }

        return response()->json(['message' => 'Images deleted successfully'], 200);
    }

    /**
     * Add new images to gallery
     */
    public function addNewImages($galleryId, $files)
    {
        // Get the highest order number for this gallery
        $maxOrder = GalleryImage::where('gallery_id', $galleryId)->max('order') ?? -1;
        $order = $maxOrder + 1;

        foreach ($files as $file) {
            // Generate a unique filename for the image
            $imageName = uniqid() . '_' . time() . '_' . $order . '.' . $file->getClientOriginalExtension();

            // Store the image in the 'public' disk under the 'gallery_images' directory
            $file->storeAs('gallery_images', $imageName, 'public');

            // Save the image path to the database with order
            GalleryImage::create([
                'gallery_id' => $galleryId,
                'image_path' => 'gallery_images/' . $imageName,
                'order' => $order++
            ]);
        }
    }

    /**
     * Update image orders
     */
    public function updateImageOrders(array $imageOrders)
    {
        foreach ($imageOrders as $imageId => $order) {
            GalleryImage::where('id', $imageId)->update(['order' => $order]);
        }
    }

    /**
     * Update existing images order based on the existing_images array
     */
    public function updateExistingImagesOrder(array $existingImages)
    {
        foreach ($existingImages as $imageData) {
            if (isset($imageData['id']) && isset($imageData['order'])) {
                GalleryImage::where('id', $imageData['id'])->update(['order' => $imageData['order']]);
            }
        }
    }

    /**
     * Delete entire gallery with all images
     */
    public function deleteGallery($id)
    {
        $gallery = Gallery::find($id);

        if (!$gallery) {
            return response()->json(['error' => 'Gallery not found'], 404);
        }

        // Get all images for this gallery
        $images = GalleryImage::where('gallery_id', $id)->get();

        // Delete all physical files
        foreach ($images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        // Delete all image records
        GalleryImage::where('gallery_id', $id)->delete();

        // Delete gallery
        $gallery->delete();

        return response()->json(['message' => 'Gallery deleted successfully'], 200);
    }

    /**
     * Get gallery count
     */
    public function getGalleryCount()
    {
        return Gallery::count();
    }

    /**
     * Get image count
     */
    public function getImageCount()
    {
        return GalleryImage::count();
    }
}
