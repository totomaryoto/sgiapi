<?php

use chriskacerguis\RestServer\RestController;

class Users extends RestController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Master_model");
        $this->load->model("User_model");
        $this->load->model("Gate_model");
        $this->load->library('encryption');

        $this->load->model("Transaction_model");
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        $headers = $this->input->request_headers();
        if ($headers['Authorization'] == null) {
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
                ], 401);
            }
        }
    }


    function useraddress_get()
    {
        $UserCode        = $this->input->get("UserCode");
        $CountryCode      = $this->input->get("CountryCode");


        $query = $this->db->query("select UserAddressCode,UserAddressType,AddressName,ContactName,a.CountryCode,b.CountryName,a.CityName,
        a.Address1,Address2,a.PostalCode,a.PhoneNumber,
        a.Email,a.DistrictCode,a.DistrictName
        from UserAddress a
        inner join Country b on (a.CountryCode=b.CountryCode)
        where a.UserCode='" . $UserCode . "' and a.CountryCode='" . $CountryCode  . "';")->result();
        $this->response([
            'status' => true,
            'data' => $query,
        ], 200);
    }

    function useraddressins_post()
    {

        $UserCode           = $this->input->post("UserCode");
        $AddressType        = $this->input->post("AddressType");
        $AddresName         = $this->input->post("AddresName");
        $ContactName        = $this->input->post("ContactName");
        $CountryCode        = $this->input->post("CountryCode");
        $CityCode           = $this->input->post("CityCode");
        $CityName           = $this->input->post("CityName");
        $DistictCode        = $this->input->post("DistictCode");
        $DistrictName       = $this->input->post("DistrictName");
        $Address1           = $this->input->post("Address1");
        $Address2           = $this->input->post("Address2");
        $PostalCode         = $this->input->post("PostalCode");
        $PhoneNumber        = $this->input->post("PhoneNumber");
        $Email              = $this->input->post("Email");

        $query = $this->db->query("call APPS_sp_user_address_input ('" . $UserCode . "','" . $AddressType . "','" . $AddresName . "',
        '" . $ContactName . "','" . $CountryCode . "','" . $CityCode . "','" . $CityName . "','" . $DistictCode . "','" . $DistrictName . "',
        '" . $Address1 . "','" . $Address2 . "','" . $PostalCode . "','" . $PhoneNumber . "','" . $Email . "')")->result();
        $this->response([
            'status' => true,
            'data' => $query,
        ], 200);
    }

    function useraddressupdate_post()
    {
        $UserAddressCode           = $this->input->post("UserAddressCode");
        $UserCode           = $this->input->post("UserCode");
        $AddressType        = $this->input->post("AddressType");
        $AddresName         = $this->input->post("AddresName");
        $ContactName        = $this->input->post("ContactName");
        $CountryCode        = $this->input->post("CountryCode");
        $CityCode           = $this->input->post("CityCode");
        $CityName           = $this->input->post("CityName");
        $DistictCode        = $this->input->post("DistictCode");
        $DistrictName       = $this->input->post("DistrictName");
        $Address1           = $this->input->post("Address1");
        $Address2           = $this->input->post("Address2");
        $PostalCode         = $this->input->post("PostalCode");
        $PhoneNumber        = $this->input->post("PhoneNumber");
        $Email              = $this->input->post("Email");

        $query = $this->db->query("call APPS_sp_user_address_edit ('" . $UserAddressCode . "','" . $UserCode . "','" . $AddressType . "','" . $AddresName . "',
        '" . $ContactName . "','" . $CountryCode . "','" . $CityCode . "','" . $CityName . "','" . $DistictCode . "','" . $DistrictName . "',
        '" . $Address1 . "','" . $Address2 . "','" . $PostalCode . "','" . $PhoneNumber . "','" . $Email . "')")->result();
        $this->response([
            'status' => true,
            'data' => $query,
        ], 200);
    }

    function vcr_get()
    {


        $query = $this->db->query("SELECT VoucherCode,VoucherAliasCode,VoucherName,VoucherValue,VoucherStartDate,VoucherEndDate,
        'https://ecs7.tokopedia.net/img/blog/promo/2022/07/FeatureImage_Linkaja.jpg' as VoucherPhoto
        FROM Voucher
        where VoucherFlag=1 and NOW() BETWEEN VoucherStartDate and  VoucherEndDate
        
        ")->result();
        $this->response([
            'status' => true,
            'data' => $query,
        ], 200);
    }

    function pointusers_get($UserCode)
    {


        $query = $this->db->query("SELECT UserCode,UserPhone1,UserPhone2,UserEmail,LanguageCode,PointTotal from Users
        where UserCode='" . $UserCode . "';       
        ")->result();
        $this->response([
            'status' => true,
            'data' => $query,
        ], 200);
    }
}
