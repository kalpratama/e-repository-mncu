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
            'description' => 'nullable|string',
            'date_when' => 'nullable|date',
            'year' => 'nullable|integer|digits:4',
            'publisher' => 'nullable|string|max:100',
            'issn' => 'nullable|string|min:8|max:20',
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
            // $filePath = null;
            // if ($request->hasFile('document_file')) {
            //     $filePath = $request->file('document_file')->store('documents', 'public');
            // }
            $filePath = null;
            if ($request->hasFile('document_file')) {
                $fileName = time() . '_' . $request->file('document_file')->getClientOriginalName();
                $request->file('document_file')->move(public_path(), $fileName);
                $filePath = '/' . $fileName;
            }
            $document = Document::create(array_merge(
                Arr::except($validatedData, ['authors', 'document_file']),
                ['file_path' => $filePath]
            ));

            $authorIds = [];
            foreach ($validatedData['authors'] as $authorData) {
                if (!empty($authorData['identifier'])) {
                    // If identifier exists, just find or create by identifier
                    $author = Author::firstOrCreate(
                        ['identifier' => $authorData['identifier']],
                        [
                            'name' => $authorData['name'],
                            'program_studi' => $authorData['program_studi'],
                            'role' => $authorData['role'],
                        ]
                    );
                } else {
                    // No identifier → create new author first
                    $author = Author::updateOrCreate([
                        'name' => $authorData['name'],
                        'program_studi' => $authorData['program_studi'],
                        'role' => $authorData['role'],
                    ]);

                    // Generate unique default identifier (e.g., 0000 + author_id)
                    $author->identifier = '0000' . str_pad($author->id, 4, '0', STR_PAD_LEFT);
                    $author->save();
                }
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
            'description' => 'nullable|string',
            'date_when' => 'nullable|date',
            'year' => 'nullable|integer|digits:4',
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
                $fileName = time() . '_' . $request->file('document_file')->getClientOriginalName();
                $request->file('document_file')->move(public_path(), $fileName);
                $filePath = '/' . $fileName;
            }
            // Update the document's main fields
            $document->update(array_merge(Arr::except($validatedData, ['authors', 'document_file']), 
                ['file_path' => $filePath]
            ));

            // Process and sync the authors
            $authorIds = [];
            foreach ($validatedData['authors'] as $authorData) {
                if (!empty($authorData['identifier'])) {
                    // If identifier exists, just find or create by identifier
                    $author = Author::firstOrCreate(
                        ['identifier' => $authorData['identifier']],
                        [
                            'name' => $authorData['name'],
                            'program_studi' => $authorData['program_studi'],
                            'role' => $authorData['role'],
                        ]
                    );
                } else {
                    // Update or create based on unique fields
                    $author = Author::updateOrCreate(
                        [
                            'name' => $authorData['name'],
                            'program_studi' => $authorData['program_studi'],
                            'role' => $authorData['role'],
                        ],
                        [
                            'program_studi' => $authorData['program_studi'],
                            'role' => $authorData['role'],
                        ]
                    );

                    // Generate identifier ONLY if it's missing
                    if (empty($author->identifier)) {
                        $author->identifier = '0000' . str_pad($author->id, 4, '0', STR_PAD_LEFT);
                        $author->save();
                    }
                }

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
        $filePath = public_path($document->file_path);

        if ($document->file_path && file_exists($filePath)) {
            return response()->download($filePath);
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