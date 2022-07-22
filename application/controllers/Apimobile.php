<?php

use chriskacerguis\RestServer\RestController;

class Apimobile extends RestController
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



    function index_get()
    {

        $this->response([
            'status' => true,
            'data' =>  "its Works",

        ], 200);
    }

    function userlogin_get()
    {
        $username = $this->input->get("UserName");
        $password = $this->input->get("Password");
        $token = $this->input->get("Token");

        $data = $this->db->query("CALL APPS_sp_user_login('" . $username . "','" . $password . "','" . $token . "')")->result();

        echo json_encode($data);
    }

    function userregister_get()
    {
        $UserFullName = $this->input->get("UserFullName");
        $UserNickName = $this->input->get("UserNickName");
        $UserPassword = $this->input->get("UserPassword");
        $UserEmail = $this->input->get("UserEmail");
        $UserPhone1 = $this->input->get("UserPhone1");
        $UserPhone2 = $this->input->get("UserPhone2");
        $LanguageCode = $this->input->get("LanguageCode");

        $data = $this->db->query("CALL APPS_sp_user_register(
            '" . $UserFullName . "',
            '" . $UserNickName . "',
            '" . $UserPassword . "',
            '" . $UserEmail . "',
            '" . $UserPhone1 . "',
            '" . $UserPhone2 . "',
            " . $LanguageCode . "
            )")->result();

        echo json_encode($data);
    }

    function userverification2_get()
    {
        $UserEmail = $this->input->get("UserEmail");
        $UserOTP = $this->input->get("UserOTP");
        $UserToken = $this->input->get("UserToken");

        $data = $this->db->query("CALL APPS_sp_user_verification2(
        '" . $UserEmail . "',
        '" . $UserOTP . "',
        '" . $UserToken . "'
        )")->result();

        echo json_encode($data);
    }

    function userresend_get()
    {
        $UserEmail = $this->input->get("UserEmail");

        $data = $this->db->query("CALL APPS_sp_user_resend(
            '" . $UserEmail . "'
            )")->result();

        echo json_encode($data);
    }

    function userpasswordupdate_get()
    {
        $UserCode = $this->input->get("UserCode");
        $OldPassword = $this->input->get("OldPassword");
        $UserPassword = $this->input->get("UserPassword");

        $data = $this->db->query("CALL APPS_sp_user_password_update(
            '" . $UserCode . "',
            '" . $OldPassword . "',
            '" . $UserPassword . "'
            )")->result();

        echo json_encode($data);
    }

    function userforgotpassword_get()
    {
        $UserEmail = $this->input->get("UserEmail");
        $UserPassword = $this->input->get("UserPassword");

        $data = $this->db->query("CALL APPS_sp_user_forgot_password(
            '" . $UserEmail . "',
            '" . $UserPassword . "'
            )")->result();

        echo json_encode($data);
    }


    function ppninfo_get()
    {

        $data = $this->db->query("SELECT 
        IFNULL(CONCAT(FORMAT(HardCode.HardCodeValue,2)),0) AS PPN 
        FROM HardCode WHERE HardCodeID = 'HR003'; ")->result();

        echo json_encode($data);
    }

    function iklanlist_get()
    {

        $data = $this->db->query("SELECT 
        IklanCode,
        IklanName,
        IFNULL(IklanPhoto,'') AS IklanPhoto,
        IFNULL(IklanNote,'') AS IklanNote
        FROM Iklan 
        WHERE IklanStatus = 1 ")->result();

        echo json_encode($data);
    }

    function bankvalist_get()
    {
        $query = "SELECT 
        CONVERT(MasterBank.KodeBank,CHAR) AS BankCode,
        NamaBank AS BankName,
        OYCode,
        IFNULL(HardCode.HardCodeValue,0) AS Nominal
        FROM 
        MasterBank
        LEFT OUTER JOIN HardCode
        ON MasterBank.HardCodeID = HardCode.HardCodeID
        WHERE MasterBank.VAFlag = 1;";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function iklancount_get()
    {

        $data = $this->db->query("SELECT Count(IklanCode) AS TotalIklan FROM Iklan
        WHERE IklanStatus = 1; ")->result();

        echo json_encode($data);
    }

    function newslist_get()
    {

        $data = $this->db->query("SELECT 
        NewsCode,
        NewsTitleID,
        NewsTitleEN,
        IFNULL(NewsPhoto,'') AS NewsPhoto
        FROM News 
        WHERE NewsStatus = 1
        ORDER BY CreatedDate DESC ")->result();

        echo json_encode($data);
    }

    function countrylist_get()
    {
        $Filter = $this->input->get("Filter");
        $PageFlag = $this->input->get("PageFlag");
        $OriginCode = $this->input->get("OriginCode");

        $query = "SELECT 
        CountryCode,
        CountryName
        FROM Country WHERE CountryName LIKE '%" . $Filter . "%' ";

        if ($PageFlag == 'ORIGIN') {
            $query = $query . " AND ImportFlag = 1";
        }

        if ($PageFlag == 'DESTINATION') {
            $query = $query . " AND ExportFlag = 1";

            if ($OriginCode == 'ID') {
                $query = $query . " AND CountryCode != 'ID'";
            } else {
                $query = $query . " AND CountryCode = 'ID'";
            }
        }

        $query = $query . " ORDER BY CountryName; ";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function goodsgrouplist_get()
    {
        $query = "SELECT 
        GoodsGroupCode,
        GoodsGroupNameEN,
        GoodsGroupNameID,
        GoodsGroupNoteEN,
        GoodsGroupNoteID
        FROM GoodsGroup ORDER BY GoodsGroupCode; ";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function goodsubgrouplist_get()
    {
        $GoodsGroupCode = $this->input->get("GoodsGroupCode");

        $query = "SELECT 
        GoodsSubGroupCode,
        GoodsSubGroupNameEN,
        GoodsSubGroupNameID,
        GoodsSubGroupNoteEN,
        GoodsSubGroupNoteID
        FROM GoodsSubGroup
        WHERE GoodsGroupCode = '" . $GoodsGroupCode . "'
        ORDER BY GoodsSubGroupCode; ";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function vendorratelist_get()
    {
        $CountryOriginCode = $this->input->get("CountryOriginCode");
        $CountryDestinationCode = $this->input->get("CountryDestinationCode");
        $GoodsGroupCode = $this->input->get("GoodsGroupCode");
        $Weight = $this->input->get("Weight");
        $Length = $this->input->get("Length");
        $Width = $this->input->get("Width");
        $Height = $this->input->get("Height");

        $query = "CALL APPS_sp_vendorrate_search(
            '" . $CountryOriginCode . "',
            '" . $CountryDestinationCode . "',
            '" . $GoodsGroupCode . "',
            " . $Weight . ",
            " . $Length . ",
            " . $Width . ",
            " . $Height . "); ";

        //print($query);

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function shipinfolist_get()
    {
        $CountryCode = $this->input->get("CountryCode");
        $VendorRateCategoryCode = $this->input->get("VendorRateCategoryCode");

        $query = "SELECT 
        IFNULL(CountryNote,'') AS Note
        FROM Country WHERE CountryCode = '" . $CountryCode . "'
        
        UNION ALL
        
        SELECT 
        IFNULL(VendorRateCAtegoryMemo,'') AS Note
        FROM VendorRateCategory WHERE VendorRateCategoryCode = '" . $VendorRateCategoryCode . "'
        
        UNION ALL

        SELECT
        IFNULL(HardCode.HardCodeText,'') AS Note
        FROM HardCode WHERE HardCodeID = 'HR002'; ";

        //print($query);

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function addresslist_get()
    {
        $UserCode = $this->input->get("UserCode");
        $UserAddressType = $this->input->get("UserAddressType");

        $query = "SELECT 
        UserAddress.UserAddressCode,
        UserAddress.UserAddressType,
        UserAddress.AddressName,
        UserAddress.ContactName,
        UserAddress.CountryCode,
        Country.CountryName,
        UserAddress.CityCode,
        UserAddress.CityName,
        UserAddress.Address1,
        UserAddress.Address2,
        UserAddress.PostalCode,
        UserAddress.PhoneNumber,
        UserAddress.Email
        FROM UserAddress
        INNER JOIN Country
        ON UserAddress.CountryCode = Country.CountryCode
        WHERE UserAddress.UserCode = '" . $UserCode . "'
        AND UserAddress.UserAddressType = '" . $UserAddressType . "'; ";

        //print($query);

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function citylist_get()
    {
        $CountryCode = $this->input->get("CountryCode");
        $Filter = $this->input->get("Filter");

        $query = "SELECT 
        CountryCode,
        CityCode,
        CityName
        FROM City WHERE CountryCode LIKE '" . $CountryCode . "' 
        AND CityName LIKE '%" . $Filter . "%' 
        
        UNION ALL
        
        SELECT
        'XX' AS CountryCode,
        '9999999999' AS CityCode,
        'Others' AS CityName
        ORDER BY CityName; ";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function districtlist_get()
    {
        $CountryCode = $this->input->get("CountryCode");
        $CityCode = $this->input->get("CityCode");
        $Filter = $this->input->get("Filter");

        $query = "SELECT 
        CountryCode,
        CityCode,
        DistrictCode,
        DistrictName
        FROM District WHERE CountryCode LIKE '" . $CountryCode . "' 
        AND CityCode LIKE '" . $CityCode . "' 
        AND DistrictName LIKE '%" . $Filter . "%' ; ";

        //print($query);

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function useraddressinput_get()
    {
        $UserCode = $this->input->get("UserCode");
        $UserAddressType = $this->input->get("UserAddressType");
        $AddressName = $this->input->get("AddressName");
        $ContactName = $this->input->get("ContactName");
        $CountryCode = $this->input->get("CountryCode");
        $CityCode = $this->input->get("CityCode");
        $CityName = $this->input->get("CityName");
        $DistrictCode = $this->input->get("DistrictCode");
        $DistrictName = $this->input->get("DistrictName");
        $Address1 = $this->input->get("Address1");
        $Address2 = $this->input->get("Address2");
        $PostalCode = $this->input->get("PostalCode");
        $PhoneNumber = $this->input->get("PhoneNumber");
        $Email = $this->input->get("Email");

        $query = "CALL APPS_sp_user_address_input(
            '" . $UserCode . "',
            '" . $UserAddressType . "',
            '" . $AddressName . "',
            '" . $ContactName . "',
            '" . $CountryCode . "',
            '" . $CityCode . "',
            '" . $CityName . "',
            '" . $DistrictCode . "',
            '" . $DistrictName . "',
            '" . $Address1 . "',
            '" . $Address2 . "',
            '" . $PostalCode . "',
            '" . $PhoneNumber . "',
            '" . $Email . "'
            )";

        //print($query);

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function useraddressedit_get()
    {
        $UserAddressCode = $this->input->get("UserAddressCode");
        $UserCode = $this->input->get("UserCode");
        $UserAddressType = $this->input->get("UserAddressType");
        $AddressName = $this->input->get("AddressName");
        $ContactName = $this->input->get("ContactName");
        $CountryCode = $this->input->get("CountryCode");
        $CityCode = $this->input->get("CityCode");
        $CityName = $this->input->get("CityName");
        $DistrictCode = $this->input->get("DistrictCode");
        $DistrictName = $this->input->get("DistrictName");
        $Address1 = $this->input->get("Address1");
        $Address2 = $this->input->get("Address2");
        $PostalCode = $this->input->get("PostalCode");
        $PhoneNumber = $this->input->get("PhoneNumber");
        $Email = $this->input->get("Email");

        $query = "CALL APPS_sp_user_address_edit(
            '" . $UserAddressCode . "',
            '" . $UserCode . "',
            '" . $UserAddressType . "',
            '" . $AddressName . "',
            '" . $ContactName . "',
            '" . $CountryCode . "',
            '" . $CityCode . "',
            '" . $CityName . "',
            '" . $DistrictCode . "',
            '" . $DistrictName . "',
            '" . $Address1 . "',
            '" . $Address2 . "',
            '" . $PostalCode . "',
            '" . $PhoneNumber . "',
            '" . $Email . "'
            )";

        //print($query);

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function addressinfo_get()
    {
        $UserAddressCode = $this->input->get("UserAddressCode");

        $query = "SELECT 
        UserAddress.UserAddressCode,
        UserAddress.UserAddressType,
        UserAddress.AddressName,
        UserAddress.ContactName,
        UserAddress.CountryCode,
        Country.CountryName,
        UserAddress.CityCode,
        UserAddress.CityName,
        UserAddress.Address1,
        UserAddress.Address2,
        UserAddress.PostalCode,
        UserAddress.PhoneNumber,
        UserAddress.Email
        FROM UserAddress
        INNER JOIN Country
        ON UserAddress.CountryCode = Country.CountryCode
        WHERE UserAddress.UserAddressCode = '" . $UserAddressCode . "'; ";

        //print($query);

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function pickuptypelist_get()
    {
        $query = "SELECT 
        PickupTypeCode,
        PickupTypeNameEN,
        PickupTypeNameID,
        CONVERT(PickupTypeFlag, CHAR) AS PickupTypeFlag,
        CONVERT(PickupTypeStatus, CHAR) AS PickupTypeStatus
        FROM PickupType WHERE PickupTypeStatus = 1
        ORDER BY PickupTypeCode; ";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function droplocationlist_get()
    {
        $query = "SELECT 
        DropLocationCode,
        DropLocation.CityCode,
        City.CityName,
        DropLocation.DropLocationType,
        DropLocation.DropLocationName,
        DropLocation.DropLocationAddress,
        DropLocation.DropLatitude,
        DropLocation.DropLongitude
        FROM DropLocation
        INNER JOIN City
        ON  DropLocation.CityCode = City.CityCode 
        WHERE DropLocation.DropLocationStatus = 1
        ORDER BY  DropLocationCode; ";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function faqlist_get()
    {
        $query = "SELECT 
        FaqCode,
        FaqQuestionEN,
        FaqQuestionID,
        FaqAnswerEN,
        FaqAnswerID
        FROM Faq
        ORDER BY FaqCode; ";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function vendorpickuplist_get()
    {
        $query = "SELECT
        VendorCode,
        VendorName,
        VendorShortName
        FROM Vendor
        WHERE TypeBisnis = 'T002'
        AND StatusVendor = 1; ";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function pickupratecategorylist_get()
    {
        $VendorCode = $this->input->get("VendorCode");

        $query = "SELECT
        Vendor.VendorCode,
        Vendor.VendorName,
        Vendor.VendorShortName,
        PickupRateCategory.PickupRateCategoryCode,
        PickupRateCategory.PickupRateCategoryName
        FROM Vendor
        INNER JOIN PickupRateCategory
        ON Vendor.VendorCode = PickupRateCategory.VendorCode
        WHERE Vendor.VendorCode = '" . $VendorCode . "'
        AND StatusVendor = 1; ";


        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function pickupvehiclelist_get()
    {
        $VendorCode = $this->input->get("VendorCode");

        $query = "SELECT
        Vendor.VendorCode,
        Vendor.VendorName,
        Vendor.VendorShortName,
        PickupVehicle.PickupVehicleCode,
        PickupVehicle.PickupVehicleNameEN,
        PickupVehicle.PickupVehicleNameID
        FROM Vendor
        INNER JOIN PickupVehicle
        ON Vendor.VendorCode = PickupVehicle.VendorCode
        WHERE Vendor.VendorCode = '" . $VendorCode . "'
        AND StatusVendor = 1; ";


        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function pickupschedulelist_get()
    {
        $VendorCode = $this->input->get("VendorCode");

        $query = "SELECT
        Vendor.VendorCode,
        Vendor.VendorName,
        Vendor.VendorShortName,
        PickupSchedule.PickupScheduleCode,
        PickupSchedule.PickupScheduleName
        FROM Vendor
        INNER JOIN PickupSchedule
        ON Vendor.VendorCode = PickupSchedule.VendorCode
        WHERE Vendor.VendorCode = '" . $VendorCode . "'
        AND StatusVendor = 1; ";


        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function paymentmethodlist_get()
    {
        $query = "SELECT 
        PaymentMethod.PaymentMethodCode,
        PaymentMethod.PaymentMethodName,
        PaymentMethod.PaymentMethodOYCode,
        IFNULL(HC1.HardCodeValue,0) AS Percentage,
        IFNULL(HC2.HardCodeValue,0) AS Nominal
        FROM PaymentMethod
        LEFT OUTER JOIN HardCode AS HC1
        ON PaymentMethod.HardCodePercentageID = HC1.HardCodeID
        LEFT OUTER JOIN HardCode AS HC2
        ON PaymentMethod.HardCodeNominalID = HC2.HardCodeID
        WHERE FlagNumber = 1
        ORDER BY PaymentMethod.`PaymentMethodCode`; ";


        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function voucherlist_get()
    {
        $query = "SELECT 
        VoucherCode,
        VoucherAliasCode,
        CONVERT(VoucherFlag,CHAR) AS VoucherFlag,
        VoucherName,
        IFNULL(CONCAT(FORMAT(VoucherValue,2)),0) AS VoucherValue,
        IFNULL(VoucherPhoto,'') AS VoucherPhoto,
        IFNULL(CONVERT(DATE_FORMAT(VoucherStartDate, '%d-%m-%Y'), CHAR),'') AS VoucherStartDate,
        IFNULL(CONVERT(DATE_FORMAT(VoucherEndDate, '%d-%m-%Y'), CHAR),'') AS VoucherEndDate
        FROM Voucher
        WHERE CURDATE() BETWEEN VoucherStartDate AND VoucherEndDate; ";


        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function userprofile_get()
    {
        $UserCode = $this->input->get("UserCode");

        $query = "SELECT 
        Users.UserCode,
        Users.UserFullName,
        Users.UserNickName,
        Users.UserPhone1,
        Users.UserEmail,
        IFNULL(Users.UserPhoto,'') AS UserPhoto,
        IF(Users.LanguageCode = '1','INDONESIA','ENGLISH') AS LanguageUser,
        CONVERT(Users.LanguageCode,CHAR) AS LanguageCode
        FROM Users
        WHERE Users.UserCode = '" . $UserCode . "' LIMIT 0,1";


        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function userprofileupdate_get()
    {
        $UserCode = $this->input->get("UserCode");
        $UserFullName = $this->input->get("UserFullName");
        $UserNickName = $this->input->get("UserNickName");
        $UserPhone1 = $this->input->get("UserPhone1");
        $LanguageCode = $this->input->get("LanguageCode");

        $query = "CALL APPS_sp_user_update(
            '" . $UserCode . "',
            '" . $UserFullName . "',
            '" . $UserNickName . "',
            '" . $UserPhone1 . "',
            " . $LanguageCode . "
            )";


        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }



    //---------------------------- Transaksi Shipping Order

    function orderheaderinput_get()
    {
        $UserCode = $this->input->get("UserCode");
        $GoodsGroupCode = $this->input->get("GoodsGroupCode");
        $QtyPieces = $this->input->get("QtyPieces");
        $CountryCodeOrigin = $this->input->get("CountryCodeOrigin");
        $CountryCodeDestination = $this->input->get("CountryCodeDestination");

        $data = $this->db->query("CALL APPS_sp_order_header_input(
            '" . $UserCode . "',
            '" . $GoodsGroupCode . "',
            " . $QtyPieces . ",
            '" . $CountryCodeOrigin . "',
            '" . $CountryCodeDestination . "'
            ) ")->result();

        echo json_encode($data);
    }

    function orderheaderdelete_get()
    {
        $OrderCode = $this->input->get("OrderCode");

        $data = $this->db->query("CALL APPS_sp_order_header_delete(
            '" . $OrderCode . "'
            ) ")->result();

        echo json_encode($data);
    }

    function orderheaderedit_get()
    {
        $OrderCode = $this->input->get("OrderCode");
        $UserCode = $this->input->get("UserCode");
        $VendorCode = $this->input->get("VendorCode");
        $VendorRateCode = $this->input->get("VendorRateCode");
        $OrderNote = $this->input->get("OrderNote");
        $PickupTypeCode = $this->input->get("PickupTypeCode");
        $DropLocationCode = $this->input->get("DropLocationCode");
        $OrderPrice = $this->input->get("OrderPrice");
        $VendorPickupCode = $this->input->get("VendorPickupCode");
        $PickupRateCategoryCode = $this->input->get("PickupRateCategoryCode");
        $PickupVehicleCode = $this->input->get("PickupVehicleCode");
        $PickupScheduleCode = $this->input->get("PickupScheduleCode");
        $PickupPrice = $this->input->get("PickupPrice");
        $PickupAWBNumber = $this->input->get("PickupAWBNumber");
        $AddressOriginCode = $this->input->get("AddressOriginCode");
        $AddressDestinationCode = $this->input->get("AddressDestinationCode");

        $query = "CALL APPS_sp_order_header_edit(
            '" . $OrderCode . "',
            '" . $UserCode . "',
            '" . $VendorCode . "',
            '" . $VendorRateCode . "',
            '" . $OrderNote . "',
            '" . $PickupTypeCode . "',
            '" . $DropLocationCode . "',
            " . $OrderPrice . ",
            '" . $VendorPickupCode . "',
            '" . $PickupRateCategoryCode . "',
            '" . $PickupVehicleCode . "',
            '" . $PickupScheduleCode . "',
            " . $PickupPrice . ",
            '" . $PickupAWBNumber . "',
            '" . $AddressOriginCode . "',
            '" . $AddressDestinationCode . "'
            )";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function orderlist_get()
    {
        $UserCode = $this->input->get("UserCode");


        $query = "SELECT 
        OrderHeader.OrderCode,
        IFNULL(OrderHeader.DocumentNo,'') AS DocumentNo,
        Origin.CountryCode AS OriginCode,
        Origin.CountryName AS OriginName,
        Destination.CountryCode AS DestinationCode,
        Destination.CountryName AS DestinationName,
        OrderHeader.VendorCode,
        Vendor.VendorName,
        VendorRate.VendorRateCode,
        VendorRateCategory.VendorRateCategoryName,
        OrderHeader.GoodsGroupCode,
        GoodsGroup.GoodsGroupNameEN,
        GoodsGroup.GoodsGroupNameID,
        IFNULL(CONVERT(DATE_FORMAT(OrderHeader.OrderDate, '%d-%m-%Y'), CHAR),'') AS OrderDate,
        IFNULL(CONVERT(DATE_FORMAT(OrderHeader.OrderDate, '%Y-%m-%d'), CHAR),'') AS OrderDate2,
        IFNULL(CONVERT(DATE_FORMAT(OrderHeader.OrderDate, '%H:%i'), CHAR),'') AS OrderTime,
        IFNULL(OrderHeader.OrderNote,'') AS OrderNote,
        CONCAT(FORMAT(IFNULL(OrderHeader.QtyPieces,0),0)) AS QtyPieces,
        CONCAT(FORMAT(IFNULL(OrderHeader.Weight,0),1)) AS Weight,
        CONCAT(FORMAT(IFNULL(OrderHeader.Length,0),0)) AS Length,
        CONCAT(FORMAT(IFNULL(OrderHeader.Width,0),0)) AS Width,
        CONCAT(FORMAT(IFNULL(OrderHeader.Height,0),0)) AS Heigth,
        CONCAT(FORMAT(IFNULL(OrderHeader.FinalWeight,0),1)) AS FinalWeight,
        IFNULL(OrderHeader.DropLocationCode,'') AS DropLocationCode,
        IFNULL(DropLocation.DropLocationName,'') AS DropLocationName,
        IFNULL(CONCAT(FORMAT(OrderHeader.OrderPrice,2)),0) AS OrderPrice,
        IFNULL(CONCAT(FORMAT(OrderHeader.AdminFee,2)),0) AS AdminFee,
        IFNULL(OrderHeader.VoucherCode,'') AS VoucherCode,
        IFNULL(CONCAT(FORMAT(OrderHeader.OrderDiscount,2)),0) AS OrderDiscount,
        IFNULL(CONCAT(FORMAT(OrderHeader.PointUsed,2)),0) AS PointUsed,
        IFNULL(CONCAT(FORMAT(OrderHeader.OrderTax,2)),0) AS OrderTax,
        IFNULL(CONCAT(FORMAT(OrderHeader.OrderTotal,2)),0) AS OrderTotal,
        IFNULL(CONCAT(FORMAT(OrderHeader.OrderGrandTotal,2)),0) AS OrderGrandTotal,
        IFNULL(CONCAT(FORMAT(OrderHeader.CancelPrice,2)),0) AS CancelPrice,
        CONVERT(OrderHeader.PaymentFlag,CHAR) AS PaymentFlag,
        CONVERT(OrderHeader.OrderStatus,CHAR) AS OrderStatus,
        CONVERT(OrderHeader.CancelStatus,CHAR) AS CancelStatus,
        CONVERT(OrderHeader.ShippingStatus,CHAR) AS ShippingStatus,
        IFNULL(OrderHeader.AWBNumber,'') AS AWBNumber,
        CONCAT(FORMAT(IFNULL(OrderHeader.PointReward,0),0)) AS PointReward,
        IFNULL(OrderHeader.VendorPickupCode,'') AS VendorPickupCode,
        IFNULL(Pickup.VendorName,'') AS VendorPickupName,
        IFNULL(OrderHeader.PickupRateCategoryCode,'') AS PickupRateCategoryCode,
        IFNULL(PickupRateCategory.PickupRateCategoryName,'') AS PickupRateCategoryName,
        IFNULL(OrderHeader.PickupVehicleCode,'') AS PickupVehicleCode,
        IFNULL(PickupVehicle.PickupVehicleNameEN,'') AS PickupVehicleNameEN,
        IFNULL(PickupVehicle.PickupVehicleNameID,'') AS PickupVehicleNameID,
        IFNULL(OrderHeader.PickupScheduleCode,'') AS PickupScheduleCode,
        IFNULL(PickupSchedule.PickupScheduleName,'') AS PickupScheduleName,
        IFNULL(OrderHeader.PickupAWBNumber,'') AS PickupAWBNumber,
        IFNULL(CancelType.CancelTypeCode,'') AS CancelTypeCode,
        IFNULL(CancelType.CancelTypeNameEN,'') AS CancelTypeNameEN,
        IFNULL(CancelType.CancelTypeNameID,'') AS CancelTypeNameID
        FROM OrderHeader
        INNER JOIN Vendor AS Vendor
        ON OrderHeader.VendorCode = Vendor.VendorCode
        INNER JOIN VendorRate
        ON VendorRate.VendorCode = Vendor.VendorCode
        AND OrderHeader.VendorRateCode = VendorRate.VendorRateCode
        INNER JOIN VendorRateCategory
        ON VendorRate.VendorRateCategoryCode = VendorRateCategory.VendorRateCategoryCode
        AND VendorRateCategory.VendorCode = Vendor.VendorCode
        INNER JOIN GoodsGroup
        ON OrderHeader.GoodsGroupCode = GoodsGroup.GoodsGroupCode
        AND VendorRate.GoodsGroupCode = GoodsGroup.GoodsGroupCode
        INNER JOIN PickupType
        ON OrderHeader.PickupTypeCode = PickupType.PickupTypeCode
        INNER JOIN Country AS Origin
        ON VendorRate.CountryOriginCode = Origin.CountryCode
        INNER JOIN Country AS Destination
        ON VendorRate.CountryDestinationCode = Destination.CountryCode
        LEFT OUTER JOIN DropLocation
        ON OrderHeader.DropLocationCode = DropLocation.DropLocationCode
        LEFT OUTER JOIN Vendor AS Pickup
        ON OrderHeader.VendorPickupCode = Pickup.VendorCode
        LEFT OUTER JOIN PickupRateCategory
        ON OrderHeader.PickupRateCategoryCode = PickupRateCategory.PickupRateCategoryCode
        AND PickupRateCategory.VendorCode = Pickup.VendorCode
        LEFT OUTER JOIN PickupVehicle 
        ON OrderHeader.PickupVehicleCode = PickupVehicle.PickupVehicleCode
        AND PickupVehicle.VendorCode = Pickup.VendorCode
        LEFT OUTER JOIN PickupSchedule
        ON OrderHeader.PickupScheduleCode = PickupSchedule.PickupScheduleCode
        AND PickupSchedule.VendorCode = Pickup.VendorCode
        LEFT OUTER JOIN CancelType
        ON OrderHeader.CancelTypeCode = CancelType.CancelTypeCode
        WHERE OrderHeader.userCode = '" . $UserCode . "'
        ORDER BY OrderHeader.OrderDate DESC; ";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }


    function orderitemlist_get()
    {
        $OrderCode = $this->input->get("OrderCode");

        $data = $this->db->query("SELECT
        OrderDetail.OrderCode,
        OrderDetail.OrderDetailCode,
        GoodsSubGroup.GoodsSubGroupCode,
        GoodsSubGroup.GoodsSubGroupNameEN,
        GoodsSubGroup.GoodsSubGroupNameID,
        CONCAT(FORMAT(IFNULL(OrderDetail.Weight,0),1)) AS Weight,
        CONCAT(FORMAT(IFNULL(OrderDetail.Length,0),0)) AS Length,
        CONCAT(FORMAT(IFNULL(OrderDetail.Width,0),0)) AS Width,
        CONCAT(FORMAT(IFNULL(OrderDetail.Height,0),0)) AS Height,
        CONCAT(FORMAT(IFNULL(OrderDetail.FinalWeight,0),1)) AS FinalWeight
        FROM 
        OrderDetail
        INNER JOIN OrderHeader
        ON OrderDetail.OrderCode = OrderHeader.OrderCode
        INNER JOIN GoodsSubGroup
        ON OrderDetail.GoodsSubGroupCode = GoodsSubGroup.GoodsSubGroupCode
        WHERE OrderDetail.OrderCode = '" . $OrderCode . "';")->result();

        echo json_encode($data);
    }

    function orderdetailinput_get()
    {
        $OrderCode = $this->input->get("OrderCode");
        $GoodsSubGroupCode = $this->input->get("GoodsSubGroupCode");
        $OthersGroupNote = $this->input->get("OthersGroupNote");
        $Weight = $this->input->get("Weight");
        $Length = $this->input->get("Length");
        $Width = $this->input->get("Width");
        $Height = $this->input->get("Height");

        $query = "CALL APPS_sp_order_detail_input(
            '" . $OrderCode . "',
            '" . $GoodsSubGroupCode . "',
            '" . $OthersGroupNote . "',
            " . $Weight . ",
            " . $Length . ",
            " . $Width . ",
            " . $Height . "
            )";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function orderdetailedit_get()
    {
        $OrderDetailCode = $this->input->get("OrderDetailCode");
        $OrderCode = $this->input->get("OrderCode");
        $GoodsSubGroupCode = $this->input->get("GoodsSubGroupCode");
        $OthersGroupNote = $this->input->get("OthersGroupNote");
        $Weight = $this->input->get("Weight");
        $Length = $this->input->get("Length");
        $Width = $this->input->get("Width");
        $Height = $this->input->get("Height");

        $query = "CALL APPS_sp_order_detail_edit(
            '" . $OrderDetailCode . "',
            '" . $OrderCode . "',
            '" . $GoodsSubGroupCode . "',
            '" . $OthersGroupNote . "',
            " . $Weight . ",
            " . $Length . ",
            " . $Width . ",
            " . $Height . "
            )";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function orderdetailinfo_get()
    {
        $OrderDetailCode = $this->input->get("OrderDetailCode");

        $query = "SELECT
        OrderDetail.OrderCode,
        OrderDetail.OrderDetailCode,
        GoodsSubGroup.GoodsSubGroupCode,
        GoodsSubGroup.GoodsSubGroupNameEN,
        GoodsSubGroup.GoodsSubGroupNameID,
        CONCAT(FORMAT(IFNULL(OrderDetail.Weight,0),0)) AS Weight,
        CONCAT(FORMAT(IFNULL(OrderDetail.Length,0),0)) AS Length,
        CONCAT(FORMAT(IFNULL(OrderDetail.Width,0),0)) AS Width,
        CONCAT(FORMAT(IFNULL(OrderDetail.Height,0),0)) AS Height,
        CONCAT(FORMAT(IFNULL(OrderDetail.FinalWeight,0),0)) AS FinalWeight,
        IFNULL(OrderDetail.OthersGroupNote,'') AS OthersGroupNote
        FROM 
        OrderDetail
        INNER JOIN OrderHeader
        ON OrderDetail.OrderCode = OrderHeader.OrderCode
        INNER JOIN GoodsSubGroup
        ON OrderDetail.GoodsSubGroupCode = GoodsSubGroup.GoodsSubGroupCode
        WHERE OrderDetail.OrderDetailCode = '" . $OrderDetailCode . "'; ";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function orderinfo_get()
    {
        $OrderCode = $this->input->get("OrderCode");


        $query = "SELECT 
        OrderHeader.OrderCode,
        IFNULL(OrderHeader.DocumentNo,'') AS DocumentNo,
        Origin.CountryCode AS OriginCode,
        Origin.CountryName AS OriginName,
        Destination.CountryCode AS DestinationCode,
        Destination.CountryName AS DestinationName,
        OrderHeader.VendorCode,
        Vendor.VendorName,
        VendorRate.VendorRateCode,
        VendorRateCategory.VendorRateCategoryName,
        OrderHeader.GoodsGroupCode,
        GoodsGroup.GoodsGroupNameEN,
        GoodsGroup.GoodsGroupNameID,
        PickupType.PickupTypeCode,
        PickupType.PickupTypeNameEN,
        PickupType.PickupTypeNameID,
        IFNULL(CONVERT(DATE_FORMAT(OrderHeader.OrderDate, '%d-%m-%Y'), CHAR),'') AS OrderDate,
        IFNULL(CONVERT(DATE_FORMAT(OrderHeader.OrderDate, '%Y-%m-%d'), CHAR),'') AS OrderDate2,
        IFNULL(CONVERT(DATE_FORMAT(OrderHeader.OrderDate, '%H:%i'), CHAR),'') AS OrderTime,
        IFNULL(OrderHeader.OrderNote,'') AS OrderNote,
        CONCAT(FORMAT(IFNULL(OrderHeader.QtyPieces,0),0)) AS QtyPieces,
        CONCAT(FORMAT(IFNULL(OrderHeader.Weight,0),1)) AS Weight,
        CONCAT(FORMAT(IFNULL(OrderHeader.Length,0),0)) AS Length,
        CONCAT(FORMAT(IFNULL(OrderHeader.Width,0),0)) AS Width,
        CONCAT(FORMAT(IFNULL(OrderHeader.Height,0),0)) AS Height,
        CONCAT(FORMAT(IFNULL(OrderHeader.FinalWeight,0),1)) AS FinalWeight,
        IFNULL(OrderHeader.DropLocationCode,'') AS DropLocationCode,
        IFNULL(DropLocation.DropLocationName,'') AS DropLocationName,
        IFNULL(DropLocation.DropLocationAddress,'') AS DropLocationAddress,
        IFNULL(CONCAT(FORMAT(OrderHeader.OrderPrice,0)),0) AS OrderPrice,
        IFNULL(CONCAT(FORMAT(OrderHeader.AdminFee,0)),0) AS AdminFee,
        IFNULL(OrderHeader.VoucherCode,'') AS VoucherCode,
        IFNULL(CONCAT(FORMAT(OrderHeader.OrderDiscount,0)),0) AS OrderDiscount,
        IFNULL(CONCAT(FORMAT(OrderHeader.PointUsed,0)),0) AS PointUsed,
        IFNULL(CONCAT(FORMAT(OrderHeader.OrderTax,0)),0) AS OrderTax,
        IFNULL(CONCAT(FORMAT(OrderHeader.OrderTotal,0)),0) AS OrderTotal,
        IFNULL(CONCAT(FORMAT(OrderHeader.PickupPrice,0)),0) AS PickupPrice,
        IFNULL(CONCAT(FORMAT(OrderHeader.OrderGrandTotal,0)),0) AS OrderGrandTotal,
        IFNULL(CONCAT(FORMAT(OrderHeader.CancelPrice,0)),0) AS CancelPrice,
        CONVERT(OrderHeader.PaymentFlag,CHAR) AS PaymentFlag,
        CONVERT(OrderHeader.OrderStatus,CHAR) AS OrderStatus,
        CONVERT(OrderHeader.CancelStatus,CHAR) AS CancelStatus,
        CONVERT(OrderHeader.ShippingStatus,CHAR) AS ShippingStatus,
        IFNULL(OrderHeader.AWBNumber,'') AS AWBNumber,
        CONCAT(FORMAT(IFNULL(OrderHeader.PointReward,0),0)) AS PointReward,
        IFNULL(OrderHeader.VendorPickupCode,'') AS VendorPickupCode,
        IFNULL(Pickup.VendorName,'') AS VendorPickupName,
        IFNULL(OrderHeader.PickupRateCategoryCode,'') AS PickupRateCategoryCode,
        IFNULL(PickupRateCategory.PickupRateCategoryName,'') AS PickupRateCategoryName,
        IFNULL(OrderHeader.PickupVehicleCode,'') AS PickupVehicleCode,
        IFNULL(PickupVehicle.PickupVehicleNameEN,'') AS PickupVehicleNameEN,
        IFNULL(PickupVehicle.PickupVehicleNameID,'') AS PickupVehicleNameID,
        IFNULL(OrderHeader.PickupScheduleCode,'') AS PickupScheduleCode,
        IFNULL(PickupSchedule.PickupScheduleName,'') AS PickupScheduleName,
        IFNULL(OrderHeader.PickupAWBNumber,'') AS PickupAWBNumber,
        IFNULL(OrderHeader.AddressNameOrigin ,'') AS AddressNameOrigin,
        IFNULL(OrderHeader.ContactNameOrigin ,'') AS ContactNameOrigin,
        IFNULL(OrderHeader.CountryCodeOrigin ,'') AS CountryCodeOrigin,
        IFNULL(OrderHeader.CityCodeOrigin ,'') AS CityCodeOrigin,
        IFNULL(OrderHeader.CityNameOrigin ,'') AS CityNameOrigin,
        IFNULL(OrderHeader.Address1Origin ,'') AS Address1Origin,
        IFNULL(OrderHeader.Address2Origin ,'') AS Address2Origin,
        IFNULL(OrderHeader.PostalCodeOrigin ,'') AS PostalCodeOrigin,
        IFNULL(OrderHeader.PhoneNumberOrigin ,'') AS PhoneNumberOrigin,
        IFNULL(OrderHeader.EmailOrigin ,'') AS EmailOrigin,
        IFNULL(OrderHeader.DistrictCodeOrigin ,'') AS DistrictCodeOrigin,
        IFNULL(OrderHeader.DistrictNameOrigin,'') AS DistrictNameOrigin,
        IFNULL(OrderHeader.AddressNameDestination ,'') AS AddressNameDestination,
        IFNULL(OrderHeader.ContactNameDestination ,'') AS ContactNameDestination,
        IFNULL(OrderHeader.CountryCodeDestination ,'') AS CountryCodeDestination,
        IFNULL(OrderHeader.CityCodeDestination ,'') AS CityCodeDestination,
        IFNULL(OrderHeader.CityNameDestination ,'') AS CityNameDestination,
        IFNULL(OrderHeader.Address1Destination ,'') AS Address1Destination,
        IFNULL(OrderHeader.Address2Destination ,'') AS Address2Destination,
        IFNULL(OrderHeader.PostalCodeDestination ,'') AS PostalCodeDestination,
        IFNULL(OrderHeader.PhoneNumberDestination ,'') AS PhoneNumberDestination,
        IFNULL(OrderHeader.EmailDestination ,'') AS EmailDestination,
        IFNULL(OrderHeader.DistrictCodeDestination ,'') AS DistrictCodeDestination,
        IFNULL(OrderHeader.DistrictNameDestination,'') AS DistrictNameDestination,
        IFNULL(OrderPayment.OrderPaymentCode,'') AS OrderRequestPaymentCode,
        IFNULL(PaymentMethod.PaymentMethodCode,'') AS PaymentMethodCode,
        IFNULL(PaymentMethod.PaymentMethodName,'') AS PaymentMethodName,
        IFNULL(MasterBank.OYCode,'') AS OYCode,
        IFNULL(MasterBank.NamaBank,'') AS NamaBank,
        IFNULL(OrderPayment.TransactionOYCode,'') AS TransactionOYCode,
        IFNULL(OrderPayment.VANumber,'') AS VANumber,
        IFNULL(OrderPayment.CardNumber,'') AS CardNumber,
        IFNULL(OrderPayment.PaymentStatus,'') AS PaymentStatus,
        IFNULL(OrderPayment.OrderPaymentCode,'') AS OrderPaymentCode,
        FORMAT(IFNULL(Users.PointTotal,0),0) AS PointTotal,
        IFNULL(CancelType.CancelTypeCode,'') AS CancelTypeCode,
        IFNULL(CancelType.CancelTypeNameEN,'') AS CancelTypeNameEN,
        IFNULL(CancelType.CancelTypeNameID,'') AS CancelTypeNameID,
        IFNULL(OrderHeader.CancelReason,'') AS CancelReason
        FROM OrderHeader
        INNER JOIN Users
        ON OrderHeader.UserCode = Users.UserCode
        INNER JOIN Vendor AS Vendor
        ON OrderHeader.VendorCode = Vendor.VendorCode
        INNER JOIN VendorRate
        ON VendorRate.VendorCode = Vendor.VendorCode
        AND OrderHeader.VendorRateCode = VendorRate.VendorRateCode
        INNER JOIN VendorRateCategory
        ON VendorRate.VendorRateCategoryCode = VendorRateCategory.VendorRateCategoryCode
        AND VendorRateCategory.VendorCode = Vendor.VendorCode
        INNER JOIN GoodsGroup
        ON OrderHeader.GoodsGroupCode = GoodsGroup.GoodsGroupCode
        AND VendorRate.GoodsGroupCode = GoodsGroup.GoodsGroupCode
        INNER JOIN PickupType
        ON OrderHeader.PickupTypeCode = PickupType.PickupTypeCode
        INNER JOIN Country AS Origin
        ON VendorRate.CountryOriginCode = Origin.CountryCode
        INNER JOIN Country AS Destination
        ON VendorRate.CountryDestinationCode = Destination.CountryCode
        LEFT OUTER JOIN DropLocation
        ON OrderHeader.DropLocationCode = DropLocation.DropLocationCode
        LEFT OUTER JOIN Vendor AS Pickup
        ON OrderHeader.VendorPickupCode = Pickup.VendorCode
        LEFT OUTER JOIN PickupRateCategory
        ON OrderHeader.PickupRateCategoryCode = PickupRateCategory.PickupRateCategoryCode
        AND PickupRateCategory.VendorCode = Pickup.VendorCode
        LEFT OUTER JOIN PickupVehicle 
        ON OrderHeader.PickupVehicleCode = PickupVehicle.PickupVehicleCode
        AND PickupVehicle.VendorCode = Pickup.VendorCode
        LEFT OUTER JOIN PickupSchedule
        ON OrderHeader.PickupScheduleCode = PickupSchedule.PickupScheduleCode
        AND PickupSchedule.VendorCode = Pickup.VendorCode
        LEFT OUTER JOIN 
        (SELECT * FROM OrderPayment
        WHERE OrderPayment.DeleteFlag = 1 
        AND IFNULL(OrderPayment.VANumber,'') != ''
        AND OrderPayment.OrderCode = '" . $OrderCode . "'
        ORDER BY OrderPayment.CreatedDate
        LIMIT 0,1) AS OrderPayment
        ON OrderPayment.OrderCode = OrderHeader.OrderCode
        LEFT OUTER JOIN PaymentMethod 
        ON OrderPayment.PaymentMethodCode = PaymentMethod.PaymentMethodCode
        LEFT OUTER JOIN MasterBank 
        ON OrderPayment.BankOYCode = MasterBank.OYCode
        LEFT OUTER JOIN CancelType
        ON OrderHeader.CancelTypeCode = CancelType.CancelTypeCode
        WHERE OrderHeader.OrderCode = '" . $OrderCode . "'
        ORDER BY OrderHeader.OrderDate DESC; ";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function orderpaymentcheck2_get()
    {

        $OrderCode = $this->input->get("OrderCode");
        $OrderPaymentCode = $this->input->get("OrderPaymentCode");
        $PaymentStatus = $this->input->get("PaymentStatus");
        $CreatedBy = $this->input->get("CreatedBy");
        $BankOYCode = $this->input->get("BankOYCode");

        $query = "CALL APPS_sp_order_payment_check2(
            '" . $OrderCode . "',
            '" . $OrderPaymentCode . "',
            '" . $PaymentStatus . "',
            '" . $CreatedBy . "',
            '" . $BankOYCode . "')";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function orderpaymentupdate_get()
    {

        $OrderCode = $this->input->get("OrderCode");
        $PaymentMethodCode = $this->input->get("PaymentMethodCode");
        $TransactionOYCode = $this->input->get("TransactionOYCode");
        $BankOYCode = $this->input->get("BankOYCode");
        $VANumber = $this->input->get("VANumber");
        $CardNumber = $this->input->get("CardNumber");
        $PaymentAmount = $this->input->get("PaymentAmount");
        $PaymentStatus = $this->input->get("PaymentStatus");
        $CustomerCode = $this->input->get("CustomerCode");
        $CustomerInvoiceCode = $this->input->get("CustomerInvoiceCode");
        $ProductPacketName = $this->input->get("ProductPacketName");
        $CreatedBy = $this->input->get("CreatedBy");
        $VoucherCode = $this->input->get("VoucherCode");
        $OrderDiscount = $this->input->get("OrderDiscount");
        $PointCode = $this->input->get("PointCode");

        $query = "CALL APPS_sp_order_payment_update(
            '" . $OrderCode . "',
            '" . $PaymentMethodCode . "',
            '" . $TransactionOYCode . "',
            '" . $BankOYCode . "',
            '" . $VANumber . "',
            '" . $CardNumber . "',
            " . $PaymentAmount . ",
            '" . $PaymentStatus . "',
            '" . $CustomerCode . "',
            '" . $CustomerInvoiceCode . "',
            '" . $ProductPacketName . "',
            '" . $CreatedBy . "',
            '" . $VoucherCode . "',
            " . $OrderDiscount . ",
            " . $PointCode . ")";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function orderwebheader_get()
    {

        $OrderCode = $this->input->get("OrderCode");
        $PaymentStatus = $this->input->get("PaymentStatus");
        $CreatedBy = $this->input->get("CreatedBy");

        $query = "CALL APPS_sp_order_payment_web_check(
            '" . $OrderCode . "',
            '" . $PaymentStatus . "',
            '" . $CreatedBy . "')";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    // -------------------- POINT CUSTOMER --------------------

    function totalpoint_get()
    {

        $UserCode = $this->input->get("UserCode");

        $query = "SELECT FORMAT(IFNULL(Users.PointTotal,0),0) AS PointTotal FROM Users
        WHERE UserCode = '" . $UserCode . "'";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    // ------------------- INFORMASI KETENTUAN (HARDCODE) --------------

    function rateinfo_get()
    {

        $query = "SELECT 
        HardCodeID,
        IFNULL(HardCodeValue,0) AS HardCodeValue,
        IFNULL(HardCodeText,'') AS HardCodeText
        FROM HardCode
        WHERE HardCodeID = 'HR012';";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function paymentmethodinfo_get()
    {

        $query = "SELECT 
        HardCodeID,
        IFNULL(HardCodeValue,0) AS HardCodeValue,
        IFNULL(HardCodeText,'') AS HardCodeText
        FROM HardCode
        WHERE HardCodeID = 'HR013';";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }

    function sendConfirmationGmail_get()
    {

        $EmailAddress        = $this->input->get("EmailAddress");
        $OTPCode             = $this->input->get("OTPCode");

        $this->load->library("MyPHPMailer");
        //$datauser = $this->db->query("SELECT appUserEmail from AppUserLogin where DirectUserCode = '" . $id . "' ")->row();
        $mail = new PHPMailer();
        $mail->SMTPDebug  = 1;
        $mail->SMTPAuth   = TRUE;
        $mail->IsHTML(true);
        $mail->IsSMTP();
        $mail->Host     = 'smtp.gmail.com';
        $mail->Username = 'mooleh.developer@gmail.com';
        $mail->Password = 'M00l3hD3VB4yar!';
        $mail->SMTPSecure = 'tls';
        $mail->Port     = 587;
        $mail->setFrom('mooleh.developer@gmail.com');
        $mail->addAddress($EmailAddress);
        $mail->Subject = 'Email confirmation. Mooleh Developer !';
        //$data['url'] = base_url() . "Api/aktivasi/" . $id;
        $body        = "Hallo, Your OTP code to activate your account : " . $OTPCode;
        $mail->Body = $body;
        $mail->send();
    }

    // ------------------ Chat User

    function saveMessageCustomer_post()
    {
        $userid = $this->input->post("userid");
        $content = $this->input->post("content");
        $messageId = $this->input->post("messageId");
        $Remark = $this->input->post("Remark");
        $messageReplyid = $this->input->post("messageReplyid");
        $replyStatus = $this->input->post("replyStatus");
        $this->db->query("INSERT INTO MessageCustomerDetail 
        (messageId,
        messageContent,
        messageFrom,
        createdDate,
        Remark,
        messageReplyid,
        replyStatus) values 
        ('" . $messageId . "',
        '" . $content . "',
        '" . $userid . "',
        NOW(),
        '" . $Remark . "',
        " . $messageReplyid . ",
        " . $replyStatus . ") ");
    }

    function updateMessageCustomer_post()
    {

        $messageId = $this->input->post("messageId");
        $VendorCode = $this->input->post("VendorCode");
        $this->db->query("UPDATE MessageCustomerDetail set ReadStatus = 1 where messageId = '" . $messageId . "' 
        AND messageFrom = '" . $VendorCode . "'
        AND ReadStatus = 0");
    }

    function deleteMessageCustomer_post()
    {

        $detailid = $this->input->post("detailid");

        $this->db->query("DELETE FROM MessageCustomerDetail where detailid = " . $detailid);
    }

    function MessageCustomer_get()
    {
        $TransactionNo = $this->input->get("TransactionNo");

        $query = "SELECT
        MessageCustomerDetail.messageId,
        MessageCustomerDetail.messageContent,
        MessageCustomerDetail.messageFrom,
        CONVERT(MessageCustomerDetail.ReadStatus,CHAR) AS ReadStatus,
        CONVERT(MessageCustomerDetail.detailid, CHAR) AS detailid,
        IFNULL(MessageCustomerDetail.Remark,'') AS Remark,
        DATE_FORMAT(MessageCustomerDetail.CreatedDate,'%m/%d/%Y %H:%i') CreatedDate,
        CONVERT(MessageCustomerDetail.replyStatus, CHAR) AS replyStatus
        FROM 
        MessageCustomerDetail 
        INNER JOIN Users
        ON MessageCustomerDetail.messageId = Users.UserCode
        WHERE Users.UserCode = '" . $TransactionNo . "' 
        ORDER BY MessageCustomerDetail.CreatedDate DESC;";

        $data = $this->db->query($query)->result();

        echo json_encode($data);
    }
}
