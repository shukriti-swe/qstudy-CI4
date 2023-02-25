<?= $this->extend('preview/preview_master'); ?>
<?= $this->section('content'); ?>

<script src="<?php echo base_url();?>/assets/ckeditor/plugins/ckeditor_wiris/integration/WIRISplugins.js?viewer=image"></script>

<style type="text/css">
  .btn_next {
    font-family: "HelveticaNeue";
    color: #2198c5;
    display: inline-block;
    padding: 0;
    line-height: 20px;
    padding: 4px 3px;
    text-align: center;
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
    background: url(../images/next_bg.png) bottom repeat-x #fff;
    border-radius: 5px;
    border: 1px solid #e2e1e6;
}
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

.ss_timer {
    margin-left: 2px;
    display: inline-block;
    background: #eeeef0;
    border: 0;
    min-width: 110px;
    text-align: center;
}
.ss_timer h1 {
    border: 1px solid #a8a2a2;
    font-size: 17px;
    margin: 0;
    line-height: 28px;
    padding: 3px 0px;
    color: #222;
}

.ss_s_b_main .panel-title a {
    text-align: center;
    color: #ffffff;
    font-size: 16px;
    font-weight: 800;
}

.workout_menu ul li a {
    padding: 5px;
    background: #a9a8a8;
    color: #fff;
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

<?php 

foreach ($total_question as $ind) {

  if ($ind['user_id'] != "") {
    $index_user_id = $ind['user_id'];
    $index_moduleType = $ind['moduleType'];
  }

if ($ind["question_type"] == 14) {
  $chk = $ind["question_order"];
 }

} 
  ?>



<?php
$this->session=session();
$question_order_array = array_column($total_question, 'question_order');
$last_question_order = end($question_order_array);

$key = $question_info_s[0]['question_order'];
if ($module_info[0]["moduleType"] != 3) {
      date_default_timezone_set('Asia/Dhaka');
    }
    // date_default_timezone_set('Asia/Dhaka');
    $module_time = time();
    $key = $question_info_s[0]['question_order'];

if ($tutorial_ans_info) {
    $temp_table_ans_info = json_decode($tutorial_ans_info[0]['st_ans'], true);
    $desired = $temp_table_ans_info;
} else {
    $desired = $this->session->get('data');
}
    
//    For Question Time
    $question_time = explode(':', $question_info_s[0]['questionTime']);
    
    $hour = 0;
    $minute = 0;
    $second = 0;
if (is_numeric($question_time[0])) {
    $hour = $question_time[0];
} if (is_numeric($question_time[1])) {
    $minute = $question_time[1];
} if (is_numeric($question_time[2])) {
    $second = $question_time[2];
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

//    End For Question Time

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

<!--         ***** Only For Special Exam *****         -->
<?php if ($module_type == 3) { ?>
    <input type="hidden" id="exam_end" value="<?php echo strtotime($module_info[0]['exam_end']);?>" name="exam_end" />
    <input type="hidden" id="now" value="<?php echo $module_time;?>" name="now" />
    <input type="hidden" id="optionalTime" value="<?php echo $module_info[0]['optionalTime'];?>" name="optionalTime" />
    <input type="hidden" id="exact_time" value="<?php echo $this->session->get('exact_time');?>" />
<?php }?>
    
<!--         ***** For Tutorial & Everyday Study *****         -->    
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
    ?>col-sm-5<?php
    //}?>">
    <?php if ($module_type == 1) { ?>
      <a href="<?php echo base_url();?>/all_tutors_by_type/<?php echo $index_user_id;?>/<?php echo $index_moduleType; ?>" style="display: inline-block;">Index</a>
    <?php } else {?>
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
  <div style="text-align: left;" class="text-center <?php echo $col_class?><?php //if ($module_type != 3) {
    //echo 'col-sm-7';
    //} else {
      //echo 'col-sm-6';
    //}?> ss_next_pre_top_menu">

    

    
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

    <a class="btn btn_next" id="draw" onClick="test()" data-toggle="modal" data-target=".bs-example-modal-lg" style="
    margin-top: -3px;" >
     Workout <img src="assets/images/icon_draw.png">
   </a>
 </div>
</div>
    <div class="container-fluid">
    <div>
              <div style="position: absolute;left:-1000px;min-height: 250px;min-width: 600px;text-align: center;" id="quesBody">
                    <?php echo $question_info['questionName']; ?>

              </div>
              
            </div>
        <form id="answer_form">
            
            <input type="hidden" value="<?php echo $question_info_s[0]['question_id']; ?>" name="question_id" id="question_id">

            <?php // if (array_key_exists($key, $total_question) && !$tutorial_ans_info) { ?>
                <?php if (($last_question_order != $key) && !$tutorial_ans_info) {?>
                  <input type="hidden" id="next_question" value="<?php echo $question_info_s[0]['question_order'] + 1; ?>" name="next_question" />
                <?php } else { ?>
                  <input type="hidden" id="next_question" value="0" name="next_question" />
                <?php } ?>
            <input type="hidden" id="module_id" value="<?php echo $question_info_s[0]['module_id'] ?>" name="module_id">
            <input type="hidden" id="current_order" value="<?php echo $key; ?>" name="current_order">   
            <input type='hidden' id="module_type" value="<?php echo $question_info_s[0]['moduleType']; ?>" name='module_type'>
            
            <input type='hidden' id="student_question_time" value="" name='student_question_time'>
            
            <div class="row">
                <div class="ss_s_b_main" style="min-height: 70vh">
                    <div class="col-sm-4">
                        <h4 class="panel-title">
                            <?php if($module_info[0]['user_type'] == 7) {?>
                                    <!-- <a style="cursor: pointer;">
                                      <span style="background: #959292;color: white; border: 5px solid #959292;" class=" qstudy_Instruction_click">
                                          <img src="assets/images/icon_draw.png" ><b> Instruction</b>
                                      </span> 
                                    </a> -->
                                    <div class="workout_menu" style=" padding-right: 15px;">
                                        <ul>
                                        <li><a style="cursor:pointer" id="show_question" class=" qstudy_Instruction_click"> <img src="assets/images/icon_draw.png"/> Instruction</a></li>

                                        <?php if ($module_type == 3 || (($module_type == 2 || $module_type == 1) && $question_time_in_second != 0)) { ?>
                                            <li><div class="ss_timer" id="demo"><h1>00:00:00 </h1></div></li>
                                        <?php }elseif ($module_type == 2 && $question_info_s[0]['optionalTime'] != 0){?>
                                            <li><div class="ss_timer" id="demo"><h1>00:00:00 </h1></div></li>
                                        <?php }?>

                                        <?php if ($question_info_s[0]['isCalculator']) : ?>
                                            <li><input type="hidden" name="" id="scientificCalc"></li>
                                        <?php endif; ?>
                                        </ul>
                                    </div>

                                <?php }else{?>
                            <a role="button" <?php if ($module_info[0]['moduleName']) {
                                ?>onclick="abc()"<?php
                                             } else {
?> data-toggle="collapse" data-parent="#accordion" href="#collapseOne"<?php
}?> aria-expanded="true" aria-controls="collapseOne">
                  <span style="background: #959292;color: white; border: 5px solid #959292;" class="Instruction_click"><img src="assets/images/icon_draw.png"> Instruction</span>
                            </a>
                            <?php }?>
                        </h4>
                    </div>
                    
                    <div class="col-sm-4" style="text-align: center">
                      <div style="border:1px solid #d1d6d8;">
                        <div class="math_plus" >
                          <?php echo $question_info['questionName']; ?>
                        </div>
                        <div class="row">
                          <div class="col-sm-12 times_table_div">
                              <div>
                                  <?php foreach ($question_info['factor1'] as $factor1) { ?>
                                  <div class="form-group" style="font-size: 30px;">
                                      <?php echo $factor1; ?>
                                  </div>
                                  <?php }?>
                              </div>
                              
                              <div>
                                  <span style="font-size: 30px;">X</span>
                              </div>
                              
                              <div>
                                  <?php foreach ($question_info['factor2'] as $factor2) { ?>
                                  <div class="form-group" style="font-size: 30px;">
                                      <?php echo $factor2; ?>
                                  </div>
                                  <?php }?>
                              </div>
                              
                              <div>
                                  <span style="font-size: 30px;">=</span>
                              </div>
                              
                              <div>
                                  <input autofocus="autofocus" type="text"  class="form-control" id="time_table_focus" name="answer[]" autocomplete="off" style="font-size: 30px;">
                              </div>
                          </div>
                        </div>
                      </div>
                        
                        
                        
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        
                        <button class="btn btn_next" type="submit" id="answer_matching">Submit</button>
                        
                    </div>

                    <div class="col-sm-4">
                        <div class="panel-group" id="raccordion" role="tablist" aria-multiselectable="true">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingOne">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#taccordion" href="#collapsethree" aria-expanded="true" aria-controls="collapseOne">  
                                            <span>Module Name:<?php echo isset($module_info[0]['moduleName'])?$module_info[0]['moduleName']:'Not found'; ?></span>
                                        </a>
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
                                          <?php }else if($desired[$i]['ans_is_right'] == 'idea'){?>
                                    <span class="glyphicon glyphicon-pencil" style="color: red;"></span>
                                            
                                    <?php   } else {?>
                                      <span class="glyphicon glyphicon-remove" style="color: red;"></span>
                    <?php if ($qus_tutorial && ($module_info[0]['repetition_days'] == '' || $module_info[0]['repetition_days'] == 'null')){?>
                                                  <span class="question_tutorial_view" question_id="<?php echo $ind['question_id']; ?>" style="font-weight: bolder;color: #ff8b00;font-size: 20px;margin-left: 3px;">T</span>
                                              <?php }?>
                                          <?php }
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

                    <div class="col-sm-4" id="draggable" style="display: none;">
                        <div class="panel-group" id="waccordion" role="tablist" aria-multiselectable="true">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingOne">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#waccordion" href="#collapseworkout" aria-expanded="true" aria-controls="collapseworkout">  Workout</a>
                                    </h4>
                                </div>
                                <div id="collapseworkout" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                                    <div class="panel-body">
                                        <textarea name="workout" class="mytextarea"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                
                <!--<div class="col-xs-12" style="text-align: center;">
                    <button class="btn btn_next" type="submit" id="answer_matching">Submit</button>
                </div>-->
                
            </div>
        </form>
    </div>
</div>


<?php $i = 1;
$total = 0;
foreach ($total_question as $ind) {
    if($ind['question_video']!='' && $ind['question_video']!="[]"){ ?>
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
                        
                        <video controls style="width: 100%" id="videoTag<?php echo $i; ?>">
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
<?php }$i++; } ?>
<script>
    $(window).on('load',function(){
      <?php 
        foreach ($total_question as $ind) {
          if ( ($ind["question_type"] !=14) && ($question_info_s[0]['question_order'] == $ind['question_order']) ) { ?>

          var id= <?php echo $ind['question_order']; ?>;  
          <?php 
          if($ind['question_video']!='' && $ind['question_video']!="[]"){ ?>
        showQuestionVideo(id);
        <?php }}}?>
    });

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
</script>

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
                <button id="get_next_question" type="button" class="btn btn_blue" data-dismiss="modal">Ok</button>        
            </div>
        </div>
    </div>
</div>

<div class="modal fade ss_modal" id="ss_info_worng" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">&nbsp;</h4>
            </div>
            <div class="modal-body row">
                <i class="fa fa-close" style="font-size:20px;color:red"></i> <span class="ss_extar_top20">Your answer is wrong</span>
                <br><?php echo $question_info_s[0]['question_solution']; ?>  
            </div>
            <div class="modal-footer">
                <button type="button" id="question_reload" class="btn btn_blue" data-dismiss="modal" onclick="reloadFunction()">close</button>         
            </div>
        </div>
    </div>
</div>
    
<div class="modal fade ss_modal" id="times_up_message" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title" id="myModalLabel">Times Up</h4>
            </div>
            <div class="modal-body row">
                <i class="fa fa-close" style="font-size:20px;color:red"></i> 
                <br><?php echo $question_info_s[0]['question_solution']; ?>  
            </div>
            <div class="modal-footer">
                <button type="button" id="question_reload" class="btn btn_blue" data-dismiss="modal" onclick="reloadFunction()">close</button>         
            </div>
        </div>
    </div>
</div>

<?php $i = 1;
foreach ($total_question as $ind) { ?>
    <div class="modal fade ss_modal ew_ss_modal" id="show_description_<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title" id="myModalLabel"> Question Description </h4>
                </div>
                <div class="modal-body">
                    <textarea class="form-control" name="questionDescription">
                        <?php echo $ind['questionDescription']; ?>
                    </textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn_blue" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php $i++;
} ?>

<script>

   $(window).load(function(){
       
    $('#time_table_focus').focus();
    
    <?php if ($module_type == 3  || ($module_type == 2 && $question_info_s[0]['optionalTime'] != 0 && ($question_time_in_second > $moduleOptionalTime || $question_time_in_second == 0))) { ?>
        takeDecesion();
        <?php }?>
    
});

    var time_count = 0;

    var popupTimes = 1;
    
    $("input").keypress(function(event) {
        if (event.which == 13) {
            event.preventDefault();
            $("form").submit();
        }
    });
    
//    $('#answer_matching').click(function () {
    $("#answer_form").on('submit', function (e) {
        e.preventDefault();

        

        var form = $("#answer_form");
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url();?>/st_answer_times_table',
            data: form.serialize(),
            dataType: 'html',
            success: function (results) {
                if (results == 6) {
                    window.location.href = '<?php echo base_url();?>/st_show_tutorial_result/'+$("#module_id").val();
                }
                if (results == 3) {

                  popupTimes = 0;
                    $('#ss_info_worng').modal('show');
                } if (results == 2) {
                    $('#ss_info_sucesss').modal('show');
                    $('#get_next_question').click(function () {
                        commonCall();
                    });
                }
                if (results == 5) {
                    commonCall();
                }
                
            }
        });

    });
    
    function commonCall() {
        $question_order = $('#next_question').val();
        var total_q = "<?= count($total_question); ?>";
        var present_order = "<?= 
        $uri = new \CodeIgniter\HTTP\URI(current_url());
        $uri->getSegment('4'); ?>";

        $module_id = $('#module_id').val();

        if (total_q == present_order) {
            window.location.href ='<?php echo base_url();?>/st_show_tutorial_result/'+$module_id;
        }
        
        if ($question_order == 0) {
            window.location.href ='<?php echo base_url();?>/st_show_tutorial_result/' + $module_id ;
        }
        if (total_q != present_order) {
            window.location.href ='<?php echo base_url();?>/get_tutor_tutorial_module/' + $module_id + '/' + $question_order;
        }

        <?php if ( count($tutorial_ans_info) > 0 ) {?>
            window.location.href ='<?php echo base_url();?>/st_show_tutorial_result/'+$module_id;
        <?php }?>
    }
    
    function showModalDes(e)
    {
        $('#show_description_' + e).modal('show');
    }

</script>

<script>

    function takeDecesion() {
        
        var exact_time = $('#exact_time').val();
        var countDownDate =  $('#exam_end').val();
        
        
        var now = $('#now').val();
        var opt = $('#optionalTime').val();
        var h1 = document.getElementsByTagName('h1')[0];    
    
        
        var distance = countDownDate - now;  
        var hours = Math.floor( distance/3600 );
        
        var x = distance % 3600;
    
        var minutes = Math.floor(x/60); 
        
        var seconds = distance % 60;

        var t_h = hours * 60 * 60;
        var t_m = minutes * 60;
        var t_s = seconds;
    
        var total = parseInt(t_h) + parseInt(t_m) + parseInt(t_s);
    
        var remaining_time;
        var end_depend_optional = parseInt(exact_time) + parseInt(opt);
    
        // if(opt > total) {
        //     remaining_time = total;
        // } else {    
        //     remaining_time = parseInt(end_depend_optional) - parseInt(now);
        // }

        if(opt > 0){
      remaining_time = parseInt(end_depend_optional) - parseInt(now);
    
      } else {  
        remaining_time = total;
      }
    
        setInterval(circulate,1000);
    
        function circulate() {
            time_count++;
            remaining_time = remaining_time - 1;

            var v_hours = Math.floor(remaining_time / 3600);
            var remain_seconds = remaining_time - v_hours * 3600;       
            var v_minutes = Math.floor(remain_seconds / 60);
            var v_seconds = remain_seconds - v_minutes * 60;
            
            $("#student_question_time").val(time_count);

            if (remaining_time > 0) {
                h1.textContent = v_hours + " : "  + v_minutes + " : " + v_seconds + "  " ;          
           } else {
                var form = $("#answer_form");
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url();?>/st_answer_times_table',
                    data: form.serialize(),
                    dataType: 'html',
                    success: function (results) {
                        window.location.href ='<?php echo base_url();?>/st_show_tutorial_result/'+$('#module_id').val();
                    }
                });
                h1.textContent = "EXPIRED";
           }
        }
    
    }

    
</script>


<script>
    function takeDecesionForQuestion() {
        
        var exact_time = $('#exact_time').val();
        
        var now = $('#now').val();
        var opt = $('#optionalTime').val();
        var h1 = document.getElementsByTagName('h1')[0];
        
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
    
        var remaining_time;
        var end_depend_optional = parseInt(exact_time) + parseInt(opt);
    
        if(opt > total) {
            remaining_time = total;
        } else {    
            remaining_time = parseInt(end_depend_optional) - parseInt(now);
        }
        var timeUp = 1;
    
        setInterval(circulate1,1000);
    
        function circulate1() {
            time_count++;
            remaining_time = remaining_time - 1;
            
            var v_hours = Math.floor(remaining_time / 3600);
            var remain_seconds = remaining_time - v_hours * 3600;       
            var v_minutes = Math.floor(remain_seconds / 60);
            var v_seconds = remain_seconds - v_minutes * 60;
            
            $("#student_question_time").val(time_count);
            
            if (remaining_time > 0) {
                h1.textContent = v_hours + " : "  + v_minutes + " : " + v_seconds + "  " ;          
           } else {
                var form = $("#answer_form");

                if (timeUp == 1) {
                  
                  $.ajax({
                      type: 'POST',
                      url: '<?php echo base_url();?>/st_answer_times_table',
                      data: form.serialize(),
                      dataType: 'html',
                      success: function (results) {
                          if (results == 3) {

                            if (popupTimes == 1) {
                              if(timeUp == 1){
                                $('#times_up_message').modal('show');
                              }
                              timeUp = 0;
                            }
                            $('#question_reload').click(function () {
                                location.reload(); 
                            });
                          } if (results == 2) {
                              $('#ss_info_sucesss').modal('show');
                              $('#get_next_question').click(function () {
                                  commonCall();
                              });
                          }

                          timeUp = 0;
                      }
                  });

                  
              }
                h1.textContent = "EXPIRED";
           }
        }
    
    }

    <?php if (($module_type == 1 || $module_type == 2) && $question_time_in_second != 0 ) { ?>
        $(document).ready(function(){
        takeDecesionForQuestion();
    });
    <?php }?>

    function reloadFunction() {
      location.reload(); 
    }
</script>

<?php require_once(APPPATH.'Views/students/question_module_type_tutorial/drawingBoard.php');?>
<?php require_once(APPPATH.'Views/students/question_module_type_tutorial/module_video.php');?>
<?php require_once(APPPATH.'Views/students/question_module_type_tutorial/instruction_video.php');?> 

<?= $this->endSection() ?>