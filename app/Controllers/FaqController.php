<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class FaqController extends BaseController
{
    public function allFaq()
    {
        $FaqClass=new \FaqClass();
        $data['allFaqs'] = $FaqClass->allFaqs();
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';

        return view('faqs/all_faq', $data);
    }


    public function editFaq($faqId)
    {
        $FaqClass=new \FaqClass();
        $post = $this->request->getVar();
        //$this->form->validation
        if (!$post) {
            $data['maxSL'] = $FaqClass->lastItemId();
            $data['faq'] = $FaqClass->info(['id'=>$faqId]);
            
            if (!count($data['faq'])) {
                return view('errors/html/error_404');
            }

            $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
            return view('faqs/edit_faq', $data);
        } else {
            $dataToUpdate = [
                'title' =>$post['faq_title'],
                'body' =>$post['faq_body_new'],
                'show_in_home' =>isset($post['show_in_home']) ? 1 : 0,
                'item_type'    => strtolower(str_replace(' ', '_', $post['faq_title'])),
            ];

            $conditions = ['id'=>$faqId];
            $FaqClass->update($conditions, $dataToUpdate);
            $this->session->set('success_msg', 'FAQ updated successfully');
            return redirect()->to(base_url("faq/edit/".$faqId));
        }
    }

    public function deleteFaq($faqId)
    {
        $FaqClass=new \FaqClass();
        $FaqClass->delete($faqId);
        echo 'true';
    }


    public function addFaq()
    {
        $FaqClass=new \FaqClass();
        $post = $this->request->getVar();
        if (!$post) {
            $data['maxSL'] = $FaqClass->lastItemId(); //last item id
            $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
            return view('faqs/add_faq', $data);
        } else {
            /*update all serial below requested serial by +1*/
            $FaqClass->reorderSerial($post['serial_num']);

            /*insert requested faq after serial update*/
            $dataToInsert = [
                'serial'       => $post['serial_num'],
                'title'        => $post['title'],
                'show_in_home' => isset($post['show_in_home'])?$post['show_in_home']:1,
                'item_type'    => strtolower(str_replace(' ', '_', $post['title'])),

            ];
            
            $insID = $FaqClass->insert($dataToInsert);
            if ($insID) {
                $this->session->set('success_msg', 'FAQ added successfully');
            } else {
                $this->session->set('error_msg', 'FAQ added successfully');
            }

            return redirect()->to(base_url("faq/all"));
        }
    }

    public function serialize($faqId , $serial_id , $serial_id_new)
    {
        // echo $faqId.'<br>';
        // echo $serial_id.'<br>';
        // echo $serial_id_new.'<br>';
        // die();
        
        $FaqClass=new \FaqClass();

        $insID = $FaqClass->serialize($serial_id_new);

        if (count($insID)) {
            $FaqClass->serialize_update($insID[0]['id'] , $serial_id);
            $FaqClass->serialize_update($faqId , $serial_id_new);
            echo "serialized Complete";
            
        }else{
            echo "This Serial is not available you entered";
        }
        
    }
  

    public function addLandPageItems()
    {
        $FaqClass=new \FaqClass();
        $post = $this->request->getVar();
        // echo '<pre>';
        // print_r($post);die();
        if (!$post) {
            $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
            return view('faqs/frontPageItems/addItems', $data);
        } else {
            
            $dataToInsert = [
                'title'=>$post['title'],
                'body'=>$post['body'],
                'item_type' => $post['itemType'],
                'show_in_home' => 0,
            ];
            
            //video items
            if ($post['itemType']=='how_it_works ') {
                $dataToInsert['body'] = strip_tags($post['body']);
            }
            
            $itemExists = $FaqClass->info(['item_type'=>$post['itemType']]);
            if (count($itemExists)) {
                $cond = [ 'item_type'=>$post['itemType'] ];
                $FaqClass->update($cond, $dataToInsert);
            } else {
                $FaqClass->insert($dataToInsert);
            }
            $this->session->set('success_msg', 'Item recorded successfully!');
            return redirect()->to(base_url("faq/add/other"));
        }
    }


    public function getItemInfo()
    {
        $FaqClass=new \FaqClass();
        $post = $this->request->getVar();
        $itemExists = $FaqClass->info(['item_type'=>$post['itemType']]);

        if (count($itemExists)) {
            $arr = [
                'title'=> $itemExists['title'],
                'body'=> $itemExists['body'],
            ];
            echo json_encode($arr);
        } else {
            echo 'false';
        }
    }

    public function landPagevideoUpload()
    {
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        return view('faqs/video_help/video_upload', $data);
    }


    public function video()
    {
        $FaqClass=new \FaqClass();

        if ($this->request->getVar('serial_num') > 11) {
            $this->session->set('Failed', 'Serial Number Can not bigger than 10'); 
            return redirect()->to(base_url($_SERVER['HTTP_REFERER']));

        }else{

            if (!$this->validate('addDialogueValidate')) {
                    $files = $this->processVideoHelp($_POST);

                    $data['serial_num'] =  $this->request->getVar('serial_num');
                    $data['userfile'] = $files;
                    $FaqClass->insertTbl($data , 'video_helps');

                    $this->session->set('message', 'successfully Uploaded');
                    return redirect()->to(base_url($_SERVER['HTTP_REFERER']));

                }else{
                    $this->session->set('Failed', 'Serial Number Has Allready Been Taken '); 
                    return redirect()->to(base_url($_SERVER['HTTP_REFERER']));
                }

        }

    }

    public function processVideoHelp($items)
    {
        $arr = array();
        $array_one = array();
        $arr['speech_to_text'] = $items['title'];
        foreach ($arr['speech_to_text'] as $key => $value) {
            if (!empty($value)) {
                $v = [
                    "title" =>$value
                ];


                array_push($array_one, $v);
            }
            else{
                $v = [
                    "title" =>"none"
                ];
                array_push($array_one, $v);
            }
        }
        
        $uType = $this->session->get('userType');
        
        $files = $_FILES;
        $video_file=$this->request->getFileMultiple('video_file');
    //    $this->load->library('upload');

    //    $config['upload_path'] = 'assets/videoHelp/';
    //    $config['allowed_types'] = 'mp4';
    //    $config['overwrite'] = false;

       if(!empty($video_file))
       { 
        foreach($video_file as $l => $audios){

               $audios_file = $audios->getRandomName();
               $audios_upload=$audios->move(ROOTPATH . 'public/assets/videoHelp', $audios_file);
               if (!$audios_upload) {
                   $status = 'error';
                   $audio = $this->upload->display_errors('', '');
                   $var1 =[
                    "Audio"=>'none'
                  ];

                   array_push($array_one, $var1);
               } else {
                  $var2 =[
                    "Audio"=>'assets/videoHelp/'.$audios_file
                  ];

                  array_push($array_one, $var2);
                 
               }
        }
      }
        return json_encode($array_one);
        
    }

    
    public function landPagevideoList()
    {
        $FaqClass=new \FaqClass();

        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['videoList'] = $FaqClass->allData('video_helps');

        return view('faqs/video_help/video_list',$data);
    }

    public function videoEdit($id)
    {

        $FaqClass=new \FaqClass();
        $data['page_title'] = '.:: Q-Study :: Tutor yourself...';
        $data['data']  = $FaqClass->selectData( $id , 'video_helps');
        $files = json_decode($data['data'][0]['userfile'] , true);

        foreach ($files as $key => $value) {
            if (isset($value['Audio'])) {
                $video[] = $value['Audio'];
            }else{
                $title[] = $value['title'];
            }
        }

        $data['video'] = $video;
        $data['title'] = $title;
        $data['id'] = $id;
        // echo '<pre>';
        // print_r($data);die();
        return view('faqs/video_help/edit',$data);
    }

    public function videoupdate()
    {
		
        $FaqClass=new \FaqClass();
        if ($this->request->getVar('serial_num') > 10) {
            $this->session->set('Failed', 'Serial Number Can not bigger than 10'); 
            return redirect()->to(base_url($_SERVER['HTTP_REFERER']));
        }else{
			
            $ckId = $FaqClass->videoSerialize($this->request->getVar('serial_num'),'video_helps');
			 
            if (count($ckId)) {
                $ckId_file = json_decode($ckId[0]['userfile'] , true);

                foreach ($ckId_file as $key => $value) {
                    if (isset($value['title'])) {
                        $toUpdate[] = $value;
                    }else{
                        $toUpdate[] = $value;
                    }
                }

                if (isset($_POST['title'])) {
                    $files = $this->processVideoHelp($_POST);

                    $uploaded_file = json_decode($files , true);

                    foreach ($uploaded_file as $key => $value) {
                        if (isset($value['title'])) {
                            $toUpdate[] = $value;
                        }else{
                            $toUpdate[] = $value;
                        }
                    }
                }

                $data['serial_num'] = $this->request->getvar('serial_num');
                $data['userfile'] = json_encode($toUpdate);

                $FaqClass->videoSerializeUpdate($ckId[0]['id'] , 'video_helps' , $this->request->getVar('serial') );
                $FaqClass->videoHelpeUpdate( $this->request->getVar('id') ,  'video_helps' , $data );
             
                $this->session->set('message', 'Updated Complete');
                return redirect()->to($_SERVER['HTTP_REFERER']);
                
            }else{
                $this->session->set('Failed', 'This Serial is not available you entered'); 
                return redirect()->to($_SERVER['HTTP_REFERER']);
            }

        }
     }

     public function removeFile($serial_num , $FileSL)
     {
        $FaqClass=new \FaqClass();
         $toUpdate =array();
         $data = $FaqClass->selectData( $serial_num , 'video_helps');
 
         $files = json_decode($data[0]['userfile'] , true);
 
         foreach ($files as $key => $value) {
             if (isset($value['Audio'])) {
                 $video[] = $value['Audio'];
             }else{
                 $title[] = $value['title'];
             }
         }
 
         unset($video[$FileSL]);
         unset($title[$FileSL]);
 
         foreach ($title as $key => $value) {
             $toUpdate[]['title'] = $value;
         }
 
         foreach ($video as $key => $value) {
             $toUpdate[]['Audio'] = $value;
         }
 
         $info['userfile'] = json_encode($toUpdate);
 
         $FaqClass->videoHelpeUpdate($serial_num, 'video_helps', $info);
         echo 1;
     }

     public function deleteVideo($id)
     {
         $FaqClass=new \FaqClass();
         $FaqClass->deleteVideo($id, 'video_helps');
         $this->session->set('message', 'Delted Successfully');
         return redirect()->to(base_url($_SERVER['HTTP_REFERER']));
     }

     public function ShowVideo($id)
     {
         $FaqClass=new \FaqClass();
         $data =   $FaqClass->selectData($id ,'video_helps');
         $files = json_decode($data[0]['userfile'] , true);
 
         $title_num  = count($files) / 2 ;
 
         foreach ($files as $key => $value) {
             if ($key < $title_num) {
                 $title[] = $value;
             }else{
                 $videos[] = $value;
             }
         }
 
         //$data['title'] =$title;
         $data['videos'] =$videos;
         $html= require_once(APPPATH.'Views/faqs/video_help/showList.php');
         
         echo $html;
     }
}
