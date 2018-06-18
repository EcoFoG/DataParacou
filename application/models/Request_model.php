<?php
class Request_model extends CI_Model {

    public $status;
    public $roles;

    function __construct(){
        // Call the Model constructor
        parent::__construct();
    }

    public function insertRequest($d)
    {
        $columns = !empty($d['columns']) ? implode(',',$d['columns']) : NULL;
        $years = !empty($d['years']) ? implode(',',$d['years']) : NULL;
        $plots = !empty($d['plots']) ? implode(',',$d['plots']) : NULL;

        $string = array(
            'email'=>$d['email'],
            'affiliation'=>$d['affiliation'],
            'address'=>$d['address'],
            'firstname'=>$d['firstname'],
            'lastname'=>$d['lastname'],
            'title_research'=>$d['title_research'],
            'summary_research'=>$d['summary_research'],
            'description_data'=>$d['description_data'],
            'columns'=>$columns,
            'years'=>$years,
            'plots'=>$plots,
            'timeline'=>$d['timeline'],
            'requested'=>date("Y/m/d")
        );
        $q = $this->db->insert_string('requests',$string);
        $this->db->query($q);
        return $this->db->insert_id();
    }

    public function updateRequestInfo($d)
    {
        $d["specific_conditions"] = isset($d["specific_conditions"]) ? $d["specific_conditions"] : NULL;
        $d["valorisation"] = isset($d['valorisation']) ? $d['valorisation'] : NULL;
        $data = array(
               'specific_conditions' => $d['specific_conditions'],
               'valorisation' => $d['valorisation'],
               'accepted' => $d['accepted']
            );
        $this->db->where('id', $d['id']);
        $this->db->update('requests', $data);
        $success = $this->db->affected_rows();

        if(!$success){
            error_log('Unable to updateRequestInfo('.$d['id'].')');
            return false;
        }

        $request_info = $this->getRequestInfo($d['id']);
        return $request_info;
    }

    public function deleteRequest($id){
        $this->db->delete('requests', array('id' => $id));
    }

    public function acceptRequest($id){
      $data = array(
             'accepted' => date('Y/m/d')
          );
      $this->db->where('id', $id);
      $this->db->update('requests', $data);

    }

    public function declineRequest($id){
      $data = array(
             'accepted' => "Declined"
          );
      $this->db->where('id', $id);
      $this->db->update('requests', $data);

    }

    public function isDuplicate($email)
    {
        $this->db->get_where('requests', array('email' => $email), 1);
        return $this->db->affected_rows() > 0 ? TRUE : FALSE;
    }

    public function getRequestList()
    {
        $q = $this->db->get('requests');
        return $q->result();
    }

    public function getRequestInfo($id)
    {
        $q = $this->db->get_where('requests', array('id' => $id), 1);
        if($this->db->affected_rows() > 0){
            $row = $q->row();
            return $row;
        }else{
            error_log('no request found getRequestInfo('.$id.')');
            return false;
        }
    }

    public function getRequestInfoByEmail($email)
    {
        $q = $this->db->get_where('requests', array('email' => $email), 1);
        if($this->db->affected_rows() > 0){
            $row = $q->row();
            return $row;
        }else{
            error_log('no user found getRequestInfo('.$email.')');
            return false;
        }
    }

}
