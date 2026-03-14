<?php

namespace App\Repositories;

use App\Models\Page;

class PageRepository
{
    public function findAllForDatatable()
    {
        return Page::with(['menu'])->orderBy('id', 'desc');
    }

    public function findAll()
    {
        return Page::with(['menu'])->orderBy('id', 'desc')->get();
    }

    public function findById($id)
    {
        return Page::find($id);
    }

    public function findBySlug($slug)
    {
        return Page::where([
            'slug' => $slug
        ])->first();
    }

    public function create($data)
    {
        return Page::create($data);
    }

    public function update($data, $id)
    {
        $result = Page::find($id);
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
        $result = Page::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }

    public function search(array $fields, string $term)
    {
        return Page::where(function ($query) use ($fields, $term) {
            foreach ($fields as $field) {
                $query->orWhere($field, 'LIKE', '%' . $term . '%');
            }
        })
            ->orderBy('id', 'desc')
            ->get();
    }
}
