<?php

require 'utils/config.php';
require 'utils/mysql.php';


$target_file_root = '\uploads';
$uploads_root = __DIR__ . $target_file_root . '/';


if (!is_null($_POST["file_name"])) {
    $upload_filename = $uploads_root . $_POST['file_name'];
    $extention = pathinfo($_FILES["upload_file"]["name"], PATHINFO_EXTENSION);


    /*
     Upload files into server
    */
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
    $model_2 = new Mysql(HOST, USER, PASSWORD, DB_2);
    $map_data = $model_2->where('table_name', $table_name)->get('map_table', 'f_field,d_field');


    if ($map_data != false && count($map_data) > 0) {
        foreach ($map_data as $item) {
            $map_object[$item['f_field']] = $item['d_field'];
        }


        /*
          Insert Data into DB_1 and DB_2
         */
        $real_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $_POST['file_name']);
        $model_1 = new Mysql(HOST, USER, PASSWORD, DB_1);


        /*
          Create table with name as Filename
        */
        $res = $model_1->create_table($fields, $real_name);


        /*
           Empty db_2 table
         */
        if ($_POST['table_replace_selector'] == 'replace') {
            $model_2 = new Mysql(HOST, USER, PASSWORD, DB_2);
            $model_2->Empty($table_name);
        }


        /*
         Insert Data into DB_1 and DB_2
        */
        foreach ($array as $item) {
            $model_1 = new Mysql(HOST, USER, PASSWORD, DB_1);

            $sql1 = $model_1->insert($real_name, $item);

            $temp_item = array();
            foreach ($map_object as $key => $value) {
                $temp_item[$value] = trim($item[$key]);
            }
            $model_2 = new Mysql(HOST, USER, PASSWORD, DB_2);
            $sql2 = $model_2->insert($table_name, $temp_item);
        }
        echo json_encode('success');
    } else {
        echo json_encode("failure");
    }
}
?>