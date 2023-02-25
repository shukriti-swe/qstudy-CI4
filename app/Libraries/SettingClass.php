<?php

class SettingClass
{
    public function __construct(){
        $this->db = db_connect();         
    }

    public function getStripeKey($public_or_secreet){
		$mode=$this->getStripeMode();
		return $this->getSettingVal($mode.'_'.$public_or_secreet.'_key','stripe');
	}

    public function getStripeMode(){
		$returnVal='test';		
		$r=$this->getSettingVal('mode','stripe');
		if($r !=''){
			$returnVal=$r;
		}
		return $returnVal;
	}

    public function getSettingVal($settingKey,$settingType){
		$returnVal='';
        $builder=$this->db->table('tbl_setting');		
		$builder->select('setting_value');
		$builder->where('setting_type',$settingType);
		$builder->where('setting_key',$settingKey);		
		$row=$builder->get()->getRowArray();
		if(!empty($row)){			
		if($row['setting_value']!='' && $row['setting_value']!=NULL){
				$returnVal=$row['setting_value'];
			}
		}
		return $returnVal;
	}

	public function getPaypalKey($public_or_secreet)
	{
		$mode=$this->getPaypalMode();
		return $this->getSettingVal($mode.'_'.$public_or_secreet,'paypal');
	}

	public function getPaypalMode()
	{
		 $returnVal='test';	
		$r=$this->getSettingVal('mode','paypal');
		if($r !=''){
			$returnVal=$r;
		}
		return $returnVal;
	}
}