<?php

namespace App\Http\Controllers\Secure;

use App\DTO\PasswordDto;
use App\DTO\ProfileDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\ProfileService;
use App\Services\UserService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected $profileService;
    protected $userService;

    public function __construct(ProfileService $profileService, UserService $userService)
    {
        $this->profileService = $profileService;
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Profile';
        setEncryptionKey();
        return view('secure.profile.index', compact('pageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProfileRequest $request)
    {
        try {
            $id = auth()->user()->id;

            $profileDto = new ProfileDto(
                $request->input('name'),
                '',
                $request->input('mobile_number'),
                $request->hasFile('profile_image') ? $request->file('profile_image') : null,
            );
            $result = $this->profileService->update($profileDto, $id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while updating profile.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile details updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $id = auth()->user()->id;

            $newPassword = $request->input('new_password');
            $oldPassword = $request->input('old_password');

            $user = $this->userService->findById($id);

            $email = $user->email; // make sure these are sent in request
            $mobileNumber = $user->mobile_number;

            // 1. Check if new password is same as old password
            if ($oldPassword === $newPassword) {
                return response()->json([
                    'success' => false,
                    'key' => setEncryptionKey(),
                    'message' => 'New password cannot be the same as old password.',
                ], 400);
            }

            // 2. Check if new password matches email or mobile number
            if ($newPassword === $email || $newPassword === $mobileNumber) {
                return response()->json([
                    'success' => false,
                    'key' => setEncryptionKey(),
                    'message' => 'Password should not be your email or mobile number.',
                ], 400);
            }

            // Check for weak/common passwords
            $commonPasswords = ['Password@123', 'Test@123', '12345678', 'Qwerty@123', 'Welcome@123', 'Name@123'];
            if (in_array($newPassword, $commonPasswords)) {
                return response()->json([
                    'success' => false,
                    'key' => setEncryptionKey(),
                    'message' => 'This password is too common. Please choose a stronger one.',
                ], 400);
            }

            // Check for banned substrings using regex
            if (preg_match('/(test|12345|qwerty|password)/i', $newPassword)) {
                return response()->json([
                    'success' => false,
                    'key' => setEncryptionKey(),
                    'message' => 'Password contains a commonly used or insecure word. Please choose a stronger one.',
                ], 400);
            }

            // 4. Enforce strong password rules using regex
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?#&])[A-Za-z\d@$!%*?#&]{8,}$/', $newPassword)) {
                return response()->json([
                    'success' => false,
                    'key' => setEncryptionKey(),
                    'message' => 'Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.',
                ], 400);
            }

            $passwordDto = new PasswordDto($newPassword);
            $result = $this->profileService->changePassword($passwordDto, $id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'key' => setEncryptionKey(),
                    'message' => 'Error while updating password.',
                ], 500);
            }

            if ($user) {
                $user->clearSessionInfo();
            }

            return response()->json([
                'success' => true,
                'key' => setEncryptionKey(),
                'message' => 'Password updated successfully, logging you out. Please login again.',
                'redirect_url' => route('logout')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'key' => setEncryptionKey(),
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function userProfile()
    {
        $pageTitle = 'Profile';
        setEncryptionKey();
        return view('secure.profile.user_profile', compact('pageTitle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function userProfileUpdate(UpdateProfileRequest $request)
    {
        try {
            $id = auth()->user()->id;

            $profileDto = new ProfileDto(
                $request->input('name'),
                '',
                $request->input('mobile_number'),
                $request->hasFile('profile_image') ? $request->file('profile_image') : null,
            );
            $result = $this->profileService->update($profileDto, $id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while updating profile.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile details updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function userChangePassword(ChangePasswordRequest $request)
    {
        try {
            $id = auth()->user()->id;

            $newPassword = $request->input('new_password');
            $oldPassword = $request->input('old_password');

            $user = $this->userService->findById($id);

            $email = $user->email; // make sure these are sent in request
            $mobileNumber = $user->mobile_number;

            // 1. Check if new password is same as old password
            if ($oldPassword === $newPassword) {
                return response()->json([
                    'success' => false,
                    'key' => setEncryptionKey(),
                    'message' => 'New password cannot be the same as old password.',
                ], 400);
            }

            // 2. Check if new password matches email or mobile number
            if ($newPassword === $email || $newPassword === $mobileNumber) {
                return response()->json([
                    'success' => false,
                    'key' => setEncryptionKey(),
                    'message' => 'Password should not be your email or mobile number.',
                ], 400);
            }

            // Check for weak/common passwords
            $commonPasswords = ['Password@123', 'Test@123', '12345678', 'Qwerty@123', 'Welcome@123', 'Name@123'];
            if (in_array($newPassword, $commonPasswords)) {
                return response()->json([
                    'success' => false,
                    'key' => setEncryptionKey(),
                    'message' => 'This password is too common. Please choose a stronger one.',
                ], 400);
            }

            // Check for banned substrings using regex
            if (preg_match('/(test|12345|qwerty|password)/i', $newPassword)) {
                return response()->json([
                    'success' => false,
                    'key' => setEncryptionKey(),
                    'message' => 'Password contains a commonly used or insecure word. Please choose a stronger one.',
                ], 400);
            }

            // 4. Enforce strong password rules using regex
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?#&])[A-Za-z\d@$!%*?#&]{8,}$/', $newPassword)) {
                return response()->json([
                    'success' => false,
                    'key' => setEncryptionKey(),
                    'message' => 'Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.',
                ], 400);
            }

            $passwordDto = new PasswordDto($newPassword);
            $result = $this->profileService->changePassword($passwordDto, $id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'key' => setEncryptionKey(),
                    'message' => 'Error while updating password.',
                ], 500);
            }

            if ($user) {
                $user->clearSessionInfo();
            }

            return response()->json([
                'success' => true,
                'key' => setEncryptionKey(),
                'message' => 'Password updated successfully, logging you out. Please login again.',
                'redirect_url' => route('logout')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'key' => setEncryptionKey(),
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
