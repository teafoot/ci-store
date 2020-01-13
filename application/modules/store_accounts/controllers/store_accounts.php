<?php

class Store_accounts extends MX_Controller {

  public function __construct() {
    parent::__construct();
  }

   function manage() {
    $this->load->library("session");
    $this->load->module("site_security");

    $this->site_security->_make_sure_is_admin();

    $data["flash"] = $this->session->flashdata("item");
    $data["query"] = $this->get("lastname");
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
      $this->form_validation->set_rules("firstname", "First Name", "required");
      $this->form_validation->set_rules("lastname", "Last Name", "required");
      $this->form_validation->set_rules("username", "Username", "required");
      $this->form_validation->set_rules("company", "Company", "required");
      $this->form_validation->set_rules("address1", "Address Line 1", "required");
      $this->form_validation->set_rules("address2", "Address Line 2", "required");
      $this->form_validation->set_rules("town", "Town", "required");
      $this->form_validation->set_rules("country", "Country", "required");
      $this->form_validation->set_rules("postcode", "Postal Code", "required");
      $this->form_validation->set_rules("telnum", "Telephone Number", "required");
      $this->form_validation->set_rules("email", "Email", "required");

      if ($this->form_validation->run() == true) {
        $data = $this->_fetch_data_from_post();

        // Update/Insert to DB
        if (is_numeric($update_id)) {
          $this->_update($update_id, $data);
          $flash_msg = "The account details were successfully updated.";
        } else {
          $data["date_made"] = time();

          $this->_insert($data);
          $update_id = $this->get_max(); // get the id of the new account.
          $flash_msg = "The account details was successfully added.";
        }

        $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
        $this->session->set_flashdata("item", $value);
        redirect("store_accounts/create/" . $update_id);
      }
    } else if ($submit == "Cancel") {
      redirect("store_accounts/manage");
    }

    // GET Request from URL
    if (is_numeric($update_id) && ($submit != "Submit")) {
      $data = $this->_fetch_data_from_db($update_id);
    } else {
      $data = $this->_fetch_data_from_post();
    }

    if (is_numeric($update_id)) {
      $data["headline"] = "Update account details";
    } else {
      $data["headline"] = "Add new account";
    }
    $data["flash"] = $this->session->flashdata("item");
    $data["update_id"] = $update_id;
    $data["view_file"] = "create";

    $this->load->module("templates");
    $this->templates->admin($data);
  }

  function _fetch_data_from_post() {
    $data["firstname"] = $this->input->post("firstname", true);
    $data["lastname"] = $this->input->post("lastname", true);
    $data["username"] = $this->input->post("username", true);
    $data["company"] = $this->input->post("company", true);
    $data["address1"] = $this->input->post("address1", true);
    $data["address2"] = $this->input->post("address2", true);
    $data["town"] = $this->input->post("town", true);
    $data["country"] = $this->input->post("country", true);
    $data["postcode"] = $this->input->post("postcode", true);
    $data["telnum"] = $this->input->post("telnum", true);
    $data["email"] = $this->input->post("email", true);

    return $data;
  }

  function _fetch_data_from_db($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $query = $this->get_where($update_id);

    foreach ($query->result() as $row) {
      $data["firstname"] = $row->firstname;
      $data["lastname"] = $row->lastname;
      $data["username"] = $row->username;
      $data["company"] = $row->company;
      $data["address1"] = $row->address1;
      $data["address2"] = $row->address2;
      $data["town"] = $row->town;
      $data["country"] = $row->country;
      $data["postcode"] = $row->postcode;
      $data["telnum"] = $row->telnum;
      $data["email"] = $row->email;
      $data["date_made"] = $row->date_made;
      $data["pword"] = $row->pword;
      $data["last_login"] = $row->last_login;
    }

    if (!isset($data)) {
      $data = "";
    }

    return $data;
  }

  function update_pword() {
    $this->load->module("site_security");
    $this->load->library("session");

    $this->site_security->_make_sure_is_admin();

    $update_id = $this->uri->segment(3);
    $submit = $this->input->post("submit", true);

    if (!is_numeric($update_id)) {
      redirect("store_accounts/manage");
    }

    // POST Request from form
    if ($submit == "Submit") {
      $this->load->library("form_validation");
      $this->form_validation->set_rules("pword", "Password", "required|min_length[7]|max_length[35]");
      $this->form_validation->set_rules("repeat_pword", "Repeat Password", "required|matches[pword]");

      if ($this->form_validation->run() == true) {
        $pword = $this->input->post("pword", true);
        $data["pword"] = $this->site_security->_hash_string($pword);

        // Update/Insert to DB
        $this->_update($update_id, $data);

        $flash_msg = "The account password was successfully updated.";
        $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
        $this->session->set_flashdata("item", $value);

        redirect("store_accounts/create/" . $update_id);
      }
    } else if ($submit == "Cancel") {
      redirect("store_accounts/create/" . $update_id);
    }

    $data["update_id"] = $update_id;
    $data["headline"] = "Update account password";
    $data["view_file"] = "update_pword";
    $data["flash"] = $this->session->flashdata("item");

    $this->load->module("templates");
    $this->templates->admin($data);
  }

  function _get_customer_name($update_id, $optional_customer_data = null) {
    if (!isset($optional_customer_data)) {
      $data = $this->_fetch_data_from_db($update_id);
    } else {
      $data["firstname"] = $optional_customer_data["firstname"];
      $data["lastname"] = $optional_customer_data["lastname"];
      $data["company"] = $optional_customer_data["company"];
    }

    if ($data=="") {
      $customer_name = "Unknown";
    } else {
      $firstname = trim(ucfirst($data["firstname"]));
      $lastname = trim(ucfirst($data["lastname"]));
      $company = trim(ucfirst($data["company"]));

      $customer_name = $firstname . " " . $lastname;
      $company_length = strlen($company);

      if ($company_length > 2) {
        $customer_name .= " from " . $company;
      }
    }

    return $customer_name;
  }

  function _generate_token($update_id) {
    $data = $this->_fetch_data_from_db($update_id);

    $date_made = $data["date_made"];
    $last_login = $data["last_login"];
    $pword = $data["pword"];

    $pword_length = strlen($pword);
    $start_point = $pword_length - 6;
    $last_six_chars = substr($pword, $start_point, 6);

    if (($pword_length > 5) and ($last_login > 0)) {
      $token = $last_six_chars . $date_made . $last_login;
    } else {
      $token = "";
    }

    return $token;
  }

  function _get_customer_id_from_token($token) {
    $last_six_chars = substr($token, 0, 6);
    $date_made = substr($token, 6, 10);
    $last_login = substr($token, 16, 10);

    $sql = "select * from store_accounts where date_made=? and pword like ? and last_login=?";
    $query = $this->db->query($sql, array($date_made, "%" . $last_six_chars, $last_login));
    // echo $this->db->last_query();
    foreach ($query->result() as $row) {
      $customer_id = $row->id;
    }

    if (!isset($customer_id)) {
      $customer_id = 0;
    }

    return $customer_id;
  }

  // function test() { // our custom token
  //   $customer_id = 1;
  //   $token = $this->_generate_token($customer_id);
  //   $customer_id = $this->_get_customer_id_from_token($token);

  //   echo "The full token is: $token" . "<br>";
  //   echo "The customer of the token is: $customer_id";
  // }

  function deleteconf($update_id) {
    if (!is_numeric($update_id)) {
      redirect("site_security/not_allowed");
    }

    $this->load->module("site_security");
    $this->site_security->_make_sure_is_admin();

    $data["headline"] = "Delete account";
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
      $allowed = $this->_make_sure_delete_allowed($update_id);

      if (!$allowed) {
        $flash_msg = "You're not allowed to delete this account.";
        $value = '<div class="alert alert-danger" role="alert">' . $flash_msg . '</div>';
        $this->session->set_flashdata("item", $value);

        redirect("store_accounts/manage");        
      }

      $this->_process_delete($update_id);

      $flash_msg = "The account was successfully deleted.";
      $value = '<div class="alert alert-success" role="alert">' . $flash_msg . '</div>';
      $this->session->set_flashdata("item", $value);

      redirect("store_accounts/manage");
    } else if ($submit == "Cancel") {
      redirect("store_accounts/create/" . $update_id);
    }
  }

  function _make_sure_delete_allowed($update_id) {
    // do not allow if account has items in basket/shoppertrack
    $mysql_query = "select * from store_basket where shopper_id=$update_id";
    $query = $this->_custom_query($mysql_query);
    $num_rows = $query->num_rows();

    if ($num_rows > 0) {
      return false; // delete not allowed (has items in basket)
    } else {
      $mysql_query = "select * from store_shoppertrack where shopper_id=$update_id";
      $query = $this->_custom_query($mysql_query);
      $num_rows = $query->num_rows();

      if ($num_rows > 0) {
        return false; // delete not allowed (shopper has made a purchase)
      }
    }

    return true;
  }

  function _process_delete($update_id) {
    $this->_delete($update_id);
  }

  function _get_shopper_address($update_id, $delimiter) {
    $data = $this->_fetch_data_from_db($update_id);
    $address = "";

    if ($data["address1"] != "") {
      $address .= $data["address1"];
      $address .= $delimiter;
    }

    if ($data["address2"] != "") {
      $address .= $data["address2"];
      $address .= $delimiter;
    }

    if ($data["town"] != "") {
      $address .= $data["town"];
      $address .= $delimiter;
    }

    if ($data["country"] != "") {
      $address .= $data["country"];
      $address .= $delimiter;
    }

    if ($data["postcode"] != "") {
      $address .= $data["postcode"];
      $address .= $delimiter;
    }

    return $address;
  }

  //

  public function get($order_by) {
    $this->load->model("mdl_store_accounts");
    $query = $this->mdl_store_accounts->get($order_by);

    return $query;
  }

  public function get_with_limit($limit, $offset, $order_by) {
    $this->load->model("mdl_store_accounts");
    $query = $this->mdl_store_accounts->get_with_limit($limit, $offset, $order_by);

    return $query;
  }

  public function get_where($id) {
    $this->load->model("mdl_store_accounts");
    $query = $this->mdl_store_accounts->get_where($id);

    return $query;
  }

  public function get_where_custom($col, $value) {
    $this->load->model("mdl_store_accounts");
    $query = $this->mdl_store_accounts->get_where_custom($col, $value);

    return $query;
  }

  public function get_with_double_condition($col1, $value1, $col2, $value2) {
    $this->load->model("mdl_store_accounts");
    $query = $this->mdl_store_accounts->get_with_double_condition($col1, $value1, $col2, $value2);

    return $query;
  }

  function _insert($data) {
    $this->load->model("mdl_store_accounts");
    $this->mdl_store_accounts->_insert($data);
  }

  function _update($id, $data) {
    $this->load->model("mdl_store_accounts");
    $this->mdl_store_accounts->_update($id, $data);
  }

  function _delete($id) {
    $this->load->model("mdl_store_accounts");
    $this->mdl_store_accounts->_delete($id);
  }

  public function count_where($column, $value) {
    $this->load->model("mdl_store_accounts");
    $count = $this->mdl_store_accounts->count_where($column, $value);

    return $count;
  }

  public function get_max() {
    $this->load->model("mdl_store_accounts");
    $max_id = $this->mdl_store_accounts->get_max();

    return $max_id;
  }

  function _custom_query($mysql_query) {
    $this->load->model("mdl_store_accounts");
    $query = $this->mdl_store_accounts->_custom_query($mysql_query);

    return $query;
  }

  function autogen() {
    $mysql_query = "show columns from store_accounts";
    $query = $this->_custom_query($mysql_query);

    // fetch data from posts
    foreach ($query->result() as $row) {
      $column_name = $row->Field;

      if ($column_name != "id") {
        echo '$data["' . $column_name . '"] = $this->input->post("' . $column_name . '", true);' . "<br>";
      }
    }

    echo "<hr>";

    // validate posts data
    foreach ($query->result() as $row) {
      $column_name = $row->Field;

      if ($column_name != "id") {
        echo '$this->form_validation->set_rules("' . $column_name . '", "' . ucfirst($column_name) . '", "required");' . "<br>";
      }
    }

    echo "<hr>";

    // fetch data from db
    foreach ($query->result() as $row) {
      $column_name = $row->Field;

      if ($column_name != "id") {
        echo '$data["' . $column_name . '"] = $row->' . $column_name . ';' . "<br>";
      }
    }

    echo "<hr>";

    // post input fields
    foreach ($query->result() as $row) {
      $column_name = $row->Field;

      if ($column_name != "id") {
        $var = '<div class="control-group">
                  <label class="control-label" for="' . $column_name . '">' . ucfirst($column_name) . '</label>
                  <div class="controls">
                    <input type="text" class="span6" name="' . $column_name . '" value="<?php echo $' .  $column_name . '; ?>">
                  </div>
                </div>';
        echo htmlentities($var) . "<br>";
      }
    }
  }

}
