<?php

class Homepage_offers extends MX_Controller {

  public function __construct() {
    parent::__construct();
  }

  function update($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $this->load->library("session");
    $this->load->module("site_security");

    $this->site_security->_make_sure_is_admin();

    $data["query"] = $this->get_where_custom("block_id", $update_id);
    $data["num_rows"] = $data["query"]->num_rows();

    $data["flash"] = $this->session->flashdata("item");
    $data["headline"] = "Update Homepage Offer Block Items";
    $data["update_id"] = $update_id;
    $data["view_file"] = "update";

    $this->load->module("templates");
    $this->templates->admin($data);
  }

  function submit($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $this->load->library("session");
    $this->load->module("site_security");

    $this->site_security->_make_sure_is_admin();

    $submit = $this->input->post("submit", true);
    $item_id = trim($this->input->post("item_id", true));

    if ($submit == "Finished") {
      redirect("homepage_blocks/create/" . $update_id);
    } else if ($submit == "Submit") {
      if (!empty($item_id)) {
        $is_valid_item = $this->_is_valid_item($item_id);

        if (!$is_valid_item) {
          $flash_msg = "The item ID that you submitted was not valid.";
          $value = '<div class="alert alert-danger" role="alert">' . $flash_msg . '</div>';
          $this->session->set_flashdata("item", $value);
        } else {
          $data["block_id"] = $update_id;
          $data["item_id"] = $item_id;
          $this->_insert($data);

          $flash_msg = "The new offer was successfully added.";
          $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
          $this->session->set_flashdata("item", $value);
        }               
      }
      redirect("homepage_offers/update/" . $update_id);
    }
    
  }

  function delete($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $this->load->library("session");
    $this->load->module("site_security");

    $this->site_security->_make_sure_is_admin();

    $query = $this->get_where($update_id);
    $row = $query->result();
    $block_id = $row[0]->block_id;
    $this->_delete($update_id);

    $flash_msg = "The item_id option was successfully deleted.";
    $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
    $this->session->set_flashdata("item", $value);

    redirect("homepage_offers/update/" . $block_id);
  }

  function _delete_for_item($block_id) {
    $mysql_query = "delete from homepage_offers where block_id=$block_id";
    $query = $this->_custom_query($mysql_query);
  }

  function _draw_offers($data, $is_mobile = false) {
    $block_id = $data["block_id"];

    $mysql_query = "
      SELECT
        store_items.*
      FROM homepage_offers
        INNER JOIN homepage_blocks
          ON homepage_offers.block_id = homepage_blocks.id
        INNER JOIN store_items
          ON homepage_offers.item_id = store_items.id
      WHERE homepage_offers.block_id = $block_id
    ";
    $query = $this->_custom_query($mysql_query);
    $num_rows = $query->num_rows();

    if ($num_rows > 0) {
      $this->load->module("site_settings");

      $data["query"] = $query;
      $data["item_segments"] = $this->site_settings->_get_item_segments();
      $data["currency_symbol"] = $this->site_settings->_get_currency_symbol();

      if ($is_mobile) {
        $view_file = "offers_jqm";
      } else {
        $view_file = "offers";
      }

      $this->load->view($view_file, $data);
    }
  }

  function _is_valid_item($item_id) {
    if (!is_numeric($item_id)) {
      return false;
    }

    $this->load->module("store_items");
    $query = $this->store_items->get_where($item_id);
    $num_rows = $query->num_rows();

    if ($num_rows > 0) {
      return true;
    } else {
      return false;
    }
  }

  //

  public function get($order_by) {
    $this->load->model("mdl_homepage_offers");
    $query = $this->mdl_homepage_offers->get($order_by);

    return $query;
  }

  public function get_with_limit($limit, $offset, $order_by) {
    $this->load->model("mdl_homepage_offers");
    $query = $this->mdl_homepage_offers->get_with_limit($limit, $offset, $order_by);

    return $query;
  }

  public function get_where($id) {
    $this->load->model("mdl_homepage_offers");
    $query = $this->mdl_homepage_offers->get_where($id);

    return $query;
  }

  public function get_where_custom($col, $value) {
    $this->load->model("mdl_homepage_offers");
    $query = $this->mdl_homepage_offers->get_where_custom($col, $value);

    return $query;
  }

  function _insert($data) {
    $this->load->model("mdl_homepage_offers");
    $this->mdl_homepage_offers->_insert($data);
  }

  function _update($id, $data) {
    $this->load->model("mdl_homepage_offers");
    $this->mdl_homepage_offers->_update($id, $data);
  }

  function _delete($id) {
    $this->load->model("mdl_homepage_offers");
    $this->mdl_homepage_offers->_delete($id);
  }

  public function count_where($column, $value) {
    $this->load->model("mdl_homepage_offers");
    $count = $this->mdl_homepage_offers->count_where($column, $value);

    return $count;
  }

  public function get_max() {
    $this->load->model("mdl_homepage_offers");
    $max_id = $this->mdl_homepage_offers->get_max();

    return $max_id;
  }

  function _custom_query($mysql_query) {
    $this->load->model("mdl_homepage_offers");
    $query = $this->mdl_homepage_offers->_custom_query($mysql_query);

    return $query;
  }

}
