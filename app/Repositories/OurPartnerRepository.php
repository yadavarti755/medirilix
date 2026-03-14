<?php

namespace App\Repositories;

use App\Models\OurPartner;

class OurPartnerRepository
{
    public function findForPublic()
    {
        return OurPartner::orderBy('id', 'desc')->get();
    }

    public function findAll()
    {
        return OurPartner::get();
    }

    public function findById($id)
    {
        return OurPartner::find($id);
    }

    public function create($data)
    {
        return OurPartner::create($data);
    }

    public function update($data, $id)
    {
        $result = OurPartner::find($id);
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
        $result = OurPartner::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
