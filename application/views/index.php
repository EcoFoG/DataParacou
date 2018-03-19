<?php
    if(NULL !== $this->input->get('circMax')){
        $circMax = $this->input->get('circMax');
    } else {
        $circMax = 150;
    }
    
    if(NULL !==$this->input->get('circMin')){
        $circMin = $this->input->get('circMin');
    } else {
        $circMin = 10;
    }
?>
<script>
    $(document).ready(function() {
        $('.multiple').select2({
            closeOnSelect: false
        });
        $('plots').select2({
            placeholder: "Select a plot"
        });
        $('#slider').slider({
            min: <?php echo $circDBMin ?>,
            max: <?php echo $circDBMax ?>,
            step: 0.01,
            values: [<?php echo $circMin ?>, <?php echo $circMax ?>],
            slide: function(event, ui) {
                for (var i = 0; i < ui.values.length; ++i) {
                    $("input.sliderValue[data-index=" + i + "]").val(ui.values[i]);
                }
            }
        });

        $('input.sliderValue').change(function() {
            var $this = $(this);
            $("#slider").slider("values", $this.data("index"), $this.val());
        });
    });
</script>

<nav class="navbar navbar-light bg-light justify-content-between">
  <a class="navbar-brand">Paracou-Ex</a>
  <form class="form-inline">
      <?php
            if($role == "admin"){
            $url_admin = base_url().'admin/';
            echo "<a class='btn' href='$url_admin'>Admin</a>";
        } ?>
      <a class="btn" href="<?php echo base_url().'main/logout/' ?>">Logout</a>
  </form>
</nav>
<div class="container-fluid"> 
    <div class="card">
        <div class="card-header">Filters</div>
            <div class="card-body">
                <form method="get">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg col-xl">  
                            <label>Circumference</label><br>
                            <div class="row">
                                <div class="col">
                                    <label for="circMin">Min</label>
                                    <input type="text" class="sliderValue form-control" name="circMin" data-index="0" value="<?php echo $circMin ?>" />
                                </div>
                                <div class="col">
                                    <label for="circMax">Max</label>
                                    <input type="text" class="sliderValue form-control" name="circMax" data-index="1" value="<?php echo $circMax ?>" />
                                </div>
                            </div>
                            <br>
                            <div id="slider"></div>
                            <br>
                            <label for="<?php echo $filters[1]?>[]">Status</label>
                            <select class="multiple form-control" name="<?php echo $filters[1]?>[]" multiple="multiple">
                                <?php
                                    foreach ($FCodeAlive as $status) {
                                        echo '<option>'.$status["CodeAlive"].'</option>';
                                    } ?>                    
                            </select>
                            </div>
                            <div class="col-md col-xl">
                                <label for="<?php echo $filters[0]?>[]">Plots </label>
                                <select class="multiple form-control" name="<?php echo $filters[0]?>[]" multiple="multiple">
                                    <?php
                                        foreach ($FPlot as $plot) {
                                            echo '<option>'.$plot["Plot"].'</option>';
                                        } ?>                    
                                </select>
                                <label for="SubPlot[]">Subplot </label>
                                <select class="multiple form-control" name="SubPlot[]" multiple="multiple">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                </select>
                                <label for="<?php echo $filters[2]?>[]">Census year</label>
                                <select class="multiple form-control" name="<?php echo $filters[2]?>[]" multiple="multiple">
                                    <?php
                                        foreach ($FCensusYear as $year) {
                                            echo '<option>'.$year["CensusYear"].'</option>';
                                        } ?>
                                </select>
                            </div>
                            <div class="col-md col-xl">
                                <label for="<?php echo $filters[3]?>[]">Vernacular name </label>
                                <select class="multiple form-control" name="<?php echo $filters[3]?>[]" multiple="multiple">
                                    <?php
                                        foreach ($FVernName as $vernname) {
                                            echo '<option>'.$vernname["VernName"].'</option>';
                                        } ?>
                                </select>
                                <label for="<?php echo $filters[4]?>[]">Family </label>
                                <select class="multiple form-control" name="<?php echo $filters[4]?>[]" multiple="multiple">
                                    <?php
                                        foreach ($FFamily as $family) {
                                            echo '<option>'.$family["Family"].'</option>';
                                        } ?>
                                </select>
                                <label for="<?php echo $filters[6]?>[]">Genus </label>
                                <select class="multiple form-control" name="<?php echo $filters[6]?>[]" multiple="multiple">
                                    <?php
                                        foreach ($FGenus as $genus) {
                                            echo '<option>'.$genus["Genus"].'</option>';
                                        } ?>
                                </select>
                                <br>
                                <label for="<?php echo $filters[5]?>[]">Species </label>
                                <select class="multiple form-control" name="<?php echo $filters[5]?>[]" multiple="multiple">
                                    <?php
                                        foreach ($FSpecies as $specie) {
                                            echo '<option>'.$specie["Species"].'</option>';
                                        } ?>
                                </select>
                                <br>
                            </div>
                        </div>
                        <input class="btn" type="submit" name="apply" value="Apply">
                        <input class="btn" type="submit" name="csv" value="Apply and export to CSV">
                    </div>
                </div>
            </form>
        </div>
            <br>
    </div>
        <?php 
            $this->table->set_heading($headers);
            echo $this->table->generate($table); 
        ?>
</div>

