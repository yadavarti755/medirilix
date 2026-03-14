<?php

namespace App\Repositories;

use App\Models\State;

class StateRepository
{
    public function findForPublic($where = [])
    {
        if (!empty($where)) {
            return State::with(['country'])->where($where)->get();
        }
        return State::with(['country'])->get();
    }

    public function findAll($where = [])
    {
        if (!empty($where)) {
            return State::with(['country'])->where($where)->get();
        }
        return State::with(['country'])->get();
    }

    public function findById($id)
    {
        return State::find($id);
    }

    public function create($data)
    {
        return State::create($data);
    }

    public function update($data, $id)
    {
        $result = State::find($id);
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
        $result = State::find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }
}
