<div class="container mx-auto my-3">
    <div class="row justify-content-center">
        <div class="col-8 justify-content-center">
            <h5>Informations</h5>
            <?php
                $fattr = array('id' => 'showrequest');
                echo form_open("",$fattr); ?>
            <?php
            foreach($requestinfo as $key=>$value){
                echo '<div class="form-group">';
                    echo form_label(humanize($key),"$key");
                    switch($key) {
                        case "address":
                            echo form_textarea(array('name'=>"$key", 'id'=> "$key", 'placeholder'=>humanize($key), 'class'=>'form-control', 'value' => set_value("$key",$value), 'disabled' => TRUE));
                            break;
                        case "summary_research":
                            echo form_textarea(array('name'=>"$key", 'id'=> "$key", 'placeholder'=>humanize($key), 'class'=>'form-control', 'value' => set_value("$key",$value), 'disabled' => TRUE));
                            break;
                        case "description_data":
                            echo form_textarea(array('name'=>"$key", 'id'=> "$key", 'placeholder'=>humanize($key), 'class'=>'form-control', 'value' => set_value("$key",$value), 'disabled' => TRUE));
                            break;
                        case "accepted":
                            echo form_textarea(array('name'=>"$key", 'id'=> "$key",'placeholder'=>'Not accepted yet', 'class'=>'form-control', 'value' => set_value("$key",$value), 'disabled' => TRUE));
                            break;
                        case "specific_conditions":
                            echo form_textarea(array('name'=>"$key", 'id'=> "$key", 'placeholder'=>humanize($key), 'class'=>'form-control', 'value' => set_value("$key",$value)));
                            break;
                        case "valorisation":
                            echo form_input(array('name'=>"$key", 'id'=> "$key", 'placeholder'=>humanize($key), 'class'=>'form-control', 'value' => set_value("$key",$value)));
                            break;
                        default:
                            echo form_input(array('name'=>"$key", 'id'=> "$key", 'placeholder'=>humanize($key), 'class'=>'form-control', 'value' => set_value("$key",$value), 'disabled' => TRUE));
                            break;
                    }
                    echo form_error($key);
                echo '</div>';
            }?>
        <?php
            if (!isset($requestinfo->accepted)) {
                echo "<a class=\"btn btn-success\" href= \"".base_url()."admin/accept_request/$id\">Accept request <i class=\"fas fa-check\"></i></a> ";
                echo "<a class=\"btn btn-danger\" href= \"".base_url()."admin/decline_request/$id\" data-confirm=\"Are you sure you want to decline this request ?\">Decline request  <i class=\"fas fa-times\"></i></a> ";
            }
            echo form_submit(array('name'=>'apply','value'=>'Apply changes',"class"=>"btn btn-primary"));
        ?>
            <a class="btn btn-secondary" href=<?php echo "\"".base_url()."admin/list_requests\"";?>>Back</a>
            <?php echo form_close(); ?>

        </div>
    </div>
</div>
