<?php

namespace App\Repositories;

use App\Models\Slider;

class SliderRepository
{
    public function findForPublic()
    {
        return Slider::with(['category'])->where('is_published', 1)->get();
    }

    public function findAll()
    {
        return Slider::with(['category'])->get();
    }

    public function findById($id)
    {
        return Slider::with(['category'])->find($id);
    }

    public function create($data)
    {
        return Slider::create($data);
    }

    public function update($data, $id)
    {
        $result = Slider::find($id);
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
        $result = Slider::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
