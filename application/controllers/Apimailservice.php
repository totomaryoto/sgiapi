<?php

use chriskacerguis\RestServer\RestController;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'C:\xampp\composer\vendor\autoload.php';

class Apimailservice extends RestController
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        die("It works");
    }

    function sendemailmooleh_get($otp)
    {



        $this->load->library("MyPhpMailer");
        $otp = $this->input->get('otp');
        $email = $this->input->get('email');

        // require 'PHPMailerAutoload.php';
        $datauser = $this->db->query("SELECT UserEmail from Users where UserOTP = '4793' ")->row();

        $mail = new PHPMailer;
        $mail->IsSMTP();
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->Host     = 'mail.mooleh.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'verifikasi@mooleh.com';
        $mail->Password = 'm00lehmail!';
        $mail->SMTPSecure = 'none';
        $mail->Port     = 587;
        $mail->setFrom('verifikasi@mooleh.com');
        $mail->Subject = "Pemberitahuan Email dari Website"; //subyek email
        $mail->addAddress($datauser->UserEmail);
        $body        = "Selamat Pendaftaran anda berhasil , silahkan klik link ini untuk aktivasi, '" . base_url() . "Apimailservice/aktivasi/" . $otp . "' ";


        $mail->Body = $body;

        // if ($mail->Send()) echo "Message has been sent";
        // else echo "Failed to sending message";


        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
    }







    function aktivasi_get($id)
    {
        $this->db->query("UPDATE Users set UserStatus = 1,ModifiedDate=NOW() where UserCode = '" . $id . "' ");
        // echo ("Aktifasi akun berhasil");

        die("<script>
		
                alert('Aktivasi akun berhasil,Silahkan Login');
		
               window.location.href='" . 'https://' . "mooleh.com';
               </script>");
    }
}
