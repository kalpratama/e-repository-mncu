<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    // Show all users (with search, sort, and pagination)
    public function index(Request $request)
    {
        // Get query params
        $search = $request->input('search');
        $sortBy = $request->input('sortBy', 'created_at'); // Default: sort by created_at
        $sortOrder = $request->input('sortOrder', 'desc'); // Default: descending
        $perPage = $request->input('perPage', 10); // Default: 10 users per page

        // Query builder
        $query = User::query();

        // If there is a search query
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('username', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('role', 'LIKE', "%{$search}%")
                  ->orWhere('prodi', 'LIKE', "%{$search}%")
                  ->orWhere('id_number', 'LIKE', "%{$search}%");
            });
        }

        // Sorting
        $query->orderBy($sortBy, $sortOrder);

        // Get paginated results
        $users = $query->paginate($perPage);

        return response()->json($users);
    }

    // Show single user by ID
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Validate input
        // $validatedData = $request->validate([
        //     'name' => 'required|string|max:255',
        //     'username' => 'required|string|max:50|unique:users,username,' . $user->id . ',id',
        //     'email' => 'required|email|max:255|unique:users,email,' . $user->id . ',id',
        //     'role' => 'required|in:admin,mahasiswa,dosen',
        //     'prodi' => 'nullable|in:Manajemen,Akuntansi,Pendidikan Matematika,Pendidikan Bahasa Inggris,Sains Komunikasi,Desain Komunikasi Visual,Sistem Informasi,Ilmu Komputer|max:100',
        //     'id_number' => 'nullable|string|max:20|unique:users,id_number,' . $user->id . ',id',
        // ]);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($user->id)
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            'role' => 'required|in:admin,mahasiswa,dosen',
            'prodi' => 'nullable|in:Manajemen,Akuntansi,Pendidikan Matematika,Pendidikan Bahasa Inggris,Sains Komunikasi,Desain Komunikasi Visual,Sistem Informasi,Ilmu Komputer|max:100',
            'id_number' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'id_number')->ignore($user->id)
            ]
        ]);


        // Update user
        $user->update($validatedData);

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }
}
