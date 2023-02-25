<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentGradeLogModel extends Model
{
    protected $table            = 'student_grade_log';
    protected $allowedFields    = [
        'user_id','	grade'
    ];
    protected $returnType       = 'object';
    protected $useTimestamps = false;
}
