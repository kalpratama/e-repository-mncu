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
        // Validate the incoming data, including the new fields and file
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'document_type_id' => 'required|exists:document_types,id',
            'abstract' => 'nullable|string',
            'year' => 'nullable|integer|digits:4',
            'publisher' => 'nullable|string|max:255',
            'issn' => 'nullable|string|max:255',
            'conference_name' => 'nullable|string|max:255',
            'publication_link' => 'nullable|url|max:255',
            'authors' => 'required|array|min:1',
            'authors.*.name' => 'required|string|max:255',
            'authors.*.identifier' => 'nullable|string|max:255',
            'authors.*.program_studi' => 'nullable|string|max:255',
            'authors.*.role' => 'nullable|string|max:255', // New field for author role
            'document_file' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB PDF
        ]);

        // Use a database transaction to ensure data integrity
        return DB::transaction(function () use ($request, $validatedData) {
            $filePath = null;
            if ($request->hasFile('document_file')) {
                // Store the file in 'storage/app/public/documents' and get its path
                $filePath = $request->file('document_file')->store('documents', 'public');
            }
            $documentData = Arr::except($validatedData, ['authors', 'document_file']);
            // $document = Document::create(array_merge($documentData, ['file_path' => $filePath]));
            $document = Document::create(array_merge(
                Arr::except($validatedData, ['authors', 'document_file']),
                ['file_path' => $filePath]
            ));

            // Process the authors
            $authorIds = [];
            foreach ($validatedData['authors'] as $authorData) {
                $author = Author::updateOrCreate( // Use updateOrCreate to handle existing authors
                    ['identifier' => $authorData['identifier']],
                    [
                        'name' => $authorData['name'],
                        'program_studi' => $authorData['program_studi'],
                        'role' => $authorData['role'],
                    ]
                );
                $authorIds[] = $author->id;
            }

            // Attach the authors to the document
            $document->authors()->sync($authorIds);

            // Return a success response with the new document and its authors
            return response()->json($document->load('authors'), 201);
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

    public function update(Request $request, Document $document)
    {
        // Validation rules are similar to the store method
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'document_type_id' => 'required|exists:document_types,id',
            'abstract' => 'nullable|string',
            'year' => 'required|integer|digits:4',
            'publisher' => 'nullable|string|max:255',
            'issn' => 'required|string|max:255',
            'conference_name' => 'nullable|string|max:255',
            'publication_link' => 'nullable|url|max:255',
            'authors' => 'required|array|min:1',
            'authors.*.name' => 'required|string|max:255',
            'authors.*.identifier' => 'nullable|string|max:255',
            'authors.*.program_studi' => 'nullable|string|max:255',
            'authors.*.role' => 'nullable|string|max:255', // New field for author role
        ]);

        return DB::transaction(function () use ($validatedData, $document) {
            // Update the document's main fields
            $document->update(Arr::except($validatedData, ['authors']));

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