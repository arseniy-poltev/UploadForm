<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class UploadManager extends CI_Controller
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
        $this->load->model('mapping_model');
        $this->host = $this->config->item('host');
        $this->user = $this->config->item('user');
        $this->password = $this->config->item('password');
        $this->db_0 = $this->config->item('database_0');
        $this->db_1 = $this->config->item('database_1');
        $this->db_2 = $this->config->item('database_2');
        $this->db_portal = $this->config->item('database_portal');
    }

    public function index()
    {
        $this->load->library('Mysql');
        $db_model = new Mysql($this->host, $this->user, $this->password, $this->db_2);
        $res = $db_model->where('table_schema', '2_transform_data')->get('information_schema.tables', 'table_name');
        $data['table_list'] = $res;

        $this->load->view('index', $data);
    }

    public function getApiList() {
        $this->load->library('Mysql');
        $table = $this->input->post('table');
        $model_1 = new Mysql($this->host, $this->user, $this->password, $this->db_1);
        $api = $model_1->where('table_name', $table)->get('m_api_manage');

        if ($api && count($api) > 0) {
            $res['state'] = 'success';
            $res['data'] = explode(',', $api[0]['api_list']);
        } else {
            $res['state'] = 'false';
        }
        echo json_encode($res);
    }
    public function mapIndex()
    {
        $this->load->view('mapList');
    }

    public function mappingList()
    {
        $cols = array("a.id", "a.table_name", "a.file_name", "a.f_field", "a.d_field", "a.last_modified");
        $row_col = array('id', 'table_name', 'file_name', 'f_field', 'd_field', 'last_modified');
        $table = "field_mapping_table a";
        $result = array();

        $amount = 10;
        $start = 0;
        $col = 5;

        $dir = "desc";
        $sStart = $this->input->get_post('start');
        $sAmount = $this->input->get_post('length');

        $sCol = "";
        $sdir = "";

        $sCol = $this->input->get_post("order");

        $searchTerm = "";
        $search = $this->input->get_post("search");
        foreach ($search as $key => $value) {
            if ($key == 'value')
                $searchTerm = $value;
        }


        foreach ($sCol as $row) {
            foreach ($row as $key => $value) {
                if ($key == 'column')
                    $sCol = $value;
                if ($key == 'dir')
                    $sdir = $value;
            }
        }


        if ($sStart !== false && strlen($sStart) > 0) {
            $start = intval($sStart);
            if ($start < 0) {
                $start = 0;
            }
        }

        if ($sAmount !== false && strlen($sAmount) > 0) {
            $amount = intval($sAmount);
            if ($amount < 6 || $amount > 100) {
                $amount = 6;
            }
        }

        if ($sCol !== false && strlen($sCol) > 0) {
            $col = intval($sCol);
            if ($col < 0 || $col > 7) {
                $col = 0;
            }
        }

        if ($sdir && strlen($sdir) > 0) {
            if ($sdir != "desc") {
                $dir = "asc";
            }
        }

        $colName = $cols[$col];

//        echo $colName;die();
        $total = 0;
        $totalAfterFilter = 0;

        $sql = " select count(*) from " . $table;
        $total = $this->mapping_model->get_count($sql);
        $totalAfterFilter = $total;


        $sql = " select  a.*, '' as action from " . $table . "  ";
        $searchSQL = "";


        $row_col = array('id', 'table_name', 'file_name', 'f_field', 'd_field', 'last_modified');

        $globalSearch = " ( "
            . " a.table_name like '%" . $searchTerm . "%' or "
            . " a.file_name like '%" . $searchTerm . "%' or "
            . " a.f_field like '%" . $searchTerm . "%' or "
            . " a.d_field like '%" . $searchTerm . "%' "
            . " ) ";

        if ($searchTerm && strlen($searchTerm) > 0) {
            $searchSQL .= " where " . $globalSearch;
        }

        $sql .= $searchSQL;
        $sql .= " order by " . $colName . " " . $dir . " ";
        $sql .= " limit " . $start . ", " . $amount . " ";
        $data = $this->mapping_model->get_content($sql);

        $sql = " select count(*) from " . $table . " ";

        $_SESSION['order_col'] = $row_col[$col];
        $_SESSION['order_rule'] = $dir;
        $_SESSION['page_length'] = $amount;


        if (strlen($searchSQL) > 0) {
            $sql .= $searchSQL;
            $totalAfterFilter = $this->mapping_model->get_count($sql);
        }

        $result["recordsTotal"] = $total;
        $result["recordsFiltered"] = $totalAfterFilter;
        $result["data"] = $data;


        echo json_encode($result);
    }

    public function geMap()
    {
        $id = $this->input->get_post("id");
        $item = $this->mapping_model->get($id);

        if ($item) {
            $res['state'] = 1;
            $res['data'] = $item;
        } else {
            $res['state'] = 0;
        }

        echo json_encode($res);
    }

    public function saveMap()
    {
        $form_data = $this->input->post();
        $form_data['last_modified'] = date("Y-m-d h:i:s");
        if ($form_data['id']) {
            $id = $form_data['id'];
            unset($form_data['id']);
            $this->mapping_model->update($id, $form_data);
        } else {
            unset($form_data['id']);
            $this->mapping_model->save($form_data);
        }

        $res['state'] = 1;
        echo json_encode($res);
    }

    public function deleteMap()
    {
        $id = $this->input->post('id');
        $this->mapping_model->delete($id);
        $res['state'] = 1;
        echo json_encode($res);
    }

    public function get_Sales_region_Api($id, $country_code, $state_code = '', $r_cluster_1 = '') {
        $model_2 = new Mysql($this->host, $this->user, $this->password, $this->db_1);
        $api = $model_2->where('id', $id)
            ->get('m_api_list');


        if (count($api) > 0) {
            $url = $api[0]['api_url'];
            if ($state_code == '' && $r_cluster_1 == '') {
                $url = $model_2->getUrl($url, $country_code, $api[0]['api_key']);
            } else {
                $search = "address=" . $state_code . "+" . $country_code . " + " . $r_cluster_1;

                if (strpos($url, '{$search}') !== false) {
                    $url = str_replace('{$search}', $search, $api[0]['api_url']);


                } else {
                    $url = str_replace('$search', $search, $api[0]['api_url']);
                }

                $url = str_replace('key=$key', ltrim(trim($api[0]['api_key']), '$'), $url);
            }
        }

//		return $url;
        $json_api = $model_2->exe_curl($url);
        return $json_api;
    }


    public function uploadFile()
    {
        $this->load->library('Mysql');

        $target_file_root = 'assets\uploads';
        $uploads_root = FCPATH . $target_file_root . '/';

        $api_string = $_POST['api_list'];
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

            /*
                Parsing file content into Array
            */
            $fields = array();
            if ($extention == 'csv'
                || $extention == 'xls'
                || $extention == 'xlsx'
                || $extention == 'json') {
                switch ($extention) {
                    case 'csv':
                        $array = $fields = array();
                        $i = 0;
                        $handle = @fopen($upload_filename, "r");
                        if ($handle) {
                            while (($row = fgetcsv($handle, 4096)) !== false) {
                                if (empty($fields)) {
                                    $fields = $row;
                                    continue;
                                }
                                foreach ($row as $k => $value) {
                                    $array[$i][$fields[$k]] = $value;
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


            /*
              Get map table data
            */
            $table_name = $_POST['table_selector'];
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
                $res = $model_1->create_table($fields, $real_name);


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


                switch ($table_name) {

                    /* Sales Regions */

                    case 'm_sales_regions':

                        $country_key = '';
                        $r_state_key = '';
                        $r_cluster1_key = '';

                        foreach ($map_object as $key => $value) {
                            if ($value == 'r_country') {
                                $country_key = $key;
                            } else if ($value == 'r_name') {
                                $r_state_key = $key;
                            } else if ($value == 'r_cluster_1') {
                                $r_cluster1_key = $key;
                            }
                        }

                        $i = 0;
                        foreach ($array as $item) {


                            $temp_item = array();
                            foreach ($map_object as $key => $value) {
                                $temp_item[$value] = trim($item[$key]);
                            }

                            // Api request

                            $country_name = trim($item[$country_key]);

                            $model_1 = new Mysql($this->host, $this->user, $this->password, $this->db_0);
                            $country_model = $model_1->where('country', $country_name)->get('m_country_code');

                            $country_code = $country_model[0]['code'];
                            $r_state = trim($item[$r_state_key]);
                            $r_cluster_1 = trim($item[$r_cluster1_key]);


                            $population_json = array();
                            /* Population Projection (ID = 18)  */
                            if (in_array('18', $api)) {
                                $json_api = $this->get_Sales_region_Api(18, $country_code);

                                if (isset($json_api['dataset'])) {
                                    /*--------------------------------------------------------*/
                                    $current_population2022 = $json_api['dataset']['data'][0][1];
                                    $current_population2021 = $json_api['dataset']['data'][1][1];
                                    $current_population2020 = $json_api['dataset']['data'][2][1];
                                    $current_population2019 = $json_api['dataset']['data'][3][1];
                                    /*--------------------------------------------------------*/
                                } else {
                                    $current_population2022 = "";
                                    $current_population2021 = "";
                                    $current_population2020 = "";
                                    $current_population2019 = "";
                                }

                                $population_json["current_population2022"] = $current_population2022;
                                $population_json["current_population2021"] = $current_population2021;
                                $population_json["current_population2020"] = $current_population2020;
                                $population_json["current_population2019"] = $current_population2019;
                            }



                            /*Employments (ID=53)*/
                            if (in_array('53', $api)) {
                                $json_api = $this->get_Sales_region_Api(53, $country_code);

                                if(isset($json_api['dataset'])) {
                                    $employment = $json_api['dataset']['data'][0][1];
                                } else  {
                                    $employment = '';
                                }

                                $population_json["employment"] = $employment;
                            }


                            /*Unemployments (ID=54)*/
                            if (in_array('54', $api)) {
                                $json_api = $this->get_Sales_region_Api(54, $country_code);
                                if(isset($json_api['dataset'])) {
                                    $unemployment = $json_api['dataset']['data'][0][1];
                                } else  {
                                    $unemployment = '';
                                }
                                $population_json["unemployment"] = $unemployment;

                            }


                            /*GDP Deflatpr (ID=55)*/

                            if (in_array('55', $api)) {
                                $json_api = $this->get_Sales_region_Api(55, $country_code);
                                if (isset($json_api['dataset'])) {
                                    $gdp_deflator = $json_api['dataset']['data'][0][1];
                                } else {
                                    $gdp_deflator = '';
                                }
                                $population_json["gdp_deflator"] = $gdp_deflator;

                            }

                            /*GNI Deflatpr (ID=57)*/
                            if (in_array('57', $api)) {
                                $json_api = $this->get_Sales_region_Api(57, $country_code);
                                if (isset($json_api['dataset'])) {
                                    $gni_deflator = $json_api['dataset']['data'][0][1];
                                } else {
                                    $gni_deflator = '';
                                }
                                $population_json["gni_deflator"] = $gni_deflator;
                            }


                            /*Gradutes (ID=58)*/
                            if (in_array('58', $api)) {
                                $json_api = $this->get_Sales_region_Api(58, $country_code);
                                if (isset($json_api['dataset'])) {
                                    $science_gradiates = $json_api['dataset']['data'][0][1];
                                } else {
                                    $science_gradiates = '';
                                }
                                $population_json["science_gradiates"] = $science_gradiates;
                            }


                            /*Capacity utilization (ID=59)*/
                            if (in_array('59', $api)) {
                                $json_api = $this->get_Sales_region_Api(59, $country_code);

                                if (isset($json_api['dataset'])) {
                                    $capacityutilization = $json_api['dataset']['data'][0][1];
                                } else {
                                    $capacityutilization = '';
                                }
                                $population_json["capacityutilization"] = $capacityutilization;
                            }

                            $latitude = '';
                            $longitude = '';
                            /* Location Information Google (ID=61)*/
                            if (in_array('61', $api)) {
                                $json_api = $this->get_Sales_region_Api(61, $country_code, $r_state, $r_cluster_1);

                                if (isset($json_api['results'][0]['geometry'])) {
                                    $latitude = $json_api['results'][0]['geometry']['location']['lat'];
                                    $longitude = $json_api['results'][0]['geometry']['location']['lng'];
                                } else {
                                    $latitude = '';
                                    $longitude = '';
                                }
                            }

                            /* Location Information OpentouteService (ID=68)*/
                            if (in_array('68', $api)) {
                                $json_api = $this->get_Sales_region_Api(68, $country_code, $r_state, $r_cluster_1);

                                if (isset($json_api['results'][0]['geometry'])) {
                                    $latitude = $json_api['results'][0]['geometry']['location']['lat'];
                                    $longitude = $json_api['results'][0]['geometry']['location']['lng'];
                                } else {
                                    $latitude = '';
                                    $longitude = '';
                                }
                            }

                            if ($population_json){
                                $temp_item['r_addtionals'] = json_encode($population_json);
                            }
                            $temp_item['r_longitude'] = $longitude;
                            $temp_item['r_latitude'] = $latitude;

							$model_1 = new Mysql($this->host, $this->user, $this->password, $this->db_1);
							$sql1 = $model_1->insert($real_name, $item);

							$model_2 = new Mysql($this->host, $this->user, $this->password, $this->db_2);
							$model_2->insert($table_name, $temp_item);
                        }
                        break;
                    case 'm_materials':
                        foreach ($array as $item) {
                            $model_1 = new Mysql($this->host, $this->user, $this->password, $this->db_1);
                            $model_1->insert($real_name, $item);

                            $temp_item = array();
                            foreach ($map_object as $key => $value) {
                                $temp_item[$value] = trim($item[$key]);
                            }
                            $temp_item['lastupdated'] = date('Y-m-d h:i:s');
                            $model_2 = new Mysql($this->host, $this->user, $this->password, $this->db_2);
                            $model_2->insert($table_name, $temp_item);
                        }
                        break;
                }
                echo json_encode('success');
            } else {
                echo json_encode("failure");
            }
        }
    }
}
