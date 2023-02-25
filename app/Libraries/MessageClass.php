<?php

class MessageClass
{
    public function __construct(){
        $this->db = db_connect();         
    }

    public function allTopics()
    {
        $builder = $this->db->table('message_topics');
        $builder->where('creator_id', $_SESSION['user_id']); 
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function insert($tableName, $dataToInsert)
    {
        $builder = $this->db->table($tableName);
        $builder->insert($dataToInsert);
        return $this->db->insertID();
    }

    public function delete($tableName, $conditions)
    {
        $builder = $this->db->table($tableName);
        $builder->where($conditions);
        $builder->delete();
        return $this->db->affectedRows();
    }

    public function get_message_by_topic($topic_id)
    {
        $builder = $this->db->table('messages');
        $builder->select('messages.*,message_topics.topic AS topic_name');
        $builder->join('message_topics', 'messages.topic = message_topics.id', 'LEFT');
        $builder->where('messages.topic', $topic_id);
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function info($tableName, $conditions)
    {
        $builder = $this->db->table($tableName);
        $builder->where($conditions);
        $query = $builder->get();
        $data=$query->getResultArray();
        
        return count($data) ? $data[0] : [];
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

    public function updateInfo($table, $colName, $colValue, $data)
    {
        $builder = $this->db->table($table);
        $builder->where($colName, $colValue);
        $builder->update($data);
    }

    public function deleteInfo($table, $colName, $colValue)
    {
        //echo $table . '+' .$colName.'+'.$colValue;//die;
        $builder = $this->db->table($table);
        $builder->where($colName, $colValue);
        $builder->delete();
    }

    
    public function message_info($message_id)
    {
        $builder = $this->db->table('messages');
        $builder->select('messages.*,,message_topics.topic AS topic_name,message_schedule.*');
        $builder->join('message_schedule', 'messages.id = message_schedule.message_id', 'LEFT');
        $builder->join('message_topics', 'messages.topic = message_topics.id', 'LEFT');
        $builder->where('messages.id', $message_id);
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function messageForToday()
    {
//        return $this->db
//            ->select("messages.*,DATE_FORMAT(messages.schedule_date, '%Y-%m-%d') as schedule_date,message_topics.topic AS title")
//          ->join("message_topics","messages.topic = message_topics.id","LEFT")
//            ->where("DATE(schedule_date)=CURDATE()")
//            ->get('messages')
//            ->result_array();
        $builder = $this->db->table('messages');
        $builder->select("messages.*,messages.schedule_date as schedule_date,message_schedule.schedule_date as notice_date, message_topics.topic AS title");
        $builder->join("message_topics", "messages.topic = message_topics.id", "LEFT");
        $builder->join("message_schedule", "messages.id = message_schedule.message_id", "LEFT");
        $builder->where("DATE(message_schedule.schedule_date)=CURDATE()");
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function get_all_student_by_grade($student_grade,$school_id)
    {
      	//echo $student_grade;die();
        $builder = $this->db->table('tbl_enrollment');
        $builder->select('tbl_enrollment.*');
        $builder->join('tbl_useraccount', 'tbl_enrollment.st_id = tbl_useraccount.id','LEFT');
        $builder->where('tbl_useraccount.student_grade', $student_grade);
        $builder->where('tbl_enrollment.sct_id',$school_id);
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function get_all_student_by_school($school_id)
    {
        $builder = $this->db->table('tbl_enrollment');
        $builder->select('tbl_enrollment.*'); 
        $builder->join('tbl_useraccount', 'tbl_enrollment.st_id = tbl_useraccount.id', 'LEFT');
        $builder->where('tbl_enrollment.sct_id', $school_id);
        $query = $builder->get();
        $data=$query->getResultArray();

        return $data;
    }

    public function allStudentEmail($userIds)
    {
        $builder = $this->db->table('tbl_useraccount as student');
        $builder->select('student.user_email as student_email,student.user_type as type, parent.user_email as parent_email');    
        $builder->join('tbl_useraccount as parent', 'student.parent_id = parent.id', 'left');
        $builder->whereIn('student.id', $userIds);
        $query = $builder->get();
        $data=$query->getResultArray();
        
   
        return $data;
          
    }
    
    

}