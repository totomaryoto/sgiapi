<?php

class AUTHORIZATION
{


    public static function validateToken($token)
    {
        $CI = &get_instance();
        $result = JWT::decode($token, $CI->config->item('jwt_key'));
        if ($result == null) {
            return false;
        }
        $id = $result->data->id;
        $query = $CI->db->query("SELECT * FROM Users where UserCode = '" . $id . "' and  Token = '" . $token . "' ")->num_rows();
        if ($query > 0) {
            return true;
        }
        return false;
    }

    public static function generateToken($data)
    {
        $CI = &get_instance();
        return JWT::encode($data, $CI->config->item('jwt_key'));
    }
}
