<?php

namespace App\Repositories;

use App\Models\Menu;

class MenuRepository
{
    public function findAll()
    {
        return Menu::orderBy('order')->get();
    }

    public function findForIndex($locationCode)
    {
        return Menu::whereNull('parent_id')
            ->where('location', $locationCode)
            ->with('children')
            ->orderBy('order')
            ->get();
    }

    public function findAllExcluding($id)
    {
        return Menu::where('id', '!=', $id)->get();
    }

    public function findByLocation($location)
    {
        return Menu::where('location', $location)->orderBy('order', 'ASC')->get();
    }

    public function findAllById($parentId)
    {
        return Menu::where('parent_id', $parentId)->orderBy('order', 'ASC')->get();
    }

    public function findById($id)
    {
        return Menu::find($id);
    }

    public function findByUrl($url)
    {
        return Menu::where([
            'url' => $url
        ])->first();
    }

    public function create($data)
    {
        return Menu::create($data);
    }

    public function update($data, $id)
    {
        $menu = Menu::find($id);
        if ($menu) {
            $result = $menu->update($data);
            if (!$result) {
                return false;
            }
            return $menu;
        }
        return false;
    }

    public function delete($id)
    {
        $menu = Menu::find($id);
        if ($menu) {
            return $menu->delete();
        }
        return false;
    }

    public function findByUrlWithParents($url)
    {
        $menu = Menu::where('url', $url)->first();

        if ($menu) {
            $menu->parents = $menu->getAllParents(); // Attach the parents collection
        }

        return $menu;
    }
}
