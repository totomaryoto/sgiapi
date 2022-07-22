<?php

use chriskacerguis\RestServer\RestController;

class Order extends RestController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Master_model");
        $this->load->model("Gate_model");
        $this->load->library('encryption');

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS");
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



    function ordertmpupdate_post()
    {
        $UserCodex                =  $this->input->post("UserCode");
        $VendorCode              = $this->input->post('VendorCode');
        $VendorRateCode          = $this->input->post('VendorRateCode');
        $OrderNote              = $this->input->post('OrderNote');
        $PickupTypeCode          = $this->input->post('PickupTypeCode');
        $DropLocationCode        = $this->input->post('DropLocationCode');
        $OrderPrice              = $this->input->post('OrderPrice');
        $VendorPickupCode        = $this->input->post('VendorPickupCode');
        $PickupRateCategoryCode  = $this->input->post('PickupRateCategoryCode');
        $PickupVehicleCode       = $this->input->post('PickupVehicleCode');
        $PickupScheduleCode      = $this->input->post('PickupScheduleCode');
        $PickupPrice             = $this->input->post('PickupPrice');
        $PickupAWBNumber         = $this->input->post('PickupAWBNumber');
        $AddressOriginCode       = $this->input->post('AddressOriginCode');
        $AddressDestinationCode  = $this->input->post('AddressDestinationCode');
        $VoucherCode             = $this->input->post('VoucherCode');
        $PointUsed             = $this->input->post('PointUsed');



        if (
            $PickupTypeCode != '' || $UserCodex != '' || $VendorCode != '' || $VendorRateCode != ''
            || $DropLocationCode != '' || $AddressOriginCode != '' || $AddressDestinationCode != ''
        ) {


            $data["header"]    = $this->db->query("Call F_OrderTmpEdit('" . $UserCodex . "','" . $VendorCode . "','" . $VendorRateCode . "','" . $OrderNote    . "',
                                                                '" . $PickupTypeCode . "','" . $DropLocationCode . "','" . $OrderPrice . "','" . $VendorPickupCode . "',
                                                                '" . $PickupRateCategoryCode . "','" . $PickupVehicleCode . "','" . $PickupScheduleCode . "',
                                                                '" . $PickupPrice . "','" . $PickupAWBNumber . "','" . $AddressOriginCode . "',
                                                                '" . $AddressDestinationCode . "','" . $VoucherCode . "','" . $PointUsed . "'        )")->result();
            mysqli_next_result($this->db->conn_id);






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


    function orderlist_get()
    {
        $UserCode = $this->get('UserCode');



        $query = $this->db->query("select a.OrderCode,b.VendorName,vrc.VendorRateCategoryName,co.CountryName as Origin, cd.CountryName as Destination,a.OrderNote,
        QtyPieces as TotalQty,a.FinalWeight,OrderPrice,IFNULL(OrderDiscount,0)as OrderDiscount,OrderTax,OrderTotal,OrderGrandTotal,
        Case when OrderStatus=0 then 'Dalam Proses' when OrderStatus=1 then'Diterima di Gudang Mooleh'
        when OrderStatus=2 then'Lulus Verifikasi'
        when OrderStatus=3 then'Diterima di Tujuan Pengiriman' when OrderStatus=4 then'Final'
        end as OrderStatusName,
        Case when OrderStatus=0 and IFNULL(PaymentFlag,0)=0 then 'Masih Menunggu Barang Tiba' 
        when OrderStatus=1 and IFNULL(PaymentFlag,0)=0 then'Proses Perhitungan di DiGudang Mooleh' when OrderStatus=1 and IFNULL(PaymentFlag,0)=1  then'Belum Dibayar'
        when OrderStatus=1 and IFNULL(PaymentFlag,0)=2  then'Lunas'
        end as PaymentStatusName,DropLocationName,AddressNameOrigin,PhoneNumberOrigin
        AddressNameDestination,PhoneNumberDestination
        from 
        OrderHeader a
        inner join Vendor b on (a.VendorCode=b.VendorCode)
        inner join VendorRate vr on (a.VendorRateCode=vr.VendorRateCode)
        inner join VendorRateCategory vrc on (a.VendorCode=vrc.VendorCode and vr.VendorRateCategoryCode=vrc.VendorRateCategoryCode)
        inner join Country co on (a.CountryCodeOrigin=co.CountryCode)
        inner join Country cd on (a.CountryCodeDestination=cd.CountryCode)
        inner join DropLocation dl on (a.DropLocationCode=dl.DropLocationCode)
        where a.UserCode ='" . $UserCode . "'")->result();

        $this->response([
            'status' => true,
            'data' =>  $query,

        ], 200);
    }


    function orderdetail_get()
    {
        $UserCode = $this->get('UserCode');
        $OrderCode = $this->get('OrderCode');



        $data["header"]    = $this->db->query("select a.OrderCode,b.VendorName,vrc.VendorRateCategoryName,co.CountryName as Origin, cd.CountryName as Destination,a.OrderNote,
        QtyPieces as TotalQty,a.FinalWeight,OrderPrice,IFNULL(OrderDiscount,0)as OrderDiscount,OrderTax,OrderTotal,OrderGrandTotal,
        Case when OrderStatus=0 then 'Dalam Proses' when OrderStatus=1 then'Diterima di Gudang Mooleh'
        when OrderStatus=2 then'Lulus Verifikasi'
        when OrderStatus=3 then'Diterima di Tujuan Pengiriman' when OrderStatus=4 then'Final'
        end as OrderStatusName,
        Case when OrderStatus=0 and IFNULL(PaymentFlag,0)=0 then 'Masih Menunggu Barang Tiba' 
        when OrderStatus=1 and IFNULL(PaymentFlag,0)=0 then'Proses Perhitungan di DiGudang Mooleh' when OrderStatus=1 and IFNULL(PaymentFlag,0)=1  then'Belum Dibayar'
        when OrderStatus=1 and IFNULL(PaymentFlag,0)=2  then'Lunas'
        end as PaymentStatusName,DropLocationName,AddressNameOrigin,PhoneNumberOrigin
        AddressNameDestination,PhoneNumberDestination
        from 
        OrderHeader a
        inner join Vendor b on (a.VendorCode=b.VendorCode)
        inner join VendorRate vr on (a.VendorRateCode=vr.VendorRateCode)
        inner join VendorRateCategory vrc on (a.VendorCode=vrc.VendorCode and vr.VendorRateCategoryCode=vrc.VendorRateCategoryCode)
        inner join Country co on (a.CountryCodeOrigin=co.CountryCode)
        inner join Country cd on (a.CountryCodeDestination=cd.CountryCode)
        inner join DropLocation dl on (a.DropLocationCode=dl.DropLocationCode)
        where a.UserCode ='" . $UserCode . "' and OrderCode='" . $OrderCode . "'")->result();


        $data["detail"]          = $this->db->query("select Weight,Length,Width,Height,FinalWeight,b.GoodsSubGroupNameID,a.OthersGroupNote from OrderDetail a
           inner join GoodsSubGroup b on (a.GoodsSubGroupCode=b.GoodsSubGroupCode)
           where  OrderCode='" . $OrderCode . "'
        ")->result();


        $this->response([
            'status' => true,
            'data' =>  $data,

        ], 200);
    }
}
