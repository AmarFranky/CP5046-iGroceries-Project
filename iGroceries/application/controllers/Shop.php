<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shop extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	  public function __construct(){
		 parent::__construct();
		 $this->load->helper('url');
		 $this->load->helper('form');
		 $this->load->library('session');
	}
	public function index()
	{
		$this->load->view('user/header');
		$this->load->view('user/landing');
		$this->load->view('user/footer');
	}
	public function products()
	{
	//	$this->load->view('header');
		$this->load->view('user/products');
	//	$this->load->view('footer');
	}
	public function about()
	{
		$this->load->view('user/header');
		$this->load->view('user/about');
		$this->load->view('user/footer'); 
	}

} 