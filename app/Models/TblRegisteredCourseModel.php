<?php

namespace App\Models;

use CodeIgniter\Model;

class TblRegisteredCourseModel extends Model
{
    protected $table            = 'tbl_registered_course';
    protected $primaryKey = 'id';
    protected $allowedFields    = [
        'course_id','user_id','created','endTime','cost','status','assign_examine'
    ];
    protected $returnType       = 'array';
    protected $useTimestamps = false;
}
