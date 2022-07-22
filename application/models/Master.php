<?php
use chriskacerguis\RestServer\RestController;

class Master extends RestController {

    function __construct()
    {
        parent::__construct();
        $this->load->model("Master_model");

    }

    function stores_get(){
        $user_login             = $this->get("user_login");
            $query = $this->db->query("SELECT * FROM stores where store_owner = '".$user_login."' ");
            if($query->num_rows() < 1){
                $this->response([
                    'status' => false,
                ], 200);
            }else{
            $this->response([
                'status' => true,
                'data' =>$query->row(),
            ], 200);
        }
    }

    function partnerstype_get(){
    
            $query = $this->db->query("select * from partner_type");
            if($query->num_rows() < 1){
                $this->response([
                    'status' => false,
                ], 200);
            }else{
            $this->response([
                'status' => true,
                'data' =>$query->result(),
            ], 200);
        }
    }


    function partnertype_get(){
        $store_id             = $this->get("store_id");
        $item_code             = $this->get("item_code");

            $query = $this->db->query("select * from partner_type where id not in (
                select partner_type from item_price where store_id = '".$store_id."' and item_code = '".$item_code."'
            )");
            if($query->num_rows() < 1){
                $this->response([
                    'status' => false,
                ], 200);
            }else{
            $this->response([
                'status' => true,
                'data' =>$query->result(),
            ], 200);
        }
    }

    function stores_put(){
        $store_id               = $this->put("store_id");
        $store_name             = $this->put("store_name");
        $store_address          = $this->put("store_address");
        $store_phone            = $this->put("store_phone");
        $created_by             = $this->put("created_by");
        $data = $this->put();
        $data['store_owner']    = $this->put("created_by");
        if($store_id === null || $store_id === ''){
            $data['store_id'] = $created_by."-".SUBSTR($created_by ,9);
            $query = $this->db->insert('stores', $data);
            if($query){
                $this->response([
                    'status' => true,
                    'message' => "Proses penambahan berhasil",
                ], 200);
            }else{
                $this->response([
                    'status' => false,
                    'message' => "Harap coba kembali",
                ], 500);
            }
        }else{
            $this->db->where("store_id", $store_id);
            $update = $this->db->update('stores', $data);
            if($update){
                $this->response([
                    'status' => true,
                    'message' => "Proses update berhasil",
                ], 200);
            }else{
                $this->response([
                    'status' => false,
                    'message' => "Harap coba kembali",
                ], 500);
           }
        }
    }

    function partners_get(){
        $start = $this->get( 'start' );
        $limit = $this->get( 'limit' );
        $type = $this->get( 'type' );
        $id = $this->get( 'id' );
        $store_id = $this->get('store_id');
        $search = $this->get('search');
        $like_condition = "";
        $condition = "";
        if ( $id !== null )
        {
            $condition = array("partner_code" => $id, "store_id" => $store_id);
            $data = $this->Master_model->get_partners($condition);
         
                $this->response($data 
                , 200);
      
        }else if($type !== null){
            $condition = array("type" => $type, "store_id" => $store_id);
        }else{
            $condition = array("store_id" => $store_id);
        }

        if($search != ""){
            $like_condition = array("partner_name" => $search);
        }


        $data = $this->Master_model->get_all_partners($start,$limit,$condition,$like_condition);
        $this->response( $data
        , 200);
    }

    function searchpartners_get(){
        $store_id = $this->get('store_id');
        $search = $this->get('query');
        $data = $this->db->query("SELECT * FROM partners where partner_name like '%".$search."%' and store_id = '".$store_id."' ")->result();
        if($data){
            $this->response( $data
            , 200);
        }else{
            $this->response("SELECT * FROM partners where partner_name like '%".$search."%' and store_id = '".$store_id."' "
            , 502);
        }
    }






    function partners_post(){
        $partner_name           = $this->input->post("partner_name");
        $partner_address        = $this->input->post("partner_address");
        $partner_phone          = $this->input->post("partner_phone");
        $partner_email          = $this->input->post("partner_email");
        $join_date              = $this->input->post("join_date");
        $type                   = $this->input->post("type");
        $created_by             = $this->input->post("type");
        $store_id               = $this->input->post("store_id");

        $data                   = $this->input->post();
        $data['created_date']     = date("Y-m-d H:i:s");

        $cek = $this->db->query("SELECT * from partners where partner_email = '".$partner_email."'")->num_rows();
        if($cek > 0){
            $this->response([
                'status' => false,
                'message'=>  "Email sudah di gunakan" 
            ], 409 );
        }

        $currentMonth = date('n');
        $currentYear = date('Y');

        $query = $this->db->query("SELECT partner_code from partners  where MONTH(created_date) = '".$currentMonth."' 
        and YEAR(created_date)  = '".$currentYear."' and store_id = '".$store_id."'  order by created_date desc limit 1");

        if($query->num_rows() < 1){
            $data['partner_code'] = "PRT-".date('m').date('y')."-0001";
        }else{
            $data['partner_code'] = ++$query->row()->partner_code;
        }

        $query = $this->Master_model->add_partners($data);
        if($query){
            $this->response([
                'status' => true,
                'message'=>  "Data berhasil di tambahkan" 
            ], 200);
        } 
        
  
        $this->response([
            'status' => false,
            'message'=>  "Woops, harap coba kembali" ,
            'err_message' => $this->db->error()
        ], 502);
    }
    function partners_put(){
        $partner_code           = $this->put("partner_code");
        $partner_name           = $this->put("partner_name");
        $partner_address        = $this->put("partner_address");
        $partner_phone          = $this->put("partner_phone");
        $partner_email          = $this->put("partner_email");
        $join_date              = $this->put("join_date");
        $store_id              = $this->put("store_id");

        $type                   = $this->put("type");
        $data                   = $this->put();
        $data['modified_date']  = date("Y-m-d H:i:s");
      
        $this->db->where('partner_code', $partner_code);
        $this->db->where('store_id', $store_id);

        $update = $this->db->update('partners',$data);
        if ($update) {
            $this->response([
                'status' => true,
                'message'=>  "Data berhasil di ubah" 
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message'=>  "Woops, harap coba kembali" 
            ], 502);
        }
    }
    function partners_delete() {
        $id = $this->delete('partner_code');
        $this->db->where('partner_code', $id);
        $delete = $this->db->delete('partners');
        if ($delete) {
            $this->response([
                'status' => true,
                'message'=> $id
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message'=>  "Woops, harap coba kembali" 
            ], 502);
        }
    }

    function items_post(){
        $item_name              = $this->input->post("item_name");
        $item_price              = $this->input->post("item_price");
        $remark                 = $this->input->post("remark");
        $data                   = $this->input->post();
        $data['created_date']     = date("Y-m-d H:i:s");
        $store_id               = $this->input->post("store_id");
    
        $currentMonth = date('n');
        $currentYear = date('Y');

        $currentMonth2 = date('m');
        $currentYear2 = date('y');

        $config['upload_path']          = './assets/file_upload/item_images/';
        $config['allowed_types']        = 'gif|jpg|png';
    
        $config['encrypt_name']         = TRUE;
        $config['quality']              = '50%';
        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('userfile'))
        {
                $error = array('error' => $this->upload->display_errors());

                $this->response([
                    'status' => false,
                    'message'=>  "Harap periksa kembali gambar anda", 
                    'error'=> $error
                ], 400);
        }
        else
        {
                $upload_result      =  $this->upload->data();
                $data['item_image'] = $upload_result['file_name'];
        }

    
        $query = $this->db->query("SELECT item_code from items 
        where MONTH(created_date) = '".$currentMonth."' 
        and YEAR(created_date)  = '".$currentYear."' and store_id = '".$store_id."'  order by item_code desc limit 1");

      
        if($query->num_rows() < 1){
            $data['item_code'] = "ITM-".date('m').date('y')."-0001";
        }else{
            $data['item_code'] = ++$query->row()->item_code;
        }

        $query = $this->Master_model->add_items($data);

        $data_price['item_code'] = $data['item_code'];
        $data_price['store_id']     = $data['store_id'];
        $data_price['item_price'] = $data['item_price'];
        $data_price['partner_type'] = 0;
        $data_price['created_date']     = date("Y-m-d H:i:s");
        $data_price['created_by']     = $data['created_by'];

        $this->Master_model->add_item_price($data_price);

        if($query){
            $this->response([
                'status' => true,
                'message'=>  "Data berhasil di tambahkan" ,
                'data'=>$data
            ], 200);
        } $this->response([
            'status' => false,
            'message'=>  "Woops, harap coba kembali", 
            'error'=> $query
        ], 502);
    }

    function items_get(){
        $start = $this->get( 'start' );
        $limit = $this->get( 'limit' );
        $id = $this->get( 'id' );
        $store_id = $this->get( 'store_id' );
        $query_search = $this->get('query');

        $condition = "";
        $like_condition = "";
        if ( $id !== null )
        {
            $condition = array("item_code" => $id, "store_id" => $store_id);
            $data = $this->Master_model->get_items($condition);
            $this->response($data 
            , 200);

        }
        $condition = array("store_id" => $store_id);
        if($query_search != ""){
            $query_search = str_replace('_', ' ', $query_search);
            $like_condition =  array("item_name" =>  $query_search);
            $start = "";
            $limit = "";
        }

    

        $data = $this->Master_model->get_all_items($start,$limit,$condition,$like_condition);
        $this->response( $data
        , 200);
    }

    function itemimage_post(){
        $item_code                  = $this->input->post("item_code");
        $store_id                   = $this->input->post("store_id");
        $item_price                 = $this->input->post("item_price");

        $data                   = $this->input->post();
        $data['modified_date']  = date("Y-m-d H:i:s");

        $config['upload_path']          = './assets/file_upload/item_images/';
        $config['allowed_types']        = 'gif|jpg|png';
    
        $config['encrypt_name']         = TRUE;
        $config['quality']              = '50%';
        $this->load->library('upload', $config);

        

        if ( ! $this->upload->do_upload('userfile'))
        {
                $error = array('error' => $this->upload->display_errors());

                $this->response([
                    'status' => false,
                    'message'=>  "Harap periksa kembali gambar anda", 
                    'error'=> $error
                ], 400);
        }

        $upload_result      =  $this->upload->data();
        $data['item_image'] = $upload_result['file_name'];
 
      
        $this->db->where('item_code', $item_code);
        $this->db->where('store_id', $store_id);

        $update = $this->db->update('items',$data);

        
        $data_price['item_code']        = $data['item_code'];
        $data_price['store_id']         = $data['store_id'];
        $data_price['item_price']       = $data['item_price'];
        $data_price['partner_type']     = 5;
        $data_price['created_date']     = date("Y-m-d H:i:s");
        $data_price['modified_by']       = $data['modified_by'];

        if ($update) {
            $this->db->where('item_code', $item_code);
            $this->db->where('store_id', $data['store_id']);
            $this->db->where('item_code', $data['item_code']);
            $this->db->update('item_price',$data_price);

            $this->response([
                'status' => true,
                'message'=>  "Data berhasil di ubah" 
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message'=>  "Woops, harap coba kembali" 
            ], 502);
        }
    }

    function items_put(){
        $item_code                  = $this->put("item_code");
        $store_id                   = $this->put("store_id");
        $item_price                 = $this->put("item_price");

        $data                   = $this->put();
        $data['modified_date']  = date("Y-m-d H:i:s");
 
      
        $this->db->where('item_code', $item_code);
        $this->db->where('store_id', $store_id);

        $update = $this->db->update('items',$data);

        
        $data_price['item_code']        = $data['item_code'];
        $data_price['store_id']         = $data['store_id'];
        $data_price['item_price']       = $data['item_price'];
        $data_price['partner_type']     = 0 ;
        $data_price['created_date']     = date("Y-m-d H:i:s");
        $data_price['modified_by']       = $data['modified_by'];

        if ($update) {
            $this->db->where('item_code', $item_code);
            $this->db->where('store_id', $data['store_id']);
            $this->db->where('item_code', $data['item_code']);
            $this->db->update('item_price',$data_price);

            $this->response([
                'status' => true,
                'message'=>  "Data berhasil di ubah" 
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message'=>  "Woops, harap coba kembali" 
            ], 502);
        }
    }

    function items_delete() {
        $id         = $this->delete('item_code');
        $store_id   = $this->delete('store_id');

        $this->db->where('item_code', $id);
        $this->db->where('store_id', $store_id);

        $delete = $this->db->delete('items');
        if ($delete) {

            $this->db->where('item_code', $id);
            $this->db->where('store_id', $store_id);
            $delete = $this->db->delete('item_stock');


            $this->db->where('item_code', $id);
            $this->db->where('store_id', $store_id);
            $delete2 = $this->db->delete('item_price');

            $this->response([
                'status' => true,
                'message'=> $id
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message'=>  "Woops, harap coba kembali" 
            ], 502);
        }
    }

    function price_post(){

        $data                   = $this->input->post();
        $data['created_date']     = date("Y-m-d H:i:s");
        $currentMonth = date('n');
        $currentYear = date('Y');

        $currentMonth2 = date('m');
        $currentYear2 = date('y');

    
        $cek = $this->db->query("SELECT * from item_price
         where item_code = '".$this->input->post('item_code')."'
          and store_id =  '".$this->input->post('store_id')."'
          and partner_type =  '".$this->input->post('partner_type')."'

           ")->num_rows();

           if($cek > 0){
            $this->response([
                'status' => false,
                'message'=>  "Anda sudah menginput data harga untuk tipe terpilih", 
            ], 502);
           }


        $query = $this->Master_model->add_item_price($data);
        if($query){
            $this->response([
                'status' => true,
                'message'=>  "Data berhasil di tambahkan" 
            ], 200);
        } $this->response([
            'status' => false,
            'message'=>  "Woops, harap coba kembali", 
        ], 502);
    }

    function price_get(){
        $start = $this->get( 'start' );
        $limit = $this->get( 'limit' );
        $id = $this->get( 'id' );
        $store_id = $this->get( 'store_id' );
        $item_code = $this->get( 'item_code' );
        $condition = "";
        if ( $id !== null )
        {
            $condition = array("item_code" => $id);
            $data = $this->Master_model->get_items_price($condition);
            $this->response($data 
            , 200);

        }
        if($start == "" || $limit == "" || $store_id == ""){
            $this->response([
                'status' => false,
                'message' => "Parameter tidak lengkap",
            ], 502);
        }
        $condition = array("a.store_id" => $store_id, "c.item_code" => $item_code);

     

        $data = $this->Master_model->get_all_item_price($start,$limit,$condition);
        $this->response( $data
        , 200);
        
    }

    function price_delete() {
        $id         = $this->delete('id');

        $this->db->where('id', $id);

        $delete = $this->db->delete('item_price');
        if ($delete) {
            $this->response([
                'status' => true,
                'message'=> $id
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message'=>  "Woops, harap coba kembali" 
            ], 502);
        }
    }

    function stock_get(){
        $start = $this->get( 'start' );
        $limit = $this->get( 'limit' );
        $id = $this->get( 'id' );
        $store_id = $this->get( 'store_id' );

        if($start == "" || $limit == "" || $store_id == ""){
            $this->response([
                'status' => false,
                'message' => "Parameter tidak lengkap",
            ], 502);
        }
        $condition = array("a.store_id" => $store_id);
        $query = $this->Master_model->get_item_stock($start,$limit,$condition);
        if($query){
            $this->response([
                'status' => true,
                'message' => "Berhasil",
                'data' => $query
            ], 200);
        }else{
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
                'data' => $query
            ], 502);
        }
    }

    function category_post(){
        $store_id                   = $this->input->post("store_id");
        $data                       = $this->input->post();
        $data['created_date']       = date("Y-m-d H:i:s");

        $currentMonth = date('n');
        $currentYear = date('Y');

        $currentMonth2 = date('m');
        $currentYear2 = date('y');


        $query = $this->db->query("SELECT category_id from category 
        where MONTH(created_date) = '".$currentMonth."' 
        and YEAR(created_date)  = '".$currentYear."' and store_id = '".$store_id."'  order by category_id desc limit 1");

        if($query->num_rows() < 1){
            $data['category_id'] = "CAT-".date('m').date('y')."-0001";
        }else{
            $data['category_id'] = ++$query->row()->category_id;
        }

        $query = $this->db->insert("category", $data);

        if($query){
            $this->response([
                'status' => true,
                'message' => "Berhasil",
            ], 200);
        }else{
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
            ], 502);
        }
    }

    function category_get(){
        $start = $this->get( 'start' );
        $limit = $this->get( 'limit' );
        $store_id = $this->get( 'store_id' );

        


        if($start == "" || $limit == "" || $store_id == ""){
            $this->response([
                'status' => false,
                'message' => "Parameter tidak lengkap",
            ], 502);
        }

        $query = $this->db->query("SELECT category_id,id,category_name FROM category 
        where store_id = '".$store_id."' order  by category_name LIMIT ".$limit." OFFSET ".$start." ")->result();
        
        if($query){
            $this->response([
                'status' => true,
                'message' => "Berhasil",
                'data' => $query
            ], 200);
        }else{
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
                'data' => $query
            ], 502);
        }

    }
    function category_delete() {
        $id                  = $this->delete('id');
        $category_id         = $this->delete('category_id');
        $store_id            = $this->delete('store_id');

        $this->db->where('id', $id);
        $this->db->where('category_id', $category_id);
        $this->db->where('store_id', $store_id);

        $delete = $this->db->delete('category');
        if ($delete) {
            $this->response([
                'status' => true,
                'message'=> $id
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message'=>  "Woops, harap coba kembali" 
            ], 502);
        }
    }

    function singlecategory_get(){
        $store_id = $this->get("store_id");
        $query = $this->db->query("SELECT * FROM category where store_id = '".$store_id."' ")->result();
        if($query){
            $this->response([
                'status' => true,
                'data'=>$query
            ], 200);
        }else{
            $this->response([
                'status' => false,
                'message'=>  "Woops, harap coba kembali" 
            ], 502);
        }
    }

}