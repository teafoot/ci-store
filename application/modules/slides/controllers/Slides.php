<?php

class Slides extends MX_Controller {

  public function __construct() {
    parent::__construct();
  }

  function update_group($parent_id) {
    // manage records belonging to a parent
    $this->load->module("site_security");
    $this->load->library("session");

    $this->site_security->_make_sure_is_admin();

    $data["query"] = $this->get_where_custom("parent_id", $parent_id);
    $data["num_rows"] = $data["query"]->num_rows();

    $data["parent_id"] = $parent_id;
    $data["headline"] = $this->_get_update_group_headline($parent_id);
    $data["entity_name"] = $this->_get_entity_name("plural");
    $data["parent_title"] = $this->_get_parent_title($parent_id);

    $data["flash"] = $this->session->flashdata("item");
    $data["view_file"] = "update_group";
    $this->load->module("templates");
    $this->templates->admin($data);
  }

  function _draw_create_modal($parent_id) {
    // modal for creating new record
    $data["parent_id"] = $parent_id;
    $this->load->view("create_modal", $data);
  }

  function submit_create() {
    // form submitted, create new record
    $this->load->module("site_security");
    $this->site_security->_make_sure_is_admin();

    $data["parent_id"] = $this->input->post("parent_id", true);
    $data["target_url"] = $this->input->post("target_url", true);
    $data["alt_text"] = $this->input->post("alt_text", true);
    $this->_insert($data);

    $max_id = $this->get_max();
    redirect("slides/view/" . $max_id);
  }

  function view($update_id) {
    // view details of this record in a form
    $this->load->module("site_security");
    $this->site_security->_make_sure_is_admin();

    $update_id = $this->uri->segment(3);
    $submit = $this->input->post("submit", true);

    if ($submit == "Cancel") {
      redirect("slides/update_group/" . $parent_id);
    }

    // GET Request from URL
    if (is_numeric($update_id) && ($submit != "Submit")) {
      $data = $this->_fetch_data_from_db($update_id);
    } else {
      $data = $this->_fetch_data_from_post();
      $data["picture"] = "";
    }

    $entity_name = ucfirst($this->_get_entity_name("singular"));
    $data["entity_name"] = $entity_name;
    $data["headline"] = "Update " . $entity_name;

    $data["update_id"] = $update_id;
    $data["view_file"] = "view";
    $data["flash"] = $this->session->flashdata("item");
    $this->load->module("templates");
    $this->templates->admin($data);
  }

  function _draw_img_btn($update_id) {
    // draw 'upload image' button on top of the view page
    $data = $this->_fetch_data_from_db($update_id);
    
    $picture = $data["picture"];
    if ($picture == "") {
      $data["got_pic"] = false;
      $data["btn_style"] = "";
      $data["btn_info"] = "No picture has been uploaded so far for this slide.";
    } else {
      $data["got_pic"] = true;
      $data["btn_style"] = 'style="clear: both; margin-top: 24px;"';
      $data["btn_info"] = "The picture that is being used for this slide is shown below.";
      $data["pic_path"] = base_url() . "uploads/slider_pics/" . $picture;
    }

    $data["update_id"] = $update_id;
    $this->load->view("img_btn", $data);
  }

  function upload_image($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $this->load->module("site_security");
    $this->site_security->_make_sure_is_admin();

    $data["headline"] = "Upload image";
    $data["update_id"] = $update_id;
    $data["view_file"] = "upload_image";
    $this->load->module("templates");
    $this->templates->admin($data);
  }

  public function do_upload($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $this->load->module("site_security");
    $this->site_security->_make_sure_is_admin();

    $submit = $this->input->post("submit", true);

    if ($submit == "Cancel") {
      $parent_id = $this->_get_parent_id($update_id);
      redirect("slides/update_group/" . $parent_id);
    }

    $config['upload_path'] = './uploads/slider_pics/';
    $config['allowed_types'] = 'gif|jpg|png';
    $config['max_size'] = 1000; // kb
    $config['max_width'] = 1024;
    $config['max_height'] = 768;

    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('userfile')) {
      $data["error"] = array('error' => $this->upload->display_errors("<p style='color: red;'>", "</p>"));

      $data["headline"] = "Upload error";
      $data["update_id"] = $update_id;
      $data["view_file"] = "upload_image";
      $this->load->module("templates");
      $this->templates->admin($data);
    } else {
      $data = array("upload_data" => $this->upload->data());

      $file_name = $data["upload_data"]["file_name"];

      $update_data["picture"] = $file_name;
      $this->_update($update_id, $update_data);

      redirect("slides/view/" . $update_id);
    }
  }

  function deleteconf($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $this->load->module("site_security");
    $this->site_security->_make_sure_is_admin();

    $entity_name = ucfirst($this->_get_entity_name("singular"));
    $data["headline"] = "Delete " . $entity_name;

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
      $parent_id = $this->_get_parent_id($update_id);
      $this->_process_delete($update_id);

      $entity_name = $this->_get_entity_name("singular");
      $flash_msg = "The " . $entity_name . " was successfully deleted.";
      $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
      $this->session->set_flashdata("item", $value);

      redirect("slides/update_group/" . $parent_id);
    } else if ($submit == "Cancel") {
      redirect("slides/view/" . $update_id);
    }
  }

  function _process_delete($update_id) {
    $data = $this->_fetch_data_from_db($update_id);
    $picture = $data["picture"];
    $picture_path = "./uploads/slider_pics/" . $picture;

    if (file_exists($picture_path) && (!empty($picture))) {
      unlink($picture_path);
    }

    unset($data);
    $data["picture"] = "";
    $this->_update($update_id, $data);

    $this->_delete($update_id);
  }

  function submit($update_id) {
    // update the record that has been submitted via /view
    $this->load->module("site_security");
    $this->site_security->_make_sure_is_admin();

    $submit = $this->input->post("submit", true);
    $target_url = $this->input->post("target_url", true);
    $alt_text = $this->input->post("alt_text", true);

    if ($submit == "Cancel") {
      $parent_id = $this->_get_parent_id($update_id);
      redirect("slides/update_group/" . $parent_id);
    } else if ($submit == "Submit") {
      $data["target_url"] = $target_url;
      $data["alt_text"] = $alt_text;
      $this->_update($update_id, $data);

      redirect("slides/view/" . $update_id);
    }
  }

  //

  function _get_parent_title($parent_id) {
    $parent_module_name = "sliders";
    $this->load->module($parent_module_name);

    $parent_title = $this->$parent_module_name->_get_title($parent_id);

    return $parent_title;
  }

  function _get_entity_name($type) {
    // Note: $type can be singular or plural
    if ($type == "singular") {
      $entity_name = "slide";
    } else {
      $entity_name = "slides";      
    }

    return $entity_name;
  }

  function _get_update_group_headline($parent_id) {
    $parent_title = ucfirst($this->_get_parent_title($parent_id));
    $entity_name = ucfirst($this->_get_entity_name("plural"));

    $headline = "Update: " . $entity_name . " for " . $parent_title;

    return $headline;
  }

  function _get_parent_id($update_id) {
    $data = $this->_fetch_data_from_db($update_id);
    $parent_id = $data["parent_id"];

    return $parent_id;
  }

  function _fetch_data_from_post() {
    $data["parent_id"] = $this->input->post("parent_id", true);
    $data["target_url"] = $this->input->post("target_url", true);
    $data["alt_text"] = $this->input->post("alt_text", true);

    return $data;
  }

  function _fetch_data_from_db($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $query = $this->get_where($update_id);
    foreach ($query->result() as $row) {
      $data["parent_id"] = $row->parent_id;
      $data["picture"] = $row->picture;
      $data["target_url"] = $row->target_url;
      $data["alt_text"] = $row->alt_text;
    }

    return $data;
  }

  //

  public function get($order_by) {
    $this->load->model("mdl_slides");
    $query = $this->mdl_slides->get($order_by);

    return $query;
  }

  public function get_with_limit($limit, $offset, $order_by) {
    $this->load->model("mdl_slides");
    $query = $this->mdl_slides->get_with_limit($limit, $offset, $order_by);

    return $query;
  }

  public function get_where($id) {
    $this->load->model("mdl_slides");
    $query = $this->mdl_slides->get_where($id);

    return $query;
  }

  public function get_where_custom($col, $value) {
    $this->load->model("mdl_slides");
    $query = $this->mdl_slides->get_where_custom($col, $value);

    return $query;
  }

  function _insert($data) {
    $this->load->model("mdl_slides");
    $this->mdl_slides->_insert($data);
  }

  function _update($id, $data) {
    $this->load->model("mdl_slides");
    $this->mdl_slides->_update($id, $data);
  }

  function _delete($id) {
    $this->load->model("mdl_slides");
    $this->mdl_slides->_delete($id);
  }

  public function count_where($column, $value) {
    $this->load->model("mdl_slides");
    $count = $this->mdl_slides->count_where($column, $value);

    return $count;
  }

  public function get_max() {
    $this->load->model("mdl_slides");
    $max_id = $this->mdl_slides->get_max();

    return $max_id;
  }

  function _custom_query($mysql_query) {
    $this->load->model("mdl_slides");
    $query = $this->mdl_slides->_custom_query($mysql_query);

    return $query;
  }

}
