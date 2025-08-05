<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Step 1: Add program_studi back to authors table
        Schema::table('authors', function (Blueprint $table) {
            $table->string('program_studi')->nullable()->after('role');
        });

        // Step 2: Migrate program_studi data from documents to authors
        DB::statement("
            UPDATE authors a
            JOIN author_document ad ON a.id = ad.author_id
            JOIN documents d ON ad.document_id = d.id
            SET a.program_studi = d.program_studi
            WHERE a.program_studi IS NULL 
            AND d.program_studi IS NOT NULL
        ");

        // Step 3: Create the missing journal sub-types
        $artikelJurnalId = DB::table('document_types')->where('slug', 'artikel-jurnal')->value('id');
        
        if ($artikelJurnalId) {
            // Insert journal sub-types
            DB::table('document_types')->insert([
                [
                    'name' => 'Jurnal Nasional',
                    'slug' => 'jurnal-nasional',
                    'parent_id' => $artikelJurnalId,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'Jurnal Internasional',
                    'slug' => 'jurnal-internasional',
                    'parent_id' => $artikelJurnalId,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'Jurnal Internal',
                    'slug' => 'jurnal-internal',
                    'parent_id' => $artikelJurnalId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }

        // Step 4: Move documents from "Artikel Jurnal" to appropriate sub-types
        // For now, we'll move all to "Jurnal Nasional" as default
        // You can manually adjust these later through admin interface
        $jurnalNasionalId = DB::table('document_types')->where('slug', 'jurnal-nasional')->value('id');
        
        if ($jurnalNasionalId && $artikelJurnalId) {
            DB::table('documents')
                ->where('document_type_id', $artikelJurnalId)
                ->update(['document_type_id' => $jurnalNasionalId]);
        }

        // Step 5: Remove program_studi from documents table
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('program_studi');
        });

        // Step 6: Set default roles for authors without roles
        DB::table('authors')
            ->whereNull('role')
            ->update(['role' => 'mahasiswa']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Add program_studi back to documents table
        Schema::table('documents', function (Blueprint $table) {
            $table->string('program_studi')->nullable();
        });

        // Move program_studi data back to documents
        DB::statement("
            UPDATE documents d
            JOIN author_document ad ON d.id = ad.document_id
            JOIN authors a ON ad.author_id = a.id
            SET d.program_studi = a.program_studi
            WHERE d.program_studi IS NULL 
            AND a.program_studi IS NOT NULL
        ");

        // Move documents back to parent type
        $artikelJurnalId = DB::table('document_types')->where('slug', 'artikel-jurnal')->value('id');
        $journalSubTypes = DB::table('document_types')
            ->whereIn('slug', ['jurnal-nasional', 'jurnal-internasional', 'jurnal-internal'])
            ->pluck('id');

        if ($artikelJurnalId && $journalSubTypes->count() > 0) {
            DB::table('documents')
                ->whereIn('document_type_id', $journalSubTypes)
                ->update(['document_type_id' => $artikelJurnalId]);
        }

        // Remove journal sub-types
        DB::table('document_types')
            ->whereIn('slug', ['jurnal-nasional', 'jurnal-internasional', 'jurnal-internal'])
            ->delete();

        // Remove program_studi from authors table
        Schema::table('authors', function (Blueprint $table) {
            $table->dropColumn('program_studi');
        });
    }
};