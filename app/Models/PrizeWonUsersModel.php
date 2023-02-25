<?php

namespace App\Models;

use CodeIgniter\Model;

class PrizeWonUsersModel extends Model
{
    protected $table            = 'prize_won_users';
    protected $allowedFields    = [
        'user_id','productId','productPoint','date','status'
    ];
    protected $returnType       = 'array';
    protected $useTimestamps = false;
}
