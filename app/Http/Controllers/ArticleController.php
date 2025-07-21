<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Author;
use Illuminate\Support\Facades\DB;
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
            'program_studi' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'issn' => 'nullable|string|max:255',
            'conference_name' => 'nullable|string|max:255',
            'publication_link' => 'nullable|url|max:255',
            'authors' => 'required|array|min:1',
            'authors.*.name' => 'required|string|max:255',
            'authors.*.identifier' => 'nullable|string|max:255',
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

            // Create the document from the validated data, including the file path
            $document = Document::create(array_merge($documentData, ['file_path' => $filePath]));

            // Process the authors
            $authorIds = [];
            foreach ($validatedData['authors'] as $authorData) {
                // Find or create the author based on their identifier
                $author = Author::firstOrCreate(
                    ['identifier' => $authorData['identifier']],
                    ['name' => $authorData['name']]
                );
                $authorIds[] = $author->id;
            }

            // Attach the authors to the document
            $document->authors()->sync($authorIds);

            // Return a success response with the new document and its authors
            return response()->json($document->load('authors'), 201);
        });
    }
}
