<?php

use chriskacerguis\RestServer\RestController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Report extends RestController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Master_model");
        $this->load->model("Report_model");
        header("Access-Control-Allow-Origin: *");
    }

    public function storelist_get()
    {
        $user_id = $this->get("user_id");
        $user = $this->db->query("
        SELECT '001' company_id, '001' company_name, '001' store_id, 'Semua Toko' store_name
        UNION ALL
        select b.company_id,b.company_name,st.store_id,st.store_name from user_company a
        inner join company b on (a.company_id=b.company_id)
        inner join stores st on (b.company_id=st.company_id)
        inner join users us on (a.user_id=us.id)
        where a.user_id='" . $user_id . "' order by company_name asc")->result();
        $this->response([
            'status' => false,
            'data' => $user
        ], 200);
    }

    public function salesheader_get()
    {
        $user_id = $this->get("user_id");

        $store_id = $this->get("store_id");
        $start_date = $this->get("start_date");
        $end_date = $this->get("end_date");
        $query = $this->Report_model->manager_get_report_sales($user_id, $store_id, $start_date, $end_date);
        if ($query) {
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
        } else {
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
            ], 502);
        }
    }

    public function salesheaderbypayment_get()
    {
        $user_id = $this->get("user_id");

        $store_id = $this->get("store_id");
        $start_date = $this->get("start_date");
        $end_date = $this->get("end_date");
        $payment_type = $this->get("payment_type");
        $query = $this->Report_model->manager_get_report_sales_payment($user_id, $store_id, $start_date, $end_date, $payment_type);
        if ($query) {
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
        } else {
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
            ], 502);
        }
    }

    public function salesmargin_get()
    {
        $user_id = $this->get("user_id");

        $store_id = $this->get("store_id");
        $start_date = $this->get("start_date");
        $end_date = $this->get("end_date");
        $query = $this->Report_model->manger_get_report_sales_margin($user_id, $store_id, $start_date, $end_date);
        if ($query) {
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
        } else {
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
            ], 502);
        }
    }

    // UNTUK NOTIFIKASI
    public function sales_get()
    {
        $store_id = $this->get("store_id");
        $transaction_id = $this->get("transaction_id");
        $query = $this->db->query("SELECT
        midtrans_transaction_id,e.description status_code,a.transaction_id, DATE_FORMAT(transaction_date, '%m/%d/%Y %H:%i') transaction_date, a.grand_total, a.store_id, partner_type partner_type_id,
        b.partner_type_name partner_type, count(c.id) total_row,IFNULL(partner_name , '') partner_name,payment_type,va_number,va_bank,biller_code,bill_key  FROM `transaction_header` a
        left join partner_type b on a.partner_type = b.id
		inner join transaction_detail c on a.transaction_id = c.transaction_id
        left join partners d on a.partner_code = d.partner_code and a.store_id = d.store_id
        left join midtrans_status_code e on a.status_code = e.id
        where  a.store_id = '" . $store_id . "' and a.transaction_id = '" . $transaction_id . "'
        group by e.description,a.transaction_id,a.transaction_date, a.grand_total, a.store_id,a.partner_type,
        b.partner_type_name, c.transaction_id,
        partner_name,payment_type,va_number,va_bank,biller_code,bill_key
        order by transaction_date asc
        ");
        if ($query) {
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
        } else {
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
            ], 502);
        }
    }

    public function salesdetail_get()
    {
        $store_id = $this->get("store_id");
        $transaction_id = $this->get("transaction_id");

        $query = $this->db->query("SELECT a.*, b.item_name,category_name from transaction_detail a
                                    inner join transaction_header header on header.transaction_id = a.transaction_id
                                    inner join items b on a.item_code = b.item_code and  b.store_id = '" . $store_id . "'
                                    inner join category c on b.item_category_id = c.category_id and c.store_id = '" . $store_id . "'
                                    where a.transaction_id = '" . $transaction_id . "' and header.store_id = '" . $store_id . "'
                                    ");

        if ($query) {
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
        } else {
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
            ], 502);
        }
    }

    function financial_get()
    {
        $store_id       = $this->get("store_id");
        $date           = $this->get("date");
        $user_id = $this->get("user_id");

        $query = $this->Report_model->manager_get_financial_report($user_id, $store_id, $date);
        if ($query) {
            if ($query->num_rows() > 0) {
                $this->response([
                    'status' => true,
                    'data' => $query->result(),
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'data' => [],
                ], 200);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
            ], 502);
        }
    }
    function cashflow_get()
    {
        $user_id = $this->get("user_id");
        $store_id = $this->get("store_id");
        $start_date = $this->get("start_date");
        $end_date = $this->get("end_date");
        $query = $this->Report_model->manager_get_cashflow_report($user_id, $store_id, $start_date, $end_date);
        if ($query) {
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
        } else {
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
            ], 502);
        }
    }
    function exportsales_post()
    {

        $json = file_get_contents('php://input');
        $obj = json_decode($json);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Store');

        $sheet->setCellValue('B1', 'Transaction Id');
        $sheet->setCellValue('C1', 'Transaction Date');
        $sheet->setCellValue('D1', 'Payment Type');
        $sheet->setCellValue('E1', 'Grand Total');
        $x = 1;
        foreach ($obj->salesdata as $jsonData) {
            $x++;
            $sheet->setCellValue('A' . $x, $jsonData->store_name);
            $sheet->setCellValue('B' . $x, $jsonData->transaction_id);
            $sheet->setCellValue('C' . $x, $jsonData->trans_date);
            $sheet->setCellValue('D' . $x, $jsonData->payment_type_name);
            $sheet->setCellValue('E' . $x, (int)$jsonData->grand_total);
            $sheet->getStyle('C' . $x)
                ->getNumberFormat()
                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
            $spreadsheet->getActiveSheet()->getStyle('E' . $x)->getNumberFormat()->setFormatCode('_("Rp. "* #,##0.00_);_("Rp. "* \(#,##0.00\);_("Rp. "* "-"??_);_(@_)');
        }

        $writer = new Xlsx($spreadsheet);
        $filename = './assets/export_report/' . $this->generateRandomString() . ".xlsx";
        $writer->save($filename);
    }

    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function searchsalesheader_get()
    {
        $store_id       = $this->get("store_id");
        $transaction_id = $this->get("transaction_id");
        if ($transaction_id == null) {
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
            ], 502);
        }
        $query = $this->Report_model->get_sales_by_id($store_id, $transaction_id);
        if ($query) {
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
        } else {
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
            ], 502);
        }
    }

    function searchsalesdetail_get()
    {
        $store_id       = $this->get("store_id");
        $transaction_id = $this->get("transaction_id");
        if ($transaction_id == null) {
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
            ], 502);
        }
        $query = $this->Report_model->get_salesdetail_by_id($store_id, $transaction_id);
        if ($query) {
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
        } else {
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
            ], 502);
        }
    }
}