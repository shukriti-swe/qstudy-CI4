<?= $this->extend('preview/preview_master'); ?>
<?= $this->section('content'); ?>

<script src="<?php echo base_url(); ?>/assets/ckeditor/plugins/ckeditor_wiris/integration/WIRISplugins.js?viewer=image"></script>
<style type="text/css">
  
  .qstudy_module_video{
    position: absolute;
    width: 300px;
    height: 230px;
    top: 35px;
    border: 1px solid #ccc;
    background: #fff;
    z-index: 5;
    display: none;
  }
  .qstudy_module_video .header{
    width: 100%;
    border-bottom: 1px solid #ccc;
    text-align: right
  }
  .qstudy_module_video .header span{
    padding: 3px 10px;
    background: #e3e1e1;
    font-weight: bold;
    cursor: pointer;
  }
  .qstudy_module_video .video-content{
    padding: 10px;
    width: 100%;
    height: 89%;
    overflow-y: scroll;
  }
  .qstudy_module_video .video-content p{

    border: 1px solid #a2a0a0;
    padding-left: 5px;
    background: #f6f6f6;
    margin-bottom: 5px;
    cursor: pointer;
  }
  .no_instruction{
    position: absolute;
    top: 40px;
    background: #c1fcc1;
    padding: 5px;
    border-radius: 5px;
    display: none;
  }
.description_video{
   position: relative;
}
.description_class{
    position: absolute;
    left: 45px;
}
.question_video_class{
    position: absolute;
    left: 70px;
}
.rs_word_limt .top_word_limt > div{
   flex-basis: 0;
    -webkit-box-flex: 1;
    -ms-flex-positive: 1;
    flex-grow: 1;
    max-width: 100%;
}
.b-btn{
display:inline-block ;
}
.tutor_ans_modal{
   max-height: 300px;
    overflow-y: auto;
}
@media only screen and (min-width:768px) and (max-width:1023px){
   .rs_word_limt .top_word_limt > div{
      font-size: 9px;
}
}
.workout_menu ul li {
    display: inline-block;
    margin-right: 5px;
}

.ss_timer {
    margin-left: 5px;
    display: inline-block;
    background: #eeeef0;
    border: 0;
    min-width: 110px;
    text-align: center;
}
</style>

<div class="" style="margin-left: 15px;">
   <div class="row">
      <div class="col-md-12">
      <?php
$question_order_array = array_column($total_question, 'question_order');
$last_question_order = end($question_order_array);
$this->session=session();
$key = $question_info_s[0]['question_order'];
date_default_timezone_set($time_zone_new);
$module_time = time();

if ($tutorial_ans_info) {
    $temp_table_ans_info = json_decode($tutorial_ans_info[0]['st_ans'], true);
    $desired = $temp_table_ans_info;
} else {
    $desired = $this->session->get('data');
}


// $question_time = explode(':', $question_info_s[0]['questionTime']);
$hour = 0;
$minute = 0;
$second = 0;

if (is_numeric($idea_info[0]['time_hour'])) {
   $hour = $idea_info[0]['time_hour'];
} if (is_numeric($idea_info[0]['time_min'])) {
   $minute = $idea_info[0]['time_min'];
} if (is_numeric($idea_info[0]['time_sec'])) {
   $second = $idea_info[0]['time_sec'];
}
$question_time_in_second = 0;
$question_time_in_second = ($hour * 3600) + ($minute * 60) + $second ;
$moduleOptionalTime = 0;
if ($question_info_s[0]['moduleType'] == 2 && $question_info_s[0]['optionalTime'] != 0)
{
    $moduleOptionalTime = $question_info_s[0]['optionalTime'];
}

$passTime = time() - $_SESSION['exam_start'] ;
$setTime = 0;
if($moduleOptionalTime <= 0)
{
    if ($question_time_in_second > 0)
    {
        $setTime = $question_time_in_second;
    }

}else{
    $moduleOptionalTime = $moduleOptionalTime - $passTime;
    if ($question_time_in_second <= 0)
    {
        $setTime = $moduleOptionalTime;
    }else{
        if ($question_time_in_second > $moduleOptionalTime)
        {
            $setTime = $moduleOptionalTime;
        }else{
            $setTime = $question_time_in_second;
        }

    }
}

$link_next = "javascript:void(0);";
$link = "javascript:void(0);";

if (is_array($desired)) {
    $link_key = $key - 1;
    if (array_key_exists($link_key, $desired) && !$tutorial_ans_info) {
        $link = $desired[$link_key]['link'];
    }
    $link_key_next = $key;
    if (array_key_exists($link_key_next, $desired) && !$tutorial_ans_info) {
        $question_id = $question_info_s[0]['question_order'] + 1;
        $link1 = base_url();
        $link_next = $link1 . 'get_tutor_tutorial_module/' . $question_info_s[0]['module_id'] . '/' . $question_id;
    }
}

$module_type = $question_info_s[0]['moduleType'];

$videoName = strlen($module_info[0]['video_name'])>1 ? $module_info[0]['video_name'] : 'Subject Video';
?>
<?php 


foreach ($total_question as $ind) {

if ($ind["question_type"] == 14) {
  $chk = $ind["question_order"];
 }

} 
  ?>
<!--         ***** Only For Special Exam *****         -->
<?php if ($module_type == 3) { ?>
    <input type="hidden" id="exam_end" value="<?php echo strtotime($module_info[0]['exam_end']);?>" name="exam_end" />
    <input type="hidden" id="now" value="<?php echo $module_time;?>" name="now" />
    <input type="hidden" id="optionalTime" value="<?php echo $module_info[0]['optionalTime'];?>" name="optionalTime" />
    <input type="hidden" id="exact_time" value="<?php echo $this->session->get('exact_time');?>" />
<?php }?>
<!--         ***** For Everyday Study & Tutorial *****         -->
<?php if ($module_type == 2 || $module_type == 1) { ?>
    <input type="hidden" id="exam_end" value="" name="exam_end" />
    <input type="hidden" id="now" value="<?php echo $module_time;?>" name="now" />
    <!--  <input type="hidden" id="optionalTime" value="--><?php //echo $question_time_in_second;?><!--" name="optionalTime" />-->
  <input type="hidden" id="optionalTime" value="<?php echo $setTime;?>" name="optionalTime" />
  
    <input type="hidden" id="exact_time" value="<?php echo $this->session->get('exact_time');?>" />
<?php }?>

<div class="ss_student_board">
    <div class="ss_s_b_top">
        <div class="ss_index_menu <?php //if ($module_type == 3) {
        ?>col-md-3<?php
        //}?>">
            <?php if ($module_type == 1) {
              
               if($this->session->get('userType')==3){?>

               <a href="<?php echo base_url();?>/all_tutors_by_type/2/1" style="display: inline-block;">Index</a>

               <?php }else{
               ?>
                <a href="<?php echo base_url();?>/all_tutors_by_type/<?php echo $total_question[0]['user_id'];?>/<?php echo $total_question[0]['moduleType'];?>" style="display: inline-block;">Index</a>
            <?php } } else {?>
                <!-- <a >Index</a> -->
            <?php }?>
            <?php  if ($module_info[0]['video_name']) { ?>
              <button class="btn btn_next" id="openVideo" style="margin-left: 25px;"><i class="fa fa-play" style="color:#35B6E7;margin-right: 5px;"></i><?php echo $videoName;  ?></button>
            <?php } ?>
        </div>

       
      <?php
      $col_class = 'col-sm-7';
      if (($module_type == 2 && $question_info_s[0]['optionalTime'] != 0) || $module_type == 3){
          $col_class = 'col-sm-4';
      }
      ?>
        <div style="text-align: center;" class="text-center <?php echo $col_class?><?php //if ($module_type != 3) {
        //echo 'col-sm-7';
        //} else {
        //echo 'col-sm-6';
        //}?> ss_next_pre_top_menu">
            <?php if ($question_info_s[0]['isCalculator']) : ?>
                <input type="hidden" name="" id="scientificCalc">
            <?php endif; ?>
            <?php if ($_SESSION['userType']==3||$_SESSION['userType']==7) :
                ?> <!-- only tutor&qstudy will be able to back next -->
                <?php if ($question_info_s[0]['moduleType'] == 1) { ?>
                <a class="btn btn_next" href="<?php echo $link; ?>"><i class="fa fa-caret-left" aria-hidden="true"></i> Back</a>
                <a class="btn btn_next" href="<?php echo $link_next; ?>"><i class="fa fa-caret-right" aria-hidden="true"></i> Next</a>
            <?php } else {?>
                <a class="btn btn_next" href="javascript:void(0);"><i class="fa fa-caret-left" aria-hidden="true"></i> Back</a>
                <a class="btn btn_next" href="javascript:void(0);"><i class="fa fa-caret-right" aria-hidden="true"></i> Next</a>
            <?php }?>
            <?php endif; ?>

            <a class="btn btn_next" id="draw" onClick="test()" data-toggle="modal" data-target=".bs-example-modal-lg" >
                Workout <img src="assets/images/icon_draw.png">
            </a>
        </div>
    </div>
            <div class="container-fluid">
               <div class="row">
                  <div class="ss_s_b_main" style="min-height: 100vh">
                     <div class="col-sm-7">
                        <div class="workout_menu" style=" padding-right: 15px;">
                           <ul>
                               
                              <li><a style="cursor:pointer" id="show_question"> <img src="assets/images/icon_draw.png"/> Instruction </a></li>

                              <?php if($idea_info[0]['large_question_allow']!=0){ ?>
                              <li><a style="cursor:pointer" id="show_questions"> Question(Click here) </a></li>
                              <?php }?>

                              <!-- <li><a  href="javascript:;"  data-toggle="modal" data-target="#show_question_idea"> Idea 1</a></li> -->

                              <?php
                              if($idea_info[0]['allow_idea']==1){
                                 $j=1;
                              // foreach($idea_description as $ideas){
                              ?>
                              <!-- <li><a class="idea_title_modal all_ideas" style="background: white; color:black;border: 1px solid #ddd7d7;" href="javascript:;" data-index="<?//=$ideas['idea_title'];?>" data-value="<?//=$ideas['idea_no'];?>" ><?//=$ideas['idea_name']?></a></li> -->
                              <?//php $j++; }?>
                              
                              <?php $k=1; foreach($student_ideas as $student_idea){?>
                                 <li style="display: none;" class="student_ans"><a class="view_student_idea"  style="background: white; color:black;border: 1px solid #ddd7d7;" href="javascript:;" data-index="<?=$student_idea['student_id'];?>" data-value="<?=$student_idea['question_id'];?>,<?=$student_idea['module_id'];?>">Idea-<?=$k;?></a></li>
                              <?php $k++;}?>

                              <?php $n=1; foreach($tutor_ideas as $tutor_idea){?>
                                 <li style="display: block;" class="tutor_ans"><a class="view_tutor_idea"  style="background: white; color:black;border: 1px solid #ddd7d7;" href="javascript:;" data-index="<?=$tutor_idea['tutor_id'];?>" data-value="<?=$tutor_idea['idea_id'];?>,<?=$tutor_idea['idea_no'];?>">Idea-<?=$n;?></a></li>
                              <?php $n++;}?>

                              <!-- <div><a href="javascript:;" data-toggle="modal" data-target="#show_question_idea"><i class="fa fa-address-card" style="font-size:48px;color:#fb8836f0"></i></a></div> -->
                              <div><a href="javascript:;" id="student_ideas"><i class="fa fa-address-card" style="font-size:40px;"></i></a></div>
                              <div><a href="javascript:;" id="tutor_ideas"><i class="fa fa-users" style="font-size:40px;margin-left:5px;"></i></a></div>
                              <div><a style="cursor:pointer;" id="show_question"><img src="assets/images/icon_a_left.png"></a></div>
                              <div><a style="cursor:pointer;" id="show_question"><img src="assets/images/icon_a_right.png"></a></div>
                              <?php }?>
                           </ul>
                        </div>
                        <div class="top_textprev">
                           
                           

                        <?php

                     
                        ?>
                           <?php if($idea_info[0]['shot_question_title']!=''){ ?>
                
                           <?php } 
                           if($idea_info[0]['short_ques_body']!=''){?>
                           <p>
                              <?php 
                                 $text=$idea_info[0]['short_ques_body'];
                                 $target = "Requirement :";
                                 $result = strstr($text, $target);
                                 $remove_html = strip_tags($result);
                                 $str = preg_replace("/[^A-Za-z]/","",$remove_html);
                                 $character_count = strlen($str);
                                    if($character_count<16){
                                       $without_requirement = str_replace("Requirement :","",$text);
                                       echo $without_requirement;
                                    }else{
                                       echo $text;
                                    }
                              ?>
                           </p>
                           <?php }?>
                        </div>

                        <div class="rs_word_limt">
                        	 <div class="top_word_limt">
                              <div> 
                                    <span id="display_count"> 0 </span> Words
                                    
                          

                                 <!-- Time -->
                                 <input type="hidden" id="exam_end" value="" name="exam_end" />
                                 <input type="hidden" id="now" value="<?php echo $module_time;?>" name="now" />
                                 <input type="hidden" id="optionalTime" value="<?php echo $question_time_in_second;?>" name="optionalTime" />
                                 <input type="hidden" id="exact_time" value="<?php echo $this->session->get('exact_time');?>" />
                            </div>
                              
                                 <?php if ($question_time_in_second != 0) { 
                                    if($idea_info[0]['time_hour']<10){
                                       $get_hour = "0".$idea_info[0]['time_hour'];
                                    }else{
                                       $get_hour = $idea_info[0]['time_hour'];
                                    }

                                    if($idea_info[0]['time_min']<10){
                                       $get_min = "0".$idea_info[0]['time_min'];
                                    }else{
                                       $get_min = $idea_info[0]['time_min'];
                                    }

                                    if($idea_info[0]['time_sec']<10){
                                       $get_sec = "0".$idea_info[0]['time_sec'];
                                    }else{
                                       $get_sec = $idea_info[0]['time_sec'];
                                    }
                                    $_REQUEST = $get_hour.':'.$get_min.':'.$get_sec;
                                    ?>
                                   <div class="" style="text-align: center; margin:auto;">
                                       <div class="ss_timer" id="demo"><h1><?=$time_show;?></h1></div>
                                   </div>
                                 <?php }?>
                              
                              
                              

                                 <?php
                                   if($idea_info[0]['word_limit']!=0){
                                 ?>
                        	 	<!-- <span class="m-auto" style="float: right;"><b>Word Limit</b></span> -->
                        	 	<div class="m-auto" style="padding-left: 20px;text-align:right;"><b>Word Limit </b><b class="b-btn"><?=$idea_info[0]['word_limit'];?></b></div>
                                 <?php }?>
                        	 </div>
                        	 <div class="btm_word_limt">

                               <form id="creative_ans_save" method="post">
 
                        	 	 <div class="content_box_word">
                        	 	  
                        	 		<div class="text-center" id="on_focus">

                                  <?php if (($last_question_order != $key) && !$tutorial_ans_info) {?>
                                 <input type="hidden" id="next_question" value="<?php echo $question_info_s[0]['question_order'] + 1; ?>" name="next_question" />
                                 <?php } else { ?>
                                 <input type="hidden" id="next_question" value="0" name="next_question" />
                                 <?php } ?>

                                     <input id="question_id" type="hidden" name="question_id" value="<?=$idea_info[0]['question_id']?>">

                                     <input id="module_id" type="hidden" name="module_id" value="<?=$question_info_s[0]['module_id'];?>">

                                     <input id="question_order_id" type="hidden" name="question_order_id" value="<?=$question_info_s[0]['question_order'];?>">

                                     <input id="module_type" type="hidden" name="module_type" value="<?=$module_type;?>">

                                     <input id="idea_id" type="hidden" name="idea_id" value="<?=$idea_info[0]['id']?>">

                                     <input id="idea_no" type="hidden" name="idea_no" value="">
                                     <input id="idea_title" type="hidden" name="idea_title" value="">
                                     <input id="total_word" type="hidden" name="total_word" value="">

                                     <textarea id="word_count" class="form-control preview_main_body mytextarea" name="preview_main_body"><?php if(!empty($idea_info[0]['time_hour']) || !empty($idea_info[0]['time_min']) || !empty($idea_info[0]['time_sec'])){?><elem id="time_image"><img  class="image-editor" style="padding-left: 20%;" data-height="250" data-width="200" height="179" src="<?php echo base_url();?>/assets/images/pv1.jpg" width="281" /></elem><?php }?></textarea>
                                     
                        	 			  
                        	 		</div> 
                                     
                        	 	 </div>
                                <div class="text-center">
                                    <?php if($idea_info[0]['add_start_button']!=0){?>
                                 <button data-toggle="modal" id="idea_start"  class="btn btn_next" type="button">Start</button> 
                                 <?php }?>
                                 <button id="answer_matching" class="btn btn_next" type="button">Submit </button> 
                                 <!-- <button id="answer_matching" class="btn btn_next" type="button"  data-toggle="modal" data-target="#alert_times_up">Submit </button>  -->
                               </div>
                               </form>
                        	 </div>
                        </div>
                     </div>
                     <div class="col-sm-1"></div>
                     <div class="col-sm-4">
                        <div class="panel-group" id="raccordion" role="tablist" aria-multiselectable="true">
                           <div class="panel panel-default">
                              <div class="panel-heading" role="tab" id="headingOne">
                                 <h4 class="panel-title">
                                 <a role="button" data-toggle="collapse" data-parent="#taccordion" href="#collapsethree" aria-expanded="true" aria-controls="collapseOne">

                                 <span>Module Name: <?php echo isset($module_info[0]['moduleName'])?$module_info[0]['moduleName']:'Not found'; ?></span></a>
                                 </h4>
                              </div>
                              <div id="collapsethree" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                                 <div class="panel-body">
                                    <div class=" ss_module_result">
                                       <div class="table-responsive">
                                          <table class="table table-bordered">
                                             <thead>
                                                <tr>
                                                   <th></th>
                                                   <th>SL</th>
                                                   <th>Mark</th>
                                                   <th>Obtained</th>
                                                   <th>Description</th>
                                                </tr>
                                             </thead>
                                             <tbody>
                              <?php $i = 1;
                              $total = 0;
                              
                              foreach ($total_question as $ind) { ?>
                              
                              <tr>
                                <?php

                                                        $style = '';
                                                        if (isset($desired[$i]['ans_is_right']))
                                                        {
                                                            $qus_tutorial = get_question_tutorial($ind['question_id']);
                                                            if ($qus_tutorial && $module_info[0]['repetition_days'] == '')
                                                            {
                                                                $style = "background-color:#dcf394;text-align: center;padding: 0px;";
                                                            }
                                                        }
                                                      ?>
                                <td style="<?php echo $style;?>">
                                    <?php if (isset($desired[$i]['ans_is_right'])) {
                    $qus_tutorial = get_question_tutorial($ind['question_id']);

                                          if ($desired[$i]['ans_is_right'] == 'correct') {?>
                                      <span class="glyphicon glyphicon-ok" style="color: green;"></span>
                    <?php if ($qus_tutorial && ($module_info[0]['repetition_days'] == '' || $module_info[0]['repetition_days'] == 'null')){?>
                                                  <span class="question_tutorial_view" question_id="<?php echo $ind['question_id']; ?>" style="font-weight: bolder;color: #ff8b00;font-size: 20px;margin-left: 3px;">T</span>
                                              <?php }?>
                                 <?php } else if($desired[$i]['ans_is_right'] == 'idea'){?>
                                    <span class="glyphicon glyphicon-pencil" style="color: red;"></span>
                                            
                                    <?php   }else {?>
                                       <span class="glyphicon glyphicon-remove" style="color: red;"></span>
                    <?php if ($qus_tutorial && ($module_info[0]['repetition_days'] == '' || $module_info[0]['repetition_days'] == 'null')){?>
                                                  <span class="question_tutorial_view" question_id="<?php echo $ind['question_id']; ?>" style="font-weight: bolder;color: #ff8b00;font-size: 20px;margin-left: 3px;">T</span>
                                              <?php }?>
                                          <?php }
                                          // echo "//bbbbb".$ind["question_type"];
                                          
                                    }?>
                                </td> 

                                
                                       <?php  if ( ($ind["question_type"] !=14) && ($question_info_s[0]['question_order'] == $ind['question_order']) ) { ?>
                                            <td style="background-color:lightblue">
                                                <?php echo $ind['question_order']; ?>
                                            </td>
                                       <?php } 

                                        elseif ( ($ind["question_type"] ==14) && $order >= $chk ) { ?>
                                            <td style="background-color:#dcf394;text-align: center;padding: 0px;">
                                              <a style="color: #000;" class="show_tutorial_modal" question_id="<?php echo $ind['question_id']; ?>" modalId="<?php echo $ind['module_id']; ?>" Order="<?php echo $ind['question_order']; ?>"><?php echo $ind['question_order']; ?><span style="font-weight: bolder;color: #ff8b00;font-size: 20px;margin-left: 3px;">T</span></a>
                                             </td>
                                       <?php } 

                                       elseif ( ($ind["question_type"] ==14) && $order < $chk ) { ?>
                                            <td style="background-color:#dcf394;text-align: center;padding: 0px;">
                                              <a style="color: #000;" class="show_tutorial_modal" question_id="<?php echo $ind['question_id']; ?>" modalId="<?php echo $ind['module_id']; ?>" Order="<?php echo $ind['question_order']; ?>"><?php echo $ind['question_order']; ?><span style="font-weight: bolder;color: #ff8b00;font-size: 20px;margin-left: 3px;">T</span></a>
                                             </td>
                                       <?php } 

                                        else{  ?>

                                          <td>
                                              <?php echo $ind['question_order']; ?>
                                          </td>
                                          
                                       <?php } ?>
                                        

                              <td>
                                  <?php
                                  echo $ind['questionMarks'];
                                  $total = $total + $ind['questionMarks'];
                                  ?>
                              </td>
                              <td>
                                <?php if ($ind["question_type"] ==14) {
                                  echo "0";
                                } ?>
                                <?php if (isset($desired[$ind['question_order']]['student_question_marks'])) {
                                  echo $desired[$ind['question_order']]['student_question_marks'];
                                } ?>
                              </td>
                                  <td class="description_video">
                                    <?php if (isset($ind['questionDescription']) && $ind['questionDescription'] != null ){ ?>
                                        <a  class="description_class" onclick="showModalDes(<?php echo $i; ?>);" style="display: inline-block;"><img src="assets/images/icon_details.png"></a>
                                    <?php } ?>
                                    <?php 
                                        $question_instruct_vid = isset($ind['question_video']) ? json_decode($ind['question_video']):'';
                                    ?>
                                    <?php if (isset($question_instruct_vid[0]) && $question_instruct_vid[0] != null ){ ?>
                                      <a onclick="showQuestionVideo(<?php echo $i; ?>)" class="question_video_class" style="display: inline-block;"><img src="http://q-study.com/assets/ckeditor/plugins/svideo/icons/svideo.png"></a>
                                    <?php } ?>
                                </td>
                            </tr>
                                  <?php $i++;
                              } ?>
                          <tr>
                            <td colspan="2">Total</td>
                            <td colspan="3"><?php echo $total?></td>
                          </tr>
                        </tbody>
                                          </table>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <br>
   <br>
</div>

<form id="answer_form">
<!-- modal idea -->
<div  class="modal fade ss_modal " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" id="show_question_idea">
  <!-- Modal -->
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
       
         <div class="mclose" data-dismiss="modal">x</div>
        
        
       <div class="btm_word_limt">
            <div class="content_box_word student_ans_modal">
                <p><strong>Here One Example</strong></p>
               <p>When meat is usually t----- and juicy, it is easily digested. However, this word is also used to describe a soft-hearted person. For instance, Sam’s mother is always warm and t----- towards him</p>
               <p>The meat was so t _ _ _ _ _  that I managed to cut through it very easily.</p>
            </div>
            <div class="created_name">
               <img src="assets/images/icon_created.png"> <a href="javascript:;" id="topicstory"> <u>Topic/Story Created By :</u> </a> &nbsp; <b class="student_name">Lubna </b> 
               <input type="hidden" id="submited_ans_view_student_id" name="selected_student" value="">
               <input type="hidden" id="submited_ans_idea_no" name="selected_student" value="">
            </div>
          </div>

        
       
   
    </div>
  </div> 
</div> 
<!-- modal idea show_question_ idea_tutor -->
<div  class="modal fade ss_modal " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" id="show_question_idea_tutor">
  <!-- Modal -->
  <div class="modal-dialog" role="document">
    <div class="modal-content">
     
       
      <div class="mclose" data-dismiss="modal">x</div>
        
       <div class="btm_word_limt">
            <div class="content_box_word tutor_ans_modal">
                <p><strong>Here One Example</strong></p>
               <p>When meat is usually t----- and juicy, it is easily digested. However, this word is also used to describe a soft-hearted person. For instance, Sam’s mother is always warm and t----- towards him</p>
               <p>The meat was so t _ _ _ _ _  that I managed to cut through it very easily.</p>
            </div>
            <div class="created_name">
               <img src="assets/images/icon_created.png"> <a type="button" href="javascript:;" data-toggle="modal" data-target="#topicstory_tutor"> <u>Topic/Story Created By :</u> </a> &nbsp; (Tutor) <b class="tutor_name"> Tutor</b>
            </div>
       </div>

    </div>
  </div> 
</div>

<!-- modal idea topicstory_tutor -->
<div  class="modal fade ss_modal " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" id="topicstory_tutor">
  <!-- Modal -->
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
       
         <div class="mclose" data-dismiss="modal">x</div>
        
        
       <div class="btm_word_limt text-center">

          <input type="hidden" name="tutor_idea_no" id="tutor_idea_no" value="">
          <input type="hidden" name="tutor_id" id="get_tutor_id" value="">
           <p>Tutor(<b class="tutor_name">name</b>)</p>
           <p><u>Topic/Stoty Title</u></p>
           <p class="blue">"New Environment"</p>
           <p > Created: &nbsp; &nbsp; <span id="tutor_submit_date">06/08/2021</span></p>
           <div class="clik_point"><i class="fa fa-thumbs-up" aria-hidden="true"></i></div>

           <div class="clik_point_detatis_tutor">
                 <div class="clik_point_detatis">
                  Total Number Of Like <div class="clik_point">33</i></div>
                 </div> 
                 <br>
                 <div class="your_achived_point">
                        Your Achived points <br>
                        <button id="like_get_point">15</button>
                 </div>
           </div>
           

       </div>

        
      
    </div>
  </div> 
</div>

<!-- modal idea profile -->
<div  class="modal fade ss_modal " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" id="show_question_idea_profile">
  <!-- Modal -->
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
       
         <div class="mclose" data-dismiss="modal">x</div> 
         <div class="row">
            <div class="col-sm-4">
               <div class="p-3 profile_left_ida">
                  <div class="text-center">
                     <img id="profile_image" src="assets/images/pp.jpg">
                  </div>
                  <table class="table" border="0">
                     <tbody id="student_info">
                        <tr>
                           <td>Created</td>
                           <td>15/08/2021</td>
                        </tr>
                         <tr>
                           <td>Name</td>
                           <td >Luchi</td>
                        </tr>
                         <tr>
                           <td>Grade/Year</td>
                           <td>3</td>
                        </tr>
                         <tr>
                           <td>School</td>
                           <td>Dhaka school</td>
                        </tr>
                         <tr>
                           <td>Country</td>
                           <td>Austrolia</td>
                        </tr> 
                     </tbody>
                  </table>
               </div>
            </div>
            <div class="col-sm-8">
               <div class="profile_right_ida"> 
                     <div class="welcom_mes">
                        Welcome! In this exciting section you have the cool opportunity to earn Extra Bonus Points. Put on the teacher’s hat and grade the student’s work below, let’s start!
                     </div>
                     <p><u> Topic/Story Title</u></p>
                     <p class="blue">"New Environment"</p>
                     <p class="p-3">
                        Submited by "<b class="student_name">Linda</b>" <button  type="button" class="btn btn-primary"  data-toggle="modal" data-target="#user_checks">Check</button> Edited by "Tutor" <button type="button" disabled="disabled" class="btn btn-primary tutor_check_button" data-toggle="modal" data-target="#tutor_checks" > Check</button>
                     </p>
               </div>
               
            </div>
         </div>

         <div class="row">
            <div class="col-sm-4">
               <div  style="display: none;" class="your_achived_point" id="your_achived_point">
                  Your Achived points <br>
                  <button id="grade_get_point">150</button>
               </div>
            </div>
            <div class="col-sm-8">
               <div class="profile_right_ida_bottom table-responsive">
               <table class="table">
	   				<thead>
	   					<tr>               
	   						<th></th>
	   						<th class="red">Poor</th>
	   						<th class="blue">Average</th>
	   						<th class="gold">Good</th>
	   						<th class="green">Very Good</th>
	   						<th class="orange">Excellent!</th>
	   					</tr>
	   				</thead>
	   				<tbody>
	   					<tr>
	   						<td>Relevance</td>
	   						<td>
	   							<input type="checkbox" value="1" class="report_box relevance" id="Rel_poor" name="Rel_poor"><span id="Rel_poor_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="2" class="report_box relevance" id="Rel_average" name="Rel_average"><span id="Rel_average_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="3" class="report_box relevance" id="Rel_good" name="Rel_good"><span id="Rel_good_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="4" class="report_box relevance" id="Rel_very_good" name="Rel_very_good"><span id="Rel_very_good_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="5" class="report_box relevance" id="Rel_excellent" name="Rel_excellent"><span id="Rel_excellent_span"></span>
	   						</td>
	   					</tr>
	   					<tr>
	   						<td>Creativity</td>
	   						<td>
	   							<input type="checkbox" value="1" class="report_box creativity" id="cre_poor" name="cre_poor"><span id="cre_poor_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="2" class="report_box creativity" id="cre_average" name="cre_average"><span id="cre_average_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="3" class="report_box creativity" id="cre_good" name="cre_good"><span id="cre_good_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="4" class="report_box creativity" id="cre_very_good" name="cre_very_good"><span id="cre_very_good_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="5" class="report_box creativity" id="cre_excellent" name="cre_excellent"><span id="cre_excellent_span"></span>
	   						</td>
	   					</tr>
	   					<tr>
	   						<td>Grammar/Spelling</td>
	   						<td>
	   							<input type="checkbox" value="1" class="report_box grammar" id="grammar_poor" name="grammar_poor"><span id="grammar_poor_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="2" class="report_box grammar" id="grammar_average" name="grammar_average"><span id="grammar_average_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="3" class="report_box grammar" id="grammar_good" name="grammar_good"><span id="grammar_good_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="4" class="report_box grammar" id="grammar_very_good" name="grammar_very_good"><span id="grammar_very_good_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="5" class="report_box grammar" id="grammar_excellent" name="grammar_excellent"><span id="grammar_excellent_span"></span>
	   						</td>
	   					</tr>
	   					<tr>
	   						<td>Vocabulary</td>
	   						<td>
	   							<input type="checkbox" value="1" class="report_box vocabulary" id="vocabulary_poor" name="vocabulary_poor"><span id="vocabulary_poor_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="2" class="report_box vocabulary" id="vocabulary_average" name="vocabulary_average"><span id="vocabulary_average_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="3" class="report_box vocabulary" id="vocabulary_good" name="vocabulary_good"><span id="vocabulary_good_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="4" class="report_box vocabulary" id="vocabulary_very_good" name="vocabulary_very_good"><span id="vocabulary_very_good_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="5" class="report_box vocabulary" id="vocabulary_excellent" name="vocabulary_excellent"><span id="vocabulary_excellent_span"></span>
	   						</td>
	   					</tr>
	   					<tr>
	   						<td>Clarity</td>
	   						<td>
	   							<input type="checkbox" value="1" class="report_box clarity" id="clarity_poor" name="clarity_poor"><span id="clarity_poor_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="2" class="report_box clarity" id="clarity_average" name="clarity_average"><span id="clarity_average_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="3" class="report_box clarity" id="clarity_good" name="clarity_good"><span id="clarity_good_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="4" class="report_box clarity" id="clarity_very_good" name="clarity_very_good"><span id="clarity_very_good_span"></span>
	   						</td>
	   						<td>
	   							<input type="checkbox" value="5" class="report_box clarity" id="clarity_excellent" name="clarity_excellent"><span id="clarity_excellent_span"></span>
	   						</td>
	   					</tr>
	   				</tbody>
	   			</table>
               </div>
               <div class="profile_right_ida">  
                     <p style="display: flex;">
                        Your grade <button style="margin-right:5px;margin-left:5px;" type="button" class="btn btn-primary" id="view_my_grade">Check</button> <input  style="margin-right:5px;" type="text" class="form-control w-50" id="my_grade" name="" value="0"> Tutor Grade <button disabled="disabled" style="margin-right:5px;margin-left:5px;" type="button" id="tutor_report_show" class="btn btn-primary tutor_check_button">Check</button>
                        <input style="margin-right:5px;" type="text" class="form-control w-50" id="tutor_grade_show" name="tutor_grade" value="0">
                        <input style="display: none;margin-right:5px;" type="text" class="form-control w-50" id="tutor_grade" name="tutor_grade" value="0">
                     </p> 

                     <input  type="hidden" class="form-control w-50" id="tutor_report" name="tutor_report" value="">

                     <div class="text-center">
                        <br>
                        <button type="button" class="btn btn_next" id="submit_button">Submit</button>
                     </div>
               </div>
            </div>
         </div>
       
     
    </div>
  </div> 
</div>

<!-- modal user_checks -->
<div  class="modal fade ss_modal " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" id="user_checks">
  <!-- Modal -->
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
         <div class="mclose" data-dismiss="modal">x</div> 
         <div class="btm_word_limt">
            <div class="content_box_word student_ans_modal">
                <p><strong>Here One Example</strong></p>
               <p>When meat is usually t----- and juicy, it is easily digested. However, this word is also used to describe a soft-hearted person. For instance, Sam’s mother is always warm and t----- towards him</p>
               <p>The meat was so t _ _ _ _ _  that I managed to cut through it very easily.</p>
            </div>
          
         </div> 
      
    </div>
  </div> 
</div>
<!-- modal user_checks -->
       

<!-- modal tutor_checks -->
<div class="modal fade ss_modal " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" id="tutor_checks">
  <!-- Modal -->
  <div class="modal-dialog" role="document">
    <div class="modal-content">
     
         <div class="mclose" data-dismiss="modal">x</div> 
         <div class="btm_word_limt">
            <div class="content_box_word">
               <div class="row">
                 <img id="teacher_correction_img" src="">
               </div>
            </div>
         </div> 
    
    </div>
  </div> 
</div>
<!-- alert_times_up -->
<div class="modal fade ss_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" id="alert_times_up">
  <!-- Modal -->
  <div class="modal-dialog" role="document">
    <div class="modal-content">
     
        <div class="modal-header">
          <h4>Times Up</h4>
        </div>
    
        <div class="modal-body">
        
          <p>Oops! You’ve lost your paragraph for exceeding your time! You need to re-write from the start.</p>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn_blue" data-dismiss="modal">Ok</button> 
        </div>
     
    </div>
  </div>

</div>
<!-- /////////////// -->
<div class="modal fade ss_modal" id="idea_save_faild" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title" id="myModalLabel">&nbsp;</h4>
                </div>
                <div class="modal-body row">
                    <img src="assets/images/icon_sucess.png" class="pull-left"> <span class="ss_extar_top20">You already submit this Idea</span> 
                </div>
                <div class="modal-footer">
                    <button id='get_next_question' type="button" class="btn btn_blue" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade ss_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" id="idea_save_success">
  <!-- Modal -->
  <div style="max-width: 20%;" class="modal-dialog" role="document">
    <div class="modal-content">
     
        <div class="modal-header">
          <h4>Success</h4>
        </div>
    
        <div class="modal-body">
         <p><i class="fa fa-pencil" style="font-size:28px;color:#f5d743;"></i></p>
         <p>Examiner will scrutinize your answer and get back to you.</p>

        </div>
        <div class="modal-footer">
        <button id="get_next_question2" type="button" class="btn btn_blue" data-dismiss="modal">Ok</button>
        </div>
     
    </div>
  </div>
</div>

               

<div  class="modal fade ss_modal " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" id="show_question_body">
  <!-- Modal -->
  <div class="modal-dialog" role="document">
    <div class="modal-content">
         
         <div class="btm_word_limt p-3">
             <div>
                <button type="button" id="close_idea" class=" pull-right" data-dismiss="modal">x</button>
             </div>
            <br> <hr>
            <?=$idea_info[0]['large_ques_body']?>
            <div class="text-center p-3">
            <button type="button" id="close_idea" class="btn btn-info pull-right" data-dismiss="modal">Close</button>
            </div>
         </div> 
    </div>
  </div>
</div>

<div class="modal fade ss_modal " id="idea_title_show" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">

  <div class="modal-content" style="width: 50%;margin-left:25%;display:none;" id="show_empty_idea">
    <div  class="modal-header">
          <h4 class="modal-title" id="myModalLabel"> </h4>
        </div>
      
        <div class="modal-body">
         <i class="fa fa-pencil" style="font-size:28px;color:#f5d743;"></i>
         <p>Please Write Idea title first!!</p>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn_blue" id="close_empty_idea">Ok</button> 
        </div>
     
    </div>

    <div  class="modal-content">
     
      <div class="modal-header">
        <h4>Topic/Story Title</h4>
      </div>

        <div class="modal-body">
          
          <div class="d-flex idea_modal_textarea">
          <textarea id="idea_title_text_get" class="form-control idea_title_text " name="idea_title_text23"></textarea>
          </div> 
          
         

        </div>
        <div class="modal-footer"> 
          <button type="button" class="btn btn_blue ideabtnclose">close</button>
        </div>
     
    </div>
  </div>
</div>
<div class="modal fade ss_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" id="alert_times_up">
  <!-- Modal -->
  <div class="modal-dialog" role="document">
    <div class="modal-content">
     
        <div class="modal-header">
          <h4>Times Up</h4>
        </div>
    
        <div class="modal-body">
        
          <p>Oops! You’ve lost your paragraph for exceeding your time! You need to re-write from the start.</p>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn_blue" data-dismiss="modal">Ok</button> 
        </div>
     
    </div>
  </div>

</div>

<div class="modal fade ss_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" id="total_limit_exceed">
  <!-- Modal -->
  <div style="max-width: 20%;" class="modal-dialog" role="document">
    <div class="modal-content">
     
    <div  class="modal-header">
          <h4 class="modal-title" id="myModalLabel"></h4>
        </div>
    
        <div class="modal-body">
         <i class="fa fa-pencil" style="font-size:28px;color:#f5d743;"></i>
         <p>you've already exceeded <b><?=$idea_info[0]['word_limit'];?></b> word.Please make it <b><?=$idea_info[0]['word_limit'];?></b> words or bellow and then resubmit.</p>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn_blue" data-dismiss="modal">Ok</button> 
        </div>
     
    </div>
  </div>

</div>

<div class="modal fade ss_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" id="low_limit">
  <!-- Modal -->
  <div style="max-width: 20%;" class="modal-dialog" role="document">
    <div class="modal-content">
    <div  class="modal-header">
          <h4 class="modal-title" id="myModalLabel"> </h4>
        </div>
      
        <div class="modal-body">
         <i class="fa fa-pencil" style="font-size:28px;color:#f5d743;"></i>
         <p>Oops! you need to have a minimum input of <b id="percent_limit"></b> words and then resubmit.</p>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn_blue" data-dismiss="modal">Ok</button> 
        </div>
     
    </div>
  </div>

</div>

<div class="modal fade ss_modal" id="times_up_message" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="max-width: 20%;" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="text-align:center;" class="modal-title" id="myModalLabel">Times Up</h4>
        </div>
        <div class="modal-body row">
          <i class="fa fa-close" style="font-size:20px;color:red"></i> 
          <!--<span class="ss_extar_top20">Your answer is wrong</span>-->
          <br><p>Oops! you've lost your paragraph for exceeding your time! You now need to re-write from the start</p>
        </div>
        <div class="modal-footer">
          <button type="button" id="question_reload" class="btn btn_blue" data-dismiss="modal">close</button>         
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade ss_modal" id="ss_info_sucesss" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title" id="myModalLabel">&nbsp;</h4>
                </div>
                <div class="modal-body row">
                    <img src="assets/images/icon_sucess.png" class="pull-left"> <span class="ss_extar_top20">Your answer is correct</span> 
                </div>
                <div class="modal-footer">
                    <button id='get_next_question' type="button" class="btn btn_blue" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>

  <div class="modal fade ss_modal " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" id="frist_time_user">
				<!-- Modal -->
				<div style="max-width: 100%;margin-top:8%;" class="modal-dialog" role="document">
					<div  class="modal-content">


						<div style="padding:10px;" class="btm_word_limt p-3">

						<form id="add_profile_form" action="" method="post" enctype="multipart/form-data">
							<div>
								<button type="button" class="btn btn-profile">Edit</button>
								<button type="button" id="close_idea_modal" class="btn btn-info pull-right" data-dismiss="modal">Close</button>
							</div>
							<hr>
							<div class="frist_time_user_mid_con">
								<div class="frist_time_user_mid_con_mes">
									<strong> Wanna be a superstar?? </strong> Each time you submit a writing task, your
									wonderful work is automatically published as a writing suggestion
									viewable around the world <a href="#">view more</a>
								</div>
								<div class="row p-3">
									<div class="col-sm-6">
										<div class="form-group">
											<label>Name</label>
											<input type="text" id="student_name" class="form-control" value="<?=$profile[0]['student_name']?>" name="student_name">
										</div>
										<div class="form-group">
											<label>School Name <a href="#">Optional</a></label>
											<input type="text" id="school_name" class="form-control" value="<?=$profile[0]['school_name']?>" name="school_name">
										</div>
										<div class="form-group">
											<label>Country</label>
											<input type="text" id="country" class="form-control" value="<?=$profile[0]['country']?>" name="country">
										</div>
									</div>
									<div class="col-sm-6">
										<div class="text-center">
										<div class="form-group">
													
													<input style="display: none;" id="file-input" type="file" class="form-control" name="profile_image" onchange="imgPreview()">
											
													<label for="file-input"><i class="fa fa-cloud-upload" aria-hidden="true"></i></label>
											<p>Chose Photo to Upload</p>
											<p><a href="">(Optional)</a></p>
										</div>
										<div class="image_box"><img style="height:100px;" id="imgFrame" src="assets/uploads/profile/thumbnail/<?=$profile[0]['profile_image']?>" width="100px" height="200px" /></div>
									</div>
								</div>
							</div>
							<hr>
							<div class="text-center p-3">
								<button type="button" id="add_profile" class="btn btn_next">Submit & Proceed</button>
							</div>
						</form>
						</div>

					</div>
				</div>
			</div>






<style type="text/css">
.frist_time_user_mid_con_mes strong{
   color: #ff7f27;
}

.frist_time_user_mid_con_mes a{
   color: #00c1f7;
   display: inline-block;
}
.frist_time_user_mid_con a{
   display: inline-block;
}
.frist_time_user_mid_con label{
   margin-bottom: 6px;
}
.frist_time_user_mid_con .image_box{
   border: 1px solid #00c1f7;
   height: 100px;
   width: 100px;
   margin: 10px auto;
   background: #d9d9d9;
}

.clik_point{
   border: 1px solid #4bb04f;
   background: #4bb04f;
   color: #fff;
   height: 60px;
   width: 60px;
   line-height: 55px;
   text-align: center;
   margin: 20px auto;
   border-radius: 50%;
   font-size: 30px;
   cursor: pointer;
}
.clik_point_detatis{
   display: inline-flex;
   justify-content: center;
   align-items: center;
   padding: 20px 0px;
}
.clik_point_detatis .clik_point{
   display: block !important;
   margin-left: 10px;
   border: 1px solid #ff0000;
   background: #ff0000;
   color: #fff;
   height: 60px;
   width: 60px;
   line-height: 55px;
   text-align: center; 
   border-radius: 50%;
   font-size: 30px;
}
.clik_point_detatis_tutor .your_achived_point{
   max-width: 200px;
   margin: auto;
}
#topicstory_tutor .btm_word_limt {
   min-height: 300px;
   padding: 30px;
}
.your_achived_point{
   border: 1px solid #015f4e;
    padding: 15px;
    text-align: center;
    margin: 10px;
    background: #f4f5f9;
}
.your_achived_point button{
   padding: 7px 15px;
   color: #fff;
   background: #015f4e;
   border: 0;
   border-radius: 5px;
   margin-top: 10px;
}
.w-50{
       width: 70px;
    display: inline-block;
}
.profile_right_ida{
   padding: 10px;
padding-top: 20px;
   text-align: center;

}
.profile_right_ida .welcom_mes {
   font-size: 13px;
   line-height: 16px;
   margin: 20px 0px;
   padding: 10px;
   background: #b5e61d;
   border: 1px solid #0079bc;
}
.profile_right_ida u{
   color: #7f7f7f;
}
.profile_right_ida .btn-primary{
   margin-bottom: 5px;
   background: #fff;
   color: #333;
   padding: 6px 15px;
   border-radius: 0;
   line-height: 16px;
   border: 1px solid #c3c3c3;
}
.profile_right_ida .btn-primary:hover{
   background: #a349a4;
   color: #fff;
   padding: 6px 15px;
   border-radius: 0;
   line-height: 16px;
}

#show_question_idea_profile table{
   font-size: 13px;
}
.profile_right_ida_bottom {
   padding:0 10px;
}
.profile_right_ida_bottom .table>thead>tr>th {
    border-bottom: 2px solid #e6eed5;
}
  .red {
    color: #ff0000;
}
 .blue {
    color: #00b0f0;
}
  .gold {
    color: #e36c09;
}
  .green {
    color: #00b050;
}
  .orange {
    color: #953734;
}
.profile_right_ida_bottom .table tbody tr > td {
    text-align: center;
    padding: 4px 10px;
    color: #ed1c24;
}
.profile_right_ida_bottom .table tbody tr {
    background: #e6eed5;
    border-bottom: 20px solid #fff;
}
.profile_right_ida_bottom .table input{
   margin: 0;
}

.profile_right_ida_bottom .table tbody tr > td:first-child {
    text-align: left;
    color: #76923c;
    font-weight: bold;
}
.profile_right_ida_bottom .table input[type=checkbox]:focus
      {
          outline: none;
      }

  .profile_right_ida_bottom .table input[type=checkbox]
   {
       background-color: #fff;
       border-radius: 2px;
       appearance: none;
       -webkit-appearance: none;
       -moz-appearance: none;
       width: 14px;
       height: 14px;
       cursor: pointer;
       position: relative; 
       border: 1px solid #959595;
   }

   .profile_right_ida_bottom .table input[type=checkbox]:checked
   {
      border: 1px solid #0699ef;
       background-color: #0699ef;
       background: #0699ef url("data:image/gif;base64,R0lGODlhCwAKAIABAP////3cnSH5BAEKAAEALAAAAAALAAoAAAIUjH+AC73WHIsw0UCjglraO20PNhYAOw==") 3px 3px no-repeat;
       background-size: 8px;
   } 
@media (min-width: 1000px){
#show_question_idea_profile .modal-dialog{
   width: 800px;
}
}
  #show_question_idea_profile{
      overflow-y: scroll;
   }
.profile_left_ida table{
   margin-top: 10px;
}
.profile_left_ida table tr td{
   border: none;
   padding: 0;
   color: #7f7f7f;
   font-size: 13px;
}
.p-3{
   padding: 15px;
}
.ss_modal .modal-content {
    border: 1px solid #a6c9e2;
    padding: 0;
    margin: 0;
}
.top_textprev{
   padding-bottom: 20px;
}
.top_textprev h4{
   color: #7f7f7f;
   font-size: 16px;
   font-weight: bold;
}
.top_textprev .btn{
   background: #9c4d9e;
   border-radius: 0;
   border: none;
   color: #fff;
   padding: 8px 20px;
   margin-top: 10px;
   margin-bottom: 20px;
}
.top_textprev h6{
   color: #000;
   font-size: 14px;
   font-weight: bold;
}
.workout_menu{
   height: initial;
}
.workout_menu ul{
   margin-bottom: 20px;
   display: flex;
   align-items: end;
   flex-wrap: wrap;
}
.workout_menu ul > div{ 
   margin-bottom: 10px;
}
    .top_word_limt{
    	background: #d9edf7;
    	padding: 8px 10px;
    	display: flex;
    	flex-wrap: wrap;
    	align-items: center;
    }
    .m-auto{
    	margin-left: auto;
    }
    .b-btn{
    	background: #0079bc;
    	padding: 5px 10px;
    	border-radius: 5px;
    	color: #fff;
    }
    #login_form .modal-dialog, .ss_modal .modal-dialog {
       max-width: 100%;
   }
    .btm_word_limt .content_box_word{
    	border-radius: 5px;
    	border: 1px solid #82bae6;
    	margin: 10px 0;
    	padding: 10px;
    	width: 100%;
    	box-shadow: 0px 0px 10px #d9edf7;
      margin-top: 0 !important;
    }
    .btm_word_limt .content_box_word u{
    	color: #888;
    }
    .btm_word_limt .content_box_word span{
    	color: #888;
    }
    .btm_word_limt .content_box_word p{
    	margin-top: 10px;
    }
    .ss_modal .modal-dialog{
      position: absolute;
      margin-top: 0% !important; 
    top: 50% !important;   
    left: 50% !important;    
    transform: translate(-50%, -50%) !important;  
    }

    .ss_modal .modal-content { 
       padding: 5px !important; 
   }

 .ss_modal .modal-header {
    background: url(assets/images/login_bg.png) repeat-x;
    color: #fff;
    padding: 0;
    border-radius: 5px;
}
 
#show_question_idea_profile .modal-dialog {
    position: relative;
    margin-top: 2% !important;
    top: 0 !important;
    left: auto !important;
    transform: translate( 0%, 0%) !important;
}
.created_name {
    background: #66afe9;
    color: #fff;
    font-size: 16px;
    padding: 10px 20px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
}
.mclose{
   position: absolute;
   right: 10px;
   top: 10px;
   font-size: 20px;
   z-index: 10;
   cursor: pointer;
}
.created_name img {
    max-width: 30px;
    margin-right: 10px;
}
.created_name a {
    color: #fff;
}
</style>
<script type="text/javascript">
  

   function imgPreview() {
      imgFrame.src = URL.createObjectURL(event.target.files[0]);
   }
   $('#answer_matching').hide();


   $("#idea_start").on("click", function () {
      
      <?php if ($question_time_in_second != 0) { ?>
    
         takeDecesionForQuestion();
      <?php }?>

       <?php if($profile[0]['user_id']==''){?>    
         $('#frist_time_user').modal('show');  
       <?php }?>
      

       var idea_no = $("#idea_no").val();
       var idea_title = $("#idea_title").val();
      //  if(idea_no!=''){
       $('#idea_start').hide();
       $('#answer_matching').show();

         text= '<p style="text-align:center;text-decoration:underline;"><b>Idea/Topic/Story title</b></p><p style="text-align:center;color:#fb8836f0;"><b>"'+idea_title+'"</b>&#9999;&#65039;</p><p>Start write here...</p>';
         CKEDITOR.instances.word_count.on('paste', function(evt) {
            evt.cancel();
         });
         CKEDITOR.instances.word_count.setData(text);
      
      <?php if($idea_info[0]['student_title']==1){?>
       $modal2 = $('#idea_title_show'); 
       $modal2.modal('show');
       <?php }?>
       
   });

   $(".ideabtnclose").on("click", function () {

      var idea_title = $(".idea_title_text").val();
     if(idea_title == ''){
      $("#show_empty_idea").css("display","block");
     }else{
       $modal2 = $('#idea_title_show'); 
       $modal2.modal('hide');
      text= '<p style="text-align:center;text-decoration:underline;"><b>Idea/Topic/Story title</b></p><p style="text-align:center;color:#fb8836f0;"><b style="font-size:18px;">"'+idea_title+'"</b>&nbsp;&#9999;&#65039;</p><br><p>Start write here...</p>';

      CKEDITOR.instances.word_count.setData(text);
     }
   });

   $("#close_empty_idea").on("click", function () {
      $("#show_empty_idea").css("display","none");
   });

   CKEDITOR.on('instanceReady', function(evt) {
      // console.log(evt.editor.getData());
      evt.editor.on('focus',function(event){
         var getData = evt.editor.getData();
         var setData = getData.replace("<p>Start write here...</p>", " ");
         evt.editor.setData(setData);
      });
      });
   
3
$( "table" ).delegate( "td", "click", function() {
  $( this ).toggleClass( "chosen" );
});

   $("#topicstory").on("click", function () {        
       $modal = $('#show_question_idea'); 
       $modal.modal('hide');
       $modal2 = $('#show_question_idea_profile'); 
       $modal2.modal('show');

       var checker_id = "<?=$user_id;?>";
       var submited_student_id=$("#submited_ans_view_student_id").val();
       var question_id= $("#question_id").val();
       var idea_id =$("#idea_id").val();
       var idea_no =$("#submited_ans_idea_no").val();

       $.ajax({
            url: "Student/check_student_grade",
				method: "POST",
				data: {question_id:question_id,checker_id:checker_id,submited_student_id:submited_student_id},
				dataType: 'json',
				success: function(data) {
            if(data!=2){
            
            var relchk1='';
            var relval1='';
            var relchk2='';
            var relval2='';
            var relchk3='';
            var relval3='';
            var relchk4='';
            var relval4='';
            var relchk5='';
            var relval5='';

            var creativechk1='';
            var creativeval1='';
            var creativechk2='';
            var creativeval2='';
            var creativechk3='';
            var creativeval3='';
            var creativechk4='';
            var creativeval4='';
            var creativechk5='';
            var creativeval5='';

            var grammerchk1='';
            var grammerval1='';
            var grammerchk2='';
            var grammerval2='';
            var grammerchk3='';
            var grammerval3='';
            var grammerchk4='';
            var grammerval4='';
            var grammerchk5='';
            var grammerval5='';

            var vocabularychk1='';
            var vocabularyval1='';
            var vocabularychk2='';
            var vocabularyval2='';
            var vocabularychk3='';
            var vocabularyval3='';
            var vocabularychk4='';
            var vocabularyval4='';
            var vocabularychk5='';
            var vocabularyval5='';

            var claritychk1='';
            var clarityval1='';
            var claritychk2='';
            var clarityval2='';
            var claritychk3='';
            var clarityval3='';
            var claritychk4='';
            var clarityval4='';
            var claritychk5='';
            var clarityval5='';

               var relevance = '';
               var creativity = '';
               var grammar = '';
               var vocabulary = '';
               var clarity = '';
               
               var reports =JSON.parse(data.checked_checkbox);
               
               var i='';
               for(i=0;i<reports.length;i++){
                  var checked= 'checked';
                  var report= reports[i].split(',');
                  //console.log(report);

                  if(report[1]=='relevance'){

                     if(report[2]==1){
                     var relchk1='checked';
                     var relval1= 1;
                     }
                     if(report[2]==2){
                     var relchk2='checked';
                     var relval2= 2;
                     }
                     if(report[2]==3){
                     var relchk3='checked';
                     var relval3= 3;
                     }
                     if(report[2]==4){
                     var relchk4='checked';
                     var relval4= 4;
                     }
                     if(report[2]==5){
                     var relchk5='checked';
                     var relval5= 5;
                     }
               
                  }

                  if(report[1]=='creativity'){

                  if(report[2]==1){
                     var creativechk1='checked';
                     var creativeval1= 1;
                     }
                     if(report[2]==2){
                     var creativechk2='checked';
                     var creativeval2= 2;
                     }
                     if(report[2]==3){
                     var creativechk3='checked';
                     var creativeval3= 3;
                     }
                     if(report[2]==4){
                     var creativechk4='checked';
                     var creativeval4= 4;
                     }
                     if(report[2]==5){
                     var creativechk5='checked';
                     var creativeval5= 5;
                     }
                  }

                  if(report[1]=='grammar'){
                  
                     if(report[2]==1){
                     var grammerchk1='checked';
                     var grammerval1= 1;
                     
                     }
                     if(report[2]==2){
                     var grammerchk2='checked';
                     var grammerval2= 2;
                     }
                     if(report[2]==3){
                     var grammerchk3='checked';
                     var grammerval3= 3;
                     }
                     if(report[2]==4){
                     var grammerchk4='checked';
                     var grammerval4= 4;
                     }
                     if(report[2]==5){
                     var grammerchk5='checked';
                     var grammerval5= 5;
                     }
                  }

                  if(report[1]=='vocabulary'){

                     if(report[2]==1){
                     var vocabularychk1='checked';
                     var vocabularyval1= 1;
                     }
                     if(report[2]==2){
                     var vocabularychk2='checked';
                     var vocabularyval2= 2;
                     }
                     if(report[2]==3){
                     var vocabularychk3='checked';
                     var vocabularyval3= 3;
                     }
                     if(report[2]==4){
                     var vocabularychk4='checked';
                     var vocabularyval4= 4;
                     }
                     if(report[2]==5){
                     var vocabularychk5='checked';
                     var vocabularyval5= 5;
                     }
                  }

                  if(report[1]=='clarity'){

                     if(report[2]==1){
                     var claritychk1='checked';
                     var clarityval1= 1;
                     }
                     if(report[2]==2){
                     var claritychk2='checked';
                     var clarityval2= 2;
                     }
                     if(report[2]==3){
                     var claritychk3='checked';
                     var clarityval3= 3;
                     }
                     if(report[2]==4){
                     var claritychk4='checked';
                     var clarityval4= 4;
                     }
                     if(report[2]==5){
                     var claritychk5='checked';
                     var clarityval5= 5;
                     }

                  }
               }

            var final_report = '<table class="table"><thead><tr><th></th><th class="red">Poor</th><th class="blue">Average</th><th class="gold">Good</th><th class="green">Very Good</th><th class="orange">Excellent!</th></tr></thead><tbody><tr><td>Relevance</td><td><input type="checkbox" value="1" class="report_box relevance" id="Rel_poor" name="Rel_poor"'+relchk1+'><span id="Rel_poor_span">'+relval1+'</span></td><td><input type="checkbox" value="2" class="report_box relevance" id="Rel_average" name="Rel_average"'+relchk2+'><span id="Rel_average_span">'+relval2+'</span></td><td><input type="checkbox" value="3" class="report_box relevance" id="Rel_good" name="Rel_good"'+relchk3+'><span id="Rel_good_span">'+relval3+'</span></td><td><input type="checkbox" value="4" class="report_box relevance" id="Rel_very_good" name="Rel_very_good"'+relchk4+'><span id="Rel_very_good_span">'+relval4+'</span></td><td><input type="checkbox" value="5" class="report_box relevance" id="Rel_excellent" name="Rel_excellent"'+relchk5+'><span id="Rel_excellent_span">'+relval5+'</span></td></tr><tr><td>Creativity</td><td><input type="checkbox" value="1" class="report_box creativity" id="cre_poor" name="cre_poor"'+creativechk1+'><span id="cre_poor_span">'+creativeval1+'</span></td><td><input type="checkbox" value="2" class="report_box creativity" id="cre_average" name="cre_average"'+creativechk2+'><span id="cre_average_span">'+creativeval2+'</span></td><td><input type="checkbox" value="3" class="report_box creativity" id="cre_good" name="cre_good"'+creativechk3+'><span id="cre_good_span">'+creativeval3+'</span></td><td><input type="checkbox" value="4" class="report_box creativity" id="cre_very_good" name="cre_very_good"'+creativechk4+'><span id="cre_very_good_span">'+creativeval4+'</span></td><td><input type="checkbox" value="5" class="report_box creativity" id="cre_excellent" name="cre_excellent"'+creativechk5+'><span id="cre_excellent_span">'+creativeval5+'</span></td></tr><tr><td>Grammar/Spelling</td><td><input type="checkbox" value="1" class="report_box grammar" id="grammar_poor" name="grammar_poor"'+grammerchk1+'><span id="grammar_poor_span">'+grammerval1+'</span></td><td><input type="checkbox" value="2" class="report_box grammar" id="grammar_average" name="grammar_average"'+grammerchk2+'><span id="grammar_average_span">'+grammerval2+'</span></td><td><input type="checkbox" value="3" class="report_box grammar" id="grammar_good" name="grammar_good"'+grammerchk3+'><span id="grammar_good_span">'+grammerval3+'</span></td><td><input type="checkbox" value="4" class="report_box grammar" id="grammar_very_good" name="grammar_very_good" '+grammerchk4+'><span id="grammar_very_good_span">'+grammerval4+'</span></td><td><input type="checkbox" value="5" class="report_box grammar" id="grammar_excellent" name="grammar_excellent" '+grammerchk5+'><span id="grammar_excellent_span">'+grammerval5+'</span></td></tr><tr><td>Vocabulary</td><td><input type="checkbox" value="1" class="report_box vocabulary" id="vocabulary_poor" name="vocabulary_poor"'+vocabularychk1+'><span id="vocabulary_poor_span">'+vocabularyval1+'</span></td><td><input type="checkbox" value="2" class="report_box vocabulary" id="vocabulary_average" name="vocabulary_average" '+vocabularychk2+'><span id="vocabulary_average_span">'+vocabularyval2+'</span></td><td><input type="checkbox" value="3" class="report_box vocabulary" id="vocabulary_good" name="vocabulary_good" '+vocabularychk3+'><span id="vocabulary_good_span">'+vocabularyval3+'</span></td><td><input type="checkbox" value="4" class="report_box vocabulary" id="vocabulary_very_good" name="vocabulary_very_good" '+vocabularychk4+'><span id="vocabulary_very_good_span">'+vocabularyval4+'</span></td><td><input type="checkbox" value="5" class="report_box vocabulary" id="vocabulary_excellent" name="vocabulary_excellent"'+vocabularychk5+'><span id="vocabulary_excellent_span">'+vocabularyval5+'</span></td></tr><tr><td>Clarity</td><td><input type="checkbox" value="1" class="report_box clarity" id="clarity_poor" name="clarity_poor" '+claritychk1+'><span id="clarity_poor_span">'+clarityval1+'</span></td><td><input type="checkbox" value="2" class="report_box clarity" id="clarity_average" name="clarity_average"'+claritychk2+'><span id="clarity_average_span">'+clarityval2+'</span></td><td><input type="checkbox" value="3" class="report_box clarity" id="clarity_good" name="clarity_good"'+claritychk3+'><span id="clarity_good_span">'+clarityval3+'</span></td><td><input type="checkbox" value="4" class="report_box clarity" id="clarity_very_good" name="clarity_very_good" '+claritychk4+'><span id="clarity_very_good_span">'+clarityval4+'</span></td><td><input type="checkbox" value="5" class="report_box clarity" id="clarity_excellent" name="clarity_excellent" '+claritychk5+'><span id="clarity_excellent_span">'+clarityval5+'</span></td></tr></tbody></table>';

            $(".profile_right_ida_bottom").html(final_report);
            $("#my_grade").val(data.total_point);
            $(".your_achived_point").css("display","block");
            $('#grade_get_point').text(data.total_point);

            }else{
               alert('You have not submit Grade Yet.');
            }
               
            }
            });

   });
   $("#view_my_grade").on("click", function () {
      var checker_id = "<?=$user_id;?>";
       var submited_student_id=$("#submited_ans_view_student_id").val();
       var question_id= $("#question_id").val();
       var idea_id =$("#idea_id").val();
       var idea_no =$("#submited_ans_idea_no").val();

       $.ajax({
            url: "Student/check_student_grade",
				method: "POST",
				data: {question_id:question_id,checker_id:checker_id,submited_student_id:submited_student_id},
				dataType: 'json',
				success: function(data) {
            if(data!=2){
            
            var relchk1='';
            var relval1='';
            var relchk2='';
            var relval2='';
            var relchk3='';
            var relval3='';
            var relchk4='';
            var relval4='';
            var relchk5='';
            var relval5='';

            var creativechk1='';
            var creativeval1='';
            var creativechk2='';
            var creativeval2='';
            var creativechk3='';
            var creativeval3='';
            var creativechk4='';
            var creativeval4='';
            var creativechk5='';
            var creativeval5='';

            var grammerchk1='';
            var grammerval1='';
            var grammerchk2='';
            var grammerval2='';
            var grammerchk3='';
            var grammerval3='';
            var grammerchk4='';
            var grammerval4='';
            var grammerchk5='';
            var grammerval5='';

            var vocabularychk1='';
            var vocabularyval1='';
            var vocabularychk2='';
            var vocabularyval2='';
            var vocabularychk3='';
            var vocabularyval3='';
            var vocabularychk4='';
            var vocabularyval4='';
            var vocabularychk5='';
            var vocabularyval5='';

            var claritychk1='';
            var clarityval1='';
            var claritychk2='';
            var clarityval2='';
            var claritychk3='';
            var clarityval3='';
            var claritychk4='';
            var clarityval4='';
            var claritychk5='';
            var clarityval5='';

               var relevance = '';
               var creativity = '';
               var grammar = '';
               var vocabulary = '';
               var clarity = '';
               
               var reports =JSON.parse(data.checked_checkbox);
               
               var i='';
               for(i=0;i<reports.length;i++){
                  var checked= 'checked';
                  var report= reports[i].split(',');
                  //console.log(report);

                  if(report[1]=='relevance'){

                     if(report[2]==1){
                     var relchk1='checked';
                     var relval1= 1;
                     }
                     if(report[2]==2){
                     var relchk2='checked';
                     var relval2= 2;
                     }
                     if(report[2]==3){
                     var relchk3='checked';
                     var relval3= 3;
                     }
                     if(report[2]==4){
                     var relchk4='checked';
                     var relval4= 4;
                     }
                     if(report[2]==5){
                     var relchk5='checked';
                     var relval5= 5;
                     }
               
                  }

                  if(report[1]=='creativity'){

                  if(report[2]==1){
                     var creativechk1='checked';
                     var creativeval1= 1;
                     }
                     if(report[2]==2){
                     var creativechk2='checked';
                     var creativeval2= 2;
                     }
                     if(report[2]==3){
                     var creativechk3='checked';
                     var creativeval3= 3;
                     }
                     if(report[2]==4){
                     var creativechk4='checked';
                     var creativeval4= 4;
                     }
                     if(report[2]==5){
                     var creativechk5='checked';
                     var creativeval5= 5;
                     }
                  }

                  if(report[1]=='grammar'){
                  
                     if(report[2]==1){
                     var grammerchk1='checked';
                     var grammerval1= 1;
                     
                     }
                     if(report[2]==2){
                     var grammerchk2='checked';
                     var grammerval2= 2;
                     }
                     if(report[2]==3){
                     var grammerchk3='checked';
                     var grammerval3= 3;
                     }
                     if(report[2]==4){
                     var grammerchk4='checked';
                     var grammerval4= 4;
                     }
                     if(report[2]==5){
                     var grammerchk5='checked';
                     var grammerval5= 5;
                     }
                  }

                  if(report[1]=='vocabulary'){

                     if(report[2]==1){
                     var vocabularychk1='checked';
                     var vocabularyval1= 1;
                     }
                     if(report[2]==2){
                     var vocabularychk2='checked';
                     var vocabularyval2= 2;
                     }
                     if(report[2]==3){
                     var vocabularychk3='checked';
                     var vocabularyval3= 3;
                     }
                     if(report[2]==4){
                     var vocabularychk4='checked';
                     var vocabularyval4= 4;
                     }
                     if(report[2]==5){
                     var vocabularychk5='checked';
                     var vocabularyval5= 5;
                     }
                  }

                  if(report[1]=='clarity'){

                     if(report[2]==1){
                     var claritychk1='checked';
                     var clarityval1= 1;
                     }
                     if(report[2]==2){
                     var claritychk2='checked';
                     var clarityval2= 2;
                     }
                     if(report[2]==3){
                     var claritychk3='checked';
                     var clarityval3= 3;
                     }
                     if(report[2]==4){
                     var claritychk4='checked';
                     var clarityval4= 4;
                     }
                     if(report[2]==5){
                     var claritychk5='checked';
                     var clarityval5= 5;
                     }

                  }
               }

            var final_report = '<table class="table"><thead><tr><th></th><th class="red">Poor</th><th class="blue">Average</th><th class="gold">Good</th><th class="green">Very Good</th><th class="orange">Excellent!</th></tr></thead><tbody><tr><td>Relevance</td><td><input type="checkbox" value="1" class="report_box relevance" id="Rel_poor" name="Rel_poor"'+relchk1+'><span id="Rel_poor_span">'+relval1+'</span></td><td><input type="checkbox" value="2" class="report_box relevance" id="Rel_average" name="Rel_average"'+relchk2+'><span id="Rel_average_span">'+relval2+'</span></td><td><input type="checkbox" value="3" class="report_box relevance" id="Rel_good" name="Rel_good"'+relchk3+'><span id="Rel_good_span">'+relval3+'</span></td><td><input type="checkbox" value="4" class="report_box relevance" id="Rel_very_good" name="Rel_very_good"'+relchk4+'><span id="Rel_very_good_span">'+relval4+'</span></td><td><input type="checkbox" value="5" class="report_box relevance" id="Rel_excellent" name="Rel_excellent"'+relchk5+'><span id="Rel_excellent_span">'+relval5+'</span></td></tr><tr><td>Creativity</td><td><input type="checkbox" value="1" class="report_box creativity" id="cre_poor" name="cre_poor"'+creativechk1+'><span id="cre_poor_span">'+creativeval1+'</span></td><td><input type="checkbox" value="2" class="report_box creativity" id="cre_average" name="cre_average"'+creativechk2+'><span id="cre_average_span">'+creativeval2+'</span></td><td><input type="checkbox" value="3" class="report_box creativity" id="cre_good" name="cre_good"'+creativechk3+'><span id="cre_good_span">'+creativeval3+'</span></td><td><input type="checkbox" value="4" class="report_box creativity" id="cre_very_good" name="cre_very_good"'+creativechk4+'><span id="cre_very_good_span">'+creativeval4+'</span></td><td><input type="checkbox" value="5" class="report_box creativity" id="cre_excellent" name="cre_excellent"'+creativechk5+'><span id="cre_excellent_span">'+creativeval5+'</span></td></tr><tr><td>Grammar/Spelling</td><td><input type="checkbox" value="1" class="report_box grammar" id="grammar_poor" name="grammar_poor"'+grammerchk1+'><span id="grammar_poor_span">'+grammerval1+'</span></td><td><input type="checkbox" value="2" class="report_box grammar" id="grammar_average" name="grammar_average"'+grammerchk2+'><span id="grammar_average_span">'+grammerval2+'</span></td><td><input type="checkbox" value="3" class="report_box grammar" id="grammar_good" name="grammar_good"'+grammerchk3+'><span id="grammar_good_span">'+grammerval3+'</span></td><td><input type="checkbox" value="4" class="report_box grammar" id="grammar_very_good" name="grammar_very_good" '+grammerchk4+'><span id="grammar_very_good_span">'+grammerval4+'</span></td><td><input type="checkbox" value="5" class="report_box grammar" id="grammar_excellent" name="grammar_excellent" '+grammerchk5+'><span id="grammar_excellent_span">'+grammerval5+'</span></td></tr><tr><td>Vocabulary</td><td><input type="checkbox" value="1" class="report_box vocabulary" id="vocabulary_poor" name="vocabulary_poor"'+vocabularychk1+'><span id="vocabulary_poor_span">'+vocabularyval1+'</span></td><td><input type="checkbox" value="2" class="report_box vocabulary" id="vocabulary_average" name="vocabulary_average" '+vocabularychk2+'><span id="vocabulary_average_span">'+vocabularyval2+'</span></td><td><input type="checkbox" value="3" class="report_box vocabulary" id="vocabulary_good" name="vocabulary_good" '+vocabularychk3+'><span id="vocabulary_good_span">'+vocabularyval3+'</span></td><td><input type="checkbox" value="4" class="report_box vocabulary" id="vocabulary_very_good" name="vocabulary_very_good" '+vocabularychk4+'><span id="vocabulary_very_good_span">'+vocabularyval4+'</span></td><td><input type="checkbox" value="5" class="report_box vocabulary" id="vocabulary_excellent" name="vocabulary_excellent"'+vocabularychk5+'><span id="vocabulary_excellent_span">'+vocabularyval5+'</span></td></tr><tr><td>Clarity</td><td><input type="checkbox" value="1" class="report_box clarity" id="clarity_poor" name="clarity_poor" '+claritychk1+'><span id="clarity_poor_span">'+clarityval1+'</span></td><td><input type="checkbox" value="2" class="report_box clarity" id="clarity_average" name="clarity_average"'+claritychk2+'><span id="clarity_average_span">'+clarityval2+'</span></td><td><input type="checkbox" value="3" class="report_box clarity" id="clarity_good" name="clarity_good"'+claritychk3+'><span id="clarity_good_span">'+clarityval3+'</span></td><td><input type="checkbox" value="4" class="report_box clarity" id="clarity_very_good" name="clarity_very_good" '+claritychk4+'><span id="clarity_very_good_span">'+clarityval4+'</span></td><td><input type="checkbox" value="5" class="report_box clarity" id="clarity_excellent" name="clarity_excellent" '+claritychk5+'><span id="clarity_excellent_span">'+clarityval5+'</span></td></tr></tbody></table>';

            $(".profile_right_ida_bottom").html(final_report);
            $("#my_grade").val(data.total_point);
            $(".your_achived_point").css("display","block");
            $('#grade_get_point').text(data.total_point);
            

            }else{
               alert('You have not submit Grade Yet.');
            }
               
            }
            });
   });
   $('.clik_point_detatis_tutor').hide();
   $(".clik_point").on("click", function () {
      
   var question_id= $("#question_id").val();
   var module_id= $("#module_id").val();
   var idea_id =$("#idea_id").val();
   var idea_no =$("#tutor_idea_no").val();
   var tutor_id = $("#get_tutor_id").val();
   // alert(question_id);  
   // alert(module_id);  
   // alert(idea_id);  
   // alert(idea_no);  alert(tutor_id);  
      $.ajax({
            url: "Student/add_tutor_like",
				method: "POST",
				data: {question_id:question_id,module_id:module_id,idea_id:idea_id,idea_no:idea_no,tutor_id:tutor_id},
				dataType: 'json',
				success: function(data) {
               console.log(data);

               var total_point =data.student_point['student_point'];
               

               if(data.insert_or_update == 1){
                   alert('like added');
                }else{
                   alert('allready like added');
                }
               $(".clik_point").text(data.total_like);
               $('.clik_point_detatis_tutor').show();       
               $('.clik_point').hide();
               $('#like_get_point').text(total_point);
            }

      })
   
   });

//    =====
$("#show_questions").on("click", function () {       
       $modal = $('#show_question_idea'); 
       $modal.modal('hide');
       $modal2 = $('#show_question_body'); 
       $modal2.modal('show');
   });

$(".idea_title_modal").on("click", function () { 

   
       var idea_no = $(this).attr("data-value");
       var idea_title = $(this).attr("data-index");
      
       $("#idea_no").val(idea_no);
       $("#idea_title").val(idea_title);

       var html ='<textarea  class="form-control idea_title_text mytextarea" name="idea_title_text'+idea_id+'"></textarea>';
       $(".idea_modal_textarea").html(html);
       $( ".idea_title_modal" ).css('background', 'none');
       $(this).css('background', '#fb8836f0');
       
       <?php if($idea_info[0]['student_title']==1){?>
       $modal2 = $('#idea_title_show'); 
       $modal2.modal('show');
       <?php }?>
   });


   $(document).ready(function()
   {
   
   var wordCounts = {};

   CKEDITOR.instances.word_count.on('key', function(e) {
   var text = CKEDITOR.instances['word_count'].document.getBody().getText();
         
    
    var matches = text.match(/\b/g);
      wordCounts[this.id] = matches ? matches.length / 2 : 0;
      var finalCount = 0;
      $.each(wordCounts, function(k, v) {
         finalCount += v;
      });
      
      $('#display_count').html(finalCount);
      
      $('#total_word').val(finalCount);
      

      am_cal(finalCount);
    
   });

   $('#answer_matching').click(function () {

      var total_word = $('#total_word').val();
      var limit_word = <?=$idea_info[0]['word_limit'];?>;
      var percentage_value = (limit_word/100)*80;
      $('#percent_limit').text(percentage_value);
      
      if(total_word>limit_word){

         $('#total_limit_exceed').modal('show');

      }else if(total_word<percentage_value){
         $('#low_limit').modal('show');
         
      }else{

      var student_ans= CKEDITOR.instances['word_count'].getData();
      // alert(student_ans);
      
      var forms = $('#creative_ans_save')[0];
		var data = new FormData(forms);
      data.append("student_ans",student_ans);    
     
      $.ajax({
				url: "<?php echo base_url();?>/st_creative_ans_save",
				method: "POST",
				enctype: 'multipart/form-data',
				data: data,
				processData: false,
				contentType: false,
				cache: false,
				dataType: 'json',
				success: function(data) {
               if(data==9){
                  $('#idea_save_faild').modal('show');
                  $('#get_next_question').click(function () {
                     commonCall();
               });
               }else{
               $('#idea_save_success').modal('show');
               
               $('#get_next_question2').click(function () {
                 
                  commonCall();
               });

               }
               
            }
         });

      }

    });
    $("#add_profile").on("click", function () {  
      var forms = $('#add_profile_form')[0];
		var data = new FormData(forms);     
      $.ajax({
				url: "<?php echo base_url();?>/add_profile_info",
				method: "POST",
				enctype: 'multipart/form-data',
				data: data,
				processData: false,
				contentType: false,
				cache: false,
				dataType: 'json',
				success: function(data) {
               if(data){
                 alert("Profile updated");
               }

            }
         });
   });

   }); 
   function commonCall()
        {
            var question_order = $('#next_question').val();
           
            var module_id = $('#module_id').val();
            
            <?php if ($tutorial_ans_info) { ?>
                window.location.href ='<?php echo base_url();?>/st_show_tutorial_result/'+module_id;
            <?php }?>

            if (question_order == 0) {
                window.location.href ='<?php echo base_url();?>/st_show_tutorial_result/' + module_id ;
            }

            if (question_order != 0) {
                window.location.href = '<?php echo base_url();?>/get_tutor_tutorial_module/' + module_id + '/' + question_order;
            }
        }
</script>

<script>
   
  var success_flag = 1;
  var remaining_time;
  var clear_interval;
  var h1 = document.getElementsByTagName('h1')[0];

  function circulate1() {
   
    remaining_time = remaining_time - 1;

    var v_hours = Math.floor(remaining_time / 3600);
    var remain_seconds = remaining_time - v_hours * 3600;       
    var v_minutes = Math.floor(remain_seconds / 60);
    var v_seconds = remain_seconds - v_minutes * 60;

    if (remaining_time > 0) {
      h1.textContent = v_hours + " : "  + v_minutes + " : " + v_seconds + "  " ;          
    } else {
      var idea_answer = CKEDITOR.instances['word_count'].getData();
      var question_id = $('#question_id').val();
      var idea_id = $('#idea_id').val();
      
       
      h1.textContent = "EXPIRED";
    }
  }

  function takeDecesionForQuestion() {
    

    var exact_time = $('#exact_time').val();
   
    var now = $('#now').val();
    var opt = $('#optionalTime').val();
   //  alert(opt);

    var countDownDate =  parseInt (now) + parseInt($('#optionalTime').val());

    var distance = countDownDate - now;  
    var hours = Math.floor( distance/3600 );
   //        alert(distance)
   var x = distance % 3600;

   var minutes = Math.floor(x/60); 

   var seconds = distance % 60;

   var t_h = hours * 60 * 60;
   var t_m = minutes * 60;
   var t_s = seconds;

   var total = parseInt(t_h) + parseInt(t_m) + parseInt(t_s);


   var end_depend_optional = parseInt(exact_time) + parseInt(opt);

   if(opt > total) {
   remaining_time = total;
   } else {    
   remaining_time = parseInt(end_depend_optional) - parseInt(now);
   }

   clear_interval = setInterval(circulate1,1000);

}




$("#student_ideas").click(function(){
 $(this).css("color","#fb8836f0");
 $("#tutor_ideas").css("color","");
 $(".all_ideas").css('display','none');
 $(".tutor_ans").css('display','none');
 $(".student_ans").css('display','block');
});
$("#tutor_ideas").click(function(){
 $(this).css("color","#fb8836f0");
 $("#student_ideas").css("color","");
 $(".all_ideas").css('display','none');
 $(".student_ans").css('display','none');
 $(".tutor_ans").css('display','block');
});
$("#tutor_ideas").css("color","#fb8836f0");


$("#submit_button").click(function(){

   let checked_checkbox = [];
   $(".report_box:checked").each(function() {
      var all_class = $(this).attr('class').split(' ');
      var className = all_class[1];
      var point =  $(this).val();
      var name = $(this).attr('name');
      var check_data = name+','+className+','+point;
      checked_checkbox.push(check_data);
      
   });


   var checker_id = "<?=$user_id;?>";
   var submited_student_id=$("#submited_ans_view_student_id").val();
   var question_id= $("#question_id").val();
   var module_id= $("#module_id").val();
   var idea_id =$("#idea_id").val();
   var idea_no =$("#submited_ans_idea_no").val();
   var total_point =$("#my_grade").val();

   $.ajax({
   url: "<?php echo base_url();?>/submit_student_grade",
   method: "POST",
   enctype: 'multipart/form-data',
   data: {checker_id:checker_id,submited_student_id:submited_student_id,question_id:question_id,module_id:module_id,idea_id:idea_id,idea_no:idea_no,checked_checkbox:checked_checkbox,total_point:total_point},
   cache: false,
   dataType: 'json',
   success: function(data) {
           if(data.status==1){
              alert("Data saved");
           }else if(data.status==2){
            alert("Data Updated"); 
           }

           var total_point =data.student_point['student_point'];

         $(".your_achived_point").css("display","block");
         $(".tutor_check_button").removeAttr('disabled');
         $("#tutor_grade_show").css("display","none");
         $("#tutor_grade").css("display","block");
         $('#grade_get_point').text(total_point);
        }
   });

});

   $(".report_box").change( function() {
      var all_class = $(this).attr('class').split(' ');
      var className = all_class[1];

      $(this).removeAttr('checked');
      $('.'+className).each(function(){
      if($(this).is(':checked')) {
         // var name = $( this ).attr('name'); 
         // alert(name);
      var pre_point = $( this ).val(); 
      var total_point = $("#my_grade").val();
      var total = parseInt(total_point) - parseInt(pre_point);
      $("#my_grade").val(total);
      }
      });


      $('.'+className).removeAttr('checked');
      
      $( this ).attr( 'checked', true );
      var point = $( this ).val();
      $('.span_'+className).remove();
      $(this).after("<span class='span_"+className+"'>"+point+"<span>");

      var total_point = $("#my_grade").val();
      var total = parseInt(total_point) + parseInt(point);
      
      $("#my_grade").val(total);

   });

$(".view_student_idea").click(function(){
 var student_id= $(this).attr("data-index");
 var ideas= $(this).attr("data-value");
  var idea= ideas.split(',');
  var question_id = idea[0];
  var module_id = idea[1];

 $.ajax({
   url: "<?php echo base_url();?>/submited_student_idea",
   method: "POST",
   enctype: 'multipart/form-data',
   data: {student_id:student_id,question_id:question_id,module_id:module_id},
   cache: false,
   dataType: 'json',
   success: function(data) { 
  
      console.log(data);
        
      var idea_info = data.get_idea[0];
      var profile_info = data.profile_info[0];
      //var idea_information = data.idea_information[0];
      //var teacher_correction = data.teacher_correction[0];
      var country = data.country[0];
        
    
      var student_name = idea_info.name;
      var student_ans = idea_info.student_ans;
      var profile_image= profile_info.profile_image;

      
      var student_info = '<tr><td>Created</td><td>'+profile_info.created+'</td></tr><tr><td>Name</td><td >'+profile_info.student_name+'</td></tr><tr><td>Grade/Year</td><td>'+profile_info.student_grade+'</td></tr><tr><td>School</td><td>Qstudy</td></tr><tr><td>Country</td><td>'+country.countryName+'</td></tr>';

     
      var check_array = Array.isArray(data.teacher_correction[0]);
      
      if(data.teacher_correction.length>0){
         
         var teacher_correction = data.teacher_correction[0];
         
         var idea_correction_img = teacher_correction.teacher_correction;
         var total_point = teacher_correction.total_point;
         var checked_checkbox = teacher_correction.checked_checkbox;
     
         $("#tutor_report").val(checked_checkbox);
         $("#teacher_correction_img").attr('src',idea_correction_img);
         $("#tutor_grade").val(total_point);
      }
   
      
      
     
      $("#profile_image").attr('src','<?php echo base_url(); ?>assets/uploads//profile/thumbnail/'+profile_image);
      $("#submited_ans_view_student_id").val(idea_info.student_id);
      //$(".blue").text('"'+idea_information.idea_title+'"');
      $("#submited_ans_idea_no").val(idea_info.idea_no);
      
      $(".student_ans_modal").html(student_ans);
      $(".student_name").html(student_name);
      $("#show_question_idea").modal("show");
      $("#student_info").html(student_info);
      

   }

 });

 $("#tutor_report_show").click(function(){
   

        var relchk1='';
        var relval1='';
        var relchk2='';
        var relval2='';
        var relchk3='';
        var relval3='';
        var relchk4='';
        var relval4='';
        var relchk5='';
        var relval5='';

        var creativechk1='';
        var creativeval1='';
        var creativechk2='';
        var creativeval2='';
        var creativechk3='';
        var creativeval3='';
        var creativechk4='';
        var creativeval4='';
        var creativechk5='';
        var creativeval5='';

        var grammerchk1='';
        var grammerval1='';
        var grammerchk2='';
        var grammerval2='';
        var grammerchk3='';
        var grammerval3='';
        var grammerchk4='';
        var grammerval4='';
        var grammerchk5='';
        var grammerval5='';

        var vocabularychk1='';
        var vocabularyval1='';
        var vocabularychk2='';
        var vocabularyval2='';
        var vocabularychk3='';
        var vocabularyval3='';
        var vocabularychk4='';
        var vocabularyval4='';
        var vocabularychk5='';
        var vocabularyval5='';

        var claritychk1='';
        var clarityval1='';
        var claritychk2='';
        var clarityval2='';
        var claritychk3='';
        var clarityval3='';
        var claritychk4='';
        var clarityval4='';
        var claritychk5='';
        var clarityval5='';

         var relevance = '';
         var creativity = '';
         var grammar = '';
         var vocabulary = '';
         var clarity = '';
         
         var checked_checkbox = $("#tutor_report").val();
         if(checked_checkbox==''){
          alert('Tutor does not grade yet');
         }else{
         var reports =JSON.parse(checked_checkbox);
         
         var i='';
         for(i=0;i<reports.length;i++){
            var checked= 'checked';
            var report= reports[i].split(',');
            //console.log(report);

            if(report[1]=='relevance'){

                if(report[2]==1){
                 var relchk1='checked';
                 var relval1= 1;
                }
                if(report[2]==2){
                 var relchk2='checked';
                 var relval2= 2;
                }
                if(report[2]==3){
                 var relchk3='checked';
                 var relval3= 3;
                }
                if(report[2]==4){
                 var relchk4='checked';
                 var relval4= 4;
                }
                if(report[2]==5){
                 var relchk5='checked';
                 var relval5= 5;
                }
          
            }

            if(report[1]=='creativity'){

              if(report[2]==1){
                 var creativechk1='checked';
                 var creativeval1= 1;
                }
                if(report[2]==2){
                 var creativechk2='checked';
                 var creativeval2= 2;
                }
                if(report[2]==3){
                 var creativechk3='checked';
                 var creativeval3= 3;
                }
                if(report[2]==4){
                 var creativechk4='checked';
                 var creativeval4= 4;
                }
                if(report[2]==5){
                 var creativechk5='checked';
                 var creativeval5= 5;
                }
            }

            if(report[1]=='grammar'){
              
                if(report[2]==1){
                 var grammerchk1='checked';
                 var grammerval1= 1;
                 
                }
                if(report[2]==2){
                 var grammerchk2='checked';
                 var grammerval2= 2;
                }
                if(report[2]==3){
                 var grammerchk3='checked';
                 var grammerval3= 3;
                }
                if(report[2]==4){
                 var grammerchk4='checked';
                 var grammerval4= 4;
                }
                if(report[2]==5){
                 var grammerchk5='checked';
                 var grammerval5= 5;
                }
            }

            if(report[1]=='vocabulary'){

                if(report[2]==1){
                 var vocabularychk1='checked';
                 var vocabularyval1= 1;
                }
                if(report[2]==2){
                 var vocabularychk2='checked';
                 var vocabularyval2= 2;
                }
                if(report[2]==3){
                 var vocabularychk3='checked';
                 var vocabularyval3= 3;
                }
                if(report[2]==4){
                 var vocabularychk4='checked';
                 var vocabularyval4= 4;
                }
                if(report[2]==5){
                 var vocabularychk5='checked';
                 var vocabularyval5= 5;
                }
            }

            if(report[1]=='clarity'){

                if(report[2]==1){
                 var claritychk1='checked';
                 var clarityval1= 1;
                }
                if(report[2]==2){
                 var claritychk2='checked';
                 var clarityval2= 2;
                }
                if(report[2]==3){
                 var claritychk3='checked';
                 var clarityval3= 3;
                }
                if(report[2]==4){
                 var claritychk4='checked';
                 var clarityval4= 4;
                }
                if(report[2]==5){
                 var claritychk5='checked';
                 var clarityval5= 5;
                }

            }
         }

        var final_report = '<table class="table"><thead><tr><th></th><th class="red">Poor</th><th class="blue">Average</th><th class="gold">Good</th><th class="green">Very Good</th><th class="orange">Excellent!</th></tr></thead><tbody><tr><td>Relevance</td><td><input type="checkbox" value="1" class="report_box relevance" id="Rel_poor" name="Rel_poor"'+relchk1+'><span id="Rel_poor_span">'+relval1+'</span></td><td><input type="checkbox" value="2" class="report_box relevance" id="Rel_average" name="Rel_average"'+relchk2+'><span id="Rel_average_span">'+relval2+'</span></td><td><input type="checkbox" value="3" class="report_box relevance" id="Rel_good" name="Rel_good"'+relchk3+'><span id="Rel_good_span">'+relval3+'</span></td><td><input type="checkbox" value="4" class="report_box relevance" id="Rel_very_good" name="Rel_very_good"'+relchk4+'><span id="Rel_very_good_span">'+relval4+'</span></td><td><input type="checkbox" value="5" class="report_box relevance" id="Rel_excellent" name="Rel_excellent"'+relchk5+'><span id="Rel_excellent_span">'+relval5+'</span></td></tr><tr><td>Creativity</td><td><input type="checkbox" value="1" class="report_box creativity" id="cre_poor" name="cre_poor"'+creativechk1+'><span id="cre_poor_span">'+creativeval1+'</span></td><td><input type="checkbox" value="2" class="report_box creativity" id="cre_average" name="cre_average"'+creativechk2+'><span id="cre_average_span">'+creativeval2+'</span></td><td><input type="checkbox" value="3" class="report_box creativity" id="cre_good" name="cre_good"'+creativechk3+'><span id="cre_good_span">'+creativeval3+'</span></td><td><input type="checkbox" value="4" class="report_box creativity" id="cre_very_good" name="cre_very_good"'+creativechk4+'><span id="cre_very_good_span">'+creativeval4+'</span></td><td><input type="checkbox" value="5" class="report_box creativity" id="cre_excellent" name="cre_excellent"'+creativechk5+'><span id="cre_excellent_span">'+creativeval5+'</span></td></tr><tr><td>Grammar/Spelling</td><td><input type="checkbox" value="1" class="report_box grammar" id="grammar_poor" name="grammar_poor"'+grammerchk1+'><span id="grammar_poor_span">'+grammerval1+'</span></td><td><input type="checkbox" value="2" class="report_box grammar" id="grammar_average" name="grammar_average"'+grammerchk2+'><span id="grammar_average_span">'+grammerval2+'</span></td><td><input type="checkbox" value="3" class="report_box grammar" id="grammar_good" name="grammar_good"'+grammerchk3+'><span id="grammar_good_span">'+grammerval3+'</span></td><td><input type="checkbox" value="4" class="report_box grammar" id="grammar_very_good" name="grammar_very_good" '+grammerchk4+'><span id="grammar_very_good_span">'+grammerval4+'</span></td><td><input type="checkbox" value="5" class="report_box grammar" id="grammar_excellent" name="grammar_excellent" '+grammerchk5+'><span id="grammar_excellent_span">'+grammerval5+'</span></td></tr><tr><td>Vocabulary</td><td><input type="checkbox" value="1" class="report_box vocabulary" id="vocabulary_poor" name="vocabulary_poor"'+vocabularychk1+'><span id="vocabulary_poor_span">'+vocabularyval1+'</span></td><td><input type="checkbox" value="2" class="report_box vocabulary" id="vocabulary_average" name="vocabulary_average" '+vocabularychk2+'><span id="vocabulary_average_span">'+vocabularyval2+'</span></td><td><input type="checkbox" value="3" class="report_box vocabulary" id="vocabulary_good" name="vocabulary_good" '+vocabularychk3+'><span id="vocabulary_good_span">'+vocabularyval3+'</span></td><td><input type="checkbox" value="4" class="report_box vocabulary" id="vocabulary_very_good" name="vocabulary_very_good" '+vocabularychk4+'><span id="vocabulary_very_good_span">'+vocabularyval4+'</span></td><td><input type="checkbox" value="5" class="report_box vocabulary" id="vocabulary_excellent" name="vocabulary_excellent"'+vocabularychk5+'><span id="vocabulary_excellent_span">'+vocabularyval5+'</span></td></tr><tr><td>Clarity</td><td><input type="checkbox" value="1" class="report_box clarity" id="clarity_poor" name="clarity_poor" '+claritychk1+'><span id="clarity_poor_span">'+clarityval1+'</span></td><td><input type="checkbox" value="2" class="report_box clarity" id="clarity_average" name="clarity_average"'+claritychk2+'><span id="clarity_average_span">'+clarityval2+'</span></td><td><input type="checkbox" value="3" class="report_box clarity" id="clarity_good" name="clarity_good"'+claritychk3+'><span id="clarity_good_span">'+clarityval3+'</span></td><td><input type="checkbox" value="4" class="report_box clarity" id="clarity_very_good" name="clarity_very_good" '+claritychk4+'><span id="clarity_very_good_span">'+clarityval4+'</span></td><td><input type="checkbox" value="5" class="report_box clarity" id="clarity_excellent" name="clarity_excellent" '+claritychk5+'><span id="clarity_excellent_span">'+clarityval5+'</span></td></tr></tbody></table>';

      $(".profile_right_ida_bottom").html(final_report);
         }
 });

});
$(".view_tutor_idea").click(function(){
 var tutor_id= $(this).attr("data-index");
 var ideas= $(this).attr("data-value");
 var idea= ideas.split(',');
 var idea_id = idea[0];
 var idea_no = idea[1];

 $.ajax({
   url: "<?php echo base_url();?>/submited_tutor_idea",
   method: "POST",
   enctype: 'multipart/form-data',
   data: {tutor_id:tutor_id,idea_id:idea_id,idea_no:idea_no},
   cache: false,
   dataType: 'json',
   success: function(data) {
        
      var idea_info = data.get_idea[0];
      var profile_info = data.profile_info[0];
      var idea_information = data.idea_information[0];
      // var teacher_correction = data.teacher_correction[0];
      var country = data.country[0];
      //  console.log(idea_info[0].id);
      var tutor_name = idea_info.name;
      var tutor_ans = idea_info.student_ans;
      //var profile_image= profile_info.profile_image;

      $(".blue").text('"'+idea_information.idea_title+'"');
     
      $(".tutor_ans_modal").html(tutor_ans);
      $(".tutor_name").html(tutor_name);
      $("#get_tutor_id").val(idea_info.tutor_id);
      $("#show_question_idea_tutor").modal("show");
      $("#tutor_submit_date").text(idea_info.submit_date);
      $("#tutor_idea_no").val(idea_information.idea_no);

   }

 });
});
</script>

<?= $this->endSection() ?>