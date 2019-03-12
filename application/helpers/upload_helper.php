<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('parse_csv')) {
	function parse_csv($file_path)
	{
		$array_data = $array_fields = array();
		$i = 0;
		$handle = @fopen($file_path, "r");
		if ($handle) {
			while (($row = fgetcsv($handle, 4096)) !== false) {
				if (empty($array_fields)) {
					$array_fields = $row;
					continue;
				}
				foreach ($row as $k => $value) {
					$array_data[$i][$array_fields[$k]] = trim($value);
				}
				$i++;
			}
			if (!feof($handle)) {
				echo "Error: unexpected fgets() fail\n";
			}
			fclose($handle);
		}

		$res['array_data'] = $array_data;
		$res['array_fields'] = $array_fields;
		return $res;
	}
}

if (!function_exists('get_googleApi_data')) {
	function get_googleApi_data($id, $search)
	{
		$CI = get_instance();
		$CI->config->load('custom_db');
		$host = $CI->config->item('host');
		$user = $CI->config->item('user');
		$password = $CI->config->item('password');
		$db1 = $CI->config->item('database_1');

		$model_2 = new Mysql($host, $user, $password, $db1);
		$api = $model_2->where('id', $id)
			->get('m_api_list');

		if (count($api) > 0) {
			$url = $api[0]['api_url'];
			if (strpos($url, '{$search}') !== false) {
				$url = str_replace('{$search}', $search, $api[0]['api_url']);
			} else {
				$url = str_replace('$search', $search, $api[0]['api_url']);
			}

			$url = str_replace('key=$key', ltrim(trim($api[0]['api_key']), '$'), $url);
			$json_api = $model_2->exe_curl($url);
			return $json_api;
		} else {
			return false;
		}
	}
}


if (!function_exists('get_quandlApi_data')) {
	function get_quandlApi_data($id, $type, $value)
	{
		$CI = get_instance();
		$CI->config->load('custom_db');
		$host = $CI->config->item('host');
		$user = $CI->config->item('user');
		$password = $CI->config->item('password');
		$db1 = $CI->config->item('database_1');


		$model_2 = new Mysql($host, $user, $password, $db1);
		$api = $model_2->where('id', $id)
			->get('m_api_list');

		if (count($api) > 0) {
			$url = $api[0]['api_url'];
			$url = $model_2->getUrl($url, $value, $api[0]['api_key'], $type);
			$json_api = $model_2->exe_curl($url);
			return $json_api;
		} else {
			return false;
		}
	}
}
