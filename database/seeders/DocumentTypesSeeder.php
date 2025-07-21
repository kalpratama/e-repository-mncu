<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            "Artikel Jurnal",
            "Artikel Jurnal Tidak Terbit",
            "Artikel",
            "Buku",
            "Bab Buku",
            "Skripsi",
            "Tugas Akhir (Project Improvement)",
            "Makalah Konferensi",
            "Modul Pembelajaran",
            "Laporan Penelitian",
            "Laporan Magang Mahasiswa",
            "Poster Ilmiah",
            "Dokumentasi Prestasi Mahasiswa",
        ];

        foreach ($types as $type) {
            DB::table('document_types')->insert([
                'name' => $type,
                'slug' => Str::slug($type), // Creates a URL-friendly slug
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
