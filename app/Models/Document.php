<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'document_type_id',
        'abstract',
        'year',
        'program_studi',
        'issn',
        'publisher',
        'conference_name',
        'publication_link',
        'file_path',
        'authors', // This will be handled via a pivot table, so it's not directly fill
        // Add other fields here as you expand your form
    ];

    public function documentType()
    {
        return $this->belongsTo(DocumentTypes::class);
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }
}
