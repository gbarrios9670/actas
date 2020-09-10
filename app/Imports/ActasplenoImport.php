<?php

namespace App\Imports;

use App\Actaspleno;
use Maatwebsite\Excel\Concerns\ToModel;

class ActasplenoImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Actaspleno([
            'anno'          => $row[0], 
            'mes'           => $row[1],
            'acta'          => $row[2],
            'dire_web'      => $row[3], 
        ]);
    }
}
