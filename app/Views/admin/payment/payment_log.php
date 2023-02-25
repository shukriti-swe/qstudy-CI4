<?= $this->extend('qstudy/master_dashboard'); ?>
<?= $this->section('content'); ?>

<div class="" style="margin-left: 15px;">
  <div class="row">
    <div class="col-md-4">
         <?php require_once(APPPATH.'Views/qstudy/leftnav.php');?>
    </div>

    <div class="col-md-8 user_list">
      <div class="panel-group " id="task_accordion" role="tablist" aria-multiselectable="true">

        <?php 
         $this->session=session();
        if (!empty( $this->session->get('paypal-success') )) { ?>
          <div class="row">
            <div class="col-md-2"></div>
            <div class="alert alert-success col-md-9" style="margin-left: 56px;">
            <?php 
                echo $this->session->get('paypal-success'); 
                $this->session->remove('paypal-success');
            ?> 
        </div>
          </div>
        <?php  } ?>
        <div class="panel panel-default">
          <div class="panel-heading" role="tab" id="headingOne">
            <h4 class="panel-title text-center">
              <a role="button" data-toggle="collapse" data-parent="#task_accordion" href="#collapseOnetask" aria-expanded="true" aria-controls="collapseOne"> 
                <strong><span style="font-size : 18px; ">Payment Log</span></strong>
              </a>
            </h4>
          </div>

          <table class="table table-bordered" id="example">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>User Type</th>
                        <th>Payment Status</th>
                        <th>Payment Date</th>
                        <th>Payment Type</th>  
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($payment_details as $row){?>
                        <tr>
                            <td><?= $row['name'];?></td>
                            <td>
                              <?php
                                    if($row['user_type'] == 1)
                                    {
                                        echo "Parent";    
                                    }
                                    else if($row['user_type'] == 2)
                                    {
                                        echo "Upper Level Student";
                                    }
                                    else if($row['user_type'] == 3)
                                    {
                                        echo "Tutor";
                                    }
                                    else if($row['user_type'] == 4)
                                    {
                                        echo "School";
                                    }
                                    else if($row['user_type'] == 5)
                                    {
                                        echo "Corporate";
                                    }
                                    else if($row['user_type'] == 6)
                                    {
                                        echo "Student";
                                    }
                                    else
                                    {
                                        echo "Q-study";
                                    }
                                ?>   

                            </td>
                            <td><?= $row['payment_status'];?></td>
                            <td><?= date(('Y-m-d'),$row['PaymentDate'])?></td>
                            <td>
                                <?php
                                    if($row['paymentType'] == 1)
                                    {
                                        echo "Stripe";    
                                    }
                                    else if($row['paymentType'] == 2)
                                    {
                                        echo "PayPal";
                                    }
                                    else
                                    {
                                        echo "Cash";
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>    
                </tbody>
          </table>

          

        </div>

      </div>

    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js"></script>
<script>
        //$('#example').DataTable();
        $(document).ready( function () {
            $('#example').DataTable();
        });
</script>
<?= $this->endSection() ?>