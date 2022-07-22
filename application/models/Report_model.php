<?php



class Report_model extends CI_Model

{



    public function manager_get_report_sales($user_id, $store_id, $start_date, $end_date)

    {

        //         CREATE DEFINER=`aroomitc_user`@`%` PROCEDURE `w_TransactionSalesReportPerCompany`(p_fa int,p_store_id varchar(64)

        // ,p_date_from DATE,p_date_to DATE,p_user_id VARCHAR(64))

        return $this->db->query("call w_TransactionSalesReportPerCompany (3,'" . $store_id . "','" . $start_date . "', '" . $end_date . "', '" . $user_id . "')");
    }



    public function manager_get_report_sales_payment($user_id, $store_id, $start_date, $end_date)

    {

        return $this->db->query("call w_TransactionSalesReportPerCompany (2,'" . $store_id . "','" . $start_date . "', '" . $end_date . "', '" . $user_id . "')");
    }

    public function manger_get_report_sales_margin($user_id, $store_id, $start_date, $end_date)

    {

        return $this->db->query("call w_TransactionSalesReportPerCompany (4,'" . $store_id . "','" . $start_date . "', '" . $end_date . "', '" . $user_id . "')");
    }

    public function manager_get_financial_report($user_id, $store_id, $date)

    {

        return $this->db->query("call w_LaporanKeuanganPerCompany(1,'" . $store_id . "','" . $date . "', '" . $user_id . "' )");
    }



    function manager_get_cashin($user_id, $store_id, $cashflow_category, $start_date, $end_date, $start, $limit)

    {

        if ($cashflow_category == "all") {

            $cashflow_category = "";
        }

        return $this->db->query("call w_CashRead(1, '" . $store_id . "','" . $cashflow_category . "','" . $start_date . "','" . $end_date . "','" . $start . "','" . $limit . "') ")->result();



        return $this->db->get()->result();
    }

    public function manager_get_cashflow_report($user_id, $store_id, $start_date, $end_date)

    {

        return $this->db->query("call w_CashReadPerCompany(3, '" . $store_id . "','1','" . $start_date . "','" . $end_date . "','0','0','" . $user_id . "') ");
    }

    //

    public function get_report_sales_daily($store_id, $start_date, $end_date)

    {

        return $this->db->query("call w_TransactionSalesReport (1,'" . $store_id . "','" . $start_date . "', '" . $end_date . "', '')");
    }

    public function get_report_sales_cashier($store_id, $start_date, $end_date)

    {

        return $this->db->query("call w_TransactionSalesReport (5,'" . $store_id . "','" . $start_date . "', '" . $end_date . "', '')");
    }

    public function get_report_sales($store_id, $start_date, $end_date)

    {

        return $this->db->query("call w_TransactionSalesReport (3,'" . $store_id . "','" . $start_date . "', '" . $end_date . "', '')");
    }

    public function get_report_sales_payment($store_id, $start_date, $end_date)

    {

        return $this->db->query("call w_TransactionSalesReport (2,'" . $store_id . "','" . $start_date . "', '" . $end_date . "', '')");
    }

    public function get_report_sales_margin($store_id, $start_date, $end_date)

    {

        return $this->db->query("call w_TransactionSalesReport (4,'" . $store_id . "','" . $start_date . "', '" . $end_date . "', '')");
    }

    public function get_financial_report($store_id, $date)

    {

        return $this->db->query("call w_LaporanKeuangan(1,'" . $store_id . "','" . $date . "')");
    }

    public function get_cashflow_report($store_id, $start_date, $end_date)

    {

        return $this->db->query("call w_CashRead(3, '" . $store_id . "','1','" . $start_date . "','" . $end_date . "','0','0') ");
    }

    public function get_sales_by_id($store_id, $transaction_id)

    {

        return $this->db->query("select  DATE(a.transaction_date) as trans_date,a.transaction_id,c.payment_type_name,a.grand_total,pt.partner_type_name ,st.store_name

		from transaction_header a

			inner join transaction_payment b on (a.transaction_id=b.transaction_id)

			inner join payment_type c on (b.payment_type_id=c.payment_type_id)

			inner join partner_type pt on (a.partner_type=pt.id)

			inner join stores st on (a.store_id=st.store_id)

        WHERE a.store_id ='" . $store_id . "' and a.transaction_id like '%" . $transaction_id . "%' ");
    }



    public function get_salesdetail_by_id($store_id, $transaction_id)

    {

        return $this->db->query("select  DATE(a.transaction_date) as trans_date,b.item_code,UPPER(b.item_short_name)AS item_short_name,item_type,item_cost,item_price,

        sum(qty)as total_qty,sum(b.item_cost*qty) as total_cost,sum(b.grand_total) as total_sales,

        sum(b.grand_total-(b.item_cost*qty)) as total_margin,st.store_name from transaction_header a

        inner join transaction_detail b on (a.store_id=b.store_id and a.transaction_id=b.transaction_id)

        inner join stores st on (a.store_id=st.store_id)

        WHERE a.transaction_id = '" . $transaction_id . "' and a.store_id = '" . $store_id . "'

        group by DATE(a.transaction_date) ,b.item_code,item_cost,item_price,item_type,st.store_name");
    }

    public function get_movementstock_report($store_id, $item_code, $start_date, $end_date, $start, $limit)

    {

        return $this->db->query("call w_StockCardRead(1, '" . $store_id . "','" . $item_code . "','" . $start_date . "','" . $end_date . "','" . $start . "','" . $limit . "') ");
    }
}