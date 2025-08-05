<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentTypes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class DocumentTypesSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DocumentTypes::truncate();
        Schema::enableForeignKeyConstraints();

        // --- Create Top-Level Categories ---
        $artikelJurnal = DocumentTypes::create(['name' => 'Artikel Jurnal', 'slug' => Str::slug('artikel-jurnal')]);
        DocumentTypes::create(['name' => 'Artikel Jurnal yang Tidak Terbit', 'slug' => Str::slug('artikel-jurnal-tidak-terbit')]);
        $artikel = DocumentTypes::create(['name' => 'Artikel', 'slug' => Str::slug('artikel')]);
        DocumentTypes::create(['name' => 'Buku', 'slug' => Str::slug('buku')]);
        DocumentTypes::create(['name' => 'Bab Buku', 'slug' => Str::slug('bab-buku')]);
        DocumentTypes::create(['name' => 'Skripsi', 'slug' => Str::slug('skripsi')]);
        DocumentTypes::create(['name' => 'Tugas Akhir (Project Improvement)', 'slug' => Str::slug('tugas-akhir-project-improvement')]);
        DocumentTypes::create(['name' => 'Makalah Konferensi', 'slug' => Str::slug('makalah-konferensi')]);
        DocumentTypes::create(['name' => 'Modul Pembelajaran', 'slug' => Str::slug('modul-pembelajaran')]);
        DocumentTypes::create(['name' => 'Laporan Penelitian', 'slug' => Str::slug('laporan-penelitian')]);
        DocumentTypes::create(['name' => 'Laporan Magang Mahasiswa', 'slug' => Str::slug('laporan-magang-mahasiswa')]);
        DocumentTypes::create(['name' => 'Poster Ilmiah', 'slug' => Str::slug('poster-ilmiah')]);
        DocumentTypes::create(['name' => 'Dokumentasi Prestasi Mahasiswa', 'slug' => Str::slug('dokumentasi-prestasi-mahasiswa')]);

        // --- Create Child Categories ---
        DocumentTypes::create(['name' => 'Jurnal Nasional', 'slug' => Str::slug('jurnal-nasional'), 'parent_id' => $artikelJurnal->id]);
        DocumentTypes::create(['name' => 'Jurnal Internasional', 'slug' => Str::slug('jurnal-internasional'), 'parent_id' => $artikelJurnal->id]);
        DocumentTypes::create(['name' => 'Jurnal Internal', 'slug' => Str::slug('jurnal-internal'), 'parent_id' => $artikelJurnal->id]);

        DocumentTypes::create(['name' => 'Majalah', 'slug' => Str::slug('majalah'), 'parent_id' => $artikel->id]);
        DocumentTypes::create(['name' => 'Koran', 'slug' => Str::slug('koran'), 'parent_id' => $artikel->id]);
    }
}
