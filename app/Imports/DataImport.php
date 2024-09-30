<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DataImport implements ToArray, WithHeadingRow
{
    public function array(array $array): array
    {
        return $array;
    }

    public function headingRow(): int
    {
        return 0;
    }
}
