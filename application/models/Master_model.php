<?php



class Master_model extends CI_Model

{



    public function get_all_partners($start = "", $limit = "", $condition = "", $like_condition = "")

    {



        $this->db->select("*");

        $this->db->from('partners a');

        $this->db->join('partner_type', 'a.type = partner_type.id');





        if ($condition != "") {

            $this->db->where($condition);
        }





        if ($like_condition != "") {

            $this->db->like($like_condition);
        } else {

            $this->db->limit($limit, $start);
        }



        return $this->db->get()->result();
    }



    public function get_partners($condition = "")

    {

        $this->db->select("*");

        if ($condition != "") {

            $this->db->where($condition);
        }

        return $this->db->get("partners")->row();
    }



    public function add_partners($data)

    {

        return $this->db->insert('partners', $data);
    }



    public function add_itemsv2(

        $item_name,

        $item_short_name,

        $barcode,

        $created_by,

        $store_id,

        $item_price,

        $item_image,

        $item_type,

        $item_category_id,

        $item_stock,

        $item_cost,

        $satuan

    ) {

        return $this->db->query('call w_ItemCreate_20201227(   

        "' . $item_name . '",

        "' . $item_short_name . '",

        "' . $barcode . '",

        "",

        "' . $created_by . '",

        "' . $store_id . '",

        "' . $item_price . '",

        "' . $item_image . '",

        "' . $item_type . '",

        "' . $item_category_id . '",

        "' . $item_stock . '",

        "' . $item_cost . '",

        "' . $satuan . '"

        

        ) ');
    }



    public function add_items(

        $item_name,

        $item_short_name,

        $barcode,

        $created_by,

        $store_id,

        $item_price,

        $item_image,

        $item_type,

        $item_category_id,

        $item_stock,

        $item_cost

    ) {

        return $this->db->query('call w_ItemCreate(   

        "' . $item_name . '",

        "' . $item_short_name . '",

        "' . $barcode . '",

        "",

        "' . $created_by . '",

        "' . $store_id . '",

        "' . $item_price . '",

        "' . $item_image . '",

        "' . $item_type . '",

        "' . $item_category_id . '",

        "' . $item_stock . '",

        "' . $item_cost . '") ');
    }

    public function edit_itemsv2(

        $item_code,

        $item_name,

        $item_short_name,

        $barcode,

        $created_by,

        $store_id,

        $item_price,

        $item_image,

        $item_type,

        $item_category_id,

        $item_stock,

        $item_cost,

        $satuan

    ) {

        return $this->db->query('call w_ItemEdit_20201227(   

          "' . $item_code . '",

        "' . $item_name . '",

        "' . $item_short_name . '",

        "' . $barcode . '",

        "",

        "' . $created_by . '",

        "' . $store_id . '",

        "' . $item_price . '",

        "' . $item_image . '",

        "' . $item_type . '",

        "' . $item_category_id . '",

        "' . $item_stock . '",

        "' . $item_cost . '",

        "' . $satuan . '") ');
    }

    public function edit_items(

        $item_code,

        $item_name,

        $item_short_name,

        $barcode,

        $created_by,

        $store_id,

        $item_price,

        $item_image,

        $item_type,

        $item_category_id,

        $item_stock,

        $item_cost

    ) {

        return $this->db->query('call w_ItemEdit(   

          "' . $item_code . '",

        "' . $item_name . '",

        "' . $item_short_name . '",

        "' . $barcode . '",

        "",

        "' . $created_by . '",

        "' . $store_id . '",

        "' . $item_price . '",

        "' . $item_image . '",

        "' . $item_type . '",

        "' . $item_category_id . '",

        "' . $item_stock . '",

        "' . $item_cost . '") ');
    }



    public function get_item_transaction($start = "", $limit = "", $store_id = "", $category_id = "", $partner = "", $search = "")

    {



        $query =  $this->db->query("call w_ItemRead (1,'" . $store_id . "','','','" . $category_id . "','','" . $partner . "','Produk','" . $start . "','" . $limit . "','" . $search . "') ");

        if ($query) {

            $data = array(

                "status" => true,

                "count" =>  $query->num_rows(),

                "data" =>  $query->result(),

            );
        } else {

            $data = array(

                "status" => false,

            );
        }



        return $data;
    }



    public function get_item_transactionv2($start = "", $limit = "", $store_id = "", $category_id = "", $partner = "", $search = "")

    {



        $query =  $this->db->query("call w_ItemRead (1,'" . $store_id . "','','','" . $category_id . "','','" . $partner . "','Produk','" . $start . "','" . $limit . "','" . $search . "') ");

        if ($query) {

            $data = array(

                "status" => true,

                "count" =>  $query->num_rows(),

                "data" =>  $query->result(),

            );
        } else {

            $data = array(

                "status" => false,

            );
        }



        return $data;
    }



    public function get_items_transaction($start = "", $limit = "", $store_id = "", $category_id = "", $partner = "", $search = "")

    {



        $query =  $this->db->query("call w_ItemRead (1,'" . $store_id . "','','','" . $category_id . "','','" . $partner . "','Produk','" . $start . "','" . $limit . "','" . $search . "') ");

        if ($query) {

            $data = array(

                "status" => true,

                "count" =>  $query->num_rows(),

                "data" =>  $query->row(),

            );
        } else {

            $data = array(

                "status" => false,

            );
        }



        return $data;
    }

    // public function get_item_transaction($start = "", $limit = "", $condition = "", $partner = "", $search)

    // {

    //     $this->db->select("sa.item_code,a.item_name, CONCAT('Rp. ', FORMAT(IFNULL(b.item_price, 0), 2)) item_price,

    //      TRIM(IFNULL(b.item_price, 0))+0 price, 0 as quantity, a.item_type, a.item_category_id,item_image,item_short_name,barcode,IFNULL(average_cost,0) as average_cost");



    //     if ($condition != "") {

    //         $this->db->where($condition);

    //     }



    //     $this->db->from('items a');

    //     if ($partner != "none") {

    //         $this->db->join('item_price b', 'a.item_code = b.item_code and a.store_id = b.store_id and partner_type = ' . $partner . ' ', "left");

    //     } else {

    //         $this->db->join('item_price b', 'a.item_code = b.item_code and a.store_id = b.store_id', "left");

    //     }

    //     $this->db->join('item_stock c', 'a.item_code = c.item_code and a.store_id = c.store_id', "left");



    //     if ($search != "all") {

    //         $search = str_replace("_", " ", $search);

    //         $this->db->like(array("item_name" => $search));

    //     }

    //     $this->db->limit($limit, $start);



    //     return $this->db->get()->result();

    // }

    public function get_all_items($start = "", $limit = "", $condition = "", $orlike = "")

    {



        $this->db->select("a.item_code,a.item_name,a.item_code, CONCAT('Rp. ', FORMAT(a.item_price, 2)) item_price, TRIM(a.item_price)+0 price, IFNULL(b.quantity, 0) quantity");

        if ($start != "") {

            $this->db->limit($limit, $start);
        }

        if ($condition != "") {

            $this->db->where($condition);
        }

        if ($orlike != "") {

            $this->db->like($orlike);
        }



        $this->db->from('items a');

        $this->db->join('item_stock b', 'a.item_code = b.item_code and a.store_id = b.store_id', "inner");

        return $this->db->get()->result();
    }



    public function get_all_itemsv2($start = "", $limit = "", $condition = "", $orlike = "")

    {



        $this->db->select("a.item_code,a.item_name,a.item_code, CONCAT('Rp. ', FORMAT(a.item_price, 2)) item_price, TRIM(a.item_price)+0 price, IFNULL(b.quantity, 0) quantity");

        if ($start != "") {

            $this->db->limit($limit, $start);
        }

        if ($condition != "") {

            $this->db->where($condition);
        }

        if ($orlike != "") {

            $this->db->like($orlike);
        }



        $this->db->from('items a');

        $this->db->join('item_stock b', 'a.item_code = b.item_code and a.store_id = b.store_id', "inner");

        return $this->db->get()->result();
    }



    public function get_all_items_search($start = "", $limit = "", $search = "", $store_id)

    {



        $this->db->select("a.item_code,a.item_name,a.item_code, CONCAT('Rp. ', FORMAT(a.item_price, 2)) item_price, TRIM(a.item_price)+0 price, IFNULL(b.quantity, 0) quantity");

        if ($start != "") {

            $this->db->limit($limit, $start);
        }

        $search = str_replace("_", " ", $search);

        $this->db->like("item_name", $search);

        $this->db->where("a.store_id", $store_id);





        $this->db->from('items a');

        $this->db->join('item_stock b', 'a.item_code = b.item_code and a.store_id = b.store_id', "inner");





        return $this->db->get()->result();
    }



    public function get_items($condition = "")

    {

        $this->db->select("*,CONCAT('Rp. ', FORMAT(item_price, 2)) item_price");

        if ($condition != "") {

            $this->db->where($condition);
        }



        return $this->db->get("items")->row();
    }



    function delete_item($store_id, $item_code)

    {



        $this->db->where('item_code', $item_code);

        $this->db->where('store_id', $store_id);

        return $this->db->query("call w_ItemDelete ('" . $store_id . "','" . $item_code . "') ");
    }



    function add_item_price($data)

    {

        return $this->db->insert('item_price', $data);
    }



    function get_all_item_price($start, $limit, $condition)

    {





        $this->db->select('a.id,c.item_name,  CONCAT("Rp. ", FORMAT(a.item_price, 2)) item_price, partner_type_name');

        $this->db->from('item_price a');

        $this->db->join('partner_type b', 'a.partner_type = b.id');

        $this->db->join('items c', 'a.item_code = c.item_code and a.store_id = c.store_id');

        $this->db->limit($limit, $start);

        if ($condition != "") {

            $this->db->where($condition);
        }

        return $this->db->get()->result();
    }





    function get_item_stock($start, $limit, $condition)

    {

        $this->db->select('a.item_image, a.item_code,item_name, SUM(IFNULL(b.quantity,0)) quantity,a.item_price');

        $this->db->from('items a');

        $this->db->join('item_stock b', 'a.item_code = b.item_code and a.store_id = b.store_id', 'LEFT');

        $this->db->group_by('a.item_code, item_name');

        $this->db->limit($limit, $start);

        if ($condition != "") {

            $this->db->where($condition);
        }

        $this->db->where('item_type', 'Produk');



        return $this->db->get()->result();
    }





    function get_transaction_item($condition, $start, $limit)

    {

        $this->db->select('a.item_image, a.item_code,item_name, SUM(IFNULL(b.quantity,0)) quantity,IFNULL(c.item_price,a.item_price) item_price');

        $this->db->from('items a');

        $this->db->join('item_stock b', 'a.item_code = b.item_code and a.store_id = b.store_id', 'INNER');

        $this->db->join('item_price c', 'a.item_code = c.item_code and a.store_id = c.store_id', 'INNER');



        $this->db->group_by('a.item_code, item_name');

        if ($condition != "") {

            $this->db->where($condition);
        }

        $this->db->limit($limit, $start);



        return $this->db->get()->result();
    }





    function get_vendor_type($start, $limit, $condition, $like_condition)

    {

        $this->db->select('type_id, type_name');

        $this->db->from('type_vendor');

        $this->db->limit($limit, $start);

        $this->db->where($condition);

        if ($like_condition != "") {

            $this->db->like($like_condition);
        }



        return $this->db->get()->result();
    }



    function get_vendor($start, $limit, $condition, $like_condition)

    {

        $this->db->select('vendor_id,vendor_name,type_id,address,phone,contact_name,store_id');

        $this->db->from('vendor');

        $this->db->limit($limit, $start);

        $this->db->where($condition);

        if ($like_condition != "") {

            $this->db->like($like_condition);
        }



        return $this->db->get()->result();
    }



    public function get_user($condition)

    {

        $this->db->where($condition);

        return $this->db->get("users")->row();
    }



    // function add_cashflow_category($cash_category_id,$store_id, $cash_category_name, $cash_category_type, $user_id){



    // }

    function add_cashflow_category($data)

    {

        return $this->db->insert('cash_category', $data);
    }



    function get_cashflow_category($condition)

    {

        $this->db->select('cash_category_id, cash_category_name, type');

        $this->db->from('cash_category');

        $this->db->where($condition);

        return $this->db->get();
    }



    function edit_cashflow_category($data)

    {

        return $this->db->replace('cash_category', $data);
    }



    function delete_cashflow_category($condition)

    {

        $this->db->where($condition);

        return $this->db->delete('cash_category');
    }
}