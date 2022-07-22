<?php
use chriskacerguis\RestServer\RestController;

class Admin extends RestController {

    function __construct()
    {
        parent::__construct();
        $this->load->model("Master_model");
        header('Access-Control-Allow-Origin: *');

    }

    function users_get(){
       $start = $this->get("start");
       $limit = $this->get("limit");

            $query = $this->db->query("SELECT a.fullname,a.id,a.email,a.phone,b.valid_from,b.valid_to,b.subscription_id,subscription_name FROM users a inner join (
                select d.*
                from (
                    select d.*, row_number() over(partition by user_id order by id desc) rn
                    from user_subscriptions d
                ) d
                where rn = 1
            ) b on a.id = b.user_id
            inner join subscriptions c on b.subscription_id = c.id");
        $this->response([
            'status' => true,
            'data' =>$query->result(),
        ], 200);
    }

    function partnerstype_get(){
            $query = $this->db->query("select * from partner_type order by partner_type_name asc");
            if($query->num_rows() < 1){
                $this->response([
                    'status' => false,
                ], 200);
            }else{
            $this->response([
                'status' => true,
                'data' =>$query->result(),
            ], 200);
        }
    }

    function partnerstype_post(){

        $partner_type_name = $this->input->post("partner_type_name");
       
        $query = $this->db->query("select * from partner_type where partner_type_name = '".$partner_type_name."' ");
        if($query->num_rows() > 0){
            $this->response([
                'status'    => false,
                'message'   => "Data already exists",
            ], 200);
        }else{
           $insert = $this->db->query("INSERT INTO partner_type(partner_type_name) values ('".$partner_type_name."') ");
        
            if($insert){
                $this->response([
                    'status'    => true,
                    'message'   => "Success",
                ], 200);
            }else{
                $this->response([
                    'status'    => false,
                    'message'   => "Something went wrong",
                ], 500);
            }
        
        }
       
}


function partnerstype_put(){
   $flag = $this->put("flag");
   $id = $this->put("id");
   $flag = ($flag == "false") ? 0 : 1;
   $query =  $this->db->query("update partner_type set flag = '".$flag."' where id = '".$id."' ");
    if($query){
        $this->response([
            'status'    => true,
            'message'   => "Success",
            'query' => "update partner_type set flag = '".$flag."' where id = '".$id."' "
        ], 200);
    }else{
        $this->response([
            'status'    => false,
            'message'   => "Something went wrong",
        ], 500);
    }

}

function ticketcategory_put(){
    $flag = $this->put("flag");
    $id = $this->put("id");
    $flag = ($flag == "false") ? 0 : 1;
    $query =  $this->db->query("update ticket_category set flag = '".$flag."' where ticket_category_id = '".$id."' ");
     if($query){
         $this->response([
             'status'    => true,
             'message'   => "Success",
             'query' => "update ticket_category set flag = '".$flag."' where ticket_category_id = '".$id."' "
         ], 200);
     }else{
         $this->response([
             'status'    => false,
             'message'   => "Something went wrong",
         ], 500);
     }
 
 }

function ticketcategory_post(){
    $ticket_category = $this->input->post("ticket_category");
    $ticket_category_order = $this->input->post("ticket_category_order");

    $query = $this->db->query("select * from ticket_category where ticket_category_name = '".$ticket_category."' ");
    if($query->num_rows() > 0){
        $this->response([
            'status'    => false,
            'message'   => "Data already exists",
        ], 200);
    }else{
       $insert = $this->db->query("INSERT INTO 
       ticket_category(ticket_category_name,ticket_category_order) values ('".$ticket_category."','".$ticket_category_order."') ");
    
        if($insert){
            $this->response([
                'status'    => true,
                'message'   => "Success",
            ], 200);
        }else{
            $this->response([
                'status'    => false,
                'message'   => "Something went wrong",
            ], 500);
        }
    
    }
}


    function ticketcategory_get(){
        $data = $this->db->query("SELECT * FROM `ticket_category` order by ticket_category_order asc ")->result();
        $this->response([
            'status' => true,
            'data'=>$data
        ], 200);
    }

    
    function ticket_get(){
        $start = $this->get("start");
        $limit = $this->get("limit");
        $id =  $this->get("id");

        if($id == null){
             $query = $this->db->query("SELECT a.fullname,a.id,a.email,a.phone,b.valid_from,b.valid_to,b.subscription_id,subscription_name,d.id ticket_id, e.ticket_category_name, d.created_date,is_close, close_date  FROM users a inner join (
                    select d.*
                    from (
                        select d.*, row_number() over(partition by user_id order by id desc) rn
                        from user_subscriptions d
                    ) d
                    where rn = 1
                ) b on a.id = b.user_id
                inner join subscriptions c on b.subscription_id = c.id
                inner join ticket d on a.id = d.user_id
                inner join ticket_category e on d.ticket_category = e.ticket_category_id
                
                ");
            $this->response([
                'status' => true,
                'data' =>$query->result(),
            ], 200);
        }else{

            $query = $this->db->query("SELECT a.device_id, a.fullname,a.user_image,a.fullname,a.id,a.email,a.phone,b.valid_from,b.valid_to,b.subscription_id,subscription_name,d.id ticket_id, e.ticket_category_name, d.created_date,is_close, close_date  FROM users a inner join (
                select d.*
                from (
                    select d.*, row_number() over(partition by user_id order by id desc) rn
                    from user_subscriptions d
                ) d
                where rn = 1
            ) b on a.id = b.user_id
            left join subscriptions c on b.subscription_id = c.id
            left join ticket d on a.id = d.user_id
            left join ticket_category e on d.ticket_category = e.ticket_category_id
            where d.id = '".$id."'
            ");

            $data = $query->row();
            
            $detail = $this->db->query("SELECT a.*,b.fullname FROM
             ticket_detail a left join users b
              on a.message_from = b.id where ticket_id = '".$id."' 
              order by a.id asc
              ")->result();
          

            $response = array(
                "device_id" => $data->device_id,
                "email" => $data->email,
                "ticket_id" => $data->ticket_id,
                "ticket_category_name" => $data->ticket_category_name,
                "is_close" => $data->is_close,
                "detail" => $detail

            );


        $this->response([
            'status' => true,
            'data' =>$response,
        ], 200);
        }
     }
   
     
    function subscription_get(){
            $query = $this->db->query("select * from subscriptions order by subscription_name asc");
            if($query->num_rows() < 1){
                $this->response([
                    'status' => false,
                ], 200);
            }else{
            $this->response([
                'status' => true,
                'data' =>$query->result(),
            ], 200);
        }
    }

    
function subscription_post(){
    $subscription_name  = $this->input->post("subscription_name");
    $month_total        = $this->input->post("month_total");
    $price              = $this->input->post("price");
    $created_by         = $this->input->post("created_by");
    $created_date       = $this->input->post("created_date");

    $query = $this->db->query("select * from subscriptions where subscription_name = '".$subscription_name."' ");
    if($query->num_rows() > 0){
        $this->response([
            'status'    => false,
            'message'   => "Data already exists",
        ], 200);
    }else{
      $insert =  $this->db->insert("subscriptions", $this->input->post());
        if($insert){
            $this->response([
                'status'    => true,
                'message'   => "Success",
            ], 200);
        }else{
            $this->response([
                'status'    => false,
                'message'   => "Something went wrong",
            ], 500);
        }
    
    }
}


function subscriptionflag_put(){
    $flag = $this->put("flag");
    $id = $this->put("id");
    $flag = ($flag == "false") ? 0 : 1;
    $query =  $this->db->query("update subscriptions set flag = '".$flag."' where id = '".$id."' ");
     if($query){
         $this->response([
             'status'    => true,
             'message'   => "Success",
         ], 200);
     }else{
         $this->response([
             'status'    => false,
             'message'   => "Something went wrong",
         ], 500);
     }
 
 }

 
 function subscription_put(){
    $subscription_name  = $this->put("subscription_name");
    $id  = $this->put("id");
    $this->db->where('id', $id);
    $insert =  $this->db->update("subscriptions", $this->put());
        if($insert){
            $this->response([
                'status'    => true,
                'message'   => "Success",
                'data' => $this->put("subscription_name")
            ], 200);
        }else{
            $this->response([
                'status'    => false,
                'message'   => "Something went wrong",
            ], 500);
        }
    }


}