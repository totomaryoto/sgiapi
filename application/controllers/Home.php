<?php

use chriskacerguis\RestServer\RestController;
use LDAP\Result;

class Home extends RestController
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
    }

    function homeheaderimage_get()
    {

        $query = $this->db->query("select IklanCode,IklanName,'https://dj93man33366h.cloudfront.net/savedesk/images/blogs/Untitled-3.png' as IklanPhoto,
        IklanNote from Iklan where IklanStatus=1 AND IklanCode='IKL001'
				union
        select IklanCode,IklanName,'https://www.sirclo.com/file/2019/01/Memanfaatkan-Promo-Free-Shipping-untuk-Bisnis-Online-1024x576.jpg' as IklanPhoto,
        IklanNote from Iklan where IklanStatus=1 AND IklanCode='IKL002'				
			union
			select IklanCode,IklanName,'https://www.b2bpay.co/sites/default/files/styles/article_banner/public/field/mainimage/netherlands_export.jpg?itok=JHlRELU0' as IklanPhoto,
        IklanNote from Iklan where IklanStatus=1 AND IklanCode='IKL003'		")->Result();
        $this->response([
            'status' => true,
            'data' => $query,


        ], 200);
    }
}
