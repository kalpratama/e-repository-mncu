<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'identifier', // This could be NIM, NIK, or any other unique identifier
        'program_studi', // Added program_studi field
    ];

    public function documents()
    {
        return $this->belongsToMany(Document::class);
    }
}
