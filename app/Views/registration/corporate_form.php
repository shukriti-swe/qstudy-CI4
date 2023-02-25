<?= $this->extend('registration/master'); ?>
<?= $this->section('content');?>

<div class="container ss_reg_form">
          <div class="row">
           <div class="col-sm-6 col-sm-offset-3">
            <?php 
            $this->session=\Config\Services::session();
            if ($this->session->get('token_error')) {
                echo $this->session->get('token_error');
                $this->session->remove('token_error');
            }?>
            <form class="row" id="corporate_form">
              <p id='form_error' style="color:red"></p>
              <div class="col-sm-6">
              <h3 class="g_heading">All fields are mandatory</h3>
              <div class="form-group">
                <label for="corporate_name">Corporate Name </label>
                <input class="form-control" type="text" id="corporate_name" name="corporate_name" />
              <p id="error_corporate" style="color:red"></p>
              </div>
              <div class="form-group">
                <label for="email">Email Adress </label>
                <input class="form-control" type="email" id="email" name="email"/>
                <span class="ss_info">Your email adress also your username</span>
              <p id="error_email" style="color:red"></p>
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input class="form-control" type="password" id="password" name="password"/>
              <p id="error_pass" style="color:red"></p>
              </div>
              <div class="form-group">
                <label for="cnfpassword">Confirm Password</label>
                <input class="form-control" type="password" id="cnfpassword" name="cnfpassword"/>
              <p id="error_cnfpass" style="color:red"></p>
              </div>

              <div class="form-group">
                  <label >Phone No </label>
                  <input class="form-control" type="text"  name="phone" id="phone" />
                  <p id="error_phone" style="color:red"></p>
              </div>

              <div class="form-group">
                  <label for=""> Mobile No</label>
                  <input type="hidden" id="full_number" name="full_number">
                  <input class="form-control" type="tel" id="mobile" name="mobile">
                  <p id="error_mobile" style="color:red;font-weight: bold;"></p>
              </div>

              <div class="form-group">
                  <label for="cnfpassword">Website </label>
                  <input class="form-control" type="text"  name="website" id="website" />
                  <p id="error_website" style="color:red"></p>
              </div>

              <div class="form-group">
              <label>Country: </label>
              <input class="form-control" type="text" id="country" value="<?php echo $country_db[0]['countryName'];?>" name="" readonly />
              <input type="hidden" id="country_code" />
              </div>
              </div>
              <div class="col-sm-6">
              <h3 class="g_heading">  </h3>
                <?php for ($x = 1; $x <= $teacher_number; $x++) { ?> 
                <div class="form-group">
                  <label >Teacher ‘s Name</label>
                  <input class="form-control" type="text" id="teacher_<?php echo $x;?>" name="teacher[]" />
                  <p id="error_<?php echo $x;?>" style="color:red"></p>
                </div>
                
                <?php } ?>
              
              
              </div>
              <div class="text-left col-md-12">
                <br/> 
                <button class="btn btn_next" id="btnSave">
                  <img src="<?php echo base_url();?>/assets/images/icon_save.png"/>Submit
                </button>
              </div>
            </form>
            
          <br/><br/>
          </div>
         </div>
         </div>
    </div>
</section>

<div id="ss_confirm_mobile"  class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form  method="post" id="corporate_otp_modal">
      
      <div class="modal-body">
        
        <p>
          <img src="<?php echo base_url();?>/assets/images/icon_logo_small.png">
        </p>
        <p>
          Congratulations! your registration almost done. Enter the verification code that has been send to your mobile number and press submit. lf the mobile number is not valid or you have not received any code, press cancel and Re-enter your mobile number and submit.s
        </p>
        <br/>
        <p id="token_error" style="color:red"></p>
        <div class="form-group">
          <label>Enter code</label>
          <input class="form-control" type="text" id="" name="random"/>
        </div>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn_blue" data-dismiss="modal">Cancel</button>       
        <button class="btn btn_blue" type="button" id="corporate_otp_check">
        Submit
      </button>
        </div>
      </form>
    </div>
  </div>
</div>

 <script>
  var iti='';
  $(window).ready(function() {
   
    <?php if ($country_db[0]['countryCode'] != 'any') { ?>
      var input = document.querySelector("#mobile");

      iti = intlTelInput(input, {
        utilsScript: "<?php echo base_url();?>/assets/js/utils.js?1537727621611",
        initialCountry:'<?php echo $country_db[0]['countryCode'];?>',
        autoHideDialCode: true,
        autoPlaceholder: "off",
        //defaultCountry: "auto",
        numberType: "MOBILE",
        separateDialCode:true,
      });
    <?php } else { ?>
      $("#mobile").intlTelInput({
        autoHideDialCode: false,
        autoPlaceholder: false,
        defaultCountry: "auto",
        numberType: "MOBILE",
        separateDialCode:true,
      });
    <?php } ?>

  });
  
 $('#btnSave').click(function(e){
 $("#full_number").val(iti.getNumber());  
   var teachers = document.getElementsByName('teacher[]');
   var j=0;
   for (i=1; i<=teachers.length; i++){
    if($("#teacher_"+i).val()){
      j=0;
      $('#error_'+i).html('');
    }else{
      $('#error_'+i).html('Teacher name can not be blank');
      j=1;
      e.preventDefault();
    }
  }
  var corporate_name=$('#corporate_name').val();
  var k=0;
  if(corporate_name == ''){
    k=1;
    $('#error_corporate').html('Corporate name can not be empty');
  e.preventDefault();
  }else{
    k=0;
    $('#error_corporate').html('');
  }
  var email=$('#email').val();
  var l=0;
  var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  if (re.test(email)) {
    l=0;
    $('#error_email').html('');
  } else {
    l=1;
    $('#error_email').html('please enter a valid email');
    e.preventDefault();
  }
  var m=0;
  var password=$('#password').val();
  var cnfpassword=$('#cnfpassword').val();  
  if(password.length < 6){
    m=1;
    $('#error_pass').html('password can not blank and minimum length 6 character');
  e.preventDefault();
  }else{
    m=0;
    $('#error_pass').html('');
  }
  
  var n=0;
  
  if(password != cnfpassword){
    n=1;
    $('#error_cnfpass').html('password and confirm password must be same');
    e.preventDefault();
  }else{    
    n=0;
    $('#error_cnfpass').html('');
  }
  var mobile_no=$('#mobile').val();
  var o=0;
  if(mobile_no == ''){
    o=1;
    $('#error_mobile').html('mobile can not be empty');
    e.preventDefault();
  }else{
    o=0;
    $('#error_mobile').html('');
  }

  var phone =$('#phone').val();
  var p=0;
  if(phone  == ''){
      p=1;
      $('#error_phone').html('Phone can not be empty');
      e.preventDefault();
  }else{
      p=0;
      $('#error_phone').html('');
  }

  var website =$('#website').val();
  var q=0;
  if(website  == ''){
      q=1;
      $('#error_website').html('Website can not be empty');
      e.preventDefault();
  }else{
      q=0;
      $('#error_website').html('');
  }


  if(j==0 && k==0 && l==0 && m==0 && n==0 && o==0 && p == 0 && q == 0 ){ 
    var data=$('#corporate_form').serialize();
    $.ajax({
      type: 'ajax',
      method: 'post',
      async: false,
      dataType:'json',
      url: 'save_corporate',
      data:data,
      success: function(msg){
        if(msg=='mobile_number_error'){
                $('#error_mobile').html('Mobile number already exists');
                return false;
          }else{
            $('#error_mobile').html('');
          }
        if(msg=='success'){ 
          $('#token_error').html('');
          $('#ss_confirm_mobile').modal('show');
          e.preventDefault();
          // window.location.href = "sure_corporate_data_save";
        }else{
          $('#form_error').html(msg);
          e.preventDefault();                 
        } 
      }
    });   
    e.preventDefault();
  }
 });
 $('#corporate_otp_check').click(function(){
   var data_up_modal=$('#corporate_otp_modal').serialize();
     var pathname = '<?php echo base_url(); ?>';
  $.ajax({
      type: 'ajax',
      method: 'post',
      async: false,
      dataType:'html',
      url: 'sure_corporate_data_save',
      data:data_up_modal,
      success: function(msg){
        if(msg==1){
            window.location.href = pathname+"/paypal";
        }
		if(msg==2){
          window.location.href = pathname+"/corporate_mail";
        }else{
          $('#token_error').html('please enter your valid token');
        }
      }
    });
  });
 </script>


<?= $this->endSection() ?>