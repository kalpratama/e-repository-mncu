<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BackgroundImage;
use Illuminate\Support\Facades\Storage;

class BackgroundImageSeeder extends Seeder
{
    public function run()
    {
        // Create some sample background images
        $sampleImages = [
            [
                'name' => 'University Campus',
                'filename' => 'campus.jpg',
                'url' => '/storage/background-images/campus.jpg',
                'path' => 'background-images/campus.jpg',
                'is_active' => true,
                'display_order' => 1
            ],
            [
                'name' => 'Library Interior',
                'filename' => 'library.jpg',
                'url' => '/storage/background-images/library.jpg',
                'path' => 'background-images/library.jpg',
                'is_active' => true,
                'display_order' => 2
            ],
            [
                'name' => 'Graduation Ceremony',
                'filename' => 'graduation.jpg',
                'url' => '/storage/background-images/graduation.jpg',
                'path' => 'background-images/graduation.jpg',
                'is_active' => false,
                'display_order' => 3
            ]
        ];

        foreach ($sampleImages as $image) {
            BackgroundImage::create($image);
        }
    }
}
