<?= $this->extend('registration/master'); ?>
<?= $this->section('content');?>

<?php
$local_session = \Config\Services::session();
if( $local_session ->get('userType') == 1 && $local_session->get('registrationType') == ''){
    $local_session->set('userType', 6);
}
// print_r($this->session->userdata('user_id')) ;die();
 // if (isset($_POST['submit']) && $_POST['submit'] = 'submit')
 // {
 //    //echo "<pre>";print_r($_POST);die();
 //    $course_data['courses'] = $_POST['course'];
 //    $course_data['totalCost'] = $_POST['totalCost'];
 //    $course_data['token'] = $_POST['token'];
 //    $course_data['paymentType'] = $_POST['paymentType'];
 //    $course_data['children'] = $_POST['children'];
 //    $this->session->set_userdata($course_data);
 //    redirect('/paypal');
 // }

?>
<style>
    .direct_debit_1{
        background: #337ab7;
        height: 76px;
        font-size: 16px;
        color: #fff;
        padding-top: 25px;
    }
    .direct_deposit_1{
        background: #b3a2c7;
        height: 76px;
        font-size: 16px;
        color: #fff;
        padding-top: 25px;
    }

    .direct_debit_2{
        background: #337ab7;
        color: #fff; 
        height: 76px;
        margin-left: 2px;       
    }

    .direct_debit_2 p{
        color: #fff;       
    }
    .direct_debit_3{
        background: #337ab7;
        color: #fff; 
        height: 76px;
        margin-left: 2px;  
        padding-top: 25px;     
    }
    .direct_deposit_3{
        background: #b3a2c7;
        color: #fff; 
        height: 76px;
        margin-left: 2px;  
        padding-top: 25px;     
    }

    .no_direct_debit .direct_debit_1{
        background: #d99694;
        color: #fff; 
        height: 76px;
        padding-top: 25px;
    }
    .no_direct_debit .direct_debit_2{
        background: #d99694;
        color: #fff; 
        height: 76px;
        margin-left: 2px; 
    }
    .no_direct_debit .direct_debit_3{
        background: #d99694;
        color: #fff; 
        height: 76px;
        margin-left: 2px; 
        padding-top: 25px;
    }
    .direct_deposits .direct_debit_2{
        background: #b3a2c7;
        color: #fff; 
        height: 76px;
        margin-left: 2px;   
        padding-top: 25px;    
    }

    .hover_set .tooltiptext {
        visibility: hidden;
        width: 315px;
        background-color: #c8d9db;
        color: #100101;
        text-align: center;
        border-radius: 1px;
        padding: 7px 0;
        margin-top: -137px;
        margin-left: -119px;
        position: absolute;
        z-index: 1;

    }

    .hover_set:hover .tooltiptext {
        visibility: visible;
    }

</style>

<div class="container">
    <div class="row">
        <p class="alert alert-success" id="help_denied" style="margin: 0 28%;"  > 
            <b> Before you select the subject please watch the video help. </b>
        </p>
        <div class="col-sm-10 col-sm-offset-1">
            <h6 style="color: #053167;font-weight: 600;text-decoration: underline;text-align: center;padding-top: 15px;">Select Your Course</h6>
            <!--<div class="disabled_option_error text-danger" style="font-size: 18px;font-weight: bold;text-align: center;margin-top: 5px;"></div>-->
            <form class="ss_form text-center form-inline" method="post" action="">
                <div class="ss_top_s_course">
                    <ul style="display: flex;align-items: center;justify-content: center;">
                        <?php
                        if ($course_details) {
                            $i = 0;
                            foreach ($course_details as $course) {
                                $i++;
                                ?>
                                

                                <?php if($i == 6){?>
                                    <li class="text-left hover_set" style="min-height:99px;position: relative;background-color:#d73b3b;">

                                    <span class="tooltiptext">Note that the position of the tooltip text isn't very good. Go back to the tutorial and continue reading on how to position the tooltip in a desirable way.Note that the position of the tooltip text isn't very good. Go back to the tutorial and continue reading on how to position the tooltip in a desirable way.Note that the position of the tooltip text isn't very good. Go back to the tutorial and continue reading on how to position the tooltip in a desirable way.</span>

                                    <p style="line-height: 18px;">
                                        <?php echo strip_tags($course['courseName']); ?><br/> 
                                        
                                        <?php //if($course['courseCost']){?>
                                            <span>$</span>
                                                <?php if($subscription_type == 1){
                                                    echo $course['courseCost'];
                                                }else{
                                                    echo 0;
                                                } ?> 
                                        <?php //}?>

                                        
                                    </p>
                                    <p class="text-right filled-in" style="position: absolute;right: 10px;bottom: 10px;">
                                        <input class="form-check-input"  id="course_<?php echo $i; ?>"type="checkbox" name="course[]" value="<?php echo $course['id'] ?>" 
                                               data='<?php echo $course['courseCost'] ?>' onclick="courseClick('<?php echo $course['id'] ?>');" <?= (in_array($course['id'],$register_course))?'disabled':'';?>>
                                    </p>
                                </li>

                                <?php }else if($i == 7){?>
                                    <li class="text-left" style="min-height:99px;position: relative;background-color:#f58230">
                                    <p style="line-height: 18px;">
                                        <?php echo strip_tags($course['courseName']); ?><br/> 
                                        
                                        <?php //if($course['courseCost']){?>
                                            <span>$</span>
                                                <?php if($subscription_type == 1){
                                                    echo $course['courseCost'];
                                                }else{
                                                    echo 0;
                                                } ?> 
                                        <?php //}?>

                                        
                                    </p>
                                    <p class="text-right filled-in" style="position: absolute;right: 10px;bottom: 10px;">
                                        <input class="form-check-input"  id="course_<?php echo $i; ?>"type="checkbox" name="course[]" value="<?php echo $course['id'] ?>" 
                                               data='<?php echo $course['courseCost'] ?>' onclick="courseClick('<?php echo $course['id'] ?>');" <?= (in_array($course['id'],$register_course))?'disabled':'';?>>
                                    </p>
                                </li>

                                <?php }else{?>
                                    <li class="text-left" style="min-height:99px;position: relative;">
                                    <p style="line-height: 18px;">
                                        <?php echo strip_tags($course['courseName']); ?><br/> 
                                        
                                        <?php //if($course['courseCost']){?>
                                            <span>$</span>
                                                <?php if($subscription_type == 1){
                                                    echo $course['courseCost'];
                                                }else{
                                                    echo 0;
                                                } ?> 
                                        <?php //}?>

                                        
                                    </p>
                                    <p class="text-right filled-in" style="position: absolute;right: 10px;bottom: 10px;">
                                        <input class="form-check-input"  id="course_<?php echo $i; ?>"type="checkbox" name="course[]" value="<?php echo $course['id'] ?>" 
                                               data='<?php echo $course['courseCost'] ?>' onclick="courseClick('<?php echo $course['id'] ?>');" <?= (in_array($course['id'],$register_course))?'disabled':'';?>>
                                    </p>
                                </li>

                                <?php }?>
                                        

                            <?php }
                        } ?>
                    </ul>
                </div>

                <div class="ss_bottom_s_course">
                    <div class="form-group">
                        <label class="label-inline">Number of children</label>  

                        <input type="Number" id="children" class="form-control ss_number" name="children" value='1' onclick="getChildreen();" onkeyup="getChildreen();" readonly>
                    </div>
                    <?php if ($local_session->get('registrationType') != 'trial') { ?>
                        <?php if (!empty($refferalUser)) { ?>
                            <div class="select active r4" data="4" checked onclick="myR4Func();">3 Months</div>
                            <!--<div class="select active r2" data="2" checked onclick="myR2Func();">6 Months</div>-->
                            <div class="select r2" data="2" onclick="myR2Func();">6 Months</div>
                            <div class="select r3" data="3" onclick="myR3Func();">1Year</div>
                            
                        <?php }else{ ?>
                            <div class="select active r1" data="1" checked onclick="myR1Func();">Per month</div>
                            <div class="select  r4" data="4"  onclick="myR4Func();">3 Months</div>
                            <div class="select  r2" data="2"  onclick="myR2Func();">6 Months</div>
                            <div class="select r3" data="3" onclick="myR3Func();">1Year</div>
                        <?php } ?>

                        <div class="total">Total<br/><b id="dolar">$0</b></div>
                        <input type="hidden" name="paymentType" value="" id="paymentType" />
                        <input type="hidden" name="totalCost" value="" id="totalCost" />
                    <?php } ?>
                </div>
                   <br> 
                <?php if ($local_session->get('registrationType') != 'trial') { ?>
                <div class="text-center" style="padding: 15px 185px;"> 
                    <a href="<?php echo base_url()?>/signup" class="btn btn-primary" style="margin-right: 50px;">Choose Option</a>
                    <br>
                    <br>
                    <div class="row" style="margin-bottom: 5px;">
                        <div class="col-md-2 direct_debit_1">Option 1</div>
                        <div class="col-md-7 direct_debit_2">
                            <p style="font-weight: bold;">Direct Debit</p>
                            <p>Your membership will be renewed automatically. You may cancel anytime</p>
                        
                        </div>
                        <div class="col-md-2 direct_debit_3">
                            <input type="checkbox" class="ck_direct_debit payment_process" id="ck_direct_debit" name="direct_debit" value="1">
                        </div>
                    </div>
                    <div class="row no_direct_debit" style="margin-bottom: 5px;">
                        <div class="col-md-2 direct_debit_1">Option 2</div>
                        <div class="col-md-7 direct_debit_2">
                            <p style="font-weight: bold;">No direct debit</p>
                            <p>One time payment without no automatic renewel.</p>
                        </div>
                        <div class="col-md-2 direct_debit_3">
                            <input type="checkbox" class="ck_no_direct_debit payment_process" id="ck_no_direct_debit" name="no_direct_debit" value="2">
                        </div>
                    </div>
                    <?php if($direct_deposit_by_contry == 1) { ?>
                    <div class="row direct_deposits">
                        <div class="col-md-2 direct_deposit_1">Option 3</div>
                        <div class="col-md-7 direct_debit_2">
                            <p style="font-weight: bold;">Direct Deposit</p>
                        </div>
                        <div class="col-md-2 direct_deposit_3">
                            <input type="checkbox" class="ck_direct_deposit payment_process" id="ck_direct_deposit" name="direct_deposit" value="3">
                        </div>
                    </div>
                    <?php } ?>
                    <div class="payment_option_error text-danger" style="font-size: 18px;font-weight: bold;text-align: left;margin-top: 5px;"></div>
                </div>
                <?php } ?>
                <?php if ($local_session ->get('registrationType') != 'trial') { ?>
                    <!-- <p class="warnin_text">“Your membership will be renewed automatically. You may cencel anytime”</p> -->
                <?php } else {
                    echo '<br>';
                } ?>

                <input type="hidden" value="1" name="token">    
                <div class="text-center" > 
                    <button type="submit" class="btn btn_next" id="must_select" name="submit" value="submit"> 
                        <img src="<?php echo base_url(); ?>/assets/images/icon_save.png"/>Save & Proceed
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</section>
<?php echo $footer; ?>
<script>
$(document).ready(function(){
    courseClick();
})
<?php if ($local_session->get('registrationType') != 'trial') { ?>

    $('#must_select').click(function(){
        if ($('#ck_direct_debit').prop('checked')) {
            var result = "OK";
        }else if ($('#ck_no_direct_debit').prop('checked')) {
            var result = "OK";
        }else if ($('#ck_direct_deposit').prop('checked')) {
            var result = "OK";
        }else{
            var result = "NO";
        }

        if (result == "NO") {
            $('.payment_option_error').html('Please select the payment option first !!');
            // alert('Please select the payment option first !!')
            return false;
        }else{
            $('.payment_option_error').html('');
            return true;
        }
    })
    
<?php } ?>


<?php if (!empty($refferalUser)) { ?>
    myR4Func();
<?php }else{ ?>
    myR1Func();
<?php } ?>

<?php if ($local_session->get('registrationType') != 'trial') { ?>

        if (true) {}
        function myR1Func() {
            var davalue = $('.r1').attr('data');
            var Period = $("#paymentType").val();
            var totalCostWithPeriod = $("#totalCost").val();
            document.getElementById("paymentType").value = davalue;
            countTotal(Period,totalCostWithPeriod,1);
            //courseClick();
        }
        function myR2Func() {
            var davalue2 = $('.r2').attr('data');
            var Period = $("#paymentType").val();
            var totalCostWithPeriod = $("#totalCost").val();
            document.getElementById("paymentType").value = davalue2;
            countTotal(Period,totalCostWithPeriod,6);
            
        }
        function myR3Func() {
            var davalue3 = $('.r3').attr('data');
            var Period = $("#paymentType").val();
            var totalCostWithPeriod = $("#totalCost").val();
            document.getElementById("paymentType").value = davalue3;
            countTotal(Period,totalCostWithPeriod,12);
        }
        function myR4Func() {
            var davalue4 = $('.r4').attr('data');
            var Period = $("#paymentType").val();
            var totalCostWithPeriod = $("#totalCost").val();
            document.getElementById("paymentType").value = davalue4;
            countTotal(Period,totalCostWithPeriod,3);
        }
        function countTotal(Period,totalCostWithPeriod,select) {
            var amountTotal = 0 ;
            if (Period == 1)
            {
                amountTotal = totalCostWithPeriod/1;
                amountTotal = amountTotal*select;

            }else if (Period == 2)
            {
                amountTotal = totalCostWithPeriod/6;
                amountTotal = amountTotal*select;
            }else if (Period == 3)
            {
                amountTotal = totalCostWithPeriod/12;
                amountTotal = amountTotal*select;
            }else if (Period == 4)
            {
                amountTotal = totalCostWithPeriod/3;
                amountTotal = amountTotal*select;
            }
            $('#dolar').html('$' + amountTotal);
            document.getElementById("totalCost").value = amountTotal;
        }

        $('.ck_direct_debit').change(function(){
            if ($('#ck_direct_debit').prop('checked')) {
                $('#ck_no_direct_debit').prop('checked',false);
                $('#ck_direct_deposit').prop('checked',false);
                
            }
        })
        $('.ck_no_direct_debit').change(function(){
            if ($('#ck_no_direct_debit').prop('checked')) {
                $('#ck_direct_debit').prop('checked',false);
                $('#ck_direct_deposit').prop('checked',false);
                
            }
        })
        $('.ck_direct_deposit').change(function(){
            if ($('#ck_direct_deposit').prop('checked')) {
                $('#ck_direct_debit').prop('checked',false);
                $('#ck_no_direct_debit').prop('checked',false);
            }
        })


<?php } ?>
    var courseNumber = document.getElementsByName('course[]');
    var amit = 0;
    for (i = 1; i <= courseNumber.length; i++) {
        if ($("#course_" + i).is(":checked")) {
            amit++;
        }
    }
    
    if (amit == 0) {
        $("#must_select").attr('disabled', true);
    } else {
        $("#must_select").attr('disabled', false);
    }

    function getChildreen() {
        var noOfChildreen = $('#children').val();
        if (noOfChildreen < 1) {
            document.getElementById("children").value = 1;
        }
        courseClick();
    }

    function courseClick() {
        
        
        var courseNumber = document.getElementsByName('course[]');
        var checkCourseNum = 0;
        for (i = 1; i <= courseNumber.length; i++) {
            if ($("#course_" + i).is(":checked")) {
                checkCourseNum++;
            }
        }
        
        var disabled = 0;
        var disable_st_tutor = 0;
        var three_course_disable = 0;
       
        for (d = 1; d <= courseNumber.length; d++) {
            if ($("#course_" + d).is(":disabled")) {
                var course_val = $("#course_" + d).attr('value');
                if (course_val == 44) {
                   disabled = 1;
                }
                if (course_val != 44) {
                   disable_st_tutor = 1;
                }
                
                if(course_val == 21 || course_val == 22 || course_val == 55){
                    three_course_disable = 1;
                }
            }
        }
        
        console.log(three_course_disable);
        if(disable_st_tutor == 1){
            for ( st_tu = 1;  st_tu <= courseNumber.length; st_tu++) {

                var course_cost = $("#course_" + st_tu).attr('data');
                var course_val = $("#course_" + st_tu).attr('value');
                if (course_val == 44) {
                    $("#course_" + st_tu).prop('checked',false);
                }
            }
        }
        
        if(disabled == 1){
            for ( dd = 1;  dd <= courseNumber.length; dd++) {

                var course_cost = $("#course_" + dd).attr('data');
                var course_val = $("#course_" + dd).attr('value');
                if (course_val != 44) {
                    $("#course_" + dd).prop('checked',false);
                }
            }
            $('.disabled_option_error').html(' "Tutor-Student Collaboration (AUS) Any Grade" course is running');
            return;
        }else{
            $('.disabled_option_error').html('');
        }
        
        
        
        if(checkCourseNum == 0 && disabled == 0 && disable_st_tutor == 0){
            $("#course_1").prop('checked',true);
        }
        
        var j = 0;
        var total_cost = 0;
        var is_st_colaburation = 0;
        var is_three_courses = 0;
        for (i = 1; i <= courseNumber.length; i++) {
            if ($("#course_" + i).is(":checked")) {
              
                var course_cost = $("#course_" + i).attr('data');
                var course_val = $("#course_" + i).attr('value');
                if (course_val == 44) {
                    is_st_colaburation = 1;
                }
                
                
                if (course_val == 21) {
                  $("#course_3").attr('disabled',true);
                  $("#course_5").attr('disabled',true);
                  var is_three_courses = 1;
                  three_course_disable = 0;
                }else if (course_val == 22) {
                  $("#course_2").attr('disabled',true);
                  $("#course_5").attr('disabled',true);
                  var is_three_courses = 1;
                  three_course_disable = 0;
                }else if (course_val == 55) {
                  $("#course_2").attr('disabled',true);
                  $("#course_3").attr('disabled',true);
                  var is_three_courses = 1;
                  three_course_disable = 0;
                }else if (course_val == 62) {
                  $("#course_7").attr('disabled',true);
                  var is_three_courses = 1;
                  three_course_disable = 0;
                }else if (course_val == 63) {
                  $("#course_6").attr('disabled',true);
                  var is_three_courses = 1;
                  three_course_disable = 0;
                }else{
                  var is_three_courses = 0;
                  three_course_disable = 0;
                }
                var total_cost = parseInt(total_cost) + parseInt(course_cost);
                j++;
            }
        }
        

        if (is_st_colaburation == 1) {
            for ( k = 1;  k <= courseNumber.length; k++) {

                var course_cost = $("#course_" + k).attr('data');
                var course_val = $("#course_" + k).attr('value');
                if (course_val != 44) {
                    $("#course_" + k).prop('checked',false);
                }else{
                    console.log(course_cost);
                    total_cost = parseInt(course_cost);
                }
            }

        }
        
        if(is_three_courses == 0 ){
          $("#course_2").attr('disabled',false);
          $("#course_3").attr('disabled',false);
          $("#course_5").attr('disabled',false);
        }
        
        if(three_course_disable == 1){
          $("#course_2").attr('disabled',true);
          $("#course_3").attr('disabled',true);
          $("#course_5").attr('disabled',true);
        }
        
        

        var children = $('#children').val();
        var total_amount = total_cost * children;

        if (j == 0) {
            $("#must_select").attr('disabled', true);
        } else {
            $("#must_select").attr('disabled', false);
        }

<?php if ($local_session->get('registrationType') != 'trial') { ?>
        var Period = $("#paymentType").val();
        if (Period == 1)
        {
            total_amount = total_amount*1;

        }else if (Period == 2)
        {
            total_amount = total_amount*6;
        }else if (Period == 3)
        {
            total_amount = total_amount*12;
        }else if (Period == 4)
        {
            total_amount = total_amount*3;
        }
            $('#dolar').html('$' + total_amount);
            document.getElementById("totalCost").value = total_amount;
<?php } ?>
      
        
       for (s = 1; s <= courseNumber.length; s++) {
            if ($("#course_" + s).is(":checked")) {
                var course_value = $("#course_" + s).attr('value');
                if(course_value == 62){
                    // alert(s);
                }
               
            }
             if(!$("#course_" + s).is(":checked")){
                var course_value = $("#course_" + s).attr('value');
                if(course_value == 62){
                    $("#course_7").attr('disabled',false);
                }
                if(course_value == 63){
                    $("#course_6").attr('disabled',false);
                }
                
            }
        }
    }
</script>
<script>
    $(document).ready(function(){
        $('#help_denied').fadeOut(15000);
    })
</script>

<?= $this->endSection() ?>