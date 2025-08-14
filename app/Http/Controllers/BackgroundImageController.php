<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BackgroundImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BackgroundImageController extends Controller
{
    /**
     * Get active background images for frontend
     */
    public function index()
    {
        $images = BackgroundImage::active()->ordered()->get();
        
        return response()->json($images);
    }

    /**
     * Get all background images for admin (requires authentication)
     */
    public function adminIndex()
    {
        $images = BackgroundImage::ordered()->get();
        
        return response()->json($images);
    }

    /**
     * Store a new background image
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB max
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $image = $request->file('image');
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('background-images', $filename, 'public');
            $url = Storage::disk('public')->url($path);

            $backgroundImage = BackgroundImage::create([
                'name' => $request->name,
                'filename' => $image->getClientOriginalName(),
                'url' => $url,
                'path' => $path,
                'file_size' => $image->getSize(),
                'mime_type' => $image->getMimeType(),
                'is_active' => $request->boolean('is_active', true),
                'display_order' => BackgroundImage::max('display_order') + 1
            ]);

            return response()->json([
                'message' => 'Image uploaded successfully',
                'data' => $backgroundImage
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update background image
     */
    public function update(Request $request, BackgroundImage $backgroundImage)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'is_active' => 'boolean',
            'display_order' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $backgroundImage->update($request->only([
                'name', 'is_active', 'display_order'
            ]));

            return response()->json([
                'message' => 'Image updated successfully',
                'data' => $backgroundImage
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete background image
     */
    public function destroy(BackgroundImage $backgroundImage)
    {
        try {
            $backgroundImage->delete();

            return response()->json([
                'message' => 'Image deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update display order
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array',
            'images.*.id' => 'required|exists:background_images,id',
            'images.*.display_order' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            foreach ($request->images as $imageData) {
                BackgroundImage::where('id', $imageData['id'])
                    ->update(['display_order' => $imageData['display_order']]);
            }

            return response()->json([
                'message' => 'Display order updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update display order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
