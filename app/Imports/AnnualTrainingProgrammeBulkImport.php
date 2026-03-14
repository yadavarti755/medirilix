<?php

namespace App\Imports;

use App\DTO\DHTI\AnnualTrainingProgrammeDto;
use App\Models\DivisionPortal\FinancialYear;
use App\Services\DHTI\AnnualTrainingProgrammeService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AnnualTrainingProgrammeBulkImport implements ToCollection, WithHeadingRow
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

                // Parse start_date
                $startDateRaw = $row['start_date'] ?? null;
                if (!empty($startDateRaw)) {
                    if (is_numeric($startDateRaw)) {
                        $startDate = Date::excelToDateTimeObject($startDateRaw)->format('Y-m-d');
                    } else {
                        $startDate = Carbon::parse($startDateRaw)->format('Y-m-d');
                    }
                } else {
                    $startDate = null;
                }

                // Parse end_date
                $endDateRaw = $row['end_date'] ?? null;
                if (!empty($endDateRaw)) {
                    if (is_numeric($endDateRaw)) {
                        $endDate = Date::excelToDateTimeObject($endDateRaw)->format('Y-m-d');
                    } else {
                        $endDate = Carbon::parse($endDateRaw)->format('Y-m-d');
                    }
                } else {
                    $endDate = null;
                }

                // Parse registration_cut_off_date
                $cutOffDateRaw = $row['registration_cut_off_date'] ?? null;
                if (!empty($cutOffDateRaw)) {
                    if (is_numeric($cutOffDateRaw)) {
                        $registrationCutOffDate = Date::excelToDateTimeObject($cutOffDateRaw)->format('Y-m-d');
                    } else {
                        $registrationCutOffDate = Carbon::parse($cutOffDateRaw)->format('Y-m-d');
                    }
                } else {
                    $registrationCutOffDate = null;
                }

                $data = [
                    'financial_year'         => $row['financial_year'] ?? null,
                    'type'                      => $row['type'] ?? null,
                    'code'                      => $row['code'] ?? null,
                    'title'                     => $row['title'] ?? null,
                    'start_date'                => $startDate,
                    'end_date'                  => $endDate,
                    'dir'                       => $row['dir'] ?? null,
                    'status'                    => strtoupper($row['status'] ?? ''),
                    'registration_cut_off_date' => $registrationCutOffDate,
                ];

                $rules = [
                    'financial_year'            => 'required|string',
                    'type'                      => 'required|string',
                    'code'                      => 'required|string|unique:annual_training_programmes,code',
                    'title'                     => 'required|string',
                    'start_date'                => 'required|date',
                    'end_date'                  => 'required|date|after_or_equal:start_date',
                    'dir'                       => 'required|string',
                    'status'                    => 'required|in:OPEN,SCHEDULED',
                    'registration_cut_off_date' => 'nullable|date|before_or_equal:start_date',
                ];

                if (strtoupper($data['status']) === 'OPEN') {
                    $rules['registration_cut_off_date'] = 'required|date|before_or_equal:start_date';
                } else {
                    $rules['registration_cut_off_date'] = 'nullable|date|before_or_equal:start_date';
                }

                $validator = Validator::make($data, $rules);

                if ($validator->fails()) {
                    throw new \Exception("Row " . ($index + 2) . " failed validation: " . json_encode($validator->errors()->all()));
                }

                // Get financial year ID from the financial year name
                $financialYear = FinancialYear::where('financial_year', $data['financial_year'])->first();
                if (!$financialYear) {
                    throw new \Exception("Financial year not found: " . $data['financial_year']);
                }

                $annualTrainingProgrammeService = new AnnualTrainingProgrammeService();
                // Check if the annual training programme already exists
                $existingProgramme = $annualTrainingProgrammeService->findByCode($data['code'], $financialYear->id);
                if ($existingProgramme) {
                    throw new \Exception("Annual Training Programme with code " . $data['code'] . " already exists for financial year " . $data['financial_year']);
                }

                // Create the DTO
                $annualTrainingProgrammeDto = new AnnualTrainingProgrammeDto(
                    $financialYear->id,
                    $data['type'],
                    $data['code'],
                    $data['title'],
                    $data['start_date'],
                    $data['end_date'],
                    $data['dir'],
                    $data['status'],
                    $data['registration_cut_off_date'],
                    auth()->user()->id,
                    auth()->user()->id,
                );

                $annualTrainingProgramme = $annualTrainingProgrammeService->create($annualTrainingProgrammeDto);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
