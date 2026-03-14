<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Parent categories
        $parents = [
            'Accessories / Spare Parts',
            'CPM (Continuous Passive Motion)',
            'Electro Therapy Equipment',
            'Electrosurgical Generator / Surgical Cautery',
            'Hair Removal Laser',
            'Laparoscopy Equipment',
            'Laser Therapy Equipment',
            'Longwave Diathermy',
            'O.T Equipment',
            'Physiotherapy Equipment Imported',
            'Shortwave Diathermy Unit',
            'Tens | EMS | NMS | Muscle Stimulator',
            'Traction Unit',
            'Ultrasound Therapy 1 MHZ',
            'Ultrasound Therapy 1 and 3 MHZ',
        ];

        // Create parents and store their IDs
        $parentIds = [];

        foreach ($parents as $name) {
            $parentIds[$name] = Category::create([
                'name'         => $name,
                'slug'         => Str::slug($name),
                'description'  => null,
                'image'        => 'no-image.png',
                'parent_id'    => null,
                'order'        => 0,
                'is_published' => 1,
                'created_by'   => 1,
            ])->id;
        }

        // Child categories
        $children = [
            'O.T Lights' => 'O.T Equipment',
            'O.T Tables' => 'O.T Equipment',
        ];

        foreach ($children as $childName => $parentName) {
            Category::create([
                'name'         => $childName,
                'slug'         => Str::slug($childName),
                'description'  => null,
                'image'        => 'no-image.png',
                'parent_id'    => $parentIds[$parentName],
                'order'        => 0,
                'is_published' => 1,
                'created_by'   => 1,
            ]);
        }
    }
}
