<?php

class Enquiries extends MX_Controller {

  public function __construct() {
    parent::__construct();
  }

  function inbox() {
    $this->output->enable_profiler(true);

    $this->load->module("site_security");
    $this->site_security->_make_sure_is_admin();

    $folder_type = "inbox";
    $data["folder_type"] = ucfirst($folder_type);
    $data["query"] = $this->_fetch_enquiries($folder_type);

    $data["flash"] = $this->session->flashdata("item");
    $data["view_file"] = "view_enquiries";

    $this->load->module("templates");
    $this->templates->admin($data);
  }

  function _fetch_enquiries($folder_type) {
    // $mysql_query = "select * from enquiries where sent_to=0 order by date_created desc";
    $mysql_query = "
      SELECT
        enquiries.*,
        store_accounts.firstname,
        store_accounts.lastname,
        store_accounts.company
      FROM enquiries
        LEFT JOIN store_accounts
          ON enquiries.sent_by = store_accounts.id
      WHERE enquiries.sent_to = 0
      ORDER BY enquiries.date_created DESC
    ";
    $query = $this->_custom_query($mysql_query);

    return $query;
  }

  function view() {
    $this->load->module("site_security");
    $this->site_security->_make_sure_is_admin();

    $update_id = $this->uri->segment(3);
    $this->_set_to_opened($update_id);

    $options[''] = "Select a rank...";
    $options['1'] = "1 Star";
    $options['2'] = "2 Stars";
    $options['3'] = "3 Stars";
    $options['4'] = "4 Stars";
    $options['5'] = "5 Stars";
    $data["options"] = $options;

    $data["update_id"] = $update_id;
    $data["query"] = $this->get_where($update_id);
    $data["flash"] = $this->session->flashdata("item");
    $data["headline"] = "Enquiry ID: " . $update_id;
    $data["view_file"] = "view";

    $this->load->module("templates");
    $this->templates->admin($data);
  }

  function _set_to_opened($update_id) {
    $data["opened"] = 1;
    $this->_update($update_id, $data);
  }

  function create() {
    $this->load->library("session");

    $this->load->module("site_security");
    $this->site_security->_make_sure_is_admin();

    $this->load->module("timedate");

    $update_id = $this->uri->segment(3);
    $submit = $this->input->post("submit", true);

    // POST Request from form
    if ($submit == "Submit") {
      $this->load->library("form_validation");
      $this->form_validation->set_rules("sent_to", "Recipient", "required");
      $this->form_validation->set_rules("subject", "Subject", "required|max_length[250]");
      $this->form_validation->set_rules("message", "Message", "required");

      if ($this->form_validation->run() == true) {
        $data = $this->_fetch_data_from_post();
        $data["sent_by"] = 0; // admin
        $data["opened"] = 0;
        $data["date_created"] = time();
        $data["code"] = $this->site_security->generate_random_string(6);

        $this->_insert($data);

        $flash_msg = "The message was successfully sent.";
        $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
        $this->session->set_flashdata("item", $value);

        redirect("enquiries/inbox");
      }
    } else if ($submit == "Cancel") {
      redirect("enquiries/inbox");
    }

    // GET Request from URL
    if (is_numeric($update_id) && ($submit != "Submit")) {
      $data = $this->_fetch_data_from_db($update_id);
      $data["message"] = 
        "<br><br>--------------------------------<br><br>" .
        "The original message is shown below: <br><br>" . 
        $data["message"];
    } else {
      $data = $this->_fetch_data_from_post();
    }

    if (!is_numeric($update_id)) {
      $data["headline"] = "Compose new message";
    } else {
      $data["headline"] = "Reply to Message";
    }

    $data["options"] = $this->_fetch_customers_as_options();
    $data["flash"] = $this->session->flashdata("item");
    $data["update_id"] = $update_id;
    $data["view_file"] = "create";

    $this->load->module("templates");
    $this->templates->admin($data);
  }

  function _fetch_data_from_post() {
    $data["sent_to"] = $this->input->post("sent_to", true);
    $data["subject"] = $this->input->post("subject", true);
    $data["message"] = $this->input->post("message", true);

    return $data;
  }

  function _fetch_data_from_db($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $query = $this->get_where($update_id);

    foreach ($query->result() as $row) {
      $data["sent_to"] = $row->sent_to;
      $data["sent_by"] = $row->sent_by;
      $data["subject"] = $row->subject;
      $data["message"] = $row->message;
      $data["opened"] = $row->opened;
      $data["date_created"] = $row->date_created;
      $data["urgent"] = $row->urgent;
    }

    return $data;
  }

  function _fetch_customers_as_options() {
    // for dropdown menu
    $options[''] = "Select customer...";

    $this->load->module("store_accounts");
    $query = $this->store_accounts->get("lastname");

    foreach ($query->result() as $row) {
      $customer_name = trim($row->firstname . " " . $row->lastname);
      $company = $row->company;
      $company_length = strlen($company);

      if ($company_length > 2) {
        $customer_name .= " from " . $company;
      }

      if (!empty($customer_name)) {
        $options[$row->id] = $customer_name;
      }
    }

    return $options;
  }

  // function test() {
  //   $firstname = "ben";
  //   $lastname = "kriss";
  //   $this->say_my_name($firstname);
  // }

  // function say_my_name($firstname, $lastname = null) { // default values
  //   if (!isset($lastname)) {
  //     echo "hello $firstname";
  //   } else {
  //     echo "hello $firstname $lastname";
  //   }
  // }

  function _draw_customer_inbox($customer_id) {
    $data["customer_id"] = $customer_id;

    $folder_type = "inbox";
    $data["folder_type"] = ucfirst($folder_type);
    $data["query"] = $this->_fetch_customer_enquiries($folder_type, $customer_id);

    $data["flash"] = $this->session->flashdata("item");

    $this->load->module("site_settings");
    $is_mobile = $this->site_settings->is_mobile();

    if ($is_mobile) {
      $view_file = "customer_inbox_jqm";
    } else {
      $view_file = "customer_inbox";
    }

    $this->load->view($view_file, $data);
  }

  function _fetch_customer_enquiries($folder_type, $customer_id) {
    $mysql_query = "
      SELECT
        enquiries.*,
        store_accounts.firstname,
        store_accounts.lastname,
        store_accounts.company
      FROM enquiries
        INNER JOIN store_accounts
          ON enquiries.sent_to = store_accounts.id
      WHERE enquiries.sent_to = $customer_id
      ORDER BY enquiries.date_created DESC
    ";
    $query = $this->_custom_query($mysql_query);

    return $query;
  }

  // function fix() { // adding code to enquiries
  //   $this->load->module("site_security");

  //   $query = $this->get("id");

  //   foreach ($query->result() as $row) {
  //     $data["code"] = $this->site_security->generate_random_string(6);
  //     $this->_update($row->id, $data);
  //   }

  //   echo "finished";
  // }

  function _attempt_get_data_from_code($customer_id, $code) {
    // make sure code is valid and customer is allowed to view/respond

    $query = $this->get_where_custom("code", $code);
    $num_rows = $query->num_rows();
    
    foreach ($query->result() as $row) {
      $data["sent_to"] = $row->sent_to;
      $data["sent_by"] = $row->sent_by;
      $data["subject"] = $row->subject;
      $data["message"] = $row->message;
      $data["opened"] = $row->opened;
      $data["date_created"] = $row->date_created;
      $data["urgent"] = $row->urgent;
    }

    if (($num_rows < 1) or ($customer_id != $data["sent_to"])) {
      redirect("site_security/not_allowed");
    }

    return $data;
  }

  function submit_ranking() {
    $this->load->module("site_security");
    $this->site_security->_make_sure_is_admin();
    
    $submit = $this->input->post("submit", true);

    if ($submit == "Cancel") {
      redirect("enquiries/inbox");
    }

    $update_id = $this->uri->segment(3);
    $data["ranking"] = $this->input->post("ranking", true);
    $this->_update($update_id, $data);

    $flash_msg = "The ranking was successfully set.";
    $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
    $this->session->set_flashdata("item", $value);
    
    redirect("enquiries/view/" . $update_id);
  }

  //

  public function get($order_by) {
    $this->load->model("mdl_enquiries");
    $query = $this->mdl_enquiries->get($order_by);

    return $query;
  }

  public function get_with_limit($limit, $offset, $order_by) {
    $this->load->model("mdl_enquiries");
    $query = $this->mdl_enquiries->get_with_limit($limit, $offset, $order_by);

    return $query;
  }

  public function get_where($id) {
    $this->load->model("mdl_enquiries");
    $query = $this->mdl_enquiries->get_where($id);

    return $query;
  }

  public function get_with_double_condition($col1, $value1, $col2, $value2) {
    $this->load->model("mdl_enquiries");
    $query = $this->mdl_enquiries->get_with_double_condition($col1, $value1, $col2, $value2);

    return $query;
  }

  public function get_where_custom($col, $value) {
    $this->load->model("mdl_enquiries");
    $query = $this->mdl_enquiries->get_where_custom($col, $value);

    return $query;
  }

  function _insert($data) {
    $this->load->model("mdl_enquiries");
    $this->mdl_enquiries->_insert($data);
  }

  function _update($id, $data) {
    $this->load->model("mdl_enquiries");
    $this->mdl_enquiries->_update($id, $data);
  }

  function _delete($id) {
    $this->load->model("mdl_enquiries");
    $this->mdl_enquiries->_delete($id);
  }

  public function count_where($column, $value) {
    $this->load->model("mdl_enquiries");
    $count = $this->mdl_enquiries->count_where($column, $value);

    return $count;
  }

  public function get_max() {
    $this->load->model("mdl_enquiries");
    $max_id = $this->mdl_enquiries->get_max();

    return $max_id;
  }

  function _custom_query($mysql_query) {
    $this->load->model("mdl_enquiries");
    $query = $this->mdl_enquiries->_custom_query($mysql_query);

    return $query;
  }

}
