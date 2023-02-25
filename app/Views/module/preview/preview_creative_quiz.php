<?= $this->extend('qstudy/master_dashboard'); ?>
<?= $this->section('content'); ?>
<style>
   .rs_word_limt .top_word_limt > div{
   flex-basis: 0;
    -webkit-box-flex: 1;
    -ms-flex-positive: 1;
    flex-grow: 1;
    max-width: 100%;
}
.tutor_ans_modal{
   max-height: 300px;
    overflow-y: auto;
}
.b-btn{
display:inline-block ;
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
      border: 1px solid #ed1c24;
       background-color: #ed1c24;
       background: #ed1c24 url("data:image/gif;base64,R0lGODlhCwAKAIABAP////3cnSH5BAEKAAEALAAAAAALAAoAAAIUjH+AC73WHIsw0UCjglraO20PNhYAOw==") 3px 3px no-repeat;
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

<?php
    // $answerCount = count(json_decode($question_info_s[0]['answer']));
    // echo "<pre>";print_r($question_info_s[0]);die();
    
    date_default_timezone_set($time_zone_new);
    $module_time = time();
    
    $key = $question_info_s[0]['question_order'];

    $this->session=session();
    $desired = $this->session->get('data');
    
    //    For Question Time
    $question_time = explode(':',$question_info_s[0]['questionTime']);
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
    
    $passTime = time() -$this->session->get('exam_start');
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
//    End For Question Time
?>

<?php 


foreach ($total_question as $ind) {

if ($ind["question_type"] == 14) {
  $chk = $ind["question_order"];
 }

} 
  ?>


<?php 
    $question_instruct = isset($question_info_s[0]['question_video']) ? json_decode($question_info_s[0]['question_video']):'';
    $question_instruct_id = $question_info_s[0]['id'];

    // echo "<pre>";print_r($word_match_info[0]);die();
    $question = $question_info_s[0]['questionName'];
    $answer = $question_info_s[0]['answer'];
    $question_description = json_decode($question_info_s[0]['questionDescription'],true);
    // echo "<pre>";print_r($question_description);die();
?>
<!--         ***** For Tutorial & Everyday Study ***** -->    
<?php // if ($module_type == 2 || $module_type == 1) { ?>
    <input type="hidden" id="exam_end" value="" name="exam_end" />
    <input type="hidden" id="now" value="<?php echo $module_time;?>" name="now" />
    <input type="hidden" id="optionalTime" value="<?php echo $question_time_in_second;?>" name="optionalTime" />
    <input type="hidden" id="exact_time" value="<?php echo $this->session->get('exact_time');?>" />
<?php // }?>


<div class="ss_student_board">
    <div class="ss_s_b_top">
        <div class="ss_index_menu">
            <a href="#">Module Setting</a>
        </div>
        
        
        
        <div class="col-sm-6 ss_next_pre_top_menu">
            <?php if ($question_info_s[0]['isCalculator']) : ?>
                <input type="hidden" name="" id="scientificCalc">
            <?php endif; ?>

            <?php if ($question_info_s[0]['question_order'] == 1) { ?>                            
                <a class="btn btn_next" href="<?php echo base_url(); ?>module_preview/<?php echo $question_info_s[0]['module_id']; ?>/1"><i class="fa fa-caret-left" aria-hidden="true"></i> Back</a>
            <?php } else { ?>
                <a class="btn btn_next" href="<?php echo base_url(); ?>module_preview/<?php echo $question_info_s[0]['module_id']; ?>/<?php echo ($question_info_s[0]['question_order'] - 1); ?>"><i class="fa fa-caret-left" aria-hidden="true"></i> Back</a>
            <?php } ?> 
            <?php if (array_key_exists($key, $total_question)) { ?>
                <a class="btn btn_next" id="question_order_link" href="<?php echo base_url(); ?>/module_preview/<?php echo $question_info_s[0]['module_id']; ?>/<?php echo $question_info_s[0]['question_order'] + 1; ?>"><i class="fa fa-caret-right" aria-hidden="true"></i> Next</a>
            <?php } ?>                                        
            <a class="btn btn_next" id="draw" onClick="showDrawBoard()" data-toggle="modal" data-target=".bs-example-modal-lg">
                Workout <img src="assets/images/icon_draw.png">
            </a>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="ss_s_b_main" style="min-height: 100vh">

                <?php
                $lettry_array = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'k', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T');
                ?>
                <form id="answer_form">

                    <input type="hidden" id="module_id" value="<?php echo $question_info_s[0]['module_id'] ?>" name="module_id">
                    <?php if (array_key_exists($key, $total_question)) { ?>
                        <input type="hidden" id="next_question" value="<?php echo $question_info_s[0]['question_order'] + 1; ?>" name="next_question" />
                    <?php } else { ?>
                        <input type="hidden" id="next_question" value="0" name="next_question" />
                    <?php } ?>
                    <input type="hidden" value="<?php echo $question_info_s[0]['question_id']; ?>" name="question_id" id="question_id">
                    <input type="hidden" id="current_order" value="<?php echo $key; ?>" name="current_order">
                    <input type='hidden' id="module_type" value="<?php echo $question_info_s[0]['moduleType']; ?>" name='module_type'>
                    
                    <div class="col-sm-8" style="padding:0;">        
                        <div class="panel-group " id="accordion" role="tablist" aria-multiselectable="true">
                            <div class="panel panel-default" style="border:none;">                  
                                <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                                    <div class="panel-body ss_imag_add_right" style="padding:0;">
                                     
                    <div class="grammer_body">
                    <div class="workout_menu" style=" padding-right: 15px;">
                           <ul>
                               
                              <li><a style="cursor:pointer" id="show_question"> <img src="assets/images/icon_draw.png"/> Instruction </a></li>

                              <?php if($idea_info[0]['large_ques_body']!=''){ ?>
                              <li><a style="cursor:pointer" id="detail_question">Detail Question </a></li>
                              <?php }?>

                              <?php if($idea_info[0]['large_question_allow']!=0){ ?>
                              <li><a style="cursor:pointer" id="show_questions"> Question(Click here) </a></li>
                              <?php }?>

                              <!-- <li><a  href="javascript:;"  data-toggle="modal" data-target="#show_question_idea"> Idea 1</a></li> -->

                              <?php
                             
                              if($idea_info[0]['allow_idea']==1){
                                 $j=1;
                              foreach($idea_description as $ideas){
                              ?>
                              <li><a style="background: white; color:black;border: 1px solid #ddd7d7;" href="javascript:;" data-id="<?=$ideas['question_id'];?>,<?=$ideas['idea_no'];?>" data-index="<?=$ideas['id'];?>"data-toggle="modal" data-target="#show_question_idea_tutor" class="idea_title_modal"><?=$ideas['idea_name']?></a></li>
                              <?php $j++;}?>


                              <div><a href="javascript:;" data-toggle="modal" ><i class="fa fa-address-card" style="font-size:40px;"></i></a></div>
                              <div><a href="javascript:;"><i class="fa fa-users" style="font-size:40px;margin-left:5px;color:rgb(251, 136, 54);"></i></a></div>
                              <div><a style="cursor:pointer;" id="show_question"><img src="assets/images/icon_a_left.png"></a></div>
                              <div><a style="cursor:pointer;" id="show_question"><img src="assets/images/icon_a_right.png"></a></div>
                              <?php }?>
                           </ul>
                        </div>
                        <div class="top_textprev">
                           
                           

                        <?php
                        error_report_check(); 
                        date_default_timezone_set($time_zone_new);
                        $module_time = time();
                        
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

                        $question_time_limit = $hour.':'.$minute.':'.$second;
                        
                        $question_time_in_second = ($hour * 3600) + ($minute * 60) + $second ;
                        
                      
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
                                   
                                 $this->session=session();   
                              ?>
                           </p>
                           
                           <?php }

                           if($idea_info[0]['image_ques_body']!=''){?>
                              <p><?=$idea_info[0]['image_ques_body'];?></p>
                           
                           <?php } ?>

                           
                        </div>
                        <div class="rs_word_limt">
                        	 <div class="top_word_limt">
                            <div> 
                                    <span id="display_count">0</span> &nbsp;Words
                                    <input id="total_word" type="hidden" value="">
                          

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
                                    $time_show = $get_hour.':'.$get_min.':'.$get_sec;
                                    ?>
                                   <div class="" style="text-align: center; margin:auto;">
                                       <div class="ss_timer" id="demo"><h1><?=$time_show;?></h1></div>
                                   </div>
                                 <?php }?> 
                              
                              
                              

                                 <?php
                                   if($idea_info[0]['word_limit']!=0){
                                 ?>
                        	 	<div class="m-auto" style="padding-left: 20px;text-align:right;"><b>Word Limit </b><b class="b-btn"><?=$idea_info[0]['word_limit'];?></b></div>
                                 <?php }?>

                                 
                        	 </div>
                        	 <div class="btm_word_limt">
                        	 	<div class="content_box_word">
                        	 	  
                        	 		<div class="text-center">

                                     <input id="question_id" type="hidden" name="question_id" value="<?=$idea_info[0]['question_id']?>">
                                     <input id="idea_id" type="hidden" name="idea_id" value="<?=$idea_info[0]['id']?>">

                                     <textarea id="word_count" class="form-control preview_main_body mytextarea" name="preview_main_body" onpaste="return false;" onCopy="return false" onCut="return false"><?php if(!empty($idea_info[0]['time_hour']) || !empty($idea_info[0]['time_min']) || !empty($idea_info[0]['time_sec'])){?><elem id="time_image"><img  class="image-editor" style="padding-left: 20%;" data-height="250" data-width="200" height="179" src="<?=base_url()?>/assets/images/pv1.jpg" width="281" /></elem><?php }?></textarea>
                        	 			  
                        	 		</div> 
                        	 	</div>
                                <div class="text-center">
                                    <?php if($idea_info[0]['add_start_button']!=0){?>
                                 <button   class="btn btn_next" type="button" id="idea_start">Start</button> 
                                 <?php }?>
                                 <button id="answer_matching" class="btn btn_next" type="button">Submit </button> 
                                 <!-- <button id="answer_matching" class="btn btn_next" type="button"  data-toggle="modal" data-target="#alert_times_up">Submit </button>  -->
                               </div>
                        	 </div>
                        </div>
                                    
                    </div>

                                    </div>
                                    
                                    <div class="col-sm-5">  </div>
                                    <div class="col-sm-4" style="margin-top: 10px;">   
                                        <!-- <button type="button" class="btn btn_next" id="answer_matching">submit</button> -->
                                    </div>                    
                                    <div class="col-sm-3">  </div>

                                </div>

                            </div>


                        </div>

                    </div>

                </form>
                
                <div class="col-sm-4">
                    <div class="panel-group" id="raccordion" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingOne">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#taccordion" href="#collapsethree" aria-expanded="true" aria-controls="collapseOne">  <span>Module Name: <?php echo $question_info_s[0]['moduleName']; ?></span></a>
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
                                                  <th>Description / Video</th>                                                  
                                                </tr>
                                              </thead>
                                              <tbody>
                                                  <?php $i = 1;
                                                  $total = 0;
                                                  foreach ($total_question as $ind) { ?>
                                                  <tr>
                                                    <td>
                                                        <?php if (isset($desired[$i]['ans_is_right'])) {
                                                        if ($desired[$i]['ans_is_right'] == 'correct') {
                                                        if($ind['questionType'] ==17){ ?>
                                                        <span class="glyphicon glyphicon-pencil" style="color: red;"></span>
                                                        <?php }else{ ?>

                                                        <span class="glyphicon glyphicon-ok" style="color: green;"></span>

                                                        <?php }} else {?>
                                                            <span class="glyphicon glyphicon-remove" style="color: red;"></span>
                                                        <?php }
                                                        }?>
                                                    </td> 
 
                                                    
                                                    <?php  if ( ($ind["question_type"] !=14) && ($question_info_s[0]['question_order'] == $ind['question_order']) ) { ?>
                                                        <td style="background-color:lightblue">
                                                            <?php echo $ind['question_order']; ?>
                                                        </td>
                                                    <?php } 

                                                    elseif ( ($ind["question_type"] ==14) && $order >= $chk ) { ?>
                                                        <td style="background-color:#FFA500">
                                                            <a href="<?php echo site_url('/module_preview/').$ind['module_id'].'/'.$ind['question_order'] ?>"><?php echo $ind['question_order']; ?></a>
                                                            </td>
                                                    <?php } 

                                                    elseif ( ($ind["question_type"] ==14) && $order < $chk ) { ?>
                                                        <td style="background-color:#FFA500">
                                                            <?php echo $ind['question_order']; ?>
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
                                                  <td><?php echo $ind['questionMarks']; ?></td>
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

                        <div class="col-sm-4" id="draggable" style="display: none;">
                            <div class="panel-group" id="waccordion" role="tablist" aria-multiselectable="true">
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="headingOne">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#waccordion" href="#collapseworkout" aria-expanded="true" aria-controls="collapseworkout">  Workout</a>
                                        </h4>
                                    </div>
                                    <div id="collapseworkout" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                                        <div class="panel-body" id="setWorkoutHere">
                                            <textarea name="workout" class="mytextarea"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        <!--Success Modal-->
        <div class="modal fade ss_modal" id="ss_info_sucesss" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="height: 265px">
                    <div class="modal-header" >

                        <h4 class="modal-title" id="myModalLabel"></h4>
                    </div>
                    <div class="modal-body row" style="height: 87%;">
                        <img src="assets/images/icon_sucess.png" class="pull-left"> <br> <span class="">Your answer is correct</span> 

                    </div>
                    <div class="modal-footer">
                     <a id="next_qustion_link" href="">
                        <button type="button" class="btn btn_blue" >Ok</button>
                      </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade ss_modal" id="ss_info_faild" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title" id="myModalLabel">&nbsp;</h4>
            </div>
            <div class="modal-body row">
                <img src="assets/images/alertd_icon.png" style="height:50px;width:50px;" class="pull-left"> <span class="ss_extar_top20">Please select answer</span> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn_blue" data-dismiss="modal">Ok</button>

            </div>
            </div>
        </div>
        </div>

        <div class="modal fade" id="ss_info_worng" role="dialog">
            <div class="modal-dialog ui-draggable" style=" width: 48%;">

                <!-- Modal content-->
                <div class="modal-content" style="width: 100%;">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-close" style="font-size:20px;color:red"></i> <span class="">Your answer is wrong</span></h4>
                    </div>
                    <div class="modal-body">
                        <div id="ss_extar_top20">
                            <?php echo $question_info_s[0]['question_solution']?>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn_blue" data-dismiss="modal">close</button>   
                    </div>
                </div>

            </div>
        </div>
        
              
<!--Times Up Modal-->
<div class="modal fade ss_modal" id="times_up_message" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Times Up</h4>
            </div>
            <div class="modal-body row">
                <i class="fa fa-close" style="font-size:20px;color:red"></i> 
                <br><?php echo $question_info_s[0]['question_solution'] ?>
            </div>
            <div class="modal-footer">
                <button type="button" id="question_reload" class="btn btn_blue" data-dismiss="modal">close</button>         
            </div>
        </div>
    </div>
</div>

<?php $i = 1;
$total = 0;
foreach ($total_question as $ind) { ?>
<!--Question Video Modal-->
<div class="modal fade" id="ss_question_video<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title" id="myModalLabel">Question Video</h4>
            </div>
            <div class="modal-body">
                <?php 
                    $question_instruct_vid = isset($ind['question_video']) ? json_decode($ind['question_video']):'';
                ?>
                <?php if (isset($question_instruct_vid[0]) && $question_instruct_vid[0] != null ){ ?>
                    <video controls style="width: 100%" id="videoTag<?php echo $i; ?>">
                      <source src="<?php echo isset($question_instruct_vid[0]) ? trim($question_instruct_vid[0]) : '';?>" type="video/mp4">
                    </video>
                    <?php if (isset($question_instruct_vid[1]) && $question_instruct_vid[1] != null ): ?>
                        
                        <video controls style="width: 100%" class="video" id="videoTag<?php echo $i; ?>">
                          <source src="<?php echo isset($question_instruct_vid[1]) ? trim($question_instruct_vid[1]) : '';?>" type="video/mp4">
                        </video>
                    <?php endif ?>
                <?php }else{ ?>
                    <P>No question video</P>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn_blue closeVideoModal" value="<?php echo $i; ?>" id="closeVideoModalId<?php echo $i; ?>" onclick="videoCloseWithModal(<?php echo $i; ?>);">Close</button>
            </div>
        </div>
    </div>
</div>
<?php $i++; } ?>

<!-- /////////// -->

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
          <a  id="preview_success" class="btn btn_blue">Ok</a> 
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
    <div  class="modal-content">
     
      <div class="modal-header">
        <h4>Topic/Story Title</h4>
      </div>

        <div class="modal-body">
          
          <div class="d-flex idea_modal_textarea">
          <textarea id="idea_title_text_get" class="form-control idea_title_text" name="idea_title_text23"></textarea>
          </div> 
          
         

        </div>
        <div class="modal-footer"> 
          <button type="button" class="btn btn_blue ideabtnclose">save</button>
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
        <div  class="modal-header">
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




<script>
    function showQuestionVideo(id){
      $('#ss_question_video'+id).modal('show');
    }
    
    
    
    
    function videoCloseWithModal(id){
      $('#ss_question_video'+id).modal('hide');
      var video = $('#videoTag'+id).get(0);
      if (video.paused === false) {
        video.pause();
      } 
    }
    
    // $('.video').click(function(){this.paused?this.play():this.pause();});
</script>
  <?php $i = 1;
  foreach ($total_question as $indwww) { ?>
      <div class="modal fade ss_modal ew_ss_modal" id="show_description_<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">

                      <h4 class="modal-title" id="myModalLabel">Question Description</h4>
                  </div>
                  <div class="modal-body">
                      <textarea class="form-control" name="questionDescription"><?php echo $indwww['questionDescription']; ?></textarea>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn_blue" data-dismiss="modal">Close</button>
                  </div>
              </div>
          </div>
      </div>
      <?php $i++;
  } ?>


  <div class="modal fade" id="myModal_2222" role="dialog">
    <div class="modal-dialog ui-draggable" style=" width: 48%;">

        <!-- Modal content-->
        <div class="modal-content" style="width: 100%;height: 64%;">
            <div class="modal-header ui-draggable-handle">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <!--<h4 class="modal-title">Video Lesson</h4>-->
            </div>
            <div class="modal-body">
                
                <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne" id="textarea_2">

                   <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                            
                        </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn_blue" data-dismiss="modal">close</button>   
            </div>
        </div>

    </div>
</div>

<div  class="modal fade ss_modal " tabindex="-1" role="dialog" aria-labelledby="myModalLabel" id="show_note_details">
<!-- Modal -->
<div class="modal-dialog" role="document" style="width: 400px;">
    <div class="modal-content">
        
        <div class="btm_word_limt p-3">
            <div style="position: relative;text-align: center;">
                <h6 style="color:gray;"><span class="glyphicon glyphicon-pencil pencil_icon" style="color:red;"></span> Note</h6>
                <button style="margin-left:auto;border: none;background: white;position: absolute;right: 10px;top: 5px;" type="button" id="close_idea" class=" pull-right" data-dismiss="modal">x</button>
            </div>
            <hr>
            <div style="max-height: 200px; overflow-y: auto;">
            <?=$question_description['note_description'];?>
            </div>
            
            
        </div> 
    </div>
</div>
</div>


<script type="text/javascript">
   $("#answer_matching").hide();
   
   $("#detail_question").on("click", function () {
      $("#show_detail_question").modal('show');
   });    

   $("#topicstory").on("click", function () {        
       $modal = $('#show_question_idea'); 
       $modal.modal('hide');
       $modal2 = $('#show_question_idea_profile'); 
       $modal2.modal('show');
   });

   $("#close_idea").on("click", function () { 
       $modal = $('#show_question_idea_profile');  
       $modal.modal('show');
   });
   
   $('.clik_point_detatis_tutor').hide();
   $(".clik_point").on("click", function () { 
       $('.clik_point_detatis_tutor').show();       
       $('.clik_point').hide();
   });


   $("#idea_start").on("click", function () { 

    <?php if ($question_time_in_second != 0) { ?>
      takeDecesionForQuestion();
    <?php }?>

 
   var idea_title = "Title";
   //  if(idea_no!=''){
   $('#idea_start').hide();
   $('#answer_matching').show();

   text= '<p style="text-align:center;text-decoration:underline;"><b>Idea/Topic/Story title</b></p><p style="text-align:center;color:#fb8836f0;"><b>"'+idea_title+'"</b>&#9999;&#65039;</p><p></p>';
   CKEDITOR.instances.word_count.on('paste', function(evt) {
            evt.cancel();
         });
   CKEDITOR.instances.word_count.setData(text);

   <?php if($idea_info[0]['student_title']==1){?>
   $modal2 = $('#idea_title_show'); 
   $modal2.modal('show');
   <?php }?>

   });


   CKEDITOR.on('instanceReady', function(evt) {


   // console.log(evt.editor.getData());
   evt.editor.on('focus',function(event){
      var getData = evt.editor.getData();
      var setData = getData.replace("<p>Start write here...</p>", " ");
      evt.editor.setData(setData);
   });
   });

// $("body").delegate("#word_count", "click", function(){
//     console.log("hello1111");
// });


   $(".ideabtnclose").on("click", function () {
     
     var idea_title = $(".idea_title_text").val();
     if(idea_title == ''){
       $modal2 = $('#idea_title_failed'); 
       $modal2.modal('show');
     }else{
       $modal2 = $('#idea_title_show'); 
       $modal2.modal('hide');
      text= '<p style="text-align:center;text-decoration:underline;"><b>Idea/Topic/Story title</b></p><p style="text-align:center;color:#fb8836f0;"><b style="font-size:18px;">"'+idea_title+'"</b>&nbsp;&#9999;&#65039;</p><br><p>Start write here...</p>';

      CKEDITOR.instances.word_count.setData(text);
     }
     
   });

//    =====
$("#show_questions").on("click", function () {       
       $modal = $('#show_question_idea'); 
       $modal.modal('hide');
       $modal2 = $('#show_question_body'); 
       $modal2.modal('show');
   });

$(".idea_title_modal").on("click", function () { 
    
       var idea_id = $(this).attr("data-index");
       var idea = $(this).attr("data-id");
       var html ='<textarea  class="form-control idea_title_text mytextarea" name="idea_title_text'+idea_id+'"></textarea>';
       $(".idea_modal_textarea").html(html);
       $( ".idea_title_modal" ).css('background', 'none');
       $(this).css('background', '#f1e7b5');
       
       <?php if($idea_info[0]['student_title']==1){?>
      //  $modal2 = $('#idea_title_show'); 
      //  $modal2.modal('show');
       <?php }?>
       $.ajax({
				url: "<?php echo base_url();?>/get_preview_idea_info",
				method: "POST",
				data: {idea:idea},
				dataType: 'json',
				success: function(data) {
              
              //const student_ans = data['student_ans'].replace(/(<([^>]+)>)/gi, "");
              
              $('.tutor_name').text(data['name']);
              //$('.tutor_ans_modal').text(student_ans);
              $('.tutor_ans_modal').html(data['student_ans']);

              $('#pre_idea_no').val(data['idea_no']);   
              $('#pre_tutor_id').val(data['tutor_id']);
              $('#pre_question_id').val(data['question_id']); 
              $('#pre_idea_id').val(data['idea_id']);   
            }
         });

   });
   $(".clik_point").on("click", function () { 
   var question_id= $("#pre_question_id").val();
   var idea_id =$("#pre_idea_id").val();
   var idea_no =$("#pre_idea_no").val();
   var tutor_id = $("#pre_tutor_id").val();
   var module_id = 0;
      $.ajax({
         url: "<?php echo base_url();?>/add_tutor_like",
				method: "POST",
				data: {question_id:question_id,module_id:module_id,idea_id:idea_id,idea_no:idea_no,tutor_id:tutor_id},
				dataType: 'json',
				success: function(data) {
               console.log(data);
               
               
               if(data.insert_or_update == 1){
                   alert('like added');
                }else{
                   alert('allready like added');
                }
               $(".clik_point").text(data.total_like);
               $('.clik_point_detatis_tutor').show();       
               $('.clik_point').hide();
            }

      })
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
         

      var idea_answer = CKEDITOR.instances['word_count'].getData();
      var question_id = $('#question_id').val();
      var idea_id = $('#idea_id').val();
      var module_id = $('#module_id').val();
      var current_order = $('#current_order').val();

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url();?>/preview_save_answer_idea',
            data: {
               idea_answer: idea_answer,
               question_id: question_id,
               idea_id: idea_id,
               module_id:module_id,
               current_order:current_order,
            },
            dataType: 'html',
            success: function (results) {

                if (results == 2) {
                    var next_question = $("#next_question").val();
                    if(next_question != 0){
                        var question_order_link = $('#question_order_link').attr('href');
                    }if(next_question == 0){
                        var current_url = $(location).attr('href');
                    
                        var question_order_link = current_url;
                    }
                    
                    $("#preview_success").attr("href", question_order_link);
                    
                    $('#idea_save_success').modal('show');
                } else if (results == 0) {
                    
                    
                }
            }
        });
      }

    });


    // $("#preview_success").click(function () {
    //             location.reload();
    // });


   }); 
</script>






<script type="text/javascript">
    function show_questionModal() {
        $('#myModal_2222').modal('show');
    }
    
    $(".response_answer_class").click(function(){
    if($('.response_answer_class').is(":checked")) {  
            var question = <?//=$answerCount?>;  
            var value = $(this).val();
            $('#ans_image'+value).show();
            if(question == 1){
                for (var i = 1; i <= 10; i++)
                {
                    if(value != i){
                        $('#ans_image'+i).hide();
                        $('#response_answer_id'+i).prop('checked',false);
                    }
                }
            
            }
        }else{
        }
    });
    $(".image_click").click(function(){
    var value = $(this).val();
    $('#response_answer_id'+value).prop('checked',false);
    $('#ans_image'+value).hide();
    });
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
    // alert(opt);

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
</script>


<?//php require_once(APPPATH.'Views/module/preview/drawingBoard.php');?>


<?= $this->endSection() ?>
