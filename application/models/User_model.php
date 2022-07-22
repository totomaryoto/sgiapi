<?php

class User_model extends CI_Model
{
    function save_user($id, $store_id, $type_code, $user_id)
    {
        // `t_UserStoreInsert`(
        //     p_user_id varchar(64),
        //     p_store_id varchar(64),
        //     p_type_code varchar(10),
        //     p_created_by varchar(64)

        return $this->db->query("CALL t_UserStoreInsert('" . $id . "','" . $store_id . "','" . $type_code . "','" . $user_id . "')");
    }
}