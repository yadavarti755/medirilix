<?php

namespace App\Imports;

use Auth;
use App\DTO\DHTI\CadreCourseNominationDto;
use App\Services\DHTI\AnnualTrainingProgrammeService;
use App\Services\DHTI\CadreCourseNominationService;
use App\Services\DivisionPortal\EmployeeService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CadreCourseNominationBulkImport implements ToCollection, WithHeadingRow
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

                $data = [
                    'course_code' => $row['course_code'] ?? null,
                    'employee_no' => $row['employee_no'] ?? null,
                ];

                $validator = Validator::make($data, [
                    'course_code' => 'required|string',
                    'employee_no' => 'required|string',
                ]);

                if ($validator->fails()) {
                    throw new \Exception("Row " . ($index + 2) . " failed validation: " . json_encode($validator->errors()->all()));
                }

                // Check if the course code exists
                $annualTrainingProgrammeService = new AnnualTrainingProgrammeService();
                $cadreCourseNominationService = new CadreCourseNominationService();
                $employeeService = new EmployeeService();

                $existingCourse = $annualTrainingProgrammeService->findByCode($data['course_code']);

                if (!$existingCourse) {
                    throw new \Exception("Row " . ($index + 2) . " failed: Course code " . $data['course_code'] . " does not exist.");
                }

                if (!in_array($existingCourse->type, ['C', 'c'])) {
                    throw new \Exception("Row " . ($index + 2) . " failed: Course code " . $data['course_code'] . " is not a cadre course.");
                }

                // Check if the employee number exists
                $employee = $employeeService->findByEmployeeNo($data['employee_no']);
                if (!$employee) {
                    throw new \Exception("Row " . ($index + 2) . " failed: Employee number " . $data['employee_no'] . " does not exist.");
                }

                // Check if the employee is already nominated for the course
                $existingNomination = $cadreCourseNominationService->checkEmployeeNomination($data['employee_no'], $data['course_code']);
                if ($existingNomination) {
                    throw new \Exception("Row " . ($index + 2) . " failed: Employee number " . $data['employee_no'] . " is already nominated for course " . $data['course_code'] . ".");
                }

                $dto = new CadreCourseNominationDto(
                    $data['course_code'],
                    $data['employee_no'],
                    Auth::id(),
                    Auth::id()
                );

                $cadreCourseNominationService = new CadreCourseNominationService();
                $cadreCourseNominationService->create($dto);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
