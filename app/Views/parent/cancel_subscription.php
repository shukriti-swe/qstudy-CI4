<?= $this->extend('qstudy/master_dashboard'); ?>
<?= $this->section('content');?>
    <div class="col-sm-6 col-sm-offset-3">
        <div align="center" style="margin-top:5%;">
            <p>Cancel Subscription of child</p>
        </div><br><br>
            <?php foreach($parent_child as $parent_childs){?>
                <div style="text-align:center;" > 
                  
                        <a style="padding:1%; display:inline-block;" align="center" href="javascript:void(0);" class="cancel_subscription" onclick="cancelSubscription()" data-id="<?php echo $parent_childs['id']?>"><?php echo $parent_childs['name'];?></a>
                <div>  
             <?php } ?> 
       
   
    </div>
<script>
    // $('.cancel_subscription').click(function(e){
      
    // });

 function cancelSubscription() {
  event.preventDefault();

  swal("Really want to cancel q-study subscription?", {
    buttons: {
      no: "NO",
      yes: {
        text: "YES",
        value: "yes", 
      },
    },
  })
  .then((value) => {
    switch (value) {
     
      case "no":
      swal("Subscription not canceled",'Thanks for being with us!!',  'error');
      break;
      
      case "yes":
        var id=$('.cancel_subscription').attr('data-id');
        $.ajax({
        url: "<?php echo base_url(); ?>/cancel_subscription",
        type: "POST",
        data: {student_id:id},
        success: function (response) {
            console.log(response);
            if(response == 'trail')
            {
                alert('This Student have no course');
            }
            else
            {
                swal( "Subscription canceled",'Thanks for being with us!!', "success");
                
            }
       
         }
        });
      break; 
      
      default:
      swal("Got away safely!");
    }
  });
  }
</script>
<?= $this->endSection() ?>