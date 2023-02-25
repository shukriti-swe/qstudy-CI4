<?= $this->extend('qstudy/master_dashboard'); ?>
<?= $this->section('content'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="button_schedule text-right" >
            <a href="" class="btn btn_next" data-toggle="modal" data-target="#ss_sucess_mess"><i class="fa fa-save"></i> Save</a>
            <a href="" class="btn btn_next"><i class="fa fa-home"></i> Home</a>
        </div>

        <input type="hidden" name="country_id" id="country_id" value="<?php echo $country_info[0]['id']?>">
        <input type="hidden" name="user_type" id="user_type" value="<?php echo $user_type;?>">
        <input type="hidden" name="subscription_type" id="subscription_type" value="<?php echo $subscription_type;?>">
        
        <div class="row schedule_country_details">
            <div class="col-md-3">
                <P style=" color: #000; "><i class="fa fa-file" style=" color: #fbea71; "></i> <?php echo $country_info[0]['countryName']?></P>
            </div>
            <div class="col-md-9">
                <label class="col-md-2">How Many Rows</label>
                <div class="col-md-2">
                    <input class="form-control" type="number" value="<?php if($course_info){echo count($course_info);}else{echo 0;}?>" id="box_num">
                </div>
                <div class="col-md-2">
                    <button class="btn btn_next" type="button" id="add_course_schedule_box">Submit</button>
                </div>
                
            </div>

            <div class="row">
                <div class="col-md-12" id="show_course_box" style="margin-top: 30px;">
                    <?php $i = 1;foreach ($course_info as $row){?>
                    <div class="col-md-4" id="show_box_content<?php echo $i;?>">
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class ="form-group">
                                    <label for="text" style="float:left;margin-right:5px;">1</label>       
                                    <div class="input-group">   
                                        <input  class="validate[required] text-input form-control" style="height: 21px;margin: 0px;width: auto" type="checkbox" value="1" id="is_enable<?php echo $i;?>" <?php if($row['is_enable'] == 1){echo 'checked';}?>/>                                           
                                    </div>   
                                </div>
                                <div class ="form-group">
                                    <label for="text" style="float:left;margin-right:5px;">$</label>       
                                    <div class="input-group">   
                                        <input  class="validate[required] text-input form-control" type="number" id="course_cost<?php echo $i;?>" value="<?php echo $row['courseCost'];?>"/>
                                        <input type="hidden" id="course_id<?php echo $i?>" value="<?php echo $row['id']?>"/>
                                    </div>   
                                </div> 
                            </div>

                            <div class="col-md-8">
                                <textarea class="course_textarea" id="course_name<?php echo $i;?>"><?php echo $row['courseName'] ?></textarea>
                            </div>

                            <div class="col-md-12" style="text-align: center;margin: 10px;">
                                <button class="btn btn_next" type="button" style="padding: 5px;" onclick="add_course_schedule('<?php echo $i;?>')">submit</button>
                            </div>
                            <div id="show_course_content<?php echo $i;?>">
                                
                            </div>

                        </div>
                    </div>
                    <?php $i++;}?>
                </div>
            </div>
            
            
        </div>
        
        
        
    </div>
</div>



<!--   Success Message   -->
<div class="modal fade ss_modal" id="ss_sucess_mess" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">&nbsp;</h4>
            </div>
            <div class="modal-body row">
                <img src="assets/images/icon_info.png" class="pull-left"> <span class="ss_extar_top20">Save Sucessfully</span> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn_blue" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>

 
<script>
    var exist_box_num = <?php if($course_info){echo count($course_info);}else{echo 0;}?>;
    
    $(document).ready(function () {
        
        $("#add_course_schedule_box").on("click", function () {
            
            var box_num = $("#box_num").val();
            
            for (var i = 1; i <= parseInt(box_num); i++) {
               //     console.log(exist_box_num);
                if(i > exist_box_num){
                    create_box(i);
                    exist_box_num = i;
                }
            }
            
            
            $('.course_textarea').ckeditor({
                height: 50,
                extraPlugins : 'simage',
                filebrowserBrowseUrl: '/assets/uploads?type=Images',
                filebrowserUploadUrl: 'imageUpload',
                toolbar: [
                        { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'NewPage', 'Preview', 'Print','Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                        { name: 'basicstyles', items: [ 'Bold', 'Italic','Underline','Strike','Subscript', 'Superscript', '-', 'SImage' ] },
                        '/',
                        { name: 'document', items: [ 'RemoveFormat','Maximize', 'ShowBlocks','TextColor', 'BGColor','Table','-', 'Templates','Link', 'addFile'] },	// Defines toolbar group with name (used to create voice label) and items in 3 subgroups.
                                // Line break - next group will be placed in new line.
                        '/',
                        { name: 'styles', items: [ 'Styles', 'Format','Font','FontSize'] }
                    ],
                allowedContent: true
            });
        });
        
        
    });
    
    function create_box(box_num){
        var box_element = "";
        box_element += '<div class="col-md-4" id="show_box_content'+box_num+'">';

            box_element += '<div class="col-md-12">';
                box_element += '<div class="col-md-4">';
                    box_element += '<div class ="form-group">';
                        box_element += '<label for="text" style="float:left;margin-right:5px;">1</label>';
                        box_element += '<div class="input-group">';
                            box_element += '<input class="validate[required] text-input form-control" style="height: 21px;margin: 0px;width: auto" type="checkbox" value="1" id="is_enable'+box_num+'" />';
                        box_element += '</div>';
                    box_element += '</div>';

                    box_element += '<div class ="form-group">';
                        box_element += '<label for="text" style="float:left;margin-right:5px;">$</label>';
                        box_element += '<div class="input-group">';
                            box_element += '<input  class="validate[required] text-input form-control" type="number" id="course_cost'+box_num+'"  value="<?php //if($subscription_type == 2){echo 0;}?>"/>';
                            box_element += '<input type="hidden" id="course_id'+box_num+'" value=""/>';
                        box_element += '</div>';
                    box_element += '</div>';

                box_element += '</div>';

                box_element += '<div class="col-md-8">';
                    box_element += '<textarea class="course_textarea" id="course_name'+box_num+'"></textarea>';
                box_element += '</div>';

                box_element += '<div class="col-md-12" style="text-align: center;margin: 10px;">';
                    box_element += '<button class="btn btn_next" type="button" style="padding: 5px;" onclick="add_course_schedule('+box_num+')">submit</button>';
                box_element += '</div>';
                box_element += '<div id="show_course_content'+box_num+'">';
                box_element += '</div>';
            box_element += '</div>';
        box_element += '</div>';
        
        $("#show_course_box").append(box_element);
//        $(box_element).insertAfter("#show_course_box");
    }
    
    
    function add_course_schedule(box_num){
        var is_enable = 0;
        if($('input:checkbox[id=is_enable'+box_num+']').is(':checked') ==true ) {
            is_enable = 1; 
        }
        
        $.ajax({
            url: '<?php echo site_url('save_course_schedule'); ?>',
            type: 'POST',
            data: {
                user_type: $("#user_type").val(), 
                box_num: box_num,
                // subscription_type: $("#subscription_type").val(),
                country_id: $("#country_id").val(),
                course_id: $("#course_id"+box_num).val(),
                courseName: $("#course_name"+box_num).val(),
                courseCost: $("#course_cost"+box_num).val(),
                is_enable: is_enable
                
            },
            success: function (data) { 
                var res = jQuery.parseJSON(data);
                $('#course_id'+box_num).val(res.course_id);
                $('#show_course_content'+box_num).html(res.course_content_div);
            }
        });
    }
    
    
    function delete_course(course_id,box_num){
    
        $.ajax({
            url: '<?php echo site_url('delete_course'); ?>',
            type: 'POST',
            data: {
                course_id: course_id,
            },
            success: function (data) {
                $('#show_box_content'+box_num).remove();
                exist_box_num = 0;
            }
        });
    }
    
</script>

<?= $this->endSection() ?>