<?php

namespace App\Models;

use CodeIgniter\Model;

class TblComposeMessageModel extends Model
{
    protected $table            = 'tbl_compose_message';
    protected $allowedFields    = [
        'message','	reciver_id','date_time','date_time','date','time','status'
    ];
    protected $returnType       = 'array';
    protected $useTimestamps = false;
}
