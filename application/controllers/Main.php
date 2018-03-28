<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

    public $status;
    public $roles;

    function __construct(){
        parent::__construct();
        $this->load->model('User_model', 'user_model', TRUE);
        $this->load->model('Request_model', 'request_model', TRUE);

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        $this->status = $this->config->item('status');
        $this->roles = $this->config->item('roles');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('download');
        $this->load->helper('date');
        $this->load->library('email');
        $this->load->library('session');
        $this->load->library('table');
        $this->load->library('pagination');
        $this->load->dbutil();

    }

	public function index()
	{
            if($this->input->post("admin")){
                redirect(base_url().'admin');
            }
            if($this->input->post("disconnect")){
                redirect(base_url().'main/logout/');
            }
            if(empty($this->session->userdata['email']) || ((isset($this->session->userdata['expires'])) && (time() > strtotime(str_replace('/', '-', $this->session->userdata['expires']))))){
                redirect(base_url().'main/login/');
            }
            
            $data['role'] = $this->session->userdata('role');
            
            #### get GET method variables ####
            $get = $this->input->get(NULL, FALSE);
            $data["get"] = $get;
            
            $paracouDB = $this->load->database('paracou', TRUE);
            
            /* Configuration of the data table in application/config/datatable.php */
            $this->config->load("datatable");
            /* Configuration of the tooltips in application/config/tooltips.php */
            $this->config->load("tooltips");
            $tmpl = $this->config->item("table_template");
            $data['headers'] = $this->config->item("headers");
            $data['tip_CodeMeas'] = $this->config->item("tip_CodeMeas");
            $data['tip_CodeAlive'] = $this->config->item("tip_CodeAlive");
            $filters = $this->config->item("filters");
            $columns = $this->config->item("columns");
            $this->table->set_template($tmpl); // Apply template to the generated table
            
            #### Get levels of filters in the databases ####
            foreach ($filters as $value) {
                $temp = $paracouDB->query("select \"$value\" from taparacou group by \"$value\" order by \"$value\"")->result_array();
                foreach ($temp as $key2=>$value2) {
                    $temp[$key2] = $temp[$key2][$value];
                }
                $data['F'.$value] = $temp;
            }
            $data['filters'] = $filters;
            $filters[] = "SubPlot";
            
            #### Set default circMax and circMin ####
            if(isset($get['circMax'])){
                $circMax = $this->input->get('circMax');
            } else {
                $circMax = 150;
            }
            if(isset($get['circMin'])){
                $circMin = $this->input->get('circMin');
            } else {
                $circMin = 10;
            }
            $min_tmp = $paracouDB->query("SELECT min(\"Circ\") FROM taparacou")->row();
            $data['circDBMin'] = $min_tmp->min;
            $max_tmp = $paracouDB->query("SELECT max(\"Circ\") FROM taparacou")->row();
            $data['circDBMax'] = $max_tmp->max;
            
            #### Create limit string for the query ####
            $offset = isset($get["page"]) ? $get["page"] : 1;
            $n_limit = isset($get['limit']) ? $get['limit'] : 50;
            $limit = " LIMIT $n_limit OFFSET $offset";
            
            #### Create like string for the query ####
            $flag = count($filters);
            foreach($filters as $value){
               $flag = (isset($get[$value])) ? $flag-1: $flag;
            }
            $like = (count($filters) > $flag) ? $this->like($filters,$get) : ''; // Empty chain in $like if no filter is select
            
            #### Query ####
            $query =   "SELECT \"".implode("\", \"", $this->pluck($columns, 'db'))."\" "
              . "FROM taparacou "
              . "WHERE \"Circ\" BETWEEN $circMin AND $circMax "
              . "$like "
              . "ORDER BY \"TreeFieldNum\",\"CensusYear\"";
            
            #### Generate the csv ####
            if(isset($get["csv"])){
                $time = time();
                $name = "Paracou".mdate("%d%m%Y",$time).".csv"; // Name of the CSV
                $csv = $this->dbutil->csv_from_result($paracouDB->query($query));
                force_download($name, $csv);
            }
            
            #### Generate the table ####
            $total_rows = $paracouDB->query($query)->num_rows(); // Getting the number of rows for pagination
            $query .= "$limit" ;
            $data['table'] = $paracouDB->query($query)->result_array();
            
            #### Pagination ####
            $this->config->load("pagination");
            $conf_pagination = $this->config->item("pagination");
            $conf_pagination['base_url'] = base_url()."main/" ;
            $conf_pagination['total_rows'] = $total_rows;
            $conf_pagination['per_page'] = $n_limit;
            $this->pagination->initialize($conf_pagination);
            $data["pagination_links"] = $this->pagination->create_links();
            
            #### Views ####
            $this->load->view('header');
            $this->load->view('index', $data);
            $this->load->view('footer');
	}
        
        protected function like($filters, $get)
        {
 
            foreach($filters as $key => $value) {
                if(isset($get[$value])){
                    $str = implode(" OR ", $get[$value]);
                } else {
                    $str='';
                }
                if ($str != '') {
                    $binding = $str;
                    $like[$key] = "CAST(\"".$value."\" AS TEXT) LIKE '".$binding."'";
                }
            }
            $like = implode(" AND ",$like);
            $like = " AND ".$like;
            return $like;
        }

        protected function _islocal(){
            return strpos($_SERVER['HTTP_HOST'], 'local');
        }
        
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
                
                $hotline = print_r($cleanpost);

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
            $this->form_validation->set_rules('name', 'Name', 'required|max_length[255]');
            $this->form_validation->set_rules('title_research', 'Title of the  research', 'required|max_length[255]');
            $this->form_validation->set_rules('summary_research', 'Summary', 'required|min_length[30]|max_length[1024]');
            $this->form_validation->set_rules('description_data', 'description', 'required|min_length[30]|max_length[1024]');
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
                print_r($post);
                $requestId = $this->request_model->insertRequest($clean);

                if(!$requestId){
                    $this->session->set_flashdata('flash_message', 'A problem appeared in your request');
                    redirect(base_url().'main/login');
                } else {
                    $requestInfo = $this->request_model->getRequestInfo($requestId);
                    $this->load->view('header');
                    print_r($requestInfo);
                    echo '<br>Your request had been taken, you will be contacted by e-mail when it will be accepted <br>'
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
