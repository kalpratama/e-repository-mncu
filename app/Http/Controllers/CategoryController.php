<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentTypes;
use App\Models\Document as Documents;

class CategoryController extends Controller
{
    public function show($slug)
    {
        // Find the document type by its slug, or fail with a 404 error
        $documentTypes = DocumentTypes::where('slug', $slug)->with('children.children')->firstOrFail();

        // **** THIS IS THE KEY CHANGE ****
        // 1. Get all descendant IDs using our new helper function.
        $allCategoryIds = $this->getDescendantIds($documentTypes);
        // 2. Add the parent category's own ID to the list.
        $allCategoryIds[] = $documentTypes->id;

        // 3. Fetch all documents where the document_type_id is in our complete list.
        $documents = Documents::with('authors')
                        ->whereIn('document_type_id', $allCategoryIds)
                        ->latest()
                        ->paginate(15);
        return response()->json([
            'category' => $documentTypes,
            'documents' => $documents,
        ]);
    }

    private function getDescendantIds($category)
    {
        $ids = [];
        foreach ($category->children as $child) {
            $ids[] = $child->id;
            // Recursively call the function for grandchildren
            $ids = array_merge($ids, $this->getDescendantIds($child));
        }
        return $ids;
    }
}