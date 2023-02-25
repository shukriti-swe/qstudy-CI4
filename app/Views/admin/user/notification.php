<?= $this->extend('qstudy/master_dashboard'); ?>
<?= $this->section('content'); ?>

<div class="" style="margin-left: 15px;">
  	<div class="row">
	    <div class="col-md-4">
          <?php require_once(APPPATH.'Views/qstudy/leftnav.php');?>
	    </div>
		<div class="col-md-8 user_list">
	      <div class="panel-group " id="task_accordion" role="tablist" aria-multiselectable="true">
	        <div class="panel panel-default">
	          <div class="panel-heading" role="tab" id="headingOne">
	            <h4 class="panel-title text-center">
	              <a role="button" data-toggle="collapse" data-parent="#task_accordion" href="#collapseOnetask" aria-expanded="true" aria-controls="collapseOne"> 
	                <strong style="border: 2px solid;padding: 4px;font-size : 16px;
    border-radius: 6%;"><span><?php echo $date ?></span></strong>
	                <strong style="border: 2px solid;padding: 4px;font-size : 16px;border-radius: 6%;"><span>Notification</span></strong>
	              </a>
	            </h4>
	          </div>
	      	</div>
	      </div>
	      <div class="section full_section">
	      	<div class="form-group row">
	      		<div class="col-md-4">
	      			<table class="table table-bordered">
		                <tbody>
		                  <tr>
		                    <td style="width: 85%"><a href="<?php echo base_url('trail_list') ?>">Trial</a></td>
		                    <td>
		                        <?= $trial_user_info ?>
		                    </td>
		                  </tr>
		                  <tr>
		                    <td><a href="<?php echo base_url('signup_users') ?>">Signup</a></td>
		                    <td>
		                        <?= $signup_user_info ?>
		                    </td>
		                  </tr>
		                  <tr>
		                    <td><a href="<?php echo base_url('inactive_users') ?>">Inactive</a></td>
		                    <td>
		                        <?= $inactive_user_info ?>
		                    </td>
		                  </tr>
		                  <tr>
		                    <td><a href="<?php echo base_url('suspend_users') ?>">Suspend</a></td>
		                    <td>
		                      <?= $suspend_user_info; ?>
		                    </td>
		                  </tr>
		                </tbody>
		            </table>
		            <br>
	      			<table class="table table-bordered">
		                <tbody>
		                  <tr>
		                    <td><a href="<?php echo base_url('guest_users') ?>">Guest</a></td>
		                    <td>
		                      <?= $guest_user_info ?>
		                    </td>
		                  </tr>
		                  <tr>
		                    <td><a href="<?php echo base_url('parent_users') ?>">Parent</a></td>
		                    <td>
		                      <?= $parent_list ?>
		                    </td>
		                  </tr>
		                  <tr>
		                    <td><a href="<?php echo base_url('student_users') ?>">Student</a></td>
		                    <td>
		                      <?= $student_list ?>
		                    </td>
		                  </tr>
		                  <tr>
		                    <td><a href="<?php echo base_url('upper_level_users') ?>">Upper Level Student</a></td>
		                    <td>
		                      <?= $upper_student ?>
		                    </td>
		                  </tr>
		                  <tr>
		                    <td><a href="<?php echo base_url('tutor_users') ?>">Tutor</a></td>
		                    <td>
		                      <?= $tutors_list ?>
		                    </td>
		                  </tr>
		                  <tr>
		                    <td><a href="<?php echo base_url('corporateList') ?>">Corporate</a></td>
		                    <td>
		                      <?= $corporate_list ?>
		                    </td>
		                  </tr>
		                  <tr>
		                    <td><a href="<?php echo base_url('schoolList') ?>">School</a></td>
		                    <td>
		                      <?= $school_list ?>
		                    </td>
		                  </tr>
		                </tbody>
		            </table>
	      		</div>
	      		<div class="col-md-4">
	               <table class="table table-bordered">
	                <tbody>
	                  <tr>
	                    <td><a href="<?php echo base_url('student_prize_list') ?>">Student Prize</a></td>
	                    <td>
	                      <?= $student_prize_list ?>
	                    </td>
	                  </tr>
	                  <tr>
	                    <td><a href="<?php echo base_url('/admin/direct_deposit_list') ?>">Direct Deposit(normal course)</a></td>
	                    <td>
	                    	<?= $direct_deposit_count; ?>
	                    </td>
	                  </tr>
	                  <tr>
	                    <td><a href="<?php echo base_url('depositeResources') ?>">Direct Deposit(resourse)</a></td>
	                    <td>
	                        <?= $deposite_resources ?>
	                    </td>
	                  </tr>
	                  <tr>
	                    <td><a href="<?php echo base_url('groupboardResources') ?>">Groupboard (resourse)</a></td>
	                    <td>
	                      <?= $groupboardResources ?>
	                    </td>
	                  </tr>
	                  <tr>
	                    <td><a href="<?php echo base_url('groupboardTrialList') ?>">Groupboard (trial)</a></td>
	                    <td>
	                      <?= $groupboardTrialList ?>
	                    </td>
	                  </tr>
	                  <tr>
	                    <td><a href="<?php echo base_url('groupboardSignup') ?>">Groupboard(signup)</a></td>
	                    <td>
	                      <?= $groupboardSignup ?>
	                    </td>
	                  </tr>
	                  <tr>
	                    <td><a href="<?php echo base_url('tutorCommisionLists') ?>">Tutor(commission)</a></td>
	                    <td>
	                      <?= $CommissiontutorList ?>
	                    </td>
	                  </tr>
	                  <tr>
	                    <td><a href="<?php echo base_url('vocabularyCommisionLists') ?>">Vocabulary(commission)</a></td>
	                    <td>
	                      <?= $vocabularyCommision ?>
	                    </td>
	                  </tr>

	                  <tr>
	                    <td><a>Inative/Tutor/corporate/school</a></td>
	                    <td>
	                      0
	                    </td>
	                  </tr>

	                  <tr>
	                    <td><a href="<?php echo base_url('ninteyPercentageMark') ?>">Student who score 90% up</a></td>
	                    <td>
	                      <?= $ninteyPercentageMark ?>
	                    </td>
	                  </tr>
	                  <tr>
	                    <td><a href="<?php echo base_url('userEmailList') ?>">Email</a></td>
	                    <td>
	                      <?= $email_messages ?>
	                    </td>
	                  </tr>
					  <tr>
	                    <td><a href="<?php echo base_url('creativeUserList') ?>">Creative Registration</a></td>
	                    <td>
	                      <?= $total_creative_reg ?>
	                    </td>
	                  </tr>
					  <tr>
	                    <td><a href="<?php echo base_url('new_idea_create_student') ?>">New Idea created student</a></td>
	                    <td>
	                      <?=$idea_created_students;?>
	                    </td>
	                  </tr>
					  <tr>
	                    <td><a href="<?php echo base_url('new_idea_create_tutor') ?>">New Idea created tutor</a></td>
	                    <td>
						<?=$total_tutors;?>
	                    </td>
	                  </tr>
	                </tbody>
	              </table>
	      		</div>
	      		<div class="col-md-4">
	      			<table class="table table-bordered">
	                <tbody>
	                 <tr>
	                    <td style="width: 85%"><a href="<?php echo base_url();?>/country_users_list/1">Australia</a></td>
	                    <td>
	                      <?= $aus_users ?>
	                    </td>
	                  </tr>
	                  <tr>
	                    <td><a href="<?php echo base_url();?>/country_users_list/9">UK</a></td>
	                    <td>
	                      <?= $uk_users ?>
	                    </td>
	                  </tr>
	                  <tr>
	                    <td><a href="<?php echo base_url();?>/country_users_list/8">Bangladesh</a></td>
	                    <td>
	                      <?= $bd_users ?>
	                    </td>
	                  </tr>
	                  <tr>
	                    <td><a href="<?php echo base_url();?>/country_users_list/2">USA</a></td>
	                    <td>
	                      <?= $usa_users ?>
	                    </td>
	                  </tr>
	                  <tr>
	                    <td><a href="<?php echo base_url();?>/country_users_list/10">Canada</a></td>
	                    <td>
	                      <?= $can_users ?>
	                    </td>
	                  </tr>
	                </tbody>
	              </table>
	      		</div>
            </div>
	      </div>
	  	</div>
	</div>
</div>

<?= $this->endSection() ?>