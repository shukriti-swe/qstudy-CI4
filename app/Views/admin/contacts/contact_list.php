<?= $this->extend('qstudy/master_dashboard'); ?>
<?= $this->section('content'); ?>
<style>
    .panel-title > a {
        text-decoration: none;
        color: #ab8d00 !important;
    }
</style>
<div class="row">
    <div class="col-md-4">
        <?php require_once(APPPATH.'Views/qstudy/leftnav.php');?>
    </div>
    <div class="col-md-8">
        <div class="sign_up_menu" id="country_div">
            <?php require_once(APPPATH.'Views/admin/contacts/contact_div.php');?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>