<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class AdminModel extends CI_Model {
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	public function adminLogin($email,$password)
	{
		$this->db->where('email',$email);
		$this->db->where('password',$password);
		$res=$this->db->get('admin');
		return $res->result();
	}
	public function addItem($data)
	{
		$result=$this->db->insert('items',$data);
		return true;
	}
	public function getCat()
	{
		$res=$this->db->get('item_categories');
		return $res->result();
	}
	public function showProducts()
	{
		$res=$this->db->get('items');
		return $res->result();
	}
	public function del_prod($id)
	{
		$this->db->where('item_id',$id);
		$this->db->delete('items');
		return true;
	}
	public function del_cust($id)
	{
		$this->db->where('customer_id',$id);
		$this->db->delete('customer');
		return true;
	}
	public function getProductFromId($id)
	{
		$this->db->where('item_id',$id);
		$res=$this->db->get('items');
		return $res->result();
	}
	public function getCust()
	{
		$res=$this->db->get('customer');
		return $res->result();
	}
	public function updateProduct($id, $data)
	{
		$this->db->where('item_id',$id);
		$this->db->update('items',$data);
		return true;

	}


}