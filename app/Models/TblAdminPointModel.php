<?php

namespace App\Models;

use CodeIgniter\Model;

class TblAdminPointModel extends Model
{
    protected $table            = 'tbl_admin_points';
    protected $allowedFields    = [
        'target_point','referral_point','ref_taken_point'
    ];
    protected $returnType       = 'object';
    protected $useTimestamps = false;
}
