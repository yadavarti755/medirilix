<?php

namespace App\Services;

use App\DTO\PasswordDto;
use App\DTO\ProfileDto;
use App\DTO\UserDto;
use App\Repositories\UserRepository;
use App\Traits\FileUploadTraits;
use Illuminate\Support\Facades\Config;

class ProfileService
{
    use FileUploadTraits;
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function update(ProfileDto $profileDto, $id)
    {
        // Upload header logo
        if ($profileDto->profile_image) {
            $profileImage = $this->uploadFile($profileDto->profile_image, Config::get('file_paths')['USER_PROFILE_IMAGE_PATH']);
            $profileDto->profile_image = $profileImage['file_name'];
        }
        $data = [
            'name' => $profileDto->name,
            'mobile_number' => $profileDto->mobile_number
        ];

        if ($profileDto->profile_image) {
            $data['profile_image'] = $profileDto->profile_image;
        }
        $user = $this->userRepository->update($data, $id);

        if (!$user) {
            return false;
        }

        return $user;
    }

    public function delete($id)
    {
        return $this->userRepository->delete($id);
    }

    public function changePassword(PasswordDto $passwordDto, $id)
    {
        $user = $this->userRepository->update([
            'password' => $passwordDto->password,
        ], $id);

        if (!$user) {
            return false;
        }

        return $user;
    }
}
