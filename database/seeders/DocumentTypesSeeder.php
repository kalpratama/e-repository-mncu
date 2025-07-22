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
        // Temporarily disable foreign key checks to allow truncating
        Schema::disableForeignKeyConstraints();
        DocumentTypes::truncate();
        Schema::enableForeignKeyConstraints();

        // --- 1. Create All 13 Top-Level Categories ---
        $artikelJurnal = DocumentTypes::create(['name' => 'Artikel Jurnal', 'slug' => Str::slug('Artikel Jurnal')]);
        $artikelJurnalTdk = DocumentTypes::create(['name' => 'Artikel Jurnal Tidak Terbit', 'slug' => Str::slug('Artikel Jurnal Tidak Terbit')]);
        $artikel = DocumentTypes::create(['name' => 'Artikel', 'slug' => Str::slug('Artikel')]);
        DocumentTypes::create(['name' => 'Buku', 'slug' => Str::slug('Buku')]);
        DocumentTypes::create(['name' => 'Bab Buku', 'slug' => Str::slug('Bab Buku')]);
        DocumentTypes::create(['name' => 'Skripsi', 'slug' => Str::slug('Skripsi')]);
        DocumentTypes::create(['name' => 'Tugas Akhir (Project Improvement)', 'slug' => Str::slug('Tugas Akhir Project Improvement')]);
        DocumentTypes::create(['name' => 'Makalah Konferensi', 'slug' => Str::slug('Makalah Konferensi')]);
        DocumentTypes::create(['name' => 'Modul Pembelajaran', 'slug' => Str::slug('Modul Pembelajaran')]);
        $laporanPenelitian = DocumentTypes::create(['name' => 'Laporan Penelitian', 'slug' => Str::slug('Laporan Penelitian')]);
        DocumentTypes::create(['name' => 'Laporan Magang Mahasiswa', 'slug' => Str::slug('Laporan Magang Mahasiswa')]);
        DocumentTypes::create(['name' => 'Poster Ilmiah', 'slug' => Str::slug('Poster Ilmiah')]);
        DocumentTypes::create(['name' => 'Dokumentasi Prestasi Mahasiswa', 'slug' => Str::slug('Dokumentasi Prestasi Mahasiswa')]);

        // --- 2. Create Child & Grandchild Categories ---

        // Children of "Artikel Jurnal"
        $dosenAJ = DocumentTypes::create(['name' => 'Dosen', 'slug' => Str::slug('Dosen AJ'), 'parent_id' => $artikelJurnal->id]);
        $mahasiswaAJ = DocumentTypes::create(['name' => 'Mahasiswa', 'slug' => Str::slug('Mahasiswa AJ'), 'parent_id' => $artikelJurnal->id]);

        // Grandchildren of "Dosen" (under Artikel Jurnal)
        DocumentTypes::create(['name' => 'Jurnal Nasional', 'slug' => Str::slug('Jurnal Nasional Dosen'), 'parent_id' => $dosenAJ->id]);
        DocumentTypes::create(['name' => 'Jurnal Internasional', 'slug' => Str::slug('Jurnal Internasional Dosen'), 'parent_id' => $dosenAJ->id]);
        DocumentTypes::create(['name' => 'Jurnal Internal', 'slug' => Str::slug('Jurnal Internal Dosen'), 'parent_id' => $dosenAJ->id]);

        // Grandchildren of "Mahasiswa" (under Artikel Jurnal)
        DocumentTypes::create(['name' => 'Jurnal Nasional', 'slug' => Str::slug('Jurnal Nasional Mahasiswa'), 'parent_id' => $mahasiswaAJ->id]);
        DocumentTypes::create(['name' => 'Jurnal Internasional', 'slug' => Str::slug('Jurnal Internasional Mahasiswa'), 'parent_id' => $mahasiswaAJ->id]);
        DocumentTypes::create(['name' => 'Jurnal Internal', 'slug' => Str::slug('Jurnal Internal Mahasiswa'), 'parent_id' => $mahasiswaAJ->id]);

        // Children of "Artikel Jurnal yang Tidak Terbit"
        DocumentTypes::create(['name' => 'Dosen', 'slug' => Str::slug('Dosen AJTT'), 'parent_id' => $artikelJurnalTdk->id]);
        DocumentTypes::create(['name' => 'Mahasiswa', 'slug' => Str::slug('Mahasiswa AJTT'), 'parent_id' => $artikelJurnalTdk->id]);
        
        // Children of "Artikel"
        DocumentTypes::create(['name' => 'Majalah', 'slug' => Str::slug('Majalah'), 'parent_id' => $artikel->id]);
        DocumentTypes::create(['name' => 'Koran', 'slug' => Str::slug('Koran'), 'parent_id' => $artikel->id]);

        // Children of "Laporan Penelitian"
        DocumentTypes::create(['name' => 'Dosen', 'slug' => Str::slug('Dosen LP'), 'parent_id' => $laporanPenelitian->id]);
        DocumentTypes::create(['name' => 'Mahasiswa', 'slug' => Str::slug('Mahasiswa LP'), 'parent_id' => $laporanPenelitian->id]);
    }
}
