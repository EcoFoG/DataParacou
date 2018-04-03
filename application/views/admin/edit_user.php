<script type="text/javascript">
$(function() {
    $('input[id="expires"]').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY'
        },
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput: false
    }, function(chosen_date) {
        $('input[id="expires"]').val(chosen_date.format('DD/MM/YYYY'));
    });  
});
</script>
<div class="container mx-auto my-3">
    <div class="row justify-content-center">
        <div class="col-8 justify-content-center">
            <h5>Informations</h5>
            <?php 
                $fattr = array('id' => 'edit_user');
                echo form_open("",$fattr); ?>
            <?php
            $expires = $expires ?? $userinfo->expires;
            $first_name = $first_name ?? $userinfo->first_name;
            $last_name = $last_name ?? $userinfo->last_name;
            
            foreach($userinfo as $key=>$value){
                echo '<div class="form-group">';
                    switch($key) {
                        case "password":
                            break;
                        case "first_name":
                            echo form_label(humanize($key),"$key");
                            echo form_input(array('name'=>"$key", 'id'=> "$key", 'placeholder'=>'First name', 'class'=>'form-control', 'value' => set_value("$key",$first_name)));
                            break;
                        case "last_name":
                            echo form_label(humanize($key),"$key");
                            echo form_input(array('name'=>"$key", 'id'=> "$key", 'placeholder'=>'Last name', 'class'=>'form-control', 'value' => set_value("$key",$last_name)));
                            break;
                        case "expires":
                            echo form_label(humanize($key),"$key");
                            echo form_input(array('name'=>"$key", 'id'=> "$key", 'placeholder'=>'Never', 'class'=>'form-control', 'value' => set_value("$key",$expires)));
                            break;
                        case "role":
                            echo form_label(humanize($key),"$key");
                            echo form_dropdown($key, array('user'=>'user','admin'=>'admin'), set_value("$key",$value), array('id'=> "$key", 'class'=>'form-control'));
                            break;
                        default:
                            echo form_label(humanize($key),"$key");
                            echo form_input(array('name'=>"$key", 'id'=> "$key", 'placeholder'=>humanize($key), 'class'=>'form-control', 'value' => set_value("$key",$value), 'disabled' => TRUE));
                            break;   
                    }
                    echo form_error($key);
                echo '</div>';
            }?>
        <?php
            echo form_submit(array('name'=>'apply','value'=>'Apply changes',"class"=>"btn btn-primary"));
        ?>
            <a class="btn btn-secondary" href=<?php echo "\"".base_url()."admin/list_users\"";?>>Back</a>
            <?php echo form_close(); ?>
            
        </div>
    </div>
</div>
