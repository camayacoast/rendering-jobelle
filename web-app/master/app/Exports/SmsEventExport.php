<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

use Illuminate\Http\Request;

use App\Models\Booking\SmsJobHistory;

class SmsEventExport implements FromView, WithColumnWidths, ShouldAutoSize
{

    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'C' => 25,
            'D' => 25,
            'E' => 25,
            'F' => 25,
            'G' => 25,
            'H' => 25,
            'I' => 25,
            'J' => 25,           
        ];
    }

    public function view(): View
    {
        $data = SmsJobHistory::find($this->id);

        return view('exports.ExcelTemplate.smsEventExport', [
            'data' => $data
        ]);
    }
}
