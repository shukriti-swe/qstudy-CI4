<?php

namespace App\Models;

use CodeIgniter\Model;

class TblPaymentModel extends Model
{
    protected $table            = 'tbl_payment';
    protected $allowedFields    = [
        'user_id','total_cost','payment_status','SenderEmail','PaymentDate','PaymentEndDate','paymentType','customerId','subscriptionId','invoiceId','payment_duration'
    ];
    protected $returnType       = 'array';
    protected $useTimestamps = false;
}
