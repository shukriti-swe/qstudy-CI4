<?= $this->extend('registration/master'); ?>
<?= $this->section('content');?>

<?php
//echo $this->session->userdata('registrationType');die();
 // if (isset($_POST['submit']) && $_POST['submit'] = 'submit')
 // {
 //    echo "abc";die();
    
 //    if ($this->session->userdata('registrationType') == 'trial') {
 //        redirect('/signup/tutor');
 //    }else{
 //        redirect('/paypal');
 //    }
 // }
 $local_session = \Config\Services::session();
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
    .checkbox_place{
        position: absolute;
        right: 10px;
        bottom: 10px;
    }
</style>
<div class="container">
    <div class="row">
        <p class="alert alert-success" id="help_denied" style="margin: 0 28%;"  > 
            <b> Before you select the subject please watch the video help. </b>
        </p>
        <div class="col-sm-10 col-sm-offset-1">
            <form class="ss_form text-center form-inline" method="post" action="">
                <!-- cheange tutor_form by show_paypal_form -->
                <div class="ss_top_s_course">
                    <ul>    
                        <?php
                        if ($course_details) {
                            $i = 0;
                            foreach ($course_details as $course) {
                                $i++;
                                ?>
                                <li class="text-left" style="position: relative;">
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
                                    <p class="text-right filled-in checkbox_place">
                                        <input class="form-check-input"  id="course_<?php echo $i; ?>"type="checkbox" name="course[]" value="<?php echo $course['id'] ?>" 
                                               data='<?php echo $course['courseCost'] ?>' onclick="courseClick('<?php echo $course['id'] ?>');" <?=  ($course['id']==42)?'checked':'';?>  <?= (in_array($course['id'],$register_course))?'disabled':'';?> >
                                    </p>
                                </li>
                            <?php }
                        } ?>
                        <!-- <li class="text-left">
                            <p>Fee</p>
                            <p><span>$</span><?php echo $course_details[0]['courseCost']; ?></p>
                            <p class="text-right filled-in">
                                <input class="form-check-input" type="hidden" name="fee" value="<?php echo $course_details[0]['courseCost']; ?>" data='' ></p>
                        </li>   -->                             
                    </ul>
                </div>
                <div class="ss_bottom_s_course">
                    <input type="hidden" id="children"  name="children" value='1' onclick="getChildreen();" onkeyup="getChildreen();">
                    <?php if ($local_session->get('registrationType') != 'trial') { ?>
                        <div class="select active r1" checked data="1" onclick="myR1Func();">Per month</div>
                        <div class="select  r4" data="4"  onclick="myR4Func();">3 Months</div>
                        <div class="select r2" data="2" onclick="myR2Func();">6 Months</div>
                        <div class="select r3" data="3" onclick="myR3Func();">1Year</div>

                        <div class="total">Total<br/><b id="dolar">$</b></div>
                        <input type="hidden" name="paymentType" value="" id="paymentType" />
                        <input type="hidden" name="totalCost" value="" id="totalCost" />
                    <?php } ?>
                </div>
                <br>
                    <?php if ($local_session->get('registrationType') != 'trial') { ?>
                <div class="text-center" style="padding: 15px 185px;margin-left:30px"> 
                    <a href="<?php echo base_url()?>/signup" class="btn btn-primary" style="margin-right: 50px;">Choose Option</a>
                    <br>
                    <br>
                    <div class="row" style="margin-bottom: 5px;">
                        <div class="col-md-2 direct_debit_1">Option 1</div>
                        <div class="col-md-7 direct_debit_2">
                            <p>Direct Debit</p>
                            <p>Your membership will be renewed automatically. You may cancel anytime</p>
                        
                        </div>
                        <div class="col-md-2 direct_debit_3">
                            <input type="checkbox" class="ck_direct_debit payment_process" id="ck_direct_debit" name="direct_debit" value="1">
                        </div>
                    </div>
                    <div class="row no_direct_debit" style="margin-bottom: 5px;">
                        <div class="col-md-2 direct_debit_1">Option 2</div>
                        <div class="col-md-7 direct_debit_2">
                            <p>No direct debit</p>
                            <p>One time payment without no automatic renewel.</p>
                        </div>
                        <div class="col-md-2 direct_debit_3">
                            <input type="checkbox" class="ck_no_direct_debit payment_process" id="ck_no_direct_debit" name="no_direct_debit" value="2">
                        </div>
                    </div>
                    <div class="row direct_deposits">
                        <div class="col-md-2 direct_deposit_1">Option 3</div>
                        <div class="col-md-7 direct_debit_2">
                            <p>Direct Deposit</p>
                        </div>
                        <div class="col-md-2 direct_deposit_3">
                            <input type="checkbox" class="ck_direct_deposit payment_process" id="ck_direct_deposit" name="direct_deposit" value="3">
                        </div>
                    </div>
                    <div class="payment_option_error text-danger" style="font-size: 18px;font-weight: bold;text-align: left;margin-top: 5px;"></div>
                </div>
                    <?php } ?> 
                
                <?php if ($local_session->get('registrationType') != 'trial') { ?>
                    <!-- <p class="warnin_text">“Your membership will be renewed automatically. You may cencel anytime”</p> -->
                <?php } else {
                    echo '<br>';
                } ?>

                <input type="hidden" value="1" name="token">    
                <div class="text-center" > 
                    <button type="submit" class="btn btn_next" id="must_select" name="submit" value="submit"> 
                        <img src="<?php echo base_url(); ?>/assets/images/icon_save.png"/>Save & Proceeds
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
<?php if ($local_session->get('registrationType') != 'trial') { ?>

        function myR1Func() {
            // var davalue = $('.r1').attr('data');
            // var Period = $("#paymentType").val();
            // var totalCostWithPeriod = "<?= $course_details[0]['courseCost']; ?>";
            // document.getElementById("paymentType").value = davalue;
            // countTotal(1,totalCostWithPeriod,1);
            
            var davalue = $('.r1').attr('data');
            var Period = $("#paymentType").val();
            var totalCostWithPeriod = $("#totalCost").val();
            document.getElementById("paymentType").value = davalue;
            countTotal(Period,totalCostWithPeriod,1);
        }
        myR1Func();
        courseClick();
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
        var j = 0;
        var total_cost = 0;
        var ss_disabled = 0;
        
        
        for (ss = 1; ss <= courseNumber.length; ss++) {
            if ($("#course_" + ss).is(":disabled")) {
                ss_disabled = ss;
                $("#course_" + ss).prop('checked',false);
            }
        }
        // if(ss_disabled > 0){
        //     $("#course_" + ss_disabled).prop('checked',false);
        // }
        
        for (i = 1; i <= courseNumber.length; i++) {
            if ($("#course_" + i).is(":checked")) {
                var course_cost = $("#course_" + i).attr('data');
                var total_cost = parseInt(total_cost) + parseInt(course_cost);
                j++;
            }
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

    }

</script>
<script>
    $(document).ready(function(){
        $('#help_denied').fadeOut(15000);
    })
</script>

<?= $this->endSection() ?>