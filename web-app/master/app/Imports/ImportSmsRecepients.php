<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class ImportSmsRecepients implements ToCollection, WithHeadingRow
{

    public $data;

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        $result = [];
        $phCode = ['09', '63', '9'];

        foreach($rows as $row)
        {
            $recipientItem = trim($row['recipient']);

            if(!is_numeric($recipientItem)) continue;

            if(!in_array(substr($recipientItem, 0, 2), $phCode)) continue;
 
            if(strlen($recipientItem) < 11) continue;

            if(strlen($recipientItem) > 12) continue;
            
            $result[] = [
                'recepient' => $recipientItem,
                'recepient_name' => $row['name']
            ];
        }
        
        $this->data = $result;
    }

}
