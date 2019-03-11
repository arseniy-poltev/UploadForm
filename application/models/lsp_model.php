<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class lsp_model extends CI_Model
{
    protected $table = 'm_lsp';

    function save($data)
    {
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

    function delete($id)
    {
        $db_2 = $this->load->database('default_2_transform_data', TRUE);

        $db_2->where('id', $id);
        $db_2->delete($this->table);
        return true;
    }

    function Insert_or_update($data)
    {
        $db_2 = $this->load->database('default_2_transform_data', TRUE);
        $sql = "select * from " . $this->table . " where lsp_name like " . "'%" . $data['lsp_name'] . "%'";
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