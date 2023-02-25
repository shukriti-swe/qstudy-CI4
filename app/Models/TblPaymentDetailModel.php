<?php

namespace App\Models;

use CodeIgniter\Model;

class TblPaymentDetailModel extends Model
{
    protected $table            = 'tbl_payment_details';
    protected $allowedFields    = [
        'paymentId','courseId'
    ];
    protected $returnType       = 'array';
    protected $useTimestamps = false;
}
