<?php

use chriskacerguis\RestServer\RestController;

class Vendoropen extends RestController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Master_model");
        $this->load->model("Gate_model");
        $this->load->library('encryption');

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS");
    }

    function ordertmpdelete_post()
    {
        $UserCode = $this->input->post("UserCode");


        if ($UserCode != '') {
            $query = $this->db->query("CALL F_OrderTmpDelete('" . $UserCode . "')");
            $this->response([
                'status' => true,


            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => " Must Be Fill",
            ], 500);
        }
    }

    function orderdetailtmpins_post()
    {
        $GoodsSubGroupCode         = $this->input->post("GoodsSubGroupCode");
        $OthersGroupNote         = $this->input->post("OthersGroupNote");
        $Weight                    = $this->input->post('Weight');
        $Lenght                    = $this->input->post('Lenght');
        $Width                     = $this->input->post('Width');
        $Height                    = $this->input->post('Height');
        $FinalWeight               = $this->input->post('FinalWeight');
        $UserCode                  = $this->input->post("UserCode");



        if ($UserCode != '') {
            $query = $this->db->query("CALL F_OrderDetailTmpIns('" . $GoodsSubGroupCode . "','" . $OthersGroupNote . "','" . $Weight . "','" . $Lenght . "',
            '" . $Width . "','" . $Height . "','" . $FinalWeight . "','" . $UserCode . "')");
            $this->response([
                'status' => true,


            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => " Must Be Fill",
            ], 500);
        }
    }

    function ordertmpins_post()
    {
        $CountryFromCode = $this->input->post("CountryFromCode");
        $CountryToCode =  $this->input->post("CountryToCode");
        $GoodsGroupCode = $this->input->post('GoodsGroupCode');
        $vQtyPieces = $this->input->post('vQtyPieces');
        $vUserCode = $this->input->post('vUserCode');


        if ($CountryFromCode != '' && $CountryToCode != '' && $GoodsGroupCode != '' && $vUserCode != '' && $vQtyPieces != '') {
            $query = $this->db->query("Call F_OrderTmpIns('" . $CountryFromCode . "','" . $CountryToCode . "','" . $GoodsGroupCode . "','" . $vQtyPieces . "','" . $vUserCode . "')");
            $this->response([
                'status' => true,


            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => " Must Be Fill",
            ], 500);
        }
    }

    function vendorsearch_post()
    {
        $CountryFromCode = $this->input->post("CountryFromCode");
        $CountryToCode =  $this->input->post("CountryToCode");
        $Weight = $this->input->post('Weight');
        $Lenght = $this->input->post('Lenght');
        $Width = $this->input->post('Width');
        $Height = $this->input->post('Height');
        $FinalWeight = $this->input->post('FinalWeight');
        $GoodsGroupCode = $this->input->post('GoodsGroupCode');
        $vQtyPieces = $this->input->post('vQtyPieces');



        // if ($CountryFromCode != '' && $CountryToCode != '' && $Weight != '' && $Lenght != '' && $Width != '' && $vQtyPieces != '') {


        $query = $this->db->query("Call F_VendorSearch('" . $CountryFromCode . "','" . $CountryToCode . "','" . $Weight . "','" . $Lenght . "',
                                    '" . $Width . "','" . $Height . "','" . $FinalWeight . "','" . $GoodsGroupCode . "','" . $vQtyPieces . "')")->result();
        $this->response([
            'status' => true,
            'data' =>  $query,

        ], 200);
        // } else {
        //     $this->response([
        //         'status' => false,
        //         'message' => " Must Be Fill",
        //     ], 500);
        // }
    }

    function vendorratesearch_post()
    {
        $vUserCode = $this->input->post('UserCode');


        if ($vUserCode != '') {
            $query = $this->db->query("Call F_VendorRateSearch('" . $vUserCode . "')")->result();
            $this->response([
                'status' => true,
                'data' =>  $query,

            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => " Must Be Fill",
            ], 500);
        }
    }
}
