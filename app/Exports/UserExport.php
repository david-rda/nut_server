<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\User;

class UserExport implements FromArray, withHeadings, WithStyles, WithColumnWidths
{
    public function styles(Worksheet $sheet) {
        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
        ];
    }

    public function headings(): array
    {
        $data = [
            'კომპანიის დასახელება',
            'ს/კ',
            'სახელი, გვარი',
            'ელ. ფოსტა',
            'ტელეფონი',
            'პ/ნ',
            'სტატუსი',
            'როლი'
        ];

        return $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function array() : array
    {
        $data = [];

        $users = User::all();

        foreach($users as $key => $value) {
            array_push($data, [
                $value?->company_name,
                $value?->identification_code,
                $value?->name,
                $value?->email,
                $value?->mobile,
                $value?->personal_id,
                ($value?->status == "active") ? "აქტიური" : "არააქტიური",
                $value?->permission == "company" ? "კომპანია" : ($value?->permission == "operator"  ? "ოპერატორი" : "კოორდინატორი")

            ]);
        }
        
        return $data;
    }
}
