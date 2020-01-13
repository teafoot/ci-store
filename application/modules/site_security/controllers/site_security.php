<?php

class Site_security extends MX_Controller {

  public function __construct() {
    parent::__construct();
  }

  function _check_admin_login_details($username, $pword) {
    $target_username = "admin";
    $target_pword = "password";

    if (($username == $target_username) && ($pword == $target_pword)) {
      return true;
    } else {
      return false;
    }
  }

  function _make_sure_is_admin() {
    $is_admin = $this->session->userdata("is_admin");

    if ($is_admin == 1) {
      return true;
    } else {      
      redirect("site_security/not_allowed");
    }
  }

  function not_allowed() {
    echo "You are not allowed to be here.";
  }

  function _hash_string($str) {
    $hashed_string = password_hash($str, PASSWORD_BCRYPT, array("cost" => 11));
    return $hashed_string;
  }

  function _verify_hash($plain_text_str, $hashed_str) {
  	$result = password_verify($plain_text_str, $hashed_str);
		return $result;
  }

  // function test() {
  //   // echo phpinfo();

  //   $name = "David";
  //   $hashed_name = $this->_hash_string($name);
  //   echo "Your name is $name" . "<br>" . "Your hashed name is $hashed_name";

  //   echo "<hr>";

		// $submitted_name = "Andy";
		// $result = $this->_verify_hash($submitted_name, $hashed_name);

  //   if ($result) {
  //     echo "pass";
  //   } else {
  //     echo "fail";
  //   }
  // }

  function generate_random_string($length) {
    $characters = "23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";
    $randomString = "";

    for ($i=0; $i < $length; $i++) { 
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $randomString;
  }

  // function test() { // for session
  //   $length = 32;
  //   echo $this->generate_random_string($length);
  // }

  function _get_user_id() {
    // check session and cookie
    $user_id = $this->session->userdata("user_id");

    if (!is_numeric($user_id)) {
      $this->load->module("site_cookies");
      $user_id = $this->site_cookies->_attempt_get_user_id();
    }

    return $user_id;
  }

  function _make_sure_logged_in() {
    $user_id = $this->_get_user_id();

    if (!is_numeric($user_id)) {
      redirect("youraccount/login");
    }
  }

  function _encrypt_string($str) {
    $this->load->library("encryption");
    $encrypted_string = $this->encryption->encrypt($str);

    return $encrypted_string;
  }

  function _decrypt_string($str) {
    $this->load->library("encryption");
    $decrypted_string = $this->encryption->decrypt($str);

    return $decrypted_string;
  }

}
