<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTypes extends Model
{
    use HasFactory;

   public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function children()
    {
        return $this->hasMany(DocumentTypes::class, 'parent_id');
    }
}
