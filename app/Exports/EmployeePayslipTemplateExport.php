<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class EmployeePayslipTemplateExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = User::select('id_number', 'email',  DB::raw("CONCAT(first_name, ' ', last_name) as fullname"))->where('user_type', 2)->where('user_status', 1)->where('agree', 1)->get();

      
        $modifiedData = $data->map(function ($item) {
            $item->payslip_file= ''; 
            return $item;
        });

        return $modifiedData;
    }


    /**
     * Return the headings of the Excel file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'employee_id_number',
            'email',
            'fullname',
            'payslip_file'
        ];
    }
}
