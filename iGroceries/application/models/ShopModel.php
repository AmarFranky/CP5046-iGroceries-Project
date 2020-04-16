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
	public function get_beverages()
	{
		$this->db->where('item_category','Beverages');
		$res=$this->db->get('items');
		return $res->result();

	}
	public function get_bread()
	{
		$this->db->where('item_category','Bread/Bakery');
		$res=$this->db->get('items');
		return $res->result();
	}
	public function get_frozen()
	{
		$this->db->where('item_category','Frozen Foods');
		$res=$this->db->get('items');
		return $res->result();
	}
	public function get_cleaners()
	{
		$this->db->where('item_category','Cleaners');
		$res=$this->db->get('items');
		return $res->result();
	}
	public function get_canned()
	{
		$this->db->where('item_category','Canned/Jarred Goods');
		$res=$this->db->get('items');
		return $res->result();
	}
	public function get_dry()
	{
		$this->db->where('item_category','Dry/Baking Goods');
		$res=$this->db->get('items');
		return $res->result();
	}
	public function get_dairy()
	{
		$this->db->where('item_category','Dairy');
		$res=$this->db->get('items');
		return $res->result();
	}
	public function get_personal()
	{
		$this->db->where('item_category','Personal Care');
		$res=$this->db->get('items');
		return $res->result();
	}
	public function get_others()
	{
		$this->db->where('item_category','Others');
		$res=$this->db->get('items');
		return $res->result();
	}
	public function get_new()
	{
		$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
		$prev_date = date('Y-m-d', strtotime($date .' -10 day'));
		$next_date = date('Y-m-d', strtotime($date .' +10 day'));
		$current_date=date('Y-m-d');
		$this->db->where('item_arrive >=', $prev_date);
		$this->db->limit(4);
		$res=$this->db->get('items');
		return $res->result();
	}
	public function get_details($id)
	{
		$this->db->where('item_id',$id);
		$res=$this->db->get('items');
		return $res->result();

	}
	


}