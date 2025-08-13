<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentTypes;

class DocumentTypeController extends Controller
{
    public function index()
    {
        // Fetch all types and eager load their children and grandchildren recursively
        $allTypes = DocumentTypes::with('children.children')->get();

        // Filter for only top-level types (those without a parent)
        $nestedTypes = $allTypes->whereNull('parent_id');

        // Return the structured, nested collection
        return response()->json($nestedTypes->values());
    }
}