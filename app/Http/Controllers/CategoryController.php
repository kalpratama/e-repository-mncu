<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentTypes;
use App\Models\Document;

class CategoryController extends Controller
{
    public function show(Request $request, $slug)
    {
        // Handle "all" special case
        if ($slug === 'all') {
            $years = Document::whereNotNull('year')
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->pluck('year');

            $programStudi = Document::join('author_document', 'documents.id', '=', 'author_document.document_id')
                                    ->join('authors', 'author_document.author_id', '=', 'authors.id')
                                    ->whereNotNull('authors.program_studi')
                                    ->distinct()
                                    ->orderBy('authors.program_studi', 'asc')
                                    ->pluck('authors.program_studi');

            $roles = Document::join('author_document', 'documents.id', '=', 'author_document.document_id')
                            ->join('authors', 'author_document.author_id', '=', 'authors.id')
                            ->whereNotNull('authors.role')
                            ->distinct()
                            ->orderBy('authors.role', 'asc')
                            ->pluck('authors.role');

            $documentsQuery = Document::with(['authors', 'documentType']);
            $documentsQuery = $this->applyFilters($documentsQuery, $request);
            $documents = $documentsQuery->latest()->paginate(15);

            $allCategory = (object) [
                'id' => 'all',
                'name' => 'Semua Dokumen',
                'slug' => 'all',
                'description' => 'Semua dokumen dari semua kategori',
                'children' => collect([])
            ];

            return response()->json([
                'category' => $allCategory,
                'documents' => $documents,
                'years' => $years,
                'program_studi' => $programStudi,
                'roles' => $roles,
            ]);
        }

        // Regular single-category logic
        $documentTypes = DocumentTypes::where('slug', $slug)->with('children.children')->firstOrFail();

        $allCategoryIds = $this->getDescendantIds($documentTypes);
        $allCategoryIds[] = $documentTypes->id;

        $years = Document::whereIn('document_type_id', $allCategoryIds)
                        ->whereNotNull('year')
                        ->distinct()
                        ->orderBy('year', 'desc')
                        ->pluck('year');

        $programStudi = Document::whereIn('document_type_id', $allCategoryIds)
                                ->join('author_document', 'documents.id', '=', 'author_document.document_id')
                                ->join('authors', 'author_document.author_id', '=', 'authors.id')
                                ->whereNotNull('authors.program_studi')
                                ->distinct()
                                ->orderBy('authors.program_studi', 'asc')
                                ->pluck('authors.program_studi');

        $roles = Document::whereIn('document_type_id', $allCategoryIds)
                        ->join('author_document', 'documents.id', '=', 'author_document.document_id')
                        ->join('authors', 'author_document.author_id', '=', 'authors.id')
                        ->whereNotNull('authors.role')
                        ->distinct()
                        ->orderBy('authors.role', 'asc')
                        ->pluck('authors.role');

        $documentsQuery = Document::with('authors')
                                ->whereIn('document_type_id', $allCategoryIds);

        $documentsQuery = $this->applyFilters($documentsQuery, $request);

        $documents = $documentsQuery->latest()->paginate(15);

        return response()->json([
            'category' => $documentTypes,
            'documents' => $documents,
            'years' => $years,
            'program_studi' => $programStudi,
            'roles' => $roles,
        ]);
    }

    private function applyFilters($documentsQuery, Request $request)
    {
        // Handle role filter (legacy support)
        if ($request->has('role') && is_array($request->input('role')) && count($request->input('role')) > 0) {
            $documentsQuery->whereHas('authors', function ($query) use ($request) {
                $query->whereIn('role', $request->input('role'));
            });
        }

        // Handle multiple years filter
        $yearFilters = $request->input('years', []);
        if (!empty($yearFilters) && is_array($yearFilters)) {
            $documentsQuery->whereIn('year', $yearFilters);
        }
        
        // Handle multiple program studi filter
        $prodiFilters = $request->input('prodi', []);
        if (!empty($prodiFilters) && is_array($prodiFilters)) {
            $documentsQuery->whereHas('authors', function ($query) use ($prodiFilters) {
                $query->whereIn('program_studi', $prodiFilters);
            });
        }

        // Handle roles filter
        $roleFilters = $request->input('roles', []);
        if (!empty($roleFilters) && is_array($roleFilters)) {
            $documentsQuery->whereHas('authors', function ($query) use ($roleFilters) {
                $query->whereIn('role', $roleFilters);
            });
        }

        // Handle search query
        $searchQuery = $request->input('search');
        if (!empty($searchQuery)) {
            $documentsQuery->where(function ($query) use ($searchQuery) {
                $query->where('title', 'like', '%' . $searchQuery . '%')
                      ->orWhere('abstract', 'like', '%' . $searchQuery . '%')
                      ->orWhereHas('authors', function ($authorQuery) use ($searchQuery) {
                          $authorQuery->where('name', 'like', '%' . $searchQuery . '%');
                      });
            });
        }

        return $documentsQuery;
    }
    public function getMenuCategories()
    {
        $categories = DocumentTypes::whereNull('parent_id')
                                  ->with('children.children')
                                  ->orderBy('name')
                                  ->get();
        
        // Add "All Documents" as the first item
        $allCategory = (object) [
            'id' => 'all',
            'name' => 'All Documents',
            'slug' => 'all',
            'children' => collect([])
        ];
        
        // Convert to array and prepend "All Documents"
        $menuItems = collect([$allCategory])->merge($categories);
        
        return response()->json($menuItems);
    }

    private function getDescendantIds($category)
    {
        $ids = [];
        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getDescendantIds($child));
        }
        return $ids;
    }
}