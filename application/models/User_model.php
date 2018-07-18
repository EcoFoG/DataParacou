<?php
class User_model extends CI_Model
{
    public $status;
    public $roles;

    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->status = $this->config->item('status');
        $this->roles = $this->config->item('roles');
    }

    public function insertUser($d)
    {
        $role = isset($d['role']) && ($d['role'] == '1') ? $this->roles[1]: $this->roles[0];
        $expires = (!empty($d['expires']) && isset($d['expires'])) ? $d['expires']: null;
        $string = array(
                'first_name'=>$d['firstname'],
                'last_name'=>$d['lastname'],
                'email'=>$d['email'],
                'expires'=>$expires,
                'created'=>date('Y/m/d'),
                'role'=>$role,
                'status'=>$this->status[0],
                'request_id'=>$d['request_id']
            );
        $q = $this->db->insert_string('users', $string);
        $this->db->query($q);
        return $this->db->insert_id();
    }
    
    public function deleteUser($id)
    {
        $this->db->delete('users', array('id' => $id));
        $this->db->delete('tokens', array('user_id' => $id));
    }

    public function isDuplicate($email)
    {
        $q = $this->db->get_where('users', array('email' => $email), 1);
        return $this->db->affected_rows() > 0 ? $q->row() : false;
    }
    
    public function getUserList()
    {
        $q = $this->db->get('users');
        return $q->result();
    }

    public function getAdminList()
    {
        $q = $this->db->get_where('users', array('role' => 'admin'));
        return $q->result();
    }

    public function insertToken($user_id)
    {
        $token = substr(sha1(rand()), 0, 30);
        $date = date('Y-m-d');

        $string = array(
                'token'=> $token,
                'user_id'=>$user_id,
                'created'=>$date
            );
        $query = $this->db->insert_string('tokens', $string);
        $this->db->query($query);
        return $token . $user_id;
    }

    public function isTokenValid($token)
    {
        $tkn = substr($token, 0, 30);
        $uid = substr($token, 30);

        $q = $this->db->get_where('tokens', array(
            'tokens.token' => $tkn,
            'tokens.user_id' => $uid), 1);

        if ($this->db->affected_rows() > 0) {
            $row = $q->row();
            $user_info = $this->getUserInfo($row->user_id);
            return $user_info;
        } else {
            return false;
        }
    }

    public function getUserInfo($id)
    {
        $q = $this->db->get_where('users', array('id' => $id), 1);
        if ($this->db->affected_rows() > 0) {
            $row = $q->row();
            return $row;
        } else {
            error_log('no user found getUserInfo('.$id.')');
            return false;
        }
    }

    public function updateUserInfo($post)
    {
        $data = array(
               'password' => $post['password'],
               'last_login' => date('Y/m/d h:i:s A'),
               'status' => $this->status[1],
            );
        $this->db->where('id', $post['user_id']);
        $this->db->update('users', $data);
        $success = $this->db->affected_rows();

        if (!$success) {
            error_log('Unable to updateUserInfo('.$post['user_id'].')');
            return false;
        }

        $user_info = $this->getUserInfo($post['user_id']);
        return $user_info;
    }
    public function editUserInfo($post)
    {
        $expires = isset($post['expires']) && !empty($post['expires']) ? $post['expires']: null;
        $data = array(
               'expires' => $expires,
               'role' => $post['role'],
               'first_name' => $post['first_name'],
               'last_name' => $post['last_name'],
            );
        if (isset($post['request_id']) && !empty($post['request_id'])) {
            $data['request_id'] = $post['request_id'];
        }
        $this->db->where('id', $post['user_id']);
        $this->db->update('users', $data);
        $success = $this->db->affected_rows();

        if (!$success) {
            error_log('Unable to editUserInfo('.$post['user_id'].')');
            return false;
        }

        $user_info = $this->getUserInfo($post['user_id']);
        return $user_info;
    }

    public function checkLogin($post)
    {
        $this->load->library('password');
        $this->db->select('*');
        $this->db->where('email', $post['email']);
        $query = $this->db->get('users');
        $userInfo = $query->row();
        
        $expiresDate = $userInfo->expires;
        $timestamp = strtotime($expiresDate);
        if ($timestamp === false) {
            $timestamp = strtotime(str_replace('/', '-', $expiresDate));
        }

        if (!$this->password->validate_password($post['password'], $userInfo->password)) {
            error_log('Unsuccessful login attempt('.$post['email'].')');
            $this->session->set_flashdata('flash_message', 'The login was unsucessful');
            return false;
        } elseif ((isset($expiresDate)) && (time()>$timestamp)) {
            error_log('Unsuccessful login attempt('.$post['email'].') account is expired');
            $this->session->set_flashdata('flash_message', 'The account is expired');
            return false;
        }

        $this->updateLoginTime($userInfo->id);

        unset($userInfo->password);
        return $userInfo;
    }

    public function updateLoginTime($id)
    {
        $this->db->where('id', $id);
        $this->db->update('users', array('last_login' => date('Y/m/d h:i:s A')));
        return;
    }

    public function getUserInfoByEmail($email)
    {
        $q = $this->db->get_where('users', array('email' => $email), 1);
        if ($this->db->affected_rows() > 0) {
            $row = $q->row();
            return $row;
        } else {
            error_log('no user found getUserInfo('.$email.')');
            return false;
        }
    }

    public function updatePassword($post)
    {
        $this->db->where('id', $post['user_id']);
        $this->db->update('users', array('password' => $post['password']));
        $success = $this->db->affected_rows();

        if (!$success) {
            error_log('Unable to updatePassword('.$post['user_id'].')');
            return false;
        }
        return true;
    }
}
