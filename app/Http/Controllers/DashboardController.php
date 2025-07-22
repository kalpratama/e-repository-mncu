<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentType;
use App\Models\Document;

class DashboardController extends Controller
{
    /**
     * Fetch initial data for the dashboard.
     */
    public function index()
    {
        // Fetch all document types from the database, ordered by name
        $documentTypes = DocumentType::orderBy('name', 'asc')->get();

        // Fetch the 10 most recently created documents,
        // also loading their author information to avoid extra queries (eager loading).
        $recentDocuments = Document::with('authors')
                                ->latest() // Orders by 'created_at' descending
                                ->take(10)
                                ->get();

        // Return both sets of data in a single JSON response
        return response()->json([
            'publicationTypes' => $documentTypes,
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
        $documents = Document::with('authors')
            ->where('title', 'LIKE', "%{$query}%")
            ->orWhere('abstract', 'LIKE', "%{$query}%")
            ->orWhereHas('authors', function ($authorQuery) use ($query) {
                $authorQuery->where('name', 'LIKE', "%{$query}%");
            })
            ->latest()
            ->take(50) // Limit search results
            ->get();

        return response()->json($documents);
    }
}