<?php

class Youraccount extends MX_Controller {

  public function __construct() {
    parent::__construct();

    $this->load->library('form_validation');
    $this->form_validation->CI = & $this;
  }

  function start() {
    $data = $this->_fetch_data_from_post();
    $data["flash"] = $this->session->flashdata("item");
    $data["view_file"] = "start";

    $this->load->module("templates");
    $this->templates->public_bootstrap($data);
  }

  function _fetch_data_from_post() {
    $data["username"] = $this->input->post("username", true);
    $data["email"] = $this->input->post("email", true);
    $data["pword"] = $this->input->post("pword", true);
    $data["repeat_pword"] = $this->input->post("repeat_pword", true);

    return $data;
  }

  function submit() {
    $submit = $this->input->post("submit", true);

    // POST Request from form
    if ($submit == "Submit") {
      $this->load->library("form_validation");
      $this->form_validation->set_rules("username", "Username", "required|min_length[4]|max_length[60]"); // |is_unique[store_accounts.username]
      $this->form_validation->set_rules("email", "Email Address", "required|valid_email|max_length[120]");
      $this->form_validation->set_rules("pword", "Password", "required|min_length[4]|max_length[35]");
      $this->form_validation->set_rules("repeat_pword", "Repeat Password", "required|matches[pword]");

      if ($this->form_validation->run() == true) {
        $this->_process_create_account();
        
        redirect("youraccount/login");
      } else {
        $this->start();
      }
    } else if ($submit == "Cancel") {
      redirect(base_url());
    }
  }

  function _process_create_account() {
    $data = $this->_fetch_data_from_post();
    unset($data['repeat_pword']);

    $this->load->module("site_security");
    $pword = $data["pword"];
    $data["pword"] = $this->site_security->_hash_string($pword);

    $this->load->module("store_accounts");
    $this->store_accounts->_insert($data);
  }

  function login() {
    $data["username"] = $this->input->post("username", true);
    $this->load->module("templates");
    $this->templates->login($data);
  }

  function submit_login() {
    $submit = $this->input->post("submit", true);

    // POST Request from form
    if ($submit == "Submit") {
      $this->load->library("form_validation");
      $this->form_validation->set_rules("username", "Username", "required|min_length[4]|max_length[60]|callback_username_check");
      $this->form_validation->set_rules("pword", "Password", "required|min_length[4]|max_length[35]");

      if ($this->form_validation->run() == true) {
        $col1 = "username";
        $value1 = $this->input->post("username", true);
        $col2 = "email";
        $value2 = $this->input->post("username", true);

        $query = $this->store_accounts->get_with_double_condition($col1, $value1, $col2, $value2);
        foreach ($query->result() as $row) {
          $user_id = $row->id;
        }

        $remember = $this->input->post("remember", true);

        if ($remember == "remember-me") {
          $login_type = "longterm";
        } else {
          $login_type = "shortterm";
        }

        $data["last_login"] = time();
        $this->store_accounts->_update($user_id, $data);

        $this->_in_you_go($user_id, $login_type);
      } else {
        $this->login();
      }
    }
  }

  function username_check($str) {
    $this->load->module("site_security");
    $this->load->module("store_accounts");

    $col1 = "username";
    $value1 = $str;
    $col2 = "email";
    $value2 = $str;

    $query = $this->store_accounts->get_with_double_condition($col1, $value1, $col2, $value2);
    $num_rows = $query->num_rows();

    if ($num_rows == 0) {
      $error_msg = "You did not enter a correct username/password.";
      $this->form_validation->set_message('username_check', $error_msg);
      return false;
    }

    foreach ($query->result() as $row) {
      $pword_on_table = $row->pword;
    }

    $pword = $this->input->post("pword", true);
    $result = $this->site_security->_verify_hash($pword, $pword_on_table);

    if ($result) {
      return true;
    } else {
      $error_msg = "You did not enter a correct username/password.";
      $this->form_validation->set_message('username_check', $error_msg);
      return false;
    }
  }

  function _in_you_go($user_id, $login_type) {
    if ($login_type == "longterm") {
      // set a cookie variable
      $this->load->module("site_cookies");
      $this->site_cookies->_set_cookie($user_id);
    } else if ($login_type == "shortterm") {
      // set a session variable
      $this->session->set_userdata("user_id", $user_id);
    }

    $this->_attempt_cart_divert($user_id);

    redirect("youraccount/welcome");
  }

  function test_set() { // for session
    $your_name = "david";
    $this->session->set_userdata("your_name", $your_name);
    echo "Session variable was set.";

    echo "<hr>";
    echo anchor("youraccount/test_get", "Get the session variable") . "<br>";
    echo anchor("youraccount/test_set", "Set the session variable") . "<br>";
    echo anchor("youraccount/test_destroy", "Unset (destroy) the session variable") . "<br>";
  }

  function test_get() { // for session
    $your_name = $this->session->userdata("your_name");

    if (!empty($your_name)) {
      echo "<h1>Hello $your_name</h1>";
    } else {
      echo "No session has been set for 'your_name'.";
    }

    echo "<hr>";
    echo anchor("youraccount/test_get", "Get the session variable") . "<br>";
    echo anchor("youraccount/test_set", "Set the session variable") . "<br>";
    echo anchor("youraccount/test_destroy", "Unset (destroy) the session variable") . "<br>";
  }

  function test_destroy() {
    unset($_SESSION['your_name']);
    echo "The session variable was unset.";

    echo "<hr>";
    echo anchor("youraccount/test_get", "Get the session variable") . "<br>";
    echo anchor("youraccount/test_set", "Set the session variable") . "<br>";
    echo anchor("youraccount/test_destroy", "Unset (destroy) the session variable") . "<br>";
  }

  function welcome() {
    $this->load->module("site_security");
    $this->site_security->_make_sure_logged_in();

    $this->load->module("site_settings");
    $is_mobile = $this->site_settings->is_mobile();

    if ($is_mobile) {
      $template = "public_jqm";
    } else {
      $template = "public_bootstrap";
    }

    $data["flash"] = $this->session->flashdata("item");
    $data["view_file"] = "welcome";

    $this->load->module("templates");
    $this->templates->$template($data);
  }

  function logout() {
    unset($_SESSION["user_id"]);
    $this->load->module("site_cookies");
    $this->site_cookies->_destroy_cookie();

    redirect(base_url());
  }

  function test() {
    $name = "Bob";
    $this->load->module("site_security");
    $hashed_name = $this->site_security->_hash_string($name);
    echo "Name is: $name<br>";
    echo "Hashed name is: $hashed_name<br>";

    $hashed_name_length = strlen($hashed_name);
    $start_point = $hashed_name_length - 6;
    $last_six_chars = substr($hashed_name, $start_point, 6);
    echo "Last six chars: $last_six_chars";
  }

  function _attempt_cart_divert($user_id) {
    $this->load->module("store_basket");

    $customer_session_id = $this->session->session_id;

    $col1 = "session_id";
    $value1 = $customer_session_id;
    $col2 = "shopper_id";
    $value2 = 0;    

    $query = $this->store_basket->get_with_double_condition($col1, $value1, $col2, $value2);
    $num_rows = $query->num_rows();

    if ($num_rows > 0) { // correct records
      $mysql_query = "update store_basket set shopper_id=$user_id where session_id='$customer_session_id'";
      $query = $this->store_basket->_custom_query($mysql_query);
      redirect("cart");
    }
  }

}
