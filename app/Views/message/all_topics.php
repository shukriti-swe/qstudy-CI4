<?= $this->extend('qstudy/master_dashboard'); ?>
<?= $this->section('content'); ?>
<style>
td {
  border: 2px solid #f68d20 !important;
}

</style>
<div class="" style="margin-left: 15px;">
  <div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-6">
        <div class="row">
            <?php 
            $this->session=\Config\Services::session();
            if ($this->session->get('success_msg')) : ?>
            <div class="col-md-2"></div>
            <div class="col-md-10">
                <div class="alert alert-success alert-dismissible" role="alert" style="margin-top:10px;"> 
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong><?php echo $this->session->get('success_msg') ?></strong>
                </div>
            </div>
            <?php elseif ($this->session->get('error_msg')) : ?>
            <div class="col-md-2"></div>
            <div class="col-md-10">
                <div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:10px;"> 
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong><?php echo $this->session->get('error_msg') ?></strong>
                </div>
            </div>
            <?php endif; ?>
        </div>

    </div>
  </div>

  <div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-10 user_list">
      <div class="panel-group " id="task_accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
          <div class="panel-heading" role="tab" id="headingOne">
            <h4 class="panel-title text-center">
              <img style="float:left;" src="<?php echo base_url();?>/assets/images/email-read.png" alt="" width="45px" height="45px;">
              <a role="button" data-toggle="collapse" data-parent="#task_accordion" href="#collapseOnetask" aria-expanded="true" aria-controls="collapseOne"> 
                <strong><span style="font-size : 18px; ">  Choose Message Topics </span></strong>
              </a>
            </h4>
          </div>

          <div class="row panel-body">
            <div class="col-sm-12 text-right"> 
               <a type="button" href="<?php echo base_url();?>/message/topics/add"  class="btn btn_next" id=""><i class="fa fa-plus" style="padding-right: 5px;"></i>Add New</a>
            </div> 
          </div>
          <div class="row panel-body">
            <div class="col-sm-12 text-right"> 
              <table class="table table-bordered c_shcedule">
                <thead>
                  <tr>
                    <th scope="col">Sl</th>
                    <th scope="col">Topic</th>
                    <!-- <th scope="col">Edit</th> -->
                    <th scope="col">Delete</th>
                  </tr>
                </thead>
                <tbody>
                    <?php $i=1; ?>
                    <?php foreach ($allTopics as $topic) : ?>
                    <tr>
						<td><?php echo $i++; ?></td>
						<td>
							<a href="<?php echo base_url();?>/show_all_message/<?php echo $topic['id']; ?>">
								<?php echo  $topic['topic']; ?>
							</a>
						</td>
                      <!-- <td><a href=""><i class="fa fa-pencil"></i></a></td> -->
                      <td><a href="" onClick="delTopic(<?php echo $topic['id']; ?>)"><i class="fa fa-times"></i></a></td>
                    </tr>
                    <?php endforeach; ?>
                  
                </tbody>
              </table>
            </div>
          </div>


        </div>

      </div>

    </div>
  </div>
</div>
<script>
  //$('.table').DataTable({});
   function delTopic(id) {
    event.preventDefault();

    swal({
      title: 'Really want to delete this?',
      text: "All messages associated with this topic will be deleted and will never deliver.",
      buttons: {
        yes:'Yes',
        no:'No',
      },
    }).then((value) => {
      switch (value) {
       
        case "yes":
        fetch("<?php echo base_url();?>/message/topics/delete/"+id)
        swal("Item deleted successfully");
        break;
        
        case "no":
        swal("Item not deleted");
        break;
        
        default:
        swal("Item not deleted");
      }
    }).then(results => {
      window.location.reload()
    });
  }

  
</script>

<?= $this->endSection() ?>