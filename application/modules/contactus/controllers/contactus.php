<?php

class Contactus extends MX_Controller {

  public function __construct() {
    parent::__construct();
  }

  function index() {
    $this->load->module("site_settings");
    $is_mobile = $this->site_settings->is_mobile();

    $data = $this->_fetch_data_from_post();

    $data["our_address"] = $this->site_settings->_get_our_address();
    $data["our_telnum"] = $this->site_settings->_get_our_telnum();
    $data["our_name"] = $this->site_settings->_get_our_name();
    $data["map_code"] = $this->site_settings->_get_map_code();
    $data["form_location"] = base_url() . "contactus/submit";

    $data["flash"] = $this->session->flashdata("item");
    $data["view_file"] = "contactus";

    if ($is_mobile) {
      $template = "public_jqm";
      $data["view_file"] .= "_jqm";      
    } else {
      $template = "public_bootstrap";
    }

    $this->load->module("templates");
    $this->templates->$template($data);
  }

  function thankyou() {
    $this->load->module("site_settings");
    $is_mobile = $this->site_settings->is_mobile();

    if ($is_mobile) {
      $template = "public_jqm";
      $data["view_file"] = "thankyou_jqm";
    } else {
      $template = "public_bootstrap";
      $data["view_file"] = "thankyou";
    }

    $this->load->module("templates");
    $this->templates->$template($data);
  }

  function submit() {
    $_SESSION["end"] = time();

    $diff = ($_SESSION["end"] - $_SESSION["start"]);
    if ($diff < 1) {
      redirect("contactus/index"); // fast bot
    }

    $firstname = trim($this->input->post("firstname", true)); // hidden var
    if ($_SESSION['antispam_token'] != $firstname) {
      redirect("contactus/index"); // bot changed the value
    }

    $submit = $this->input->post("submit");

    if ($submit == "Submit") {
      $this->load->library("form_validation");
      $this->form_validation->set_rules("yourname", "Name", "required|max_length[60]");
      $this->form_validation->set_rules("email", "Email", "required|valid_email");
      $this->form_validation->set_rules("telnum", "Telephone number", "numeric|required|max_length[20]");
      $this->form_validation->set_rules("message", "Message", "required");

      if ($this->form_validation->run() == true) {
        $posted_data = $this->_fetch_data_from_post();

        $this->load->module("site_security");
        $this->load->module("enquiries");

        $data["sent_to"] = 0;
        $data["sent_by"] = 0;
        $data["subject"] = "Contact Form";
        $data["message"] = $this->build_msg($posted_data);
        $data["opened"] = 0;
        $data["code"] = $this->site_security->generate_random_string(6);
        $data["urgent"] = 0;
        $data["date_created"] = time();
        
        $this->enquiries->_insert($data);

        redirect("contactus/thankyou");
      } else {
        $this->index();
      }
    }
  }

  function _fetch_data_from_post() {
    $data["yourname"] = $this->input->post("yourname", true);
    $data["email"] = $this->input->post("email", true);
    $data["telnum"] = $this->input->post("telnum", true);
    $data["message"] = $this->input->post("message", true);

    return $data;
  }

  function build_msg($data) {
    $yourname = ucfirst($data["yourname"]);

    $msg = $yourname . " submitted the following information: <br><br>";
    $msg .= "Name: " . $yourname . "<br>";
    $msg .= "Email: " . $data["email"] . "<br>";
    $msg .= "Telephone Number: " . $data["telnum"] . "<br>";
    $msg .= "Message: " . $data["message"] . "<br>";

    return $msg;
  }

}
