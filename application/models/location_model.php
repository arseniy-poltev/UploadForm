<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class location_model extends CI_Model
{
	protected $table = 'm_locations';

	function save($data) {
		$db_2 = $this->load->database('default_2_transform_data', TRUE);

		$db_2->insert($this->table, $data);
		$insert_id = $db_2->insert_id();
		return $insert_id;
	}

	function update($id, $data)
	{
		$db_2 = $this->load->database('default_2_transform_data', TRUE);

		$db_2->where('id', $id);
		$db_2->update($this->table, $data);
		return true;
	}

	function delete($id){
		$db_2 = $this->load->database('default_2_transform_data', TRUE);

		$db_2->where('id', $id);
		$db_2->delete($this->table);
		return true;
	}

	function Insert_or_update($data, $type) {
		$db_2 = $this->load->database('default_2_transform_data', TRUE);
		$sql = "select * from " . $this->table . " where (a_street like " . "'%" . $data['a_street'] . "%'";
		$sql.= " or a_city like " . "'%" . $data['a_city'] . "%')";
		$sql .= " and type = '" . $type . "'";
		$query = $db_2->query($sql);
		$result = $query->result_array();

		if (count($result) > 0) {
			$id = $result[0]['id'];
			$this->update($result[0]['id'], $data);
		} else {
			$data['type'] = $type;
			$id =$this->save($data);
		}
		return $id;
	}
}
