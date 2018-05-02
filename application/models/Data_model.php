<?php
class Data_model extends CI_Model {

    private $paracouDB;

    function __construct(){
        // Call the Model constructor
        parent::__construct();
        $this->paracouDB = $this->load->database('paracou', TRUE);
    }
    #### Génère la chaine de caractère $like pour le filtrage ####
    private function like($filters, $get){
        foreach($filters as $key => $value) {
            if(isset($get[$value])){
                $str = implode("|", $get[$value]);
            } else {
                $str='';
            }
            if ($str != '') {
                $binding = $str;
                $like[$key] = "CAST(\"".$value."\" AS TEXT) SIMILAR TO '".$binding."'";
            }
        }
        $like = implode(" AND ",$like);
        $like = " AND ".$like;
        return $like;
    }

    private function generateLikeString($filters = NULL, $get = NULL){
      #### Create like string for the query ####
      $flag = count($filters);
      foreach($filters as $value){
         $flag = (isset($get[$value])) ? $flag-1: $flag;
      }
      $like = (count($filters) > $flag) ? $this->like($filters,$get) : ''; // Empty chain in $like if no filter is select
      return $like;
    }

    private function generateLimitString($offset = NULL, $n_limit = NULL){
      $limit = " LIMIT $n_limit OFFSET $offset";
      return $limit;
    }

    private function generateQueryString($columns = NULL, $filters, $get, $circMin, $circMax){
      $like = $this->generateLikeString($filters,$get);
      #### Query ####
      $query =   "SELECT \"".implode("\", \"", $columns)."\" " // implode : http://php.net/manual/fr/function.implode.php
        . "FROM taparacou "
        . "WHERE \"Circ\" BETWEEN $circMin AND $circMax "
        . "$like "
        . "ORDER BY \"Plot\",\"SubPlot\",\"TreeFieldNum\",\"CensusYear\"";
      return $query;
    }

    public function getTable($filters, $get, $columns, $circMin, $circMax, $offset, $n_limit){
      $limit = $this->generateLimitString($offset,$n_limit);
      $query = $this->generateQueryString($columns, $filters, $get, $circMin, $circMax);

      $query .= $limit;
      $data = $this->paracouDB->query($query)->result_array();

      return $data;
    }

    public function getCsv($filters, $get, $columns, $circMin, $circMax){
      $query = $this->generateQueryString($columns, $filters, $get, $circMin, $circMax);
      $csv = $this->dbutil->csv_from_result($this->paracouDB->query($query));
      return $csv;
    }

    public function getNumRows($columns, $filters, $get, $circMin, $circMax){
      $query = $this->generateQueryString($columns, $filters, $get, $circMin, $circMax);
      $num_rows = $this->paracouDB->query($query)->num_rows(); // Getting the number of rows for pagination
      return $num_rows;
    }

    public function getCircBoundaries(){
      $min_tmp = $this->paracouDB->query("SELECT min(\"Circ\") FROM taparacou")->row();
      $circBoundaries['circDBMin'] = $min_tmp->min;
      $max_tmp = $this->paracouDB->query("SELECT max(\"Circ\") FROM taparacou")->row();
      $circBoundaries['circDBMax'] = $max_tmp->max;
      return $circBoundaries;
    }
}
