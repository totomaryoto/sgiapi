<?php

use chriskacerguis\RestServer\RestController;


class Inventory extends RestController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Inventory_model");
        // $headers = $this->input->request_headers();

        // if ($headers['Authorization'] == null) {
        //     $this->response([
        //         'status' => false,
        //         'message' => "Token is required"
        //     ], 401);
        // }
        // if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
        //     $decodedToken = AUTHORIZATION::validateToken($headers['Authorization']);
        //     if ($decodedToken == false || empty($decodedToken) || $decodedToken != "false") {
        //         $this->response([
        //             'status' => false,
        //         ], 401);
        //     }
        // }
    }

    function purchaseorder_get()
    {
        $id = $this->get('id');
        $start = $this->get('start');
        $limit = $this->get('limit');
        $store_id = $this->get('store_id');
        $search_title = $this->get('title');
        $search_vendor = $this->get('vendor');
        $search_startdate = $this->get('startdate');
        $search_enddate = $this->get('enddate');
        $condition = array("a.store_id" => $store_id);
        if ($id != null) {
            $condition = array("po_id" => $id, "a.store_id" => $store_id);
            $data = $this->Inventory_model->get_purchase_order($condition);
            $this->response([
                'status' => true,
                'data' =>  $data
            ], 200);
        }
        $date_condition = "";
        if ($search_startdate != "" or $search_enddate != "") {
            $date_condition = array(
                'po_date >=', $search_startdate,
                'po_date <=', $search_enddate,
            );
        }

        $like_condition = array(
            "po_title" => $search_title ?? '',
            "a.vendor_id" => $search_vendor ?? '',
        );

        $data = $this->Inventory_model->get_purchase_orders($start, $limit, $condition, $date_condition, $like_condition);
        if (count($data) > 0) {
            $this->response([
                'status' => true,
                'data' =>  $data
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'data' =>  []
            ], 200);
        }
    }
    function purchaseorder_post()
    {
        $data = $this->Inventory_model->create_po();
        if ($data == null) {
            $this->response([
                'status' => false,
                'message' =>  "Pembuatan PO gagal. Harap coba kembali"
            ], 500);
        } else {
            $this->response(
                $data,
                200
            );
        }
    }

    function purchaseorder_put()
    {
        $po_id                   = $this->put("po_id");

        $store_id                   = $this->put("store_id");
        $vendor_id                  = $this->put("vendor_id");
        $modified_by                = $this->put("created_by");
        $po_date                    = $this->put("po_date");

        $data = $this->put();

        $data['modified_by'] = $this->put("created_by");
        $data['modified_date']     = date("Y-m-d H:i:s");
        $this->db->where("store_id", $store_id);
        $this->db->where("po_id", $po_id);

        $update = $this->db->update('po_header', $data);
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

    function purchaseorder_delete()
    {
        $po_id         = $this->delete('po_id');
        $store_id   = $this->delete('store_id');

        $this->db->where('po_id', $po_id);
        $this->db->where('store_id', $store_id);
        $delete = $this->db->delete('po_header');

        $this->db->where('po_id', $po_id);
        $this->db->where('store_id', $store_id);
        $delete_detail = $this->db->delete('po_detail');


        if ($delete) {
            $this->response([
                'status' => true,
                'message' => $po_id
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' =>  "Woops, harap coba kembali"
            ], 502);
        }
    }

    function purchaseorderdetail_post()
    {
        $query = $this->Inventory_model->create_podetail();
        if ($query) {
            $this->response([
                'status' => true,
                'message' =>  "Pembuatan PO Detail berhasil"
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' =>  "Pembuatan PO Detail gagal. Harap coba kembali"
            ], 500);
        }
    }
    function purchaseorderdetail_get()
    {
        $store_id   = $this->get('store_id');
        $po_id      = $this->get('po_id');
        $start = $this->get('start');
        $limit = $this->get('limit');

        if ($po_id == null || $store_id == null) {
            $this->response([
                'status' => false,
                'message' => "Parameter tidak lengkap",
            ], 502);
        }
        $data = $this->Inventory_model->get_podetail($po_id, $store_id, $start, $limit);
        $this->response(
            $data ?? [],
            200
        );
    }
    function purchaseorderdetail_delete()
    {
        $po_id         = $this->delete('po_id');
        $store_id       = $this->delete('store_id');
        $pod_id       = $this->delete('pod_id');

        $this->db->where('po_id', $po_id);
        $this->db->where('pod_id', $pod_id);

        $this->db->where('store_id', $store_id);
        $delete = $this->db->delete('po_detail');


        if ($delete) {
            $this->response([
                'status' => true,
                'message' => $po_id
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' =>  "Woops, harap coba kembali"
            ], 502);
        }
    }
    function purchaseorderdetail_put()
    {
        $po_id                   = $this->put("po_id");
        $pod_id                   = $this->put("pod_id");

        $store_id                   = $this->put("store_id");
        $qty                  = $this->put("qty");
        $modified_by                = $this->put("created_by");
        $item_code                    = $this->put("item_code");

        $data = $this->put();

        $data['modified_by'] = $this->put("created_by");
        $data['modified_date']     = date("Y-m-d H:i:s");

        $update = $this->db->query("call t_InsPoUpdateDetail ('" . $store_id . "', '" . $po_id . "','" . $item_code . "', '" . $qty . "', '" . $modified_by . "'  ) ");


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
    function puchaseorderupdatestatus_put()
    {

        $po_id      = $this->put("po_id");
        $store_id   = $this->put("store_id");
        $status     = $this->put("status");
        $created_by = $this->put("created_by");

        $data = $this->Inventory_model->update_status_po($store_id, $po_id, $status, $created_by);
        if ($data == null) {
            $this->response([
                'status' => false,
                'message' =>  "Pembuatan PO gagal. Harap coba kembali"
            ], 500);
        } else {
            $this->response(
                $data,
                200
            );
        }
    }
    function puchaseorderapprove_put()
    {
        $po_id      = $this->put("po_id");
        $store_id   = $this->put("store_id");
        $created_by = $this->put("created_by");

        $data = $this->Inventory_model->update_approve_po($store_id, $po_id, $created_by);
        if ($data == null) {
            $this->response([
                'status' => false,
                'message' =>  "Pembuatan PO gagal. Harap coba kembali"
            ], 500);
        } else {
            $this->response(
                $data,
                200
            );
        }
    }
    function purchasereceipt_post()
    {
        $data = $this->Inventory_model->create_purchase_receipt();
        if ($data == null) {
            $this->response([
                'status' => false,
                'message' =>  "Pembuatan PO gagal. Harap coba kembali"
            ], 500);
        } else {
            $this->response(
                $data,
                200
            );
        }
    }

    function purchasereceipt_get()
    {
        $id = $this->get('id');
        $po_id = $this->get('po_id');

        $start = $this->get('start');
        $limit = $this->get('limit');
        $store_id = $this->get('store_id');
        $search_title = $this->get('title');
        $search_vendor = $this->get('vendor');
        $search_startdate = $this->get('startdate');
        $search_enddate = $this->get('enddate');
        $condition = array("a.store_id" => $store_id);
        if ($id != null) {
            $condition = array("rcv_id" => $id, "a.store_id" => $store_id, "po_id" => $po_id);
            $data = $this->Inventory_model->get_purchase_receipt($condition);
            $this->response([
                'status' => true,
                'data' =>  $data
            ], 200);
        }
        $date_condition = "";
        if ($search_startdate != "" or $search_enddate != "") {
            $date_condition = array(
                'a.created_date >=', $search_startdate,
                'a.created_date <=', $search_enddate,
            );
        }

        $like_condition = array(
            "po_title" => $search_title ?? '',
            "a.vendor_id" => $search_vendor ?? '',
        );

        $data = $this->Inventory_model->get_purchase_receipts($start, $limit, $condition, $date_condition, $like_condition);
        if (count($data) > 0) {
            $this->response([
                'status' => true,
                'data' =>  $data
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'data' =>  []
            ], 200);
        }
    }

    function purchasereceiptdetail_get()
    {
        $store_id   = $this->get('store_id');
        $pr_id      = $this->get('pr_id');
        $start = $this->get('start');
        $limit = $this->get('limit');

        if ($pr_id == null || $store_id == null) {
            $this->response([
                'status' => false,
                'message' => "Parameter tidak lengkap",
            ], 502);
        }
        $data = $this->Inventory_model->get_prdetail($pr_id, $store_id, $start, $limit);
        $this->response(
            $data ?? [],
            200
        );
    }

    function purchasereceiptdetail_post()
    {
        $store_id           = $this->input->post("store_id");
        $rcv_id             = $this->input->post("rcv_id");
        $rcvd_id            = $this->input->post("rcvd_id");
        $item_code          = $this->input->post("item_code");
        $modified_by        = $this->input->post("modified_by");
        $qty_rcv            = $this->input->post("qty_rcv");

        $data = $this->Inventory_model->update_quantity_receipt($store_id, $rcv_id, $rcvd_id, $item_code, $modified_by, $qty_rcv);
        if ($data) {
            $this->response(
                $data ?? [],
                200
            );
        }
        $this->response([
            'status' => false,
            'message' => "Update gagal",
        ], 500);
    }
    function puchasereceiptupdatestatus_put()
    {

        $pr_id      = $this->put("pr_id");
        $store_id   = $this->put("store_id");
        $status     = $this->put("status");
        $created_by = $this->put("created_by");

        $data = $this->Inventory_model->update_status_pr($store_id, $pr_id, $status, $created_by);
        if ($data == null) {
            $this->response([
                'status' => false,
                'message' =>  "Pembuatan PO gagal. Harap coba kembali"
            ], 500);
        } else {
            $this->response(
                $data,
                200
            );
        }
    }

    function puchasereceiptapprove_put()
    {
        $pr_id      = $this->put("pr_id");
        $store_id   = $this->put("store_id");
        $created_by = $this->put("created_by");

        $data = $this->Inventory_model->update_approve_pr($store_id, $pr_id, $created_by);
        if ($data == null) {
            $this->response([
                'status' => false,
                'message' =>  "Pembuatan PO gagal. Harap coba kembali"
            ], 500);
        } else {
            $this->response(
                $data,
                200
            );
        }
    }
}