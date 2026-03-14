<?php

namespace App\Repositories;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleRepository
{
    public function findAllWithPermissions()
    {
        return Role::with('permissions')->get();
    }

    public function findForDatatable()
    {
        return Role::with('permissions')->select('id', 'name', 'landing_page_url')->where('name', '!=', 'SUPERADMIN')->get();
    }

    public function findById($id)
    {
        return Role::findOrFail($id);
    }

    public function findByName($name)
    {
        return Role::where('name', $name)->first();
    }

    public function create(array $data)
    {
        return Role::create($data);
    }

    public function update(array $data, $id)
    {
        $role = $this->findById($id);
        $role->update($data);
        return $role;
    }

    public function delete($id)
    {
        $role = $this->findById($id);
        return $role->delete();
    }

    public function syncPermissions($role, $permissions)
    {
        return $role->syncPermissions($permissions);
    }

    public function getAllPermissionsGrouped()
    {
        return Permission::all()->groupBy('group');
    }

    public function getAllPermissionsGroupedByScope()
    {
        return Permission::all()->groupBy(function ($item) {
            return ucfirst(explode(' ', $item->name)[1] ?? 'Miscellaneous');
        });
    }

    public function getPermissionsForRole($role)
    {
        return $role->permissions;
    }
}
