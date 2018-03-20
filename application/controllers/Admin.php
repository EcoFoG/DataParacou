<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    function __construct(){
            parent::__construct();
            $this->load->model('User_model', 'user_model', TRUE);

            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            $this->status = $this->config->item('status');
            $this->roles = $this->config->item('roles');
            $this->load->helper('form');
            $this->load->helper('url');
            $this->load->library('email');
            $this->load->library('session');
            $this->load->library('table');
            $this->load->dbutil();

        }

    public function index(){
        if(empty($this->session->userdata['email'])){
            redirect(base_url().'main/login/');
        }
        if ($this->session->userdata('role') != 'admin'){        
            redirect(base_url().'admin/right_error/');
        } else {
            redirect(base_url().'admin/list_users/');
        }
        
    }
    public function add(){
        if(empty($this->session->userdata['email'])){
            redirect(base_url().'main/login/');
        }
        if ($this->session->userdata('role') != 'admin'){        
            redirect(base_url().'admin/right_error/');
        } else {

            $this->form_validation->set_rules('firstname', 'First Name', 'required');
            $this->form_validation->set_rules('lastname', 'Last Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

            if ($this->form_validation->run() == FALSE) {
                $this->load->view('admin/header');
                $this->load->view('admin/register');
                $this->load->view('admin/footer');
            }else{
                if($this->user_model->isDuplicate($this->input->post('email'))){
                    $this->session->set_flashdata('flash_message', 'User email already exists');
                    redirect(base_url().'admin');
                }else{

                    $clean = $this->security->xss_clean($this->input->post(NULL, TRUE));
                    $id = $this->user_model->insertUser($clean);
                    $token = $this->user_model->insertToken($id);

                    $qstring = $this->base64url_encode($token);
                    $url = base_url() . 'main/complete/token/' . $qstring;
                    $link = '<a href="' . $url . '">' . $url . '</a>';

                    $panel1 = '<p>En attendant que le serveur mail soit mis en place, il faut envoyer manuellement ce message par e-mail : </p> <div class="card">';

                    $message = '';
                    $message .= "<strong>You have been invited to Paracou-Ex</strong><br>";
                    $message .= '<strong>Please click:</strong> ' . $link;

                    $panel2 = '</div>';

                    echo $panel1;
                    echo $message; //send this in email
                    echo $panel2;
                    exit;


                }
            }
        }
    }
    public function delete($id){
        $userinfo = $this->user_model->getUserInfo($id);
        if ($userinfo) {
            if ($userinfo->role != "admin") {
                $this->user_model->deleteUser($id);
            } else {
                $this->session->set_flashdata('error_message',"You cannot delete an admin");
            }
        } else {
            $this->session->set_flashdata('error_message',"The user n°$id doesn't exist");
        }
        redirect(base_url().'admin/list_users/');
    }
    public function list_users(){
        $data['flash_message'] = $this->session->flashdata('error_message');
        $data['users'] = $this->user_model->getUserList();
        $this->load->view('admin/header');
        $this->load->view('admin/list', $data);
        $this->load->view('admin/footer');
    }
    
    public function right_error(){
        $this->load->view('admin/header');
        $this->load->view('admin/error');
        $this->load->view('admin/footer');
    }
    
    public function base64url_encode($data) {
      return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function base64url_decode($data) {
      return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}

