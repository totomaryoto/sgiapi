<?php

use chriskacerguis\RestServer\RestController;

class Master extends RestController
{

    function __construct()
    {
        parent::__construct();
        header("Access-Control-Allow-Origin: *");
        $this->load->model("Master_model");
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

    function satuan_get()
    {
        $query = $this->db->query("SELECT * FROM uom order by uom_name asc")->result();
        $this->response([
            'status' => false,
            'data' => $query
        ], 200);
    }


    function stores_get()
    {
        $user_login             = $this->get("user_login");
        $query = $this->db->query("SELECT * FROM stores where store_owner = '" . $user_login . "' ");
        if ($query->num_rows() < 1) {
            $this->response([
                'status' => false,
            ], 200);
        } else {
            $this->response([
                'status' => true,
                'data' => $query->row(),
            ], 200);
        }
    }

    function storescount_get()
    {
        $user_login             = $this->get("user_login");
        $query = $this->db->query("SELECT * FROM stores where store_owner = '" . $user_login . "' ");
        if ($query->num_rows() < 1) {
            $this->response([
                'status' => false,
            ], 200);
        } else {
            $this->response([
                'status' => true,
                'row' => $query->num_rows(),
            ], 200);
        }
    }

    function company_post()
    {
        $id = uniqid();
        $company_name = $this->input->post("company_name");
        $created_by = $this->input->post("created_by");
        $company_type = "PERSON";
        // Save company
        $query =  $this->db->query("INSERT INTO company (company_id, company_name, type_company, created_date, created_by) 
        values ('" . $id . "', '" . $company_name . "','" . $company_type . "',NOW(), '" . $created_by . "' )  ");
        if ($query) {
            $this->response([
                'company_id' => $id,
                'status' => true,
            ], 200);
        } else {
            $this->response([
                'status' => false,
            ], 500);
        }
    }

    function partnerstype_get()
    {
        $query = $this->db->query("select * from partner_type where flag = 1 order by partner_type_name asc");
        if ($query->num_rows() < 1) {
            $this->response([
                'status' => false,
            ], 200);
        } else {
            $this->response([
                'status' => true,
                'data' => $query->result(),
            ], 200);
        }
    }

    function partnertype_get()
    {
        $store_id             = $this->get("store_id");
        $item_code             = $this->get("item_code");

        $query = $this->db->query("select * from partner_type where id not in (
                select partner_type from item_price where store_id = '" . $store_id . "' and item_code = '" . $item_code . "'
            )");
        if ($query->num_rows() < 1) {
            $this->response([
                'status' => false,
            ], 200);
        } else {
            $this->response([
                'status' => true,
                'data' => $query->result(),
            ], 200);
        }
    }

    function stores_post()
    {
        $store_id                       = $this->input->post("store_id");
        $store_name                     = $this->input->post("store_name");
        $store_address                  = $this->input->post("store_address");
        $store_phone                    = $this->input->post("store_phone");
        $store_owner                    = $this->input->post("created_by");
        $created_by                     = $this->input->post("created_by");
        $company_id                     = $$this->input->post("company_id");

        $data['store_owner']    = $this->input->post("created_by");
        if ($store_id === null || $store_id === '') {
            $data['created_date']     = date("Y-m-d H:i:s");
            $currentMonth = date('n');
            $currentYear = date('Y');
            $cek_store = $this->db->query("SELECT * FROM stores where MONTH(created_date) = '" . $currentMonth . "' 
            and YEAR(created_date)  = '" . $currentYear . "' order by store_id desc");

            if ($cek_store->num_rows() < 1) {
                $data['store_id'] = "STR-" . date('y') . "-00001";
            } else {
                $data['store_id'] = ++$cek_store->row()->store_id;
            }
            $query = $this->db->query("call t_StoreIns ('" . $store_name . "','" . $store_address . "','" . $store_phone . "','" . $store_owner . "','0','" . $created_by . "') ");

            $this->db->query("UPDATE stores set company_id = '" . $company_id . "' where store_id = '" . $store_id . "' and  store_owner = '" . $created_by . "' ");

            if ($query) {
                $this->response([
                    'status' => true,
                    'message' => "Proses penambahan berhasil",
                    'store_id' => $query->row()->store_id
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => "Harap coba kembali",
                ], 500);
            }
        }
    }

    function stores_put()
    {
        $store_id                       = $this->put("store_id");
        $store_name                     = $this->put("store_name");
        $store_address                  = $this->put("store_address");
        $store_phone                    = $this->put("store_phone");
        $store_owner                    = $this->put("created_by");
        $created_by                     = $this->put("created_by");

        //Check company

        $data = $this->put();
        $data['store_owner']    = $this->put("created_by");
        if ($store_id === null || $store_id === '') {
            $data['created_date']     = date("Y-m-d H:i:s");
            $currentMonth = date('n');
            $currentYear = date('Y');
            $cek_store = $this->db->query("SELECT * FROM stores where MONTH(created_date) = '" . $currentMonth . "' 
            and YEAR(created_date)  = '" . $currentYear . "' order by store_id desc");

            if ($cek_store->num_rows() < 1) {
                $data['store_id'] = "STR-" . date('y') . "-00001";
            } else {
                $data['store_id'] = ++$cek_store->row()->store_id;
            }
            $query = $this->db->query("call t_StoreIns ('" . $store_name . "','" . $store_address . "','" . $store_phone . "','" . $store_owner . "','0','" . $created_by . "') ");

            if ($query) {
                $this->response([
                    'status' => true,
                    'message' => "Proses penambahan berhasil",
                    'store_id' => $query->row()->store_id
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => "Harap coba kembali",
                ], 500);
            }
        } else {
            $data['modified_date']     = date("Y-m-d H:i:s");
            $this->db->where("store_id", $store_id);
            $update = $this->db->update('stores', $data);
            if ($update) {
                $this->response([
                    'status' => true,
                    'message' => "Proses update berhasil",
                    'store_id' => $store_id

                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' => "Harap coba kembali",
                ], 500);
            }
        }
    }

    function partners_get()
    {
        $start = $this->get('start');
        $limit = $this->get('limit');
        $type = $this->get('type');
        $id = $this->get('id');
        $store_id = $this->get('store_id');
        $search = $this->get('search');
        $like_condition = "";
        $condition = "";
        if ($id !== null) {
            $condition = array("partner_code" => $id, "store_id" => $store_id);
            $data = $this->Master_model->get_partners($condition);

            $this->response(
                $data,
                200
            );
        } else if ($type !== null) {
            $condition = array("type" => $type, "store_id" => $store_id);
        } else {
            $condition = array("store_id" => $store_id);
        }

        if ($search != "") {
            $search = str_replace("_", " ", $search);
            $like_condition = array("partner_name" => $search);
        }


        $data = $this->Master_model->get_all_partners($start, $limit, $condition, $like_condition);
        $this->response(
            $data,
            200
        );
    }

    function searchpartners_get()
    {
        $store_id = $this->get('store_id');
        $search = $this->get('query');
        $search = str_replace("_", " ", $search);


        $data = $this->db->query("SELECT * FROM partners where partner_name like '%" . $search . "%' and store_id = '" . $store_id . "' ")->result();
        if ($data) {
            $this->response(
                $data,
                200
            );
        } else {
            $this->response(
                "SELECT * FROM partners where partner_name like '%" . $search . "%' and store_id = '" . $store_id . "' ",
                502
            );
        }
    }
    function partners_post()
    {
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

        $cek = $this->db->query("SELECT * from partners where partner_email = '" . $partner_email . "'")->num_rows();
        if ($cek > 0) {
            $this->response([
                'status' => false,
                'message' =>  "Email sudah di gunakan"
            ], 409);
        }

        $currentMonth = date('n');
        $currentYear = date('Y');

        $query = $this->db->query("SELECT partner_code from partners  where MONTH(created_date) = '" . $currentMonth . "' 
        and YEAR(created_date)  = '" . $currentYear . "' and store_id = '" . $store_id . "'  order by created_date desc limit 1");

        if ($query->num_rows() < 1) {
            $data['partner_code'] = "PRT-" . date('m') . date('y') . "-0001";
        } else {
            $data['partner_code'] = ++$query->row()->partner_code;
        }

        $query = $this->Master_model->add_partners($data);
        if ($query) {
            $this->response([
                'status' => true,
                'message' =>  "Data berhasil di tambahkan"
            ], 200);
        }


        $this->response([
            'status' => false,
            'message' =>  "Woops, harap coba kembali",
            'err_message' => $this->db->error()
        ], 502);
    }
    function partners_put()
    {
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

        $update = $this->db->update('partners', $data);
        if ($update) {
            $this->response([
                'status' => true,
                'message' =>  "Data berhasil di ubah"
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' =>  "Woops, harap coba kembali"
            ], 502);
        }
    }
    function partners_delete()
    {
        $id = $this->delete('partner_code');
        $this->db->where('partner_code', $id);
        $delete = $this->db->delete('partners');
        if ($delete) {
            $this->response([
                'status' => true,
                'message' => $id
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' =>  "Woops, harap coba kembali"
            ], 502);
        }
    }

    function items_post()
    {
        $data                               = $this->input->post();
        $item_name                          = $this->input->post("item_name");
        $item_short_name                    = $this->input->post("item_short_name");
        $barcode                            = $this->input->post("item_barcode");
        $created_by                         = $this->input->post("created_by");
        $item_price                         = $this->input->post("item_price");
        $store_id                           = $this->input->post("store_id");
        $item_type                          = $this->input->post("item_type");
        $item_category_id                   = $this->input->post("item_category_id");
        $item_cost                          = $this->input->post("item_cost");
        $item_stock                         = $this->input->post("item_stock");

        $data['created_date']               = date("Y-m-d H:i:s");
        $store_id                           = $this->input->post("store_id");



        $config['upload_path']          = './assets/file_upload/item_images/';
        $config['allowed_types']        = 'gif|jpg|png';

        $config['encrypt_name']         = TRUE;
        $config['quality']              = '50%';
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('userfile')) {
            $item_image = "";
        } else {
            $upload_result      =  $this->upload->data();
            $item_image = $upload_result['file_name'];
        }
        $this->_create_thumbs($item_image);

        $query = $this->Master_model->add_items(
            $item_name,
            $item_short_name,
            $barcode,
            $created_by,
            $store_id,
            $item_price,
            $item_image,
            $item_type,
            $item_category_id,
            $item_stock,
            $item_cost
        );
        if ($query) {
            $response = $query->row();
            if ($response->status != "0") {
                $this->response([
                    'status' => true,
                    'message' =>  "Data berhasil di tambahkan",
                    'data' => $query->row()
                ], 200);
            } else {
                $this->response([
                    'status' => true,
                    'message' => $response->msg,

                ], 500);
            }
        } else {
            $this->response([
                'status' => false,
                'message' =>  "Woops, harap coba kembali",
                'error' => $query->row()
            ], 500);
        }
    }

    function itemsv2_post()
    {
        $data                               = $this->input->post();
        $item_name                          = $this->input->post("item_name");
        $item_short_name                    = $this->input->post("item_short_name");
        $barcode                            = $this->input->post("item_barcode");
        $created_by                         = $this->input->post("created_by");
        $item_price                         = $this->input->post("item_price");
        $store_id                           = $this->input->post("store_id");
        $item_type                          = $this->input->post("item_type");
        $item_category_id                   = $this->input->post("item_category_id");
        $item_cost                          = $this->input->post("item_cost");
        $item_stock                         = $this->input->post("item_stock");
        $item_stock                         = $this->input->post("item_stock");
        $satuan                             = $this->input->post("satuan");
        $data['created_date']               = date("Y-m-d H:i:s");
        $store_id                           = $this->input->post("store_id");



        $config['upload_path']          = './assets/file_upload/item_images/';
        $config['allowed_types']        = 'gif|jpg|png';

        $config['encrypt_name']         = TRUE;
        $config['quality']              = '50%';
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('userfile')) {
            $item_image = "";
        } else {
            $upload_result      =  $this->upload->data();
            $item_image = $upload_result['file_name'];
        }
        $this->_create_thumbs($item_image);

        $query = $this->Master_model->add_itemsv2(
            $item_name,
            $item_short_name,
            $barcode,
            $created_by,
            $store_id,
            $item_price,
            $item_image,
            $item_type,
            $item_category_id,
            $item_stock,
            $item_cost,
            $satuan
        );
        if ($query) {
            $response = $query->row();
            if ($response->status != "0") {
                $this->response([
                    'status' => true,
                    'message' =>  "Data berhasil di tambahkan",
                    'data' => $query->row()
                ], 200);
            } else {
                $this->response([
                    'status' => true,
                    'message' => $response->msg,

                ], 500);
            }
        } else {
            $this->response([
                'status' => false,
                'message' =>  "Woops, harap coba kembali",
                'error' => $query->row()
            ], 500);
        }
    }

    function items_get()
    {
        $start = $this->get('start');
        $limit = $this->get('limit');
        $id = $this->get('id');
        $store_id = $this->get('store_id');
        $category_id = $this->get('category_id');
        $search = $this->get('search');

        $condition = "";
        $like_condition = "";
        if ($id !== null) {
            $condition = array("item_code" => $id, "store_id" => $store_id);
            $data = $this->Master_model->get_items($condition);
            $this->response(
                $data,
                200
            );
        }
        $condition = array("a.store_id" => $store_id);
        if ($category_id != "") {
            $like_condition =  array("a.item_category_id" =>  $category_id);
            $start = "";
            $limit = "";
        }
        if ($search != null) {
            $data = $this->Master_model->get_all_items_search($start, $limit, $search, $store_id);
        } else {
            $data = $this->Master_model->get_all_items($start, $limit, $condition, $like_condition);
        }


        $this->response(
            $data,
            200
        );
    }

    function itemsv2_get()
    {
        $start = $this->get('start');
        $limit = $this->get('limit');
        $id = $this->get('id');
        $store_id = $this->get('store_id');
        $category_id = $this->get('category_id');
        $search = $this->get('search');

        $condition = "";
        $like_condition = "";
        if ($id !== null) {
            $condition = array("item_code" => $id, "store_id" => $store_id);
            $data = $this->Master_model->get_items($condition);
            $this->response(
                $data,
                200
            );
        }
        $condition = array("a.store_id" => $store_id);
        if ($category_id != "") {
            $like_condition =  array("a.item_category_id" =>  $category_id);
            $start = "";
            $limit = "";
        }
        if ($search != null) {
            $data = $this->Master_model->get_all_items_search($start, $limit, $search, $store_id);
        } else {
            $data = $this->Master_model->get_all_items($start, $limit, $condition, $like_condition);
        }


        $this->response(
            $data,
            200
        );
    }

    function itemimage_post()
    {

        $data                               = $this->input->post();
        $item_code                          = $this->input->post("item_code");

        $item_name                          = $this->input->post("item_name");
        $item_short_name                    = $this->input->post("item_short_name");
        $barcode                            = $this->input->post("item_barcode");
        $created_by                         = $this->input->post("created_by");
        $item_price                         = $this->input->post("item_price");
        $store_id                           = $this->input->post("store_id");
        $item_type                          = $this->input->post("item_type");
        $item_category_id                   = $this->input->post("item_category_id");
        $item_cost                          = $this->input->post("item_cost");
        $item_stock                         = $this->input->post("item_stock");
        $satuan                             = $this->input->post("satuan");

        $data['created_date']               = date("Y-m-d H:i:s");
        $store_id                           = $this->input->post("store_id");


        $data                   = $this->input->post();
        $data['modified_date']  = date("Y-m-d H:i:s");

        $config['upload_path']          = './assets/file_upload/item_images/';
        $config['allowed_types']        = 'gif|jpg|png';

        $config['encrypt_name']         = TRUE;
        $config['quality']              = '50%';
        $this->load->library('upload', $config);



        if (!$this->upload->do_upload('userfile')) {
            $error = array('error' => $this->upload->display_errors());

            $this->response([
                'status' => false,
                'message' =>  "Harap periksa kembali gambar anda",
                'error' => $error
            ], 400);
        }
        $upload_result      =  $this->upload->data();
        $item_image = $upload_result['file_name'];
        $this->_create_thumbs($item_image);
        $query = $this->Master_model->edit_items(
            $item_code,
            $item_name,
            $item_short_name,
            $barcode,
            $created_by,
            $store_id,
            $item_price,
            $item_image,
            $item_type,
            $item_category_id,
            $item_stock,
            $item_cost
        );
        if ($query) {
            $response = $query->row();
            if ($response->status != "0") {
                $this->response([
                    'status' => true,
                    'message' =>  "Data berhasil di tambahkan",
                    'data' => $query->row()
                ], 200);
            } else {
                $this->response([
                    'status' => true,
                    'message' => $response->msg,

                ], 500);
            }
        }
        $this->response([
            'status' => false,
            'message' =>  "Woops, harap coba kembali",
            'error' => $query
        ], 502);
    }
    function itemimagev2_post()
    {

        $data                               = $this->input->post();
        $item_code                          = $this->input->post("item_code");

        $item_name                          = $this->input->post("item_name");
        $item_short_name                    = $this->input->post("item_short_name");
        $barcode                            = $this->input->post("item_barcode");
        $created_by                         = $this->input->post("created_by");
        $item_price                         = $this->input->post("item_price");
        $store_id                           = $this->input->post("store_id");
        $item_type                          = $this->input->post("item_type");
        $item_category_id                   = $this->input->post("item_category_id");
        $item_cost                          = $this->input->post("item_cost");
        $item_stock                         = $this->input->post("item_stock");
        $satuan                             = $this->input->post("satuan");

        $data['created_date']               = date("Y-m-d H:i:s");
        $store_id                           = $this->input->post("store_id");


        $data                   = $this->input->post();
        $data['modified_date']  = date("Y-m-d H:i:s");

        $config['upload_path']          = './assets/file_upload/item_images/';
        $config['allowed_types']        = 'gif|jpg|png';

        $config['encrypt_name']         = TRUE;
        $config['quality']              = '50%';
        $this->load->library('upload', $config);



        if (!$this->upload->do_upload('userfile')) {
            $error = array('error' => $this->upload->display_errors());

            $this->response([
                'status' => false,
                'message' =>  "Harap periksa kembali gambar anda",
                'error' => $error
            ], 400);
        }
        $upload_result      =  $this->upload->data();
        $item_image = $upload_result['file_name'];
        $this->_create_thumbs($item_image);
        $query = $this->Master_model->edit_itemsv2(
            $item_code,
            $item_name,
            $item_short_name,
            $barcode,
            $created_by,
            $store_id,
            $item_price,
            $item_image,
            $item_type,
            $item_category_id,
            $item_stock,
            $item_cost,
            $satuan
        );
        if ($query) {
            $response = $query->row();
            if ($response->status != "0") {
                $this->response([
                    'status' => true,
                    'message' =>  "Data berhasil di tambahkan",
                    'data' => $query->row()
                ], 200);
            } else {
                $this->response([
                    'status' => true,
                    'message' => $response->msg,

                ], 500);
            }
        }
        $this->response([
            'status' => false,
            'message' =>  "Woops, harap coba kembali",
            'error' => $query
        ], 502);
    }
    function items_put()
    {
        $item_code                          = $this->put("item_code");
        $item_name                          = $this->put("item_name");
        $item_short_name                    = $this->put("item_short_name");
        $barcode                            = $this->put("item_barcode");
        $created_by                         = $this->put("created_by");
        $item_price                         = $this->put("item_price");
        $store_id                           = $this->put("store_id");
        $item_type                          = $this->put("item_type");
        $item_category_id                   = $this->put("item_category_id");
        $item_cost                          = $this->put("item_cost");
        $item_stock                         = $this->put("item_stock");

        $data = $this->db->query("SELECT * from items where item_code = '" . $item_code . "' and store_id = '" . $store_id . "' ")->row();

        $query = $this->Master_model->edit_items(
            $item_code,
            $item_name,
            $item_short_name,
            $barcode,
            $created_by,
            $store_id,
            $item_price,
            $data->item_image,
            $item_type,
            $item_category_id,
            $item_stock,
            $item_cost
        );
        if ($query) {
            $response = $query->row();
            if ($response->status != "0") {
                $this->response([
                    'status' => true,
                    'message' =>  "Data berhasil di tambahkan",
                    'data' => $query->row()
                ], 200);
            } else {
                $this->response([
                    'status' => true,
                    'message' => $response->msg,

                ], 500);
            }
        }
        $this->response([
            'status' => false,
            'message' =>  "Woops, harap coba kembali",
            'error' => $query
        ], 502);
    }

    function itemsv2_put()
    {
        $item_code                          = $this->put("item_code");
        $item_name                          = $this->put("item_name");
        $item_short_name                    = $this->put("item_short_name");
        $barcode                            = $this->put("item_barcode");
        $created_by                         = $this->put("created_by");
        $item_price                         = $this->put("item_price");
        $store_id                           = $this->put("store_id");
        $item_type                          = $this->put("item_type");
        $item_category_id                   = $this->put("item_category_id");
        $item_cost                          = $this->put("item_cost");
        $item_stock                         = $this->put("item_stock");
        $satuan                             = $this->put("satuan");

        $data = $this->db->query("SELECT * from items where item_code = '" . $item_code . "' and store_id = '" . $store_id . "' ")->row();

        $query = $this->Master_model->edit_itemsv2(
            $item_code,
            $item_name,
            $item_short_name,
            $barcode,
            $created_by,
            $store_id,
            $item_price,
            $data->item_image,
            $item_type,
            $item_category_id,
            $item_stock,
            $item_cost,
            $satuan
        );
        if ($query) {
            $response = $query->row();
            if ($response->status != "0") {
                $this->response([
                    'status' => true,
                    'message' =>  "Data berhasil di tambahkan",
                    'data' => $query->row()
                ], 200);
            } else {
                $this->response([
                    'status' => true,
                    'message' => $response->msg,

                ], 500);
            }
        }
        $this->response([
            'status' => false,
            'message' =>  "Woops, harap coba kembali",
            'error' => $query
        ], 502);
    }

    function _create_thumbs($file_name)
    {
        // Image resizing config
        $config = array(
            // Large Image
            array(
                'image_library' => 'GD2',
                'source_image'  => './assets/file_upload/item_images/' . $file_name,
                'maintain_ratio' => FALSE,
                'width'         => 500,
                'height'        => 500,
                'new_image'     => './assets/file_upload/item_images/large/' . $file_name
            ),
            // Medium Image
            array(
                'image_library' => 'GD2',
                'source_image'  => './assets/file_upload/item_images/' . $file_name,
                'maintain_ratio' => FALSE,
                'width'         => 300,
                'height'        => 300,
                'new_image'     => './assets/file_upload/item_images/medium/' . $file_name
            ),
            // Small Image
            array(
                'image_library' => 'GD2',
                'source_image'  => './assets/file_upload/item_images/' . $file_name,
                'maintain_ratio' => FALSE,
                'width'         => 150,
                'height'        => 150,
                'new_image'     => './assets/file_upload/item_images/small/' . $file_name
            )
        );

        $this->load->library('image_lib', $config[0]);
        foreach ($config as $item) {
            $this->image_lib->initialize($item);
            if (!$this->image_lib->resize()) {
                return false;
            }
            $this->image_lib->clear();
        }
    }


    function items_delete()
    {
        $id         = $this->delete('item_code');
        $store_id   = $this->delete('store_id');

        $query = $this->Master_model->delete_item($store_id, $id);
        if ($query) {
            $response = $query->row();
            if ($response->status == "0") {
                $this->response([
                    'status' => false,
                    'message' =>  $response->msg
                ], 200);
            }
            $this->response([
                'status' => true,
                'message' => $id
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' =>  "Woops, harap coba kembali"
            ], 502);
        }
    }
    function price_post()
    {

        $data                   = $this->input->post();
        $data['created_date']     = date("Y-m-d H:i:s");
        $currentMonth = date('n');
        $currentYear = date('Y');

        $currentMonth2 = date('m');
        $currentYear2 = date('y');


        $cek = $this->db->query("SELECT * from item_price
         where item_code = '" . $this->input->post('item_code') . "'
          and store_id =  '" . $this->input->post('store_id') . "'
          and partner_type =  '" . $this->input->post('partner_type') . "'

           ")->num_rows();

        if ($cek > 0) {
            $this->response([
                'status' => false,
                'message' =>  "Anda sudah menginput data harga untuk tipe terpilih",
            ], 502);
        }


        $query = $this->Master_model->add_item_price($data);
        if ($query) {
            $this->response([
                'status' => true,
                'message' =>  "Data berhasil di tambahkan"
            ], 200);
        }
        $this->response([
            'status' => false,
            'message' =>  "Woops, harap coba kembali",
        ], 502);
    }
    function price_get()
    {
        $start = $this->get('start');
        $limit = $this->get('limit');
        $id = $this->get('id');
        $store_id = $this->get('store_id');
        $item_code = $this->get('item_code');
        $condition = "";
        if ($id !== null) {
            $condition = array("item_code" => $id);
            $data = $this->Master_model->get_items_price($condition);
            $this->response(
                $data,
                200
            );
        }
        if ($start == "" || $limit == "" || $store_id == "") {
            $this->response([
                'status' => false,
                'message' => "Parameter tidak lengkap",
            ], 502);
        }
        $condition = array("a.store_id" => $store_id, "c.item_code" => $item_code);



        $data = $this->Master_model->get_all_item_price($start, $limit, $condition);
        $this->response(
            $data,
            200
        );
    }
    function price_delete()
    {
        $id         = $this->delete('id');

        $this->db->where('id', $id);

        $delete = $this->db->delete('item_price');
        if ($delete) {
            $this->response([
                'status' => true,
                'message' => $id
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' =>  "Woops, harap coba kembali"
            ], 502);
        }
    }
    function stock_get()
    {
        $start = $this->get('start');
        $limit = $this->get('limit');
        $store_id = $this->get('store_id');

        if ($start == "" || $limit == "" || $store_id == "") {
            $this->response([
                'status' => false,
                'message' => "Parameter tidak lengkap",
            ], 502);
        }
        $condition = array("a.store_id" => $store_id);
        $query = $this->Master_model->get_item_stock($start, $limit, $condition);
        if ($query) {
            $this->response([
                'status' => true,
                'message' => "Berhasil",
                'data' => $query
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
                'data' => $query
            ], 502);
        }
    }


    function category_put()
    {
        // 'store_id': widget.store_id,
        // 'created_by': prefs.getString('user_id'),
        // 'id': widget.item_category.id,
        // 'category_id': widget.item_category.categoryId,
        // 'category_name': _categoryName.text.toString(),

        $store_id = $this->put("store_id");
        $created_by = $this->put("created_by");
        $id = $this->put("id");
        $category_id = $this->put("category_id");
        $category_name = $this->put("category_name");

        $this->db->query("UPDATE category set category_name = '" . $category_name . "' 
        where id = '" . $id . "' and store_id = '" . $store_id . "' and category_id = '" . $category_id . "' ");
        $this->response([
            'status' => true,
            'message' => "Berhasil",
        ], 200);
    }

    function category_post()
    {
        $store_id                   = $this->input->post("store_id");
        $data                       = $this->input->post();
        $data['created_date']       = date("Y-m-d H:i:s");

        $currentMonth = date('n');
        $currentYear = date('Y');


        $query = $this->db->query("SELECT category_id from category 
        where MONTH(created_date) = '" . $currentMonth . "' 
        and YEAR(created_date)  = '" . $currentYear . "' and store_id = '" . $store_id . "'  order by category_id desc limit 1");

        if ($query->num_rows() < 1) {
            $data['category_id'] = "CAT-" . date('m') . date('y') . "-0001";
        } else {
            $data['category_id'] = ++$query->row()->category_id;
        }

        $query = $this->db->insert("category", $data);

        if ($query) {
            $this->response([
                'status' => true,
                'message' => "Berhasil",
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
            ], 502);
        }
    }
    function category_get()
    {
        $start = $this->get('start');
        $limit = $this->get('limit');
        $store_id = $this->get('store_id');




        if ($start == "" || $limit == "" || $store_id == "") {
            $this->response([
                'status' => false,
                'message' => "Parameter tidak lengkap",
            ], 502);
        }

        $query = $this->db->query("SELECT category_id,id,category_name FROM category 
        where store_id = '" . $store_id . "' order  by category_name LIMIT " . $limit . " OFFSET " . $start . " ");

        if ($query) {
            $this->response([
                'status' => true,
                'message' => "Berhasil",
                'data' => $query->result()
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
                'data' => $query
            ], 502);
        }
    }
    function category_delete()
    {
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
                'message' => $id
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' =>  "Woops, harap coba kembali"
            ], 502);
        }
    }
    function singlecategory_get()
    {
        $store_id = $this->get("store_id");
        $query = $this->db->query("SELECT * FROM category where store_id = '" . $store_id . "' ")->result();
        if ($query) {
            $this->response([
                'status' => true,
                'data' => $query
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' =>  "Woops, harap coba kembali"
            ], 502);
        }
    }
    function notification_get()
    {
        $user_id = $this->get("user_id");
        $start = $this->get('start');
        $limit = $this->get('limit');

        $notifikasi = $this->db->query("SELECT * FROM notification 
        where notification_to = '" . $user_id . "' order by notification_date desc limit " . $limit . " offset " . $start . " ");
        $this->response([
            'status' => true,
            'data' => $notifikasi->result()
        ], 200);
    }
    function unreadnotification_get()
    {
        $user_id = $this->get("user_id");

        $notifikasi = $this->db->query("SELECT * FROM notification 
        where notification_to = '" . $user_id . "' and read_status = 0 ");
        $this->response([
            'status' => true,
            'count' => $notifikasi->num_rows()
        ], 200);
    }

    function readnotification_post()
    {
        $id = $this->input->post("id");
        $current_date =  date("Y-m-d H:i:s");
        $this->db->query("UPDATE `notification` set read_status = 1 , read_date ='" . $current_date . "' where id =" . $id . "");
    }

    function ticketcategory_get()
    {
        $data = $this->db->query("SELECT * FROM `ticket_category` where flag = 1 order by ticket_category_order asc ")->result();
        $this->response([
            'status' => true,
            'data' => $data
        ], 200);
    }

    function ticket_get()
    {
        $user_id            = $this->get("user_id");
        $query = $this->db->query("SELECT a.*,ticket_category_name,message 
        FROM ticket a 
        inner join ticket_category b on a.ticket_category = b.ticket_category_id
        left join (
            
            select d.*
                from (
                    select d.*, row_number() over(partition by ticket_id order by id desc) rn
                    from ticket_detail d
                ) d
                where rn = 1
            
            ) dt on dt.ticket_id = a.id
        
        
         where user_id = '" . $user_id . "'");
        $this->response([
            'status' => true,
            'data' => $query->result()
        ], 200);
    }

    function ticket_post()
    {
        $user_id            = $this->input->post("user_id");
        $ticket_category_id = $this->input->post("ticket_category_id");

        $data['user_id'] = $this->input->post("user_id");
        $data['ticket_category'] = $this->input->post("ticket_category_id");

        $data['created_date']     = date("Y-m-d H:i:s");

        $currentMonth = date('n');
        $currentYear = date('Y');
        $query = $this->db->query("SELECT * FROM ticket where MONTH(created_date) = '" . $currentMonth . "' 
        and YEAR(created_date)  = '" . $currentYear . "' order by id desc limit 1");

        if ($query->num_rows() < 1) {
            $data['id'] = "TIKET-" . date('m') . date('y') . "-0001";
        } else {
            $data['id'] = ++$query->row()->id;
        }

        $query = $this->db->insert('ticket', $data);

        $this->response([
            'status' => true,
            'ticket_id' => $data['id']
        ], 200);
    }

    function ticketdetail_get()
    {
        $ticket_id = $this->get("ticket_id");
        $user_id = $this->get("user_id");
        $data = $this->db->query("SELECT a.* FROM ticket_detail a 
        inner join ticket b on a.ticket_id = b.id where user_id = '" . $user_id . "'
        and b.id = '" . $ticket_id . "' 
         ")->result();

        $this->response([
            'status' => true,
            'data' => $data
        ], 200);
    }

    function ticketdetail_post()
    {
        $ticket_id = $this->input->post("ticket_id");
        $message = $this->input->post("message");
        $user_id = $this->input->post("user_id");
        $date = $this->input->post("date");

        $data = array(
            'ticket_id' => $ticket_id,
            'message' => $message,
            'message_from' => $user_id,
            'message_date' => $date
        );

        $query = $this->db->insert("ticket_detail", $data);

        if ($query) {
            $ch = curl_init();

            $this->response([
                'status' => true,
            ], 200);
        } else {
            $this->response([
                'status' => false,

            ], 200);
        }
    }

    function ticketdetailimage_post()
    {
        $ticket_id = $this->input->post("ticket_id");
        $message = $this->input->post("message");
        $user_id = $this->input->post("user_id");
        $date = $this->input->post("date");

        $config['upload_path']          = './assets/file_upload/ticket_images/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['encrypt_name']         = TRUE;
        $config['quality']              = '50%';
        $this->load->library('upload', $config);



        if (!$this->upload->do_upload('userfile')) {
            $error = array('error' => $this->upload->display_errors());

            $this->response([
                'status' => false,
                'message' =>  "Harap periksa kembali gambar anda",
                'error' => $error
            ], 400);
        }


        $upload_result      =  $this->upload->data();
        $data = array(
            'ticket_id' => $ticket_id,
            'message' => $message,
            'message_from' => $user_id,
            'message_date' => $date,
            'attachment' => $upload_result['file_name']
        );


        $query = $this->db->insert("ticket_detail", $data);
        if ($query) {
            $this->response([
                'status' => true,
            ], 200);
        } else {
            $this->response([
                'err' => $this->db->error(),
                'status' => false,
            ], 200);
        }
    }

    function vendortype_get()
    {
        $start = $this->get('start');
        $limit = $this->get('limit');
        $store_id = $this->get('store_id');
        $search = $this->get("search");
        $like_condition = "";

        if ($start == "" || $limit == "" || $store_id == "") {
            $this->response([
                'status' => false,
                'message' => "Parameter tidak lengkap",
            ], 502);
        }
        if ($search != "") {
            $search = str_replace("_", " ", $search);
            $like_condition = array("type_name" => $search);
        }
        $condition = array("store_id" => $store_id);

        $query = $this->Master_model->get_vendor_type($start, $limit, $condition, $like_condition);
        $this->response(
            $query,
            200
        );
    }

    function vendortype_post()
    {
        $store_id                   = $this->input->post("store_id");
        $data                       = $this->input->post();
        $data['created_date']       = date("Y-m-d H:i:s");

        $currentMonth = date('n');
        $currentYear = date('Y');


        $query = $this->db->query("SELECT type_id from type_vendor 
        where MONTH(created_date) = '" . $currentMonth . "' 
        and YEAR(created_date)  = '" . $currentYear . "' and store_id = '" . $store_id . "'  order by type_id desc limit 1");

        if ($query->num_rows() < 1) {
            $data['type_id'] = "VTY-" . date('m') . date('y') . "-0001";
        } else {
            $data['type_id'] = ++$query->row()->type_id;
        }

        $query = $this->db->insert("type_vendor", $data);

        if ($query) {
            $this->response([
                'status' => true,
                'message' => "Berhasil",
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
            ], 502);
        }
    }

    function vendortype_delete()
    {
        $id = $this->delete('id');
        $store_id = $this->delete('store_id');

        $this->db->where('type_id', $id);
        $this->db->where('store_id', $store_id);
        $delete = $this->db->delete('type_vendor');
        if ($delete) {
            $this->response([
                'status' => true,
                'message' => $id
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' =>  "Woops, harap coba kembali"
            ], 502);
        }
    }

    function vendortype_put()
    {
        $type_id               = $this->put("type_id");
        $store_id             = $this->put("store_id");
        $modified_by          = $this->put("modified_by");

        $data = $this->put();

        $data['modified_date']     = date("Y-m-d H:i:s");
        $this->db->where("type_id", $type_id);
        $this->db->where("store_id", $store_id);

        $update = $this->db->update('type_vendor', $data);
        if ($update) {
            $this->response([
                'status' => true,
                'message' => "Proses update berhasil",
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => "Harap coba kembali",
            ], 500);
        }
    }


    function vendor_get()
    {
        $start = $this->get('start');
        $limit = $this->get('limit');
        $store_id = $this->get('store_id');
        $search = $this->get("search");
        $like_condition = "";

        if ($start == "" || $limit == "" || $store_id == "") {
            $this->response([
                'status' => false,
                'message' => "Parameter tidak lengkap",
            ], 502);
        }
        if ($search != "") {
            $like_condition = array("vendor_name" => $search);
        }
        $condition = array("store_id" => $store_id);

        $query = $this->Master_model->get_vendor($start, $limit, $condition, $like_condition);
        $this->response(
            $query,
            200
        );
    }
    function vendor_post()
    {
        $store_id                   = $this->input->post("store_id");
        $data                       = $this->input->post();
        $data['created_date']       = date("Y-m-d H:i:s");

        $currentMonth = date('n');
        $currentYear = date('Y');


        $query = $this->db->query("SELECT vendor_id from vendor 
        where MONTH(created_date) = '" . $currentMonth . "' 
        and YEAR(created_date)  = '" . $currentYear . "' and store_id = '" . $store_id . "'  order by vendor_id desc limit 1");

        if ($query->num_rows() < 1) {
            $data['vendor_id'] = "V-" . $store_id . date('m') . date('y') . "-0001";
        } else {
            $data['vendor_id'] = ++$query->row()->vendor_id;
        }

        $query = $this->db->insert("vendor", $data);

        if ($query) {
            $this->response([
                'status' => true,
                'message' => "Berhasil",
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
            ], 502);
        }
    }
    function vendor_put()
    {
        $vendor_id               = $this->put("vendor_id");
        $store_id             = $this->put("store_id");
        $modified_by          = $this->put("modified_by");

        $data = $this->put();

        $data['modified_date']     = date("Y-m-d H:i:s");
        $this->db->where("vendor_id", $vendor_id);
        $this->db->where("store_id", $store_id);

        $update = $this->db->update('vendor', $data);
        if ($update) {
            $this->response([
                'status' => true,
                'message' => "Proses update berhasil",
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => "Harap coba kembali",
            ], 500);
        }
    }

    function paymentmethod_get()
    {
        $query = $this->db->query("SELECT payment_type_id,payment_type_name,flag_card FROM payment_type order by payment_type_name asc")->result();
        if ($query) {
            $this->response([
                'status' => true,
                'data' => $query,
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => "Harap coba kembali",
            ], 500);
        }
    }

    function barcodeitem_get()
    {
        $store_id = $this->get("store_id");
        $barcode = $this->get("barcode");

        $query = $this->db->query("select item_type,a.item_code,a.barcode,a.item_name,a.item_short_name,a.item_category_id,a.item_image,
        IFNULL(buffer_min,0)AS buffer_min,
        IFNULL(b.buffer_max,0)AS buffer_max,
        IFNULL(b.quantity ,0) as QtyStok,a.item_price,IFNULL(b.average_cost,0)as Cost,a.store_id
        
        from items a 
        inner join item_stock b on (a.item_code=b.item_code and a.store_id=b.store_id)
        where a.store_id ='" . $store_id . "' and item_type='Produk' and barcode = '" . $barcode . "' ");
        if ($query) {
            if ($query->num_rows() > 0) {
                $this->response([
                    'status' => true,
                    'data' => $query->row(),
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                ], 404);
            }
        }
        $this->response([
            'status' => false,
            'message' => "Harap coba kembali",
        ], 500);
    }

    function barcodeitemv2_get()
    {
        $store_id = $this->get("store_id");
        $barcode = $this->get("barcode");

        $query = $this->db->query("select item_type,a.item_code,a.barcode,a.item_name,a.item_short_name,a.item_category_id,a.item_image,
        IFNULL(buffer_min,0)AS buffer_min,
        IFNULL(b.buffer_max,0)AS buffer_max,
        IFNULL(b.quantity ,0) as QtyStok,a.item_price,IFNULL(b.average_cost,0)as Cost,a.store_id
        IFNULL(a.uom_id,1)as uom_id , IFNULL(c.uom_name,'PCS') as uom_name, IFNULL(c.flag_decimal,false) as flag_decimal

        from items a 
        inner join item_stock b on (a.item_code=b.item_code and a.store_id=b.store_id)
        inner join uom c on (IFNULL(a.uom_id,1)=c.uom_id)
        where a.store_id ='" . $store_id . "' and item_type='Produk' and barcode = '" . $barcode . "' ");
        if ($query) {
            if ($query->num_rows() > 0) {
                $this->response([
                    'status' => true,
                    'data' => $query->row(),
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                ], 404);
            }
        }
        $this->response([
            'status' => false,
            'message' => "Harap coba kembali",
        ], 500);
    }

    function cashflowcategory_post()
    {
        $cash_category_id   = $this->input->post("cash_category_id");
        $store_id           = $this->input->post("store_id");
        $cash_category_name = $this->input->post("cash_category_name");
        $cash_category_type = $this->input->post("cash_category_type");
        $user_id            = $this->input->post("user_id");
        // $query = $this->Master_model->add_cashflow_category($cash_category_id,$store_id, $cash_category_name, $cash_category_type, $user_id);
        $query = $this->Master_model->add_cashflow_category($this->input->post());

        if ($query) {
            $this->response([
                'status' => true,
                'message' => "Proses berhasil",
            ], 200);
        }
        $this->response([
            'status' => false,
            'message' => "Harap coba kembali",
        ], 500);
    }

    function cashflowcategory_put()
    {
        $cash_category_id   = $this->put("cash_category_id");
        $store_id           = $this->put("store_id");
        $cash_category_name = $this->put("cash_category_name");
        $cash_category_type = $this->input->post("cash_category_type");
        $user_id            = $this->input->post("user_id");
        // $query = $this->Master_model->add_cashflow_category($cash_category_id,$store_id, $cash_category_name, $cash_category_type, $user_id);
        $query = $this->Master_model->edit_cashflow_category($this->put());

        if ($query) {
            $this->response([
                'status' => true,
                'message' => "Proses berhasil",
            ], 200);
        }
        $this->response([
            'status' => false,
            'message' => "Harap coba kembali",
        ], 500);
    }

    function cashflowcategory_get()
    {
        $store_id           = $this->get("store_id");
        $type           = $this->get("type");

        if ($type == "all") {
            $condition          = array("store_id" => $store_id);
        } else {
            $condition          = array("store_id" => $store_id, "type" => $type);
        }

        $query = $this->Master_model->get_cashflow_category($condition);

        if ($query) {
            if ($query->num_rows() > 0) {
                $this->response([
                    'status' => true,
                    'data' => $query->result(),
                ], 200);
            } else {
                $this->response([
                    'status' => true,
                    'data' => [],
                ], 200);
            }
        }
        $this->response([
            'status' => false,
            'message' => "Harap coba kembali",
        ], 500);
    }
    function cashflowcategory_delete()
    {
        $cash_category_id           = $this->delete("id");
        $store_id                   = $this->delete("store_id");

        $condition          = array("store_id" => $store_id, "cash_category_id" => $cash_category_id);

        $query = $this->Master_model->delete_cashflow_category($condition);

        if ($query->affected_rows() > 0) {
            $this->response([
                'status' => true,
                'data' => [],
            ], 200);
        }
        $this->response([
            'status' => false,
            'message' => "Harap coba kembali",
        ], 500);
    }
}