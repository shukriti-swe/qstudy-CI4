<?= $this->extend('qstudy/master_dashboard'); ?>
<?= $this->section('content');?>
<div class="container">
                <div class="row">
                    <div class="sign_my_acount">
                        <div class="col-md-4 col-md-offset-4">
                            <p class="accordion_new">
                                <a class="btn btn-primary" data-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1"><i class="fa fa-sort-down"></i> My Account</a>
                            </p>
                            <div class="">
                                <div class="col">
                                    <div class="collapse multi-collapse accordion_body in" id="multiCollapseExample1">
                                        <div class="card card-body">
                                            <ul>
                                                <li><a href="<?php echo base_url();?>/my_details">Setting</a></li>
                                                <li><a href="<?php echo base_url();?>/upload_photo">Logo/Photo</a></li>
                                                <li><a href="<?php echo base_url();?>/cancel_subscription">Cancel Subscription</a></li>
                                                <!----<li><a href="my_enrollment_list.php">Enrollment List</a></li>---->
                                            </ul>   
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <script src="<?php echo base_url();?>/assets/js/cancel_subscription.js"></script>

<?= $this->endSection() ?>