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
        $("#tabs").tabs();
        $("th:contains(Measure code)").attr("data-original-title","<?php foreach($tip_CodeMeas as $value){echo "$value<br>";} ?>").attr("data-placement","bottom").attr("data-html","true").tooltip();
        $("th:contains(Status)").attr("data-original-title","<?php foreach($tip_CodeAlive as $value){echo "$value<br>";} ?>").attr("data-placement","bottom").attr("data-html","true").tooltip();
        $("th:contains(Status)").append('<div class="annoted-header">');
        $("th:contains(Measure code)").append('<div class="annoted-header">');
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
            echo "<a class=\"m-3\" href='$url_admin'>Admin</a>";
        } ?>
      <a class="m-3" href="<?php echo base_url().'main/logout/' ?>">Logout</a>
  </form>
</nav>
<br>
<div class="container-fluid"> 
    <div id="tabs" class="card">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                  <a class="nav-link" href="#filters">Filters</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#spatial">Spatial</a>
                </li>
            </ul>
            <div class="card-body">
                <form method="get">
                    <div id="spatial">
                        
                    </div>
                    <div id="filters">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-4 col-xl-4 col-sm-12 col-md-12">  
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
                                <select class="multiple form-control" name="<?php echo $filters[1]?>[]" multiple="multiple" style="width:100%;">
                                    <?php
                                            if (isset($get["CodeAlive"])) {
                                                $codeAliveInter = array_intersect($FCodeAlive,$get["CodeAlive"]);
                                            }
                                        foreach ($FCodeAlive as $key=>$status) {
                                            if (isset($codeAliveInter[$key]) && $codeAliveInter[$key] == $FCodeAlive[$key]) {
                                                echo '<option selected="selected">'.$status.'</option>';
                                            } else {
                                                echo '<option>'.$status.'</option>';
                                            }

                                        } ?>                    
                                </select>
                                </div>
                                <div class="col-lg-4 col-xl-4 col-sm-12 col-md-12">  
                                    <label for="<?php echo $filters[0]?>[]">Plots </label>
                                    <select class="multiple form-control" name="<?php echo $filters[0]?>[]" multiple="multiple" style="width:100%;">
                                        <?php
                                             if (isset($get["CodeAlive"])) {
                                                $PlotInter = array_intersect($FPlot,$get["Plot"]);
                                            }
                                            foreach ($FPlot as $key=>$plot) {
                                                if (isset($PlotInter[$key]) && $PlotInter[$key] == $FPlot[$key]) {
                                                    echo '<option selected="selected">'.$plot.'</option>';
                                                } else {
                                                    echo '<option>'.$plot.'</option>';
                                                }
                                            } ?>
                                    </select>
                                    <label for="SubPlot[]">Subplot </label>
                                    <select class="multiple form-control" name="SubPlot[]" multiple="multiple" style="width:100%;">
                                        <?php
                                            for($i = 1 ; $i <= 4; $i++){
                                            if (isset($get["SubPlot"]) && in_array($i, $get["SubPlot"])) {
                                                echo "<option selected=\"selected\">$i</option>";
                                            } else {
                                                echo "<option>$i</option>";
                                            }

                                            }
                                        ?>
                                    </select>
                                    <label for="<?php echo $filters[2]?>[]">Census year</label>
                                    <select class="multiple form-control" name="<?php echo $filters[2]?>[]" multiple="multiple" style="width:100%;">
                                        <?php
                                             if (isset($get["CensusYear"])) {
                                                $YearInter = array_intersect($FCensusYear,$get["CensusYear"]);
                                            }
                                            foreach ($FCensusYear as $key=>$year) {
                                                if (isset($YearInter[$key]) && $YearInter[$key] == $FCensusYear[$key]) {
                                                    echo '<option selected="selected">'.$year.'</option>';
                                                } else {
                                                    echo '<option>'.$year.'</option>';
                                                }
                                            }?>
                                    </select>
                                </div>
                                <div class="col-md-4 col-xl-4 col-sm-12 col-md-12">
                                    <label for="<?php echo $filters[3]?>[]">Vernacular name </label>
                                    <select class="multiple form-control" name="<?php echo $filters[3]?>[]" multiple="multiple" style="width:100%;">
                                        <?php
                                             if (isset($get["VernName"])) {
                                                $VernInter = array_intersect($FVernName,$get["VernName"]);
                                            }
                                            foreach ($FVernName as $key=>$vernname) {
                                                if (isset($VernInter[$key]) && $VernInter[$key] == $FVernName[$key]) {
                                                    echo '<option selected="selected">'.$vernname.'</option>';
                                                } else {
                                                    echo '<option>'.$vernname.'</option>';
                                                }
                                            }?>
                                    </select>
                                    <label for="<?php echo $filters[4]?>[]">Family </label>
                                    <select class="multiple form-control" name="<?php echo $filters[4]?>[]" multiple="multiple" style="width:100%;">
                                        <?php
                                             if (isset($get["Family"])) {
                                                $FamilyInter = array_intersect($FFamily,$get["Family"]);
                                            }
                                            foreach ($FFamily as $key=>$family) {
                                                if (isset($FamilyInter[$key]) && $FamilyInter[$key] == $FFamily[$key]) {
                                                    echo '<option selected="selected">'.$family.'</option>';
                                                } else {
                                                    echo '<option>'.$family.'</option>';
                                                }
                                            }?>
                                    </select>
                                    <label for="<?php echo $filters[6]?>[]">Genus </label>
                                    <select class="multiple form-control" name="<?php echo $filters[6]?>[]" multiple="multiple" style="width:100%;">
                                        <?php
                                             if (isset($get["Genus"])) {
                                                $GenusInter = array_intersect($FGenus,$get["Genus"]);
                                            }
                                            foreach ($FGenus as $key=>$genus) {
                                                if (isset($GenusInter[$key]) && $GenusInter[$key] == $FGenus[$key]) {
                                                    echo '<option selected="selected">'.$genus.'</option>';
                                                } else {
                                                    echo '<option>'.$genus.'</option>';
                                                }
                                            }?>
                                    </select>
                                    <br>
                                    <label for="<?php echo $filters[5]?>[]">Species </label>
                                    <select class="multiple form-control" name="<?php echo $filters[5]?>[]" multiple="multiple" style="width:100%;">
                                        <?php
                                             if (isset($get["Species"])) {
                                                $SpeciesInter = array_intersect($FSpecies,$get["Species"]);
                                            }
                                            foreach ($FSpecies as $key=>$specie) {
                                                if (isset($SpeciesInter[$key]) && $SpeciesInter[$key] == $FSpecies[$key]) {
                                                    echo '<option selected="selected">'.$specie.'</option>';
                                                } else {
                                                    echo '<option>'.$specie.'</option>';
                                                }
                                            }?>
                                    </select>
                                    <br>
                                </div>
                            </div>
                            <input class="m-2 mx-auto btn" type="submit" name="apply" value="Apply">
                            <input class="m-2 mx-auto btn" type="submit" name="csv" value="Export to CSV">
                        </div>
                    </div>
                </form>
            </div> 
    </div>
    <br>
    <div class="container">
        <?php echo "$pagination_links";?>
    </div>
</div>
<?php 
    $this->table->set_heading($headers);
    echo $this->table->generate($table); 
?>
