<?php

class Perfectcontroller extends MX_Controller {

  public function __construct() {
    parent::__construct();
  }

  public function get($order_by) {
    $this->load->model("mdl_perfectcontroller");
    $query = $this->mdl_perfectcontroller->get($order_by);

    return $query;
  }

  public function get_with_limit($limit, $offset, $order_by) {
    $this->load->model("mdl_perfectcontroller");
    $query = $this->mdl_perfectcontroller->get_with_limit($limit, $offset, $order_by);

    return $query;
  }

  public function get_where($id) {
    $this->load->model("mdl_perfectcontroller");
    $query = $this->mdl_perfectcontroller->get_where($id);

    return $query;
  }

  public function get_where_custom($col, $value) {
    $this->load->model("mdl_perfectcontroller");
    $query = $this->mdl_perfectcontroller->get_where_custom($col, $value);

    return $query;
  }

  function _insert($data) {
    $this->load->model("mdl_perfectcontroller");
    $this->mdl_perfectcontroller->_insert($data);
  }

  function _update($id, $data) {
    $this->load->model("mdl_perfectcontroller");
    $this->mdl_perfectcontroller->_update($id, $data);
  }

  function _delete($id) {
    $this->load->model("mdl_perfectcontroller");
    $this->mdl_perfectcontroller->_delete($id);
  }

  public function count_where($column, $value) {
    $this->load->model("mdl_perfectcontroller");
    $count = $this->mdl_perfectcontroller->count_where($column, $value);

    return $count;
  }

  public function get_max() {
    $this->load->model("mdl_perfectcontroller");
    $max_id = $this->mdl_perfectcontroller->get_max();

    return $max_id;
  }

  function _custom_query($mysql_query) {
    $this->load->model("mdl_perfectcontroller");
    $query = $this->mdl_perfectcontroller->_custom_query($mysql_query);

    return $query;
  }

}
