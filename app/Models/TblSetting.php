<?php

namespace App\Models;

use CodeIgniter\Model;

class TblSetting extends Model
{
    protected $table            = 'tbl_setting';
    protected $allowedFields    = [
        'setting_key','setting_value','setting_type'
    ];
    protected $returnType       = 'object';
    protected $useTimestamps = false;
}
