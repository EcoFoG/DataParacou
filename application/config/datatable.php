<?php
$config['brandName'] = "Paracou Data";
// Colonnes à aller chercher dans la base de données
$config['columns'] = array(
    'Forest',
    'Plot',
    'SubPlot',
    'TreeFieldNum',
    'PlotSurface',
    'idTree',
    'Xfield',
    'Yfield',
    'Xutm',
    'Yutm',
    'Lat',
    'Lon',
    'VernName',
    'Circ',
    'CircCorr',
    'CodeAlive',
    'CodeMeas',
    'CensusYear',
    'Family',
    'Genus',
    'Species',
    'BotaSource',
    'BotaCertainty'
  );

// Nom des headers (à ordonner par rapport aux colonnes),
// "<Nom colonne>" => "<Annotation au survol de la souris>"
$config["headers"] = array(
    "Forest" => "",
    "Plot" => "Each plot is subdivided into 4 subplots (125x125m), excepted for plot 16 (25 subplots (100x100m))",
    "Subplot" => "",
    "Tree Field number" => "Tree number as labelled in the field",
    "Plot area" => "in ha",
    "Tree id" => "Unique id for each tree in the database",
    "X Field" => "position (in m within the subplot, taken from the SO corner)",
    "Y Field" => "position (in m within the subplot, taken from the SO corner)",
    "X UTM (Zone 22)" => "",
    "Y UTM (Zone 22)" => "",
    "Latitude" => "",
    "Longitude" => "",
    "Vern name" => "",
    "Circumference" => "",
    "Circumference corrected" => "",
    "Status" => "0 = dead ; 1 = alive",
    "Measure code" => "",
    "Year of census" => "",
    "Family" => "",
    "Genus" => "",
    "Species" => "",
    "Botanical source" => "Bota = a botanist has identified the tree ; Vern = botanical name is obtained from Vern Name",
    "Safety index"  => "-1 no identification ; 0 unknown family ; 1 unknown genus ; 2 unknown species ; 3 known species but low confidence ; 4 known species with high confidence"
);

$tableRelativeFontSize = 80; // Taille relative de la table en % (modifie aussi la taille de la police)

$config['table_template'] = array (
    'table_open'          => "<table id=\"datatable\" class=\"table table-responsive table-striped table-bordered\" width=\"100%\" style=\"font-size: $tableRelativeFontSize%;\" cellspacing=\"0\">",

    'heading_row_start'   => '<tr>',
    'heading_row_end'     => '</tr>',
    'heading_cell_start'  => '<th>',
    'heading_cell_end'    => '</th>',

    'row_start'           => '<tr>',
    'row_end'             => '</tr>',
    'cell_start'          => '<td>',
    'cell_end'            => '</td>',

    'row_alt_start'       => '<tr>',
    'row_alt_end'         => '</tr>',
    'cell_alt_start'      => '<td>',
    'cell_alt_end'        => '</td>',

    'table_close'         => '</table>'
);

$config['filters'] = array(
    "Tree" => array(
        "CodeAlive" => "Status"
    ),
    "Division" => array(
        "Forest" => "Forest",
        "Plot" => "Plot",
        "SubPlot" => "Subplot",
        "CensusYear" => "Census year"
    ),
    "Taxon" => array(
        "VernName" => "Vernacular name",
        "Family" => "Family",
        "Genus" => "Genus",
        "Species" => "Species"
    )
);

$config['defaultCircBoundaries'] = array(
    'circMin' => 10,
    'circMax' => 400
);
