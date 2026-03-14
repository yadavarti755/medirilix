<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use Exception;

class RoleService
{
    protected $roleRepository;

    public function __construct()
    {
        $this->roleRepository = new RoleRepository();
    }

    public function findAllWithPermissions()
    {
        return $this->roleRepository->findAllWithPermissions();
    }

    public function findForDatatable()
    {
        return $this->roleRepository->findForDatatable();
    }

    public function findById($id)
    {
        return $this->roleRepository->findById($id);
    }

    public function findByName($name)
    {
        return $this->roleRepository->findByName($name);
    }

    public function create($dto)
    {
        $role = $this->roleRepository->create([
            'name' => $dto->name,
            'landing_page_url' => $dto->landing_page_url
        ]);

        if ($dto->permissions) {
            $this->roleRepository->syncPermissions($role, $dto->permissions);
        }

        return $role;
    }

    public function update($dto, $id)
    {
        $role = $this->roleRepository->update([
            'name' => $dto->name,
            'landing_page_url' => $dto->landing_page_url
        ], $id);

        if ($dto->permissions) {
            $this->roleRepository->syncPermissions($role, $dto->permissions);
        }

        return $role;
    }

    public function delete($id)
    {
        return $this->roleRepository->delete($id);
    }

    public function getAllPermissionsGrouped()
    {
        return $this->roleRepository->getAllPermissionsGrouped();
    }

    public function getAllPermissionsGroupedByScope()
    {
        return $this->roleRepository->getAllPermissionsGroupedByScope();
    }

    public function getPermissionsForRole($role)
    {
        return $this->roleRepository->getPermissionsForRole($role);
    }
}
