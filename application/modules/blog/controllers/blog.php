<?php

class Blog extends MX_Controller {

  public function __construct() {
    parent::__construct();
  }

  function manage() {
    $this->load->library("session");
    $this->load->module("site_security");

    $this->site_security->_make_sure_is_admin();

    $data["flash"] = $this->session->flashdata("item");
    $data["query"] = $this->get("date_published desc");
    $data["view_file"] = "manage";

    $this->load->module("templates");
    $this->templates->admin($data);
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
      $this->form_validation->set_rules("author", "Author", "required");
      $this->form_validation->set_rules("blog_title", "Blog Title", "required|max_length[250]");
      $this->form_validation->set_rules("blog_content", "Blog Content", "required");
      $this->form_validation->set_rules("date_published", "Date Published", "required");

      if ($this->form_validation->run() == true) {
        $data = $this->_fetch_data_from_post();
        $data["blog_url"] = url_title($data["blog_title"]);
        $data["date_published"] = $this->timedate->make_timestamp_from_datepicker_us($data["date_published"]); // convert datepicker into unix timestamp

        // Update/Insert to DB
        if (is_numeric($update_id)) {
          $this->_update($update_id, $data);
          $flash_msg = "The blog entry details were successfully updated.";
        } else {
          $this->_insert($data);
          $update_id = $this->get_max(); // get the id of the new item.
          $flash_msg = "The blog entry was successfully created.";
        }

        $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
        $this->session->set_flashdata("item", $value);
        redirect("blog/create/" . $update_id);
      }
    } else if ($submit == "Cancel") {
      redirect("blog/manage");
    }

    // GET Request from URL
    if (is_numeric($update_id) && ($submit != "Submit")) {
      $data = $this->_fetch_data_from_db($update_id);
    } else {
      $data = $this->_fetch_data_from_post();
    }

    if (is_numeric($update_id)) {
      $data["headline"] = "Update blog details";
    } else {
      $data["headline"] = "Create new blog";
    }

    if ($data["date_published"] > 0) {
      // Convert unix timestamp to datepicker format
      $data["date_published"] = $this->timedate->get_nice_date($data["date_published"], "datepicker_us");
    }

    $data["flash"] = $this->session->flashdata("item");
    $data["update_id"] = $update_id;
    $data["view_file"] = "create";

    $this->load->module("templates");
    $this->templates->admin($data);
  }

  function _fetch_data_from_post() {
    $data["author"] = $this->input->post("author", true);
    $data["blog_title"] = $this->input->post("blog_title", true);
    $data["blog_keywords"] = $this->input->post("blog_keywords", true);
    $data["blog_description"] = $this->input->post("blog_description", true);
    $data["blog_content"] = $this->input->post("blog_content", true);
    $data["date_published"] = $this->input->post("date_published", true);

    return $data;
  }

  function _fetch_data_from_db($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $query = $this->get_where($update_id);

    foreach ($query->result() as $row) {
      $data["author"] = $row->author;
      $data["blog_title"] = $row->blog_title;
      $data["blog_keywords"] = $row->blog_keywords;
      $data["blog_description"] = $row->blog_description;
      $data["blog_content"] = $row->blog_content;
      $data["blog_url"] = $row->blog_url;
      $data["picture"] = $row->picture;
      $data["date_published"] = $row->date_published;
    }

    return $data;
  }

  function deleteconf($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $this->load->module("site_security");
    $this->site_security->_make_sure_is_admin();

    $data["headline"] = "Delete blog";
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

      $flash_msg = "The blog was successfully deleted.";
      $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
      $this->session->set_flashdata("item", $value);

      redirect("blog/manage");
    } else if ($submit == "Cancel") {
      redirect("blog/create/" . $update_id);
    }
  }

  function _process_delete($update_id) {
    $this->_delete($update_id);
  }

  // function test() {
  //   $this->load->module("timedate");

  //   $now = time(); // unix
  //   $datepicker = $this->timedate->get_nice_date($now, "datepicker_us"); // datepicker
  //   $timestamp = $this->timedate->make_timestamp_from_datepicker_us($datepicker); // unix
  //   $cool_date = $this->timedate->get_nice_date($timestamp, "cool"); // datepicker

  //   echo $now;
  //   echo "<hr>";
  //   echo $datepicker;
  //   echo "<hr>";
  //   echo $timestamp;
  //   echo "<hr>";
  //   echo $cool_date;

  // }

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
      redirect("blog/create/" . $update_id);
    }

    $config['file_name'] = $this->site_security->generate_random_string(16);
    $config['upload_path'] = './uploads/blog_pics/';
    $config['allowed_types'] = 'gif|jpg|png';
    $config['max_size'] = 1000; // kb
    $config['max_width'] = 2024;
    $config['max_height'] = 1468;

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

      $raw_name = $data["upload_data"]["raw_name"];
      $file_ext = $data["upload_data"]["file_ext"];
      $file_name = $data["upload_data"]["file_name"];

      $thumbnail_name = $raw_name . "_thumb" . $file_ext;

      $this->_generate_thumbnail($file_name, $thumbnail_name);

      $update_data["picture"] = $file_name;
      $this->_update($update_id, $update_data);

      $data["headline"] = "Upload success";
      $data["update_id"] = $update_id;
      $data["view_file"] = "upload_success";

      $this->load->module("templates");
      $this->templates->admin($data);
    }
  }

  function _generate_thumbnail($file_name, $thumbnail_name) {
    $config['image_library'] = 'gd2';
    $config['source_image'] = "./uploads/blog_pics/" . $file_name;
    $config['new_image'] = "./uploads/blog_pics/" . $thumbnail_name;
    $config['maintain_ratio'] = TRUE;
    $config['width'] = 200;
    $config['height'] = 200;

    $this->load->library('image_lib', $config);

    $this->image_lib->resize();
  }

  public function delete_image($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $this->load->module("site_security");
    $this->site_security->_make_sure_is_admin();

    $data = $this->_fetch_data_from_db($update_id);
    $picture = $data["picture"];

    $big_blog_pic_path = "./uploads/blog_pics/" . $picture;
    $small_blog_pic_path = "./uploads/blog_pics/" . str_replace('.', '_thumb.', $picture);

    if (file_exists($big_blog_pic_path)) {
      unlink($big_blog_pic_path);
    }

    if (file_exists($small_blog_pic_path)) {
      unlink($small_blog_pic_path);
    }

    $flash_msg = "The image was successfully deleted.";
    $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
    $this->session->set_flashdata("item", $value);

    unset($data);
    $data["picture"] = "";
    $this->_update($update_id, $data);

    redirect("blog/create/" . $update_id);
  }

  function _draw_feed_homepage($is_mobile = false) {
    $this->load->helper("text");

    $mysql_query = "select * from blog order by date_published desc limit 0,3";
    $data["query"] = $this->_custom_query($mysql_query);

    if ($is_mobile) {
      $view_file = "feed_homepage_jqm";
    } else {
      $view_file = "feed_homepage";
    }

    $this->load->view($view_file, $data);
  }

  //

  public function get($order_by) {
    $this->load->model("mdl_blog");
    $query = $this->mdl_blog->get($order_by);

    return $query;
  }

  public function get_with_limit($limit, $offset, $order_by) {
    $this->load->model("mdl_blog");
    $query = $this->mdl_blog->get_with_limit($limit, $offset, $order_by);

    return $query;
  }

  public function get_where($id) {
    $this->load->model("mdl_blog");
    $query = $this->mdl_blog->get_where($id);

    return $query;
  }

  public function get_where_custom($col, $value) {
    $this->load->model("mdl_blog");
    $query = $this->mdl_blog->get_where_custom($col, $value);

    return $query;
  }

  function _insert($data) {
    $this->load->model("mdl_blog");
    $this->mdl_blog->_insert($data);
  }

  function _update($id, $data) {
    $this->load->model("mdl_blog");
    $this->mdl_blog->_update($id, $data);
  }

  function _delete($id) {
    $this->load->model("mdl_blog");
    $this->mdl_blog->_delete($id);
  }

  public function count_where($column, $value) {
    $this->load->model("mdl_blog");
    $count = $this->mdl_blog->count_where($column, $value);

    return $count;
  }

  public function get_max() {
    $this->load->model("mdl_blog");
    $max_id = $this->mdl_blog->get_max();

    return $max_id;
  }

  function _custom_query($mysql_query) {
    $this->load->model("mdl_blog");
    $query = $this->mdl_blog->_custom_query($mysql_query);

    return $query;
  }

}
