<div class="container">
    <div class="row">
        <div class="col-8 justify-content-center">
            <h5>Please enter the required information below.</h5>
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
                echo "<a class=\"btn btn-primary\" href= \"".base_url()."admin/accept_request/$id\">Accept request</a>";
            } 
            echo form_submit(array('name'=>'apply','value'=>'Apply changes',"class"=>"btn btn-primary"));
        ?>
            <a class="btn btn-secondary" href=<?php echo "\"".base_url()."admin/list_requests\"";?>>Back</a>
            <?php echo form_close(); ?>
            
        </div>
    </div>
</div>