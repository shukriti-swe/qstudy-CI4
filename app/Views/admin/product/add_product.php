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
        
        <?php 
        $this->session=session();
        if (!empty( $this->session->get('error') )) { ?>
          <div class="row">
            <div class="col-md-2"></div>
            <div class="alert alert-danger col-md-9" style="margin-left: 56px;"><?php echo $this->session->get('error'); ?> </div>
          </div>
        <?php  } ?>
        <form action="add_product_submit" method="post" enctype="multipart/form-data">
            <div class="button_schedule text-right" >
                <button type="submit" class="btn btn_next" ><i class="fa fa-save"></i> Save</button>
                <a href="" class="btn btn_next"><i class="fa fa-home"></i> Home</a>
            </div>
            <div class="sign_up_menu" id="product_div">
                <div class="row">
                    <div class="col-md-2">
                        <label>Product Title</label>                    
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="product_title">
                        <small style="color:red;" class="text-danger"><?php if (isset($validation)) {
                            echo $validation->getError('product_title');
                        } ?>
                       </small>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-2">
                        <label>Product Details</label>                    
                    </div>
                    <div class="col-md-8">
                        <textarea class="form-control" name="product_details"  row="3"></textarea>
                        <small style="color:red;" class="text-danger"><?php if (isset($validation)) {
                            echo $validation->getError('product_details');
                        } ?>
                        </small>
                    </div>

                </div><br>
                <div class="row">
                    <div class="col-md-2">
                        <label>Product Poit</label>                    
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="product_point">
                        <small style="color:red;" class="text-danger"><?php if (isset($validation)) {
                            echo $validation->getError('product_point');
                        } ?>
                        </small>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-2">
                        <label>Product Image</label>                    
                    </div>
                    <div class="col-md-8">
                        <input type="file" class="form-control" name="image">
                    </div>
                    <small style="color:red;" class="text-danger"><?php if (isset($validation)) {
                            echo $validation->getError('image');
                        } ?>
                    </small>
                </div><br>
            </div>
        </form>
    </div>
</div>

<script>
    
    
</script>

<?= $this->endSection() ?>