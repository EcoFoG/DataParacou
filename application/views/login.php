<div class="container">
    <div class="row">
        <div class="col-lg-4 offset-lg-4">
                <h2>Please login</h2>
                <?php $fattr = array('class' => 'form-signin');
                     echo form_open(base_url().'main/login/', $fattr); ?>
                <div class="form-group">
                  <?php echo form_input(array(
                      'name'=>'email',
                      'id'=> 'email',
                      'placeholder'=>'Email',
                      'class'=>'form-control',
                      'value'=> set_value('email'))); ?>
                  <?php echo form_error('email') ?>
                </div>
                <div class="form-group">
                  <?php echo form_password(array(
                      'name'=>'password',
                      'id'=> 'password',
                      'placeholder'=>'Password',
                      'class'=>'form-control',
                      'value'=> set_value('password'))); ?>
                  <?php echo form_error('password') ?>
                </div>
                <?php echo form_submit(array('value'=>'Let me in!', 'class'=>'btn btn-lg btn-primary btn-block')); ?>
                <?php echo form_close(); ?>
                <p>Click <a href="<?php echo base_url();?>main/forgot">here</a> if you forgot your password.</p>
            </div>
    </div>
</div>
