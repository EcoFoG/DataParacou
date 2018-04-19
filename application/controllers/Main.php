<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

    public $status;
    public $roles;

    function __construct(){ // Constructeur http://php.net/manual/fr/language.oop5.decon.php
        parent::__construct();

        // Appelle les modèles depuis application/models/
        $this->load->model('User_model', 'user_model', TRUE);
        $this->load->model('Request_model', 'request_model', TRUE);

        // Appelle les items "roles" et "status" du fichier application/config/config.php
        $this->status = $this->config->item('status');
        $this->roles = $this->config->item('roles');

        // Appelle les librairies
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('download');
        $this->load->helper('date');
        $this->load->library('email');
        $this->load->library('session');
        $this->load->library('table');
        $this->load->driver('cache', array('adapter' => 'file'));
        $this->load->library('pagination');
        $this->load->dbutil();

    }
    // Redirections
    protected function checkLogin(){
        if($this->input->post("admin")){ // Si lien "Admin" pressé redirige vers le panel admin
            redirect(base_url().'admin');
        }
        if($this->input->post("disconnect")){ // Si lien "Logout" pressé redirige vers la fonction logout()
            redirect(base_url().'main/logout/');
        }
        // Redirection vers login() si la session n'existe pas ou si le comtpe est expiré
        if(empty($this->session->userdata['email']) || ((isset($this->session->userdata['expires'])) && (time() > strtotime(str_replace('/', '-', $this->session->userdata['expires']))))){
            redirect(base_url().'main/login/');
        }
        return $this->session->userdata('role');
    }

    // Put config files in variables
    private function configTable(&$data, &$filters, &$columns){
        /* Configuration of the data table in application/config/datatable.php */
        $this->config->load("datatable");
        $tmpl = $this->config->item("table_template");
        $data['headers'] = $this->config->item("headers");
        $data['columns'] = $this->config->item("columns");
        $data['tooltips'] = $this->config->item("tooltips");
        $data['defaultCircBoundaries'] = $this->config->item("defaultCircBoundaries");
        $filters = $this->config->item("filters");
        $columns = $this->config->item("columns");
        $this->table->set_template($tmpl); // Apply template to the generated table
    }
    private function getFilters(&$data, $filters, $paracouDB){
            foreach ($filters as $value) { // Pour chaque filtre dans application/config/datatable.php
                $data['F'.$value] = $this->cache->get('F'.$value); // Cherche dans le cache si les niveaux de filtres ne sont pas déjà enregistrés
                if (!($data['F'.$value])) { // Si ils n'existent pas
                    $temp = $paracouDB->query("select \"$value\" from taparacou group by \"$value\" order by \"$value\"")->result_array(); // Prend les niveaux dans la base de données
                    foreach($temp as $key2=>$value2) {
                        $temp[$key2] = $temp[$key2][$value];
                    }
                    $data['F'.$value] = $temp; // Enregistre dans un tableau avec la clé F*filtre* (FCensusYear, FGenus ...)
                    $this->cache->save('F'.$value, $data['F'.$value], 0); // Enregistre dans le cache
                }
            }
            $data['filters'] = $filters;
    }
    private function getCircBoundaries(&$data, $paracouDB){
            $circDBMin = $this->cache->get('circDBMin');
            $circDBMax = $this->cache->get('circDBMin');
            if (!$circDBMin || !$circDBMax) {
                $min_tmp = $paracouDB->query("SELECT min(\"Circ\") FROM taparacou")->row();
                $data['circDBMin'] = $min_tmp->min;
                $max_tmp = $paracouDB->query("SELECT max(\"Circ\") FROM taparacou")->row();
                $data['circDBMax'] = $max_tmp->max;
                $this->cache->save('circDBMin', $circDBMin, 86400);
                $this->cache->save('circDBMax', $circDBMax, 86400);
            } else {
                $data['circDBMax'] = $circDBMax;
                $data['circDBMin'] = $circDBMin;
            }

    }

    /* Retourne une chaine de caractère $limit qui servira pour la requête pour la pagination et le nombre de lignes affiché à l'écran */
    private function limit($offset, $n_limit){
        $limit = " LIMIT $n_limit OFFSET $offset";
        return $limit;
    }
    /* Crée les liens de pagination à partir du nombre de lignes et du nombre de ligne à afficher */
    private function paginate(&$data, $total_rows, $n_limit){
        $this->config->load("pagination");
        $conf_pagination = $this->config->item("pagination");
        $conf_pagination['base_url'] = base_url()."main/" ;
        $conf_pagination['total_rows'] = $total_rows;
        $conf_pagination['per_page'] = $n_limit;
        $this->pagination->initialize($conf_pagination);
        $data["pagination_links"] = $this->pagination->create_links();
    }

    public function index(){
        $filters = $columns = $data = array();

        $data['role'] = $this->checkLogin();

        #### get GET method variables ####
        $get = $this->input->get(NULL, FALSE);

        $data["get"] = $get;

        #### Configuration de la base de données dans application/config/database.php ####
        $paracouDB = $this->load->database('paracou', TRUE);
        $this->configTable($data, $filters, $columns);

        #### Get levels of filters in the databases ####
        $this->getFilters($data, $filters, $paracouDB);

        #### Get Circ boundaries in the database ####
        $this->getCircBoundaries($data,$paracouDB);

        #### Views ####
        $this->load->view('header');
        $this->load->view('index', $data);
        $this->load->view('footer');
    }

        #### Génère un JSON pour le javascript de application/views/index.php ####
        public function api_table(){

            $this->checkLogin();

            $get = $this->input->get(NULL, FALSE);

            $paracouDB = $this->load->database('paracou', TRUE);

            $filters = $columns = $data = array();
            $this->configTable($data, $filters, $columns);

            #### Set default circMax and circMin ####
            $circMax = isset($get['circMax']) ? $get['circMax'] : $data['defaultCircBoundaries']['circMax'];
            $circMin = isset($get['circMin']) ? $get['circMin'] : $data['defaultCircBoundaries']['circMin'];

            #### Create like string for the query ####
            $flag = count($filters);
            foreach($filters as $value){
               $flag = (isset($get[$value])) ? $flag-1: $flag;
            }
            $like = (count($filters) > $flag) ? $this->like($filters,$get) : ''; // Empty chain in $like if no filter is select

            #### Query ####
            $query =   "SELECT \"".implode("\", \"", $this->pluck($columns, 'db'))."\" " // implode : http://php.net/manual/fr/function.implode.php
              . "FROM taparacou "
              . "WHERE \"Circ\" BETWEEN $circMin AND $circMax "
              . "$like "
              . "ORDER BY \"Plot\",\"SubPlot\",\"TreeFieldNum\",\"CensusYear\"";

            #### Create limit string for the query ####
            $offset = isset($get["page"]) ? $get["page"] : 1;
            $n_limit = isset($get['limit']) ? $get['limit'] : 50;
            $limit = $this->limit($offset,$n_limit);

            #### Génère le CSV si l'input "CSV" existe ####
            if(isset($get["csv"])){
                $time = time();
                $name = "Paracou".mdate("%Y%m%d",$time).".csv"; // Name of the CSV
                $csv = $this->dbutil->csv_from_result($paracouDB->query($query));
                force_download($name, $csv);
            #### Sinon génère la table ####
            } else {
                $total_rows = $paracouDB->query($query)->num_rows(); // Getting the number of rows for pagination
                $query .= "$limit" ;

                $json["num_rows"] = $total_rows;
                $json["table"] = $paracouDB->query($query)->result_array();

                echo json_encode($json);
            }
        }

        #### Génère un JSON pour le javascript de application/views/index.php ####
        public function api_filters()
	{
            $this->checkLogin();

            $get = $this->input->get();
            $cache['VernNamesSelected'] = $this->cache->get('FamilyGenusSpeciesByVernName');
            $cache['FamiliesSelected'] = $this->cache->get('GenusSpeciesByFamily');
            $cache['GenusSelected'] = $this->cache->get('SpeciesFamilyByGenus');
            $cache['SpeciesSelected'] = $this->cache->get('GenusFamilyBySpecies');
            $cache['SubPlot'] = $this->cache->get('SubPlotByPlot');
            $cache['CensusYear'] = $this->cache->get('CensusYearByPlot');
            $cache['Plot'] = $this->cache->get('PlotByCensusYear');


            if (isset($get['VernNamesSelected'])) {
                foreach($get['VernNamesSelected'] as $value){
                        $VernNamesSelected[$value] = $cache['VernNamesSelected'][$value];
                }
                $temp['VernNamesSelected']['Family'] = call_user_func_array('array_merge',array_column($VernNamesSelected,'Family'));
                $temp['VernNamesSelected']['Genus'] = call_user_func_array('array_merge',array_column($VernNamesSelected,'Genus'));
                $temp['VernNamesSelected']['Species'] = call_user_func_array('array_merge',array_column($VernNamesSelected,'Species'));
            }

            if (isset($get['FamiliesSelected'])) {
                foreach($get['FamiliesSelected'] as $value){
                        $FamiliesSelected[$value] = $cache['FamiliesSelected'][$value];
                }
                $temp['FamiliesSelected']['Genus'] = call_user_func_array('array_merge',array_column($FamiliesSelected,'Genus'));
                $temp['FamiliesSelected']['Species'] = call_user_func_array('array_merge',array_column($FamiliesSelected,'Species'));
            }

            if (isset($get['GenusSelected'])) {
                foreach($get['GenusSelected'] as $value){
                        $GenusSelected[$value] = $cache['GenusSelected'][$value];
                }
                $temp['GenusSelected']['Family'] = call_user_func_array('array_merge',array_column($GenusSelected,'Family'));
                $temp['GenusSelected']['Species'] = call_user_func_array('array_merge',array_column($GenusSelected,'Species'));
            }
            if (isset($get['SpeciesSelected'])) {
                foreach($get['SpeciesSelected'] as $value){
                        $SpeciesSelected[$value] = $cache['SpeciesSelected'][$value];
                }
                $temp['SpeciesSelected']['Family'] = call_user_func_array('array_merge',array_column($SpeciesSelected,'Family'));
                $temp['SpeciesSelected']['Genus'] = call_user_func_array('array_merge',array_column($SpeciesSelected,'Genus'));
            }

            if (isset($get['PlotsSelected'])) {
                foreach($get['PlotsSelected'] as $key=>$value){
                         $tempSubPlot[$value] = $cache['SubPlot'][$value];
                         $tempCensusYear[$value] = $cache['CensusYear'][$value];
                }
                $output['SubPlot'] = array_unique(call_user_func_array('array_merge',$tempSubPlot));
                $output['CensusYear'] = array_unique(call_user_func_array('array_merge',$tempCensusYear));
            }

            if (isset($get['CensusYearsSelected'])) {
                foreach($get['CensusYearsSelected'] as $key=>$value){
                         $tempPlot[$value] = $cache['Plot'][$value];
                }
                $output['Plot'] = array_unique(call_user_func_array('array_merge',$tempPlot));
            }

//            How to intersect array only if they exists
//            https://stackoverflow.com/questions/49694616/how-to-intersect-array-only-if-they-exists/49696487#49696487

            $output['Family'] = (isset($temp['VernNamesSelected']['Family']) && isset($temp['GenusSelected']['Family']) && isset($temp['SpeciesSelected']['Family']) ? array_intersect($temp['VernNamesSelected']['Family'],$temp['GenusSelected']['Family'],$temp['SpeciesSelected']['Family']) :
                (isset($temp['VernNamesSelected']['Family']) && isset($temp['GenusSelected']['Family']) && !isset($temp['SpeciesSelected']['Family']) ? array_intersect($temp['VernNamesSelected']['Family'],$temp['GenusSelected']['Family']) :
                (isset($temp['VernNamesSelected']['Family']) && !isset($temp['GenusSelected']['Family']) && isset($temp['SpeciesSelected']['Family']) ? array_intersect($temp['VernNamesSelected']['Family'],$temp['SpeciesSelected']['Family']) :
                (!isset($temp['VernNamesSelected']['Family']) && isset($temp['GenusSelected']['Family']) && isset($temp['SpeciesSelected']['Family']) ? array_intersect($temp['GenusSelected']['Family'],$temp['SpeciesSelected']['Family']) :
                (isset($temp['VernNamesSelected']['Family']) && !isset($temp['GenusSelected']['Family']) && !isset($temp['SpeciesSelected']['Family']) ? $temp['VernNamesSelected']['Family'] :
                (!isset($temp['VernNamesSelected']['Family']) && isset($temp['GenusSelected']['Family']) && !isset($temp['SpeciesSelected']['Family']) ? $temp['GenusSelected']['Family'] :
                (!isset($temp['VernNamesSelected']['Family']) && !isset($temp['GenusSelected']['Family']) && isset($temp['SpeciesSelected']['Family']) ? $temp['SpeciesSelected']['Family'] :
                NULL)))))));

            $output['Genus'] = (isset($temp['VernNamesSelected']['Genus']) && isset($temp['FamiliesSelected']['Genus']) && isset($temp['SpeciesSelected']['Genus']) ? array_intersect($temp['VernNamesSelected']['Genus'],$temp['FamiliesSelected']['Genus'],$temp['SpeciesSelected']['Genus']) :
                (isset($temp['VernNamesSelected']['Genus']) && isset($temp['FamiliesSelected']['Genus']) && !isset($temp['SpeciesSelected']['Genus']) ? array_intersect($temp['VernNamesSelected']['Genus'],$temp['FamiliesSelected']['Genus']) :
                (isset($temp['VernNamesSelected']['Genus']) && !isset($temp['FamiliesSelected']['Genus']) && isset($temp['SpeciesSelected']['Genus']) ? array_intersect($temp['VernNamesSelected']['Genus'],$temp['SpeciesSelected']['Genus']) :
                (!isset($temp['VernNamesSelected']['Genus']) && isset($temp['FamiliesSelected']['Genus']) && isset($temp['SpeciesSelected']['Genus']) ? array_intersect($temp['FamiliesSelected']['Genus'],$temp['SpeciesSelected']['Genus']) :
                (isset($temp['VernNamesSelected']['Genus']) && !isset($temp['FamiliesSelected']['Genus']) && !isset($temp['SpeciesSelected']['Genus']) ? $temp['VernNamesSelected']['Genus'] :
                (!isset($temp['VernNamesSelected']['Genus']) && isset($temp['FamiliesSelected']['Genus']) && !isset($temp['SpeciesSelected']['Genus']) ? $temp['FamiliesSelected']['Genus'] :
                (!isset($temp['VernNamesSelected']['Genus']) && !isset($temp['FamiliesSelected']['Genus']) && isset($temp['SpeciesSelected']['Genus']) ? $temp['SpeciesSelected']['Genus'] :
                NULL)))))));

            $output['Species'] = (isset($temp['VernNamesSelected']['Species']) && isset($temp['GenusSelected']['Species']) && isset($temp['FamiliesSelected']['Species']) ? array_intersect($temp['VernNamesSelected']['Species'],$temp['GenusSelected']['Species'],$temp['FamiliesSelected']['Species']) :
                (isset($temp['VernNamesSelected']['Species']) && isset($temp['GenusSelected']['Species']) && !isset($temp['FamiliesSelected']['Species']) ? array_intersect($temp['VernNamesSelected']['Species'],$temp['GenusSelected']['Species']) :
                (isset($temp['VernNamesSelected']['Species']) && !isset($temp['GenusSelected']['Species']) && isset($temp['FamiliesSelected']['Species']) ? array_intersect($temp['VernNamesSelected']['Species'],$temp['FamiliesSelected']['Species']) :
                (!isset($temp['VernNamesSelected']['Species']) && isset($temp['GenusSelected']['Species']) && isset($temp['FamiliesSelected']['Species']) ? array_intersect($temp['GenusSelected']['Species'],$temp['FamiliesSelected']['Species']) :
                (isset($temp['VernNamesSelected']['Species']) && !isset($temp['GenusSelected']['Species']) && !isset($temp['FamiliesSelected']['Species']) ? $temp['VernNamesSelected']['Species'] :
                (!isset($temp['VernNamesSelected']['Species']) && isset($temp['GenusSelected']['Species']) && !isset($temp['FamiliesSelected']['Species']) ? $temp['GenusSelected']['Species'] :
                (!isset($temp['VernNamesSelected']['Species']) && !isset($temp['GenusSelected']['Species']) && isset($temp['FamiliesSelected']['Species']) ? $temp['FamiliesSelected']['Species'] :
                NULL)))))));

                if(!empty($output['SubPlot'])){
                    foreach($output['SubPlot'] as $key2=>$value2){
                        $output['SubPlot'][$key2] = array('id' => $value2 , 'text' => $value2);
                    }
                } else {
                    $output['SubPlot'] = $this->cache->get('FSubPlot');
                }

                if(!empty($output['CensusYear'])){
                    foreach($output['CensusYear'] as $key2=>$value2){
                        $output['CensusYear'][$key2] = array('id' => $value2 , 'text' => $value2);
                    }
                } else {
                    $output['CensusYear'] = $this->cache->get('FCensusYear');
                }

                if(!empty($output['Plot'])){
                    foreach($output['Plot'] as $key2=>$value2){
                        $output['Plot'][$key2] = array('id' => $value2 , 'text' => $value2);
                    }
                } else {
                    $output['Plot'] = $this->cache->get('FPlot');
                }

                if(!empty($output['VernName'])){
                    foreach($output['VernName'] as $key2=>$value2){
                        $output['VernName'][$key2] = array('id' => $value2 , 'text' => $value2);
                    }
                } else {
                    $output['VernName'] = $this->cache->get('FVernName');
                }

                if(!empty($output['Family'])){
                    foreach($output['Family'] as $key2=>$value2){
                        $output['Family'][$key2] = array('id' => $value2 , 'text' => $value2);
                    }
                } else {
                    $output['Family'] = $this->cache->get('FFamily');
                }
                if(!empty($output['Genus'])){
                    foreach($output['Genus'] as $key2=>$value2){
                        $output['Genus'][$key2] = array('id' => $value2 , 'text' => $value2);
                    }
                } else {
                    $output['Genus'] = $this->cache->get('FGenus');
                }
                if(!empty($output['Species'])){
                    foreach($output['Species'] as $key2=>$value2){
                        $output['Species'][$key2] = array('id' => $value2 , 'text' => $value2);
                    }
                } else {
                    $output['Species'] = $this->cache->get('FSpecies');
                }
                echo json_encode($output);
        }

        #### Génère la chaine de caractère $like pour le filtrage ####
        protected function like($filters, $get)
        {

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

        protected function _islocal(){
            return strpos($_SERVER['HTTP_HOST'], 'local');
        }

        #### Transforme le tableau des colonnes en tableau à nom simples ####
        private function pluck( $a, $prop )
	{
		$out = array();
		for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
			$out[] = $a[$i][$prop];
		}
		return $out;
	}

        public function complete()
        {
            $token = base64_decode($this->uri->segment(4));
            $cleanToken = $this->security->xss_clean($token);

            $user_info = $this->user_model->isTokenValid($cleanToken); //either false or array();

            if(!$user_info){
                $this->session->set_flashdata('flash_message', 'Token is invalid or expired');
                redirect(base_url().'main/login');
            }
            $data = array(
                'firstName'=> $user_info->first_name,
                'email'=>$user_info->email,
                'user_id'=>$user_info->id,
                'token'=>$this->base64url_encode($token)
            );

            $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
            $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');

            if ($this->form_validation->run() == FALSE) {
                $this->load->view('header');
                $this->load->view('complete', $data);
                $this->load->view('footer');
            }else{

                $this->load->library('password');
                $post = $this->input->post(NULL, TRUE);

                $cleanPost = $this->security->xss_clean($post);

                $hashed = $this->password->create_hash($cleanPost['password']);
                $cleanPost['password'] = $hashed;
                unset($cleanPost['passconf']);
                $userInfo = $this->user_model->updateUserInfo($cleanPost);

                if(!$userInfo){
                    $this->session->set_flashdata('flash_message', "There was a problem updating your record");
                    redirect(base_url().'main/login');
                }

                unset($userInfo->password);

                foreach($userInfo as $key=>$val){
                    $this->session->set_userdata($key, $val);
                }
                redirect(base_url().'main/');

            }
        }

        public function login()
        {
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'required');

            if($this->form_validation->run() == FALSE) {
                $this->load->view('header');
                $this->load->view('login');
                $this->load->view('footer');
            }else{

                $post = $this->input->post();
                $clean = $this->security->xss_clean($post);

                $userInfo = $this->user_model->checkLogin($clean);

                if(!$userInfo){
                    redirect(base_url().'main/login');
                }
                foreach($userInfo as $key=>$val){
                    $this->session->set_userdata($key, $val);
                }
                redirect(base_url().'main/');
            }

        }

        public function request()
        {
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('affiliation', 'Affiliation|max_length[255]');
            $this->form_validation->set_rules('address', 'Full address', 'required|max_length[255]');
            $this->form_validation->set_rules('firstname', 'Name', 'required|max_length[255]');
            $this->form_validation->set_rules('lastname', 'Name', 'required|max_length[255]');
            $this->form_validation->set_rules('title_research', 'Title of the  research', 'required|max_length[255]');
            $this->form_validation->set_rules('summary_research', 'Summary', 'required|min_length[15]|max_length[1024]');
            $this->form_validation->set_rules('description_data', 'description', 'required|min_length[15]|max_length[1024]');
            $this->form_validation->set_rules('timeline', 'Timeline', 'required|valid_date|max_length[255]');

            if($this->form_validation->run() == FALSE) {

                $fields = array(
                    "CensusYear",
                    "Plot"
                );

                $paracouDB = $this->load->database('paracou', TRUE); // Use paracou database

                /* Get columns name */
                $data["columns_name"] = $paracouDB->query("SELECT *
                FROM information_schema.columns
                WHERE table_schema = 'public'
                  AND table_name   = 'taparacou'")->result_array();

                foreach ($fields as $value) {
                    $data[$value] = $paracouDB->query("select \"$value\" from taparacou group by \"$value\" order by \"$value\"")->result_array();
                }

                $this->load->view('header');
                $this->load->view('request',$data);
                $this->load->view('footer');
            }else{

                $post = $this->input->post();
                $clean = $this->security->xss_clean($post);
                $requestId = $this->request_model->insertRequest($clean);

                if(!$requestId){
                    $this->session->set_flashdata('flash_message', 'A problem appeared in your request');
                    redirect(base_url().'main/login');
                } else {
                    $requestInfo = $this->request_model->getRequestInfo($requestId);
                    $this->load->view('header');
                    print_r($requestInfo);
                    echo '<br>Your request had been taken, you will be contacted by e-mail when accepted <br>'
                    . '<a href="'. base_url().'/main/">Back to login</a>';
                    $this->load->view('footer');
                }
            }

        }


        public function logout()
        {
            $this->session->sess_destroy();
            redirect(base_url().'main/login/');
        }

        public function forgot()
        {

            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

            if($this->form_validation->run() == FALSE) {
                $this->load->view('header');
                $this->load->view('forgot');
                $this->load->view('footer');
            }else{
                $email = $this->input->post('email');
                $clean = $this->security->xss_clean($email);
                $userInfo = $this->user_model->getUserInfoByEmail($clean);

                if(!$userInfo){
                    $this->session->set_flashdata('flash_message', 'We cant find your email address');
                    redirect(base_url('main/login'));
                }

                if($userInfo->status != $this->status[1]){ //if status is not approved
                    $this->session->set_flashdata('flash_message', 'Your account is not in approved status');
                    redirect(base_url('main/login'));
                }

                //build token

                $token = $this->user_model->insertToken($userInfo->id);
                $qstring = $this->base64url_encode($token);
                $url = base_url('main/reset_password/token/') . $qstring;
                $link = '<a href="' . $url . '">' . $url . '</a>';

                $message = '';
                $message .= '<strong>A password reset has been requested for this email account</strong><br>';
                $message .= '<strong>Please click:</strong> ' . $link;

                echo $message; //send this through mail
                exit;

            }

        }

        public function reset_password()
        {
            $token = $this->base64url_decode($this->uri->segment(4));
            $cleanToken = $this->security->xss_clean($token);

            $user_info = $this->user_model->isTokenValid($cleanToken); //either false or array();

            if(!$user_info){
                $this->session->set_flashdata('flash_message', 'Token is invalid or expired');
                redirect(base_url('main/login'));
            }
            $data = array(
                'firstName'=> $user_info->first_name,
                'email'=>$user_info->email,
                'user_id'=>$user_info->id,
                'token'=>$this->base64url_encode($token)
            );

            $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
            $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');

            if ($this->form_validation->run() == FALSE) {
                $this->load->view('header');
                $this->load->view('reset_password', $data);
                $this->load->view('footer');
            }else{

                $this->load->library('password');
                $post = $this->input->post(NULL, TRUE);

                $cleanPost = $this->security->xss_clean($post);

                $hashed = $this->password->create_hash($cleanPost['password']);
                $cleanPost['password'] = $hashed;
                $cleanPost['user_id'] = $user_info->id;
                unset($cleanPost['passconf']);
                if(!$this->user_model->updatePassword($cleanPost)){
                    $this->session->set_flashdata('flash_message', "There was a problem updating your password $test");
                }else{
                    $this->session->set_flashdata('flash_message', 'Your password has been updated. You may now login');
                }
                redirect(base_url().'main/login');
            }
        }

    public function base64url_encode($data) {
      return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function base64url_decode($data) {
      return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
