<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Author;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;

class ArticleController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'document_type_id' => 'required|exists:document_types,id',
            'abstract' => 'nullable|string',
            'year' => 'nullable|integer|digits:4',
            'publisher' => 'nullable|string|max:100',
            'issn' => 'nullable|string|min:8|max:13',
            'conference_name' => 'nullable|string|max:100',
            'publication_link' => 'nullable|url|max:255',
            'authors' => 'required|array|min:1',
            'authors.*.name' => 'required|string|max:50',
            'authors.*.identifier' => 'nullable|string|max:20',
            'authors.*.program_studi' => 'nullable|string|max:50',
            'authors.*.role' => 'nullable|string|max:10',
            'document_file' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB PDF
            
            'location' => 'nullable|string|max:100',
            'achievement_type' => 'nullable|string|max:100',
            'championship' => 'nullable|string|max:100',
            'champ_ranking' => 'nullable|string|max:100',
        ]);

        return DB::transaction(function () use ($request, $validatedData) {
            $filePath = null;
            if ($request->hasFile('document_file')) {
                $filePath = $request->file('document_file')->store('documents', 'public');
            }
            $document = Document::create(array_merge(
                Arr::except($validatedData, ['authors', 'document_file']),
                ['file_path' => $filePath]
            ));

            $authorIds = [];
            foreach ($validatedData['authors'] as $authorData) {
                $author = Author::updateOrCreate(
                    ['identifier' => $authorData['identifier']],
                    [
                        'name' => $authorData['name'],
                        'program_studi' => $authorData['program_studi'],
                        'role' => $authorData['role'],
                    ]
                );
                $authorIds[] = $author->id;
            }

            $document->authors()->sync($authorIds);

            return response()->json($document->load('authors'), 201);
        });
    }

    public function update(Request $request, Document $document)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'document_type_id' => 'required|exists:document_types,id',
            'abstract' => 'nullable|string',
            'year' => 'required|integer|digits:4',
            'publisher' => 'nullable|string|max:255',
            'issn' => 'nullable|string|max:255',
            'conference_name' => 'nullable|string|max:255',
            'publication_link' => 'nullable|url|max:255',
            'authors' => 'required|array|min:1',
            'authors.*.name' => 'required|string|max:255',
            'authors.*.identifier' => 'nullable|string|max:255',
            'authors.*.program_studi' => 'nullable|string|max:255',
            'authors.*.role' => 'nullable|string|max:255',
            'document_file' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB PDF

            'location' => 'nullable|string|max:100',
            'achievement_type' => 'nullable|string|max:100',
            'championship' => 'nullable|string|max:100',
            'champ_ranking' => 'nullable|string|max:100',
        ]);

        return DB::transaction(function () use ($request, $validatedData, $document) {
            $filePath = null;
            if ($request->hasFile('document_file')) {
                $filePath = $request->file('document_file')->store('documents', 'public');
            }
            // Update the document's main fields
            $document->update(array_merge(Arr::except($validatedData, ['authors', 'document_file']), 
                ['file_path' => $filePath]
            ));

            // Process and sync the authors
            $authorIds = [];
            foreach ($validatedData['authors'] as $authorData) {
                $author = Author::firstOrCreate(
                    ['identifier' => $authorData['identifier']],
                    [
                        'name' => $authorData['name'],
                        'program_studi' => $authorData['program_studi'],
                    ]
                );
                $authorIds[] = $author->id;
            }
            $document->authors()->sync($authorIds);

            return response()->json($document->load('authors'), 200);
        });
    }

    public function show(Document $document)
    {
        return $document->load('authors', 'DocumentType');
    }

    public function download(Document $document)
    {
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            return response()->download(storage_path('app/public/' . $document->file_path));
        }
        return response()->json(['message' => 'File not found.'], 404);
    }

    public function destroy(Document $document)
    {
        // Use a transaction in case the file deletion fails
        return DB::transaction(function () use ($document) {
            // If a file is associated, delete it from storage first
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Delete the document record from the database
            $document->delete();

            return response()->json(['message' => 'Document deleted successfully.'], 200);
        });
    }
}