<?php



use chriskacerguis\RestServer\RestController;



class Transaction extends RestController

{



    function __construct()

    {

        parent::__construct();

        $this->load->model("Master_model");

        $this->load->model("Transaction_model");

        header("Access-Control-Allow-Origin: *");

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





    function itemstransaction_get()

    {

        $start = $this->get('start');

        $limit = $this->get('limit');

        $store_id = $this->get('store_id');

        $category_id = $this->get('category_id');

        $search = $this->get('search');

        $partner = $this->get("partner");



        if ($category_id == "all") {

            $category_id = "";
        }

        if ($search == "all") {

            $search = "";
        }



        $query = $this->Master_model->get_item_transaction($start, $limit, $store_id, $category_id, $partner, $search);



        if ($query) {

            $this->response(

                $query,

                200

            );
        } else {

            $this->response([

                'status' => false,

                'message' => "Woops, harap coba kembali",

                'data' => $query

            ], 500);
        }
    }



    function itemstransactionv2_get()

    {

        $start = $this->get('start');

        $limit = $this->get('limit');

        $store_id = $this->get('store_id');

        $category_id = $this->get('category_id');

        $search = $this->get('search');

        $partner = $this->get("partner");



        if ($category_id == "all") {

            $category_id = "";
        }

        if ($search == "all") {

            $search = "";
        }



        $query = $this->Master_model->get_item_transactionv2($start, $limit, $store_id, $category_id, $partner, $search);



        if ($query) {

            $this->response(

                $query,

                200

            );
        } else {

            $this->response([

                'status' => false,

                'message' => "Woops, harap coba kembali",

                'data' => $query

            ], 500);
        }
    }





    function stock_post()

    {

        $item_code      = $this->input->post("item_code");

        $item_quantity  = $this->input->post("quantity");

        $store_id       = $this->input->post("store_id");

        $data = $this->input->post();

        $data['created_date']     = date("Y-m-d H:i:s");



        $cek = $this->db->query("SELECT * FROM item_stock where item_code = '" . $item_code . "' and store_id = '" . $store_id . "' ")->num_rows();

        if ($cek < 1) {

            $query = $this->db->insert("item_stock", $data);

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
        } else {



            $dataUpdate['item_code']            = $item_code;

            $dataUpdate['store_id']             = $store_id;

            $dataUpdate['quantity']             = $item_quantity;

            $dataUpdate['modified_date']        = date("Y-m-d H:i:s");

            $dataUpdate['modified_by']          =  $this->input->post("created_by");



            $this->db->where(array("item_code" => $item_code));

            $this->db->where(array("store_id" => $store_id));

            $update = $this->db->update("item_stock", $dataUpdate);

            if ($update) {

                $this->response([

                    'status' => true,

                    'message' => "Berhasil",

                ], 200);
            } else {

                $this->response([

                    'status' => false,

                    'message' => "Woops, harap coba kembali",

                ], 500);
            }
        }
    }



    function sales_delete()

    {

        $store_id           = $this->delete('store_id');

        $user_id            = $this->delete('user_id');

        $data_user          = $this->Master_model->get_user(array("id" => $user_id));

        $device_id          = $data_user->device_id;



        $query = $this->db->query("DELETE from transaction_detail_tmp

        where store_id='" . $store_id . "' and device_id ='" . $device_id . "'");

        if ($query) {

            $this->response([

                'status' => true,

                'message' => "Berhasil",

            ], 200);
        } else {

            $this->response([

                'status' => false,

                'message' => "Woops, harap coba kembali",

            ], 500);
        }
    }



    function sales_post()

    {



        $user_id            = $this->input->post("created_by");

        $store_id           = $this->input->post("store_id");

        $data_user          = $this->Master_model->get_user(array("id" => $user_id));

        $created_by         = $this->input->post("created_by");

        $partner_type       = 0;

        $partner_code       = "";

        $payment_method     = $this->input->post("payment_method");

        $amount             = $this->input->post("amount");

        $card_no            = $this->input->post("card_no");

        $grand_total        = $this->input->post("grand_total");



        $query              = $this->Transaction_model->post_transaction($store_id, $data_user->device_id, $partner_type, $partner_code, $created_by);



        if (!$query) {

            $this->response([

                'status' => false,

                'message' => "Woops, harap coba kembali",

            ], 500);
        }

        $transaction_id     = $query->transaction_id;







        $midtrans_transaction_id = "";

        $midtrans_order_id = "";

        $status_code = "";

        $bank = "";

        $biller_code = "";

        $biller_key = "";

        $this->insert_payment($transaction_id, $store_id, $grand_total, $amount, $payment_method, $midtrans_transaction_id, $midtrans_order_id, $status_code, $card_no, $bank, $biller_code, $biller_key, $user_id);
    }



    function insert_payment($transaction_id, $store_id, $grand_total, $amount, $payment_method, $midtrans_transaction_id, $midtrans_order_id, $status_code, $card_no, $bank, $biller_code, $biller_key, $user_id)

    {

        $insert_payment = $this->Transaction_model->insert_payment($transaction_id, $store_id, $grand_total, $amount, $payment_method, $midtrans_transaction_id, $midtrans_order_id, $status_code, $card_no, $bank, $biller_code, $biller_key, $user_id);



        if (!$insert_payment) {

            $this->response([

                'status' => false,

                'message' => "Woops, harap coba kembali",

            ], 500);
        } else {

            $this->response([

                'status' => true,

                'message' => "Berhasil",

                'query' =>  $insert_payment



            ], 200);
        }
    }



    function sales_get()

    {

        $transaction_id = $this->get("transaction_id");

        $store_id = $this->get("store_id");



        $query = $this->db->query("SELECT a.*, b.item_name from transaction_detail a 

                                    inner join items b on a.item_code = b.item_code

                                    where a.transaction_id = '" . $transaction_id . "' and store_id = '" . $store_id . "'

                                    ");
    }

    function cartprint_get()
    {
        $transaction_id         = $this->get("transaction_id");
        $store_id           = $this->get("store_id");

        $query = $this->Transaction_model->get_cart_print($store_id, $transaction_id);
        $this->response(
            $query,
            200
        );
    }



    function cartv2_get()
    {
        $created_by         = $this->get("created_by");
        $store_id           = $this->get("store_id");
        $data_user          = $this->Master_model->get_user(array("id" => $created_by));

        $query = $this->Transaction_model->get_cartv2($store_id, $created_by, $data_user->device_id);
        $this->response(
            $query,
            200
        );
    }

    function cart_get()
    {
        $created_by = $this->get("created_by");
        $store_id = $this->get("store_id");
        $data_user          = $this->Master_model->get_user(array("id" => $created_by));


        $query = $this->Transaction_model->get_cart($store_id, $created_by, $data_user->device_id);
        $this->response(
            $query,
            200
        );
    }


    function item_post()

    {

        $user_id            = $this->input->post("created_by");

        $store_id           = $this->input->post("store_id");





        $data_user          = $this->Master_model->get_user(array("id" => $user_id));







        $item_code          = $this->input->post("item_code");

        $item_short_name    = $this->input->post("item_short_name");

        $item_price         = $this->input->post("item_price");

        $qty                = $this->input->post("qty");

        $disc               = $this->input->post("disc");

        $tax                = $this->input->post("tax");

        $created_by         = $this->input->post("created_by");





        $query = $this->Transaction_model->post_item(

            $user_id,

            $store_id,

            $data_user->device_id,

            $item_code,

            $item_short_name,

            $item_price,

            $qty,

            $disc,

            $tax,

            $created_by

        );



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

            ], 502);
        }
    }



    function itemqty_post()

    {

        $user_id            = $this->input->post("created_by");

        $store_id           = $this->input->post("store_id");





        $data_user          = $this->Master_model->get_user(array("id" => $user_id));







        $item_code          = $this->input->post("item_code");

        $item_short_name    = $this->input->post("item_short_name");

        $item_price         = $this->input->post("item_price");

        $qty                = $this->input->post("qty");

        $disc               = $this->input->post("disc");

        $tax                = $this->input->post("tax");

        $created_by         = $this->input->post("created_by");





        $query = $this->Transaction_model->updateqty(

            $user_id,

            $store_id,

            $data_user->device_id,

            $item_code,

            $item_short_name,

            $item_price,

            $qty,

            $disc,

            $tax,

            $created_by

        );



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

            ], 502);
        }
    }

    function item_put()

    {

        $user_id            = $this->put("created_by");

        $store_id           = $this->put("store_id");





        $data_user          = $this->Master_model->get_user(array("id" => $user_id));







        $item_code          = $this->put("item_code");

        $item_short_name    = $this->put("item_short_name");

        $item_price         = $this->put("item_price");

        $qty                = $this->put("qty");

        $disc               = $this->put("disc");

        $tax                = $this->put("tax");

        $created_by         = $this->put("created_by");





        $query = $this->Transaction_model->update_item(

            $user_id,

            $store_id,

            $data_user->device_id,

            $item_code,

            $item_short_name,

            $item_price,

            $qty,

            $disc,

            $tax,

            $created_by

        );



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

            ], 502);
        }
    }

    function cashin_post()

    {

        $cashflow_category = $this->input->post("cashflow_category");

        $cashflow_title = $this->input->post("cashflow_title");

        $cashflow_amount = $this->input->post("cashflow_amount");

        $store_id = $this->input->post("store_id");

        $created_by = $this->input->post("created_by");



        $query = $this->Transaction_model->insert_cashin($cashflow_title, $cashflow_category, $cashflow_amount, $store_id, $created_by);

        if ($query) {

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



    function cashin_get()

    {

        $store_id               = $this->get("store_id");

        $start                  = $this->get("start");

        $limit                  = $this->get("limit");

        $cashflow_category      = $this->get("category");

        $start_date             = $this->get("start_date");

        $end_date               = $this->get("end_date");







        $query = $this->Transaction_model->get_cashin($store_id, $cashflow_category, $start_date, $end_date, $start, $limit);

        $this->response([

            'status' => true,

            'message' => "Berhasil",

            'data' => $query



        ], 200);
    }



    function cashin_put()

    {

        $cash_in_id = $this->put("cash_in_id");

        $cashflow_category = $this->put("cashflow_category");

        $cashflow_title = $this->put("cashflow_title");

        $cashflow_amount = $this->put("cashflow_amount");

        $store_id = $this->put("store_id");

        $created_by = $this->put("created_by");





        $query = $this->Transaction_model->update_cashin($cash_in_id, $cashflow_title, $cashflow_category, $cashflow_amount, $store_id, $created_by);

        if ($query) {

            if ($query->row()->status == "1") {

                $this->response([

                    'status' => true,

                    'data' => [],

                ], 200);
            } else {

                $this->response([

                    'status' => false,

                    'message' => $query->row()->msg,

                ], 500);
            }
        }

        $this->response([

            'status' => false,

            'message' => "Harap coba kembali",

        ], 500);
    }



    function approvecashin_put()

    {

        $cash_in_id = $this->put("cash_in_id");

        $store_id = $this->put("store_id");

        $created_by = $this->put("created_by") ?? "";

        $query = $this->Transaction_model->approve_cashin($cash_in_id, $store_id, $created_by);

        if ($query) {

            $this->response([

                'status' => true,



            ], 200);
        }

        $this->response([

            'status' => false,

            'message' => "Harap coba kembali",

        ], 500);
    }



    function cashin_delete()

    {

        $cash_in_id           = $this->delete('id');

        $store_id            = $this->delete('store_id');





        $query = $this->Transaction_model->delete_cashin($store_id, $cash_in_id);

        if ($query) {

            if ($query->row()->status == "1") {

                $this->response([

                    'status' => true,

                    'message' => "Berhasil",

                ], 200);
            } else {

                $this->response([

                    'status' => false,

                    'message' => $query->row()->msg,

                ], 500);
            }
        } else {

            $this->response([

                'status' => false,

                'message' => "Woops, harap coba kembali",

            ], 500);
        }
    }



    function cashout_get()

    {

        $store_id               = $this->get("store_id");

        $start                  = $this->get("start");

        $limit                  = $this->get("limit");

        $cashflow_category      = $this->get("category");

        $start_date             = $this->get("start_date");

        $end_date               = $this->get("end_date");







        $query = $this->Transaction_model->get_cashout($store_id, $cashflow_category, $start_date, $end_date, $start, $limit);

        $this->response([

            'status' => true,

            'message' => "Berhasil",

            'data' => (!$query) ? [] : $query

        ], 200);
    }



    function cashout_post()

    {

        $cashflow_category = $this->input->post("cashflow_category");

        $cashflow_title = $this->input->post("cashflow_title");

        $cashflow_amount = $this->input->post("cashflow_amount");

        $store_id = $this->input->post("store_id");

        $created_by = $this->input->post("created_by");



        $query = $this->Transaction_model->insert_cashout($cashflow_title, $cashflow_category, $cashflow_amount, $store_id, $created_by);

        if ($query) {
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



    function cashout_put()

    {

        $cash_out_id = $this->put("cash_out_id");

        $cashflow_category = $this->put("cashflow_category");

        $cashflow_title = $this->put("cashflow_title");

        $cashflow_amount = $this->put("cashflow_amount");

        $store_id = $this->put("store_id");

        $created_by = $this->put("created_by");



        $query = $this->Transaction_model->update_cashout($cash_out_id, $cashflow_title, $cashflow_category, $cashflow_amount, $store_id, $created_by);

        if ($query) {

            if ($query->row()->status == "1") {

                $this->response([

                    'status' => true,

                    'data' => [],

                ], 200);
            }

            $this->response([

                'status' => false,

                'message' => $query->row()->msg,

            ], 500);
        }

        $this->response([

            'status' => false,

            'message' => "Harap coba kembali",

        ], 500);
    }



    function cashout_delete()

    {

        $cash_in_id           = $this->delete('id');

        $store_id            = $this->delete('store_id');





        $query = $this->Transaction_model->delete_cashout($store_id, $cash_in_id);

        if ($query) {

            if ($query->row()->status == "1") {

                $this->response([

                    'status' => true,

                    'message' => "Berhasil",

                ], 200);
            } else {

                $this->response([

                    'status' => false,

                    'message' => $query->row()->msg,

                ], 500);
            }
        } else {

            $this->response([

                'status' => false,

                'message' => "Woops, harap coba kembali",

            ], 500);
        }
    }



    function approvecashout_put()

    {

        $cash_out_id = $this->put("cash_out_id");

        $store_id = $this->put("store_id");

        $created_by = $this->put("created_by") ?? "";

        $query = $this->Transaction_model->approve_cashout($cash_out_id, $store_id, $created_by);

        if ($query) {

            $this->response([

                'status' => true,



            ], 200);
        }

        $this->response([

            'status' => false,

            'message' => "Harap coba kembali",

        ], 500);
    }



    function salesreturn_post()

    {

        $store_id       = $this->input->post("store_id");

        $transaction_id = $this->input->post("transaction_id");

        $user_id        = $this->input->post("user_id");



        $query = $this->Transaction_model->save_sales_retur($store_id, $transaction_id, $user_id);

        if ($query) {

            $response = $query->row();

            if ($response->status == "0") {

                $this->response([

                    'status' => true,

                    'retur_id' => $query->row()->retur_id

                ], 200);
            } else {

                $this->response([

                    'status' => true,

                    'retur_id' => $response->retur_id,

                ], 200);
            }
        } else {

            $this->response([

                'status' => false,

                'message' =>  "Woops, harap coba kembali",

                'error' => $query->row()

            ], 500);
        }

        $this->response([

            'status' => false,

            'data' => $this->input->post()

        ], 500);
    }

    function salesreturbyid_get()

    {

        $store_id = $this->get("store_id");

        $transaction_id = $this->get("transaction_id");

        $query = $this->Transaction_model->get_salesretur_by_id($store_id, $transaction_id);

        if ($query) {

            $this->response([

                'status' => true,

                'data' => $query

            ], 200);
        }

        $this->response([

            'status' => false,

            'data' => $this->input->post()

        ], 500);
    }



    function salesreturdetail_get()

    {

        $store_id = $this->get("store_id");

        $transaction_id = $this->get("transaction_id");

        $query = $this->Transaction_model->get_salesretur_detail($store_id, $transaction_id);

        if ($query) {

            $this->response([

                'status' => true,

                'data' => $query

            ], 200);
        }

        $this->response([

            'status' => false,

            'data' => $this->input->post()

        ], 500);
    }



    function salesreturdetail_post()

    {

        $store_id       = $this->input->post("store_id");

        $transaction_id = $this->input->post("transaction_id");

        $user_id        = $this->input->post("user_id");

        $detail_id        = $this->input->post("detail_id");

        $qty        = $this->input->post("qty");

        $item_code = $this->input->post("item_code");



        $query = $this->Transaction_model->update_sales_retur_detail($store_id, $transaction_id, $user_id, $detail_id, $qty, $item_code);

        if ($query) {

            $this->response([

                'status' => true,

            ], 200);
        }

        $this->response([

            'status' => false,

            'data' => $this->input->post()

        ], 500);
    }





    function salesretur_put()

    {

        $store_id       = $this->put("store_id");

        $transaction_id = $this->put("transaction_id");

        $status         = $this->put("status");

        $user_id        = $this->put("user_id");



        $query = $this->Transaction_model->update_sales_retur($store_id, $transaction_id, $status, $user_id);

        if ($query) {

            $response = $query->row();

            if ($response->status == "0") {

                $this->response([

                    'status' => false,

                    'message' => $response->msg

                ], 200);
            } else {

                $this->response([

                    'status' => true,

                    'message' => $response->msg

                ], 200);
            }
        } else {

            $this->response([

                'status' => false,

                'message' =>  "Woops, harap coba kembali",

            ], 500);
        }
    }

    function salesreturapprove_put()

    {

        $store_id       = $this->put("store_id");

        $transaction_id = $this->put("transaction_id");

        $user_id        = $this->put("user_id");



        $query = $this->Transaction_model->approve_sales_retur($store_id, $transaction_id, $user_id);

        if ($query) {

            $this->response([

                'status' => true,

                'message' => "Berhasil"

            ], 200);
        } else {

            $this->response([

                'status' => false,

                'message' =>  "Woops, harap coba kembali",

            ], 500);
        }
    }

    function settlement_get()
    {
        $store_id               = $this->get("store_id");
        $created_by             = $this->get("created_by");
        $type_settlement        = $this->get("type");


        // p_store_id VARCHAR(64),									
        // p_total_transaction decimal(18,2),
        // p_total_payment_cash decimal(18,2),
        // p_total_payment_non_cash decimal(18,2),
        // p_inital_amount decimal(18,2),
        // p_blind_amount decimal(18,2),
        // p_created_by varchar(64),
        // p_type_settlement int

        $data =  $this->db->query("call w_settlementread (1 , '" . $store_id . "','" . $type_settlement . "','" . $created_by . "') ")->result();
        $this->response(
            $data,
            200
        );
    }
    function settlement_post()
    {
        $store_id               = $this->input->post("store_id");
        $created_by             = $this->input->post("created_by");
        $penerimaan_tunai       = $this->input->post("penerimaan_tunai");
        $penerimaan_non_tunai   = $this->input->post("penerimaan_non_tunai");
        $modal_awal             = $this->input->post("modal_awal");
        $uang_lebihan           = $this->input->post("uang_lebihan");
        $type_settlement        = $this->input->post("settlement_type");
        $total_transaction      = $this->input->post("total_transaction");


        $save = $this->db->query("
       call w_settlementIns('" . $store_id . "','" . $total_transaction . "',
        '" . $penerimaan_tunai . "','" . $penerimaan_non_tunai . "',
        '" . $modal_awal . "','" . $uang_lebihan . "',
        '" . $created_by . "','" . $type_settlement . "') ")->row();



        if ($save) {

            if ($save->status == "0") {
                $this->response([
                    'status' => false,
                    'message' => $save->msg,
                ], 200);
            } else {
                $this->response([
                    'status' => true,
                    'message' => $save->msg,
                ], 200);
            }
        } else {
            $this->response([
                'status' => false,
                'message' =>  "Woops, harap coba kembali",
            ], 500);
        }
    }
}