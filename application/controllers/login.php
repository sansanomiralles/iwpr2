<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
    function __construct()
      {
       parent::__construct();
      }

      function index()
      {
       $this->load->helper(array('form'));
       $this->load->view('publica/login_view.php');
      }
  }
?>
