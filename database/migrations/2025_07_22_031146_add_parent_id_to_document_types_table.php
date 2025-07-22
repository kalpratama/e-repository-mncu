<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            // Add a nullable foreign key that references the same table
            $table->foreignId('parent_id')->nullable()->constrained('document_types')->onDelete('cascade')->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            // Important to drop the foreign key before the column
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
