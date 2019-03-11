<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class sales_region_model extends CI_Model
{
    protected $table = 'm_sales_regions';

    public function get_count($sql){
        $db_2 = $this->load->database('default_2_transform_data', TRUE);
        $query = $db_2->query($sql);
        $result = $query->row_array();
        return  $result["count(*)"];
    }

    public function get_content($sql){
        $db_2 = $this->load->database('default_2_transform_data', TRUE);

        $query = $db_2->query($sql);
        $result = $query->result_array();
        return $result;
    }

    function get($id) {
        $db_2 = $this->load->database('default_2_transform_data', TRUE);

        $db_2->where('id', $id);
        $query = $db_2->get($this->table_name);
        $result = $query->result_array();

        if(count($result) > 0) {
            return $result[0];
        }
        else {
            return false;
        }
    }

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

    function Insert_or_update($data) {
        $db_2 = $this->load->database('default_2_transform_data', TRUE);
        $sql = "select * from " . $this->table . " where r_name like " . "'%" . $data['r_name'] . "%'";
        $query = $db_2->query($sql);
        $result = $query->result_array();

        if (count($result) > 0) {
            $id = $result[0]['id'];
            $this->update($result[0]['id'], $data);
        } else {
            $id =$this->save($data);
        }
        return $id;
    }
}
