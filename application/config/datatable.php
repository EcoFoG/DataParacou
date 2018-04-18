<?php

$tableRelativeFontSize = 80; // En %
// Colonnes à aller chercher dans la base de données
$config['columns'] = array(
    array('db' => 'Forest', 'dt' => 0 ),
    array('db' => 'Plot', 'dt' => 1 ),
    array('db' => 'SubPlot', 'dt' => 2 ),
    array('db' => 'TreeFieldNum', 'dt' => 3 ),
    array('db' => 'PlotSurface', 'dt' => 4 ),
    array('db' => 'idTree', 'dt' => 5 ),
    array('db' => 'Xfield', 'dt' => 6 ),
    array('db' => 'Yfield', 'dt' => 7 ),
    array('db' => 'Xutm', 'dt' => 8 ),
    array('db' => 'Yutm', 'dt' => 9 ),
    array('db' => 'Lat', 'dt' => 10 ),
    array('db' => 'Lon', 'dt' => 11 ),
    array('db' => 'VernName', 'dt' => 12 ),
    array('db' => 'Circ', 'dt' => 13 ),
    array('db' => 'CircCorr', 'dt' => 14 ),
    array('db' => 'CodeAlive', 'dt' => 15 ),
    array('db' => 'CodeMeas', 'dt' => 16 ),
    array('db' => 'CensusYear', 'dt' => 17 ),
    array('db' => 'Family', 'dt' => 18 ),
    array('db' => 'Genus', 'dt' => 19 ),
    array('db' => 'Species', 'dt' => 20 ),
    array('db' => 'BotaSource', 'dt' => 21 ),
    array('db' => 'BotaCertainty', 'dt' => 22 )
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
    "Plot",
    "SubPlot",
    "CodeAlive",
    "CensusYear",
    "VernName",
    "Family",
    "Species",
    "Genus"
);

$config['defaultCircBoundaries'] = array(
    'circMin' => 10,
    'circMax' => 150
);