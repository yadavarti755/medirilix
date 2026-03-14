<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function findAllWithTrashed($where = null)
    {
        $query = User::with(['roles', 'permissions'])->withTrashed(); // 👈 include deleted users

        if (!empty($where)) {
            $query->where($where);
        }

        return $query->orderBy('id', 'desc')->get();
    }


    public function findAll($where = null)
    {
        if (!empty($where)) {
            return User::with(['roles', 'permissions'])->where($where)->whereHas('roles', function ($query) {
                $query->where('name', '!=', 'SUPERADMIN');
            })->orderBy('id', 'desc')->get();
        }

        return User::with(['roles', 'permissions'])->whereHas('roles', function ($query) {
            $query->where('name', '!=', 'SUPERADMIN');
        })->orderBy('id', 'desc')->get();
    }

    public function findById($id)
    {
        return User::find($id);
    }

    public function create($data)
    {
        return User::create($data);
    }

    public function update($data, $id)
    {
        $user = User::find($id);
        if ($user) {
            $result = $user->update($data);
            if (!$result) {
                return false;
            }
            return $user;
        }
        return false;
    }

    public function delete($id)
    {
        $user = User::find($id);
        if ($user) {
            return $user->delete();
        }
        return false;
    }

    /**
     * Find user by email
     */
    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    /**
     * Update user verification status
     */
    public function updateVerificationStatus($email, $isVerified = 1)
    {
        return User::where('email', $email)
            ->update(['is_verified' => $isVerified]);
    }

    /**
     * Check if email exists
     */
    public function emailExists($email)
    {
        return User::where('email', $email)->exists();
    }

    /**
     * Check if phone number exists
     */
    public function phoneExists($phoneNumber)
    {
        return User::where('phone_number', $phoneNumber)->exists();
    }
}
