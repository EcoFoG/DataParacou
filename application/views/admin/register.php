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
<div class="container">
    <div class="row">
        <div class="col-lg-4 offset-lg-4">
            <h5>Please enter the required information below.</h5>
        <?php
            $fattr = array('class' => 'form-signin', 'id' => 'register');
            echo form_open('',$fattr); ?>
            <div class="form-group">
                <?php echo form_label("First name",'firstname');?>
                <?php echo form_input(array('name'=>'firstname', 'id'=> 'firstname', 'placeholder'=>'First Name', 'class'=>'form-control', 'value' => set_value('firstname',$firstname))); ?>
                <?php echo form_error('firstname');?>
            </div>
            <div class="form-group">
                <?php echo form_label("Last name",'lastname');?>
                <?php echo form_input(array('name'=>'lastname', 'id'=> 'lastname', 'placeholder'=>'Last Name', 'class'=>'form-control', 'value'=> set_value('lastname',$lastname))); ?>
                <?php echo form_error('lastname');?>
            </div>
            <div class="form-group">
                <?php echo form_label("E-mail",'email');?>
                <?php echo form_input(array('name'=>'email', 'id'=> 'email', 'placeholder'=>'Email', 'class'=>'form-control', 'value'=> set_value('email',$email))); ?>
                <?php echo form_error('email');?>
            </div>
            <div class="form-group">
                <?php echo form_label("Expires",'expires');?>
                <?php echo form_input(array('name'=>'expires', 'id'=> 'expires', 'placeholder'=>'Never', 'class'=>'form-control', 'value'=> set_value('expires',$expires))); ?>
                <?php echo form_error('expires');?>
            </div>
            <div class="form-group">
                <?php echo form_label("Role",'role');?>
                <?php echo form_dropdown('role',array('user','admin'),'user',array( 'id'=> 'role', 'class'=>'form-control' )); ?>
                <?php echo form_error('role');?>
            </div>
            <?php echo form_submit(array('value'=>'Add user', 'class'=>'btn btn-lg btn-primary btn-block')); ?>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>