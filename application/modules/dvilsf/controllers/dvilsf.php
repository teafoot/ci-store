<?php

class Dvilsf extends MX_Controller {

  public function __construct() {
    parent::__construct();

    $this->load->library('form_validation');
    $this->form_validation->CI = & $this;
  }

  function index() {
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
        $this->_in_you_go();
      } else {
        $this->index();
      }
    }
  }

  function username_check($str) {
    $this->load->module("site_security");

    $pword = $this->input->post("pword", true);
    $result = $this->site_security->_check_admin_login_details($str, $pword);

    if ($result == false) {
      $error_msg = "You did not enter a correct username/password.";
      $this->form_validation->set_message('username_check', $error_msg);
      return false;
    } else {
      return true;
    }
  }

  function _in_you_go() {    
    $this->session->set_userdata("is_admin", 1);    

    redirect("dashboard/home");
  }

  function logout() {
    unset($_SESSION["is_admin"]);

    redirect(base_url());
  }

}
