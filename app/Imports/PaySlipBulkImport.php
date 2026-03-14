<?php

namespace App\Imports;

use App\DTO\UserDto;
use App\DTO\DivisionPortal\PaySlipDto;
use App\Models\User;
use App\Models\DivisionPortal\PaySlip;
use App\Services\DivisionPortal\EmployeeService;
use App\Services\DivisionPortal\PaySlipService;
use App\Services\UserService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PaySlipBulkImport implements ToCollection, WithHeadingRow
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

                // Convert ondate if it's in Excel format
                $onDateRaw = $row['ondate'];
                if (!empty($onDateRaw)) {
                    if (is_numeric($onDateRaw)) {
                        $onDate = Date::excelToDateTimeObject($onDateRaw)->format('Y-m-d');
                    } else {
                        $onDate = Carbon::parse($onDateRaw)->format('Y-m-d');
                    }
                } else {
                    $onDate = null;
                }

                $data = [
                    'employee_no' => $row['employee_number'] ?? null,
                    'cao' => $row['cao'] ?? null,
                    'cda' => $row['cda'] ?? null,
                    'office' => $row['office'] ?? null,
                    'accountno' => $row['accountno'] ?? null,
                    'da' => $row['da'] ?? null,
                    'hra' => $row['hra'] ?? null,
                    'tpt' => $row['tpt'] ?? null,
                    'cgies' => $row['cgies'] ?? null,
                    'cghs' => $row['cghs'] ?? null,
                    'gpfno' => $row['gpfno'] ?? null,
                    'gpf_subs' => $row['gpf_subs'] ?? null,
                    'gross_earn' => $row['gross_earn'] ?? null,
                    'pymf_gross_deduct' => $row['pymf_gross_deduct'] ?? null,
                    'pymf_net_pay' => $row['pymf_net_pay'] ?? null,
                    'income_tax' => $row['income_tax'] ?? null,
                    'eh2' => $row['eh2'] ?? null,
                    'ev2' => $row['ev2'] ?? null,
                    'eh3' => $row['eh3'] ?? null,
                    'ev3' => $row['ev3'] ?? null,
                    'eh4' => $row['eh4'] ?? null,
                    'ev4' => $row['ev4'] ?? null,
                    'eh5' => $row['eh5'] ?? null,
                    'ev5' => $row['ev5'] ?? null,
                    'dh1' => $row['dh1'] ?? null,
                    'dv1' => $row['dv1'] ?? null,
                    'inst1' => $row['inst1'] ?? null,
                    'dh2' => $row['dh2'] ?? null,
                    'dv2' => $row['dv2'] ?? null,
                    'inst2' => $row['inst2'] ?? null,
                    'dh3' => $row['dh3'] ?? null,
                    'dv3' => $row['dv3'] ?? null,
                    'inst3' => $row['inst3'] ?? null,
                    'dh4' => $row['dh4'] ?? null,
                    'dv4' => $row['dv4'] ?? null,
                    'inst4' => $row['inst4'] ?? null,
                    'dh5' => $row['dh5'] ?? null,
                    'dv5' => $row['dv5'] ?? null,
                    'inst5' => $row['inst5'] ?? null,
                    'dh6' => $row['dh6'] ?? null,
                    'dv6' => $row['dv6'] ?? null,
                    'inst6' => $row['inst6'] ?? null,
                    'dh7' => $row['dh7'] ?? null,
                    'dv7' => $row['dv7'] ?? null,
                    'inst7' => $row['inst7'] ?? null,
                    'ifsc' => $row['ifsc'] ?? null,
                    'ondate' => $onDate,
                    'remks' => $row['remks'] ?? null,
                    'payband' => $row['payband'] ?? null,
                    'basic_pay' => $row['basic_pay'] ?? null,
                    'pscl_code' => $row['pscl_code'] ?? null,
                    'fy_itax' => $row['fy_itax'] ?? null,
                    'gross_income_actual' => $row['gross_income_actual'] ?? null,
                    'gpf_nps_cgeis_actual' => $row['gpf_nps_cgeis_actual'] ?? null,
                    'deduct_cghs_actual' => $row['deduct_cghs_actual'] ?? null,
                    'edu_cess' => $row['edu_cess'] ?? null,
                    'acc_no' => $row['acc_no'] ?? null,
                    'acc_detail' => $row['acc_detail'] ?? null,
                    'cpf_govt_contrbn' => $row['cpf_govt_contrbn'] ?? null,
                    'wash_allce' => $row['wash_allce'] ?? null,
                    'tax_regime' => $row['tax_regime'] ?? null,
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ];

                // Minimal validation - adjust rules as needed
                $validator = Validator::make($data, [
                    'employee_no' => 'required|max:255',
                    'cao' => 'nullable|string|size:2',
                    'cda' => 'nullable|integer|min:0|max:255',
                    'office' => 'nullable|string|max:32',
                    'accountno' => 'nullable|string|max:20',
                    'da' => 'nullable|integer|min:0',
                    'hra' => 'nullable|integer|min:0',
                    'tpt' => 'nullable|integer|min:0',
                    'cgies' => 'nullable|integer|min:0|max:65535',
                    'cghs' => 'nullable|integer|min:0|max:65535',
                    'gpfno' => 'nullable|string|max:20',
                    'gpf_subs' => 'nullable|numeric|min:0',
                    'gross_earn' => 'nullable|integer|min:0',
                    'pymf_gross_deduct' => 'nullable|numeric|min:0',
                    'pymf_net_pay' => 'nullable|numeric|min:0',
                    'income_tax' => 'nullable|numeric|min:0',

                    'eh2' => 'nullable|string|max:20',
                    'ev2' => 'nullable|numeric|min:0',
                    'eh3' => 'nullable|string|max:20',
                    'ev3' => 'nullable|numeric|min:0',
                    'eh4' => 'nullable|string|max:20',
                    'ev4' => 'nullable|numeric|min:0',
                    'eh5' => 'nullable|string|max:20',
                    'ev5' => 'nullable|numeric|min:0',

                    'dh1' => 'nullable|string|max:20',
                    'dv1' => 'nullable|numeric|min:0',
                    'inst1' => 'nullable|string|max:7',
                    'dh2' => 'nullable|string|max:20',
                    'dv2' => 'nullable|numeric|min:0',
                    'inst2' => 'nullable|string|max:7',
                    'dh3' => 'nullable|string|max:20',
                    'dv3' => 'nullable|numeric|min:0',
                    'inst3' => 'nullable|string|max:7',
                    'dh4' => 'nullable|string|max:20',
                    'dv4' => 'nullable|numeric|min:0',
                    'inst4' => 'nullable|string|max:7',
                    'dh5' => 'nullable|string|max:20',
                    'dv5' => 'nullable|numeric|min:0',
                    'inst5' => 'nullable|string|max:7',
                    'dh6' => 'nullable|string|max:20',
                    'dv6' => 'nullable|numeric|min:0',
                    'inst6' => 'nullable|string|max:7',
                    'dh7' => 'nullable|string|max:20',
                    'dv7' => 'nullable|numeric|min:0',
                    'inst7' => 'nullable|string|max:7',

                    'ifsc' => 'nullable|string|size:11',
                    'ondate' => 'nullable|date',
                    'remks' => 'nullable|string|max:25',
                    'payband' => 'nullable|string|max:17',
                    'basic_pay' => 'nullable|integer|min:0',
                    'pscl_code' => 'nullable|string|max:4',
                    'fy_itax' => 'nullable|integer|min:0',
                    'gross_income_actual' => 'nullable|integer|min:0',
                    'gpf_nps_cgeis_actual' => 'nullable|integer|min:0',
                    'deduct_cghs_actual' => 'nullable|integer|min:0|max:65535',
                    'edu_cess' => 'nullable|numeric|min:0',
                    'acc_no' => 'nullable|string|max:20',
                    'acc_detail' => 'nullable|string|max:50',
                    'cpf_govt_contrbn' => 'nullable|integer|min:0',
                    'wash_allce' => 'nullable|integer|min:0|max:65535',
                    'tax_regime' => 'nullable|string|size:1',
                ]);

                if ($validator->fails()) {
                    throw new \Exception("Row " . ($index + 2) . " failed validation: " . json_encode($validator->errors()->all()));
                }

                $paySlipService = new PaySlipService();

                // Check if employee exists
                $employeeService = new EmployeeService();
                $employee = $employeeService->findByEmployeeNo($data['employee_no']);
                if (!$employee) {
                    throw new \Exception("Employee does not exist with this employee number: " . $data['employee_no']);
                }

                // Check for duplicate pay slip using employee_no and ondate
                if ($onDate) {
                    // Check for duplicate pay slip using employee_no and year-month from ondate
                    $duplicateCheckResult = $paySlipService->checkDuplicateByEmployeeAndMonth(
                        $data['employee_no'],
                        $data['ondate']
                    );

                    if ($duplicateCheckResult->count() > 0) {
                        throw new \Exception('Duplicate entry of pay slip for ' . $data['employee_no']);
                    }
                }

                $dto = new PaySlipDto(...array_values($data));

                $result = $paySlipService->create($dto);

                if (!$result) {
                    throw new \Exception("Failed to create payslip for employee " . $data['employee_no']);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
