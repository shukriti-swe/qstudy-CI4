<?= $this->extend('qstudy/master_dashboard'); ?>
<?= $this->section('content'); ?>

<style>
    td {
        border: 2px solid #f68d20 !important;
    }
    
    .checkbox input[type="checkbox"]{
        position: absolute;
        margin-top: 4px\9;
        margin-left: 0px;
    }

</style>


<div class="" style="margin-left: 15px;">
    <div class="row">
        <!--<div class="col-md-2"></div>-->
        <div class="col-md-12 user_list">
            <div class="panel-group " id="task_accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingOne">
                        <h4 class="panel-title text-center">
                            <img style="float:left;" src="assets/images/email-read.png" alt="" width="45px" height="45px;">
                            <a role="button" data-toggle="collapse" data-parent="#task_accordion" href="#collapseOnetask" aria-expanded="true" aria-controls="collapseOne"> 
                                <strong><span style="font-size : 18px; ">  Set Message </span></strong>
                            </a>
                        </h4>
                    </div>
                    <form action="<?php echo base_url();?>/message/set" method="POST"> 
                        
                        <input type="hidden" name="message_id" value="">
                        
                        <div class="row panel-body">
                            <div class="col-sm-12 text-right"> 
                                <a href="<?php echo base_url();?>/message/topics" style="display: inline-block;">
                                    <button type="button" class="btn btn_next" id=""><i class="fa fa-times" style="padding-right: 5px;"></i>Cancel</button>
                                </a>
                                <button type="submit"  class="btn btn_next" id=""><i class="fa fa-check" style="padding-right: 5px;"></i>Save</button>
                            </div> 
                        </div>
                        <div class="row panel-body">
                            <div class="col-sm-4"> 
                                <div class="form-group row">
                                    <label for="title" class="col-sm-2 control-label">Title</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="title" placeholder="" value="<?php echo $topic['topic']; ?>" readonly>
                                        <input type="hidden" name="topicId" value="<?php echo $topic['id']; ?>">
                                    </div>
                                </div>
                            
                                <div class="form-group row">
                                    <label for="body" class="col-sm-2 control-label">Body</label>
                                    <div class="col-sm-10">
                                        <textarea class="mytextarea" name="body" id="body" cols="30" rows="3"></textarea>
                                    </div>
                                </div> 
                            </div>
                            <div class="col-sm-3"> 
                                
                                <div class="form-group row">
                                    <!--<label for="title" class="col-sm-2 control-label">Date to Show</label>-->
                                    <div class="col-sm-6">
                                        <div id="mdp-demo"></div>
                                        <input type="hidden" name="dateToShow" value="">
                                    </div>
                                </div>
                                

                            </div>
                            <div class="col-sm-5"> 
                                <div class="col-sm-12">
                                    <div class="checkbox">
                                        <label>Email For All Student(Per Level)
                                            <input type="checkbox" class="student_level" name="email_for_student" value="1">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12" id="level_div" style="display: none;margin-left: 20px;">
                                    <label for="title">Grade/Year/Level</label>
                                    <select class="form-control select2" name="student_grade">
                                        <option value="">Select Grade/Year/Level</option>
                                        <?php $grades = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]; ?>
                                        <?php foreach ($grades as $grade) { ?>
                                            <option value="<?php echo $grade; ?>">
                                                <?php echo $grade; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    
                                </div>
                              <?php if(($single_data->user_type == 4) || ($single_data->user_type == 5)){?>  
                                <?php if($single_data->user_type == 4){?> 
                                    <input type="hidden" class="school_level" name="email_for_school" value="1">
                                    <input type="hidden" name="school_id" id="" value="<?php echo $single_data->id;?>">
                                <?php } else {?>
                                    <input type="hidden" class="school_level" name="email_for_corporate" value="1">
                                    <input type="hidden" name="corporate_id" id="" value="<?php echo $single_data->id;?>">
                                 <?php }?>
                               
                             <?php } else {?>
                                <div class="col-sm-12">
                                    <div class="checkbox">
                                        <label>Email For Entire School
                                            <input type="checkbox" class="school_level" name="email_for_school" value="1">
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12" id="school_div" style="display: none;margin-left: 20px;">
                                    <label for="title">Schools</label>
                                    <select class="form-control select2" name="school_id">
                                        <option value="">Select School</option>
                                        <?php foreach ($all_school as $school) { ?>
                                            <option value="<?php echo $school['id'] ?>">
                                                <?php echo $school['name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    
                                </div>
                                
                             <?php } ?>   
                            </div>
                        </div>
                    </form>

                </div>

            </div>

        </div>
    </div>
</div>


<script>
    //$('.table').DataTable({});
    $( document ).ready(function() {
        show_student_level();
        show_school_level();
        
        $('#mdp-demo').multiDatesPicker({
//            beforeShow: function() { $(this).datepicker().addClass("datepickerBorder"); },
//            onClose: function() { $(this).datepicker().removeClass("datepickerBorder"); },
            dateFormat : "yy-mm-dd",
            
//            showOn: "button",
//            inline:true,
            onSelect: function() {
                $("input[name=dateToShow]").val($(this).multiDatesPicker("getDates"));    
            }
        });
        
//        $(".datepicker").datepicker('show');
//        $(document).off('mousedown', $.datepicker._checkExternalClick);


    });
    
    $( ".student_level" ).click(function() {
        show_student_level();
    });
    
    function show_student_level(){
        if ($('input.student_level').is(':checked')) {
            $("#level_div").show();
        } else {
            $("#level_div").hide();
        }
    }
    
    
    $( ".school_level" ).click(function() {
        show_school_level();
    });
    
    function show_school_level(){
        if ($('input.school_level').is(':checked')) {
            $("#school_div").show();
        } else {
            $("#school_div").hide();
        }
    }
</script>


<?= $this->endSection() ?>