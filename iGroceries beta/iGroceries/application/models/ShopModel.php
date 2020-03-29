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
	


}