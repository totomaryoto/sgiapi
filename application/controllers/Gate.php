<?php

require_once './vendor/autoload.php';

use chriskacerguis\RestServer\RestController;

class Gate extends RestController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Gate_model");
        $this->load->library('encryption');
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
    }


    public function register_post()
    {

        $UserFullName = $this->input->post("UserFullName");
        $UserNickName = $this->input->post("UserNickName");
        $UserPassword = $this->input->post("UserPassword");
        $UserEmail = $this->input->post("UserEmail");
        $UserPhone1 = $this->input->post("UserPhone1");
        $UserPhone2 = $this->input->post("UserPhone2");
        $LanguageCode = $this->input->post("LanguageCode");

        $data['regis']   = $this->db->query("CALL APPS_sp_user_register(
            '" . $UserFullName . "',
            '" . $UserNickName . "',
            '" . $UserPassword . "',
            '" . $UserEmail . "',
            '" . $UserPhone1 . "',
            '" . $UserPhone2 . "',
            " . $LanguageCode . "
            )")->row();

        $UserCode = $data['regis']->UserCode;
        $Status = $data['regis']->statusTrans;


        if ($Status == 1) {


            // $this->sendConfirmationEmail($UserCode);
            $this->response([
                'status' => true,
                'data' =>  $data,
            ], 200);
        }


        // echo json_encode($data->OTPCode);


    }

    public function sendConfirmationEmail_get($id)
    {

        $this->load->library("MyPHPMailer");
        $datauser = $this->db->query("SELECT UserEmail from Users where UserCode = '" . $id . "' ")->row();
        echo json_encode($datauser);


        // $mail = new PHPMailer();
        // $mail->IsHTML(true);
        // $mail->IsSMTP();
        // $mail->SMTPOptions = array(
        //     'ssl' => array(
        //         'verify_peer' => false,
        //         'verify_peer_name' => false,
        //         'allow_self_signed' => true
        //     )
        // );

        // $mail->Host     = 'smtp.gmail.com';
        // $mail->Username = 'mooleh.developer@gmail.com';
        // $mail->Password = 'M00l3hD3VB4yar!';
        // $mail->SMTPSecure = 'tls';
        // $mail->Port     = 587;
        // $mail->setFrom('mooleh.developer@gmail.com');
        // $mail->addAddress($datauser->appUserEmail);


        // $mail->Subject = 'Email confirmation. mooleh.com';
        // $data['url'] = base_url() . "Api/aktivasi/" . $id;
        // $body        = "Hallo, " . $datauser->appUserEmail . "<br> <br>Click here to activate your account <a href='" . base_url() . "Gate/aktivasi/" . $id . "'> AKTIVASI AKUN </a>";
        // $mail->Body = $body;
        // $mail->send();
    }

    public function aktivasi_get($id)
    {
        $this->db->query("UPDATE Users set UserStatus = 1,ModifiedDate=NOW() where UserCode = '" . $id . "' ");
        echo ("Aktifasi akun berhasil");
    }

    public function generate_token($user_data)
    {
        $tokenData = array();
        $issuedat_claim = time();

        $tokenData['iss'] =  "POSAKU_API";
        $tokenData['iat'] =  $issuedat_claim;
        $tokenData['data']['id'] = $user_data->UserCode;
        $tokenData['data']['email'] = $user_data->UserEmail;
        // $tokenData['data']['device_id'] = $user_data->device_id;
        $tokenData['data']['phone'] = $user_data->UserPhone1;
        $tokenData['data']['user_image'] = $user_data->user_photo;
        // $tokenData['data']['store_id'] = $user_data->store_id;
        // $tokenData['data']['store_name'] = $user_data->store_name;
        // $tokenData['data']['store_address'] = $user_data->store_address;
        // $tokenData['data']['user_type'] = $user_data->user_type;
        return AUTHORIZATION::generateToken($tokenData);
    }

    public function validatesession_get()
    {
        $token = $this->get("token");
        $decodedToken = AUTHORIZATION::validateToken($token);
        if ($decodedToken == false) {
            $this->response([
                'status' => false,
            ], 401);
            return;
        }
    }

    public function login_post()
    {
        $email      = $this->input->post("email");
        $password   = $this->input->post("password");
        $row_count =  $this->Gate_model->row_count(array("UserEmail" => $email));

        if ($row_count < 1) {
            $this->response([
                'status' => false,
                'message' =>  "Email tidak di temukan",
            ], 404);
        } else {
            $user2 = $this->Gate_model->get_password(array("UserEmail" => $email));
            $save_password = $user2->UserPassword;
            if (SHA1($password) ==  $save_password) {
                $user = $this->db->query("SELECT UserCode,UserFullName,UserEmail,LanguageCode,
                case when LanguageCode=1 then 'Indonesia' else 'English' end as LanguageName,PointTotal
                 from Users where UserEmail='" . $email . "'")->row();

                mysqli_next_result($this->db->conn_id);

                $token = $this->generate_token($user);
                $data = array(
                    'Token' =>  $token,
                    'UserCode'  => $user->UserCode
                );
                // $this->db->replace('Token', $data);

                $this->db->query("Update Users set Token ='" . $token . "' where UserCode='" . $user->UserCode . "' ");

                $this->response([
                    'status' => true,
                    'message' =>   $token,
                    'data' => $user,
                ], 200);
            } else {
                $this->response([
                    'status' => false,
                    'message' =>  "Password Salah di temukan",
                    'dataemail' =>  $email,
                ], 404);
            }
        }
    }

    public function password_post()
    {
        $phone = $this->input->post("phone");
        $row_count =  $this->Gate_model->row_count(array("phone" => $phone));
        if ($row_count < 1) {
            $this->response([
                'status' => false,
                'message' =>  "No telp tidak di temukan",
            ], 404);
        } else {
            $this->response([
                'status' => true,
            ], 200);
        }
    }

    public function validatetoken_get()
    {
        $headers = $this->input->request_headers();

        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($headers['Authorization']);
            if ($decodedToken != false) {
                $this->response([
                    'status' => true,
                    'data' => $decodedToken,
                ], 200);
                return;
            }
        }
        $this->response([
            'status' => true,
        ], 401);
    }


    function generatepassword_post()
    {
        $phone      = $this->input->post("phone");
        $password   = $this->encryption->encrypt($this->input->post("password"));

        $update = $this->db->query("UPDATE users set password = '" . $password . "' where phone = '" . $phone . "'  ");
        if ($update) {
            $this->response([
                'status' => true,
            ], 200);
        } else {
            $this->response([
                'status' => false,
                'message' => "Woops, harap coba kembali",
            ], 502);
        }
    }

    function deviceid_post()
    {
        $user_id    = $this->input->post("userId");
        $token      = $this->input->post("token");
        $this->db->query("UPDATE users set device_id = '" . $token . "' where id = '" . $user_id . "'  ");
    }

    function notification_get()
    {
        $ch = curl_init();
        $title = "JUDUL";
        $body = "BODY";

        $msg = array(
            'body' => $body,
            'tag' => "TAG",
            'title' => $title,
            'sound' => 'mySound',
            'priority' => 'high',
            'show_in_foreground' => True,
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
        );


        $fields = array(
            "to" => 'dFhnwmbuQ0-LOh69L1gW5y:APA91bGK6w8DErdi3rDfHWCutXya4Yt2UJfJes9vk9kAMx_CS5-rV0A5r5vZ9JEGkYEX-CXEGTfcfSLZid_09L0sB75AbwxfAhcsEHj8JNvacZRWjcXr4MaHd1ZiJJvEQFQc84oyuKTj',
            'notification' => $msg,
            'data' => array(
                "page_to_open" => "PAGE TO OPEN"
            ),
        );



        $headers = array(
            'Authorization: key=AAAAdq90f9A:APA91bGm6b26MVCAAmZDFFOk34qy0Wjwj5fBSduAUYuC4dR2Aj1YZI1vKiKTeczBVfho9TXq994eFYN21_lVxvas0Bqjv1SIhEvYw-zFj0o7Ks_CaRYSekPwo0WIVd4m1rJ91-Jyj8UM',
            'Content-Type: application/json'
        );

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);

        curl_close($ch);
    }
}
