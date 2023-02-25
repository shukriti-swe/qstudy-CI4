<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductPointModel extends Model
{
    protected $table            = 'product_poinits';
    protected $allowedFields    = [
        'user_id','recent_point','bonus_point','referral_point','total_point','use_point'
    ];
    protected $returnType       = 'object';
    protected $useTimestamps = false;
}
