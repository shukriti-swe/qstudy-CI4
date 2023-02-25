<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8" />
    <title>Recover Password | Upzet - Admin & Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesdesign" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Bootstrap Css -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- App Css-->
    <link href="<?php echo base_url(); ?>/admin/assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />

</head>

<body class="bg-pattern">
    <div class="bg-overlay"></div>
    <div class="account-pages my-5 pt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-4 col-lg-6 col-md-8">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="">
                                <div class="text-center">
                                    <a href="index.html" class="">
                                        <img src="assets/images/logo-dark.png" alt="" height="24" class="auth-logo logo-dark mx-auto">
                                        <img src="assets/images/logo-light.png" alt="" height="24" class="auth-logo logo-light mx-auto">
                                    </a>
                                </div>

                                <!-- end row -->
                                <h4 class="font-size-18 text-muted mt-2 text-center">Password Reset Form</h4>
                                <p class="mb-5 text-center">Reset your Password with Upzet.</p>
                                <?php 
                                if(!isset($reset_chk)){
                                ?>
                                    <p style="color:red;display:none;" id="code_error">Please enter valid code</p>

                                    <div id="code_section">    
                                        <div class="mt-4">
                                                <label class="form-label" for="useremail">Enter Reset Code</label>
                                                <input type="number" class="form-control" placeholder="Enter Code" required id="get_code">
                                                <div class="valid-feedback">Looks good!</div>
                                                <small style="color:red;" class="text-danger"><?php if (isset($validation)) {
                                                    echo $validation->getError('user_pass');
                                                } ?>
                                            </small>
                                        </div>
                                        <div class="d-grid mt-4">
                                            <button type="button" class="btn btn-primary waves-effect waves-light" id="code_submit">Submit</button>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php
                                    if (isset($success)) {
                                        echo $success;
                                    }
                                    ?>

                                    <?php if (isset($success)) { ?>
                                        <p class="text-white-50">Password Reset Successfully!
                                            <a href="<?php echo base_url() ?>/" class="fw-medium text-primary">
                                                <button type="button" class="btn btn-success w-100">Login</button>
                                            </a>
                                        </p>
                                    <?php } ?>

                                <form class="form-horizontal" action="<?php echo base_url() ?>/reset_pass/<?= $token; ?>" method="post" class="needs-validation" novalidate id="pass_section" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-12">

                                            <?php if (empty($success) && empty($error)) { ?>
                                            <div class="mt-4">
                                                    <label class="form-label" for="useremail">Password</label>
                                                    <input type="password" name="user_pass" class="form-control" placeholder="Enter Your Password" required>
                                                    <div class="valid-feedback">Looks good!</div>
                                                    <small style="color:red;" class="text-danger"><?php if (isset($validation)) {
                                                        echo $validation->getError('user_pass');
                                                    } ?>
                                                </small>
                                            </div>
                                            <div class="mt-4">
                                                <label class="form-label" for="useremail">Confirm Password</label>
                                                <input type="password" name="re_pass" class="form-control" placeholder="Enter Your Confirm Password" required>
                                                <div class="valid-feedback">Looks good!</div>
                                                <small style="color:red;" class="text-danger"><?php if (isset($validation)) {
                                                    echo $validation->getError('re_pass');
                                                } ?>
                                            </small>
                                            </div>
                                        <div class="d-grid mt-4">
                                            <button type="submit" class="btn btn-primary waves-effect waves-light">Reset Password</button>
                                        </div>
                                    <?php } ?>

                                    


                                    <?php
                                    if (isset($error)) {
                                        echo $error;
                                    }
                                    ?>

                                    <?php if (isset($error)) { ?>
                                        <div class="mt-4">
                                            <label class="form-label" for="useremail"><?php echo lang('Login.f_password'); ?></label>
                                            <input type="password" name="user_pass" class="form-control" placeholder="Enter Your Password" required>
                                            <div class="valid-feedback">Looks good!</div>
                                            <small style="color:red;" class="text-danger"><?php if (isset($validation)) {
                                                echo $validation->getError('user_pass');
                                            } ?>
                                        </small>
                                    </div>
                                    <div class="mt-4">
                                        <label class="form-label" for="useremail"><?php echo lang('Login.f_con_pass'); ?></label>
                                        <input type="password" name="re_pass" class="form-control" placeholder="Enter Your Confirm Password" required>
                                        <div class="valid-feedback">Looks good!</div>
                                        <small style="color:red;" class="text-danger"><?php if (isset($validation)) {
                                            echo $validation->getError('re_pass');
                                        } ?>
                                    </small>
                                </div>
                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light"><?php echo lang('Login.reset'); ?></button>
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
<!-- end row -->
</div>
</div>
<!-- end Account pages -->

<!-- JAVASCRIPT -->
<script src="<?php echo base_url(); ?>/admin/assets/libs/jquery/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>/admin/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url(); ?>/admin/assets/libs/metismenu/metisMenu.min.js"></script>
<script src="<?php echo base_url(); ?>/admin/assets/libs/simplebar/simplebar.min.js"></script>
<script src="<?php echo base_url(); ?>/admin/assets/libs/node-waves/waves.min.js"></script>
<script src="<?php echo base_url() ?>/admin/assets/js/pages/form-validation.init.js"></script>

<script src="<?php echo base_url(); ?>/admin/assets/js/app.js"></script>

</body>

<script>
    // $('#pass_section').hide();
    // $('#code_error').hide();
    $(document).ready(function(){
      $('#code_submit').click(function(){
        var get_code = $('#get_code').val();
        var user_code = <?=$pass_reset_code?>;
        // alert(user_code);
        if(get_code !=''){
            if(get_code ==user_code){
                $('#pass_section').show();
                $('#code_section').hide();
                $('#code_error').hide();
            }else{
                $('#code_error').show();
            }
        }else{
            alert('Please Write Code');
        }      });
    });
</script>

</html>