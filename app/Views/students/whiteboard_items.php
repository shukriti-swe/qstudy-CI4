<?= $this->extend('qstudy/master_dashboard'); ?>
<?= $this->section('content'); ?>

<div class="row">
    <div class="">
        <ul class="personal_ul">
        	<li class="presonal4">
	            <a href="<?php echo base_url();?>/std-question-store" style="margin-left: 59px;"> 
	         <img src="<?= base_url('/assets/images/resources.png') ?>"  height="40" >
	        </a></li>
        </ul>
    </div>
</div>

<?= $this->endSection() ?>