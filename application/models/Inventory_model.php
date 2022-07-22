<?php

class Inventory_model extends CI_Model
{

    function get_purchase_order($condition)
    {
        $this->db->select('po_id,po_date,a.vendor_id,a.store_id,po_title,sub_total,tax_total,disc_total,grand_total,po_status,po_receipt,vendor_name,type_name');

        $this->db->from('po_header a');
        $this->db->join('vendor b', 'a.vendor_id = b.vendor_id and a.store_id = b.store_id', 'INNER');
        $this->db->join('type_vendor c', 'b.type_id = c.type_id and a.store_id = c.store_id', 'INNER');
        $this->db->where($condition);


        return $this->db->get()->row();
    }
    function get_purchase_orders($start, $limit, $condition, $date_condition, $like_condition)
    {
        $this->db->select('po_id,po_date,a.vendor_id,a.store_id,po_title,sub_total,tax_total,disc_total,grand_total,po_status,po_receipt,vendor_name,type_name');

        $this->db->from('po_header a');
        $this->db->join('vendor b', 'a.vendor_id = b.vendor_id and a.store_id = b.store_id', 'INNER');
        $this->db->join('type_vendor c', 'b.type_id = c.type_id and a.store_id = c.store_id', 'INNER');
        $this->db->where($condition);
        if ($date_condition != "") {
            $this->db->where($date_condition);
        }
        $this->db->not_like('po_status', '9');  // WHERE `title` NOT LIKE '%match% ESCAPE '!'
        $this->db->like($like_condition);
        $this->db->limit($limit, $start);


        return $this->db->get()->result();
    }
    function create_po()
    {
        $po_date = $this->input->post("po_date");
        $vendor_id = $this->input->post("vendor_id");
        $store_id = $this->input->post("store_id");
        $po_title = $this->input->post("po_title");
        $created_by = $this->input->post("created_by");

        return $this->db->query("call t_InsPOHeader ('" . $po_date . "','" . $vendor_id . "','" . $store_id . "','" . $po_title . "','" . $created_by . "') ")->row();
    }

    function create_podetail()
    {
        $po_id = $this->input->post("po_id");
        $item_code = $this->input->post("item_code");
        $qty = $this->input->post("qty");

        $item_price = $this->input->post("item_price");
        $subtotal = $this->input->post("subtotal");
        $disc = $this->input->post("disc");
        $total_disc = $this->input->post("total_disc");
        $tax = $this->input->post("tax");
        $total_tax = $this->input->post("total_tax");
        $modified_by = $this->input->post("modified_by");
        $store_id = $this->input->post("store_id");

        return $this->db->query("call t_InsPODetail (
            '" . $po_id . "','" . $item_code . "',
            '" . $qty . "','" . $item_price . "','" . $subtotal . "',
            '" . $disc . "','" . $total_disc . "',
            '" . $tax . "','" . $total_tax . "',
            '" . $modified_by . "','" . $store_id . "'
            
            ) ");
    }

    function get_podetail($po_id, $store_id, $start, $limit)
    {
        $this->db->select("flag_decimal,uom_name,pod_id,a.item_code,item_name,qty,price,disc,total_disc,tax,total_tax,sub_total");

        $this->db->from('po_detail a');
        $this->db->join('items b', 'a.item_code = b.item_code and a.store_id = b.store_id', 'INNER');
        $this->db->join('uom c', 'b.uom_id = c.uom_id', 'INNER');

        $this->db->where("po_id", $po_id);
        $this->db->where("a.store_id", $store_id);
        if ($limit != "" || $limit != null) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get()->result();
    }



    function update_status_po($store_id, $po_id, $status, $created_by)
    {
        return $this->db->query("call t_InsPOUpdateStatus('" . $store_id . "', '" . $po_id . "','" . $status . "','" . $created_by . "' ) ")->row();
    }
    function update_approve_po($store_id, $po_id, $created_by)
    {
        return $this->db->query("call t_InsPOApproval('" . $store_id . "', '" . $po_id . "','" . $created_by . "' ) ")->row();
    }
    function create_purchase_receipt()
    {
        $po_id = $this->input->post("po_id");
        $vendor_id = $this->input->post("vendor_id");
        $store_id = $this->input->post("store_id");
        $created_by = $this->input->post("created_by");
        return $this->db->query("call t_InsRcv ('" . $po_id . "','" . $store_id . "','" . $vendor_id . "','" . $created_by . "') ")->row();
    }

    function get_purchase_receipt($condition)
    {
        $this->db->select('rcv_id,po_id,a.created_date as pr_date,a.vendor_id,a.store_id,po_title,sub_total,tax_total,disc_total,grand_total,rcv_status,rcv_retur,vendor_name,type_name');

        $this->db->from('rcv_header a');
        $this->db->join('vendor b', 'a.vendor_id = b.vendor_id and a.store_id = b.store_id', 'INNER');
        $this->db->join('type_vendor c', 'b.type_id = c.type_id and a.store_id = c.store_id', 'INNER');
        $this->db->where($condition);



        return $this->db->get()->row();
    }

    function get_purchase_receipts($start, $limit, $condition, $date_condition, $like_condition)
    {
        $this->db->select('rcv_id,po_id,a.created_date as pr_date,a.vendor_id,a.store_id,po_title,sub_total,tax_total,disc_total,grand_total,rcv_status,rcv_retur,vendor_name,type_name');

        $this->db->from('rcv_header a');
        $this->db->join('vendor b', 'a.vendor_id = b.vendor_id and a.store_id = b.store_id', 'INNER');
        $this->db->join('type_vendor c', 'b.type_id = c.type_id and a.store_id = c.store_id', 'INNER');
        $this->db->where($condition);
        if ($date_condition != "") {
            $this->db->where($date_condition);
        }
        $this->db->like($like_condition);
        $this->db->limit($limit, $start);


        return $this->db->get()->result();
    }


    function get_prdetail($pr_id, $store_id, $start, $limit)
    {
        $this->db->select("flag_decimal,uom_name,rcvd_id,a.item_code,item_name,qty_po,qty_rcv,price,disc,total_disc,tax,total_tax,sub_total");

        $this->db->from('rcv_detail a');
        $this->db->join('items b', 'a.item_code = b.item_code and a.store_id = b.store_id', 'INNER');
        $this->db->join('uom c', 'b.uom_id = c.uom_id', 'INNER');

        $this->db->where("rcv_id", $pr_id);
        $this->db->where("a.store_id", $store_id);
        $this->db->limit($limit, $start);

        return $this->db->get()->result();
    }
    function update_quantity_receipt($store_id, $rcv_id, $rcvd_id, $item_code, $modified_by, $qty_rcv)
    {
        return $this->db->query("call t_InsRcvUpdateDetail('" . $store_id . "', '" . $rcv_id . "','" . $rcvd_id . "' ,'" . $item_code . "' ,'" . $qty_rcv . "' ,'" . $modified_by . "' ) ")->row();
    }
    function update_status_pr($store_id, $po_id, $status, $created_by)
    {
        return $this->db->query("call t_InsRcvUpdateStatus('" . $store_id . "', '" . $po_id . "','" . $status . "','" . $created_by . "' ) ")->row();
    }
    function update_approve_pr($store_id, $po_id, $created_by)
    {
        return $this->db->query("call t_InsRcvApprove('" . $store_id . "', '" . $po_id . "','" . $created_by . "' ) ")->row();
    }
}