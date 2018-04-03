<?php
    $circMax = !(empty($this->input->get('circMax'))) ? $this->input->get('circMax') : $defaultCircBoundaries['circMax'];
    $circMin = !(empty($this->input->get('circMin'))) ? $this->input->get('circMin') : $defaultCircBoundaries['circMin'];
?>
<script>
    $(document).ready(function() {   
        <?php 
            foreach($tooltips as $key=>$value){
                if ($value) {
                    echo "\$(\"th:contains('$key')\").attr(\"data-toggle\",\"tooltip\").attr(\"title\",\"$value\")\n";
                }
            }
        
        ?>
        $("#tabs").tabs();
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
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a href=<?php echo base_url();?> class="navbar-brand">Paracou-Ex</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarText">
    <ul class="navbar-nav mr-auto">
        <li class="nav-item">
            <a class="nav-link" href=<?php echo "\"".base_url()."public/pdf/Paracou_data_dictionnary.pdf\"";?>>Data dictionnary</a>
        </li>
    </ul>
    <form class="navbar-text form-inline">
        <?php
            if($role == "admin"){
            $url_admin = base_url().'admin/';
            echo "<a class=\"m-3\" href='$url_admin'>Admin</a>";
        } ?>
        <a class="m-2" href="<?php echo base_url().'main/logout/' ?>">Logout</a>
    </form>
  </div>
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
                                <label for="CodeAlive[]">Status</label>
                                <select class="multiple form-control" name="CodeAlive[]" multiple="multiple" style="width:100%;">
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
                                    <label for="Plot[]">Plots </label>
                                    <select class="multiple form-control" name="Plot[]" multiple="multiple" style="width:100%;">
                                        <?php
                                             if (isset($get["Plot"])) {
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
                                            if (isset($get["SubPlot"])) {
                                                $SubPlotinter = array_intersect($FSubPlot,$get["SubPlot"]);
                                            }
                                            foreach ($FSubPlot as $key=>$subplot) {
                                                if (isset($SubPlotinter[$key]) && $SubPlotinter[$key] == $FSubPlot[$key]) {
                                                    echo '<option selected="selected">'.$subplot.'</option>';
                                                } else {
                                                    echo '<option>'.$subplot.'</option>';
                                                }
                                            }?>
                                    </select>
                                    <label for="CensusYear[]">Census year</label>
                                    <select class="multiple form-control" name="CensusYear[]" multiple="multiple" style="width:100%;">
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
                                    <label for="VernName[]">Vernacular name </label>
                                    <select class="multiple form-control" name="VernName[]" multiple="multiple" style="width:100%;">
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
                                    <label for="Family[]">Family </label>
                                    <select class="multiple form-control" name="Family[]" multiple="multiple" style="width:100%;">
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
                                    <label for="Genus[]">Genus </label>
                                    <select class="multiple form-control" name="Genus[]" multiple="multiple" style="width:100%;">
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
                                    <label for="Species[]">Species </label>
                                    <select class="multiple form-control" name="Species[]" multiple="multiple" style="width:100%;">
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
