<?php

class Webpages extends MX_Controller {

  public function __construct() {
    parent::__construct();
  }

  function manage() {
    $this->load->library("session");
    $this->load->module("site_security");

    $this->site_security->_make_sure_is_admin();

    $data["flash"] = $this->session->flashdata("item");
    $data["query"] = $this->get("page_url");
    $data["view_file"] = "manage";

    $this->load->module("templates");
    $this->templates->admin($data);
  }

  function create() {
    $this->load->module("site_security");
    $this->load->library("session");

    $this->site_security->_make_sure_is_admin();

    $update_id = $this->uri->segment(3);
    $submit = $this->input->post("submit", true);

    // POST Request from form
    if ($submit == "Submit") {
      $this->load->library("form_validation");
      $this->form_validation->set_rules("page_title", "Page Title", "required|max_length[250]");
      $this->form_validation->set_rules("page_content", "Page Content", "required");

      if ($this->form_validation->run() == true) {
        $data = $this->_fetch_data_from_post();
        $data["page_url"] = url_title($data["page_title"]);

        // Update/Insert to DB
        if (is_numeric($update_id)) {
          if ($update_id == 1 || $update_id == 2) {
            unset($data["page_url"]);
          }

          $this->_update($update_id, $data);
          $flash_msg = "The page details were successfully updated.";
        } else {
          $this->_insert($data);
          $update_id = $this->get_max(); // get the id of the new item.
          $flash_msg = "The page details were successfully added.";
        }

        $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
        $this->session->set_flashdata("item", $value);
        redirect("webpages/create/" . $update_id);
      }
    } else if ($submit == "Cancel") {
      redirect("webpages/manage");
    }

    // GET Request from URL
    if (is_numeric($update_id) && ($submit != "Submit")) {
      $data = $this->_fetch_data_from_db($update_id);
    } else {
      $data = $this->_fetch_data_from_post();
    }

    if (is_numeric($update_id)) {
      $data["headline"] = "Update page details";
    } else {
      $data["headline"] = "Create new page";
    }
    $data["flash"] = $this->session->flashdata("item");
    $data["update_id"] = $update_id;
    $data["view_file"] = "create";

    $this->load->module("templates");
    $this->templates->admin($data);
  }

  function _fetch_data_from_post() {
    $data["page_title"] = $this->input->post("page_title", true);
    $data["page_keywords"] = $this->input->post("page_keywords", true);
    $data["page_description"] = $this->input->post("page_description", true);
    $data["page_content"] = $this->input->post("page_content", true);

    return $data;
  }

  function _fetch_data_from_db($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $query = $this->get_where($update_id);

    foreach ($query->result() as $row) {
      $data["page_title"] = $row->page_title;
      $data["page_keywords"] = $row->page_keywords;
      $data["page_description"] = $row->page_description;
      $data["page_content"] = $row->page_content;
      $data["page_url"] = $row->page_url;
    }

    return $data;
  }

  function deleteconf($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    } else if ($update_id == 1 || $update_id == 2) { // prevent deletion of home and contact us page
      redirect("site_security/not_allowed");
    }

    $this->load->module("site_security");
    $this->site_security->_make_sure_is_admin();

    $data["headline"] = "Delete page";
    $data["update_id"] = $update_id;
    $data["view_file"] = "deleteconf";

    $this->load->module("templates");
    $this->templates->admin($data);
  }

  function delete($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $this->load->library("session");
    $this->load->module("site_security");

    $this->site_security->_make_sure_is_admin();

    $submit = $this->input->post("submit", true);

    if ($submit == "Delete") {
      $this->_process_delete($update_id);

      $flash_msg = "The page was successfully deleted.";
      $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
      $this->session->set_flashdata("item", $value);

      redirect("webpages/manage");
    } else if ($submit == "Cancel") {
      redirect("webpages/create/" . $update_id);
    }
  }

  function _process_delete($update_id) {
    $this->_delete($update_id);
  }

  //

  public function get($order_by) {
    $this->load->model("mdl_webpages");
    $query = $this->mdl_webpages->get($order_by);

    return $query;
  }

  public function get_with_limit($limit, $offset, $order_by) {
    $this->load->model("mdl_webpages");
    $query = $this->mdl_webpages->get_with_limit($limit, $offset, $order_by);

    return $query;
  }

  public function get_where($id) {
    $this->load->model("mdl_webpages");
    $query = $this->mdl_webpages->get_where($id);

    return $query;
  }

  public function get_where_custom($col, $value) {
    $this->load->model("mdl_webpages");
    $query = $this->mdl_webpages->get_where_custom($col, $value);

    return $query;
  }

  function _insert($data) {
    $this->load->model("mdl_webpages");
    $this->mdl_webpages->_insert($data);
  }

  function _update($id, $data) {
    $this->load->model("mdl_webpages");
    $this->mdl_webpages->_update($id, $data);
  }

  function _delete($id) {
    $this->load->model("mdl_webpages");
    $this->mdl_webpages->_delete($id);
  }

  public function count_where($column, $value) {
    $this->load->model("mdl_webpages");
    $count = $this->mdl_webpages->count_where($column, $value);

    return $count;
  }

  public function get_max() {
    $this->load->model("mdl_webpages");
    $max_id = $this->mdl_webpages->get_max();

    return $max_id;
  }

  function _custom_query($mysql_query) {
    $this->load->model("mdl_webpages");
    $query = $this->mdl_webpages->_custom_query($mysql_query);

    return $query;
  }

}
