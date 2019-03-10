<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mapping_model extends CI_Model
{
	public $table_name = "field_mapping_table";


	public function get_count($sql){
		$query = $this->db->query($sql);
		$result = $query->row_array();
		return  $result["count(*)"];
	}

	public function get_content($sql){
		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}

	function get($id) {
		$this->db->where('id', $id);
		$query = $this->db->get($this->table_name);
		$result = $query->result_array();

		if(count($result) > 0) {
			return $result[0];
		}
		else {
			return false;
		}
	}

	function save($data) {
		$this->db->insert($this->table_name, $data);
		return true;
	}

	function update($id, $data)
	{
		$this->db->where('id', $id);
		$this->db->update($this->table_name, $data);
		return true;
	}

	function delete($id){
		$this->db->where('id', $id);
		$this->db->delete($this->table_name);
		return true;
	}
}

