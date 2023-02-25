<?= $this->extend('qstudy/master_dashboard'); ?>
<?= $this->section('content'); ?>

<style>
  label {
    font-size: 13px;
  }

  .user_list {
    border-color: #2F91BA;
  }
</style>


<div class="row" style="margin-top:50px;">
  <div class="col-md-4">
      <?php require_once(APPPATH.'Views/faqs/all_faq_left_nav.php');?> 
  </div>
  
    <?php 

    $this->session=session();
    if ($this->session->get('success_msg')) :?>
  <div class="col-md-8" id="flashmsg">
    <div class="alert alert-success" role="alert">
        <?php echo $this->session->get('success_msg'); ?>
    </div>
  </div>
    <?php endif; ?>
  <div class="col-md-8 user_list">
    <div class="panel-group " id="task_accordion" role="tablist" aria-multiselectable="true">
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne">
          <h4 class="panel-title text-center">
            <a role="button" data-toggle="collapse" data-parent="#task_accordion" href="#collapseOnetask" aria-expanded="true" aria-controls="collapseOne"> 
              <strong><span style="font-size : 18px; ">  <?php echo $faq['title']; ?> </span></strong>
            </a>
          </h4>
        </div>

          <div class="row panel-body">
             <div class="col-sm-12">
                  <?php 
                    if($faq['show_in_home']==1)
                    {
                      echo $faq['body'];
                    }
                  ?>
                </div>
          </div>
      
      </div>
    </div>

  </div>
</div>


<?= $this->endSection() ?>