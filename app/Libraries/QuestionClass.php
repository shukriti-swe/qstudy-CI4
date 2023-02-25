<?php
use App\Models\TblSetting;

class QuestionClass
{
    public function __construct(){
        $this->db = db_connect();         
    }

    public function vocabularyCommission($id)
    {
        return $array = $this->db->table('dictionary_payment')->where('word_creator',$id)->get()->getRow();
        // print_r($array);
    }

    public function getIdea()
    {
        $builder = $this->db->table('idea_save_temp');
        $builder->select('*');
        $query = $builder->get();
        $data=$query->getResultArray();
        return $data;
    }

    public function search($tableName, $params)
    {
        $res = $this->db->table($tableName)->where($params)->get()->getResultArray();

        return $res;
    }

    public function groupedWordItems()
    {
        $builder = $this->db->table('tbl_question');
        $builder->select('tbl_question.id word_id,answer word,tbl_useraccount.name word_creator, user_id creator_id');
        $builder->select('tbl_question.created ques_created_at, word_approved');
        $builder->select('tbl_useraccount.subscription_type creator_type');
        $builder->select('tbl_country.countryName creator_country');
        $builder->where('dictionary_item', 1);
        $builder->join('tbl_useraccount', 'tbl_useraccount.id=tbl_question.user_id', "left");
        $builder->join('tbl_usertype', 'tbl_useraccount.user_type=tbl_usertype.id', "left");
        $builder->join('tbl_country', 'tbl_useraccount.country_id=tbl_country.id', "left");
        $builder->orderBy('user_id', 'ASC');
        $query=$builder->get();
        $res=$query->getResultArray();
        return $res;
    }

    public function countDictWord()
    {
        $res = $this->db->table('tbl_question')->select('count(*) total')
            ->where('questionType', 3)
            ->where('dictionary_item', 1)
            ->get()
            ->getResultArray();  
        return isset($res[0]['total']) ? $res[0]['total']:0;
    }

    public function update($tableName, $selector, $value, $dataToUpdate)
    {
        $dataToUpdate['updated_at'] = time();
        $this->db->table($tableName)->where($selector, $value)->update($dataToUpdate);
    }//end update()

    public function insert($tableName, $dataToInsert)
    {
        $this->db->table($tableName)->insert($dataToInsert);
    } //end insert()

    public function delete($tableName, $selector, $value)
    {
        $this->db->table($tableName)->where($selector, $value)->delete();

        return $this->db->affectedRows();
    } //end delete()

    public function wordCreatorToPay()
    {
        
        return $this->db
        ->query("SELECT `dictionary_payment`.*,`name` FROM `dictionary_payment` left join `tbl_useraccount` on `tbl_useraccount`.id=`dictionary_payment`.`word_creator`")
        ->getResultArray();
        
        // return $this->db
        // ->query("SELECT `dictionary_payment`.*,`name` FROM `dictionary_payment` left join `tbl_useraccount` on `tbl_useraccount`.id=`dictionary_payment`.`word_creator`  where total_approved-total_paid>=".VOCABULARY_PAYMENT)
        // ->result_array();
        
        //echo $this->db->last_query();
    }

    public function allDictionaryWord()
    {
        $res = $this->db->table('tbl_question')->select('answer')->select('user_id')->where('dictionary_item', 1)
             ->orderBy('user_id', 'desc')
             ->get()
             ->getResultArray();
        $res = array_unique(array_column($res, 'answer'));
        return $res;
    }
	
	public function wordCreatorToPayCount()
    {
        return $this->db->query("SELECT `dictionary_payment`.*,`name` FROM `dictionary_payment` left join `tbl_useraccount` on `tbl_useraccount`.id=`dictionary_payment`.`word_creator`  where total_approved-total_paid>=".VOCABULARY_PAYMENT)
        ->getResultArray();
        //echo $this->db->last_query();
    }

    public function info($questionId)
    {
        $builder = $this->db->table('tbl_question');
        $builder->select('*');
        $builder->where('id', $questionId);
        $query = $builder->get();
        $data=$query->getResultArray();
        // echo $questionId."<pre>";print_r($data);die();
        return count($data[0]) ? $data[0] : [];
    } //end info()

    public function last_question($user_id)
    {
        $builder = $this->db->table('tbl_question');
        $builder->select('*');
        $builder->where('user_id', $user_id);
        $builder->limit(1);
        $builder->orderBy('id',"DESC");
        $query = $builder->get();
        $data=$query->getResult();
        return $data;
    }

    public function last_question_order($user_id , $questionType)
    {
        $builder = $this->db->table('tbl_question');
        $builder->select('*');
        $builder->where('user_id', $user_id);
        $builder->where('questionType', $questionType);
        $query = $builder->get();
        $data=$query->getResult();
        return count($data);
    }

}   