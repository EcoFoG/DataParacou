<?php
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "idMesure";

    /* DB table to use */
    $sTable = "TtGuyaforShiny";

    /* Database connection information */
    $gaSql['user']       = "SQL.PlatForm";
    $gaSql['password']   = "az23qb9!";
    $gaSql['db']         = "Guyafor";
    $gaSql['server']     = "sql.ecofog.gf";

    /*
    * Columns
    * If you don't want all of the columns displayed you need to hardcode $aColumns array with your elements.
    * If not this will grab all the columns associated with $sTable
    */
    $aColumns =  array('NomForet',
        'n_parcelle',
        'n_carre',
        'n_arbre',
        'Surface',
        'i_arbre',
        'X',
        'Y',
        'Xutm',
        'Yutm',
        'UTMZone',
        'Lat',
        'Lon',
        'n_essence',
        'nomPilote',
        'Densite',
        'circonf',
        'code_vivant',
        'code_mesure',
        'campagne',
        'DateMesure',
        'Famille',
        'Genre',
        'Espece',
        'Commerciale',
        'SourceBota',
        'indSurete');

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * If you just want to use the basic configuration for DataTables with PHP server-side, there is
     * no need to edit below this line
     */

    /*
     * ODBC connection
     */
    $connectionInfo = array("UID" => $gaSql['user'], "PWD" => $gaSql['password'], "Database"=>$gaSql['db'],"ReturnDatesAsStrings"=>true);
    $gaSql['link'] = sqlsrv_connect( $gaSql['server'], $connectionInfo);
    $params = array();
    $options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
    //print_r($_GET['columns']);


    /* Ordering */
    $sOrder = "";
    if ( isset( $_GET['iSortCol_0'] ) ) {
        $sOrder = "ORDER BY  ";
        for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ) {
            if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" ) {
                $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
                    ".addslashes( $_GET['sSortDir_'.$i] ) .", ";
            }
        }
        $sOrder = substr_replace( $sOrder, "", -2 );
        if ( $sOrder == "ORDER BY" ) {
            $sOrder = "";
        }
    }

    /* Filtering */
    $sWhere = "";
    if ( isset($_GET['search']['value']) && $_GET['search']['value'] != "" ) {
        $sWhere = "WHERE (";
        for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
            $sWhere .= $aColumns[$i]." LIKE '%".addslashes( $_GET['search']['value'] )."%' OR ";
        }
        $sWhere = substr_replace( $sWhere, "", -3 );
        $sWhere .= ')';
    }
    /* Individual column filtering */
    for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
        if ( isset($_GET['columns'][$i]['searchable']) && $_GET['columns'][$i]['searchable'] == "true" && $_GET['search']['value'] != '' )  {
            if ( $sWhere == "" ) {
                $sWhere = "WHERE ";
            } else {
                $sWhere .= " AND ";
            }
            $sWhere .= $aColumns[$i]." LIKE '%".addslashes($_GET['search']['value'])."%' ";
        }
    }

    /* Paging */
    $sEcho = $_GET['draw'];
    $top = (isset($_GET['start']))?((int)$_GET['start']):0 ;
    $limit = (isset($_GET['length']))?((int)$_GET['length'] ):10;
    $sQuery = "SELECT TOP $limit ".implode(",",$aColumns)."
        FROM $sTable
        $sWhere ".(($sWhere=="")?" WHERE ":" AND ")." $sIndexColumn NOT IN
        (
            SELECT $sIndexColumn FROM
            (
                SELECT TOP $top ".implode(",",$aColumns)."
                FROM $sTable
                $sWhere
                $sOrder
            )
            as [virtTable]
        )
        $sOrder";
    $rResult = sqlsrv_query($gaSql['link'],$sQuery) or die("$sQuery: " . sqlsrv_errors());

    $sQueryCnt = "SELECT * FROM $sTable $sWhere";
    $rResultCnt = sqlsrv_query( $gaSql['link'], $sQueryCnt ,$params, $options) or die (" $sQueryCnt: " . sqlsrv_errors());
    $iFilteredTotal = sqlsrv_num_rows( $rResultCnt );

    $sQuery = " SELECT * FROM $sTable ";
    $rResultTotal = sqlsrv_query( $gaSql['link'], $sQuery ,$params, $options) or die(sqlsrv_errors());
    $iTotal = sqlsrv_num_rows( $rResultTotal );

    $output = array(
        "sEcho" => $sEcho,
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );


    while ( $aRow = sqlsrv_fetch_array( $rResult ) ) {
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
            if ( $aColumns[$i] != ' ' ) {
                $v = $aRow[ $aColumns[$i] ];
                $v = mb_check_encoding($v, 'UTF-8') ? $v : utf8_encode($v);
                $row[]=$v;
            }
        }
        If (!empty($row)) { $output['aaData'][] = $row; }
    }
    echo json_encode( $output );
?>
