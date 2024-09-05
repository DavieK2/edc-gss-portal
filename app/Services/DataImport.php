<?php

namespace App\Services;

use App\User;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DataImport implements ToArray, WithHeadingRow
{
    use Importable;
    /**
     * @param array $row
     *
     * @return User|null
     */
    public function array(array $row)
    {
        return $row;
    }
}