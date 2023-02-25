<?php

namespace App\Models;

use CodeIgniter\Model;

class DirectDepositeAdminSettingModel extends Model
{
    protected $table            = 'direct_deposit_admin_setting';
    protected $allowedFields    = [
        'country_id','bank_details','active_status','instant_message','email_message','inbox_message'
    ];
    protected $returnType       = 'object';
    protected $useTimestamps = false;
}
