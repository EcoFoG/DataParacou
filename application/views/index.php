<script type="text/javascript">
$(document).ready(function() {
    $('#Circ').prependTo("#Tree"); // Circ ne fait pas partie du tableau filters alors il faut le placer manuellement dans la catégorie Tree
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

        getFilterStates(function(filterStates){
            reloadFilterStatesList(filterStates);
        });
        
        var xhr; // Déclaration de l'objet ajax pour l'utilisation d'abort en cas d'appui sur apply alors que le tableau n'est pas chargé

        $('#filterStates').change(function(){
            let base_url = "<?php echo base_url() ?>";
            let selectedFilterState = $("#filterStates option:selected").val();
            window.location.href = base_url + "main/?" + selectedFilterState;
        });

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
            <?php 
            foreach ($columns as $columnName) {
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

        var <?php foreach ($reducedFilters as $key => $value) {
            echo "$key, ";
        }?>page, offset; // Variables globales des filtres du document

        /* Enregistre les filtres selectionnés dans des variables */
        function getFilters(){
            circMin = $("#circMin").val();
            circMax = $("#circMax").val();
            <?php 
            foreach ($reducedFilters as $key => $value) {
                echo "$key = $('#$key').select2('data').map(function (obj) { return obj.text; });";
            }
            ?>
        }

        /* Placement des tooltips sur les headers du tableau */
        <?php
        foreach ($headers as $key=>$value) {
            if ($value) {
                echo "\$(\"th:contains('$key')\").attr(\"data-toggle\",\"tooltip\").attr(\"title\",\"$value\").append(\"<div class='annoted-header'>\");\n"; // Attribue les valeurs de /application/config/datatable.php à chaque header
            }
        }
        ?>

        $("#save").click(function(){
            saveFilterState();
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
                data: {<?php foreach ($reducedFilters as $key => $value) {
                    echo "$key : $key, ";
                }?>circMin : circMin, circMax : circMax, page : page, offset : offset, apply : "apply"} // Passe les paramètres via la méthode get
            }).done(function(data){ // Evènement données reçues
                data = JSON.parse(data); // Transforme JSON -> Array javascript
                $('#page-selection').html(data.pagination_links);
                $('.loader').remove(); // Supprime l'animation loader
                createTable(data.table); // Ajoute les données à la table
            });
            return xhr;
        }


        /* Save filter state */
        var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>',
            csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

        function saveFilterState() {
            let txt;
            let name = prompt("Please enter the name of your filter configuration : ");
            if (!(name == null || name == "")) {
                let state = decodeURIComponent( $("#formFilters").serialize())
                $.ajax({
                    url : "<?php echo base_url()?>main/saveFilterState",
                    dataType : "json",
                    type : "POST",
                    data : { token : csrfHash, name : name, state : state}
                }).done(function(data){ // Evènement données reçues
                    csrfName = data.csrfName;
                    csrfHash = data.csrfHash;
                    getFilterStates(function(filterStates){
                        reloadFilterStatesList(filterStates);
                    });
                });
            }
        }

        function getFilterStates(callback) {
            $.ajax({
                    url : "<?php echo base_url()?>main/getFilterStates",
                    dataType : "json",
                    type : "GET"
                }).done(function(data){ // Evènement données reçues
                    callback(data);
                });
        }

        function reloadFilterStatesList(filterStates) {
            selectFilterStates = document.getElementById('filterStates');
            
            selectFilterStates.options.length = 1;

            for (let i = 0; i < filterStates.length; i++) {
                selectFilterStates.options.add(new Option(filterStates[i].name, filterStates[i].state));
            }
        }

    });
</script>
<!-- NAVIGATION -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a href="<?php echo base_url();?>main/?Plot[]=6" class="navbar-brand"><?php echo $brandName; ?></a> <!-- Titre affiché à gauche de la nav -->
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
            <a class="dropdown-item" href=<?php echo "\"".base_url()."public/pdf/Paracou_data_dictionnary.pdf\"";?>>Data dictionnary</a> <!-- Lien vers le data dictionnary -->
          </div>
        </li>
        <!-- Vous pouvez rajouter des liens ici / doc : https://www.w3schools.com/tags/att_a_href.asp -->

    </ul>
    <div class="navbar-text form-inline">
        <button class="btn" id="save">Save filters</button>
        <select id="filterStates" class="form-control">
            <option value="">--- Your filters list ---</option>
        </select>
        <?php
        if ($role == "admin") { // Si le role de l'utilisateur est "admin" afficher le lien "Admin" dans la nav
            $url_admin = base_url().'admin/';
            echo "<a class=\"m-3\" href='$url_admin'>Admin</a>";
        } ?>
        <a class="m-2" href="<?php echo base_url().'main/logout/' ?>">Logout</a>
    </div>
  </div>
</nav>
<br>
<div class="container-fluid">
    <div id="tabs" class="card"><!-- https://getbootstrap.com/docs/4.0/components/card/ -->
            <div class="card-header">
                  Filters
            </div>
            <div class="card-body">
                    <div id="filters">
                        <form id="formFilters" method="get" action="<?php echo base_url().'main/api_table' ?>">
                        <div class="form-group">
                            <div class="row">
                                <div id="Circ">
                                    <label>Circumference</label>
                                    <div class="row">
                                        <div class="col">
                                            <label for="circMin">Min</label>
                                            <input type="text" class="sliderValue form-control" name="circMin" id="circMin" data-index="0" value="<?php echo $circMin?>" />
                                        </div>
                                        <div class="col">
                                            <label for="circMax">Max</label>
                                            <input type="text" class="sliderValue form-control" name="circMax" id="circMax" data-index="1" value="<?php echo $circMax?>" />
                                        </div>
                                    </div>
                                    <br>
                                    <div id="slider"></div>
                                    <br>
                                </div>
                                <?php
                                foreach ($filters as $key => $categorie) {
                                    echo '<div id="'.$key.'" class="col-lg col-xl col-sm-12 col-md-12">';
                                    echo "\n";
                                    foreach ($categorie as $dbName => $labelName) {
                                        echo '<label for="'.$dbName.'[]">'.$labelName.'</label>';
                                        echo "\n";
                                        echo '<select class="multiple form-control" name="'.$dbName.'[]" id="'.$dbName.'" multiple="multiple" style="width:100%;">';
                                        echo "\n";
                                        /* Récupère les options des filtres passés dans l'URL et les affiche en tant qu'options sélectionnés (pour save state) */
                                        if (isset($get[$dbName])) {
                                            $inter[$dbName] = array_intersect($dataFilters[$dbName], $get[$dbName]);
                                        }
                                        foreach ($dataFilters[$dbName] as $key2=>$value) { // Récupère les options des filtres depuis la base de données (variable data['dataFilters'] dans main.php)
                                            if (isset($inter[$dbName][$key2]) && $inter[$dbName][$key2] == $dataFilters[$dbName][$key2]) {
                                                echo '<option selected="selected">'.$value.'</option>';
                                            } else {
                                                echo '<option>'.$value.'</option>';
                                            }
                                        }
                                        echo '</select>';
                                        echo "\n";
                                    }
                                    echo '</div>';
                                    echo "\n";
                                }?>
                            </div>
                            <input class="m-2 mx-auto btn" name="csv" type="submit" value="Export to CSV">
                        </div>
                        </form>
                        <button class="m-2 mx-auto btn" id="apply">Apply</button>
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