<?php 

class User_model extends CI_Model{

	public function __construct(){

		$this->load->database();

	}

	public function set_user($user)
	{
		return $this->db->insert('utilisateur', $user);
	}

	public function get_user($id_fb){

		
		$query = $this->db->get_where('utilisateur', array('id_fb' => $id_fb));
		//var_dump($query);
        return $query->row_array();
		 
	}

}



 ?>