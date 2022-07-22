<?php

use chriskacerguis\RestServer\RestController;

class Countryopen extends RestController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Master_model");
        $this->load->model("Gate_model");
        $this->load->library('encryption');

        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        Header('Access-Control-Allow-Origin: *'); //for allow any domain, insecure
        Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
        Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE'); //method allowed

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

            if ($CountryFromCode != 'ID') {
                $query = $this->db->query("SELECT CountryCode,CountryName,CountryISOCode,CountryShippingNote,CountryNote,CountryNoteTax FROM  Country 
            where CountryCode ='ID' ")->result();
            } else {
                $query = $this->db->query("SELECT CountryCode,CountryName,CountryISOCode,CountryShippingNote,CountryNote,CountryNoteTax FROM  Country 
                where CountryCode <>'" . $CountryFromCode . "' and  ImportFlag=1 and ExportFlag=1")->result();
            }
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

    function citysearch_post()
    {
        $CountryCode = $this->post('CountryCode');
        $CityName = $this->post('CityName');

        if ($CountryCode != '') {
            $query = $this->db->query("select CountryCode,CityCode,CityName,AirportName from City
            where CountryCode='" . $CountryCode . "' and CityName Like '%" . $CityName . "%' order by CityName ")->result();

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


    function dimensipembagi_get()
    {

        $query = $this->db->query("
        select HardCodeValue from HardCode
        where HardCodeID='HR001'
         ")->result();
        $this->response([
            'status' => true,
            'data' =>  $query,

        ], 200);
    }


    function Ordertmp_get()
    {
        $UserCode = $this->get('UserCode');

        if ($UserCode != '') {
            $data["header"]   = $this->db->query("select 1 as StatusDoc,a.CountryCodeOrigin, co.CountryName as OriginName ,a.CountryCodeDestination,ct.CountryName as DestinationName ,a.VendorCode,b.VendorName,FORMAT(a.FinalWeight,2) as FinalWeight,
            a.QtyPieces as TotalQtyPacket,FORMAT(OrderGrandTotal,0)AS OrderGrandTotal ,a.GoodsGroupCode,d.GoodsGroupNameID,
            ct.CountryShippingNote,ct.CountryNote,ct.TaxThreshold,ct.DutyThreshold,ct.Tax,c.EstimationDays	from OrderTmp a
            inner join Vendor b on (a.VendorCode=b.VendorCode)
            inner join VendorRate c on (a.VendorCode=c.VendorCode and a.VendorRateCode=c.VendorRateCode and a.GoodsGroupCode=c.GoodsGroupCode)
            inner join GoodsGroup d on (a.GoodsGroupCode=d.GoodsGroupCode)
            inner join Country ct on (a.CountryCodeDestination=ct.CountryCode)
            inner join Country co on (a.CountryCodeOrigin=co.CountryCode)
            where UserCode='" . $UserCode . "' ")->result();






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
                'message' => "UserCode  Must Be Fill",
            ], 500);
        }
    }

    function useraddresswhere_get()
    {

        // header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        // Header('Access-Control-Allow-Origin: *'); //for allow any domain, insecure
        // Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
        // Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE'); //method allowed
        // Header('Access-Control-Allow-Headers: x-requested-with'); //method allowed

        header('Access-Control-Allow-Origin: htts://mooleh.com');
        //if you need cookies or login etc
        header('Access-Control-Allow-Credentials: true');

        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Max-Age: 604800');
        //if you need special headers
        header('Access-Control-Allow-Headers: x-requested-with');
        exit(0);



        $UserCode        = $this->input->get("UserCode");
        $CountryCode      = $this->input->get("CountryCode");
        $UserAddressCode      = $this->input->get("UserAddressCode");


        $query = $this->db->query("select UserAddressCode,UserAddressType,AddressName,ContactName,a.CountryCode,b.CountryName,a.CityName,
        a.Address1,Address2,a.PostalCode,a.PhoneNumber,
        a.Email,a.DistrictCode,a.DistrictName
        from UserAddress a
        inner join Country b on (a.CountryCode=b.CountryCode)
        where a.UserCode='" . $UserCode . "' and a.CountryCode='" . $CountryCode . "' and a.UserAddressCode='" . $UserAddressCode  . "';")->result();
        $this->response([
            'status' => true,
            'data' => $query,
        ], 200);
    }

    function useraddreswheretype_get()
    {
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        Header('Access-Control-Allow-Origin: *'); //for allow any domain, insecure
        Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
        Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE'); //method allowed

        $UserCode        = $this->input->get("UserCode");
        $UserAddressType      = $this->input->get("UserAddressType");


        $query = $this->db->query("select UserAddressCode,UserAddressType,AddressName,ContactName,a.CountryCode,b.CountryName,a.CityName,
        a.Address1,Address2,a.PostalCode,a.PhoneNumber,
        a.Email,a.DistrictCode,a.DistrictName
        from UserAddress a
        inner join Country b on (a.CountryCode=b.CountryCode)
        where a.UserCode='" . $UserCode . "' and a.UserAddressType='" . $UserAddressType . "' ;")->result();
        $this->response([
            'status' => true,
            'data' => $query,
        ], 200);
    }
}
