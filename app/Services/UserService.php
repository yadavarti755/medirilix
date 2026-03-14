<?php

namespace App\Services;

use Str;
use Carbon\Carbon;
use App\Repositories\UserRepository;
use App\DTO\UserDto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserService
{
    private $userDto;
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function findAllWithTrashed($where = null)
    {
        return $this->userRepository->findAllWithTrashed($where);
    }

    public function findAll($where = null)
    {
        return $this->userRepository->findAll($where);
    }

    public function findById($id)
    {
        return $this->userRepository->findById($id);
    }

    public function create(UserDto $userDto)
    {
        $user = $this->userRepository->create([
            'name' => $userDto->name,
            'email' => $userDto->email,
            'mobile_number' => $userDto->mobile_number,
            'password' => $userDto->password,
            'created_by' => $userDto->created_by,
            'updated_by' => $userDto->updated_by,
        ]);

        if (!$user) {
            return false;
        }

        $user->assignRole($userDto->roles);

        // Assign permissions directly to user if you want
        // if (!empty($userDto->permissions)) {
        //     $user->syncPermissions($userDto->permissions);
        // }

        // Generate token
        // $token = Str::random(60);
        // $hashedToken = Hash::make($token);

        // Delete any existing reset tokens for this email
        // DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        // // Create new reset token record
        // DB::table('password_reset_tokens')->insert([
        //     'email' => $user->email,
        //     'token' => $hashedToken,
        //     'created_at' => Carbon::now()
        // ]);

        // Encode the token for URL safety
        $encodedToken = base64_encode($token);

        $userRoles = $user->roles->pluck('name')->toArray();
        // if (in_array('EMPLOYEE', $userRoles)) {
        //     $resetUrl = route('employee-reset-password.form', ['token' => $encodedToken]);
        // } else {
        //     $resetUrl = route('backend-reset-password.form', ['token' => $encodedToken]);
        // }

        // Send password reset email after user creation
        // EmailService::sendInitialPasswordResetEmail($user->email, $user->name, $resetUrl, true);

        // $smsService = new SMSService();
        // $smsService->sendPasswordSetup($user->mobile_number, $user->name, $resetUrl, true);

        return $user;
    }

    public function update(UserDto $userDto, $id)
    {
        $user = $this->userRepository->update([
            'name' => $userDto->name,
            'email' => $userDto->email,
            'mobile_number' => $userDto->mobile_number,
            'created_by' => $userDto->created_by,
            'updated_by' => $userDto->updated_by,
        ], $id);

        if (!$user) {
            return false;
        }

        $user->roles()->detach();
        $user->assignRole($userDto->roles);

        // Handle permission syncing
        // if (isset($userDto->permissions) && is_array($userDto->permissions)) {
        //     // Sync only custom permissions
        //     $user->syncPermissions([]);
        //     $user->syncPermissions($userDto->permissions);
        // }

        return $user;
    }

    public function delete($id)
    {
        return $this->userRepository->delete($id);
    }

    public function resetPassword(UserDto $userDto, $id)
    {
        // $user = $this->userRepository->update([
        //     'password' => $userDto->password,
        //     'updated_by' => $userDto->updated_by,
        // ], $id);

        // if (!$user) {
        //     return false;
        // }
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return false;
        }

        // Generate password reset token
        $token = Str::random(60);

        // Store password reset token in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        // Encode the token for URL safety
        $encodedToken = base64_encode($token);

        $userRoles = $user->roles->pluck('name')->toArray();
        $resetUrl = route('backend-reset-password.form', ['token' => $encodedToken]);

        // Send password reset email
        EmailService::sendPasswordResetEmail($user->email, $resetUrl, $user->name);

        // $smsService = new SMSService();
        // $smsService->sendPasswordSetup($user->mobile_number, $user->name, $resetUrl, true);

        return $user;
    }
}
