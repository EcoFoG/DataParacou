<?php
class Filter_state_model extends CI_Model
{
    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    public function insertFilterState($d)
    {
        $data = array(
            'name'=>$d['name'],
            'state'=>$d['state'],
            'user_id'=>$d['user_id']
        );
        $q = $this->db->insert_string('filter_state', $data);
        $this->db->query($q);
        return $this->db->insert_id();
    }

    public function updateFilterState($d)
    {
        $data = array(
            'id'=>$d['id'],
            'name'=>$d['name'],
            'state'=>$d['state'],
        );
        $this->db->query($q);
        $this->db->where('id', $d['id']);
        $this->db->update('filter_state', $data);
        $success = $this->db->affected_rows();

        if (!$success) {
            error_log('Unable to updateRequestInfo('.$d['id'].')');
            return false;
        }

        return true;
    }

    public function getFilterStatesByUser($id)
    {
        $q = $this->db->get_where('filter_state', array('user_id' => $id));
        if ($this->db->affected_rows() > 0) {
            return $q->result();
        } else {
            error_log('user not found getFilterStates('.$id.')');
            return false;
        }
    }
}
