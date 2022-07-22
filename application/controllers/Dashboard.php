<?php

use chriskacerguis\RestServer\RestController;

class Dashboard extends RestController
{
    function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        parent::__construct();
        $headers = $this->input->request_headers();

        if (!isset($headers['Authorization'])) {
            $this->response([
                'status' => false,
                'message' => "Token is required"
            ], 401);
        }
        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($headers['Authorization']);
            if ($decodedToken == false || empty($decodedToken) || $decodedToken != "false") {
                $this->response([
                    'status' => false,
                    'decodeToken' => $decodedToken
                ], 401);
            }
        }
    }
    function sales_get()
    {
        $store_id = $this->get("store_id");
        $data = $this->db->query("call w_Dashboard(1 ,'" . $store_id . "','xx') ");
        $this->response([
            'status' => true,
            'data' =>  $data->row(),
        ], 200);
    }

    function chart_get()
    {
        $store_id = $this->get("store_id");
        $data = $this->db->query("call w_Dashboard(7,'" . $store_id . "','xx') ");
        $this->response([
            'status' => true,
            'data' =>  $data->result(),
            'count' => $data->num_rows()
        ], 200);
    }

    function purchasereceipt_get()
    {
        $store_id = $this->get("store_id");
        $data = $this->db->query("call w_Dashboard(2,'" . $store_id . "','xx') ");
        $this->response([
            'status' => true,
            'data' =>  $data->row(),
        ], 200);
    }

    function referal_get()
    {
        $user_id = $this->get("user_id");
        $currentMonth = date('n');
        $currentYear = date('Y');
        $lastMonth = $currentMonth - 1;

        if ($currentMonth == 1) {
            $currentYear = date('Y') - 1;
        }

        $data = $this->db->query("SELECT * FROM users where  MONTH(created_date) = '" . $currentMonth . "' and year(created_date) = '" . $currentYear . "'   and referal_code = '" . $user_id . "' ");
        $datalast = $this->db->query("SELECT * FROM users where  MONTH(created_date) = '" . $lastMonth . "' and year(created_date) = '" . $currentYear . "' and referal_code = '" . $user_id . "' ");

        $this->response([
            'status' => true,
            'current_month' =>  $data->num_rows(),
            'last_month' =>  $datalast->num_rows(),

        ], 200);
    }

    function allreferal_get()
    {
        $user_id = $this->get("user_id");

        $data = $this->db->query("SELECT * FROM users where referal_code = '" . $user_id . "' ");

        $this->response([
            'status' => true,
            'total' =>  $data->num_rows(),

        ], 200);
    }

    function cashin_get()
    {

        $store_id = $this->get("store_id");
        $data = $this->db->query("call w_Dashboard(3 ,'" . $store_id . "','xx') ");
        $this->response([
            'status' => true,
            'data' =>  $data->row(),
        ], 200);
    }

    function cashout_get()
    {

        $store_id = $this->get("store_id");
        $data = $this->db->query("call w_Dashboard(4 ,'" . $store_id . "','xx') ");
        $this->response([
            'status' => true,
            'data' =>  $data->row(),
        ], 200);
    }
}