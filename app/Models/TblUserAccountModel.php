<?php

namespace App\Models;

use CodeIgniter\Model;

class TblUserAccountModel extends Model
{
    protected $table            = 'tbl_useraccount';
    protected $allowedFields    = [
        'user_type','country_id','children_number','name','user_email','user_pawd','user_mobile','user_phone','student_grade','SCT_link','created','subscription_type','end_subscription','parent_id','token','image','payment_status','trial_end_date','suspension_status','subscription_status','pass_reset_code','website','direct_deposite','whiteboar_id','whiteboard','tutor_permission','sms_status_stop','unlimited'
    ];
    protected $returnType       = 'object';
    protected $useTimestamps = false;
}
