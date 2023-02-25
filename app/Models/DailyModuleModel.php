<?php

namespace App\Models;

use CodeIgniter\Model;

class DailyModuleModel extends Model
{
    protected $table            = 'daily_modules';
    protected $allowedFields    = [
        'user_id','module_id','complete_date','percentage','status'
    ];
    protected $returnType       = 'array';
    protected $useTimestamps = false;
}
