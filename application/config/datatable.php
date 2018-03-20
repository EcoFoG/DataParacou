<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$config['headers'] = array(
    "Forest",
    "Plot",
    "Subplot",
    "Tree Field number",
    "Area",
    "Tree id",
    "X Field",
    "Y Field",
    "X UTM",
    "Y UTM",
    "UTM Zone",
    "Latitude",
    "Longitude",
    "Vern name",
    "Wood density",
    "Circumference",
    "Status",
    "Measure code",
    "Year",
    "Measure date",
    "Family",
    "Genus",
    "Specie",
    "Botanical source",
    "Safety index"
);

$config['table_template'] = array (
    'table_open'          => '<table id="guyafor_datatable" class="table table-striped table-bordered" width="100%" style="font-size: 60%;" cellspacing="0">',

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
    "CodeAlive",
    "CensusYear",
    "VernName",
    "Family",
    "Species",
    "Genus"
);

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
    array('db' => 'UTMZone', 'dt' => 10 ),
    array('db' => 'Lat', 'dt' => 11 ),
    array('db' => 'Lon', 'dt' => 12 ),
    array('db' => 'VernName', 'dt' => 13 ),
    array('db' => 'WoodDensity', 'dt' => 14 ),
    array('db' => 'Circ', 'dt' => 15 ),
    array('db' => 'CodeAlive', 'dt' => 16 ),
    array('db' => 'CodeMeas', 'dt' => 17 ),
    array('db' => 'CensusYear', 'dt' => 18 ),
    array('db' => 'CensusDate', 'dt' => 19 ),
    array('db' => 'Family', 'dt' => 20 ),
    array('db' => 'Genus', 'dt' => 21 ),
    array('db' => 'Species', 'dt' => 22 ),
    array('db' => 'BotaSource', 'dt' => 23 ),
    array('db' => 'BotaCertainty', 'dt' => 24 )
);