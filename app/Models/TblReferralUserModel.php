<?php

namespace App\Models;

use CodeIgniter\Model;

class TblReferralUserModel extends Model
{
    protected $table            = 'tbl_referral_users';
    protected $allowedFields    = [
        'user_id','refferalUser','refferalLink','status'
    ];
    protected $returnType       = 'array';
    protected $useTimestamps = false;
}
