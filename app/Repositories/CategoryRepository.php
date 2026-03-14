<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public function findForPublic()
    {
        return Category::where('is_published', 1)->get();
    }

    public function findAll()
    {
        return Category::withCount('products')->get();
    }

    public function findForBackend()
    {
        return Category::whereNull('parent_id')->with('children')->orderBy('order')->get();
    }

    public function findAllForEdit($id)
    {
        return Category::where('id', '!=', $id)->orderBy('order')->get();
    }

    public function findById($id)
    {
        return Category::find($id);
    }

    public function create($data)
    {
        return Category::create($data);
    }

    public function update($data, $id)
    {
        $result = Category::find($id);
        if ($result) {
            $result = $result->update($data);
            if (!$result) {
                return false;
            }
            return $result;
        }
        return false;
    }

    public function delete($id)
    {
        $result = Category::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }

    public function findBySlug($slug)
    {
        return Category::where('slug', $slug)->first();
    }

    public function searchByName($query, $limit = 4)
    {
        return Category::where('name', 'LIKE', "%$query%")
            ->limit($limit)
            ->get();
    }
}
