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
		 $this->load->helper('file');
		 $this->load->library('session');
		 $this->load->model('ShopModel');

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
	public function signup()
	{
		$this->load->view('user/header');
		$this->load->view('user/signup');
		$this->load->view('user/footer');
	}
	public function add_customer()
	{
		$first_name=$this->input->post('first_name');
		$last_name=$this->input->post('last_name');
		$address=$this->input->post('address');
		$gender=$this->input->post('gender');
		$contact=$this->input->post('contact');
		$email=$this->input->post('email');
		$password=$this->input->post('password');
		
		

	
		$config['upload_path']          = './uploads/';
		$config['allowed_types']        = '*';
		$this->load->library('upload', $config);
		$this->upload->do_upload('file');
		$picture=$_FILES['file']['name'];
		
		$data=array('fname'=>$first_name,
		'lname'=>$last_name,
		'address'=>$address,
		'gender'=>$gender,
		'contact'=>$contact,
		'email'=>$email,
		'password'=>base64_encode($password),
		'image'=>$picture);

		$res=$this->ShopModel->ins_customer($data);
		
		if($res==true)
		{
		
			$this->session->set_flashdata('success','Thank you for signup with iGroceries!');
			redirect('Shop');
			
		}
		else{
			$this->session->set_flashdata('success','Registration Unsuccessful Try Again!');
			redirect('Shop/signup');
		}
	}

} 