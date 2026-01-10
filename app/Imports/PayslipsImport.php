<?php

namespace App\Imports;

use App\Models\Payslips;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PayslipsImport implements ToModel, WithHeadingRow
{

    protected $payslip_upload_id, $id_numbers = [];

    // Constructor to accept the payslip_upload_id
    public function __construct($payslip_upload_id, $id_numbers)
    {
        $this->payslip_upload_id = $payslip_upload_id;
        $this->id_numbers = $id_numbers;
    }


    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Check if employee_id_number is in the provided id_numbers array
        if (in_array($row['employee_id_number'], $this->id_numbers)) {
            // If found in the id_numbers array, update the record
            return Payslips::updateOrCreate(
                ['employee_id_number' => $row['employee_id_number'], 'payslip_upload_id' => $this->payslip_upload_id],
                [
                    'email' => $row['email'],
                    'fullname' => $row['fullname'],
                    'payslip_file' => $row['payslip_file'],
                ]
            );
        } else {
            // If not found, create a new record as is
            return new Payslips([
                'payslip_upload_id' => $this->payslip_upload_id,
                'employee_id_number' => $row['employee_id_number'],
                'email' => $row['email'],
                'fullname' => $row['fullname'],
                'payslip_file' => $row['payslip_file'],
            ]);
        }
    }
}
