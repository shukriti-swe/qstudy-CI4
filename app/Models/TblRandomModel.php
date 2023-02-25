<?php

namespace App\Models;

use CodeIgniter\Model;

class TblRandomModel extends Model
{
    protected $table            = 'tbl_random';
    protected $allowedFields    = [
        'number'
    ];
    protected $returnType       = 'object';
    protected $useTimestamps = false;
}
