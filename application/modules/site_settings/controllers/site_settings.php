<?php

class Site_settings extends MX_Controller {

  public function __construct() {
    parent::__construct();
  }

  function _get_item_segments() {
    // segments for item pages
    $segments = "musical/instrument/";
    return $segments;
  }

  function _get_items_segments() {
    // segments for the category pages
    $segments = "music/instruments/";
    return $segments;
  }
  
  function _get_page_not_found_msg() {
    $msg = "<h1>It's a webpage Jim, but not as we know it!</h1>";
    $msg .= "<p>Please check your vibe and try again.</p>";
    return $msg;
  }

  function _get_currency_symbol() {
    $symbol = "&pound;";
    return $symbol;
  }

  function _get_cookie_name() {
    $cookie_name = "hwefcdsdfhz";
    return $cookie_name;
  }

  function _get_support_team_name() {
    $name = "Customer Support";
    return $name;
  }

  function _get_welcome_msg($customer_id) {
    $this->load->module("store_accounts");
    $customer_name = $this->store_accounts->_get_customer_name($customer_id);

    $msg = "Hello " . $customer_name . ",<br><br>";
    $msg .= "Thank you for creating an account with CI Shop. If you have any questions about any of our products and services then please do get in touch. We are here seven days a week and would be happy to help you.<br><br>";
    $msg .= "Regards,<br><br>";
    $msg .= "John Doe (founder)";

    return $msg;
  }

  function _get_our_address() {
    $address = "795 Folsom Ave, Suite 600<br>" .
      "San Francisco, CA 94107<br>";

    return $address;
  }

  function _get_our_telnum() {
    $telnum = "(123) 456-7890";

    return $telnum;
  }

  function _get_our_name() {
    $name = "Coolness Inc.";

    return $name;
  }

  function _get_map_code() {
    $code = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d791.6317268176274!2d-4.257400204217633!3d55.86140593840843!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4888469e3109cc69%3A0x5d7a511adfacac97!2sPizza+Punks!5e0!3m2!1sen!2sbo!4v1555527393320!5m2!1sen!2sbo" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>';

    return $code;
  }

  function _get_paypal_email() {
    $email = "davidchenlog@gmail.com";

    return $email;
  }

  function _get_paypal_sandbox_email() {
    $email = "davidthemerchant@merchant.com";
    // $email = "davidthebuyer1@buyer.com";
    // pass: Eh7cJrh3    

    return $email;
  }

  function _get_currency_code() {
    $code = "GBP"; // great british pound

    return $code;
  }

  function is_mobile() {
    $this->load->library("user_agent");
    $is_mobile = $this->agent->is_mobile();
    // $is_mobile = true;
    return $is_mobile;
  }

}
