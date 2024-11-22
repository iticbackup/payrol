<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Models\NewDataPengerjaan;

class LpPerMonth implements FromQuery, WithTitle
{
    // /**
    // * @return \Illuminate\Support\Collection
    // */
    // public function collection()
    // {
    //     //
    // }

    private $jenis_operator_id;

    public function __construct(int $jenis_operator_id)
    {
        $this->jenis_operator_id = $jenis_operator_id;
    }

    public function query()
    {
        // return $this->month;
        return NewDataPengerjaan::query()
            ->whereYear('created_at', $this->year)
            ->whereMonth('created_at', $this->month);
    }

    public function title(): string
    {
        return 'Month ' . $this->month;
    }
}
