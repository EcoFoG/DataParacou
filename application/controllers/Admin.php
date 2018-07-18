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
            $this->load->library('session');
            $this->load->library('table');
            $this->load->library('pagination');
            $this->load->dbutil();

        }

    private function checkRights(){
        if(empty($this->session->userdata['email'])){
            $this->session->set_userdata('page_url',  current_url());
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

    public function add_user($idRequest = NULL){
        if ($this->checkRights()) {
            if (isset($idRequest)) {
                $requestinfo= $this->request_model->getRequestInfo($idRequest);
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
                    $clean["request_id"] = isset($idRequest) ? $idRequest : NULL;
                    $id = $this->user_model->insertUser($clean);

                    $idAcceptor = $this->session->userdata['id'];
                    !(isset($idRequest)) ?: $this->request_model->acceptRequest($idRequest, $idAcceptor); // Accept request if user is added
                    $token = $this->user_model->insertToken($id);
                    $dest = $this->input->post('email');

                    $qstring = $this->base64url_encode($token);
                    $url = base_url() . 'main/complete/token/' . $qstring;
                    $link = '<a href="' . $url . '">' . $url . '</a>';

                    $message = '';
                    $message .= "Invitation link to Paracou Data<br>";
                    $message .= 'Please click: ' . $link;

                    $this->load->config('email');
                    $email_config = $this->config->item('email');
                    $this->email->initialize($email_config);
                    $this->email->from("noreply@paracoudata.cirad.fr", 'Paracou Data');
                    $this->email->to($dest);

                    $this->email->subject('Invitation Paracou Data');
                    $this->email->message($message);
                    
                    $r = $this->email->send();

                    $this->email->clear();
                    $this->load->view('admin/header');
                    echo '<div class="container mt-5">
                            <div class="row justify-content-center">
                            <a href="'.base_url().'admin/list_users" >Back to user list</a>
                            </div>
                          </div>';
                    $this->load->view('admin/footer');
                }
            }
        }
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
            $data['disableRoleField'] = $userinfo->id === $this->session->userdata['id'] ? TRUE : FALSE;

            $this->form_validation->set_rules('first_name', 'First Name', 'required');
            $this->form_validation->set_rules('last_name', 'Last Name', 'required');
            $this->form_validation->set_rules('expires', 'Expires', 'valid_date');

            if ($this->form_validation->run() != FALSE) {
                $clean = $this->security->xss_clean($this->input->post(NULL, TRUE));
                $clean['user_id'] = $id;
                $clean['request_id'] = $request_id;
                $this->user_model->editUserInfo($clean);
                $idAcceptor = $this->session->userdata['id'];
                !(isset($request_id)) ?: $this->request_model->acceptRequest($request_id, $idAcceptor); // Accept request if user is added
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

    public function list_requests(){
        #### Account verifications ####
        $this->checkRights();
        $get = $this->input->get();

        $flash['flash_message'] = $this->session->flashdata('error_message');
        $requests = $this->request_model->getRequestList(); // get requests list
        $data['requests'] = $requests;
        $data['user_model'] = $this->user_model;

        if(isset($get["csv"])){
            $array = json_decode(json_encode($requests), True);
            $this->exports_array_csv($array,"Request_list");
        }

        #### Views ####
        $this->load->view('admin/header',$flash);
        $this->load->view('admin/list_requests', $data);
        $this->load->view('admin/footer');
    }

    public function show_request($id){
        $this->checkRights();
        $requestinfo = $this->request_model->getRequestInfo($id);
        $data["id"] = $id;
        if ($requestinfo) {
            $clean = $this->security->xss_clean($this->input->post(NULL, TRUE));
            $clean["id"] = $id;
            $clean["accepted"] = $requestinfo->accepted;
            $this->request_model->updateRequestInfo($clean);
            if ($this->input->post('apply') === "Accept") {
                $this->_accept_request($id);
            } else if ($this->input->post('apply') === "Decline") {
                $this->_decline_request($id);
            }
            $data['requestinfo'] = $requestinfo;
        } else {
            $this->session->set_flashdata('error_message',"The request n°$id doesn't exist");
            redirect(base_url().'admin/list_requests/');
        }
        $this->load->view('admin/header');
        $this->load->view('admin/show_request', $data);
        $this->load->view('admin/footer');
    }

    private function _sendApproveMail($requestInfo, $message){
        $this->load->config('email');
        $email_config = $this->config->item('email');
        $this->email->initialize($email_config);
        $this->email->from("noreply@paracoudata.cirad.fr",'Paracou Data');
        $this->email->to($requestInfo->email);

        $this->email->subject('Request approval');
        $this->email->message($message);
        
        $r = $this->email->send();
        echo $this->email->print_debugger();
        if(!$r){
            log_message('error', $this->email->print_debugger());
        }
        $this->email->clear();
    }

    private function _accept_request($id){
        $this->checkRights();
        $requestinfo = $this->request_model->getRequestInfo($id);
        $user_duplicated = $this->user_model->isDuplicate($requestinfo->email);
        if (($requestinfo) && !isset($requestinfo->accepted)) {
            $message = "Dear $requestInfo->name_principal_investigator,<br>Your account on Paracou Data has been created.<br>You will receive another mail with an invitation link.<br>
            You may now visualize and extract the data you need for your scientific project.<br>
            We remind you that these data are solely usable for the study for which you have requested access. Please do not communicate these data to anyone for any other use. For any other scientific study, please make a new data request on our website https://paracoudata.cirad.fr/main/login/.<br>
            If your work on these data leads to a public communication (scientific paper, communication, report…), please cite the source as 'Paracou Research Station, a large scale forest disturbance experiment in Amazonia from 1982, Cirad, https://paracou.cirad.fr/'<br>
            You can find the metadata here and geographic data here.<br>

            Specific Conditions : <br>
            
            $requestinfo->specific_conditions

            The Paracou team <br>
            https://paracou.cirad.fr";

            $this->_sendApproveMail($requestinfo, $message);
            if($user_duplicated){
                redirect(base_url()."admin/edit_user/user-$user_duplicated->id/request-$id");
            } else {
                $this->session->set_flashdata('accept',TRUE);
                redirect(base_url()."admin/add_user/$id");
            }
        } else if (isset($requestinfo->accepted)) {
            $this->session->set_flashdata('error_message',"The request n°$id is already accepted");
            redirect(base_url().'admin/list_requests/');
        } else {
            $this->session->set_flashdata('error_message',"The request n°$id doesn't exist");
            redirect(base_url().'admin/list_requests/');
        }
    }

    private function _decline_request($id){
        $this->checkRights();
        $requestinfo = $this->request_model->getRequestInfo($id);
        if (($requestinfo) && !isset($requestinfo->accepted)) {
            $this->request_model->declineRequest($id);
            $message = "$requestinfo->firstname $requestinfo->lastname,<br> Sorry your request has been declined.";
            $this->_sendApproveMail($requestinfo,$message);
            redirect(base_url().'admin/list_requests/');
        } else if (isset($requestinfo->accepted)) {
            $this->session->set_flashdata('error_message',"The request n°$id is already accepted");
            redirect(base_url().'admin/list_requests/');
        } else {
            $this->session->set_flashdata('error_message',"The request n°$id doesn't exist");
            redirect(base_url().'admin/list_requests/');
        }
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
