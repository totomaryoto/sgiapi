<?php

use chriskacerguis\RestServer\RestController;

class Country extends RestController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Master_model");
        $this->load->model("Gate_model");
        $this->load->library('encryption');

        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header("Access-Control-Allow-Headers: Origin,X-Requested-With,Content-Type,Accept,Access-Control-Request-Method,Authorization,Cache-Control");
        Header('Access-Control-Allow-Origin: *'); //for allow any domain, insecure
        Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
        Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE'); //method allowed


        $headers = $this->input->request_headers();
        if ($headers['Authorization'] == null) {
            $this->response([
                'status' => false,
                'message' => "Authorization NULL,Token is required",
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



    function countryorigin_get()
    {
        $query = $this->db->query("SELECT CountryCode,CountryName,CountryISOCode,CountryShippingNote,CountryNote,CountryNoteTax
         FROM  Country  where ImportFlag=1 and ExportFlag=1")->result();
        $this->response([
            'status' => true,
            'data' =>  $query,

        ], 200);
    }

    function countrydestination_get()
    {
        $CountryFromCode = $this->get('CountryFromCode');

        if ($CountryFromCode != '') {
            $query = $this->db->query("SELECT CountryCode,CountryName,CountryISOCode,CountryShippingNote,CountryNote,CountryNoteTax FROM  Country 
            where CountryCode <>'" . $CountryFromCode . "' and  ImportFlag=1 and ExportFlag=1")->result();
            $this->response([
                'status' => true,
                'data' =>  $query,

            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => "Origin Country Must Be Fill",
            ], 500);
        }
    }

    function city_post()
    {
        $CountryCode = $this->post('CountryCode');

        if ($CountryCode != '') {
            $query = $this->db->query("select CountryCode,CityCode,CityName,AirportName from City
            where CountryCode='" . $CountryCode . "' order by CityName ")->result();

            $this->response([
                'status' => true,
                'data' =>  $query,

            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => "Origin Country Must Be Fill",
            ], 500);
        }
    }

 



    function countrydestinationnote_get()
    {
        $CountryToCode = $this->get('CountryToCode');

        if ($CountryToCode != '') {
            $query = $this->db->query("select CountryCode,CountryNoteCode,NoteLabel from CountryNoteExt
            where CountryCode ='" . $CountryToCode . "' ")->result();
            $this->response([
                'status' => true,
                'data' =>  $query,

            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => "Origin Destination Must Be Fill",
            ], 500);
        }
    }

    function countrywhere_post()
    {
        $CountryCode = $this->post('CountryCode');

        if ($CountryCode != '') {
            $query = $this->db->query("SELECT CountryCode,CountryName,CountryISOCode,CountryShippingNote,CountryNote,CountryNoteTax FROM  Country 
            where CountryCode ='" . $CountryCode . "' ")->result();
            $this->response([
                'status' => true,
                'data' =>  $query,

            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => "CountryCode Must Be Fill",
            ], 500);
        }
    }

    function goodslist_get()
    {

        $query = $this->db->query("select GoodsGroupCode,GoodsGroupNameEN,GoodsGroupNameID,GoodsGroupNoteEN,GoodsGroupNoteID FROM GoodsGroup ")->result();
        $this->response([
            'status' => true,
            'data' =>  $query,

        ], 200);
    }

    function goodssublist_get()
    {
        $GoodsGroupCode = $this->get('GoodsGroupCode');

        if ($GoodsGroupCode != '') {
            $query = $this->db->query("select GoodsGroupCode,GoodsSubGroupCode,GoodsSubGroupNameEN,GoodsSubGroupNameID,GoodsSubGroupNoteEN,GoodsSubGroupNoteID 
            FROM GoodsSubGroup
            where GoodsGroupCode='" . $GoodsGroupCode . "' ")->result();
            $this->response([
                'status' => true,
                'data' =>  $query,

            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => "GoodsGroup Must Be Fill",
            ], 500);
        }
    }

    function droploc_get()
    {

        $query = $this->db->query("select DropLocationCode,DropLocationType,DropLocationName,Coordinate,DropLocationAddress
        from DropLocation
        where DropLocationStatus=1  ")->result();
        $this->response([
            'status' => true,
            'data' =>  $query,

        ], 200);
    }
    function pickuptype_get()
    {

        $query = $this->db->query("select PickupTypeCode,PickupTypeNameEN,PickupTypeNameID,PickupTypeFlag from PickupType
        where PickupTypeStatus=1  ")->result();
        $this->response([
            'status' => true,
            'data' =>  $query,

        ], 200);
    }
}
