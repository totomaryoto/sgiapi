<?php



class Transaction_model extends CI_Model

{



    public function updateqty(

        $user_id,

        $store_id,

        $device_id,

        $item_code,

        $item_short_name,

        $item_price,

        $qty,

        $disc,

        $tax,

        $created_by

    ) {



        return $this->db->query("call t_TransactionInsDetailTmpUpdateByInput(

        '1',

     '" . $store_id . "',

     '" . $device_id . "',

     '" . $item_code . "',

     '" . $item_short_name . "',

     '" . $item_price . "',



     '" . $qty . "',

     '" . $disc . "',

     '" . $tax . "',

     '" . $created_by . "' )")->row();
    }



    public function post_item(

        $user_id,

        $store_id,

        $device_id,

        $item_code,

        $item_short_name,

        $item_price,

        $qty,

        $disc,

        $tax,

        $created_by

    ) {



        return $this->db->query("call t_TransactionInsDetailTmp(

        '1',

     '" . $store_id . "',

     '" . $device_id . "',

     '" . $item_code . "',

     '" . $item_short_name . "',

     '" . $item_price . "',



     '" . $qty . "',

     '" . $disc . "',

     '" . $tax . "',

     '" . $created_by . "' )")->row();
    }



    public function update_item(

        $user_id,

        $store_id,

        $device_id,

        $item_code,

        $item_short_name,

        $item_price,

        $qty,

        $disc,

        $tax,

        $created_by

    ) {



        return $this->db->query("call t_TransactionInsDetailTmp(

        '2',

     '" . $store_id . "',

     '" . $device_id . "',

     '" . $item_code . "',

     '" . $item_short_name . "',

     '" . $item_price . "',



     '" . $qty . "',

     '" . $disc . "',

     '" . $tax . "',

     '" . $created_by . "' )")->row();
    }



    // `t_TransactionDetailTmpRead`(p_fa int,p_device_id  varchar(255),

    // p_store_id varchar(64),p_created_by varchar(64))



    function get_cart($store_id, $created_by, $device_id)

    {

        // return $this->db->query("call t_TransactionDetailTmpRead(1, '" . $device_id . "','" . $store_id . "','" . $created_by . "') ")->result();


        $this->db->select("item_code,item_short_name as item_name,FORMAT(FLOOR(qty),0) qty,item_price,disc,total_disc,tax,total_tax,sub_total, (item_price * qty) as grand_total");
        $this->db->from('transaction_detail_tmp');
        $this->db->where("device_id", $device_id);
        $this->db->where("store_id", $store_id);
        $this->db->where("created_by", $created_by);

        return $this->db->get()->result();
    }
    function get_cartv2($store_id, $created_by, $device_id)
    {
        return $this->db->query("call t_TransactionDetailTmpRead(1, '" . $device_id . "','" . $store_id . "','" . $created_by . "') ")->result();
    }

    function get_cart_print($store_id, $transaction_id)
    {
        return $this->db->query("call w_SalesReprint(1, '" . $store_id . "','" . $transaction_id . "') ")->result();
    }

    function post_transaction($store_id, $device_id, $partner_type, $partner_code, $created_by)

    {

        mysqli_next_result($this->db->conn_id);

        return $this->db->query("call t_TransactionInsHeaderDetail(

            '1',

         '" . $store_id . "',

         '" . $device_id . "',

         '" . $partner_type . "',

         '" . $partner_code . "',

         '" . $created_by . "' )")->row();
    }



    function insert_payment($transaction_id, $store_id, $grand_total, $amount, $payment_method, $midtrans_transaction_id, $midtrans_order_id, $status_code, $card_no, $bank, $biller_code, $biller_key, $user_id)

    {

        mysqli_next_result($this->db->conn_id);

        return $this->db->query(

            " call t_TransactionPaymentSave ('" . $transaction_id . "',

                                        '" . $store_id . "',

                                      '" . $grand_total . "',

                                       '" . $amount . "',

                                       '" . $payment_method . "',

                                        '" . $midtrans_transaction_id . "',

                                      '" . $midtrans_order_id . "',

                                        '" . $status_code . "',

                                       '" . $card_no . "',

                                      '" . $bank . "',

    

                                        '" . $biller_code . "',

                                        '" . $biller_key . "', '" . $user_id . "' )"

        )->row();
    }



    function insert_cashin($cashflow_title, $cashflow_category, $cashflow_amount, $store_id, $created_by)

    {

        return $this->db->query("call w_CashInSave('" . $store_id . "','" . $cashflow_category . "', '" . $cashflow_title . "', '" . $cashflow_amount . "','" . $created_by . "' ) ");
    	// return $this->db->query("call w_CashInSave('" . $store_id . "','" . $cashflow_category . "', '" . $cashflow_title . "', '" . $cashflow_amount . "','" . $created_by . "' ) ")->row();
   

   
    }

    function update_cashin($cash_in_id, $cashflow_title, $cashflow_category, $cashflow_amount, $store_id, $created_by)

    {

        return $this->db->query("call w_CashUpdate(1,'" . $store_id . "','" . $cash_in_id . "','" . $cashflow_category . "', '" . $cashflow_title . "', '" . $cashflow_amount . "' ) ");
    }



    function approve_cashin($cash_in_id, $store_id, $user_id)

    {

        $data = array(

            'status_cash_in' => 1

        );



        $this->db->where('cash_in_id', $cash_in_id);

        $this->db->where('store_id', $store_id);



        return $this->db->update('cash_in', $data);
    }



    function get_cashin($store_id, $cashflow_category, $start_date, $end_date, $start, $limit)

    {

        if ($cashflow_category == "all") {

            $cashflow_category = "";
        }

        return $this->db->query("call w_CashRead(1, '" . $store_id . "','" . $cashflow_category . "','" . $start_date . "','" . $end_date . "','" . $start . "','" . $limit . "') ")->result();



        return $this->db->get()->result();
    }



    function get_cashout($store_id, $cashflow_category, $start_date, $end_date, $start, $limit)

    {

        if ($cashflow_category == "all") {

            $cashflow_category = "";
        }

        return $this->db->query("call w_CashRead(2, '" . $store_id . "','" . $cashflow_category . "','" . $start_date . "','" . $end_date . "','" . $start . "','" . $limit . "') ")->result();
    }



    function delete_cashin($store_id, $cash_in_id)

    {

        return $this->db->query("CALL w_CashDelete(1, '" . $store_id . "', '" . $cash_in_id . "'  )");
    }

    function insert_cashout($cashflow_title, $cashflow_category, $cashflow_amount, $store_id, $created_by)

    {

        return $this->db->query("call w_CashOutSave('" . $store_id . "','" . $cashflow_category . "', '" . $cashflow_title . "', '" . $cashflow_amount . "','" . $created_by . "' ) ");
    }

    function update_cashout($cash_in_id, $cashflow_title, $cashflow_category, $cashflow_amount, $store_id, $created_by)

    {

        return $this->db->query("call w_CashUpdate(2,'" . $store_id . "','" . $cash_in_id . "','" . $cashflow_category . "', '" . $cashflow_title . "', '" . $cashflow_amount . "' ) ");
    }

    function delete_cashout($store_id, $cash_in_id)

    {

        return $this->db->query("CALL w_CashDelete(2, '" . $store_id . "', '" . $cash_in_id . "'  )");
    }

    function approve_cashout($cash_in_id, $store_id, $user_id)

    {

        $data = array(

            'status_cash_out' => 1

        );



        $this->db->where('cash_out_id', $cash_in_id);

        $this->db->where('store_id', $store_id);

        return $this->db->update('cash_out', $data);
    }


    function get_settlement($fa,$store_id, $type_settlement, $user_id)

    {

     
        return $this->db->query("call w_settlementread($fa, '" . $store_id . "','" . $type_settlement . "','" . $user_id. "') ")->result();



        return $this->db->get()->result();
    }

    function get_salesretur_by_id($store_id, $transaction_id)

    {

        return $this->db->query("call t_ReturPenjualanRead(1,'" . $store_id . "','" . $transaction_id . "','','')")->row();
    }



    function get_salesretur_detail($store_id, $transaction_id)

    {

        return $this->db->query("call t_ReturPenjualanRead(2,'" . $store_id . "','" . $transaction_id . "','','')")->result();
    }



    function save_sales_retur($store_id, $transaction_id, $user_id)

    {

        return $this->db->query("call t_ReturPenjualanIns ('" . $transaction_id . "','" . $store_id . "','" . $user_id . "') ");
    }



    function update_sales_retur($store_id, $transaction_id, $status, $user_id)

    {

        return $this->db->query("call t_ReturPenjualanUpdateStatus ('" . $store_id . "','" . $transaction_id . "','" . $status . "','" . $user_id . "') ");
    }



    function approve_sales_retur($store_id, $transaction_id, $user_id)

    {

        return $this->db->query("call t_ReturPenjualanApprove ('" . $store_id . "','" . $transaction_id . "','" . $user_id . "') ");
    }

    function update_sales_retur_detail($store_id, $transaction_id, $user_id, $detail_id, $qty, $item_code)

    {

        return $this->db->query("call t_ReturPenjualanUpdateDetail ('" . $store_id . "', '" . $transaction_id . "','" . $detail_id . "','" . $item_code . "','" . $qty . "','" . $user_id . "') ");
    }
}