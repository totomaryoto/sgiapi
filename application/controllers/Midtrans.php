<?php

class Midtrans extends CI_Controller
{



  function charge()
  {

    $server_key = "SB-Mid-server-zuUrDXTb8J9r5Ep_ZqEtJ7Rs";

    $api_url = 'https://app.sandbox.midtrans.com/snap/v1/transactions';

    if (!strpos($_SERVER['REQUEST_URI'], '/charge')) {
      http_response_code(404);
      echo "wrong path, make sure it's `/charge`";
      exit();
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(404);
      echo "Page not found or wrong HTTP request method is used";
      exit();
    }
    $request_body = file_get_contents('php://input');
    header('Content-Type: application/json');
    $charge_result = $this->chargeAPI($api_url, $server_key, $request_body);
    http_response_code($charge_result['http_code']);
    echo $charge_result['body'];
  }
  function chargeAPI($api_url, $server_key, $request_body)
  {
    $ch = curl_init();
    $curl_options = array(
      CURLOPT_URL => $api_url,
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_POST => 1,
      CURLOPT_HEADER => 0,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode($server_key . ':')
      ),
      CURLOPT_POSTFIELDS => $request_body
    );
    curl_setopt_array($ch, $curl_options);
    $result = array(
      'body' => curl_exec($ch),
      'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
    );
    return $result;
  }


  function handler()
  {
    $json_result = file_get_contents('php://input');
    $data = json_decode($json_result, true);

    $order_id                 = $data['order_id'];
    $midtrans_transaction_id  = $data['transaction_id'];
    $payment_type             = $data['payment_type'];
    $status_code              = $data['status_code'];
    $transaction_status       = $data['transaction_status'];

    $va_bank = "";
    $va_number = "";
    $biller_code = "";
    $bill_key = "";

    if ($payment_type == "bank_transfer") {
      if (isset($data['permata_va_number'])) {
        $va_number = $data['permata_va_number'];
        $va_bank = "permata";
      } else {
        $va_number = $data['va_numbers'][0]['va_number'];
        $va_bank = $data['va_numbers'][0]['bank'];
      }
    } else if ($payment_type == "echannel") {
      $va_number = $data['biller_code'] . " - " . $data['bill_key'];
      $va_bank = "mandiri";
      $biller_code = $data['biller_code'];
      $bill_key = $data['bill_key'];
    }

    $data_update = array(
      "midtrans_transaction_id" => $midtrans_transaction_id,
      "payment_type" => $payment_type,
      "status_code" => $status_code,
      "midtrans_transaction_id" => $midtrans_transaction_id,
      "status_code" => $status_code,
      "va_number" => $va_number,
      "va_bank" => $va_bank,
      "biller_code" => $biller_code,
      "bill_key" => $bill_key,
    );

    $condition = array("midtrans_order_id" => $order_id);
    $this->db->where($condition);
    $this->db->update('transaction_header', $data_update);

    $judul = "";
    $title = "";

    $query = $this->db->query("select a.transaction_id,b.store_name,c.device_id,c.id  
  from transaction_header a inner join stores b on a.store_id = b.store_id 
  inner join users c on b.store_owner = c.id 
  where midtrans_order_id = '" . $order_id . "'
  ")->row();

    if ($transaction_status == "capture") {
      $title = "Transaksi Berhasil " . $query->transaction_id;
      $body = "Pembayaran untuk transaksi dengan nomor " . $order_id . " sudah di bayar.";
    } else if ($transaction_status == "expire") {
      $title = "Transaksi Hangus " . $query->transaction_id;
      $body = "Transaksi dengan nomor " . $query->transaction_id . " sudah hangus.";
      $this->test_notifikasi($title, $body, $query->device_id, $query->id);
    } else if ($transaction_status == "cancel") {
      $title = "Transaksi Di batalkan " . $query->transaction_id;
      $body = "Transaksi dengan nomor " . $query->transaction_id . " berhasil di batalkan.";
      $this->test_notifikasi($title, $body, $query->device_id, $query->id);
    } else if ($transaction_status == "settlement") {
      $title = "Transaksi Berhasil " . $query->transaction_id;
      $body = "Pembayaran untuk transaksi dengan nomor " . $query->transaction_id . " sudah di bayar.";
      $this->test_notifikasi($title, $body, $query->device_id, $query->id);
    } else if ($transaction_status == "pending") {
      $title = "Transaksi Di tagihkan " . $query->transaction_id;
      $body = "Transasksi dengan nomor " . $query->transaction_id . " menunggu pembayaran.";
      $this->test_notifikasi($title, $body, $query->device_id, $query->id);
    } else if ($transaction_status == "deny") {
      $title = "Transaksi Di tolak " . $query->transaction_id;
      $body = "Transasksi dengan nomor " . $query->transaction_id . " ditolak oleh pihak bank.";
      $this->test_notifikasi($title, $body, $query->device_id, $query->id);
    }

    $current_date =  date("Y-m-d H:i:s");
    $parameter = array(
      "type" => "sales",
      "transaction_id" => $query->transaction_id,
    );

    $this->db->query("INSERT INTO notification (title,body,notification_date,parameter,notification_to,read_status)
  values
  ('" . $title . "', '" . $body . "' , '" . $current_date . "','" . json_encode($parameter) . "','" . $query->id . "',0)");
  }

  function cek_status_pembayaran($orderId)
  {
    $this->load->library('veritrans');
    $params = array('server_key' => "SB-Mid-server-zuUrDXTb8J9r5Ep_ZqEtJ7Rs", "production" => false);
    $this->veritrans->config($params);
    $query = $this->db->query("SELECT midtrans_transaction_id from transaction_header where transaction_id = '" . $orderId . "' ")->row();
    $data = $this->veritrans->status($query->midtrans_transaction_id);


    $order_id                 = $data->order_id;
    $midtrans_transaction_id  = $data->transaction_id;
    $payment_type             = $data->payment_type;
    $status_code              = $data->status_code;

    $va_bank = "";
    $va_number = "";
    $biller_code = "";
    $bill_key = "";

    if ($payment_type == "bank_transfer") {
      if (isset($data->permata_va_number)) {
        $va_number = $data->permata_va_number;
        $va_bank = "permata";
      } else {
        $va_number = $data->va_numbers[0]->va_number;
        $va_bank = $data->va_numbers[0]->bank;
      }
    } else if ($payment_type == "echannel") {
      $va_number = $data->biller_code . " - " . $data->bill_key;
      $va_bank = "mandiri";
      $biller_code = $data->biller_code;
      $bill_key = $data->bill_key;
    }

    $data_update = array(
      "midtrans_transaction_id" => $midtrans_transaction_id,
      "payment_type" => str_replace("_", " ", ucfirst($payment_type)),
      "status_code" => $status_code,
      "midtrans_transaction_id" => $midtrans_transaction_id,
      "status_code" => $status_code,
      "va_number" => $va_number,
      "va_bank" => strtoupper($va_bank),
      "biller_code" => $biller_code,
      "bill_key" => $bill_key,
    );
    echo json_encode($data_update);
  }

  function test_notifikasi($title, $body, $device_id, $user_id)
  {

    $ch = curl_init();
    $msg = array(
      'body' => $body,
      'tag' => "TAG",
      'title' => $title,
      'icon' => 'myicon',
      'sound' => 'mySound',
      'priority' => 'high',
      'show_in_foreground' => True,
      'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
    );

    $other = array(
      'icon' => 'myicon',
      'sound' => 'mySound',
      'priority' => 'high',
      'show_in_foreground' => True,
      'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
      "screen" => "screenA",
      "sound" => "default",
      "status" => "done",
      "screen" => "MAINTENANCE_DETAIL",
      "extradata" => "aa"
    );

    $fields = array(
      'to' => $device_id,
      'notification' => $msg,
      'data' => $other
    );



    $headers = array(
      'Authorization: key=AAAAA_7KTUQ:APA91bE-CzBSbqrQL5_BSyPwLWjc7kHKZ7pMBHfQ95RHuePfW6Io4KQWLOuMiW4uaeoub4onHE9vMhIaAsVnXDqcEpeKWZR6uQ5OBxL1qAjxd5up3O5s01B6zLivijuO_r9ULmO12MSp',
      'Content-Type: application/json'
    );

    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    print_r($result);
    curl_close($ch);
  }
}