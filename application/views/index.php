<?php
    $circMax = !(empty($this->input->get('circMax'))) ? $this->input->get('circMax') : $defaultCircBoundaries['circMax']; // Opérateur ternaire : http://php.net/manual/fr/language.operators.comparison.php#language.operators.comparison.ternary
    $circMin = !(empty($this->input->get('circMin'))) ? $this->input->get('circMin') : $defaultCircBoundaries['circMin']; // Si circMin passé dans l'url, l'enregistrer dans la variable $circMin sinon utiliser la valeur de $defaultCircBoundaries (référence fonction index application/controller/main.php)
?>
<script type="text/javascript">
    $(document).ready(function() {
      $('.multiple').on('select2:close', function (e){
                  var VernNameObj = $("#VernName").select2('data');
                  var VernNamesSelected = VernNameObj.map(function (obj) {
                      return obj.text;
                  });
                  var FamiliesObj = $("#Family").select2('data');
                  var FamiliesSelected = FamiliesObj.map(function (obj) {
                      return obj.text;
                  });
                  var GenusObj = $("#Genus").select2('data');
                  var GenusSelected = GenusObj.map(function (obj) {
                      return obj.text;
                  });
                  var SpeciesObj = $("#Species").select2('data');
                  var SpeciesSelected = SpeciesObj.map(function (obj) {
                      return obj.text;
                  });
                  var PlotObj = $("#Plot").select2('data');
                  var PlotsSelected = PlotObj.map(function (obj) {
                      return obj.text;
                  });
                  var SubPlotObj = $("#SubPlot").select2('data');
                  var SubPlotsSelected = SubPlotObj.map(function (obj) {
                      return obj.text;
                  });
                  var CensusYearObj = $("#CensusYear").select2('data');
                  var CensusYearsSelected = CensusYearObj.map(function (obj) {
                      return obj.text;
                  });

                  $.ajax({
                      url: "<?php echo base_url()?>main/api_filters",
                      data: {PlotsSelected : PlotsSelected, CensusYearsSelected : CensusYearsSelected, VernNamesSelected : VernNamesSelected, FamiliesSelected : FamiliesSelected, GenusSelected : GenusSelected, SpeciesSelected : SpeciesSelected },
                      datatype: 'json',
                      async: true
                  }).done(function(dataajax){
                      dataajax = JSON.parse(dataajax);

                      var VernNameObj = $("#VernName").select2('data');
                      var FamilyObj = $("#Family").select2('data');
                      var GenusObj = $("#Genus").select2('data');
                      var SpeciesObj = $("#Species").select2('data');
                      var PlotObj = $("#Plot").select2('data');
                      var SubPlotObj = $("#SubPlot").select2('data');
                      var CensusYearObj = $("#CensusYear").select2('data');

                      // Merge Selected options with received data to avoid unselection and convert it to an array
                      let mergedVernNames = Object.values(Object.assign(dataajax.VernName, VernNameObj)).sort((b, a) => b.text - a.text);
                      let mergedFamily = Object.values(Object.assign(dataajax.Family, FamiliesObj)).sort((b, a) => b.text - a.text);
                      let mergedGenus = Object.values(Object.assign(dataajax.Genus, GenusObj)).sort((b, a) => b.text - a.text);
                      let mergedSpecies = Object.values(Object.assign(dataajax.Species, SpeciesObj)).sort((b, a) => b.text - a.text);
                      let mergedPlot = Object.values(Object.assign(dataajax.Plot, PlotObj)).sort((b, a) => b.text - a.text);
                      let mergedSubPlot = Object.values(Object.assign(dataajax.SubPlot, SubPlotObj)).sort((b, a) => b.text - a.text);
                      let mergedCensusYear = Object.values(Object.assign(dataajax.CensusYear, CensusYearObj)).sort((b, a) => b.text - a.text);

                      if (dataajax) {
                          $("#VernName").html("");
                          $('#VernName').select2({
                              closeOnSelect: false,
                              data : mergedVernNames
                          });
                          $("#Family").html("");
                          $('#Family').select2({
                              closeOnSelect: false,
                              data : mergedFamily
                          });
                          $("#Genus").html("");
                          $('#Genus').select2({
                              closeOnSelect: false,
                              data : mergedGenus
                          });
                          $("#Species").html("");
                          $('#Species').select2({
                              closeOnSelect: false,
                              data : mergedSpecies
                          });
                          $("#Plot").html("");
                          $('#Plot').select2({
                              closeOnSelect: false,
                              data : mergedPlot
                          });
                          $("#SubPlot").html("");
                          $('#SubPlot').select2({
                              closeOnSelect: false,
                              data : mergedSubPlot
                          });
                          $("#CensusYear").html("");
                          $('#CensusYear').select2({
                              closeOnSelect: false,
                              data : mergedCensusYear
                          });
                      };
                  });
              });

        $('#datatable').after("<div class=\"loader mx-auto my-4\" />"); // Affiche l'animation loader

        var xhr; // Déclaration de l'objet ajax pour l'utilisation d'abort en cas d'appui sur apply alors que le tableau n'est pas chargé

        /* Gestion des onglets sur les filtres (Jquery UI : https://jqueryui.com/tabs/) */
        $("#tabs").tabs();

        /* Crée les input de selection multiple (Select2 : https://select2.org/) */
        $('.multiple').select2({
            closeOnSelect: false
        });
        /* Animation du slider de choix de la circonférence (Jquery UI : https://jqueryui.com/slider/) */
        $('#slider').slider({
            min: <?php echo $circDBMin ?>,
            max: <?php echo $circDBMax ?>,
            step: 0.01,
            values: [<?php echo $circMin ?>, <?php echo $circMax ?>],
            slide: function(event, ui) {
                for (let i = 0; i < ui.values.length; ++i) {
                    $("input.sliderValue[data-index=" + i + "]").val(ui.values[i]);
                }
            }
        });
        /* Au déplacement du slider, changer la valeur de l'input correspondant */
        $('input.sliderValue').change(function() {
            let $this = $(this);
            $("#slider").slider("values", $this.data("index"), $this.val());
        });

        /* Génère une ligne de la table */
        function drawRow(rowData) {
            var row = $("<tr />");
            $("#datatable").append(row);
            <?php foreach($columns as $columnName){
                $columnName = $columnName['db'];
                echo "row.append($(\"<td>\" + rowData.$columnName + \"</td>\"))\n\t\t";
            } ?>
        }
        /* Utilise les données pour générer une table */
        function createTable(data){
            var body = $("<tbody />");
            $("#datatable").append(body);
            for (let i = 0; i < data.length; i++) {
                drawRow(data[i]);
            }
        }
        /* Supprime le contenu de la table */
        function cleanTable() {
            $("#datatable tbody").remove();
        }

        var circMin, circMax, codeAlive, Plot, SubPlot, CensusYear, VernName, Family, Genus, Species, page, offset; // Variables globales des filtres du document

        /* Enregistre les filtres selectionnés dans des variables */
        function getFilters(){
            circMin = $("#circMin").val();
            circMax = $("#circMax").val();
            codeAlive = $("#CodeAlive").select2('data').map(function (obj) { return obj.text; });
            Plot = $("#Plot").select2('data').map(function (obj) { return obj.text; });
            SubPlot = $("#SubPlot").select2('data').map(function (obj) { return obj.text; });
            CensusYear = $("#CensusYear").select2('data').map(function (obj) { return obj.text; });
            VernName = $("#VernName").select2('data').map(function (obj) { return obj.text; });
            Family = $("#Family").select2('data').map(function (obj) { return obj.text; });
            Genus = $("#Genus").select2('data').map(function (obj) { return obj.text; });
            Species = $("#Species").select2('data').map(function (obj) { return obj.text; });
        }

        /* Placement des tooltips sur les headers du tableau */
        <?php
            foreach($headers as $key=>$value){
                if ($value) {
                    echo "\$(\"th:contains('$key')\").attr(\"data-toggle\",\"tooltip\").attr(\"title\",\"$value\").append(\"<div class='annoted-header'>\");\n"; // Attribue les valeurs de /application/config/datatable.php à chaque header
                }
            }
        ?>

        /* Evènement clic sur save */
        $("#save").click(function(){
            alert("<?php echo base_url() ?>main/?" + decodeURIComponent( $("#formFilters").serialize())); // Génère l'URL correspondant aux filtres séléctionnés
        });
        /* Evènemenent clic sur apply */
        $("#apply").click(function(){
          load_data(1);
        });
        $(document).on("click", ".pagination li a", function(event){
            event.preventDefault();
            var page = $(this).data("ci-pagination-page");
            xhr.abort(); // Abandonne la requête ajax en cours
            load_data(page);
        });

        getFilters();
        xhr = load_data(1);

        function load_data(page) {
            cleanTable(); // Supprime le contenu de la table
            if (!$(".loader").length) {
                $('#datatable').after("<div class=\"loader mx-auto my-4\" />"); // Ajoute l'animation de loader si elle n'est pas déjà présente
            }
            getFilters(); // Enregistre les filtres selectionnés dans les variables globales
            xhr = $.ajax({ // Début de la requête ajax http://api.jquery.com/jquery.ajax/
                url: "<?php echo base_url() ?>main/api_table", // Appelle la page fonction api_table dans application/controller/main.php
                datatype: "json",
                data: { circMin : circMin, circMax : circMax, codeAlive : codeAlive, Plot : Plot, SubPlot : SubPlot, CensusYear : CensusYear, VernName : VernName, Family : Family, Genus : Genus, Species : Species, page : page, offset : offset, apply : "apply"} // Passe les paramètres via la méthode get
            }).done(function(data){ // Evènement données reçues
                data = JSON.parse(data); // Transforme JSON -> Array javascript
                $('#page-selection').html(data.pagination_links);
                $('.loader').remove(); // Supprime l'animation loader
                createTable(data.table); // Ajoute les données à la table
            });
            return xhr;
        }

    });
</script>
<!-- NAVIGATION -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a href=<?php echo base_url();?> class="navbar-brand">Data Paracou</a> <!-- Titre affiché à gauche de la nav -->
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarText">
    <ul class="navbar-nav mr-auto">
        <li class="nav-item"><a class="nav-link" href="https://paracou.cirad.fr/">Website</a></li>
        <li class="nav-item"><a class="nav-link" href="https://paracoumaps.cirad.fr/">Map</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Help</a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href=<?php echo "\"".base_url()."public/pdf/Paracou_data_dictionnary.pdf\"";?>>Data dictionnary</a></li> <!-- Lien vers le data dictionnary -->
          </div>
        </li>
            <!-- Vous pouvez rajouter des liens ici / doc : https://www.w3schools.com/tags/att_a_href.asp -->

    </ul>
    <form class="navbar-text form-inline">
        <?php
        if($role == "admin"){ // Si le role de l'utilisateur est "admin" afficher le lien "Admin" dans la nav
            $url_admin = base_url().'admin/';
            echo "<a class=\"m-3\" href='$url_admin'>Admin</a>";
        } ?>
        <a class="m-2" href="<?php echo base_url().'main/logout/' ?>">Logout</a>
    </form>
  </div>
</nav>
<br>
<div class="container-fluid">
    <div id="tabs" class="card"><!-- https://getbootstrap.com/docs/4.0/components/card/ -->
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                  <a class="nav-link" href="#filters">Filters</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#spatial">Spatial</a>
                </li>
            </ul>
            <div class="card-body">
                    <div id="spatial">

                    </div>
                    <div id="filters">
                        <form id="formFilters" method="get" action="<?php echo base_url().'main/api_table' ?>">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-lg-4 col-xl-4 col-sm-12 col-md-12">
                                <label>Circumference</label><br>
                                <div class="row">
                                    <div class="col">
                                        <label for="circMin">Min</label>
                                        <input type="text" class="sliderValue form-control" name="circMin" id="circMin" data-index="0" value="<?php echo $circMin ?>" />
                                    </div>
                                    <div class="col">
                                        <label for="circMax">Max</label>
                                        <input type="text" class="sliderValue form-control" name="circMax" id="circMax" data-index="1" value="<?php echo $circMax ?>" />
                                    </div>
                                </div>
                                <br>
                                <div id="slider"></div>
                                <br>
                                <label for="CodeAlive[]">Status</label>
                                <select class="multiple form-control" name="CodeAlive[]" id="CodeAlive" multiple="multiple" style="width:100%;">
                                    <?php
                                            /* Récupère les options des filtres passés dans l'URL et les affiche en tant qu'options sélectionnés (pour save state) */
                                            if (isset($get["CodeAlive"])) {
                                                $codeAliveInter = array_intersect($FCodeAlive,$get["CodeAlive"]);
                                            }
                                        foreach ($FCodeAlive as $key=>$status) { // Récupère les options des filtres depuis la base de données (variable data['F'.filters] dans main.php)
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
                                    <select class="multiple form-control" name="Plot[]" id="Plot" multiple="multiple" style="width:100%;">
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
                                    <select class="multiple form-control" name="SubPlot[]" id="SubPlot" multiple="multiple" style="width:100%;">
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
                                    <select class="multiple form-control" name="CensusYear[]" id="CensusYear" multiple="multiple" style="width:100%;">
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
                                    <select class="multiple form-control" id="VernName" name="VernName[]" multiple="multiple" style="width:100%;">
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
                                    <select class="multiple form-control" id="Family" name="Family[]" multiple="multiple" style="width:100%;">
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
                                    <select class="multiple form-control" id="Genus" name="Genus[]" multiple="multiple" style="width:100%;">
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
                                    <select class="multiple form-control" id="Species" name="Species[]" multiple="multiple" style="width:100%;">
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
                            <input class="m-2 mx-auto btn" name="csv" type="submit" value="Export to CSV">
                        </div>
                        </form>
                        <button class="m-2 mx-auto btn" id="apply">Apply</button>
                        <button class="m-2 mx-auto btn" id="save">Save state</button>
                    </div>
            </div>
    </div>
    <br>
    <div class="container">
        <div id="page-selection"></div>
    </div>
</div>
<?php
    $this->table->set_heading(array_keys($headers)); // Génère les headers du tableau fournis dans datatable.php
    echo $this->table->generate();
?>
