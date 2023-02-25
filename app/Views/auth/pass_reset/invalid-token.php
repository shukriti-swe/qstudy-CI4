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
    <link href="<?php echo base_url(); ?>/admin/assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?php echo base_url(); ?>/admin/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
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
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php
                                        if (isset($invalidToken)) {
                                            echo $invalidToken;
                                        }
                                        ?>
                                        <p class="text-black"><?php echo lang('Login.resend_link'); ?>
                                            <a href="<?php echo base_url() ?>/forgot_pass" class="fw-medium text-primary"> Resend Link </a>
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        <p class="text-white-50">© <script>
                                document.write(new Date().getFullYear())
                            </script> <?php echo lang('Login.signby'); ?> <i class="mdi mdi-heart text-danger"></i> <?php echo lang('Login.sign_with'); ?></p>
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

</html>