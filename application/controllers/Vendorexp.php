<?php

use chriskacerguis\RestServer\RestController;

class Vendorexp extends RestController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Master_model");
        $this->load->model("Gate_model");
        $this->load->library('encryption');

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
                    'data' => $decodedToken,
                    'message' => "Invalid Token A"
                ], 401);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Invalid Token B"
            ], 401);
        }
    }

    function vendorsearch_get()
    {
        $CountryFromCode = $this->input->get("CountryFromCode");
        $CountryToCode =  $this->input->get("CountryToCode");
        $Weight = $this->input->get('Weight');
        $Lenght = $this->input->get('Lenght');
        $Width = $this->input->get('Width');
        $Height = $this->input->get('Height');


        if ($CountryFromCode != '' || $CountryToCode != '' || $Weight != '' || $Lenght != '' || $Width != '') {
            $query = $this->db->query("Call APPS_sp_vendorrate_search('" . $CountryFromCode . "','" . $CountryToCode . "','" . $Weight . "','" . $Lenght . "','" . $Width . "','" . $Height . "')")->result();
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

    function listvendorpickup_get()
    {
        $query = $this->db->query("select VendorCode,VendorName,VendorShortName,Coordinate from Vendor 
        where TypeBisnis='T002'")->result();
        $this->response([
            'status' => true,
            'data' =>  $query,

        ], 200);
    }

    function listvendorpickupcategory_get()
    {
        $VendorCode = $this->get('VendorCode');

        $query = $this->db->query("select PickupRateCategoryCode,VendorCode,PickupRateCategoryName,PickupRateCategoryMemo from PickupRateCategory
        where VendorCode='" . $VendorCode . "'")->result();

        $this->response([
            'status' => true,
            'data' =>  $query,

        ], 200);
    }

    function listvendorpickupschedule_get()
    {
        $VendorCode = $this->get('VendorCode');

        $query = $this->db->query("select PickupScheduleCode,VendorCode,PickupScheduleName,StartTime,EndTime from PickupSchedule
        where VendorCode='" . $VendorCode . "'")->result();

        $this->response([
            'status' => true,
            'data' =>  $query,

        ], 200);
    }

    function listpickupvehicle_get()
    {
        $VendorCode = $this->get('VendorCode');

        $query = $this->db->query("select PickupVehicleCode,VendorCode,PickupVehicleNameEN from PickupVehicle
                                  where VendorCode='" . $VendorCode . "'")->result();

        $this->response([
            'status' => true,
            'data' =>  $query,

        ], 200);
    }

    function vendorpickupsearch_get()
    {
        $CountryOriginCode = $this->input->get("CountryOriginCode");
        $VendorCode =  $this->input->get("VendorCode");
        $PickupRateCategoryCode =  $this->input->get("PickupRateCategoryCode");
        $CityOriginCode =  $this->input->get("CityOriginCode");
        $CityDestinationCode =  $this->input->get("CityDestinationCode");

        $Weight = $this->input->get('Weight');
        $Lenght = $this->input->get('Lenght');
        $Width = $this->input->get('Width');
        $Height = $this->input->get('Height');


        if ($CountryOriginCode != '' || $VendorCode != '' || $Weight != '' || $Lenght != '' || $Width != '') {
            $query = $this->db->query("CALL F_VendorPickupRate ('${CountryOriginCode}','${VendorCode}','${PickupRateCategoryCode}','${CityOriginCode}','${CityDestinationCode}',
            '${Weight}','${Lenght}','${Width}','${Height}')")->result();


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

    function vendororderupdate_post()
    {
        $DeviceId = $this->input->post("DeviceId");
        $UserCode =  $this->input->post("UserCode");
        $VendorCode = $this->input->post('VendorCode');
        $VendorRateCode = $this->input->post('VendorRateCode');
        $VendorRate = $this->input->post('VendorRate');



        if ($DeviceId != '' || $UserCode != '' || $VendorCode != '' || $VendorRateCode != '' || $VendorRate != '') {

            $data["header"]   = $this->db->query("Call F_OrderTmpUpdateVendor('" . $DeviceId . "','" . $UserCode . "','" . $VendorCode . "','" . $VendorRateCode . "','" . $VendorRate . "')")->result();
            mysqli_next_result($this->db->conn_id);


            $data["terms"]          = $this->db->query("select TermConditioanDesc from TermCondition order by Id
                                                     ")->result();


            $data["detail"]          = $this->db->query("select FORMAT(Weight ,2)as Weight,FORMAT(Length ,2)as Length,FORMAT(Width ,2)as Width,FORMAT(Height ,2)as Height,
            FORMAT(FinalWeight ,2)as FinalWeight,b.GoodsSubGroupNameID,a.OthersGroupNote 
            from OrderDetailTmp  a
            inner join GoodsSubGroup b on (a.GoodsSubGroupCode=b.GoodsSubGroupCode)
             where UserCode='" . $UserCode . "'
                                                     ")->result();



            $this->response([
                'status' => true,
                'data' =>  $data,
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => " Must Be Fill",
            ], 500);
        }
    }
}
