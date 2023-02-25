<?php
use App\Models\TblSetting;

class AdminClass
{
    public function __construct(){
        $this->db = db_connect();         
    }

    
    public function getDirectDepositCourse($user_id){

        $builder = $this->db->table('tbl_payment');
        $builder->where('user_id',$user_id);
        $builder->where('paymentType',3);
        $builder->where('PaymentEndDate >',time());
        $data=$builder->countAllResults();
        return $data;
    }

    public function getDirectDepositPendingCourse($user_id){
        $builder = $this->db->table('tbl_payment');
        $builder->where('user_id',$user_id);
        $builder->where('payment_status','pending');
        $builder->where('paymentType',3);
        $builder->where('PaymentEndDate >',time());
        $data=$builder->countAllResults();;
        return $data;
    }

    public function getActiveCourse($user_id){
        $builder = $this->db->table('tbl_payment');
        $builder->where('user_id',$user_id);
        $builder->whereIn('payment_status',['succeeded','Completed','active']);
        $builder->whereIn('paymentType',[1,2,3]);
        $builder->where('PaymentEndDate >',time());
        $data=$builder->countAllResults();
        return $data;
    }

    public function getSmsApiKeySettings()
    {
        $builder = $this->db->table('tbl_setting');
        $builder->select('*');
        $builder->where('setting_type', 'sms_api_settings');
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function getSmsMessageSettings()
    {
        $builder = $this->db->table('tbl_setting');
        $builder->select('*');
        $builder->where('setting_type', 'sms_message_settings');
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function getSmsType($id)
    {
        $builder = $this->db->table('tbl_setting');
        $builder->select('*');
        $builder->where('setting_key', $id);
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function getInfo($table, $colName, $colValue)
    {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where($colName, $colValue);
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function search($tableName, $params)
    {
        $builder = $this->db->table($tableName);
        $builder->where($params);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function updateInfo($table, $colName, $colValue, $data)
    {
        $builder = $this->db->table($table);
        $builder->where($colName, $colValue);
        $builder->update($data);
    }
	
    public function getInfoInactiveUserCheck($table, $colName, $colValue,$endTrial,$user)
    {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where($colName, $colValue);
        $builder->where('created <',$endTrial);
        $builder->where('id',$user);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function getDepositDetails($table,$countryId){
        $builder = $this->db->table($table);
        $builder->where('country_id',$countryId);
        $query = $builder->get();
        $data=$query->getRow();
        return $data;
    }

    public function total_registered_search($name,$userType,$contryID)
    {
        $builder = $this->db->table('tbl_useraccount');
        $builder->select('tbl_useraccount.*,tbl_usertype.userType,tbl_country.countryName');  
        $builder->join('tbl_usertype', 'tbl_useraccount.user_type = tbl_usertype.id', 'LEFT');
        $builder->join('tbl_country', 'tbl_useraccount.country_id = tbl_country.id', 'LEFT');
        
        $builder->where('user_type != ', 0);
        $builder->where('user_type != ', 7);
        if ($name != null) {
            $builder->where('name', $name);
        }
        if ($userType != null) {
            $builder->where('user_type', $userType);
        }
        if ($contryID != null) {
            $builder->where('country_id',$contryID);
        }

        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function today_registered_search($name,$userType,$contryID)
    {
        $builder = $this->db->table('tbl_useraccount');
        $builder->select('count(*) AS total_registered'); 
        $builder->where('user_type != ', 0);
        $builder->where('user_type != ', 7);
        if ($name != null) {
            $builder->where('name', $name);
        }
        if ($userType != null) {
            $builder->where('user_type', $userType);
        }
        if ($contryID != null) {
            $builder->where('country_id',$contryID);
        }

        $query = $builder->get();
        $data=$query->getRowArray();
        return $data;
    }

    public function total_registered()
    {
        $builder = $this->db->table('tbl_useraccount');
        $builder->select('tbl_useraccount.*,tbl_usertype.userType,tbl_country.countryName');
        
        $builder->join('tbl_usertype', 'tbl_useraccount.user_type = tbl_usertype.id', 'LEFT');
        $builder->join('tbl_country', 'tbl_useraccount.country_id = tbl_country.id', 'LEFT');
        
        $builder->where('user_type != ', 0);
        $builder->where('user_type != ', 7);

        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function today_registered()
    {
        $builder = $this->db->table('tbl_useraccount');
        $builder->select('count(*) AS total_registered');
        $builder->where('user_type != ', 0);
        $builder->where('user_type != ', 7);
        $query = $builder->get();
        $data=$query->getRowArray();
        return $data;
    }

    public function tutor_with_10_student()
    {
        $query = $this->db->query('SELECT *
            FROM tbl_useraccount t 
            LEFT JOIN tbl_country ON t.country_id = tbl_country.id 
            LEFT JOIN tbl_usertype ON t.user_type = tbl_usertype.id 
            WHERE ((SELECT count(*) FROM tbl_enrollment c WHERE c.sct_id = t.id)) >= 10 AND 
            user_type = 3');
        return $query->getResultArray();
    }

    public function getTotalIncome(){
        $builder = $this->db->table('tbl_payment');
        $builder->select('sum(total_cost) as total_cost');
        $builder->where('payment_status !=','pending');
        $query = $builder->get();
        $data=$query->getResult();
        return $data;
    }

    public function getDailyIncome(){
        $startTime = strtotime(date('Y-m-d 00:00:00'));
        $endTime   = strtotime(date('Y-m-d 23:59:59'));
        // echo $startTime;echo "<br>";echo $endTime;die;
        $builder = $this->db->table('tbl_payment');
        $builder->select('sum(total_cost) as daily_income');
        $builder->where('PaymentDate >=', $startTime);
        $builder->where('PaymentDate <=', $endTime);
        $builder->where('payment_status !=','pending');
        $query = $builder->get();
        $data=$query->getResult();
        return $data;
    }

    public function tutor_with_50_vocabulary()
    {
        $query = $this->db->query('SELECT * '
            . 'FROM tbl_useraccount t '
            . 'LEFT JOIN tbl_country ON t.country_id = tbl_country.id 
            LEFT JOIN tbl_usertype ON t.user_type = tbl_usertype.id '
            . 'WHERE ((SELECT COUNT(*) FROM tbl_question q WHERE q.user_id = t.id AND q.questionType = 3 HAVING COUNT(*))) >= 50 AND '
            . 'user_type = 3 ');
        return $query->getResultArray();
    }

    public function get_todays_data($date)
    {
        $query = $this->db->query('SELECT COUNT(*) AS today_registered FROM `tbl_useraccount` WHERE DATE(FROM_UNIXTIME(created)) LIKE "%'.$date.'%" ');
        return $query->getResultArray();
    }

    public function getAllInfo($table)
    {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->limit(1892);
        $query = $builder->get();
        
        $data=$query->getResultArray();
        return $data;
    }

    public function groupboard_req()
    {
        $builder = $this->db->table('tbl_registered_course');
        $builder->select('user_id');
        $builder->join('tbl_useraccount', 'tbl_registered_course.user_id = tbl_useraccount.id');
        $builder->whereIn('tbl_useraccount.user_type' , [ 3 , 4 ] );
        $builder->whereIn('course_id' , [ 53 , 54 ] );
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function groupboard_taker()
    {
        $builder = $this->db->table('tbl_useraccount');
        $builder->select('id');
        $builder->where('whiteboar_id !=', 0);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function getInfoDirectDepositUserList(){
        
        $builder = $this->db->table('tbl_payment');
        $builder->select('tbl_useraccount.name,tbl_useraccount.id');
        $builder->where('paymentType',3);
        $builder->where('PaymentEndDate >',time());
        $builder->where('tbl_payment.payment_status','pending');
        $builder->join('tbl_useraccount','tbl_payment.user_id = tbl_useraccount.id');
        $builder->groupBy('tbl_payment.user_id');
        $query=$builder->get();
        $data=$query->getResultArray();
        return $data;
    } 

    public function getInfoTrialActiveUser($table, $colName, $colValue,$endTrial,$limit,$offset)
    {
     
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where($colName, $colValue);
        $builder->where('created >',$endTrial);
        $builder->limit($limit,$offset);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function getInfoInactiveUser($table, $colName, $colValue,$limit,$offset)
    {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where($colName, $colValue);
        $builder->where('end_subscription <',date('Y-m-d'));
        $builder->limit($limit,$offset);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function getInfoInactiveTrialUser($table, $colName, $colValue,$endTrial,$limit,$offset)
    {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where($colName, $colValue);
        $builder->where('created <',$endTrial);
        $builder->limit($limit,$offset);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function getAllSignupUsers($table, $colName, $colValue,$limit,$offset)
    {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where($colName, $colValue);
        $builder->where('end_subscription >',date('Y-m-d'));
        $builder->limit($limit,$offset);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }
    
    public function getInfoSuspendUser($table, $colName, $colValue,$limit,$offset)
    {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where($colName, $colValue);
        $builder->limit($limit,$offset);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function getAllguestUsers($table, $colName, $colValue,$limit,$offset)
    {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where($colName, $colValue);
        $builder->limit($limit,$offset);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function getUsersTypeWaise($table, $colName, $colValue,$limit,$offset)
    {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where($colName, $colValue);
        $builder->limit($limit,$offset);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function getDepositeResources($limit,$offset)
    {
        $builder = $this->db->table('tbl_qs_payment');
        $builder->select('tbl_useraccount.name,tbl_qs_payment.user_id');
        $builder->join('tbl_useraccount', 'tbl_qs_payment.user_id = tbl_useraccount.id', 'LEFT');
        $builder->where('tbl_qs_payment.PaymentEndDate >',time());
        $builder->groupBy('tbl_qs_payment.user_id');
        $builder->limit($limit,$offset);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function whiteboardPurchesLists($limit,$offset)
    {
        $builder = $this->db->table('tbl_registered_course');
        $builder->select('tbl_useraccount.name,tbl_registered_course.user_id');
        $builder->join('tbl_useraccount', 'tbl_registered_course.user_id = tbl_useraccount.id');
        $builder->whereIn('course_id' , [ 53 , 54 ] );
        $builder->groupBy('tbl_registered_course.user_id');
        $builder->limit($limit,$offset);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function whiteboardPurchesSignupLists($type,$limit,$offset)
    {
        $TblSetting=new TblSetting();

        $tbl_setting = $TblSetting->where('setting_key','days')->first();
        $duration    = $tbl_setting->setting_value;
        $date        = date('Y-m-d');
        $d1          = date('Y-m-d', strtotime('-'.$duration.' days', strtotime($date)));
        $trialEndDate= strtotime($d1);
        
        $builder = $this->db->table('tbl_registered_course');
        $builder->select('tbl_useraccount.name,tbl_registered_course.user_id');
        $builder->join('tbl_useraccount', 'tbl_registered_course.user_id = tbl_useraccount.id');
        $builder->whereIn('course_id', [ 53 , 54 ]);
        $builder->where('subscription_type',$type);
        if($type == 'signup'){
            $builder->where('end_subscription >',date('Y-m-d'));
        }
        if($type == 'trial'){
            $builder->where('tbl_useraccount.created >',$trialEndDate);
            $builder->where('tbl_useraccount.parent_id',null);
        }
        $builder->where('tbl_useraccount.whiteboar_id',0);
        $builder->groupBy('tbl_registered_course.user_id');
        $builder->limit($limit,$offset);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function tutorCommisionForAssignStudent($limit,$offset){
        $builder = $this->db->table('tbl_tutor_commisions');
        $builder->select('tbl_useraccount.name,tbl_tutor_commisions.tutorId as user_id');
        $builder->join('tbl_useraccount', 'tbl_tutor_commisions.tutorId = tbl_useraccount.id');
        $builder->where('tbl_tutor_commisions.status',0);
        $builder->groupBy('tbl_tutor_commisions.tutorId');
        $builder->limit($limit,$offset);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }
    
    public function vocabularyCommisionCheck($limit,$offset){

        $builder = $this->db->table('dictionary_payment');
        $builder->select('tbl_useraccount.name,dictionary_payment.word_creator as user_id');
        $builder->join('tbl_useraccount','dictionary_payment.word_creator = tbl_useraccount.id');
        $builder->where('dictionary_payment.total_approved >',intval('dictionary_payment.total_paid')+50);
        $builder->limit($limit,$offset);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function checkStudentPercentageNotification($table,$limit,$offset)
    {
         $builder = $this->db->table('daily_modules');
         $builder->select('user_id,tbl_useraccount.name, COUNT(user_id) as total_row, sum(percentage) as total_percentage, sum(percentage)/COUNT(user_id) as percentage');
         $builder->where('status', 0);
         $builder->join('tbl_useraccount','daily_modules.user_id = tbl_useraccount.id');
         $builder->groupBy('daily_modules.user_id');
         $builder->limit($limit,$offset);  
         $query = $builder->get();
         $data=$query->getResultArray();
         return $data;
    }

    public function getInfoPrizeWinerUser($limit,$offset)
    {
        $builder = $this->db->table('prize_won_users');
        $builder->select('tbl_useraccount.*,prize_won_users.*');
        $builder->join('tbl_useraccount', 'tbl_useraccount.id=prize_won_users.user_id');
        $builder->where('prize_won_users.status','pending');
        $builder->groupBy('prize_won_users.user_id');
        $builder->limit($limit,$offset);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function getallcreative(){
        $builder = $this->db->table('tbl_registered_course');
        $builder->select('*');
        $builder->where('assign_examine', 1);
        $value = array(62, 63);
        $builder->whereIn('course_id', $value);

        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
        
    }

    public function idea_created_students_list(){
        $builder = $this->db->table('idea_student_ans');
        $builder->select('*');
        $builder->where('teacher_correction',0);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function idea_created_tutor_list(){

        $builder = $this->db->table('tbl_question');
        $builder->distinct();
        $builder->select('tbl_useraccount.id');
        $builder->where('tbl_question.questionType', 17);
        $builder->where('idea_info.allows_online', 1);
        $builder->join('idea_info', 'idea_info.question_id = tbl_question.id');
        $builder->join('tbl_useraccount', 'tbl_question.user_id = tbl_useraccount.id');

        $query = $builder->get();
        return $result = $query->getResultArray();
        
    }
    public function get_tutor_ideas($tutor_id){
        $builder = $this->db->table('tbl_question');
        $builder->select('*');
        $builder->where('tbl_question.user_id', $tutor_id);
        $builder->where('idea_info.allows_online', 1);
        $builder->where('tbl_question.questionType', 17);
        $builder->join('idea_info', 'idea_info.question_id = tbl_question.id');

        $query = $builder->get();
        return $result = $query->getResultArray();
    }

      
    public function getInfoDirectDepositUserAllByList($limit,$offset)
    {  
        $builder = $this->db->table('tbl_payment');
        $builder->select('tbl_useraccount.name,tbl_useraccount.id');
        $builder->where('paymentType',3);
        $builder->where('PaymentEndDate >',time());
        $builder->where('tbl_payment.payment_status','pending');
        $builder->join('tbl_useraccount','tbl_payment.user_id = tbl_useraccount.id');
        $builder->groupBy('tbl_payment.user_id');
        $builder->limit($limit,$offset);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function getInfoPrizeWinerUserByID($user_id,$limit,$offset)
    {
        $builder = $this->db->table('prize_won_users');
        $builder->select('tbl_useraccount.*,prize_won_users.*,tbl_products.product_title,tbl_products.image');
        $builder->join('tbl_useraccount', 'tbl_useraccount.id = prize_won_users.user_id');
        $builder->join('tbl_products', 'tbl_products.id = prize_won_users.productId');
        $builder->where('prize_won_users.user_id', $user_id);
        $builder->orderBy('prize_won_users.id','desc');
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function getInfoTrialActiveUserAdmin($table, $colName, $colValue,$user_id,$endTrial)
    {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where($colName, $colValue);
        $builder->where('id',$user_id);
        $builder->where('created >',$endTrial);
        return $builder->countAll();
    }

    public function getStudentsRefLink($user_id)
    {
        $builder = $this->db->table('tbl_useraccount');
        $builder->select('tbl_useraccount.*,tbl_enrollment.sct_id,tbl_enrollment.st_id');
        $builder->join('tbl_enrollment', 'tbl_useraccount.id=tbl_enrollment.st_id');
        $builder->where('tbl_enrollment.sct_id', $user_id);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function getTutorRefLink($user_id)
    {
        $builder = $this->db->table('tbl_enrollment');
        $builder->select('tbl_useraccount.name,tbl_useraccount.id,tbl_enrollment.sct_id,tbl_enrollment.st_id');
        $builder->join('tbl_useraccount', 'tbl_useraccount.id=tbl_enrollment.sct_id');
        $builder->where('tbl_enrollment.st_id', $user_id);
        $builder->where('tbl_useraccount.user_type',3);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function getTutorCommission($table, $colName, $colValue,$colName2,$colValue2)
    {
        $builder = $this->db->table($table);
        $builder->selectSum('amount', 'Amount');
        $builder->where($colName2, $colValue2);
        $builder->where($colName, $colValue);
        $builder->groupBy("tutorId");
        $query = $builder->get();
        $data=$query->getResultArray();
        
        return $data[0]['Amount'];
    }

    public function checkStudentPercentage($table, $colName, $colValue)
    {
         $builder = $this->db->table($table);
         $builder->select('user_id, COUNT(user_id) as total_row, sum(percentage) as total_percentage, sum(percentage)/COUNT(user_id) as percentage');
         $builder->where($colName, $colValue);
         $builder->where('status', 0);
         $builder->groupBy('user_id');  
         $query = $builder->get();
         $data=$query->getResultArray();
         return $data;
    }

    public function whiteboardPurches( $table , $user_id)
    {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where("user_id" , $user_id );
        $builder->where('endTime >',time());
        $builder->where('status',1);
        $builder->whereIn('course_id' , [ 53 , 54 ] );
        $query = $builder->get();
        return count( $query->getResultArray() ) > 0 ? 1:0 ;
    }

    public function checkRegisterCourse($course_id,$user_id){
        $builder = $this->db->table('tbl_registered_course');
        $builder->where('user_id',$user_id);
        $builder->where('course_id',$course_id);
        $builder->where('status',1);
        return $builder->countAll();
    }

    public function getCountry($country_id){
        $builder = $this->db->table('tbl_country');
        $builder->select('*');
        $builder->where('id', $country_id);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data[0];
    }

    public function whereIn($table, $selectorCol, $values = [], $conditions = [])
    {
        if($values == -1)
        {
            $res=$this->db->table($table)->where($conditions)->get()->getResultArray();
        }
        else
        {
            $res=$this->db->table($table)->whereIn($selectorCol,$values)->where($conditions)->get()
            ->getResultArray(); 
        }
        return $res;
    }

    public function insertId($table, $data)
    {
        $this->db->table($table)->insert($data);

        $insert_id = $this->db->insertID();
        return $insert_id;
    }

    public function insertInfo($table, $data)
    {
        $this->db->table($table)->insert($data);
        return $this->db->insertID();
    }

    public function insertBatch($table, $data)
    {
        $this->db->table($table)->insertBatch($data);
        return $this->db->insertID();
    }

    
    public function getRow($table, $colName, $colValue)
    {
        $builder = $this->db->table($table);
        $builder->select('*');
        $builder->where($colName, $colValue);
        $query = $builder->get();
        return $query->getRowArray();
    }

    
    public function deleteInfo($table, $colName, $colValue)
    {
        //echo $table . '+' .$colName.'+'.$colValue;//die;
        $this->db->table($table)->where($colName, $colValue)->delete();
    }

    public function get_all_whereTwo($select, $table, $columnName, $columnValue , $columnNameTwo , $columnValueTwo)
    {
        $builder = $this->db->table($table);
        $builder->select($select);
        $builder->where($columnName, $columnValue);
        $builder->where($columnNameTwo, $columnValueTwo);

        $query = $builder->get();
        return $query->getResultArray();
    }

    public function get_all_where($select, $table, $columnName, $columnValue)
    {
        $builder = $this->db->table($table);
        $builder->select($select);
        $builder->where($columnName, $columnValue);
        $query = $builder->get();
        return $query->getResultArray();
    }

    public function whereNotIn($table, $selectorCol, $filter)
    {
        $res = $this->db->table($table)->whereNotIn($selectorCol, $filter)->get()->getResultArray();
        return $res;
    }

    public function courseName($courseId)
    {
        $res = $this->db->table('tbl_course')->select('courseName')->where('id', $courseId)->get()
               ->getResultArray();

        return isset($res[0]) ? $res[0]['courseName'] : '';
    }

    public function getallcreativeDetails(){

        $builder = $this->db->table('tbl_registered_course');
        // SELECT * FROM `tbl_registered_course` WHERE `assign_examine`=1 and (`course_id`=63||course_id=62);
        $builder->select('*');
        $builder->join('tbl_useraccount', 'tbl_registered_course.user_id = tbl_useraccount.id', 'LEFT');
        $builder->where('tbl_registered_course.assign_examine', 1);
        $value = array(62, 63);
        $builder->whereIn('tbl_registered_course.course_id', $value);
        $query =$builder->get();
        
        return $query->getResultArray();
        
    }

    public function idea_created_student_list(){
        $builder = $this->db->table('tbl_registered_course');
        $builder->select('*');
        $builder->join('tbl_useraccount', 'tbl_registered_course.user_id = tbl_useraccount.id', 'LEFT');
        $value = array(62, 63);
        $builder->whereIn('tbl_registered_course.course_id', $value);

        $query = $builder->get();
        return $query->getResultArray();
        
    }

    public function idea_created_students(){
        $builder = $this->db->table('idea_student_ans');
        $builder->distinct();
        $builder->select('student_id');
        $builder->where('teacher_correction',0);
        $query = $builder->get();
        return $query->getResultArray();
        
    }

    public function get_all_tutor(){

        $builder = $this->db->table('tbl_useraccount');
        $builder->select('*');
        $builder->where('user_type', 3);
        $builder->orWhere('user_type', 7);

        $query = $builder->get();
        return $query->getResultArray();
    }


    public function checkDepositDetails($table,$countryId){
        $builder = $this->db->table($table);
        $builder->where('country_id',$countryId);
        $data=$builder->countAllResults();
        return $data;
    }
    
    public function checkDepositDetailsUpdate($table,$countryId,$data){
        $query_result=$this->db->table($table)->where('country_id',$countryId)->update($data);
        return $query_result; 
    }

    public function checkDepositDetailsInsert($table,$countryId,$data){
        $query_result = $this->db->table($table)->insert($data);
        return $query_result;    
    }

    public function get_course($user_type, $country_id)
    {  
        $builder = $this->db->table('tbl_course');
        $builder->select('*');
        $builder->where('user_type', $user_type);
        $builder->where('country_id', $country_id);

        $query = $builder->get();
        return $query->getResultArray();
    }

    public function copy($source_table, $conditions, $changeCol = 0, $changeVal = 0)
    {
        $condition = '';
        
        $flag = 0;
        $totCond = count($conditions);
        foreach ($conditions as $key => $value) {
            $condition .= "`". $key ."`=".$value."";
            
            if (++$flag < $totCond) {
                $condition .= ' AND ';
            }
        }
        $this->db->query("CREATE TEMPORARY TABLE temp_table AS SELECT * FROM $source_table where $condition");
  
        
        if ($changeCol) {
            $this->db->query("UPDATE temp_table SET $changeCol=$changeVal,id=0");
        } else {
            $this->db->query("UPDATE temp_table SET id=0");
        }

        $this->db->query("INSERT INTO $source_table SELECT * FROM temp_table");
        $insertId = $this->db->insertID();
        
        $this->db->query("DROP TEMPORARY TABLE temp_table");

        return $insertId;
    }

    public function updateSmsApiSettings($a_settings_grop, $a_settings_key, $a_settings_value)
    {
        $builder = $this->db->table('tbl_setting');
        $builder->set('setting_value', $a_settings_value);
        $builder->where('setting_type', $a_settings_grop);
        $builder->where('setting_key', $a_settings_key);
        $rs = $builder->update();
        return 1;
    }

    public function insertTbl($dataToInsert , $tbl)
    {
        $this->db->table($tbl)->insert($dataToInsert);

        return $this->db->insertID();
    }

    public function updateInfoStripe($table, $colName, $colValue, $data)
    {
        $builder = $this->db->table($table);
        $builder->where($colName, $colValue);
        $builder->where("setting_type", "stripe");
        $builder->update($data);
    }

    public function updateInfoPaypal($table, $colName, $colValue, $data)
    {
        $builder = $this->db->table($table);
        $builder->where($colName, $colValue);
        $builder->where("setting_type", "paypal");
        $builder->update($data);
    }

    public function getModule($tableName, $params)
    {
        // Previous query
        // $this->db->select('tbl_module.*,tbl_course.courseName,tbl_subject.subject_name,tbl_chapter.chapterName,tbl_country.countryName');
        // $this->db->from($tableName);
        // $this->db->join('tbl_course', 'tbl_course.id = tbl_module.course_id', 'LEFT');
        // $this->db->join('tbl_subject', 'tbl_subject.subject_id = tbl_module.subject', 'LEFT');
        // $this->db->join('tbl_chapter', 'tbl_chapter.id = tbl_module.chapter', 'LEFT');
        // $this->db->join('tbl_country', 'tbl_country.id = tbl_module.country', 'LEFT');
        // $this->db->where($params);
        // $query = $this->db->get();
        // return $query->result_array();
        $builder = $this->db->table($tableName);
        $builder->select('tbl_module.*,tbl_course.courseName,tbl_subject.subject_name,tbl_chapter.chapterName,tbl_country.countryName,BIN(tbl_module.moduleName) as module_name');
        $builder->join('tbl_course', 'tbl_course.id = tbl_module.course_id', 'LEFT');
        $builder->join('tbl_subject', 'tbl_subject.subject_id = tbl_module.subject', 'LEFT');
        $builder->join('tbl_chapter', 'tbl_chapter.id = tbl_module.chapter', 'LEFT');
        $builder->join('tbl_country', 'tbl_country.id = tbl_module.country', 'LEFT');
        $builder->where($params);
        $builder->orderBy('tbl_module.moduleType','asc');
        $builder->orderBy('tbl_module.subject','asc');
        $builder->orderBy('tbl_module.studentGrade','asc');
        $builder->orderBy('LENGTH(tbl_module.moduleName)');
        $builder->orderBy('tbl_module.moduleName','asc');
        $query = $builder->get();
        //echo $this->db->last_query();die();
        return $query->getResultArray();
    }

    public function get_this_idea($checkout_id){
        $builder = $this->db->table('idea_check_workout');
        $builder->select('*');
        $builder->where('id', $checkout_id);
        
        $query = $builder->get();
        $result = $query->getRow();
        
       // echo $result->idea_id;
       $builder = $this->db->table('idea_student_ans');
       $builder->select('*');
       $builder->join('idea_info', 'idea_student_ans.idea_id = idea_info.id', 'LEFT');
       $builder->where('idea_student_ans.idea_id', $result->idea_id);
       $builder->where('idea_student_ans.student_id', $result->student_id);
       
       $query2 = $builder->get();
       return $query2->getResultArray();

    }

    public function get_ideas($checkout_id){
        $builder = $this->db->table('idea_check_workout');
        $builder->select('*');
        $builder->where('id', $checkout_id);
        
        $query = $builder->get();
        $result = $query->getRow();
        
       // echo $result->idea_id;
       $builder = $this->db->table('idea_student_ans');
       $builder->select('*');
       $builder->join('idea_info', 'idea_student_ans.idea_id = idea_info.id', 'LEFT');
       $builder->where('idea_student_ans.idea_id', $result->idea_id);
       
       $query2 = $builder->get();
       return $query2->getResultArray();

    }

    public function get_admin_workout($checkout_id){
        $builder = $this->db->table('idea_check_workout');
        $builder->select('*');
        $builder->where('id', $checkout_id);
        
        $query = $this->db->get();
        return $query->getResultArray();

    }
    
    public function update_question_notification($question_id){
        $data['admin_seen'] = 1;

        $builder = $this->db->table('tbl_question');
        $builder->where("id", $question_id);
        $update = $builder->update($data);
        return $update;
    }
}