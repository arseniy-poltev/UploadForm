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
        $this->load->model('location_model');
        $this->load->model('vendor_model');
        $this->load->model('customer_model');
        $this->load->model('sales_organization_model');
        $this->load->model('sales_region_model');
        $this->load->model('lsp_model');


        $this->host = $this->config->item('host');
        $this->user = $this->config->item('user');
        $this->password = $this->config->item('password');
        $this->db_0 = $this->config->item('database_0');
        $this->db_1 = $this->config->item('database_1');
        $this->db_2 = $this->config->item('database_2');
        $this->db_portal = $this->config->item('database_portal');
    }

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
                try {
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

    public function materials()
    {
        $this->load->view('file_uploads/common/header');
        $this->load->view('file_uploads/materials');
    }

    public function upload_materials()
    {
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
                try {
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

    public function transport_equipment()
    {
        $this->load->view('file_uploads/common/header');
        $this->load->view('file_uploads/transport_equipment');
    }

    public function upload_transport_equipment()
    {
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
                try {
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

    public function vendor_master()
    {
        $this->load->view('file_uploads/common/header');
        $this->load->view('file_uploads/vendor_master');
    }

    public function upload_vendor_master()
    {
        $this->load->library('Mysql');
        $target_file_root = 'assets\uploads';
        $uploads_root = FCPATH . $target_file_root . '/';
        $table_name = 'm_vendors';
        $geocoding = $_POST['geocoding_selector'];

        if (!is_null($_POST["file_name"])) {
            $upload_filename = $uploads_root . $_POST['file_name'];
            $extention = pathinfo($_FILES["upload_file"]["name"], PATHINFO_EXTENSION);

            /*geocoding_selector
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
                        $csv_data = parse_csv($upload_filename);
                        $array_fields = $csv_data['array_fields'];
                        $array_data = $csv_data['array_data'];
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
                try {
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

                    $country_name = '';
                    $country_code = '';
                    $zip_code = '';
                    $street = '';
                    $city = '';

                    $vendor_item = $location_item = array();

                    foreach ($map_object as $key => $value) {

                        if ($value == 'v_name' || $value == 'v_number') {
                            $vendor_item[$value] = trim($datum[$key]);
                        } else {
                            if ($value == 'a_country') {
                                $country_name = trim($datum[$key]);
                            }

                            if ($value == 'a_zip') {
                                $zip_code = trim($datum[$key]);
                            }

                            switch ($value) {
                                case 'a_country':
                                    $country_name = trim($datum[$key]);
                                    break;
                                case 'a_zip':
                                    $zip_code = trim($datum[$key]);
                                    break;
                                case 'a_city':
                                    $city = trim($datum[$key]);
                                    break;
                                case 'a_street':
                                    $street = trim($datum[$key]);
                                    break;
                            }
                            $location_item[$value] = trim($datum[$key]);
                        }
                    }


                    // get position data
                    if ($country_name) {
                        $model_1 = new Mysql($this->host, $this->user, $this->password, $this->db_0);
                        $country_model = $model_1->where('country', $country_name)->get('m_country_code');
                        if (count($country_model) > 0) {
                            $country_code = $country_model[0]['code'];
                        }
                    }

                    $location_item['a_country'] = $country_code;

                    $latitude = '';
                    $longitude = '';
                    $search = "address=" . $street . "+" . $zip_code . " + " . $city . " + " . $country_code;

                    if ($geocoding == 'google_map') {
                        $json_api = get_googleApi_data(63, $search);
                    }

                    if ($geocoding == 'uni_heid') {
                        $json_api = $this->get_googleApi_data(70, $search);
                    }
                    if (isset($json_api['results'][0]['geometry'])) {
                        $latitude = $json_api['results'][0]['geometry']['location']['lat'];
                        $longitude = $json_api['results'][0]['geometry']['location']['lng'];
                    }

                    $location_item['a_latitude'] = $latitude;
                    $location_item['a_longitude'] = $longitude;

                    $location_id = $this->location_model->Insert_or_update($location_item, "Supplier");
                    $vendor_item['v_shipfrom_location_id'] = $location_id;

                    $vendor_item['lastupdated'] = date('Y-m-d h:i:s');
                    $res = $this->vendor_model->Insert_or_update($vendor_item);
                }
                echo json_encode('success');
            } else {
                echo json_encode('failure');
            }
        }
    }

    public function customer()
    {
        $this->load->view('file_uploads/common/header');
        $this->load->view('file_uploads/customer_master');
    }

    public function upload_customer_master()
    {

        $this->load->library('Mysql');
        $target_file_root = 'assets\uploads';
        $uploads_root = FCPATH . $target_file_root . '/';
        $table_name = 'm_customers';
        $geocoding = $_POST['geocoding_selector'];

        if (!is_null($_POST["file_name"])) {
            $upload_filename = $uploads_root . $_POST['file_name'];
            $extention = pathinfo($_FILES["upload_file"]["name"], PATHINFO_EXTENSION);

            /*geocoding_selector
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
                        $csv_data = parse_csv($upload_filename);
                        $array_fields = $csv_data['array_fields'];
                        $array_data = $csv_data['array_data'];
                }
            }


            $real_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $_POST['file_name']);

            $model_2 = new Mysql($this->host, $this->user, $this->password, $this->db_2);
            $map_data = $model_2->where('table_name', $table_name)
//								->and_where('file_name', $real_name)
                ->get('field_mapping_table', 'f_field,d_field');


            if ($map_data != false && count($map_data) > 0) {
                /*
                Insert Data into DB_1 and DB_2
               */
                $model_1 = new Mysql($this->host, $this->user, $this->password, $this->db_1);

                /*
                  Create table with name as Filename
                */
                try {
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

                    $country_name = '';
                    $country_code = '';
                    $zip_code = '';
                    $street = '';
                    $city = '';
                    $sales_organization = '';
                    $sales_region = '';

                    $customer_item = $location_item = array();

                    foreach ($map_object as $key => $value) {

                        if ($value == 'c_name' || $value == 'c_number') {
                            $customer_item[$value] = trim($datum[$key]);
                        } else {
                            if ($value == 'name')
                                $sales_organization = trim($datum[$key]);
                            if ($value == 'r_name')
                                $sales_region = trim($datum[$key]);

                            switch ($value) {
                                case 'a_country':
                                    $country_name = trim($datum[$key]);
                                    $location_item[$value] = trim($datum[$key]);

                                    break;
                                case 'a_zip':
                                    $zip_code = trim($datum[$key]);
                                    $location_item[$value] = trim($datum[$key]);

                                    break;
                                case 'a_city':
                                    $city = trim($datum[$key]);
                                    $location_item[$value] = trim($datum[$key]);

                                    break;
                                case 'a_street':
                                    $street = trim($datum[$key]);
                                    $location_item[$value] = trim($datum[$key]);

                                    break;
                            }
                        }
                    }


                    // get position data
                    if ($country_name) {
                        $model_1 = new Mysql($this->host, $this->user, $this->password, $this->db_0);
                        $country_model = $model_1->where('country', $country_name)->get('m_country_code');
                        if (count($country_model) > 0) {
                            $country_code = $country_model[0]['code'];
                        }
                    }

                    $location_item['a_country'] = $country_code;

                    $latitude = '';
                    $longitude = '';
                    $search = "address=" . $street . "+" . $zip_code . " + " . $city . " + " . $country_code;

                    if ($geocoding == 'google_map') {
                        $json_api = get_googleApi_data(63, $search);
                    }

                    if ($geocoding == 'uni_heid') {
                        $json_api = $this->get_googleApi_data(70, $search);
                    }
                    if (isset($json_api['results'][0]['geometry'])) {
                        $latitude = $json_api['results'][0]['geometry']['location']['lat'];
                        $longitude = $json_api['results'][0]['geometry']['location']['lng'];
                    }

                    $location_item['a_latitude'] = $latitude;
                    $location_item['a_longitude'] = $longitude;

                    $sales_organization_item['name'] = $sales_organization;
                    $sales_region_item['r_name'] = $sales_region;

                    $location_id = $this->location_model->Insert_or_update($location_item, 'Customer');
                    $sales_organization_id = $this->sales_organization_model->Insert_or_update($sales_organization_item);
                    $sales_region_id = $this->sales_region_model->Insert_or_update($sales_region_item);


                    $customer_item['c_sales_organization'] = $sales_organization_id;
                    $customer_item['c_sales_region'] = $sales_region_id;
                    $customer_item['c_shipto_location_id'] = $location_id;

                    $customer_item['lastupdated'] = date('Y-m-d h:i:s');
                    $res = $this->customer_model->Insert_or_update($customer_item);
                }
                echo json_encode('success');
            } else {
                echo json_encode('failure');
            }
        }
    }

    public function carriers_logistics()
    {
        $this->load->view('file_uploads/common/header');
        $this->load->view('file_uploads/carriers_logistics');
    }

    public function upload_carriers_logistics()
    {
        $this->load->library('Mysql');
        $target_file_root = 'assets\uploads';
        $uploads_root = FCPATH . $target_file_root . '/';
        $table_name = 'm_lsp';
        $geocoding = $_POST['geocoding_selector'];

        if (!is_null($_POST["file_name"])) {
            $upload_filename = $uploads_root . $_POST['file_name'];
            $extention = pathinfo($_FILES["upload_file"]["name"], PATHINFO_EXTENSION);

            /*geocoding_selector
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
                        $csv_data = parse_csv($upload_filename);
                        $array_fields = $csv_data['array_fields'];
                        $array_data = $csv_data['array_data'];
                }
            }


            $real_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $_POST['file_name']);

            $model_2 = new Mysql($this->host, $this->user, $this->password, $this->db_2);
            $map_data = $model_2->where('table_name', $table_name)
//								->and_where('file_name', $real_name)
                ->get('field_mapping_table', 'f_field,d_field');


            if ($map_data != false && count($map_data) > 0) {
                /*
                Insert Data into DB_1 and DB_2
               */
                $model_1 = new Mysql($this->host, $this->user, $this->password, $this->db_1);

                /*
                  Create table with name as Filename
                */
                try {
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

                    $country_name = '';
                    $country_code = '';
                    $zip_code = '';
                    $street = '';
                    $city = '';

                    $lsp_item = $location_item = array();

                    foreach ($map_object as $key => $value) {
                        switch ($value) {
                            case 'a_country':
                                $country_name = trim($datum[$key]);
                                $location_item[$value] = trim($datum[$key]);
                                break;
                            case 'a_zip':
                                $zip_code = trim($datum[$key]);
                                $location_item[$value] = trim($datum[$key]);

                                break;
                            case 'a_city':
                                $city = trim($datum[$key]);
                                $location_item[$value] = trim($datum[$key]);

                                break;
                            case 'a_street':
                                $street = trim($datum[$key]);
                                $location_item[$value] = trim($datum[$key]);
                                break;
                            default:
                                $lsp_item[$value] = trim($datum[$key]);
                                break;
                        }
                    }

                    // get position data
                    if ($country_name) {
                        $model_1 = new Mysql($this->host, $this->user, $this->password, $this->db_0);
                        $country_model = $model_1->where('country', $country_name)->get('m_country_code');
                        if (count($country_model) > 0) {
                            $country_code = $country_model[0]['code'];
                        }
                    }


                    $location_item['a_country'] = $country_code;

                    $latitude = '';
                    $longitude = '';
                    $search = "address=" . $street . "+" . $zip_code . " + " . $city . " + " . $country_code;

                    if ($geocoding == 'google_map') {
                        $json_api = get_googleApi_data(63, $search);
                    }

                    if ($geocoding == 'uni_heid') {
                        $json_api = get_googleApi_data(70, $search);
                    }
                    if (isset($json_api['results'][0]['geometry'])) {
                        $latitude = $json_api['results'][0]['geometry']['location']['lat'];
                        $longitude = $json_api['results'][0]['geometry']['location']['lng'];
                    }

                    $location_item['a_latitude'] = $latitude;
                    $location_item['a_longitude'] = $longitude;


                    $location_id = $this->location_model->Insert_or_update($location_item, 'LSP');

                    $lsp_item['location_id'] = $location_id;
                    $res = $this->lsp_model->Insert_or_update($lsp_item);
                }
                echo json_encode('success');
            } else {
                echo json_encode('failure');
            }
        }
    }

    public function locations()
    {
        $this->load->view('file_uploads/common/header');
        $this->load->view('file_uploads/locations');
    }

    public function upload_location()
    {
        $this->load->library('Mysql');

        $target_file_root = 'assets\uploads';
        $uploads_root = FCPATH . $target_file_root . '/';

        $api_string = $_POST['api_list'];
        $geocoding = $_POST['geocoding_selector'];
        $table_name = 'm_locations';
        $api = explode(',', $api_string);

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
                        $csv_data = parse_csv($upload_filename);
                        $array_data = $csv_data['array_data'];
                        $array_fields = $csv_data['array_fields'];
                }
            }

            /*
              Get map table data
            */
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
                $res = $model_1->create_table($array_fields, $real_name);


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


					$country_name = '';
					$city_code = '';
					$street_code = '';
					$zip_code = '';

					$location_item = array();
					$sales_regions = array();
					$lsp_region = array();


					foreach ($map_object as $key => $value) {

						if ($value == 'r_name') {
							$sales_regions[$value] = $datum[$key];
						} else if ($value == 'lsp_name') {
							$lsp_region[$value] = $datum[$key];
						} else {
							$location_item[$value] = $datum[$key];
						}

						switch ($value) {
							case 'a_country':
								$country_name = $datum[$key];
								break;
							case 'a_city':
								$city_code = $datum[$key];
								break;
							case 'a_street':
								$street_code = $datum[$key];
								break;
							case 'a_zip':
								$zip_code = $datum[$key];
								break;
						}
					}


					// get position data
					if ($country_name) {
						$model_1 = new Mysql($this->host, $this->user, $this->password, $this->db_0);
						$country_model = $model_1->where('country', $country_name)->get('m_country_code');
						if (count($country_model) > 0) {
							$country_code = $country_model[0]['code'];
						}
					}

					$location_item['a_country'] = $country_code;

					$latitude = '';
					$longitude = '';


					$json_data = array();
					if (in_array('19', $api)) {
						$json_api = get_quandlApi_data(19, 'city', $city_code);
						if(isset($json_api['dataset']) && count($json_api['dataset']['data']) > 0) {
							$json_data['City Population'] = $json_api['dataset']['data'][0][1];
						} else {
							$json_data['City Population'] = '';
						}
					}


					if (in_array('50', $api)) {
						$json_api = get_quandlApi_data(50, 'country', $city_code);
						if(isset($json_api['dataset']) && count($json_api['dataset']['data']) > 0) {
							$json_data['World Development Indicators'] = $json_api['dataset']['data'][0][1];
						} else {
							$json_data['World Development Indicators'] = '';
						}
					}

					if (in_array('51', $api)) {
						$json_api = get_quandlApi_data(51, 'country', $city_code);
						if(isset($json_api['dataset']) && count($json_api['dataset']['data']) > 0) {
							$json_data['Doing Business 1'] = $json_api['dataset']['data'][0][1];
						} else {
							$json_data['Doing Business 1'] = '';
						}
					}

					if (in_array('52', $api)) {
						$json_api = get_quandlApi_data(52, 'country', $city_code);
						if(isset($json_api['dataset']) && count($json_api['dataset']['data']) > 0) {
							$json_data['Doing Business 2'] = $json_api['dataset']['data'][0][1];
						} else {
							$json_data['Doing Business 2'] = '';
						}
					}

					$search = "address=" . $street_code . "+" . $zip_code . " + " . $city_code . " + " . $country_code;
					if (in_array('62', $api)) {
						if ($geocoding == 'google_map') {
							$json_api = get_googleApi_data(62, $search);
						}
					} else if (in_array('69', $api)) {
						if ($geocoding == 'uni_heid') {
							$json_api = get_googleApi_data(69, $search);
						}
					}


					if (isset($json_api['results'][0]['geometry'])) {
						$latitude = $json_api['results'][0]['geometry']['location']['lat'];
						$longitude = $json_api['results'][0]['geometry']['location']['lng'];
					}

					$sales_region_id = $this->sales_region_model->Insert_or_update($sales_regions);
					$lsp_id = $this->lsp_model->Insert_or_update($lsp_region);


					$location_item['a_latitude'] = $latitude;
					$location_item['a_longitude'] = $longitude;
					$location_item['operator'] = $lsp_id;
					$location_item['a_sales_region'] = $sales_region_id;
					if ($json_data) {
						$location_item['a_additionals'] = json_encode($json_data);
					}
					$model_1 = new Mysql($this->host, $this->user, $this->password, $this->db_1);
					$sql1 = $model_1->insert($real_name, $datum);

					$location_id = $this->location_model->Insert_or_update($location_item);
				}

				echo json_encode("success");
			}
			else {
				echo json_encode("failure");
			}

        }
    }

    public function headcount() {
		$this->load->view('file_uploads/common/header');
		$this->load->view('file_uploads/headcount');
	}

	public function upload_headcount() {
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
				try {
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
}
