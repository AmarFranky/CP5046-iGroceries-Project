<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class ShopModel extends CI_Model {
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	public function ins_customer($data)
	{
		$this->db->insert('customer',$data);
		return true;
	}
	public function loginCustomer($email,$password)
	{
		$this->db->where('email',$email);
		$this->db->where('password',base64_encode($password));
		$result=$this->db->get('customer');
		return $result->result();
	}
	


}