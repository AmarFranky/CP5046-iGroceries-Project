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


}