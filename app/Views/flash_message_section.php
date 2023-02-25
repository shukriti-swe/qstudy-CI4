<div class="row">
    <?php 
    $this->session=session();
    // echo '<pre>';
    // print_r($_SESSION);die();
    if ($this->session->get('success_msg')) : ?>
      <div class="col-md-2"></div>
      <div class="col-md-10">
        <div class="alert alert-success alert-dismissible" role="alert" style="margin-top:10px;"> 
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <strong><?php echo $this->session->get('success_msg') ?></strong>
        </div>
      </div>
    <?php 
     $this->session=session();
    elseif ($this->session->get('error_msg')) : ?>
      <div class="col-md-2"></div>
      <div class="col-md-10">
        <div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:10px;"> 
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <strong><?php echo $this->session->get('error_msg') ?></strong>
        </div>
      </div>
    <?php endif; ?>
  </div>
