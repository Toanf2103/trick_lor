<?php

namespace App\Services\Site;

use App\Models\Category;

class CategoryService
{
    public function getAll()
    {
        return Category::where('active', 1)->get();
    }

    public function getBySlug($slug)
    {
        return Category::where('slug', $slug)->first();
    }
}