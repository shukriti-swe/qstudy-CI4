<?php

namespace App\Models;

use CodeIgniter\Model;

class TblClassRoomModel extends Model
{
    protected $table            = 'tbl_classrooms';
    protected $allowedFields    = [
        'all_student_checked','students','tutor_id','start_time','end_time','tutor_name','class_url'
    ];
    protected $returnType       = 'array';
    protected $useTimestamps = false;
}
