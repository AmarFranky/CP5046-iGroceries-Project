<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

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
		 $this->load->model('AdminModel');
		 $this->load->library('session');
	}
	public function index()
	{
		$this->load->view('admin/admin-login');
	}
	public function dashboard()
	{
		$this->load->view('admin/dashboard');
	}
	public function isAdminLogin()
	{
		$email=$this->input->post('email');
		$password=$this->input->post('password');
		//$data=array('eamil'=>$email,'password'=>$password);
		$return=$this->AdminModel->adminLogin($email,$password);

		if(count($return)>0)
		{
			foreach($return as $row)
			{
				$sessionArray=array(
					'fname'=>$row->fname,
					'lname'=>$row->lname,
				);
			}
			$this->session->set_userdata($sessionArray);
		//	$this->session->set_flashdata('success','Login Successful');
			redirect('Admin/dashboard');
		}
		else
		{
			$this->session->set_flashdata('failed','Invalid Admin Email or Password');
			redirect('Admin');
		}

	}
	public function loadAddProducts()
	{
		$data['cat']=$this->AdminModel->getCat();
		$this->load->view('admin/header');
		$this->load->view('admin/add_products',$data);
		//$this->load->view('admin/footer');
	}
	public function insertProducts()
	{
		$item_name=$this->input->post('item_name');
		$item_category=$this->input->post('item_category');
		$item_description=$this->input->post('item_description');
		$item_price=$this->input->post('item_price');
		$item_stock=$this->input->post('item_stock');
		$arrived_at=date("Y-m-d");
		$config['upload_path']          = './uploads/';
		$config['allowed_types']        = 'gif|jpg|png|jpeg';
		$config['max_size']             = 1000;
		$config['max_width']            = 1024;
		$config['max_height']           = 768;

		$this->load->library('upload', $config);
		$this->upload->do_upload('userfile');
		$image=$_FILES['userfile']['name']; 
		$data=array('item_name'=>$item_name,
		'item_category'=>$item_category,
		'item_description'=>$item_description,
		'item_price'=>$item_price,
		'item_image'=>$image,
		'item_stock'=>$item_stock,
		'item_arrive'=>$arrived_at
		);

		$res=$this->AdminModel->addItem($data);
		if($res){
		
			echo "success";
		}
		else{
			echo "Error";
		}
	}
	public function loadManageProduct()
	{
		$data['products']=$this->AdminModel->showProducts();
		$this->load->view('admin/manage_prod',$data);
	}
	public function loadEditProduct()
	{
		$id=$this->input->get('id');
		$data['product']=$this->AdminModel->getProductFromId($id);
		$this->load->view('admin/header');
		$this->load->view('admin/edit_products',$data);

	}
	public function updateProduct()
	{
		$id=$this->input->get('id');
		$item_name=$this->input->post('item_name');
		$item_description=$this->input->post('item_description');
		$item_price=$this->input->post('item_price');
		$item_stock=$this->input->post('item_stock');

		$data=array('item_name'=>$item_name,
		'item_description'=>$item_description,
		'item_price'=>$item_price,
		'item_stock'=>$item_stock
		);

		$res=$this->AdminModel->updateProduct($id,$data);
		if($res)
		{
			redirect('Admin/loadManageProduct');
		}
		else{
			echo "unable to update";
		}
		
	}
	public function del_prod()
	{
		$id=$this->input->get('id');
		$res=$this->AdminModel->del_prod($id);
		if($res)
		{
			$data['products']=$this->AdminModel->showProducts();
		$this->load->view('admin/manage_prod',$data);
		}
		else
		{
			echo "Unable to delete";
		}
	}
	public function del_cust()
	{
		$id=$this->input->get('id');
		$res=$this->AdminModel->del_cust($id);
		if($res)
		{
			redirect('Admin/manageCust');
		}
		else
		{
			echo "Unable to delete";
		}
	}

	public function manageCust()
	{
		$data['customers']=$this->AdminModel->getCust();
		$this->load->view('admin/manage_customers',$data);
	}
	public function logout()
	{
		session_destroy();
		redirect('Admin');
	}

} 