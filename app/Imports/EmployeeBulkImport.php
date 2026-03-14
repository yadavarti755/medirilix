<?php

namespace App\Imports;

use App\DTO\UserDto;
use App\DTO\DivisionPortal\EmployeeDto;
use App\Models\User;
use App\Models\DivisionPortal\Employee;
use App\Services\BulkCredentialsService;
use App\Services\DivisionPortal\EmployeeService;
use App\Services\UserService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EmployeeBulkImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        // First pass: Collect all validation errors
        $validationErrors = [];
        $processedData = [];

        foreach ($rows as $index => $row) {
            // Skip completely empty rows
            if (empty(array_filter($row->toArray()))) {
                continue;
            }

            // Handle Date of Birth conversion
            $dob = null;
            $dobRaw = $row['dob'];
            if (!empty($dobRaw)) {
                try {
                    if (is_numeric($dobRaw)) {
                        $dob = Date::excelToDateTimeObject($dobRaw)->format('Y-m-d');
                    } else {
                        $dob = Carbon::parse($dobRaw)->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    $validationErrors["Row " . ($index + 2)][] = "Invalid date format for DOB: " . $dobRaw;
                }
            }

            $data = [
                'employee_no'   => $row['employee_number'],
                'name'          => $row['name'],
                'designation'   => $row['designation'],
                'dob'           => $dob,
                'pan'           => $row['pan'],
                'email_id'      => $row['email_id'],
                'mobile_number' => $row['mobile_number'],
                'status'        => $row['status'],
            ];

            // Validate the data
            $validator = Validator::make($data, [
                'employee_no'   => 'required|string|unique:employees,employee_no',
                'name'          => 'required|string',
                'designation'   => 'required|string',
                'dob'           => 'nullable|date',
                'pan'           => 'nullable|string|unique:employees,pan',
                'email_id'      => 'required|email|unique:users,email',
                'mobile_number' => 'required|digits:10|unique:users,mobile_number',
                'status'        => 'required|in:0,1',
            ]);

            if ($validator->fails()) {
                $validationErrors["Row " . ($index + 2)] = $validator->errors()->all();
            } else {
                // Store valid data for processing
                $processedData[] = [
                    'row_number' => $index + 2,
                    'data' => $data
                ];
            }
        }

        // If there are validation errors, throw exception with all errors
        if (!empty($validationErrors)) {
            $errorMessage = "Validation failed for the following rows:</br>";
            foreach ($validationErrors as $row => $errors) {
                $errorMessage .= $row . ":</br>";
                foreach ($errors as $error) {
                    $errorMessage .= "  - " . $error . "</br>";
                }
                $errorMessage .= "</br>";
            }
            throw new \Exception($errorMessage);
        }

        // If no validation errors, proceed with database operations
        DB::beginTransaction();

        try {
            $finalProcessedData = [];

            // Process valid data for database insertion
            foreach ($processedData as $item) {
                $data = $item['data'];

                // Store processed data for bulk operations
                $finalProcessedData[] = [
                    'user_data' => [
                        'name'          => strtoupper($data['name']),
                        'email'         => strtolower($data['email_id']),
                        'mobile_number' => $data['mobile_number'],
                        'password'      => Hash::make('Password@123'), // Default password
                        'created_by'    => auth()->user()->id,
                        'updated_by'    => auth()->user()->id,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ],
                    'employee_data' => [
                        'employee_no'   => $data['employee_no'],
                        'designation'   => strtoupper($data['designation']),
                        'dob'           => $data['dob'],
                        'pan'           => strtoupper($data['pan']),
                        'status'        => $data['status'],
                        'created_by'    => auth()->user()->id,
                        'updated_by'    => auth()->user()->id,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]
                ];
            }

            if (!empty($finalProcessedData)) {
                // Option 1: Individual processing (current implementation - better for error handling)
                // foreach ($finalProcessedData as $data) {
                //     // Create user first
                //     $user = User::create($data['user_data']);

                //     if (!$user) {
                //         throw new \Exception("Failed to create user for employee: " . $data['employee_data']['employee_no']);
                //     }

                //     // Assign role to user
                //     $user->assignRole('EMPLOYEE');

                //     // Add user_id to employee data
                //     $data['employee_data']['user_id'] = $user->id;

                //     // Create employee record
                //     $employee = Employee::create($data['employee_data']);

                //     if (!$employee) {
                //         throw new \Exception("Failed to create employee record for: " . $data['employee_data']['employee_no']);
                //     }
                // }

                // Option 2: Bulk insert approach (uncomment for better performance)

                // Bulk insert users
                $userData = array_column($finalProcessedData, 'user_data');
                User::insert($userData);

                // Get inserted users with their IDs
                $emails = array_column($userData, 'email');
                $users = User::whereIn('email', $emails)->get()->keyBy('email');

                // Prepare employee data with user_ids
                $employeeData = [];
                foreach ($finalProcessedData as $data) {
                    $email = $data['user_data']['email'];
                    $user = $users->get($email);

                    if ($user) {
                        $data['employee_data']['user_id'] = $user->id;
                        $employeeData[] = $data['employee_data'];

                        // Assign role
                        $user->assignRole('EMPLOYEE');
                    }
                }

                // Bulk insert employees
                if (!empty($employeeData)) {
                    Employee::insert($employeeData);
                }

                // Now process the jobs for sending credentials
                $userIds = User::whereIn('email', $emails)->pluck('id');
                $bulkCredentialsService = new BulkCredentialsService();
                $bulkCredentialsService->sendCredentialsToUsers($userIds, true, true, 5);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
