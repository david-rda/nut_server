<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Statement;
use App\Models\User;

class StatementExport implements FromArray, withHeadings, WithStyles
{
    private $from;
    private $to;
    private $user_id;

    public function __construct($from = null, $to = null, $user_id = null) {
        $this->from = $from;
        $this->to = $to;
        $this->user_id = $user_id;
    }

    public function styles(Worksheet $sheet) {
        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }

    public function headings(): array
    {
        $data = [
            'ზედნადების ნომერი',
            'ზედნადების თარიღი',
            'მაღაზიის მისამართი',
            'აგრობარათის მფლობელი',
            'აგრობარათის ბოლო 4 ციფრი',
            'ჯამური აგროქულა',
            'კომპანიის სახელი',
            'კომენტარი',
            'სტატუსი',
            "განაცხადის გადაგზავნის თარიღი",
            "პროდუქტის დასახელება",
            "პროდუქტის ფასი"
        ];

        return $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function array() : array
    {
        $data = [];
        $statement = [];

        if(User::where("id", $this->user_id)->first()?->permission == "company") {
            $statement = Statement::whereBetween("overhead_date", [$this->from, $this->to])->with("statement_product")->where("user_id", $this->user_id)->get();
        }else if(User::where("id", $this->user_id)->first()?->permission != "company") {
            $statement = Statement::whereBetween("overhead_date", [$this->from, $this->to])->with("statement_product")->get();
        }

        foreach($statement as $key => $value) {
            foreach($value?->statement_products as $products) {
                array_push($data, [
                    $value?->overhead_number,
                    $value?->overhead_date,
                    $value?->store_address,
                    $value?->beneficiary_name,
                    $value?->card_number,
                    $value?->full_amount,
                    $value?->company_name,
                    $value?->comment,
                    ($value?->status == "new") ? 'ახალი' : (($value?->status == "rejected") ? 'დახარვეზებული' : (($value?->status == "approved") ? 'დადასტურებული' : (($value?->status == "stopped") ? 'შეჩერებული' : 'გადაწერილია ოპერატორზე'))),
                    $value?->created_at,
                    $products?->name,
                    $products?->price,
                ]);
            }
        }
        
        return $data;
    }
}
