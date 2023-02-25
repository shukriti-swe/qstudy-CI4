<?php

namespace App\Models;

use CodeIgniter\Model;

class TargetPointModel extends Model
{
    protected $table            = 'target_points';
    protected $allowedFields    = [
        'user_id','target','date','targetPoint'
    ];
    protected $returnType       = 'object';
    protected $useTimestamps = false;
}
