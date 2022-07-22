<?php

use chriskacerguis\RestServer\RestController;

class Login extends RestController
{

    public function generate_token($user_data)
    {
        $tokenData = array();
        $issuedat_claim = time();

        $tokenData['iss'] =  "POSAKU_API";
        $tokenData['iat'] =  $issuedat_claim;
        $tokenData['data']['id'] = $user_data->user_id;
        $tokenData['data']['email'] = $user_data->email;
        $tokenData['data']['device_id'] = $user_data->device_id;
        $tokenData['data']['phone'] = $user_data->phone;
        $tokenData['data']['user_image'] = $user_data->user_image;
        $tokenData['data']['store_id'] = $user_data->store_id;
        $tokenData['data']['store_name'] = $user_data->store_name;
        $tokenData['data']['store_address'] = $user_data->store_address;
        $tokenData['data']['user_type'] = $user_data->user_type;
        $tokenData['data']['company_id'] = $user_data->company_id;
        $tokenData['data']['company_name'] = $user_data->company_name;
        return AUTHORIZATION::generateToken($tokenData);
    }
    function __construct()
    {
        parent::__construct();
        $this->load->model("Gate_model");
        $this->load->library('encryption');
        header("Access-Control-Allow-Origin: *");
    }
    public function login_post()
    {
        $phone      = $this->input->post("phone");
        $password   = $this->input->post("password");
        $row_count =  $this->Gate_model->row_count(array("phone" => $phone));
        if ($row_count < 1) {
            $this->response([
                'status' => false,
                'message' =>  "No telp tidak di temukan",
            ], 404);
        } else {
            $user2 = $this->Gate_model->get_password(array("phone" => $phone));
            $save_password = $this->encryption->decrypt($user2->password);
            if ($password ==  $save_password) {
                $user = $this->db->query("select a.user_id,us.fullname,us.phone,b.company_id,b.company_name,st.store_id,st.store_name from user_company a
                inner join company b on (a.company_id=b.company_id)
                inner join stores st on (b.company_id=st.company_id)
                inner join users us on (a.user_id=us.id)
                where us.phone='" . $phone . "'  order by b.company_id desc limit 1")->row();
                if (count($user) > 0) {


                    mysqli_next_result($this->db->conn_id);
                    $token = $this->generate_token($user);
                    $data = array(
                        'token' =>  $token,
                        'user_id'  => $user->user_id
                    );
                    $this->db->replace('user_token', $data);

                    $this->response([
                        'status' => true,
                        'token' => $token,
                    ], 200);
                }
                $this->response([
                    'status' => false,
                    'message' =>  "Data Toko Tidak di Temukan",
                ], 404);
            } else {
                $this->response([
                    'status' => false,
                    'message' =>  "No telp tidak di temukan",
                ], 404);
            }
        }
    }
}