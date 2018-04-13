<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

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
            $this->load->helper('inflector');
            $this->load->library('email');
            $this->load->library('PeEmail');
            $this->load->library('session');
            $this->load->library('table');
            $this->load->library('pagination');
            $this->load->dbutil();

        }
        
    protected function checkRights(){
        if(empty($this->session->userdata['email'])){
            redirect(base_url().'main/login/');
        }
        if ($this->session->userdata('role') != 'admin'){        
            redirect(base_url().'admin/right_error/');
        } else {
            return true;
        }
    }    
    public function index(){
        if ($this->checkRights()) {
            redirect(base_url().'admin/list_users/');
        }
    }
    public function add($idrequest = NULL){
        if ($this->checkRights()) {
            if (isset($idrequest)) {
                $requestinfo= $this->request_model->getRequestInfo($idrequest);
                $data["email"] = $requestinfo->email;
                $data["firstname"] = $requestinfo->firstname;
                $data["lastname"] = $requestinfo->lastname;
                $data["expires"] = $requestinfo->timeline;
            } else {
                $data["email"] = NULL;
                $data["firstname"] = NULL;
                $data["lastname"] = NULL;
                $data["expires"] = date('Y/m/d', strtotime('+1 months')); 
            }
            $this->form_validation->set_rules('firstname', 'First Name', 'required');
            $this->form_validation->set_rules('lastname', 'Last Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
            $this->form_validation->set_rules('expires', 'Expires', 'valid_date');

            if ($this->form_validation->run() == FALSE){
                $this->load->view('admin/header');
                $this->load->view('admin/register',$data);
                $this->load->view('admin/footer');
            }else{
                if($this->user_model->isDuplicate($this->input->post('email'))){
                    $this->session->set_flashdata('error_message', 'User email already exists');
                    redirect(base_url().'admin/list_users');
                }else{
                    $clean = $this->security->xss_clean($this->input->post(NULL, TRUE));
                    $clean["request_id"] = isset($idrequest) ? $idrequest : NULL;
                    $id = $this->user_model->insertUser($clean);
                    $token = $this->user_model->insertToken($id);
                    $dest = $this->input->post('email');

                    $qstring = $this->base64url_encode($token);
                    $url = base_url() . 'main/complete/token/' . $qstring;
                    $link = '<a href="' . $url . '">' . $url . '</a>';

                    $panel1 = '<p>En attendant que le serveur mail soit mis en place, il faut envoyer manuellement ce message par e-mail : </p> <div class="card">';
                    $message = '';
                    $message .= "<strong>You have been invited to Paracou-Ex</strong><br>";
                    $message .= '<strong>Please click:</strong> ' . $link;
                    
                    $catch = $this->peemail->sendMail($dest,"Invitation Paracou-Ex",$message);
                    if ($catch) {
                        echo "E-mail sent at $dest";
                    } else {
                        echo $catch;
                    }

                    $panel2 = '</div>';

                    echo $panel1;
                    echo $message; //send this in email
                    echo $panel2;
                    exit;


                }
            }
        }
    }
    public function delete_user($id){
        $this->checkRights();
        $userinfo = $this->user_model->getUserInfo($id);
        if ($userinfo) {
            $this->user_model->deleteUser($id);
            if (isset($userinfo->request_id)) {
                $this->request_model->deleteRequest($id);
            }
        } else {
            $this->session->set_flashdata('error_message',"The user n°$id doesn't exist");
        }
        redirect(base_url().'admin/list_users/');
    }
    
    public function show_request($id){
        $this->checkRights();
        $requestinfo = $this->request_model->getRequestInfo($id);
        $data["id"] = $id;
        if ($requestinfo) {
            $data['requestinfo'] = $requestinfo;
            if ($this->input->post('apply')) {
                $clean = $this->security->xss_clean($this->input->post(NULL, TRUE));
                $clean["id"] = $id;
                $clean["accepted"] = $requestinfo->accepted;
                $this->request_model->updateRequestInfo($clean);
            }
        } else {
            $this->session->set_flashdata('error_message',"The request n°$id doesn't exist");
            redirect(base_url().'admin/list_requests/');
        }
        $this->load->view('admin/header');
        $this->load->view('admin/show_request', $data);
        $this->load->view('admin/footer');
    }
    
    public function accept_request($id){
        $this->checkRights();
        $requestinfo = $this->request_model->getRequestInfo($id);
        $user_duplicated = $this->user_model->isDuplicate($requestinfo->email);
        if (($requestinfo) && !isset($requestinfo->accepted)) {
            if($user_duplicated){
                $clean = $this->security->xss_clean($this->input->post(NULL, TRUE));
                $clean["id"] = $id;
                $clean["accepted"] = date('Y/m/d');
                $this->request_model->updateRequestInfo($clean);
                redirect(base_url()."admin/edit_user/user-$user_duplicated->id/request-$id");
            }
            $clean = $this->security->xss_clean($this->input->post(NULL, TRUE));
            $clean["id"] = $id;
            $clean["accepted"] = date('Y/m/d');
            $this->request_model->updateRequestInfo($clean);
            redirect(base_url()."admin/add/$id");
        } else if (isset($requestinfo->accepted)) {
            $this->session->set_flashdata('error_message',"The request n°$id is already accepted");
            redirect(base_url().'admin/list_requests/');
        } else {
            $this->session->set_flashdata('error_message',"The request n°$id doesn't exist");
            redirect(base_url().'admin/list_requests/');
        }
    }
    
    public function decline_request($id){
        $this->checkRights();
        $requestinfo = $this->request_model->getRequestInfo($id);
        if (($requestinfo) && !isset($requestinfo->accepted)) {
            $d["id"] = $id;
            $d["accepted"] = 'Declined';
            $this->request_model->updateRequestInfo($d);
        } else if (isset($requestinfo->accepted)) {
            $this->session->set_flashdata('error_message',"The request n°$id is already accepted");
            redirect(base_url().'admin/list_requests/');
        } else {
            $this->session->set_flashdata('error_message',"The request n°$id doesn't exist");
            redirect(base_url().'admin/list_requests/');
        }
    }
    
    public function list_users(){
        
        #### Account verifications ####
        $this->checkRights();
        
        $flash['flash_message'] = $this->session->flashdata('error_message');
        $data['users'] = $this->user_model->getUserList(); // get User list

        #### Views ####
        $this->load->view('admin/header',$flash);
        $this->load->view('admin/list_users', $data);
        $this->load->view('admin/footer');
    }
    
    public function edit_user($id, $request_id = NULL){
        $this->checkRights();
        $userinfo = $this->user_model->getUserInfo($id);
        if (isset($request_id)) {
            $requestinfo= $this->request_model->getRequestInfo($request_id);
            $data["first_name"] = $requestinfo->firstname;
            $data["last_name"] = $requestinfo->lastname;
            $data["expires"] = $requestinfo->timeline;
        } 
        $data["id"] = $id;
        if ($userinfo) {
            $data['userinfo'] = $userinfo;
            
            $this->form_validation->set_rules('first_name', 'First Name', 'required');
            $this->form_validation->set_rules('last_name', 'Last Name', 'required');
            $this->form_validation->set_rules('expires', 'Expires', 'valid_date');
            
            if ($this->form_validation->run() != FALSE) {
                $clean = $this->security->xss_clean($this->input->post(NULL, TRUE));
                $clean['user_id'] = $id;
                $clean['request_id'] = $request_id;
                $this->user_model->editUserInfo($clean);
                redirect(base_url().'admin/list_users/');
                
            } else {
                $this->load->view('admin/header');
                $this->load->view('admin/edit_user', $data);
                $this->load->view('admin/footer');
            }
        } else {
            $this->session->set_flashdata('error_message',"The user n°$id doesn't exist");
            redirect(base_url().'admin/list_users/');
        }

    }
    
    public function list_requests(){
        #### Account verifications ####
        $this->checkRights();
        $get = $this->input->get();
        
        $flash['flash_message'] = $this->session->flashdata('error_message');
        $requests = $this->request_model->getRequestList(); // get requests list
        $data['requests'] = $requests;

        if(isset($get["csv"])){
            $array = json_decode(json_encode($requests), True);
            $this->exports_array_csv($array,"Request_list");
        }
        
        #### Views ####
        $this->load->view('admin/header',$flash);
        $this->load->view('admin/list_requests', $data);
        $this->load->view('admin/footer');
    }
    
    public function right_error(){
        $this->load->view('admin/header');
        $this->load->view('admin/error');
        $this->load->view('admin/footer');
    }
    
    public function exports_array_csv($data,$name){
                    
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=\"$name".".csv\"");
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            
            foreach ($data as $data) {
                fputcsv($handle, $data);
            }
                fclose($handle);
            exit;
        }
    
    public function base64url_encode($data) {
      return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function base64url_decode($data) {
      return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}

