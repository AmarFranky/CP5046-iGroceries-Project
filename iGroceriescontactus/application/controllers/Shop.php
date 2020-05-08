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
		 $this->load->library('Simplify');
		 $this->load->library('cart');

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
	public function prod_details()
	{
		$id=$this->input->get('id');
		$data['details']=$this->ShopModel->get_details($id);
		$this->load->view('user/header');
		$this->load->view('user/prod_detail',$data);
		$this->load->view('user/footer');

	}
	public function cart()
	{
		$this->load->view('user/header');
		$this->load->view('user/cart');
		$this->load->view('user/footer');
	}
	public function add()
	{
		$id=$this->uri->segment(3);
		$this->db->select('*');
		$this->db->from('items');
		$this->db->where('item_id',$id);
		$query=$this->db->get();
		$row=$query->row();
		$data=array(
			'id'=>$id,
			'qty'=>1,
			'price'=>$row->item_price,
			'name'=>$row->item_name
		);
		$this->cart->insert($data);
		redirect('Shop','refresh');
	}
	public function checkout()
	{
		$id=$this->input->get('id');
		$data['items']=$this->ShopModel->getItemCheckout($id);
		$this->load->view('user/header');
		$this->load->view('user/checkout',$data);
		$this->load->view('user/footer');
	}
	function buyp($id){                                                                                                                                                                                                                                                                                
        //Set variables for paypal form
        $returnURL = base_url().'paypal/success'; //payment success url
        $cancelURL = base_url().'paypal/cancel'; //payment cancel url
        $notifyURL = base_url().'paypal/ipn'; //ipn url
        //get particular product data
        $product = $this->UserModel->getRows($id);
        $userID = 1; //current user id
        $logo = base_url().'';

        
        $this->paypal_lib->add_field('return', $returnURL);
        $this->paypal_lib->add_field('cancel_return', $cancelURL);
        $this->paypal_lib->add_field('notify_url', $notifyURL);
        $this->paypal_lib->add_field('item_name', $product['product_name']);
        $this->paypal_lib->add_field('custom', $userID);
        $this->paypal_lib->add_field('item_number',  $product['product_id']);
        $this->paypal_lib->add_field('amount',  $product['product_price']);        
        $this->paypal_lib->image($logo);
        $this->paypal_lib->paypal_auto_form();
	}
	public function pay_form()
	{
		$id=$this->input->get('id');
		$data['product']=$this->ShopModel->getItemCheckout($id);
		$this->load->view('user/product_form',$data);
	}
	public function check()
	{
		//check whether stripe token is not empty
		if(!empty($_POST['stripeToken']))
		{
			//get token, card and user info from the form
			$token  = $_POST['stripeToken'];
			$name = $_POST['name'];
			$email = $_POST['email'];
			$card_num = $_POST['card_num'];
			$card_cvc = $_POST['cvc'];
			$card_exp_month = $_POST['exp_month'];
			$card_exp_year = $_POST['exp_year'];
			$full_address= $_POST['full_address'];
			$pincode=$_POST['pincode'];
			$city=$_POST['city'];
			$state=$_POST['state'];
			$country=$_POST['country'];
			
			//include Stripe PHP library
			require_once APPPATH."third_party/stripe/init.php";
			
			//set api key
			$stripe = array(
			  "secret_key"      => "sk_test_BYlrQvHecEG2C1JC6kRfQPWm00YMcf3KAH",
			  "publishable_key" => "pk_test_mXgaMV1XBnXTke3iq3TtJzhP00mib398zP"
			);
			
			\Stripe\Stripe::setApiKey($stripe['secret_key']);
			
			//add customer to stripe
			$customer = \Stripe\Customer::create(array(
				'email' => $email,
				'source'  => $token
			));

			
			//item information
			$itemName = $this->input->post('item_name');;
			$itemNumber = $this->input->post('item_number');
			$itemPrice = $this->input->post('item_price');
			$currency = "inr";
			$orderID = "1";



			/*
				$itemName = "stripe donation";
				$itemNumber = "PS123456";
				$itemPrice = 50;
				$curency = "inr";
				$itemName="proargi9+";
				orderID = "SKA92712";
			*/
			
			//charge a credit or a debit card
			$charge = \Stripe\Charge::create(array(
				'customer' => $customer->id,
				'amount'   => $itemPrice,
				'currency' => $currency,
				'description' => $itemNumber,
				'metadata' => array(
				'item_id' => $itemNumber
				)
			));
			
			//retrieve charge details
			$chargeJson = $charge->jsonSerialize();

			//check whether the charge is successful
			if($chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1 && $chargeJson['captured'] == 1)
			{
				//order details 
				$amount = $chargeJson['amount'];
				$balance_transaction = $chargeJson['balance_transaction'];
				$currency = $chargeJson['currency'];
				$status = $chargeJson['status'];
				$date = date("Y-m-d H:i:s");
				$date1=date("y-m-d H:i:s");
				
				//insert tansaction data into the database
				$dataDB = array(
					'name' => $name,
					'email' => $email, 
					'card_num' => $card_num, 
					'card_cvc' => $card_cvc, 
					'card_exp_month' => $card_exp_month, 
					'card_exp_year' => $card_exp_year, 
					'item_name' => $itemName, 
					'item_number' => $itemNumber, 
					'item_price' => $itemPrice/100, 
					'item_price_currency' => $currency, 
					'paid_amount' => $amount/100, 
					'paid_amount_currency' => $currency, 
					'txn_id' => $balance_transaction, 
					'payment_status' => $status,
					'created' => $date,
					'modified' => $date,
					'full_address' => $full_address,
					'pincode' => $pincode,
					'city' => $city,
					'state' => $state,
					'country' => $country
				);

				if ($this->db->insert('orders', $dataDB)) {
					if($this->db->insert_id() && $status == 'succeeded'){
						$data['insertID'] = $this->db->insert_id();
						$this->load->view('user/payment_success', $data);
						// redirect('Welcome/payment_success','refresh');
					}else{
						echo "Transaction has been failed";
					}
				}
				else
				{
					echo "not inserted. Transaction has been failed";
				}

			}
			else
			{
				echo "Invalid Token";
				$statusMsg = "";
			}
		}
	}

	public function payment_success()
	{
		$this->load->view('payment_success');
	}

	public function payment_error()
	{
		$this->load->view('payment_error');
	}
	public function loadContact()
	{
		$this->load->view('user/header');
		$this->load->view('user/contact');
		$this->load->view('user/footer');
	}
	public function ins_contact()
	{
		$uname=$this->input->post('uname');
		$uemail=$this->input->post('uemail');
		$usubject=$this->input->post('usubject');
		$umessage=$this->input->post('umessage');
		$ucontact=$this->input->post('ucontact');
		
		

	
	
		$data=array('uname'=>$uname,
		'uemail'=>$uemail,
		'usubject'=>$usubject,
		'umessage'=>$umessage,
		'ucontact'=>$ucontact,
		
	);

		$res=$this->ShopModel->ins_contact($data);
		
		if($res==true)
		{
		
			$this->session->set_flashdata('success','Thank you for sending us your message!');
			redirect('Shop/loadContact');
			
		}
		else{
			$this->session->set_flashdata('success','Message cant send!');
			redirect('Shop/loadContact');
		}	
	}

} 