<?php

class Test extends MX_Controller {

  public function __construct() {
    parent::__construct();
  }

  function index() {
    header("HTTP/1.1 302 Moved Temporarily"); // fake

    $data["name"] = "David";
    $this->load->view("index", $data);
  }

  function get_headers() { // to check http status of a site
    $url = "http://localhost/cishop/test";
    $headers = get_headers($url);
    $var_type = gettype($headers);

    echo "headers has a var type of " . $var_type . "<hr>";
    foreach ($headers as $key => $value) {
      echo "key of $key has value of $value<br>";
    }
  }

  function form() {
    $this->load->view("form");
  }

  function submit() {
    $name = $this->input->post("name", true);
    $city = $this->input->post("city", true);
    $age = $this->input->post("age", true);

    if (($name != "") and ($city != "") and ($age != "")) {
      $data["name"] = $name;
      $data["city"] = $city;
      $data["age"] = $age;

      $this->_insert($data);
      echo "success";
    } else {
      echo "uncool vibes";
    }
  }

  function autosubmit() {
    $data["name"] = "David";
    $data["city"] = "New York";
    $data["age"] = 900;

    foreach ($data as $key => $value) {
      $post_items[] = $key . "=" . $value;
    }

    $post_string = implode("&", $post_items);

    $target_url = "http://localhost/cishop/test/submit";
    $curl_connection = curl_init($target_url);
    curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_string); // prepare data
    $result = curl_exec($curl_connection);
    curl_close($curl_connection);
  }

  function log($data = [], $trace = false, $type = "info") {
    /* 
    Note:
      $trace = false for CI HMVC
      $trace = true for CI
    */
    $this->load->library("fb");        
    $this->fb->$type($data);

    if ($trace) {
      $this->fb->trace($data);
    }
  }

  // SQL Injection

  function sqltest1() {
    // Default
    // Rubbish: Returns results

    $id = $this->input->post("id", true);
    // addslashes();
    // mysqli_real_escape_string();

    $submit = $this->input->post("submit");

    if ($submit == "Submit") {
      $mysql_query = "select * from store_items where id='$id'";
      $query = $this->_custom_query($mysql_query);
      $num_rows = $query->num_rows();

      echo "<h1>" . $mysql_query . "</h1>";
      echo "<h2>The query produced " . $num_rows . " row(s).</h2>";
      // 44
      // select * from store_items where id='44'
      // The query produced 1 row(s).

      // asd' or id > '0
      // select * from store_items where id='asd' or id > '0'
      // The query produced 1003 row(s).
    }

    $current_url = current_url();
    echo form_open($current_url);
    echo form_input("id", "");
    echo form_submit("submit", "Submit");
    echo form_close();
  }

  function sqltest2() {
    // Escaping Queries
    // OK: Returns error

    $id = $this->input->post("id", true);
    $submit = $this->input->post("submit");

    if ($submit == "Submit") {
      // $id = $this->db->escape($id);

      $mysql_query = "select * from store_items where id='$id'";
      $mysql_query = $this->db->escape($mysql_query);
      $query = $this->_custom_query($mysql_query);
      $num_rows = $query->num_rows();

      echo "<h1>" . $mysql_query . "</h1>";
      echo "<h2>The query produced " . $num_rows . " row(s).</h2>";
      // 44
      // Error in SQL syntax
      // select * from store_items where id=\'44\'

      // asd' or id > '0
      // Error in SQL syntax
      // select * from store_items where id=\'asd\' or id > \'0\'
    }

    $current_url = current_url();
    echo form_open($current_url);
    echo form_input("id", "");
    echo form_submit("submit", "Submit");
    echo form_close();
  }

  function sqltest3() {
    // Query Binding
    // Good

    $id = $this->input->post("id", true);
    $submit = $this->input->post("submit");

    if ($submit == "Submit") {
      $mysql_query = "select * from store_items where id=?";
      $query = $this->db->query($mysql_query, array($id));
      $num_rows = $query->num_rows();

      $last_query = $this->db->last_query();

      echo "<h1>" . $last_query . "</h1>";
      echo "<h2>The query produced " . $num_rows . " row(s).</h2>";
      // 44
      // select * from store_items where id=?
      // select * from store_items where id='44'
      // The query produced 1 row(s).

      // asd' or id > '0
      // select * from store_items where id=?
      // select * from store_items where id='asd\' or id > \'0'
      // The query produced 0 row(s).
    }

    $current_url = current_url();
    echo form_open($current_url);
    echo form_input("id", "");
    echo form_submit("submit", "Submit");
    echo form_close();
  }

  function sqltest4() {
    // Query Builder
    // Best

    $id = $this->input->post("id", true);
    $submit = $this->input->post("submit");

    if ($submit == "Submit") {
      $this->load->module("store_items");
      $query = $this->store_items->get_where_custom("id", $id);
      $num_rows = $query->num_rows();

      $last_query = $this->db->last_query();

      echo "<h1>" . $last_query . "</h1>";
      echo "<h2>The query produced " . $num_rows . " row(s).</h2>";
      // 44
      // SELECT * FROM `store_items` WHERE `id` = '44'
      // The query produced 1 row(s).

      // asd' or id > '0
      // SELECT * FROM `store_items` WHERE `id` = 'asd\' or id > \'0'
      // The query produced 0 row(s).
    }

    $current_url = current_url();
    echo form_open($current_url);
    echo form_input("id", "");
    echo form_submit("submit", "Submit");
    echo form_close();
  }

  //

  public function get($order_by) {
    $this->load->model("mdl_test");
    $query = $this->mdl_test->get($order_by);

    return $query;
  }

  public function get_with_limit($limit, $offset, $order_by) {
    $this->load->model("mdl_test");
    $query = $this->mdl_test->get_with_limit($limit, $offset, $order_by);

    return $query;
  }

  public function get_where($id) {
    $this->load->model("mdl_test");
    $query = $this->mdl_test->get_where($id);

    return $query;
  }

  public function get_where_custom($col, $value) {
    $this->load->model("mdl_test");
    $query = $this->mdl_test->get_where_custom($col, $value);

    return $query;
  }

  function _insert($data) {
    $this->load->model("mdl_test");
    $this->mdl_test->_insert($data);
  }

  function _update($id, $data) {
    $this->load->model("mdl_test");
    $this->mdl_test->_update($id, $data);
  }

  function _delete($id) {
    $this->load->model("mdl_test");
    $this->mdl_test->_delete($id);
  }

  public function count_where($column, $value) {
    $this->load->model("mdl_test");
    $count = $this->mdl_test->count_where($column, $value);

    return $count;
  }

  public function get_max() {
    $this->load->model("mdl_test");
    $max_id = $this->mdl_test->get_max();

    return $max_id;
  }

  function _custom_query($mysql_query) {
    $this->load->model("mdl_test");
    $query = $this->mdl_test->_custom_query($mysql_query);

    return $query;
  }

}
