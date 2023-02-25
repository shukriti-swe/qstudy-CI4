<?= $this->extend('registration/master'); ?>
<?= $this->section('content');?>
<div class="container">
    <div class="row">
        
        <div class="col-sm-10 col-sm-offset-1">

            <form class="ss_form text-center form-inline" method="post" action="<?php echo base_url(); ?>/school_form" id="school_form" >
                <div class="ss_top_s_course">
                    <ul>

                        <?php
                         $local_session = \Config\Services::session();
                       
                        if ($course_details) {
                            $i = 0;
                            foreach ($course_details as $course) {
                                $i++;
                                ?>
                                <li class="text-left">
                                    <p style="line-height: 18px;">
                                        <?php echo strip_tags($course['courseName']); ?><br/> 

                                        <?php //if ($course['courseCost']) {?>
                                            <span>$</span>
                                            <?php if($subscription_type == 1){
                                                echo $course['courseCost'];
                                            }else{
                                                echo 0;
                                            } ?> 
                                        <?php //}?>

                                        (per user)
                                    </p>
                                    <p class="text-right filled-in">
                                        <input class="form-check-input"  id="course_<?php echo $i; ?>"type="checkbox" name="course[]" value="<?php echo $course['id'] ?>" 
                                        data='<?php echo $course['courseCost'] ?>' onclick="courseClick('<?php echo $course['id'] ?>');">
                                    </p>
                                </li>
                            <?php }
                        } ?>
                    </ul>
                </div>

                <div class="ss_bottom_s_course">  

                    <?php   if ($local_session->get('userType') == 4 ) { ?>
                        
                        <div class="form-group">

                            <input type="hidden" id="children" class="form-control ss_number" name="teacher" value='1' onclick="getChildreen();" onkeyup="getChildreen();">
                        </div>

<script type="text/javascript">
    $("form").submit();
</script>
  <?php }  ?>

                    <?php if ($local_session->get('registrationType') != 'trial') { ?>
                        <!-- <div class="select active r1" checked data="1" onclick="myR1Func();">Per month</div>
                        <div class="select r2" data="2" onclick="myR2Func();">6 Months</div>
                        <div class="select r3" data="3" onclick="myR3Func();">1Year</div>

                        <div class="total">Total<br/><b id="dolar">$0</b></div>
                        <input type="hidden" name="paymentType" value="" id="paymentType" />
                        <input type="hidden" name="totalCost" value="" id="totalCost" /> -->
                    <?php } ?>
                </div>
                <?php if ($local_session->get('registrationType') != 'trial') { ?>
                    <p class="warnin_text"></p>
                <?php } else {
                    echo '<br>';
                } ?>

                <input type="hidden" value="1" name="token">    
                <div class="text-center" > 
                </div>
            </form>

        </div>
    </div>
</div>
</div>
</section>

<script>
    $("#must_select").attr('disabled', false);

    <?php if ($local_session->get('registrationType') != 'trial') { ?>
        function myR1Func() {
            var davalue = $('.r1').attr('data');
            document.getElementById("paymentType").value = davalue;
        }
        myR1Func();
        function myR2Func() {
            var davalue2 = $('.r2').attr('data');
            document.getElementById("paymentType").value = davalue2;
        }
        function myR3Func() {
            var davalue3 = $('.r3').attr('data');
            document.getElementById("paymentType").value = davalue3;
        }
    <?php } ?>
    var courseNumber = document.getElementsByName('course[]');
    var amit = 0;
    // for (i = 1; i <= courseNumber.length; i++) {
        // if ($("#course_" + i).is(":checked")) {
            // amit++;
        // }
    // }
    // if (amit == 0) {
        // $("#must_select").attr('disabled', true);
    // } else {
        // $("#must_select").attr('disabled', false);
    // }

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
        for (i = 1; i <= courseNumber.length; i++) {
            if ($("#course_" + i).is(":checked")) {
                var course_cost = $("#course_" + i).attr('data');
                var total_cost = parseInt(total_cost) + parseInt(course_cost);
                j++;
            }
        }
        var children = $('#children').val();
        var total_amount = total_cost * children;
        // if (j == 0) {
            // $("#must_select").attr('disabled', true);
        // } else {
            // $("#must_select").attr('disabled', false);
        // }
        <?php if ($local_session->get('registrationType') != 'trial') { ?>
            $('#dolar').html('$' + total_amount);
            document.getElementById("totalCost").value = total_amount;
        <?php } ?>
    }
</script>

<script type="text/javascript">
    document.getElementById('school_form').submit();
</script>


<?= $this->endSection() ?>