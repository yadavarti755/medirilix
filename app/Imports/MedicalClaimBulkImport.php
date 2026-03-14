<?php

namespace App\Imports;

use App\DTO\UserDto;
use App\DTO\DivisionPortal\MedicalClaimDto;
use App\Models\User;
use App\Models\DivisionPortal\MedicalClaim;
use App\Services\DivisionPortal\EmployeeService;
use App\Services\DivisionPortal\MedicalClaimService;
use App\Services\UserService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class MedicalClaimBulkImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                // Skip completely empty rows
                if (empty(array_filter($row->toArray()))) {
                    continue;
                }

                $billDateRaw = $row['bill_date'];
                if (!empty($billDateRaw)) {
                    if (is_numeric($billDateRaw)) {
                        $billDate = Date::excelToDateTimeObject($billDateRaw)->format('Y-m-d');
                    } else {
                        $billDate = Carbon::parse($billDateRaw)->format('Y-m-d');
                    }
                }

                $data = [
                    'employee_no'   => $row['employee_number'],
                    'bill_number'   => $row['bill_number'],
                    'no_of_bills'   => $row['no_of_bills'],
                    'bill_date'     => $billDate,
                    'claimed_amount'           => $row['claimed_amount'],
                    'deduction_amount'      => $row['deduction_amount'],
                    'final_bill_amount' => $row['final_bill_amount'],
                    'status'        => $row['status'],
                ];

                $validator = Validator::make($data, [
                    'employee_no'    => 'required|max:255',
                    'bill_number'    => 'required|max:255',
                    'no_of_bills'    => 'required|integer|min:1',
                    'bill_date'      => 'required|date',
                    'claimed_amount' => 'required|numeric|min:0',
                    'deduction_amount' => 'nullable|numeric|min:0',
                    'final_bill_amount' => 'required|numeric|min:0',
                    'status'         => 'required',
                ]);

                if ($validator->fails()) {
                    throw new \Exception("Row " . ($index + 2) . " failed validation: " . json_encode($validator->errors()->all()));
                }

                // Check if employee exists
                $employeeService = new EmployeeService();
                $employee = $employeeService->findByEmployeeNo($data['employee_no']);
                if (!$employee) {
                    throw new \Exception("Employee does not exist with this employee number: " . $data['employee_no']);
                }

                // Final bill amount should not be greater than claim amount
                if (isset($data['final_bill_amount']) && isset($data['claimed_amount']) && $data['final_bill_amount'] > $data['claimed_amount']) {
                    throw new \Exception("Final bill amount should be not greater than the claim amount for employee " . $data['employee_no']);
                }

                // Bill date can not be future date.
                if (isset($data['bill_date']) && Carbon::parse($data['bill_date'])->isFuture()) {
                    throw new \Exception("Bill date cannot be a future date for employee " . $data['employee_no']);
                }

                $medicalClaimService = new MedicalClaimService();

                // Check for duplicate pay slip using employee_no and ondate
                $duplicateCheckResult = $medicalClaimService->findAll([
                    'employee_no' => $data['employee_no'],
                    'bill_date' => $data['bill_date'],
                    'bill_number' => $data['bill_number'],
                ]);

                if ($duplicateCheckResult->count() > 0) {
                    throw new \Exception('Duplicate entry for ' . $data['employee_no'] . ' and bill number ' . $data['bill_number'] . ' and bill date ' . $data['bill_date']);
                }

                // Store MedicalClaim
                $employeeDto = new MedicalClaimDto(
                    $data['employee_no'],
                    $data['bill_number'],
                    $data['no_of_bills'],
                    $data['bill_date'],
                    $data['claimed_amount'],
                    $data['deduction_amount'],
                    $data['final_bill_amount'],
                    $data['status'] ?? 'Pending',
                    auth()->user()->id,
                    auth()->user()->id,
                );

                $employee = $medicalClaimService->create($employeeDto);

                if (!$employee) {
                    throw new \Exception("Failed to create Medical Claim for employee " . $data['employee_no']);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
