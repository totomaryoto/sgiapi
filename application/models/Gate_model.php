<?php

class Gate_model extends CI_Model
{



    public function get_password($condition = "")
    {
        $this->db->select('UserPassword');
        $this->db->where($condition);

        return $this->db->get("Users")->row();
    }


    public function row_count($condition = "")
    {

        $this->db->where($condition);
        return $this->db->get("Users")->num_rows();
    }

    public function create_user($data)
    {
        return $this->db->insert('Users', $data);
    }

    public function get_user($condition)
    {
        $this->db->where($condition);
        return $this->db->get("Users")->row();
    }
}
