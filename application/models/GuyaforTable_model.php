<?php
class GuyaforTable_model extends CI_Model
{
    private $table_head_info = array(
    "NomForet" => "Forest",
    "n_parcelle" => "Plot",
    "n_carre" => "Subplot",
    "n_arbre" => "Tree number",
    "Surface" => "Area",
    "i_arbre" => "Tree id",
    "X" => "X",
    "Y" => "Y",
    "Xutm" => "X UTM",
    "Yutm" => "Y UTM",
    "UTMZone" => "UTM Zone",
    "Lat" => "Latitude",
    "Lon" => "Longitude",
    "n_essence" => "Vern id",
    "nomPilote" => "Vern name",
    "Densite" => "Density",
    "circonf" => "Circumference",
    "code_vivant" => "Status",
    "code_mesure" => "Measure code",
    "campagne" => "Year",
    "DateMesure" => "Measure date",
    "circ_corr" => "Corrected circumference",
    "code_corr" => "Correction code",
    "Famille" => "Family",
    "Genre" => "Genus",
    "Espece" => "Specie",
    "Commerciale" => "Commercial",
    "SourceBota" => "Botanist source",
    "indSurete" => "Safety index",
    );

    function __construct(){
        parent::__construct();
    }
    public function writeJsons(){
        $row = NULL;
        $this->db->close();
        $this->load->database('guyafor');
        $rowNum = $this->db->query("SELECT dbo.TtGuyaforShiny.idMesure FROM dbo.TtGuyaforShiny LEFT OUTER JOIN dbo.taMesure_Corr ON dbo.TtGuyaforShiny.idMesure = dbo.taMesure_Corr.idMesure WHERE NomForet='Paracou' ORDER BY dbo.TtGuyaforShiny.idMesure")->result_array();
        foreach ($rowNum as $key => $value){
            $rowNum[$key] = $rowNum[$key]["idMesure"];
        }
        $pas = 3000;
        ini_set('max_execution_time', 1000);
        ini_set('memory_limit', "-1");
        unlink(FCPATH."application/ajax/GuyaforTable_brut.json");
        for($i = $rowNum[0]; $i < count($rowNum); $i = $i+$pas){
            $begin = $rowNum[$i];
            $end = (empty($rowNum[$i+($pas-1)])) ? end($rowNum) : $rowNum[$i+($pas-1)];
            $query = "
                    SELECT        dbo.TtGuyaforShiny.NomForet, dbo.TtGuyaforShiny.n_parcelle, dbo.TtGuyaforShiny.n_carre, dbo.TtGuyaforShiny.n_arbre, dbo.TtGuyaforShiny.Surface, 
                                  dbo.TtGuyaforShiny.i_arbre, dbo.TtGuyaforShiny.X, dbo.TtGuyaforShiny.Y, dbo.TtGuyaforShiny.Xutm, dbo.TtGuyaforShiny.Yutm, dbo.TtGuyaforShiny.UTMZone, 
                                  dbo.TtGuyaforShiny.Lat, dbo.TtGuyaforShiny.Lon, dbo.TtGuyaforShiny.n_essence, dbo.TtGuyaforShiny.nomPilote, 
                                  dbo.TtGuyaforShiny.Densite, dbo.TtGuyaforShiny.circonf, dbo.TtGuyaforShiny.code_vivant, dbo.TtGuyaforShiny.code_mesure, dbo.TtGuyaforShiny.campagne, 
                                  dbo.taMesure_Corr.circ_corr, dbo.taMesure_Corr.code_corr, dbo.TtGuyaforShiny.Famille, dbo.TtGuyaforShiny.Genre, 
                                  dbo.TtGuyaforShiny.Espece, dbo.TtGuyaforShiny.Commerciale, dbo.TtGuyaforShiny.SourceBota, dbo.TtGuyaforShiny.indSurete
                    FROM         dbo.TtGuyaforShiny 
                    LEFT OUTER JOIN dbo.taMesure_Corr ON dbo.TtGuyaforShiny.idMesure = dbo.taMesure_Corr.idMesure 
                    WHERE dbo.TtGuyaforShiny.NomForet='Paracou' AND dbo.TtGuyaforShiny.idMesure BETWEEN {$begin} AND {$end} ";
            $rows = $this->db->query($query)->result_array();
            $line = json_encode($rows,JSON_HEX_QUOT | JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
            file_put_contents(FCPATH."application/ajax/GuyaforTable_brut.json", $line, FILE_APPEND | LOCK_EX);
        }
        $charMappings = [
            '][' => ',',
        ];
        $content = file_get_contents(FCPATH."application/ajax/GuyaforTable_brut.json");
        file_put_contents(FCPATH."application/ajax/GuyaforTable_brut.json", strtr($content, $charMappings));
        ini_set('max_execution_time', 30);
        ini_set('memory_limit', "512M");

    }

    public function getCount(){
        $this->db->close();
        $this->load->database('guyafor');
        $count = $this->db->query("SELECT COUNT(dbo.TtGuyaforShiny.idMesure) FROM dbo.TtGuyaforShiny LEFT OUTER JOIN dbo.taMesure_Corr ON dbo.TtGuyaforShiny.idMesure = dbo.taMesure_Corr.idMesure WHERE NomForet='Paracou'")->result_array();
        $count = $count[0][""];
        return $count;
    }

    public function formatTable($data){
        foreach($data as $key => $value) {
            foreach($value as $key2 => $value2){
                switch ($key2){
                    case "Densite" :
                        $data[$key][$key2] = round($value2, 3);
                        break;
                    case "circ_corr" :
                        $data[$key][$key2] = round($value2, 1);
                        break;
                    case "n_arbre" :
                        $data[$key][$key2] = round($value2, 2);
                        break;
                    case "X" :
                        $data[$key][$key2] = round($value2, 2);
                        break;
                    case "Y" :
                        $data[$key][$key2] = round($value2, 2);
                        break;
                    case "indSurete" :
                        $data[$key][$key2] = round($value2, 0);
                        break;
                    case "nomPilote" :
                        $data[$key][$key2] = utf8_encode(ucfirst($value2));
                        break;
                    case "Espece" :
                        $data[$key][$key2] = utf8_encode(ucfirst($value2));
                        break;
                    case "code_vivant" :
                        $data[$key][$key2] = ($value2) ? 'Alive' : 'Dead';
                        break;

                }
            }
        }
        return $data;
    }

    public function getTable(){

    }

    public function getTableHeadInfo(){
        return $this->table_head_info;
    }

}