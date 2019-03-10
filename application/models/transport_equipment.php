<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class transport_equipment extends CI_Model
{
    protected $table = 'transport_equipment';

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
        $query = $db_2->get($this->table);
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
        return true;
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

    function Insert_or_update($name, $data) {
        $db_2 = $this->load->database('default_2_transform_data', TRUE);
        $sql = "select * from " . $this->table . " where serial_number like " . "'%" . $name . "%'";
        $query = $db_2->query($sql);
        $result = $query->result_array();

        if (count($result) > 0) {
            $this->update($result[0]['id'], $data);
        } else {
            $this->save($data);
        }
        return true;
    }

    function get_equipment_data($model) {
        $transport_mode_tb = 'm_transport_modes';
        $db_2 = $this->load->database('default_2_transform_data', TRUE);

        $sql = "select * from " . $transport_mode_tb . " where name like " . "'%" . $model['transport_mode_id'] . "%'";
        $query = $db_2->query($sql);
        $result = $query->result_array();

        if (count($result) > 0) {
            $model['transport_mode_id'] = $result[0]['id'];
        } else {
            $model['transport_mode_id'] = '';
        }

        return $model;
    }
}