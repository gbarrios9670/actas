<?php

namespace App\Imports;

use App\Actascomi;
use Maatwebsite\Excel\Concerns\ToModel;

class ActascomisImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Actascomi([
            'anno'          => $row[0], 
            'mes'           => $row[1],
            'acta'          => $row[2],
            'comision'      => $row[3], 
            'dire_web'      => $row[4], 
        ]);
    }
}
