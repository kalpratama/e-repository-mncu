<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixAuthorRolesAndDocumentTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Step 1: Add role column to authors table
        Schema::table('authors', function (Blueprint $table) {
            $table->enum('role', ['dosen', 'mahasiswa'])->nullable()->after('program_studi');
        });

        // Step 2: Update existing authors with roles based on document types
        // First, update authors who have documents with "dosen" document types
        DB::statement("
            UPDATE authors a
            JOIN author_document ad ON a.id = ad.author_id
            JOIN documents d ON ad.document_id = d.id
            JOIN document_types dt ON d.document_type_id = dt.id
            SET a.role = 'dosen'
            WHERE dt.slug IN ('dosen-aj', 'dosen-ajtt') 
               OR dt.name LIKE '%Dosen%'
        ");

        // Update authors who have documents with "mahasiswa" document types
        DB::statement("
            UPDATE authors a
            JOIN author_document ad ON a.id = ad.author_id
            JOIN documents d ON ad.document_id = d.id
            JOIN document_types dt ON d.document_type_id = dt.id
            SET a.role = 'mahasiswa'
            WHERE dt.slug IN ('mahasiswa-aj', 'mahasiswa-ajtt') 
               OR dt.name LIKE '%Mahasiswa%'
        ");

        // Step 3: Move documents from role-based document types to their parent types
        // Move documents from "Dosen" categories to their parent categories
        $dosenTypes = DB::table('document_types')
            ->whereIn('slug', ['dosen-aj', 'dosen-ajtt'])
            ->orWhere('name', 'like', '%Dosen%')
            ->get();

        foreach ($dosenTypes as $dosenType) {
            if ($dosenType->parent_id) {
                DB::table('documents')
                    ->where('document_type_id', $dosenType->id)
                    ->update(['document_type_id' => $dosenType->parent_id]);
            }
        }

        // Move documents from "Mahasiswa" categories to their parent categories
        $mahasiswaTypes = DB::table('document_types')
            ->whereIn('slug', ['mahasiswa-aj', 'mahasiswa-ajtt'])
            ->orWhere('name', 'like', '%Mahasiswa%')
            ->get();

        foreach ($mahasiswaTypes as $mahasiswaType) {
            if ($mahasiswaType->parent_id) {
                DB::table('documents')
                    ->where('document_type_id', $mahasiswaType->id)
                    ->update(['document_type_id' => $mahasiswaType->parent_id]);
            }
        }

        // Step 4: Remove the role-based document types
        DB::table('document_types')
            ->whereIn('slug', ['dosen-aj', 'dosen-ajtt', 'mahasiswa-aj', 'mahasiswa-ajtt'])
            ->orWhere('name', 'like', '%Dosen%')
            ->orWhere('name', 'like', '%Mahasiswa%')
            ->delete();

        // Step 5: Make role column NOT NULL after data migration
        Schema::table('authors', function (Blueprint $table) {
            $table->enum('role', ['dosen', 'mahasiswa'])->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Recreate the role-based document types
        $ajttId = DB::table('document_types')->where('slug', 'ajtt')->value('id');
        $ajId = DB::table('document_types')->where('slug', 'aj')->value('id');

        if ($ajttId) {
            DB::table('document_types')->insert([
                ['name' => 'Dosen', 'slug' => 'dosen-ajtt', 'parent_id' => $ajttId, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Mahasiswa', 'slug' => 'mahasiswa-ajtt', 'parent_id' => $ajttId, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if ($ajId) {
            DB::table('document_types')->insert([
                ['name' => 'Dosen', 'slug' => 'dosen-aj', 'parent_id' => $ajId, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Mahasiswa', 'slug' => 'mahasiswa-aj', 'parent_id' => $ajId, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Move documents back to role-based types based on author roles
        $dosenAjttId = DB::table('document_types')->where('slug', 'dosen-ajtt')->value('id');
        $mahasiswaAjttId = DB::table('document_types')->where('slug', 'mahasiswa-ajtt')->value('id');
        $dosenAjId = DB::table('document_types')->where('slug', 'dosen-aj')->value('id');
        $mahasiswaAjId = DB::table('document_types')->where('slug', 'mahasiswa-aj')->value('id');

        if ($dosenAjttId && $ajttId) {
            DB::statement("
                UPDATE documents d
                JOIN author_document ad ON d.id = ad.document_id
                JOIN authors a ON ad.author_id = a.id
                SET d.document_type_id = ?
                WHERE d.document_type_id = ? AND a.role = 'dosen'
            ", [$dosenAjttId, $ajttId]);
        }

        if ($mahasiswaAjttId && $ajttId) {
            DB::statement("
                UPDATE documents d
                JOIN author_document ad ON d.id = ad.document_id
                JOIN authors a ON ad.author_id = a.id
                SET d.document_type_id = ?
                WHERE d.document_type_id = ? AND a.role = 'mahasiswa'
            ", [$mahasiswaAjttId, $ajttId]);
        }

        if ($dosenAjId && $ajId) {
            DB::statement("
                UPDATE documents d
                JOIN author_document ad ON d.id = ad.document_id
                JOIN authors a ON ad.author_id = a.id
                SET d.document_type_id = ?
                WHERE d.document_type_id = ? AND a.role = 'dosen'
            ", [$dosenAjId, $ajId]);
        }

        if ($mahasiswaAjId && $ajId) {
            DB::statement("
                UPDATE documents d
                JOIN author_document ad ON d.id = ad.document_id
                JOIN authors a ON ad.author_id = a.id
                SET d.document_type_id = ?
                WHERE d.document_type_id = ? AND a.role = 'mahasiswa'
            ", [$mahasiswaAjId, $ajId]);
        }

        // Remove role column from authors table
        Schema::table('authors', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
}