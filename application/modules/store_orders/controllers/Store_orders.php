<?php

class Store_orders extends MX_Controller {

  public function __construct() {
    parent::__construct();
  }

  function _auto_generate_order($paypal_id, $customer_session_id) {
    // this gets called from the Paypal IPN Listener, when an order is placed.
    $this->load->module("site_security");
    $order_ref = $this->site_security->generate_random_string(6);
    $order_ref = strtoupper($order_ref);

    $data["order_ref"] = $order_ref;
    $data["session_id"] = $customer_session_id;
    $data["paypal_id"] = $paypal_id;
    $data["opened"] = 0;
    $data["order_status"] = 0;
    $data["shopper_id"] = $this->_get_shopper_id($customer_session_id);
    $data["mc_gross"] = $this->_get_mc_gross($paypal_id);
    $data["date_created"] = time();

    $this->_insert($data);

    // transfer from store_basket to store_shoppertrack
    $this->load->module("store_shoppertrack");
    $this->store_shoppertrack->_transfer_from_basket($customer_session_id);
  }

  function browse() {
    $this->load->module("site_security");
    $this->load->library("session");
    $this->load->module("custom_pagination");

    $this->site_security->_make_sure_is_admin();

    // count the orders that belong to this order status
    $use_limit = false;
    $mysql_query = $this->_generate_mysql_query($use_limit);
    $query = $this->_custom_query($mysql_query);
    $total_items = $query->num_rows();

    // fetch the orders that belong to this order status
    $use_limit = true;
    $mysql_query = $this->_generate_mysql_query($use_limit);
    $data["query"] = $this->_custom_query($mysql_query);
    $data["num_rows"] = $data["query"]->num_rows();

    // generate the pagination
    $pagination_data["template"] = "admin"; 
    $pagination_data["target_base_url"] = $this->get_target_pagination_base_url(); 
    $pagination_data["total_rows"] = $total_items; 
    $pagination_data["offset_segment"] = 4; // from url
    $pagination_data["limit"] = $this->get_limit();
    $data["pagination"] = $this->custom_pagination->_generate_pagination($pagination_data);
    //
    $pagination_data["offset"] = $this->get_offset();
    $data["showing_statement"] = $this->custom_pagination->get_showing_statement($pagination_data);

    $data["current_order_status"] = $this->_get_order_status_title();

    $data["flash"] = $this->session->flashdata("item");
    $data["view_file"] = "browse";
    $this->load->module("templates");
    $this->templates->admin($data);
  }

  function _generate_mysql_query($use_limit) {
    $order_status = $this->uri->segment(3);
    $order_status = str_replace("status", "", $order_status);

    if (!is_numeric($order_status)) {
      $order_status = 0;
    }

    // $mysql_query = "select * from store_orders where order_status=$order_status order by date_created desc";
    if ($order_status > 0) {
      $mysql_query = "
        SELECT
          store_orders.id,
          store_orders.order_ref,
          store_orders.opened,
          store_orders.mc_gross,
          store_orders.date_created,
          store_accounts.firstname,
          store_accounts.lastname,
          store_accounts.company,
          store_order_status.status_title
        FROM store_orders
          INNER JOIN store_accounts
            ON store_orders.shopper_id = store_accounts.id
          INNER JOIN store_order_status
            ON store_orders.order_status = store_order_status.id
        WHERE store_orders.order_status=$order_status 
        ORDER BY store_orders.date_created DESC
      ";
    } else {
      $mysql_query = "
        SELECT
          store_orders.id,
          store_orders.order_ref,
          store_orders.opened,
          store_orders.mc_gross,
          store_orders.date_created,
          store_accounts.firstname,
          store_accounts.lastname,
          store_accounts.company
        FROM store_orders
          INNER JOIN store_accounts
            ON store_orders.shopper_id = store_accounts.id
        WHERE store_orders.order_status = $order_status
        ORDER BY store_orders.date_created DESC
      ";
    }

    if ($use_limit) {
      $limit = $this->get_limit();
      $offset = $this->get_offset();
      $mysql_query .= " limit " . $offset . ", " . $limit;
    }

    return $mysql_query;
  }

  function get_limit() {
    $limit = 5;
    return $limit;
  }

  function get_offset() {
    $offset = $this->uri->segment(4);

    if (!is_numeric($offset)) {
      $offset = 0;
    }

    return $offset;
  }

  function get_target_pagination_base_url() {
    $first_bit = $this->uri->segment(1);
    $second_bit = $this->uri->segment(2);
    $third_bit = $this->uri->segment(3);

    $target_base_url = base_url() . $first_bit . "/" . $second_bit . "/" . $third_bit;

    return $target_base_url; 
  }

  function _get_mc_gross($paypal_id) {
    // find the total amount taken in from this order
    $this->load->module("paypal");
    $query = $this->paypal->get_where($paypal_id);
    foreach ($query->result() as $row) {
      $posted_information = $row->posted_information;
    }

    if (!isset($posted_information)) {
      $mc_gross = 0;
    } else {
      $posted_information = unserialize($posted_information);
      $mc_gross = $posted_information["mc_gross"];
    }

    return $mc_gross;
  }

  function _get_shopper_id($customer_session_id) {
    $this->load->module("store_basket");

    $query = $this->store_basket->get_where_custom("session_id", $customer_session_id);
    foreach ($query->result() as $row) {
      $shopper_id = $row->shopper_id;
    }

    if (!isset($shopper_id)) {
      $shopper_id = 0;
    }

    return $shopper_id;
  }

  // function test() { // testing _get_mc_gross($paypal_id)
  //   $paypal_id = 9;
  //   $this->_get_mc_gross($paypal_id);
  // }

  function view() {
    $this->load->module("site_security");
    $this->load->library("session");
    $this->load->module("store_accounts");
    $this->load->module("store_order_status");
    $this->load->module("cart");
    $this->load->module("timedate");

    $this->site_security->_make_sure_is_admin();

    $update_id = $this->uri->segment(3);
    $this->_set_to_opened($update_id);

    $query = $this->get_where($update_id);
    foreach ($query->result() as $row) {
      $data["order_ref"] = $row->order_ref;
      $session_id = $row->session_id;
      $data["paypal_id"] = $row->paypal_id;
      $data["opened"] = $row->opened;
      $order_status = $row->order_status;
      $data["shopper_id"] = $row->shopper_id;
      $data["mc_gross"] = $row->mc_gross;
      $date_created = $row->date_created;
    }

    if ($order_status == 0) {
      $data["status_title"] = "Order Submitted";
    } else {
      $data["status_title"] = $this->store_order_status->_get_status_title($order_status);
    }

    $data["order_status"] = $order_status;
    $data["options"] = $this->store_order_status->_get_dropdown_options();
    $data["date_created"] = $this->timedate->get_nice_date($date_created, "full");
    $data["store_accounts_data"] = $this->store_accounts->_fetch_data_from_db($data["shopper_id"]);
    $data["customer_address"] = $this->store_accounts->_get_shopper_address($data["shopper_id"], "<br>");

    // fetch contents of shopping cart
    $table = "store_shoppertrack";
    $data["query_cart_contents"] = $this->cart->_fetch_cart_contents($session_id, $data["shopper_id"], $table);

    $data["update_id"] = $update_id;
    $data["headline"] = "Order: " . $data["order_ref"];
    $data["view_file"] = "view";
    $data["flash"] = $this->session->flashdata("item");
    $this->load->module("templates");
    $this->templates->admin($data);
  }

  function submit_order_status() {
    $this->load->module("site_security");
    $this->site_security->_make_sure_is_admin();

    $update_id = $this->uri->segment(3);

    $order_status = $this->input->post("order_status", true);
    $submit = $this->input->post("submit", true);

    if ($submit == "Cancel") {
      // get the current order status for this order
      $query = $this->get_where($update_id);
      foreach ($query->result() as $row) {
        $order_status = $row->order_status;
      }

      $target_url = base_url() . "store_orders/browse/status" . $order_status;
      redirect($target_url);
    } else if ($submit == "Submit") {
      $data["order_status"] = $order_status;
      $this->_update($update_id, $data);

      $this->_send_auto_notify($update_id);

      $flash_msg = "The order status was successfully updated.";
      $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
      $this->session->set_flashdata("item", $value);

      redirect("store_orders/view/" . $update_id);
    }
  }

  function _get_order_status_title() {
    // note: this gets called by browse.php and it figures out the order status title
    $this->load->module("store_order_status");

    $order_status = $this->uri->segment(3);
    $order_status = str_replace("status", "", $order_status);

    if (!is_numeric($order_status)) {
      $order_status = 0;
    }

    if ($order_status == 0) {
      $status_title = "Order Submitted";
    } else {
      $status_title = $this->store_order_status->_get_status_title($order_status);
    }

    return $status_title;
  }

  function _set_to_opened($update_id) {
    $data["opened"] = 1;
    $this->_update($update_id, $data);
  }

  function _send_auto_notify($update_id) {
    // note: notifies the customer when an order status has been updated.
    $this->load->module("site_security");
    $this->load->module("store_order_status");
    $this->load->module("enquiries");

    $query = $this->get_where($update_id);
    foreach ($query->result() as $row) {
      $order_ref = $row->order_ref;
      $shopper_id = $row->shopper_id;
      $order_status = $row->order_status;
    }

    $status_title = $this->store_order_status->_get_status_title($order_status);

    $msg = "Order: " . $order_ref . " has just been updated. " . "The new status for your order is: " . $status_title . ".";

    $data["sent_by"] = 0; // admin
    $data["sent_to"] = $shopper_id;
    $data["subject"] = "Order Status Update";
    $data["message"] = $msg;
    $data["opened"] = 0;
    $data["code"] = $this->site_security->generate_random_string(6);
    $data["date_created"] = time();

    $this->enquiries->_insert($data);
  }

  //

  public function get($order_by) {
    $this->load->model("mdl_store_orders");
    $query = $this->mdl_store_orders->get($order_by);

    return $query;
  }

  public function get_with_limit($limit, $offset, $order_by) {
    $this->load->model("mdl_store_orders");
    $query = $this->mdl_store_orders->get_with_limit($limit, $offset, $order_by);

    return $query;
  }

  public function get_where($id) {
    $this->load->model("mdl_store_orders");
    $query = $this->mdl_store_orders->get_where($id);

    return $query;
  }

  public function get_with_double_condition($col1, $value1, $col2, $value2) {
    $this->load->model("mdl_store_orders");
    $query = $this->mdl_store_orders->get_with_double_condition($col1, $value1, $col2, $value2);

    return $query;
  }

  public function get_where_custom($col, $value) {
    $this->load->model("mdl_store_orders");
    $query = $this->mdl_store_orders->get_where_custom($col, $value);

    return $query;
  }

  function _insert($data) {
    $this->load->model("mdl_store_orders");
    $this->mdl_store_orders->_insert($data);
  }

  function _update($id, $data) {
    $this->load->model("mdl_store_orders");
    $this->mdl_store_orders->_update($id, $data);
  }

  function _delete($id) {
    $this->load->model("mdl_store_orders");
    $this->mdl_store_orders->_delete($id);
  }

  public function count_where($column, $value) {
    $this->load->model("mdl_store_orders");
    $count = $this->mdl_store_orders->count_where($column, $value);

    return $count;
  }

  public function get_max() {
    $this->load->model("mdl_store_orders");
    $max_id = $this->mdl_store_orders->get_max();

    return $max_id;
  }

  function _custom_query($mysql_query) {
    $this->load->model("mdl_store_orders");
    $query = $this->mdl_store_orders->_custom_query($mysql_query);

    return $query;
  }

}
