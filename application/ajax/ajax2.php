<?php
 
/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */
 
// DB table to use
$table = 'taparacou';
 
// Table's primary key
$primaryKey = 'idMeasure';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
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
    array('db' => 'WoodDensity', 'dt' => 13 ),
    array('db' => 'Circ', 'dt' => 14 ),
    array('db' => 'CodeAlive', 'dt' => 15 ),
    array('db' => 'CodeMeas', 'dt' => 16 ),
    array('db' => 'CensusYear', 'dt' => 17 ),
    array('db' => 'CensusDate', 'dt' => 18 ),
    array('db' => 'Family', 'dt' => 19 ),
    array('db' => 'Genus', 'dt' => 20 ),
    array('db' => 'Species', 'dt' => 21 ),
    array('db' => 'BotaSource', 'dt' => 22 ),
    array('db' => 'BotaCertainty', 'dt' => 23 )
);
 
// SQL server connection information
$sql_details = array(
    'user' => 'paracou',
    'pass' => 'zJJxuwH3FQ',
    'db'   => 'paracou',
    'host' => 'paracoumaps.cirad.fr'
);
 
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
 
require( 'ssp.class.php' );
 
echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);