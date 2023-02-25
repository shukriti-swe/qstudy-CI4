<?php

namespace App\Models;

use CodeIgniter\Model;

class TblTutorCommision extends Model
{
    protected $table            = 'tbl_tutor_commisions';
    protected $allowedFields    = [
        'student_id','tutorId','amount','status','date'
    ];
    protected $returnType       = 'array';
    protected $useTimestamps = false;
}
