<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentTypes;
use App\Models\Document;

class CategoryController extends Controller
{
    public function show(Request $request, $slug)
    {
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

        $roleFilters = $request->input('roles', []);
        if (!empty($roleFilters) && is_array($roleFilters)) {
            $documentsQuery->whereHas('authors', function ($query) use ($roleFilters) {
                $query->whereIn('role', $roleFilters);
            });
        }

        $documents = $documentsQuery->latest()->paginate(15);

        return response()->json([
            'category' => $documentTypes,
            'documents' => $documents,
            'years' => $years,
            'program_studi' => $programStudi,
            'roles' => $roles,
        ]);
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

// class CategoryController extends Controller
// {
//     public function show(Request $request, $slug)
//     {
//         $documentTypes = DocumentTypes::where('slug', $slug)->with('children.children')->firstOrFail();

//         $allCategoryIds = $this->getDescendantIds($documentTypes);
//         $allCategoryIds[] = $documentTypes->id;

//         $years = Document::whereIn('document_type_id', $allCategoryIds)
//                          ->whereNotNull('year')
//                          ->distinct()
//                          ->orderBy('year', 'desc')
//                          ->pluck('year');
        
//         $programStudi = Document::whereIn('document_type_id', $allCategoryIds)
//                                 ->join('author_document', 'documents.id', '=', 'author_document.document_id')
//                                 ->join('authors', 'author_document.author_id', '=', 'authors.id')
//                                 ->whereNotNull('authors.program_studi')
//                                 ->distinct()
//                                 ->orderBy('authors.program_studi', 'asc')
//                                 ->pluck('authors.program_studi');

//         $documentsQuery = Document::with('authors')
//                               ->whereIn('document_type_id', $allCategoryIds);

//         if ($request->has('year') && is_array($request->input('year'))) {
//             $documentsQuery->whereIn('year', $request->input('year'));
//         }
        
//         if ($request->has('prodi')) {
//             $documentsQuery->whereHas('authors', function ($query) use ($request) {
//                 $query->where('program_studi', $request->input('prodi'));
//             });
//         }

//         $documents = $documentsQuery->latest()->paginate(15);

//         return response()->json([
//             'category' => $documentTypes,
//             'documents' => $documents,
//             'years' => $years,
//             'program_studi' => $programStudi,
//         ]);
//     }

//     private function getDescendantIds($category)
//     {
//         $ids = [];
//         foreach ($category->children as $child) {
//             $ids[] = $child->id;
//             $ids = array_merge($ids, $this->getDescendantIds($child));
//         }
//         return $ids;
//     }
// }