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
		$data['new']=$this->ShopModel->get_new();
		$this->load->view('user/header');
		$this->load->view('user/landing',$data);
		$this->load->view('user/footer');
	}
	public function isLogiCustomer()
	{
		$email=$this->input->post('email');
		$password=$this->input->post('password');
		$result=$this->ShopModel->loginCustomer($email,$password);
		if(count($result)>0)
		{
			foreach($result as $row)
			{
				$sessionArray=array(
				'fname'=>$row->fname,
				'lname'=>$row->lname,
				'picture'=>$row->picture
				);
			}
		
			$this->session->set_userdata($sessionArray);
			$this->session->set_flashdata('success', 'You are now logged in with iGroceries');
			redirect('Shop/index');
		}
		/*foreach('load/tech')*/
		else
		{
			$this->session->set_flashdata('success','Email Or Password Incorect!! Try Again');			
			redirect('Shop');
		}
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
	public function logout()
	{
		session_destroy();
		redirect('Shop/index');
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
	public function get_beverages()
	{
		$data['cat']="Beverages";
		$data['items']= $this->ShopModel->get_beverages();
		$data['cat']="Beverages";
		$this->load->view('user/header');
		$this->load->view('user/items',$data);
		$this->load->view('user/footer'); 

	}
	public function get_bread()
	{
		$data['cat']="Bread/Backery";
		$data['items']= $this->ShopModel->get_bread();
		$this->load->view('user/header');
		$this->load->view('user/items',$data);
		$this->load->view('user/footer'); 

	}
	public function get_frozen()
	{
		$data['cat']="Frozen Food";
		$data['items']= $this->ShopModel->get_frozen();
		$this->load->view('user/header');
		$this->load->view('user/items',$data);
		$this->load->view('user/footer'); 
	}
	public function get_cleaners()
	{
		$data['cat']="Cleaners";
		$data['items']= $this->ShopModel->get_cleaners();
		$this->load->view('user/header');
		$this->load->view('user/items',$data);
		$this->load->view('user/footer'); 
	}
	public function get_canned()
	{
		$data['cat']="Canned Goods";
		$data['items']= $this->ShopModel->get_canned();
		$this->load->view('user/header');
		$this->load->view('user/items',$data);
		$this->load->view('user/footer'); 
	}
	public function get_dry()
	{
		$data['cat']="Dry/Backing Goods";
		$data['items']= $this->ShopModel->get_dry();
		$this->load->view('user/header');
		$this->load->view('user/items',$data);
		$this->load->view('user/footer'); 
	}
	public function get_dairy()
	{
		$data['cat']="Dairy Products";
		$data['items']= $this->ShopModel->get_dairy();
		$this->load->view('user/header');
		$this->load->view('user/items',$data);
		$this->load->view('user/footer'); 
	}
	public function get_personal()
	{
		$data['cat']="Personal Care";
		$data['items']= $this->ShopModel->get_personal();
		$this->load->view('user/header');
		$this->load->view('user/items',$data);
		$this->load->view('user/footer'); 
	}
	public function get_others()
	{
		$data['items']= $this->ShopModel->get_others();
		$data['cat']="Others";
		$this->load->view('user/header');
		$this->load->view('user/items',$data);
		$this->load->view('user/footer'); 
	}
	public function new()
	{
		$res=$this->ShopModel->get_new();

		print_r($res);
	}

} 