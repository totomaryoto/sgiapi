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



        $this->load->helper('url');
    }



    public function salesheader_get()

    {

        $store_id = $this->get("store_id");

        $start_date = $this->get("start_date");

        $end_date = $this->get("end_date");

        $query = $this->Report_model->get_report_sales($store_id, $start_date, $end_date);

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

        $store_id = $this->get("store_id");

        $start_date = $this->get("start_date");

        $end_date = $this->get("end_date");

        $payment_type = $this->get("payment_type");

        $query = $this->Report_model->get_report_sales_payment($store_id, $start_date, $end_date, $payment_type);

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

        $store_id = $this->get("store_id");

        $start_date = $this->get("start_date");

        $end_date = $this->get("end_date");

        $query = $this->Report_model->get_report_sales_margin($store_id, $start_date, $end_date);

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



        $query = $this->Report_model->get_financial_report($store_id, $date);

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

        $store_id = $this->get("store_id");

        $start_date = $this->get("start_date");

        $end_date = $this->get("end_date");

        $query = $this->Report_model->get_cashflow_report($store_id, $start_date, $end_date);

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



    function salesdaily_get()

    {

        $store_id = $this->get("store_id");

        $start_date = $this->get("start_date");

        $end_date = $this->get("end_date");

        $query = $this->Report_model->get_report_sales_daily($store_id, $start_date, $end_date);

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

    function salescashier_get()

    {

        $store_id = $this->get("store_id");

        $start_date = $this->get("start_date");

        $end_date = $this->get("end_date");

        $query = $this->Report_model->get_report_sales_cashier($store_id, $start_date, $end_date);

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

        $sheet->setCellValue('A1', 'Nama Toko');



        $sheet->setCellValue('B1', 'No Transaksi');

        $sheet->setCellValue('C1', 'Tanggal Transaksi');

        $sheet->setCellValue('D1', 'Tipe Pembayaran');

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

        $sheet = $spreadsheet->getActiveSheet();

        foreach ($sheet->getColumnIterator() as $column) {

            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        $rand_id = $this->generateRandomString();

        $file_name = base_url() . 'assets/export_report/' . $rand_id . ".xlsx";

        $fileath = './assets/export_report/' . $rand_id . ".xlsx";

        $save = $writer->save($fileath);

        $this->response([

            'status' => true,

            'file_path' => $file_name,

            'response' => $save

        ], 200);
    }

    function exportsalesmargin_post()

    {



        $json = file_get_contents('php://input');

        $obj = json_decode($json);



        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Nama Toko');

        $sheet->setCellValue('B1', 'Tanggal Transaksi');

        $sheet->setCellValue('C1', 'Kode Item');

        $sheet->setCellValue('D1', 'Nama Item');

        $sheet->setCellValue('E1', 'Tipe Item');

        $sheet->setCellValue('F1', 'Harga Modal');

        $sheet->setCellValue('G1', 'Harga Jual');

        $sheet->setCellValue('H1', 'Qty');

        $sheet->setCellValue('I1', 'Stok');

        $sheet->setCellValue('J1', 'Total Penjualan');

        $sheet->setCellValue('K1', 'Total Margin');





        $x = 1;

        foreach ($obj->salesmargindata as $jsonData) {

            $x++;

            $sheet->setCellValue('A' . $x, $jsonData->store_name);

            $sheet->setCellValue('B' . $x, $jsonData->trans_date);

            $sheet->setCellValue('C' . $x, $jsonData->item_code);

            $sheet->setCellValue('D' . $x, $jsonData->item_short_name);

            $sheet->setCellValue('E' . $x, $jsonData->item_type);

            $sheet->setCellValue('F' . $x, $jsonData->item_cost);

            $sheet->setCellValue('G' . $x, $jsonData->item_price);

            $sheet->setCellValue('H' . $x, $jsonData->total_qty);

            $sheet->setCellValue('I' . $x, $jsonData->total_cost);

            $sheet->setCellValue('J' . $x, $jsonData->total_sales);

            $sheet->setCellValue('K' . $x, $jsonData->total_margin);



            $sheet->getStyle('B' . $x)

                ->getNumberFormat()

                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);



            $spreadsheet->getActiveSheet()->getStyle('F' . $x)->getNumberFormat()->setFormatCode('_("Rp. "* #,##0.00_);_("Rp. "* \(#,##0.00\);_("Rp. "* "-"??_);_(@_)');

            $spreadsheet->getActiveSheet()->getStyle('G' . $x)->getNumberFormat()->setFormatCode('_("Rp. "* #,##0.00_);_("Rp. "* \(#,##0.00\);_("Rp. "* "-"??_);_(@_)');

            $spreadsheet->getActiveSheet()->getStyle('J' . $x)->getNumberFormat()->setFormatCode('_("Rp. "* #,##0.00_);_("Rp. "* \(#,##0.00\);_("Rp. "* "-"??_);_(@_)');

            $spreadsheet->getActiveSheet()->getStyle('K' . $x)->getNumberFormat()->setFormatCode('_("Rp. "* #,##0.00_);_("Rp. "* \(#,##0.00\);_("Rp. "* "-"??_);_(@_)');

            $spreadsheet->getActiveSheet()->getStyle('L' . $x)->getNumberFormat()->setFormatCode('_("Rp. "* #,##0.00_);_("Rp. "* \(#,##0.00\);_("Rp. "* "-"??_);_(@_)');
        }



        $sheet = $spreadsheet->getActiveSheet();

        foreach ($sheet->getColumnIterator() as $column) {

            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        $rand_id = "salesmargin_" . $this->generateRandomString();

        $file_name = base_url() . 'assets/export_report/' . $rand_id . ".xlsx";

        $fileath = './assets/export_report/' . $rand_id . ".xlsx";

        $save = $writer->save($fileath);









        $this->response([

            'status' => true,

            'file_path' => $file_name,

        ], 200);
    }



    function exportsalesdaily_post()

    {



        $json = file_get_contents('php://input');

        $obj = json_decode($json);



        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Nama Toko');

        $sheet->setCellValue('B1', 'Tanggal Transaksi');

        $sheet->setCellValue('C1', 'Grand Total');

        $x = 1;

        foreach ($obj->salesdaily as $jsonData) {

            $x++;

            $sheet->setCellValue('A' . $x, $jsonData->store_name);

            $sheet->setCellValue('B' . $x, $jsonData->trans_date);



            $sheet->setCellValue('C' . $x, $jsonData->grand_total);



            $sheet->getStyle('B' . $x)

                ->getNumberFormat()

                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);



            $spreadsheet->getActiveSheet()->getStyle('C' . $x)->getNumberFormat()->setFormatCode('_("Rp. "* #,##0.00_);_("Rp. "* \(#,##0.00\);_("Rp. "* "-"??_);_(@_)');
        }



        $sheet = $spreadsheet->getActiveSheet();

        foreach ($sheet->getColumnIterator() as $column) {

            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        $rand_id = "salesdaily_" . $this->generateRandomString();

        $file_name = base_url() . 'assets/export_report/' . $rand_id . ".xlsx";

        $fileath = './assets/export_report/' . $rand_id . ".xlsx";

        $save = $writer->save($fileath);



        $this->response([

            'status' => true,

            'file_path' => $file_name,

        ], 200);
    }



    function exportsalespayment_post()

    {



        $json = file_get_contents('php://input');

        $obj = json_decode($json);



        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Nama Toko');

        $sheet->setCellValue('B1', 'Tanggal Transaksi');

        $sheet->setCellValue('C1', 'Tipe Pembayaran');



        $sheet->setCellValue('D1', 'Grand Total');

        $x = 1;

        foreach ($obj->salespayment as $jsonData) {

            $x++;

            $sheet->setCellValue('A' . $x, $jsonData->store_name);

            $sheet->setCellValue('B' . $x, $jsonData->trans_date);

            $sheet->setCellValue('C' . $x, $jsonData->payment_type_name);

            $sheet->setCellValue('D' . $x, $jsonData->grand_total);

            $sheet->getStyle('B' . $x)

                ->getNumberFormat()

                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);

            $spreadsheet->getActiveSheet()->getStyle('D' . $x)->getNumberFormat()->setFormatCode('_("Rp. "* #,##0.00_);_("Rp. "* \(#,##0.00\);_("Rp. "* "-"??_);_(@_)');
        }



        $sheet = $spreadsheet->getActiveSheet();

        foreach ($sheet->getColumnIterator() as $column) {

            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        $rand_id = "salespayment_" . $this->generateRandomString();

        $file_name = base_url() . 'assets/export_report/' . $rand_id . ".xlsx";

        $fileath = './assets/export_report/' . $rand_id . ".xlsx";

        $save = $writer->save($fileath);



        $this->response([

            'status' => true,

            'file_path' => $file_name,

        ], 200);
    }



    function exportsalescashier_post()

    {



        $json = file_get_contents('php://input');

        $obj = json_decode($json);



        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Nama Toko');

        $sheet->setCellValue('B1', 'Tanggal Transaksi');

        $sheet->setCellValue('C1', 'Kasir');



        $sheet->setCellValue('D1', 'Grand Total');

        $x = 1;

        foreach ($obj->salescashier as $jsonData) {

            $x++;

            $sheet->setCellValue('A' . $x, $jsonData->store_name);

            $sheet->setCellValue('B' . $x, $jsonData->trans_date);

            $sheet->setCellValue('C' . $x, $jsonData->fullname);

            $sheet->setCellValue('D' . $x, $jsonData->grand_total);

            $sheet->getStyle('B' . $x)

                ->getNumberFormat()

                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);

            $spreadsheet->getActiveSheet()->getStyle('D' . $x)->getNumberFormat()->setFormatCode('_("Rp. "* #,##0.00_);_("Rp. "* \(#,##0.00\);_("Rp. "* "-"??_);_(@_)');
        }



        $sheet = $spreadsheet->getActiveSheet();

        foreach ($sheet->getColumnIterator() as $column) {

            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        $rand_id = "salescashier_" . $this->generateRandomString();

        $file_name = base_url() . 'assets/export_report/' . $rand_id . ".xlsx";

        $fileath = './assets/export_report/' . $rand_id . ".xlsx";

        $save = $writer->save($fileath);



        $this->response([

            'status' => true,

            'file_path' => $file_name,

        ], 200);
    }

    function exportfinancial_post()

    {



        $json = file_get_contents('php://input');

        $obj = json_decode($json);



        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Nama Toko');

        $sheet->setCellValue('B1', 'Bulan');

        $sheet->setCellValue('C1', 'Tahun');

        $sheet->setCellValue('D1', 'Kategori');

        $sheet->setCellValue('E1', 'Amount');

        $sheet->setCellValue('F1', 'Keterangan');



        $x = 1;

        foreach ($obj->financial as $jsonData) {

            $x++;

            $sheet->setCellValue('A' . $x, $jsonData->store_name);

            $sheet->setCellValue('B' . $x, $jsonData->month_date);

            $sheet->setCellValue('C' . $x, $jsonData->year_date);

            $sheet->setCellValue('D' . $x, $jsonData->category);

            $sheet->setCellValue('E' . $x, $jsonData->amount);

            $sheet->setCellValue('F' . $x, $jsonData->keterangan);



            $spreadsheet->getActiveSheet()->getStyle('E' . $x)->getNumberFormat()->setFormatCode('_("Rp. "* #,##0.00_);_("Rp. "* \(#,##0.00\);_("Rp. "* "-"??_);_(@_)');
        }



        $sheet = $spreadsheet->getActiveSheet();

        foreach ($sheet->getColumnIterator() as $column) {

            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        $rand_id = "keuangan_" . $this->generateRandomString();

        $file_name = base_url() . 'assets/export_report/' . $rand_id . ".xlsx";

        $fileath = './assets/export_report/' . $rand_id . ".xlsx";

        $save = $writer->save($fileath);



        $this->response([

            'status' => true,

            'file_path' => $file_name,

        ], 200);
    }



    function exportcashin_post()

    {



        $json = file_get_contents('php://input');

        $obj = json_decode($json);



        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');

        $sheet->setCellValue('B1', 'Nama Toko');

        $sheet->setCellValue('C1', 'Kategori');

        $sheet->setCellValue('D1', 'Deskripsi');

        $sheet->setCellValue('E1', 'Tanggal');

        $sheet->setCellValue('F1', 'Amount');

        $urut = 0;

        $x = 1;

        foreach ($obj->cashin as $jsonData) {

            $x++;

            $urut++;

            $sheet->setCellValue('A' . $x, $urut);

            $sheet->setCellValue('B' . $x, $jsonData->store_name ?? '');

            $sheet->setCellValue('C' . $x, $jsonData->cash_category_name);

            $sheet->setCellValue('D' . $x, $jsonData->cash_in_title);

            $sheet->setCellValue('E' . $x, $jsonData->cash_in_date);

            $sheet->setCellValue('F' . $x, $jsonData->amount_in);





            $sheet->getStyle('E' . $x)

                ->getNumberFormat()

                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);



            $spreadsheet->getActiveSheet()->getStyle('F' . $x)->getNumberFormat()->setFormatCode('_("Rp. "* #,##0.00_);_("Rp. "* \(#,##0.00\);_("Rp. "* "-"??_);_(@_)');
        }



        $sheet = $spreadsheet->getActiveSheet();

        foreach ($sheet->getColumnIterator() as $column) {

            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        $rand_id = "cashin_" . $this->generateRandomString();

        $file_name = base_url() . 'assets/export_report/' . $rand_id . ".xlsx";

        $fileath = './assets/export_report/' . $rand_id . ".xlsx";

        $save = $writer->save($fileath);



        $this->response([

            'status' => true,

            'file_path' => $file_name,

        ], 200);
    }



    function exportcashout_post()

    {



        $json = file_get_contents('php://input');

        $obj = json_decode($json);



        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');

        $sheet->setCellValue('B1', 'Nama Toko');

        $sheet->setCellValue('C1', 'Kategori');

        $sheet->setCellValue('D1', 'Deskripsi');

        $sheet->setCellValue('E1', 'Tanggal');

        $sheet->setCellValue('F1', 'Amount');

        $urut = 0;

        $x = 1;

        foreach ($obj->cashout as $jsonData) {

            $x++;

            $urut++;

            $sheet->setCellValue('A' . $x, $urut);

            $sheet->setCellValue('B' . $x, $jsonData->store_name ?? '');

            $sheet->setCellValue('C' . $x, $jsonData->cash_category_name);

            $sheet->setCellValue('D' . $x, $jsonData->cash_out_title);

            $sheet->setCellValue('E' . $x, $jsonData->cash_out_date);

            $sheet->setCellValue('F' . $x, $jsonData->amount_out);





            $sheet->getStyle('E' . $x)

                ->getNumberFormat()

                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);



            $spreadsheet->getActiveSheet()->getStyle('F' . $x)->getNumberFormat()->setFormatCode('_("Rp. "* #,##0.00_);_("Rp. "* \(#,##0.00\);_("Rp. "* "-"??_);_(@_)');
        }



        $sheet = $spreadsheet->getActiveSheet();

        foreach ($sheet->getColumnIterator() as $column) {

            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        $rand_id = "cashout_" . $this->generateRandomString();

        $file_name = base_url() . 'assets/export_report/' . $rand_id . ".xlsx";

        $fileath = './assets/export_report/' . $rand_id . ".xlsx";

        $save = $writer->save($fileath);



        $this->response([

            'status' => true,

            'file_path' => $file_name,

        ], 200);
    }

    function exportcashflow_post()

    {



        $json = file_get_contents('php://input');

        $obj = json_decode($json);





        // {

        //     "store_name": "-",

        //     "category": "BAYAR SEWA TEMPAT",

        //     "title": "Bulan desember",

        //     "amount_in": "0.00",

        //     "amount_out": "1000000.00"

        //   }



        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');

        $sheet->setCellValue('B1', 'Nama Toko');

        $sheet->setCellValue('C1', 'Kategori');

        $sheet->setCellValue('D1', 'Deskripsi');

        $sheet->setCellValue('E1', 'Uang Masuk');

        $sheet->setCellValue('F1', 'Uang Keluar');

        $urut = 0;

        $x = 1;

        foreach ($obj->cashflow as $jsonData) {

            $x++;

            $urut++;

            $sheet->setCellValue('A' . $x, $urut);

            $sheet->setCellValue('B' . $x, $jsonData->store_name ?? '');

            $sheet->setCellValue('C' . $x, $jsonData->category);

            $sheet->setCellValue('D' . $x, $jsonData->title);

            $sheet->setCellValue('E' . $x, $jsonData->amount_in);

            $sheet->setCellValue('F' . $x, $jsonData->amount_out);





            // $sheet->getStyle('E' . $x)

            //     ->getNumberFormat()

            //     ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);

            $spreadsheet->getActiveSheet()->getStyle('E' . $x)->getNumberFormat()->setFormatCode('_("Rp. "* #,##0.00_);_("Rp. "* \(#,##0.00\);_("Rp. "* "-"??_);_(@_)');

            $spreadsheet->getActiveSheet()->getStyle('F' . $x)->getNumberFormat()->setFormatCode('_("Rp. "* #,##0.00_);_("Rp. "* \(#,##0.00\);_("Rp. "* "-"??_);_(@_)');
        }



        $sheet = $spreadsheet->getActiveSheet();

        foreach ($sheet->getColumnIterator() as $column) {

            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        $rand_id = "cashflow_" . $this->generateRandomString();

        $file_name = base_url() . 'assets/export_report/' . $rand_id . ".xlsx";

        $fileath = './assets/export_report/' . $rand_id . ".xlsx";

        $save = $writer->save($fileath);



        $this->response([

            'status' => true,

            'file_path' => $file_name,

        ], 200);
    }

    function stockmovement_get()

    {

        $store_id       = $this->get("store_id");

        $start_date     = $this->get("start_date");

        $end_date       = $this->get("end_date");

        $item_code      = $this->get("item_code");

        $start          = $this->get("start");

        $limit          = $this->get("limit");



        $query = $this->Report_model->get_movementstock_report($store_id, $item_code, $start_date, $end_date, $start, $limit);

        if ($query) {

            if ($query->num_rows() < 1) {

                $this->response([

                    'status' => false,
                    'data' => [],


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