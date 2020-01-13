<?php

class Invoices extends MX_Controller {

  public function __construct() {
    parent::__construct();
  }

  function test() {
    $data["name"] = "David";
    $this->load->view('test', $data);

    // Convert to PDF
    $html = $this->output->get_output();    
    $this->load->library('dompdf_gen');    
    $this->dompdf->load_html($html);
    $this->dompdf->render();
    $this->dompdf->stream("welcome.pdf", array("Attachment" => false));
  }

}
