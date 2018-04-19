<?php
class Cron extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->driver('cache', array('adapter' => 'file'));
        $this->load->dbutil();
    }

    public function main() {
        if(!$this->input->is_cli_request())
        {
            echo "Cron jobs can only be accessed from the command line";
            return;
        }

        $paracouDB = $this->load->database('paracou', TRUE);

        $this->config->load("datatable");
        $filters =  $this->config->item("filters");
        foreach ($filters as $value) {
            $data['F'.$value] = $this->cache->get('F'.$value);
            if (!($data['F'.$value])) {
                $temp = $paracouDB->query("select \"$value\" from taparacou group by \"$value\" order by \"$value\"")->result_array();
                foreach($temp as $key2=>$value2) {
                    $temp[$key2] = $temp[$key2][$value];
                }
                $data['F'.$value] = $temp;
                $this->cache->save('F'.$value, $data['F'.$value], 0);
            }
        }

        $Plot = $this->cache->get('FPlot');
        $CensusYear = $this->cache->get('FCensusYear');
        $Family = $this->cache->get('FFamily');
        $Species = $this->cache->get('FSpecies');
        $Genus = $this->cache->get('FGenus');
        $VernName = $this->cache->get('FVernName');

        $SubPlotByPlot = array();
        $PlotByCensusYear = array();
        $GenusSpeciesByFamily = array();
        $GenusFamilyBySpecies = array();
        $SpeciesFamilyByGenus = array();
        $FamilyGenusSpeciesByVernName = array();

        foreach($CensusYear as $value){
            $temp = $paracouDB->query("select \"Plot\" from taparacou where \"CensusYear\"='$value' group by \"Plot\" order by \"Plot\"")->result_array();
            foreach($temp as $key2=>$value2) {
                $temp[$key2] = $temp[$key2]["Plot"];
            }
            $PlotByCensusYear[$value] = $temp;
        }
        print_r($PlotByCensusYear);
        $this->cache->save('PlotByCensusYear', $PlotByCensusYear, 0);

        foreach($Plot as $value){
            $temp = $paracouDB->query("select \"CensusYear\" from taparacou where \"Plot\"='$value' group by \"CensusYear\" order by \"CensusYear\"")->result_array();
            foreach($temp as $key2=>$value2) {
                $temp[$key2] = $temp[$key2]["CensusYear"];
            }
            $CensusYearByPlot[$value] = $temp;
        }
        print_r($CensusYearByPlot);
        $this->cache->save('CensusYearByPlot', $CensusYearByPlot, 0);

        foreach($Plot as $value){
            $temp = $paracouDB->query("select \"SubPlot\" from taparacou where \"Plot\"='$value' group by \"SubPlot\" order by \"SubPlot\"")->result_array();
            foreach($temp as $key2=>$value2) {
                $temp[$key2] = $temp[$key2]["SubPlot"];
            }
            $SubPlotByPlot[$value] = $temp;
        }
        print_r($SubPlotByPlot);
        $this->cache->save('SubPlotByPlot', $SubPlotByPlot, 0);

        foreach($Plot as $value){
            $temp = $paracouDB->query("select \"CensusYear\" from taparacou where \"Plot\"='$value' group by \"CensusYear\" order by \"CensusYear\"")->result_array();
            foreach($temp as $key2=>$value2) {
                $temp[$key2] = $temp[$key2]["CensusYear"];
            }
            $SubPlotByPlot[$value] = $temp;
        }
        print_r($CensusYearByPlot);
        $this->cache->save('CensusYearByPlot', $CensusYearByPlot, 0);

        foreach($Family as $value){
            $temp['Genus'] = $paracouDB->query("select \"Genus\" from taparacou where \"Family\"='$value' group by \"Genus\" order by \"Genus\"")->result_array();
            foreach ($temp['Genus'] as $key2=>$value2){
                $temp['Genus'][$key2] = $temp['Genus'][$key2]['Genus'];
            }
            $temp['Species'] = $paracouDB->query("select \"Species\" from taparacou where \"Family\"='$value' group by \"Species\" order by \"Species\"")->result_array();
            foreach ($temp['Species'] as $key2=>$value2){
                $temp['Species'][$key2] = $temp['Species'][$key2]['Species'];
            }
            $GenusSpeciesByFamily[$value] = $temp;
        }
        print_r($GenusSpeciesByFamily);
        $this->cache->save('GenusSpeciesByFamily', $GenusSpeciesByFamily, 0);

        foreach($Genus as $value){
            $temp['Family'] = $paracouDB->query("select \"Family\" from taparacou where \"Genus\"='$value' group by \"Family\" order by \"Family\"")->result_array();
            foreach ($temp['Family'] as $key2=>$value2){
                $temp['Family'][$key2] = $temp['Family'][$key2]['Family'];
            }
            $temp['Species'] = $paracouDB->query("select \"Species\" from taparacou where \"Genus\"='$value' group by \"Species\" order by \"Species\"")->result_array();
            foreach ($temp['Species'] as $key2=>$value2){
                $temp['Species'][$key2] = $temp['Species'][$key2]['Species'];
            }
            $SpeciesFamilyByGenus[$value] = $temp;
        }
        print_r($SpeciesFamilyByGenus);
        $this->cache->save('SpeciesFamilyByGenus', $SpeciesFamilyByGenus, 0);

        foreach($Species as $value){
            $temp['Family'] = $paracouDB->query("select \"Family\" from taparacou where \"Species\"='$value' group by \"Family\" order by \"Family\"")->result_array();
            foreach ($temp['Family'] as $key2=>$value2){
                $temp['Family'][$key2] = $temp['Family'][$key2]['Family'];
            }
            $temp['Genus'] = $paracouDB->query("select \"Genus\" from taparacou where \"Species\"='$value' group by \"Genus\" order by \"Genus\"")->result_array();
            foreach ($temp['Genus'] as $key2=>$value2){
                $temp['Genus'][$key2] = $temp['Genus'][$key2]['Genus'];
            }

            $GenusFamilyBySpecies[$value] = $temp;
        }
        print_r($GenusFamilyBySpecies);
        $this->cache->save('GenusFamilyBySpecies', $GenusFamilyBySpecies, 0);

        foreach($VernName as $value){
            $temp['Family'] = $paracouDB->query("select \"Family\" from taparacou where \"VernName\"='$value' group by \"Family\" order by \"Family\"")->result_array();
            foreach ($temp['Family'] as $key2=>$value2){
                $temp['Family'][$key2] = $temp['Family'][$key2]['Family'];
            }
            $temp['Genus'] = $paracouDB->query("select \"Genus\" from taparacou where \"VernName\"='$value' group by \"Genus\" order by \"Genus\"")->result_array();
            foreach ($temp['Genus'] as $key2=>$value2){
                $temp['Genus'][$key2] = $temp['Genus'][$key2]['Genus'];
            }
            $temp['Species'] = $paracouDB->query("select \"Species\" from taparacou where \"VernName\"='$value' group by \"Species\" order by \"Species\"")->result_array();
            foreach ($temp['Species'] as $key2=>$value2){
                $temp['Species'][$key2] = $temp['Species'][$key2]['Species'];
            }

            $FamilyGenusSpeciesByVernName[$value] = $temp;
        }
        print_r($FamilyGenusSpeciesByVernName);
        $this->cache->save('FamilyGenusSpeciesByVernName', $FamilyGenusSpeciesByVernName, 0);


    }

}
