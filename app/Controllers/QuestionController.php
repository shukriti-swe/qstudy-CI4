<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class QuestionController extends BaseController
{
    public function check_idea_short_question()
    {
        $short_question_title = $this->request->getVar('short_title');

        $builder = $this->db->table('idea_info'); 
        $builder->select('*');
        $builder->where('shot_question_title',$short_question_title);
        $query = $builder->get();
        $results= $query->getResultArray();
        //echo "hello <pre>";print_r($results);die();
        if(!empty($results)){
            echo 1;
        }else{
            echo 2;
        }
    }

    public function save_idea()
    {

        $data = array();
        $data = [
            'idea_title' => $this->request->getVar('idea_title'),
            'question_description' => $this->request->getVar('question_description'),
            'total_word' => $this->request->getVar('total_word'),
        ];

        $this->db->table('idea_save_temp')->insert($data);
        $last_id= $this->db->insertID();

        $builder = $this->db->table('idea_save_temp'); 
        $builder->select('*');
        $builder->where('id',$last_id);
        $query = $builder->get();
        $results= $query->getResultArray();
        echo json_encode($results);
        
    }

    public function deleteQuestion($questionId = 0)
    {
        $QuestionClass=new \QuestionClass();
        /*delete question info from tbl_question
        delete all question module relationship*/
        $delItems = $QuestionClass->delete('tbl_question', 'id', $questionId);
        $QuestionClass->delete('tbl_modulequestion', 'question_id', $questionId);
        if ($delItems) {
            echo 'true';
        } else {
            echo 'false';
        }
    }//end deleteQuestion()
	
	public function comprehension_image_upload(){
        
        $return=array();
        $image_file = $this->request->getFile('file');
        
		if($image_file != ''){
			$image_file_name = $image_file->getRandomName();
            $uploaded=$image_file->move(ROOTPATH.'public/assets/comprehension/',$image_file_name);		
			if($uploaded){ 			
				$main_image = $image_file_name;
				echo $main_image;
			}else{
				$return['main_image_error']	= array('error' => 'error do not uploaded');
				return $return;
			}
		}  

    }

    public function glossary_image_upload(){
        //echo $_FILES['file']['name'];die();
        $return=array();
        $image_file = $this->request->getFile('file');

        if($image_file != ''){
			$image_file_name = $image_file->getRandomName();
            $uploaded=$image_file->move(ROOTPATH.'public/assets/glossary/',$image_file_name);		
			if($uploaded){ 			
				$main_image = $image_file_name;
				echo $main_image;
			}else{
				$return['main_image_error']	= array('error' => 'error do not uploaded');
				return $return;
			}
		} 
 
    }

    public function html_text_to_array(){
        $wrrite_input = $this->request->getVar('wrrite_input');

        $get_sentences =  preg_split('/<\/\s*p\s*>/', $wrrite_input);
        // echo "<pre>";print_r($get_sentences);die();
        $k=1;
        $al_words = '';
        foreach($get_sentences as $sentence){
           $al_words .= '<p style="display: flex;flex-wrap: wrap;">';
           $words = explode(' ',strip_tags($sentence));
           if($k==1){
              $i=0;
           }
           foreach($words as $word){
              $al_words .= '<span class="hint_words word_no'.$i.'" data-index="'.$i.'">'.$word.'</span>';
           $i++; }
           $al_words .= '</p><br>';

        $k++; }

           echo $al_words;
    }

    public function question_duplicate()
    {
        $QuestionClass=new \QuestionClass();
        $data = array();
        $user_id  = $this->request->getVar('user_id');
        $questionId  = $this->request->getVar('qId');

        $parentQuestion = $QuestionClass->info($questionId);
        unset($parentQuestion['id']);
        if (count($parentQuestion)) {
            $parentQuestion['country'] = $parentQuestion['country'] ? $parentQuestion['country']: 1;
            $QuestionClass->insert('tbl_question', $parentQuestion);
            $duclicate = $QuestionClass->last_question($user_id);

            $order_no = $QuestionClass->last_question_order($user_id, $duclicate[0]->questionType);

           
            $output ='<li style="background-color:2CE316;" data-id="'.$duclicate[0]->questionType.'_'.$duclicate[0]->id.'" id="q_<?=$i?>_'.$duclicate[0]->questionType.'"> 
                              <a href="question_edit/'.$duclicate[0]->questionType.'/'.$duclicate[0]->id.'" target="_blank">Q'.$order_no.'</a>
                             </li>';

            $var = [

                "questionType" =>$duclicate[0]->questionType,
                "element" =>$output

            ];    

            array_push($data, $var);             

            print_r(json_encode($data));
            
        } else {
            echo 'false';
        }
    }

    public function search_idea()
    {
        $search_text = $this->request->getVar('search_text');
        $search_type = $this->request->getVar('search_type');
        $data['search_type'] = $search_type;
        // echo $search_text.'//'.$search_type;die();
        if($search_type == 1){
            $builder = $this->db->table('idea_info'); 
            $builder->select('*');
            $builder->where('allows_online',1);
            $builder->like('shot_question_title',$search_text,'after');
            //$this->db->or_like('large_question_title',$search_text);
            $builder->orderBy('shot_question_title');
            $query = $builder->get();
            $results= $query->getResultArray();
            //echo  $this->db->last_query();die();
         
            $data['questions']= $results;
            echo json_encode($data);

        }else if($search_type == 2){
            $builder = $this->db->table('idea_description'); 
            $builder->select('*');
            $builder->from('idea_description');
            $builder->where('allow_online',1);
            $builder->like('idea_name',$search_text);
            $query = $builder->get();
            $results['ideas']= $query->result_array();
            $data['ideas']= $results;
            echo json_encode($data);
        }

    }

    public function search_image_idea()
    {
        $search_text = $this->request->getVar('search_text');
        
        $builder = $this->db->table('idea_info'); 
        $builder->select('*');
        $builder->where('allows_online',1);
        $builder->where('LENGTH(image_title) >',0); 
        if(!empty($search_text)){
            $builder->like('image_title',$search_text,'after');
        }
        $builder->orderBy('image_title');
        $query = $builder->get();
        
        $results= $query->getResultArray();

        $data['questions']= $results;
        echo json_encode($data);
    }

    public function get_idea()
    {
        $question_id = $this->request->getVar('question_id');
        $builder = $this->db->table('idea_description id'); 
        $builder->select('id.*,ii.*,tbl_useraccount.name,tbl_question.created_at as q_created_at');
      
        $builder->join('idea_info ii','ii.question_id = id.question_id');
        $builder->join('tbl_question','tbl_question.id = ii.question_id');
        $builder->join('tbl_useraccount','tbl_useraccount.id = tbl_question.user_id');
        $builder->where('ii.question_id',$question_id);
        $builder->orderBy('ii.id desc');
        $builder->limit(1);
        $query = $builder->get();
        $results= $query->getResultArray();
        echo json_encode($results);
        
    }

    public function type_one_box_one_image_upload(){
        $return=array();
        $image_file = $this->request->getFile('file');
        
		if($image_file != ''){
			$image_file_name = $image_file->getRandomName();
            $uploaded=$image_file->move(ROOTPATH.'public/assets/imageQuiz/',$image_file_name);		
			if($uploaded){ 			
				$main_image = $image_file_name;
				echo $main_image;
			}else{
				$return['main_image_error']	= array('error' => 'error do not uploaded');
				return $return;
			}
		} 
    }
    public function type_one_box_one_hint_image_upload(){
        $return=array();
        $image_file = $this->request->getFile('file');
        
		if($image_file != ''){
			$image_file_name = $image_file->getRandomName();
            $uploaded=$image_file->move(ROOTPATH.'public/assets/imageQuiz/',$image_file_name);		
			if($uploaded){ 			
				$main_image = $image_file_name;
				echo $main_image;
			}else{
				$return['main_image_error']	= array('error' => 'error do not uploaded');
				return $return;
			}
		} 
    }

    public function type_three_box_one_image_upload($type=null){
        // echo $_FILES['file']['name'];die();
        $return=array();
        $image_file = $this->request->getFile('file');
        $height_max = 0;
		if($image_file != ''){
            
			// $config['upload_path'] = './assets/imageQuiz';
			// $config['allowed_types'] = '*';
			// $config['file_name'] = $_FILES['file']['name'];
			//  $config['min_width']  = '200';
            if(isset($type)){
               if($type==3){
                $size = getimagesize($image_file);
                // echo "<pre>";print_r($size);die();
                if($size[1]<=700){
                    $height_max = 1;
                }
               }
            }
            $image_file_name = $image_file->getRandomName();
            $uploaded=$image_file->move(ROOTPATH.'public/assets/imageQuiz/',$image_file_name);
			//  $config['min_height']  = '200';

			if($uploaded && $height_max !=1){
				$main_image = $image_file_name;
				echo $main_image;
                // die();
				// $this->_create_thumbs($uploadData['file_name']);
                
			}else{
                //print_r($this->upload->display_errors());die();
				$return['main_image_error']	= array('error' => 'error do not uploaded');
				return $return;
			}
		}  
    }

}
