<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Statement;
use App\Models\StatementProduct;

class StatementExport implements FromArray, withHeadings, WithColumnWidths, WithStyles
{
    private $from;
    private $to;
    private $user_id;
    private $status;

    public function __construct($from = null, $to = null, $status = null, $user_id = null) {
        $this->from = $from;
        $this->to = $to;
        $this->user_id = $user_id;
        $this->status = $status;
    }

    public function styles(Worksheet $sheet) {
        return [
            1 => [
                'font' => ['bold' => true],
                'height' => '38'
            ],
        ];
    }

    public function columnWidths(): array {
        return [
            'A' => 40,
            'B' => 40,
            'C' => 40,
            'D' => 40,
            'E' => 40,
            'F' => 40,
            'G' => 40,
            'H' => 40,
            'I' => 40,
            'J' => 40,
            'K' => 40,
            'L' => 40,
            'M' => 40,
            'N' => 40,
            'O' => 40,
            'P' => 40,
            'Q' => 40,
            'R' => 40,
            'S' => 40,
            'T' => 40,
            'U' => 40,
            'V' => 40,
            'W' => 40,
            'X' => 40,
            'Y' => 40,
            'Z' => 40
        ];
    }

    public function headings(): array
    {
        $products = StatementProduct::all();

        $data = [
            'ზედნადების ნომერი',
            'ზედნადების თარიღი',
            'მაღაზიის მისამართი',
            'ბენეფიციარის სახელი, გვარი',
            'ბარათის ბოლო 4 ციფრი',
            'ჯამური თანხა',
            'კომენტარი',
            'სტატუსი',
        ];

        foreach($products as $key => $value) {
            array_push($data,
                "პროდუქტის დასახელება " . $key + 1,
                "პროდუქტის ფასი " . $key + 1,
            );
        }

        return $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function array() : array
    {
        $data = [];

        if($this->from != null && $this->to != null) {
            $statement = Statement::get();

            foreach($statement as $key => $value) {
                array_push($data, [
                    $value?->overhead_number,
                    $value?->overhead_date,
                    $value?->store_address,
                    $value?->beneficiary_name,
                    $value?->card_number,
                    $value?->full_amount,
                    $value?->comment,
                    $value?->status,
                    $value?->statement_product[$key]["products"]->name,
                    $value?->statement_product[$key]->price,
                ]);
            }
        }

        if($this->from != null && $this->to != null && $this->status == "all") {
            $statement = Statement::get();

            foreach($statement as $key => $value) {
                array_push($data, [
                    $value?->overhead_number,
                    $value?->overhead_date,
                    $value?->store_address,
                    $value?->beneficiary_name,
                    $value?->card_number,
                    $value?->full_amount,
                    $value?->comment,
                    $value?->status,
                    $value?->statement_product[$key]["products"]->name,
                    $value?->statement_product[$key]->price,
                ]);
            }
        }

        if($this->from != null && $this->to != null && $this->status == "all" && $this->user_id != null) {
            $statement = Statement::where("user_id", $this->user_id)->get();

            foreach($statement as $key => $value) {
                array_push($data, [
                    $value?->overhead_number,
                    $value?->overhead_date,
                    $value?->store_address,
                    $value?->beneficiary_name,
                    $value?->card_number,
                    $value?->full_amount,
                    $value?->comment,
                    $value?->status,
                    $value?->statement_product[$key]["products"]->name,
                    $value?->statement_product[$key]->price,
                ]);
            }
        }

        if($this->status != "all") {
            $statement = Statement::where("status", $this->status)->get();

            foreach($statement as $key => $value) {
                array_push($data, [
                    $value?->overhead_number,
                    $value?->overhead_date,
                    $value?->store_address,
                    $value?->beneficiary_name,
                    $value?->card_number,
                    $value?->full_amount,
                    $value?->comment,
                    $value?->status,
                    $value?->statement_product[$key]["products"]->name,
                    $value?->statement_product[$key]->price,
                ]);
            }
        }

        return $data;
    }
}
