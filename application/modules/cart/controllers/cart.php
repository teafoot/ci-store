<?php

class Cart extends MX_Controller {

  public function __construct() {
    parent::__construct();
  }

  function _draw_add_to_cart($item_id) {
    $data["item_id"] = $item_id;

    // fetch color options
    $data["submitted_color"] = $this->input->post("submitted_color", true);

    if (empty($submitted_color)) {
      $color_options[''] = "Please Select...";
    }

    $this->load->module("store_item_colors");
    $query = $this->store_item_colors->get_where_custom("item_id", $item_id);

    foreach ($query->result() as $row) {
      $color_options[$row->id] = $row->color;
    }

    $data["num_colors"] = $query->num_rows();
    $data["color_options"] = $color_options;

    // fetch size options
    $data["submitted_size"] = $this->input->post("submitted_size", true);

    if (empty($submitted_size)) {
      $size_options[''] = "Please Select...";
    }

    $this->load->module("store_item_sizes");
    $query = $this->store_item_sizes->get_where_custom("item_id", $item_id);

    foreach ($query->result() as $row) {
      $size_options[$row->id] = $row->size;
    }

    $data["num_sizes"] = $query->num_rows();
    $data["size_options"] = $size_options;

    $this->load->view("add_to_cart", $data);
  }

  function index() {
    $third_bit = $this->uri->segment(3);

    if ($third_bit != "") {
      $session_id = $this->_check_and_get_session_id($third_bit); // check the token
    } else {
      $session_id = $this->session->session_id;
    }

    $this->load->module("site_security");
    $shopper_id = $this->site_security->_get_user_id();

    if (!is_numeric($shopper_id)) {
      $shopper_id = 0;
    }

    $table = "store_basket";
    $data["query"] = $this->_fetch_cart_contents($session_id, $shopper_id, $table);
    $data["num_rows"] = $data["query"]->num_rows();
    $data["showing_statement"] = $this->_get_showing_statement($data["num_rows"]);

    $data["flash"] = $this->session->flashdata("item");
    $data["view_file"] = "cart";
    $this->load->module("templates");
    $this->templates->public_bootstrap($data);
  }

  function _fetch_cart_contents($session_id, $shopper_id, $table) {
    $this->load->module("store_basket");

    $mysql_query = "
      SELECT
        $table.*,
        store_items.small_pic
      FROM $table
        LEFT JOIN store_items
          ON $table.item_id = store_items.id
    ";

    if ($shopper_id > 0) {
      $where_condition = " WHERE $table.shopper_id = $shopper_id";
    } else {
      $where_condition = " WHERE $table.session_id = '$session_id'";
    }

    $mysql_query .= $where_condition;
    $query = $this->store_basket->_custom_query($mysql_query);

    return $query;
  }

  function _get_showing_statement($num_items) {
    if ($num_items == 1) {
      $showing_statement = "You have one item in your shopping basket.";
    } else {
      $showing_statement = "You have " . $num_items . " items in your shopping basket.";      
    }

    return $showing_statement;
  }

  function _draw_cart_contents($query, $user_type) {
    // note: $user_type can be 'public' or 'admin'
    $this->load->module("site_settings");
    $data["currency_symbol"] = $this->site_settings->_get_currency_symbol();

    $this->load->module("shipping");
    $data["shipping"] = $this->shipping->_get_shipping();

    if ($user_type == "public") {
      $view_file = "cart_contents_public";
    } else {
      $view_file = "cart_contents_admin";
    }

    $data["query"] = $query;
    $this->load->view($view_file, $data);
  }

  function _attempt_draw_checkout_btn($query) {
    $data["query"] = $query;
    $third_bit = $this->uri->segment(3);

    $this->load->module("site_security");
    $shopper_id = $this->site_security->_get_user_id();

    if ((!is_numeric($shopper_id)) and ($third_bit == "")) {
      $this->_draw_checkout_btn_fake($query); // logged out
    } else {
      $this->_draw_checkout_btn_real($query);
    }
  }

  function _draw_checkout_btn_fake($query) {
    foreach ($query->result() as $row) {
      $session_id = $row->session_id;
    }

    $data["checkout_token"] = $this->_create_checkout_token($session_id);

    $this->load->view("checkout_btn_fake", $data);
  }

  function _draw_checkout_btn_real($query) {
    $this->load->module("paypal");
    $this->paypal->_draw_checkout_btn($query);
  }

  function go_to_checkout() {
    $this->load->module("site_security");
    $shopper_id = $this->site_security->_get_user_id();

    if (is_numeric($shopper_id)) {
      redirect("cart");
    }

    $data["checkout_token"] = $this->uri->segment(3);

    $data["flash"] = $this->session->flashdata("item");
    $data["view_file"] = "go_to_checkout";
    $this->load->module("templates");
    $this->templates->public_bootstrap($data);
  }

  function submit_choice() {
    $submit = $this->input->post("submit", true);

    if ($submit == "No") {
      $checkout_token = $this->input->post("checkout_token", true);
      $this->_generate_guest_account($checkout_token);      

      redirect("cart/index/" . $checkout_token);
    } else if ($submit == "Yes") {
      redirect("youraccount/start");
    }
  }

  // function test() { // encryption
  //   $string = "Hello blue sky";
  //   $this->load->module("site_security");
  //   $encrypted_string = $this->site_security->_encrypt_string($string);
  //   $decrypted_string = $this->site_security->_decrypt_string($encrypted_string);

  //   echo "string is $string<br>";
  //   echo "encrypted string is $encrypted_string<br>";
  //   echo "decrypted string is $decrypted_string<br>";
  // }

  // function test2() { // encryption
  //   $this->load->module("site_security");

  //   $third_bit = $this->uri->segment(3);

  //   if ($third_bit != "") {
  //     $encrypted_string = $third_bit;
  //   } else {
  //     $encrypted_string = $this->site_security->_encrypt_string($third_bit);
  //   }

  //   $decrypted_string = $this->site_security->_decrypt_string($encrypted_string);

  //   echo "string is: " . $third_bit;
  //   echo "<hr>";
  //   echo "encrypted string is: " . $encrypted_string;
  //   echo "<br>";
  //   echo "decrypted string is: " . $decrypted_string;

  //   $new_encrypted_string = $this->site_security->_encrypt_string($third_bit);
  //   echo anchor("cart/test2/" . $new_encrypted_string, "refresh");
  // }

  function _create_checkout_token($session_id) {
    $this->load->module("site_security");
    $encrypted_string = $this->site_security->_encrypt_string($session_id);

    $checkout_token = str_replace("+", "-plus-", $encrypted_string);
    $checkout_token = str_replace("/", "-fwrd-", $checkout_token);
    $checkout_token = str_replace("=", "-eqls-", $checkout_token);

    return $checkout_token;
  }

  function _get_session_id_from_token($checkout_token) {
    $this->load->module("site_security");

    $session_id = str_replace("-plus-", "+", $checkout_token);
    $session_id = str_replace("-fwrd-", "/", $session_id);
    $session_id = str_replace("-eqls-", "=", $session_id);

    $session_id = $this->site_security->_decrypt_string($session_id);

    return $session_id;
  }

  function _check_and_get_session_id($checkout_token) {
    $session_id = $this->_get_session_id_from_token($checkout_token);

    if ($session_id == "") {
      redirect(base_url());
    }

    $this->load->module("store_basket");
    $query = $this->store_basket->get_where_custom("session_id", $session_id);
    $num_rows = $query->num_rows();

    if ($num_rows < 1) {
      redirect(base_url());
    }

    return $session_id;
  }

  function _generate_guest_account($checkout_token) {
    $this->load->module("site_security");
    $this->load->module("store_accounts");

    $customer_session_id = $this->_get_session_id_from_token($checkout_token);

    $ref = $this->site_security->generate_random_string(4);    
    $data["username"] = "Guest_" . $ref;
    $data["firstname"] = "Guest";
    $data["lastname"] = "Account";
    $data["pword"] = $checkout_token;
    $data["date_made"] = time();
    $this->store_accounts->_insert($data);
    $new_account_id = $this->store_accounts->get_max();

    $mysql_query = "update store_basket set shopper_id=$new_account_id where session_id='$customer_session_id'";
    $query = $this->store_accounts->_custom_query($mysql_query);
  }

  function _calc_cart_total($cart_data) {
    $customer_session_id = $cart_data["customer_session_id"];
    $shopper_id = $cart_data["shopper_id"];
    $table = $cart_data["table"];
    $add_shipping = $cart_data["add_shipping"];

    $grand_total = 0;    
    $query = $this->_fetch_cart_contents($customer_session_id, $shopper_id, $table);
    foreach ($query->result() as $row) {
      $sub_total = $row->price * $row->item_qty;
      $grand_total += $sub_total;
    }

    if ($add_shipping) {
      $this->load->module("shipping");
      $shipping = $this->shipping->_get_shipping();
    } else {
      $shipping = 0;
    }

    $grand_total += $shipping;

    return $grand_total;
  }

}
