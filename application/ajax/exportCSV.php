<?php

// DB table to use
$table = 'taparacou';
 
// Table's primary key
$primaryKey = 'idMeas';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array('db' => 'Forest', 'dt' => 0 ),
    array('db' => 'Plot', 'dt' => 1 ),
    array('db' => 'SubPlot', 'dt' => 2 ),
    array('db' => 'NumTree', 'dt' => 3 ),
    array('db' => 'surface', 'dt' => 4 ),
    array('db' => 'idTree', 'dt' => 5 ),
    array('db' => 'X', 'dt' => 6 ),
    array('db' => 'Y', 'dt' => 7 ),
    array('db' => 'Xutm', 'dt' => 8 ),
    array('db' => 'Yutm', 'dt' => 9 ),
    array('db' => 'UTMZone', 'dt' => 10 ),
    array('db' => 'Lat', 'dt' => 11 ),
    array('db' => 'Lon', 'dt' => 12 ),
    array('db' => 'n_essence', 'dt' => 13 ),
    array('db' => 'Vern', 'dt' => 14 ),
    array('db' => 'WoodDensity', 'dt' => 15 ),
    array('db' => 'Circ', 'dt' => 16 ),
    array('db' => 'CodeAlive', 'dt' => 17 ),
    array('db' => 'CodeMeas', 'dt' => 18 ),
    array('db' => 'Year', 'dt' => 19 ),
    array('db' => 'DateMesure', 'dt' => 20 ),
    array('db' => 'Family', 'dt' => 21 ),
    array('db' => 'Genus', 'dt' => 22 ),
    array('db' => 'Species', 'dt' => 23 ),
    array('db' => 'CommercialSp', 'dt' => 24 ),
    array('db' => 'BotaSource', 'dt' => 25 ),
    array('db' => 'InsurIndex', 'dt' => 26 )
);
 
// SQL server connection information
$sql_details = array(
    'user' => 'paracou',
    'pass' => 'zJJxuwH3FQ',
    'db'   => 'paracou',
    'host' => 'paracoumaps.cirad.fr'
);

require( 'ssp.class.php' );

header("Content-type: text/x-csv");
header("Content-Disposition: attachment; filename=".$csv_filename."");
echo SSP::export($_GET, $sql_details, $table, $primaryKey, $columns);