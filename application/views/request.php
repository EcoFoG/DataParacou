<script>
    $(document).ready(function() {   
        $('.multiple').select2({
            closeOnSelect: false,
            placeholder: "All"
        });
        $('#timeline').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY'
            },
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false
        }, function(chosen_date) {
            $('#timeline').val(chosen_date.format('DD/MM/YYYY'));
        });  
        
    });
</script>
<div class="container mx-auto my-3">
    <div class="row justify-content-center">
        <div class="col-8 justify-content-center">
                <h2>Please enter the required information below.</h2>
                <?php $fattr = array('class' => 'form');
                     echo form_open(base_url().'main/request/', $fattr); ?>

                <div class="form-group">
                  <?php echo form_label('E-mail');?>
                  <?php echo form_input(array(
                      'name'=>'email',
                      'id'=> 'email',
                      'class'=>'form-control',
                      'value'=> set_value('email'))); ?>
                  <?php echo form_error('email') ?>
                </div>
                <div class="form-group">
                    <?php echo form_label('Affiliation');?>
                    <?php echo form_input(array(
                        'name'=>'affiliation',
                        'id'=> 'affiliation',
                        'class'=>'form-control',
                        'value'=> set_value('affiliation'))); ?>
                    <?php echo form_error('affiliation') ?>
                </div>
                <div class="form-group">
                    <?php echo form_label('Full address');?>
                  <?php echo form_textarea(array(
                      'name'=>'address',
                      'id'=> 'address',
                      'class'=>'form-control',
                      'value'=> set_value('address'))); ?>
                  <?php echo form_error('address') ?>
                </div>
                <div class="form-group">
                    <?php echo form_label('First name');?>
                  <?php echo form_input(array(
                      'name'=>'firstname',
                      'id'=> 'firstname',
                      'class'=>'form-control',
                      'value'=> set_value('firstname'))); ?>
                  <?php echo form_error('firstname') ?>
                </div>
                 <div class="form-group">
                    <?php echo form_label('Last name');?>
                  <?php echo form_input(array(
                      'name'=>'lastname',
                      'id'=> 'lastname',
                      'class'=>'form-control',
                      'value'=> set_value('lastname'))); ?>
                  <?php echo form_error('lastname') ?>
                </div>
                <div class="form-group">
                    <?php echo form_label('Title of the proposed research');?>
                  <?php echo form_input(array(
                      'name'=>'title_research',
                      'id'=> 'title_research',
                      'class'=>'form-control',
                      'value'=> set_value('title_research'))); ?>
                  <?php echo form_error('title_research') ?>
                </div>
                <div class="form-group">
                    <?php echo form_label('Summary of the proposed research (detailing the background, objectives, methods and expected results)');?>
                  <?php echo form_textarea(array(
                      'name'=>'summary_research',
                      'id'=> 'summary_research',
                      'class'=>'form-control',
                      'value'=> set_value('summary_research'))); ?>
                  <?php echo form_error('summary_research') ?>
                </div>
                <div class="form-group">
                    <?php echo form_label('Description of the required data');?>
                  <?php echo form_textarea(array(
                      'name'=>'description_data',
                      'id'=> 'description_data',
                      'class'=>'form-control',
                      'value'=> set_value('description_data'))); ?>
                  <?php echo form_error('description_data') ?>
                </div>
                <div class="form-group">
                    <?php echo form_label('Fields requested');?>
                  <select class="multiple form-control" name="Columns[]" multiple="multiple" style="width:100%;">
                    <?php
                        foreach($columns_name as $key=>$row){
                            echo "<option>".$row['column_name']."</option>";
                        }
                    ?>
                    </select>
                </div>
                <div class="form-group">
                    <?php echo form_label('Years requested');?>
                    <select class="multiple form-control" name="Years[]" multiple="multiple" style="width:100%;">
                    <?php
                        foreach($CensusYear as $key=>$row){
                            echo "<option>".$row['CensusYear']."</option>";
                        }
                    ?>
                    </select>
                </div>
                <div class="form-group">
                    <?php echo form_label('Plots requested');?>
                    <select class="multiple form-control" name="Plot[]" multiple="multiple" style="width:100%;">
                    <?php
                        foreach($Plot as $key=>$row){
                            echo "<option>".$row['Plot']."</option>";
                        }
                    ?>
                    </select>
                </div>
                <div class="form-group">
                    <?php echo form_label('Desired access time');?>
                  <?php echo form_input(array(
                      'name'=>'timeline',
                      'id'=> 'timeline',
                      'placeholder'=> 'Day of expiration',
                      'class'=>'form-control',
                      'value'=> set_value('timeline'))); ?>
                  <?php echo form_error('timeline') ?>
                </div>      
                <?php echo form_submit(array('value'=>'Request', 'class'=>'btn btn-lg btn-primary btn-block')); ?>
                <?php echo form_close(); ?>
            </div>
    </div>
</div>


