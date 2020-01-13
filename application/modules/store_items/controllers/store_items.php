<?php

class Store_items extends MX_Controller {

  public function __construct() {
    parent::__construct();

    $this->load->library('form_validation');
    $this->form_validation->CI = & $this;
  }

  function manage() {
    $this->load->library("session");
    $this->load->module("site_security");

    $this->site_security->_make_sure_is_admin();

    $data["flash"] = $this->session->flashdata("item");
    $data["query"] = $this->get("item_title");
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
      $this->form_validation->set_rules("item_title", "Item Title", "required|max_length[240]|callback_item_check");
      $this->form_validation->set_rules("item_price", "Item Price", "required|numeric");
      $this->form_validation->set_rules("was_price", "Was Price", "numeric");
      $this->form_validation->set_rules("status", "Status", "required|numeric");
      $this->form_validation->set_rules("item_description", "Item Description", "required");

      if ($this->form_validation->run() == true) {
        $data = $this->_fetch_data_from_post();
        $data["item_url"] = url_title($data["item_title"]);

        // Update/Insert to DB
        if (is_numeric($update_id)) {
          $this->_update($update_id, $data);
          $flash_msg = "The item details were successfully updated.";
        } else {
          $this->_insert($data);
          $update_id = $this->get_max(); // get the id of the new item.
          $flash_msg = "The item details was successfully added.";
        }

        $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
        $this->session->set_flashdata("item", $value);
        redirect("store_items/create/" . $update_id);
      }
    } else if ($submit == "Cancel") {
      redirect("store_items/manage");
    }

    // GET Request from URL
    if (is_numeric($update_id) && ($submit != "Submit")) {
      $data = $this->_fetch_data_from_db($update_id);
    } else {
      $data = $this->_fetch_data_from_post();
    }

    if (is_numeric($update_id)) {
      $data["headline"] = "Update item details";
    } else {
      $data["headline"] = "Add new item";
    }

    $data["got_gallery_pic"] = $this->_got_gallery_pic($update_id);

    $data["flash"] = $this->session->flashdata("item");
    $data["update_id"] = $update_id;
    $data["view_file"] = "create";
    $this->load->module("templates");
    $this->templates->admin($data);
  }

  function _fetch_data_from_post() {
    $data["item_title"] = $this->input->post("item_title", true);
    $data["item_price"] = $this->input->post("item_price", true);
    $data["was_price"] = $this->input->post("was_price", true);
    $data["item_description"] = $this->input->post("item_description", true);
    $data["status"] = $this->input->post("status", true);

    return $data;
  }

  function _fetch_data_from_db($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $query = $this->get_where($update_id);

    foreach ($query->result() as $row) {
      $data["item_title"] = $row->item_title;
      $data["item_url"] = $row->item_url;
      $data["item_price"] = $row->item_price;
      $data["item_description"] = $row->item_description;
      $data["big_pic"] = $row->big_pic;
      $data["small_pic"] = $row->small_pic;
      $data["was_price"] = $row->was_price;
      $data["status"] = $row->status;
    }

    return $data;
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
      redirect("store_items/create/" . $update_id);
    }

    $config['upload_path'] = './uploads/big_pics/';
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
      $this->_generate_thumbnail($file_name);

      $update_data["big_pic"] = $file_name;
      $update_data["small_pic"] = $file_name;
      $this->_update($update_id, $update_data);

      $data["headline"] = "Upload success";
      $data["update_id"] = $update_id;
      $data["view_file"] = "upload_success";

      $this->load->module("templates");
      $this->templates->admin($data);
    }
  }

  function _generate_thumbnail($file_name) {
    $config['image_library'] = 'gd2';
    $config['source_image'] = "./uploads/big_pics/" . $file_name;
    $config['new_image'] = "./uploads/small_pics/" . $file_name;
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
    $big_pic = $data["big_pic"];
    $small_pic = $data["small_pic"];

    $big_pic_path = "./uploads/big_pics/" . $big_pic;
    $small_pic_path = "./uploads/small_pics/" . $small_pic;

    if (file_exists($big_pic_path)) {
      unlink($big_pic_path);
    }

    if (file_exists($small_pic_path)) {
      unlink($small_pic_path);
    }

    $flash_msg = "The image was successfully deleted.";
    $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
    $this->session->set_flashdata("item", $value);

    unset($data);
    $data["big_pic"] = "";
    $data["small_pic"] = "";
    $this->_update($update_id, $data);

    redirect("store_items/create/" . $update_id);
  }

  function deleteconf($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $this->load->module("site_security");
    $this->site_security->_make_sure_is_admin();

    $data["headline"] = "Delete item";
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

      $flash_msg = "The item was successfully deleted.";
      $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
      $this->session->set_flashdata("item", $value);

      redirect("store_items/manage");
    } else if ($submit == "Cancel") {
      redirect("store_items/create/" . $update_id);
    }
  }

  function _process_delete($update_id) {
    // Delete: item colors, item sizes, item big pics, item small pics, item record.
    $this->load->module("store_item_colors");
    $this->store_item_colors->_delete_for_item($update_id);

    $this->load->module("store_item_sizes");
    $this->store_item_sizes->_delete_for_item($update_id);

    $data = $this->_fetch_data_from_db($update_id);
    $big_pic = $data["big_pic"];
    $small_pic = $data["small_pic"];

    $big_pic_path = "./uploads/big_pics/" . $big_pic;
    $small_pic_path = "./uploads/small_pics/" . $small_pic;

    if (file_exists($big_pic_path) && (!empty($big_pic))) {
      unlink($big_pic_path);
    }

    if (file_exists($small_pic_path) && (!empty($small_pic))) {
      unlink($small_pic_path);
    }

    unset($data);
    $data["big_pic"] = "";
    $data["small_pic"] = "";
    $this->_update($update_id, $data);

    $this->_delete($update_id);
  }

  function view($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $this->load->module("site_settings");
    $is_mobile = $this->site_settings->is_mobile();

    $data = $this->_fetch_data_from_db($update_id);

    $data["currency_symbol"] = $this->site_settings->_get_currency_symbol();

    $breadcrumbs_data["template"] = "public_bootstrap";
    $breadcrumbs_data["current_page_title"] = $data["item_title"];
    $breadcrumbs_data["breadcrumbs_array"] = $this->_generate_breadcrumbs_array($update_id);
    $data["breadcrumbs_data"] = $breadcrumbs_data;

    $data["use_featherlight"] = true;

    $query_gallery_pics = $this->_get_gallery_pics($update_id);
    $num_rows = $query_gallery_pics->num_rows();

    if ($num_rows > 0) {
      // We have at least one gallery picture
      $data["use_angularjs"] = true;

      // $count = 0;
      foreach ($query_gallery_pics->result() as $row) {
        $gallery_pics[] = base_url() . "uploads/item_galleries_pics/" . $row->picture;
        // $count++;
      }
      array_unshift($gallery_pics, base_url() . "uploads/big_pics/" . $data["big_pic"]); // add main picture to beginning
      $data["gallery_pics"] = $gallery_pics;

      $data["view_file"] = "view_gallery_version";
    } else {
      // Load a normal page
      $data["view_file"] = "view";
    }

    $data["flash"] = $this->session->flashdata("item");
    $data["update_id"] = $update_id;
    $data["view_module"] = "store_items";

    if ($is_mobile) {
      $template = "public_jqm";
      $data["view_file"] .= "_jqm"; // view_gallery_version_jqm or view_jqm
    } else {
      $template = "public_bootstrap";
    }

    $this->load->module("templates");
    $this->templates->$template($data);
  }

  function _get_item_id_from_item_url($item_url) {
    $query = $this->get_where_custom("item_url", $item_url);
    foreach ($query->result() as $row) {
      $item_id = $row->id;    
    }

    if (!isset($item_id)) {
      $item_id = 0;
    }

    return $item_id;
  }

  function _generate_breadcrumbs_array($update_id) {
    $this->load->module("store_categories");

    $homepage_url = base_url();

    $sub_cat_id = $this->_get_sub_cat_id($update_id);
    $sub_cat_title = $this->store_categories->_get_cat_title($sub_cat_id);
    $sub_cat_url = $this->store_categories->_get_full_cat_url($sub_cat_id);

    $breadcrumbs_array[$homepage_url] = "Home";
    $breadcrumbs_array[$sub_cat_url] = $sub_cat_title;

    return $breadcrumbs_array;
  }

  function _get_sub_cat_id($update_id) {

    $this->load->module("site_settings");
    $this->load->module("store_categories");

    if (!isset($_SERVER["HTTP_REFERER"])) {
      $refer_url = "";
    } else {
      $refer_url = $_SERVER["HTTP_REFERER"];    
    }
    
    $items_segments = $this->site_settings->_get_items_segments();
    $ditch_this = base_url() . $items_segments;
    $cat_url = str_replace($ditch_this, "", $refer_url);
    $sub_cat_id = $this->store_categories->_get_cat_id_from_cat_url($cat_url);

    if ($sub_cat_id > 0) {
      return $sub_cat_id;
    } else {
      $sub_cat_id = $this->_get_best_sub_cat_id($update_id);
      return $sub_cat_id;
    }
  }

  function _get_best_sub_cat_id($update_id) {
    // figure out which subcategory has the most items
    $this->load->module("store_cat_assign");
    $query = $this->store_cat_assign->get_where_custom("item_id", $update_id);

    foreach ($query->result() as $row) {
      $potential_sub_cats[] = $row->cat_id;
    }

    $num_sub_cats_for_item = count($potential_sub_cats);

    if ($num_sub_cats_for_item == 1) {
      $sub_cat_id = $potential_sub_cats[0];
      return $sub_cat_id;
    } else {
      foreach ($potential_sub_cats as $value) {
        $sub_cat_id = $value;
        $num_items_in_sub_cat = $this->store_cat_assign->count_where("cat_id", $sub_cat_id);
        $num_items_count[$sub_cat_id] = $num_items_in_sub_cat;
      }

      $sub_cat_id = $this->get_best_array_key($num_items_count);
      return $sub_cat_id;
    }

  }

  function get_best_array_key($target_array) {
    foreach ($target_array as $key => $value) {
      if (!isset($key_with_highest_value)) {
        $key_with_highest_value = $key;
      } else if ($value > $target_array[$key_with_highest_value]) {
        $key_with_highest_value = $key;
      }
    }

    return $key_with_highest_value;
  }

  function _get_title($update_id) {
    $data = $this->_fetch_data_from_db($update_id);
    $item_title = $data["item_title"];

    return $item_title;
  }

  function _got_gallery_pic($update_id) {
    $this->load->module("item_galleries");

    $query = $this->item_galleries->get_where_custom("parent_id", $update_id);
    $num_rows = $query->num_rows();

    if ($num_rows > 0) {
      return true; // we have at least one gallery picture for this item
    } else {
      return false;
    }
  }

  function _get_gallery_pics($update_id) {
    $this->load->module("item_galleries");
    $query = $this->item_galleries->get_where_custom("parent_id", $update_id);

    return $query;
  }

  //

  public function get($order_by) {
    $this->load->model("mdl_store_items");
    $query = $this->mdl_store_items->get($order_by);

    return $query;
  }

  public function get_with_limit($limit, $offset, $order_by) {
    $this->load->model("mdl_store_items");
    $query = $this->mdl_store_items->get_with_limit($limit, $offset, $order_by);

    return $query;
  }

  public function get_where($id) {
    $this->load->model("mdl_store_items");
    $query = $this->mdl_store_items->get_where($id);

    return $query;
  }

  public function get_where_custom($col, $value) {
    $this->load->model("mdl_store_items");
    $query = $this->mdl_store_items->get_where_custom($col, $value);

    return $query;
  }

  function _insert($data) {
    $this->load->model("mdl_store_items");
    $this->mdl_store_items->_insert($data);
  }

  function _update($id, $data) {
    $this->load->model("mdl_store_items");
    $this->mdl_store_items->_update($id, $data);
  }

  function _delete($id) {
    $this->load->model("mdl_store_items");
    $this->mdl_store_items->_delete($id);
  }

  public function count_where($column, $value) {
    $this->load->model("mdl_store_items");
    $count = $this->mdl_store_items->count_where($column, $value);

    return $count;
  }

  public function get_max() {
    $this->load->model("mdl_store_items");
    $max_id = $this->mdl_store_items->get_max();

    return $max_id;
  }

  function _custom_query($mysql_query) {
    $this->load->model("mdl_store_items");
    $query = $this->mdl_store_items->_custom_query($mysql_query);

    return $query;
  }

  //

  function item_check($str) {
    $item_url = url_title($str);
    $sql = "select * from store_items where item_title='$str' and item_url='$item_url'";

    $update_id = $this->uri->segment(3);

    if (is_numeric($update_id)) {
      $sql .= " and id!=$update_id";
    }

    $query = $this->_custom_query($sql);
    $num_rows = $query->num_rows();

    if ($num_rows > 0) {
      $this->form_validation->set_message('item_check', 'The item title that you submitted is already taken.');
      return false;
    } else {
      return true;
    }
  }

}
