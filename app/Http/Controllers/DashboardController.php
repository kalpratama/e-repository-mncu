<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentTypes;
use App\Models\Document;

class DashboardController extends Controller
{
    public function index()
    {
        $allTypes = DocumentTypes::with('children.children')->get();

        $publicationTypes = $allTypes->whereNull('parent_id');

        $recentDocuments = Document::with('authors', 'documentType')
                                ->latest()
                                ->take(6)
                                ->get();

        // Return both sets of data in a single JSON response
        return response()->json([
            'publicationTypes' => $publicationTypes->values(), // Use values() to reset array keys
            'recentPublications' => $recentDocuments,
        ]);
    }

    /**
     * Search for documents based on a query.
     */
    public function search(Request $request)
    {
        $query = $request->input('q');

        // Perform a search across document titles, abstracts, and author names.
        $documents = Document::with('authors', 'documentType')
            ->where('title', 'LIKE', "%{$query}%")
            ->orWhere('year', 'LIKE', "%{$query}%")
            ->orWhere('location', 'LIKE', "%{$query}%")
            ->orWhere('abstract', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhereHas('authors', function ($authorQuery) use ($query) {
                $authorQuery->where('name', 'LIKE', "%{$query}%");
            })
            ->latest()
            ->take(50) // Limit search results
            ->get();

        return response()->json($documents);
    }
}