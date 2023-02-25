<?php

namespace App\Models;

use CodeIgniter\Model;

class ModulePointModel extends Model
{
    protected $table            = 'module_points';
    protected $allowedFields    = [
        'user_id','point','	status'
    ];
    protected $returnType       = 'object';
    protected $useTimestamps = false;
}
