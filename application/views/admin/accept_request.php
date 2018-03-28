<div class="container">
    <div class="row">
        <div class="col-lg-4 offset-lg-4">
            <h5>Please enter the required information below.</h5>
        <?php
            $fattr = array('class' => 'form-signin');
            echo form_open("/admin/accept_request/$request_id", $fattr); ?>
            <div class="form-group">
                <?php echo form_label("Specific conditions",'specific_conditions');?>
                <?php echo form_textarea(array('name'=>'specific_conditions', 'id'=> 'specific_conditions', 'class'=>'form-control', 'value' => set_value('specific_conditions'))); ?>
                <?php echo form_error('specific_conditions');?>
            </div>
            <?php echo form_submit(array('value'=>'Accept', 'class'=>'btn btn-lg btn-primary btn-block')); ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>