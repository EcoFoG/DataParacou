<script>
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2({
            hideSelected: true
        });
    });
</script>
    
<div class="container-fluid">
    <h3>Welcome</h3>
    Minimum circumference: <input id="min" name="min" type="text">
    Maximum circumference: <input id="max" name="max" type="text">
    <table id="guyafor_datatable" class="table table-striped table-bordered" width="100%" cellspacing="0">
        <thead>
        <tr>
            <th>Forest</th>
            <th>Plot</th>
            <th>Subplot</th>
            <th>Tree Field number</th>
            <th>Area</th>
            <th>Tree id</th>
            <th>X</th>
            <th>Y</th>
            <th>X UTM</th>
            <th>Y UTM</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Vern name</th>
            <th>Wood density</th>
            <th>Circumference</th>
            <th>Status</th>
            <th>Measure code</th>
            <th>Year</th>
            <th>Measure date</th>
            <th>Family</th>
            <th>Genus</th>
            <th>Specie</th>
            <th>Botanist source</th>
            <th>Safety index</th>
        </tr>
        <tr>
            <th></th>
            <th>
                <select class="js-example-basic-multiple" name="plots[]" multiple="multiple">
                    <?php
                        foreach ($plots as $plot) {
                            echo '<option>'.$plot["Plot"].'</option>';
                        } ?>                    
                </select>
            </th>
            <th>
                <select class="js-example-basic-multiple" name="subplots[]" multiple="multiple">
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                </select>
            </th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th><select class="js-example-basic-multiple" name="vernnames[]" multiple="multiple">
                    <?php
                        foreach ($vernnames as $vernname) {
                            echo '<option>'.$vernname["VernName"].'</option>';
                        } ?>
                </select>
            </th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>
                <select class="js-example-basic-multiple" name="years[]" multiple="multiple">
                    <?php
                        foreach ($years as $year) {
                            echo '<option>'.$year["CensusYear"].'</option>';
                        } ?>
                </select>
            </th>
            <th></th>
            <th>
                <select class="js-example-basic-multiple" name="families[]" multiple="multiple">
                    <?php
                        foreach ($families as $family) {
                            echo '<option>'.$family["Family"].'</option>';
                        } ?>
                </select>
            </th>
            <th>
                <select class="js-example-basic-multiple" name="genuses[]" multiple="multiple">
                    
                    <?php
                        foreach ($genuses as $genus) {
                            echo '<option>'.$genus["Genus"].'</option>';
                        } ?>
                </select>
            </th>
            <th></th>
            <th></th>
            <th></th>
            
        </tr>
        </thead>
        
    </table>
</div>

