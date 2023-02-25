<?php

class RegisterClass
{
    public function __construct(){
        $this->db = db_connect();         
    }

    public function getUserType()
	{
        $builder = $this->db->table('tbl_usertype');
		$builder->select('*');
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
	}
    
    public function getCountry()
	{
        $builder = $this->db->table('tbl_country');
		$builder->select('*');
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
	}

    public function getCourse($user_type, $country_id) {
       
        $builder = $this->db->table('tbl_course');
        $builder->select('*');
        $builder->where('is_enable', 1);
        $builder->where('user_type', $user_type);
        $builder->where('country_id', $country_id);
        // $this->db->where('subscription_type', $subscription_type);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function getSpecificCountry($id)
	{
        $builder = $this->db->table('tbl_country');
		$builder->select('*');
		$builder ->where('id',$id);
		$query = $builder->get();
        $data=$query->getResultArray();

        return $data;
	}

    public function save_random_digit($data)
	{

        $TblRandomModel = new App\Models\TblRandomModel();
		return $TblRandomModel->insert($data);
	}

    public function basicInsert($table_name,$data)
	{
        $builder = $this->db->table($table_name);
		$builder->insert($data); 
		return $this->db->insertID();
	}

    public function getCourseCost($course_id){
        $builder = $this->db->table('tbl_course');
		$builder->select('courseCost,courseName');
		$builder->where('id',$course_id);
		$query = $builder->get();
        $data=$query->getResultArray();
        return $data;
	}

    public function getInfo($table_name,$column_name,$id)
	{
       // echo $id;die();
        $builder = $this->db->table($table_name);
		$builder->select('*');
		$builder->where($column_name,$id);
		$query = $builder->get();
        $data=$query->getResultArray();
        return $data;
	}

    public function refferalLinkInsert($table_name,$data)
	{
        $builder = $this->db->table($table_name);
		$builder->insert($data); 
		return $this->db->insertID();
	}

    public function saveUser($data)
	{
        $builder = $this->db->table('tbl_useraccount');
		$builder->insert($data); 
		return $this->db->insertID();
	}

    public function emailCheck($email)
	{
        $builder = $this->db->table('tbl_useraccount');
        $builder->where('user_email',$email);
        $query = $builder->get();
        $res=$query->getResultArray();
		return ( count( $res )>0 ) ? 1:0;
	}

    public function passwdChk( $email , $phone )
	{
        $builder = $this->db->table('tbl_useraccount');
        $builder->where('user_email',$email);
        $builder->where('user_mobile',$phone);
        $query = $builder->get();
        $res=$query->getResultArray();
		return ( count( $res )>0 ) ? 1:0;

	}

    public function phoneChk( $phone )
	{
        $builder = $this->db->table('tbl_useraccount');
        $builder->where('user_mobile',$phone);
        $query = $builder->get();
        $res=$query->getResultArray();
		return ( count( $res )>0 ) ? 1:0;
	}

}