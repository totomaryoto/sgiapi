<?php

use chriskacerguis\RestServer\RestController;

class Profile extends RestController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Master_model");
        header("Access-Control-Allow-Origin: *");

    }

    function image_delete()
    {
        $user_id   = $this->delete('user_id');
        $data = $this->db->query("SELECT * FROM users where id = '" . $user_id . "' ")->row();

        if (unlink('./assets/file_upload/user_images/' . $data->user_image)) {
            $this->db->query("UPDATE users set user_image = NULL where id = '" . $user_id . "' ");
            $this->response([
                'status' => true,
                'message' => "OK"
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' =>  "Harap periksa kembali gambar anda",
                'error' => "Woopsy"
            ], 400);
        }
    }

    function image_post()
    {

        $user_id =  $this->input->post("user_id");

        $config['upload_path']          = './assets/file_upload/user_images/';
        $config['allowed_types']        = 'gif|jpg|png';

        $config['encrypt_name']         = TRUE;
        $config['quality']              = '50%';
        $this->load->library('upload', $config);



        if (!$this->upload->do_upload('userfile')) {
            $error = array('error' => $this->upload->display_errors());

            $this->response([
                'status' => false,
                'message' =>  "Harap periksa kembali gambar anda",
                'error' => $error
            ], 400);
        }
        $upload_result      =  $this->upload->data();
        $file_name = $upload_result['file_name'];

        $this->db->query("UPDATE users set user_image = '" . $file_name . "' where id = '" . $user_id . "' ");

        $this->response([
            'status' => true,
            'file_name' => $upload_result['file_name'],
        ], 200);
    }

    function bankinformation_get()
    {
        $user_id =  $this->get("user_id");
        $data = $this->db->query("SELECT rekening_no, rekening_name, rekening_bank from users where id = '" . $user_id . "' ")->row();

        if ($data) {
            $this->response([
                'status' => true,
                'data' => $data,
            ], 200);
        }
        $this->response([
            'status' => false,
        ], 200);
    }


    function bankinformation_post()
    {
        $user_id =  $this->input->post("user_id");
        $rekening_no =  $this->input->post("rekening_no");
        $rekening_name =  $this->input->post("rekening_name");
        $rekening_bank =  $this->input->post("rekening_bank");

        $query = $this->db->query("UPDATE users set 
        rekening_no = '" . $rekening_no . "', 
        rekening_name = '" . $rekening_name . "', 
        rekening_bank = '" . $rekening_bank . "'
        where id = '" . $user_id . "' 
          ");

        if ($query) {
            $this->response([
                'status' => true,
            ], 200);
        } else {
            $this->response([
                'status' => true,
            ], 200);
        }
    }

    function subscription_get()
    {
        $user_id =  $this->get("user_id");
        $query = $this->db->query("select a.valid_from,a.valid_to,b.subscription_name from user_subscriptions a 
                                inner join subscriptions b on a.subscription_id = b.id 
                                WHERE user_id = '" . $user_id . "' order by a.id desc LIMIT 1
                                ")->row();

        $valid_from     = strtotime($query->valid_from);
        $valid_from     = date('Y-m-d', $valid_from);

        $valid_to       = strtotime($query->valid_to);
        $valid_to       = date('Y-m-d', $valid_to);

        $current_date   = date('Y-m-d');

        $diff = abs(strtotime($current_date) - strtotime($valid_to));

        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

        $response = array(
            "expired_date"          => $valid_to,
            "expired_in"            => round($diff / (60 * 60 * 24)),
            "subscription_name"     => $query->subscription_name,
        );


        if ($query) {
            $this->response([
                'status' => true,
                'data' => $response
            ], 200);
        } else {
            $this->response([
                'status' => false,
            ], 200);
        }
    }
}