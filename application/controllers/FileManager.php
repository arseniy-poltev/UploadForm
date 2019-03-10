<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class FileManager extends CI_Controller
{
    protected $host;
    protected $user;
    protected $password;
    protected $db_0;
    protected $db_1;
    protected $db_2;
    protected $db_portal;

    public function __construct()
    {
        parent::__construct();
        $this->config->load('custom_db');
        $this->load->model('transport_mode');
        $this->load->model('materials_model');
        $this->load->model('transport_equipment');

        $this->host = $this->config->item('host');
        $this->user = $this->config->item('user');
        $this->password = $this->config->item('password');
        $this->db_0 = $this->config->item('database_0');
        $this->db_1 = $this->config->item('database_1');
        $this->db_2 = $this->config->item('database_2');
        $this->db_portal = $this->config->item('database_portal');
    }

    /* Transport Modes */
    public function transportModes()
    {
        $this->load->view('file_uploads/common/header');
        $this->load->view('file_uploads/transport_modes');
    }

    public function upload_transport_modes()
    {
        $this->load->library('Mysql');
        $target_file_root = 'assets\uploads';
        $uploads_root = FCPATH . $target_file_root . '/';
        $table_name = 'm_transport_modes';

        if (!is_null($_POST["file_name"])) {
            $upload_filename = $uploads_root . $_POST['file_name'];
            $extention = pathinfo($_FILES["upload_file"]["name"], PATHINFO_EXTENSION);

            /*
             Upload files into server
            */

            if (
                $extention == 'csv'
                || $extention == 'xls'
                || $extention == 'xlsx'
                || $extention == 'json'
            )

            move_uploaded_file($_FILES["upload_file"]["tmp_name"], $upload_filename);

            /* parsing CSV to Array */

            $array_fields = array();
            if ($extention == 'csv'
                || $extention == 'xls'
                || $extention == 'xlsx'
                || $extention == 'json') {
                switch ($extention) {
                    case 'csv':
                        $array_data = $array_fields = array();
                        $i = 0;
                        $handle = @fopen($upload_filename, "r");
                        if ($handle) {
                            while (($row = fgetcsv($handle, 4096)) !== false) {
                                if (empty($array_fields)) {
                                    $array_fields = $row;
                                    continue;
                                }
                                foreach ($row as $k => $value) {
                                    $array_data[$i][$array_fields[$k]] = $value;
                                }
                                $i++;
                            }
                            if (!feof($handle)) {
                                echo "Error: unexpected fgets() fail\n";
                            }
                            fclose($handle);
                        }
                }
            }

            $model_2 = new Mysql($this->host, $this->user, $this->password, $this->db_2);
            $map_data = $model_2->where('table_name', $table_name)
//								->where('file_name', $_POST["file_name"])
                ->get('field_mapping_table', 'f_field,d_field');

            if ($map_data != false && count($map_data) > 0) {

                /*
                 Insert Data into DB_1 and DB_2
                */
                $real_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $_POST['file_name']);
                $model_1 = new Mysql($this->host, $this->user, $this->password, $this->db_1);

                /*
                  Create table with name as Filename
                */
                try{
                    $model_1->create_table($array_fields, $real_name);
                } catch (Exception $exception) {

                }


                /*
                   Empty db_2 table
                 */
                if ($_POST['table_replace_selector'] == 'replace') {
                    $model_2 = new Mysql($this->host, $this->user, $this->password, $this->db_2);
                    $model_2->Empty_table($table_name);
                }

                foreach ($map_data as $item) {
                    $map_object[$item['f_field']] = $item['d_field'];
                }

                foreach ($array_data as $datum) {
                    $temp_item = array();

                    foreach ($map_object as $key => $value) {
                        $temp_item[$value] = trim($datum[$key]);
                    }

                    $model_1 = new Mysql($this->host, $this->user, $this->password, $this->db_1);
                    $sql1 = $model_1->insert($real_name, $datum);
                    $res = $this->transport_mode->Insert_or_update($temp_item['name'], $temp_item);
                }
                echo json_encode('success');
            } else {
                echo json_encode('false');
            }
        }
    }

    /* Materials */
    public function materials() {
        $this->load->view('file_uploads/common/header');
        $this->load->view('file_uploads/materials');
    }

    public function upload_materials() {
        $this->load->library('Mysql');
        $target_file_root = 'assets\uploads';
        $uploads_root = FCPATH . $target_file_root . '/';
        $table_name = 'm_materials';

        if (!is_null($_POST["file_name"])) {
            $upload_filename = $uploads_root . $_POST['file_name'];
            $extention = pathinfo($_FILES["upload_file"]["name"], PATHINFO_EXTENSION);

            /*
             Upload files into server
            */

            if (
                $extention == 'csv'
                || $extention == 'xls'
                || $extention == 'xlsx'
                || $extention == 'json'
            )

                move_uploaded_file($_FILES["upload_file"]["tmp_name"], $upload_filename);

            /* parsing CSV to Array */

            $array_fields = array();
            if ($extention == 'csv'
                || $extention == 'xls'
                || $extention == 'xlsx'
                || $extention == 'json') {
                switch ($extention) {
                    case 'csv':
                        $array_data = $array_fields = array();
                        $i = 0;
                        $handle = @fopen($upload_filename, "r");
                        if ($handle) {
                            while (($row = fgetcsv($handle, 4096)) !== false) {
                                if (empty($array_fields)) {
                                    $array_fields = $row;
                                    continue;
                                }
                                foreach ($row as $k => $value) {
                                    $array_data[$i][$array_fields[$k]] = $value;
                                }
                                $i++;
                            }
                            if (!feof($handle)) {
                                echo "Error: unexpected fgets() fail\n";
                            }
                            fclose($handle);
                        }
                }
            }

            $model_2 = new Mysql($this->host, $this->user, $this->password, $this->db_2);
            $map_data = $model_2->where('table_name', $table_name)
//								->where('file_name', $_POST["file_name"])
                ->get('field_mapping_table', 'f_field,d_field');

            if ($map_data != false && count($map_data) > 0) {

                /*
                 Insert Data into DB_1 and DB_2
                */
                $real_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $_POST['file_name']);
                $model_1 = new Mysql($this->host, $this->user, $this->password, $this->db_1);

                /*
                  Create table with name as Filename
                */
                try{
                    $model_1->create_table($array_fields, $real_name);
                } catch (Exception $exception) {

                }

                /*
                   Empty db_2 table
                 */
                if ($_POST['table_replace_selector'] == 'replace') {
                    $model_2 = new Mysql($this->host, $this->user, $this->password, $this->db_2);
                    $model_2->Empty_table($table_name);
                }

                foreach ($map_data as $item) {
                    $map_object[$item['f_field']] = $item['d_field'];
                }


                foreach ($array_data as $datum) {
                    $temp_item = array();

                    foreach ($map_object as $key => $value) {
                        $temp_item[$value] = trim($datum[$key]);
                    }

                    $model_1 = new Mysql($this->host, $this->user, $this->password, $this->db_1);
                    $sql1 = $model_1->insert($real_name, $datum);

                    $res = $this->materials_model->Insert_or_update($temp_item['mat_num'], $temp_item);
                }
                echo json_encode('success');
            } else {
                echo json_encode('false');
            }
        }
    }

    /* Transport Equipment  */
    public function transport_equipment() {
        $this->load->view('file_uploads/common/header');
        $this->load->view('file_uploads/transport_equipment');
    }

    public function upload_transport_equipment() {
        $this->load->library('Mysql');
        $target_file_root = 'assets\uploads';
        $uploads_root = FCPATH . $target_file_root . '/';
        $table_name = 'transport_equipment';

        if (!is_null($_POST["file_name"])) {
            $upload_filename = $uploads_root . $_POST['file_name'];
            $extention = pathinfo($_FILES["upload_file"]["name"], PATHINFO_EXTENSION);

            /*
             Upload files into server
            */

            if (
                $extention == 'csv'
                || $extention == 'xls'
                || $extention == 'xlsx'
                || $extention == 'json'
            )

                move_uploaded_file($_FILES["upload_file"]["tmp_name"], $upload_filename);
            $array_fields = array();
            if ($extention == 'csv'
                || $extention == 'xls'
                || $extention == 'xlsx'
                || $extention == 'json') {
                switch ($extention) {
                    case 'csv':
                        $array_data = $array_fields = array();
                        $i = 0;
                        $handle = @fopen($upload_filename, "r");
                        if ($handle) {
                            while (($row = fgetcsv($handle, 4096)) !== false) {
                                if (empty($array_fields)) {
                                    $array_fields = $row;
                                    continue;
                                }
                                foreach ($row as $k => $value) {
                                    $array_data[$i][$array_fields[$k]] = $value;
                                }
                                $i++;
                            }
                            if (!feof($handle)) {
                                echo "Error: unexpected fgets() fail\n";
                            }
                            fclose($handle);
                        }
                }
            }

            $model_2 = new Mysql($this->host, $this->user, $this->password, $this->db_2);
            $map_data = $model_2->where('table_name', $table_name)
//								->where('file_name', $_POST["file_name"])
                ->get('field_mapping_table', 'f_field,d_field');

            if ($map_data != false && count($map_data) > 0) {

                /*
                 Insert Data into DB_1 and DB_2
                */
                $real_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $_POST['file_name']);
                $model_1 = new Mysql($this->host, $this->user, $this->password, $this->db_1);

                /*
                  Create table with name as Filename
                */
                try{
                    $model_1->create_table($array_fields, $real_name);
                } catch (Exception $exception) {

                }

                /*
                   Empty db_2 table
                 */
                if ($_POST['table_replace_selector'] == 'replace') {
                    $model_2 = new Mysql($this->host, $this->user, $this->password, $this->db_2);
                    $model_2->Empty_table($table_name);
                }

                foreach ($map_data as $item) {
                    $map_object[$item['f_field']] = $item['d_field'];
                }


                foreach ($array_data as $datum) {
                    $temp_item = array();

                    foreach ($map_object as $key => $value) {
                        $temp_item[$value] = trim($datum[$key]);
                    }

                    $model_1 = new Mysql($this->host, $this->user, $this->password, $this->db_1);
                    $sql1 = $model_1->insert($real_name, $datum);

                    $row_data = $this->transport_equipment->get_equipment_data($temp_item);
                    $res = $this->transport_equipment->Insert_or_update($row_data['serial_number'], $row_data);
                }
                echo json_encode('success');
            } else {
                echo json_encode('false');
            }
        }
    }

    public function vendor_master() {
        $this->load->view('file_uploads/common/header');
        $this->load->view('file_uploads/vendor_master');
    }

    public function upload_vendor_master() {
        $this->load->library('Mysql');
        $target_file_root = 'assets\uploads';
        $uploads_root = FCPATH . $target_file_root . '/';
        $table_name = 'm_vendors';

        if (!is_null($_POST["file_name"])) {
            $upload_filename = $uploads_root . $_POST['file_name'];
            $extention = pathinfo($_FILES["upload_file"]["name"], PATHINFO_EXTENSION);

            /*
             Upload files into server
            */

            if (
                $extention == 'csv'
                || $extention == 'xls'
                || $extention == 'xlsx'
                || $extention == 'json'
            )

            move_uploaded_file($_FILES["upload_file"]["tmp_name"], $upload_filename);
            $array_fields = array();
            if ($extention == 'csv'
                || $extention == 'xls'
                || $extention == 'xlsx'
                || $extention == 'json') {
                switch ($extention) {
                    case 'csv':
                        $array_data = $array_fields = array();
                        $i = 0;
                        $handle = @fopen($upload_filename, "r");
                        if ($handle) {
                            while (($row = fgetcsv($handle, 4096)) !== false) {
                                if (empty($array_fields)) {
                                    $array_fields = $row;
                                    continue;
                                }
                                foreach ($row as $k => $value) {
                                    $array_data[$i][$array_fields[$k]] = $value;
                                }
                                $i++;
                            }
                            if (!feof($handle)) {
                                echo "Error: unexpected fgets() fail\n";
                            }
                            fclose($handle);
                        }
                }
            }
            $model_2 = new Mysql($this->host, $this->user, $this->password, $this->db_2);
            $map_data = $model_2->where('table_name', $table_name)
//								->where('file_name', $_POST["file_name"])
                ->get('field_mapping_table', 'f_field,d_field');

            if ($map_data != false && count($map_data) > 0) {
                /*
                Insert Data into DB_1 and DB_2
               */
                $real_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $_POST['file_name']);
                $model_1 = new Mysql($this->host, $this->user, $this->password, $this->db_1);

                /*
                  Create table with name as Filename
                */
                try{
                    $model_1->create_table($array_fields, $real_name);
                } catch (Exception $exception) {

                }

                /*
                   Empty db_2 table
                 */
                if ($_POST['table_replace_selector'] == 'replace') {
                    $model_2 = new Mysql($this->host, $this->user, $this->password, $this->db_2);
                    $model_2->Empty_table($table_name);
                }

                foreach ($map_data as $item) {
                    $map_object[$item['f_field']] = $item['d_field'];
                }
            }

        }
    }
}