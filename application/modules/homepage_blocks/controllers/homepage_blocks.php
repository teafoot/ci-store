<?php

class Homepage_blocks extends MX_Controller {

  public function __construct() {
    parent::__construct();
  }

  function manage() {
    $this->load->library("session");
    $this->load->module("site_security");

    $this->site_security->_make_sure_is_admin();

    $data["sort_this"] = true;
    $data["view_file"] = "manage";
    $data["flash"] = $this->session->flashdata("item");

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
      $this->form_validation->set_rules("block_title", "Homepage Offer Title", "required|max_length[240]");

      if ($this->form_validation->run() == true) {
        $data = $this->_fetch_data_from_post();

        // Update/Insert to DB
        if (is_numeric($update_id)) {
          $this->_update($update_id, $data);
          $flash_msg = "The homepage offer details were successfully updated.";
        } else {
          $this->_insert($data);
          $update_id = $this->get_max(); // get the id of the new item.
          $flash_msg = "The homepage offer details were successfully added.";
        }

        $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
        $this->session->set_flashdata("item", $value);

        redirect("homepage_blocks/create/" . $update_id);
      }
    } else if ($submit == "Cancel") {
      redirect("homepage_blocks/manage");
    }

    // GET Request from URL
    if (is_numeric($update_id) && ($submit != "Submit")) {
      $data = $this->_fetch_data_from_db($update_id);
    } else {
      $data = $this->_fetch_data_from_post();
    }

    if (is_numeric($update_id)) {
      $block_title = $this->_get_block_title($update_id);
      $data["headline"] = "Update " . $block_title;
    } else {
      $data["headline"] = "Create new homepage offer";
    }
    $data["update_id"] = $update_id;
    $data["view_file"] = "create";
    $data["flash"] = $this->session->flashdata("item");

    $this->load->module("templates");
    $this->templates->admin($data);
  }

  function _fetch_data_from_post() {
    $data["block_title"] = $this->input->post("block_title", true);

    return $data;
  }

  function _fetch_data_from_db($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $query = $this->get_where($update_id);

    foreach ($query->result() as $row) {
      $data["block_title"] = $row->block_title;
    }

    return $data;
  }

  function _get_block_title($update_id) {
    $data = $this->_fetch_data_from_db($update_id);
    $block_title = $data["block_title"];
    return $block_title;
  }

  function sort() {
    $this->load->module("site_security");
    $this->site_security->_make_sure_is_admin();

    $list_size = $this->input->post("list_size", true);

    for ($i = 1; $i <= $list_size; $i++) { 
      $data["priority"] = $i; 
      $update_id = $_POST["list_item_" . $i];
      $this->_update($update_id, $data);
    }
    
  }

  function view($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    // fetch the homepage offer details
    $data = $this->_fetch_data_from_db($update_id);

    // count the items that belong to this homepage offer
    $use_limit = false;
    $mysql_query = $this->_generate_mysql_query($update_id, $use_limit);
    $query = $this->_custom_query($mysql_query);
    $total_items = $query->num_rows();

    // fetch the items that belong to this homepage offer
    $use_limit = true;
    $mysql_query = $this->_generate_mysql_query($update_id, $use_limit);
    $data["query"] = $this->_custom_query($mysql_query);

    $this->load->module("site_settings");
    $data["currency_symbol"] = $this->site_settings->_get_currency_symbol();
    $data["item_segments"] = $this->site_settings->_get_item_segments();

    $this->load->module("custom_pagination");
    $pagination_data["template"] = "public_bootstrap"; 
    $pagination_data["target_base_url"] = $this->get_target_pagination_base_url(); 
    $pagination_data["total_rows"] = $total_items; 
    $pagination_data["offset_segment"] = 4; // from url
    $pagination_data["limit"] = $this->get_limit();
    $data["pagination"] = $this->custom_pagination->_generate_pagination($pagination_data);
    //
    $pagination_data["offset"] = $this->get_offset();
    $data["showing_statement"] = $this->custom_pagination->get_showing_statement($pagination_data);

    $data["flash"] = $this->session->flashdata("item");
    $data["update_id"] = $update_id;
    $data["view_module"] = "homepage_blocks";
    $data["view_file"] = "view";

    $this->load->module("templates");
    $this->templates->public_bootstrap($data);
  }

  function _generate_mysql_query($update_id, $use_limit) {
    $mysql_query = "
      SELECT
        store_items.item_title,
        store_items.item_url,
        store_items.item_price,
        store_items.small_pic,
        store_items.was_price
      FROM store_items
        INNER JOIN store_cat_assign
          ON store_items.id = store_cat_assign.item_id
      WHERE store_cat_assign.cat_id = $update_id
      AND store_items.status = 1
    ";

    if ($use_limit) {
      $limit = $this->get_limit();
      $offset = $this->get_offset();
      $mysql_query .= " limit " . $offset . ", " . $limit;
    }

    return $mysql_query;
  }

  function get_limit() {
    $limit = 5;
    return $limit;
  }

  function get_offset() {
    $offset = $this->uri->segment(4);

    if (!is_numeric($offset)) {
      $offset = 0;
    }

    return $offset;
  }

  function get_target_pagination_base_url() {
    $first_bit = $this->uri->segment(1);
    $second_bit = $this->uri->segment(2);
    $third_bit = $this->uri->segment(3);

    $target_base_url = base_url() . $first_bit . "/" . $second_bit . "/" . $third_bit;

    return $target_base_url; 
  }

  function _draw_blocks($is_mobile = false) {
    $data["query"] = $this->get("priority");
    $num_rows = $data["query"]->num_rows();

    if ($num_rows > 0) {
      if ($is_mobile) {
        $view_file = "homepage_blocks_jqm";
      } else {
        $view_file = "homepage_blocks";
      }
      $this->load->view($view_file, $data);
    }
  }

  function _draw_sortable_list() {
    $mysql_query = "select * from homepage_blocks order by priority asc";
    $data["query"] = $this->_custom_query($mysql_query);
    $this->load->view("sortable_list", $data);
  }

  function deleteconf($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $this->load->module("site_security");
    $this->site_security->_make_sure_is_admin();

    $data["headline"] = "Delete Entire Offer Block";
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

      $flash_msg = "The entire offer block was successfully deleted.";
      $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
      $this->session->set_flashdata("item", $value);

      redirect("homepage_blocks/manage");
    } else if ($submit == "Cancel") {
      redirect("homepage_blocks/create/" . $update_id);
    }
  }

  function _process_delete($update_id) {
    // delete any items associated with the offer block
    $mysql_query = "delete from homepage_offers where block_id=$update_id";
    $query = $this->_custom_query($mysql_query);
    // delete the offer block
    $this->_delete($update_id);
  }

  //

  public function get($order_by) {
    $this->load->model("mdl_homepage_blocks");
    $query = $this->mdl_homepage_blocks->get($order_by);

    return $query;
  }

  public function get_with_limit($limit, $offset, $order_by) {
    $this->load->model("mdl_homepage_blocks");
    $query = $this->mdl_homepage_blocks->get_with_limit($limit, $offset, $order_by);

    return $query;
  }

  public function get_where($id) {
    $this->load->model("mdl_homepage_blocks");
    $query = $this->mdl_homepage_blocks->get_where($id);

    return $query;
  }

  public function get_where_custom($col, $value) {
    $this->load->model("mdl_homepage_blocks");
    $query = $this->mdl_homepage_blocks->get_where_custom($col, $value);

    return $query;
  }

  function _insert($data) {
    $this->load->model("mdl_homepage_blocks");
    $this->mdl_homepage_blocks->_insert($data);
  }

  function _update($id, $data) {
    $this->load->model("mdl_homepage_blocks");
    $this->mdl_homepage_blocks->_update($id, $data);
  }

  function _delete($id) {
    $this->load->model("mdl_homepage_blocks");
    $this->mdl_homepage_blocks->_delete($id);
  }

  public function count_where($column, $value) {
    $this->load->model("mdl_homepage_blocks");
    $count = $this->mdl_homepage_blocks->count_where($column, $value);

    return $count;
  }

  public function get_max() {
    $this->load->model("mdl_homepage_blocks");
    $max_id = $this->mdl_homepage_blocks->get_max();

    return $max_id;
  }

  function _custom_query($mysql_query) {
    $this->load->model("mdl_homepage_blocks");
    $query = $this->mdl_homepage_blocks->_custom_query($mysql_query);

    return $query;
  }

}
