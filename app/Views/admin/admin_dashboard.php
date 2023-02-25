<?= $this->extend('qstudy/master_dashboard'); ?>
<?= $this->section('content'); ?>

<div class="row">
    <div class="">

        <ul class="personal_ul personal_ul_course schedule">
            <li class="presonal"><a href="<?php echo base_url();?>/admin">Personal</a></li>
            <!--<li class="presonal2"><a href="course_theme">Course Theme</a></li>-->
            <li class="presonal2"><a href="<?php echo base_url();?>/all_area">All Area</a></li>
        </ul>

    </div>
</div>

<?= $this->endSection() ?>