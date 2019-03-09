<?php
//
//include './utils/config.php';
//include './utils/mysql.php';
//
//$model = new Mysql('127.0.0.1', 'root', '', '2_transform_data');
//$res = $model->where('table_schema', '2_transform_data')->get('information_schema.tables', 'table_name');
//
//?>


<!DOCTYPE html>
<html lang="en">
<head>
	<title>ClientProject</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="<?= base_url() ?>/assets/bootstrap/css/bootstrap.min.css">
	<script src="<?= base_url() ?>/assets/jquery/dist/jquery.min.js"></script>


	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script>
	<!--    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>-->

	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
	<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

	<style>
		.bd-example-modal-lg .modal-dialog{
			display: table;
			position: relative;
			margin: 0 auto;
			top: calc(50% - 24px);
		}

		.bd-example-modal-lg .modal-dialog .modal-content{
			background-color: transparent;
			border: none;
		}

		.dropdown-submenu {
			position: relative;
		}

		.dropdown-submenu> a:after {
			content: ">";
			float: right;
		}

		.dropdown-submenu>.dropdown-menu {
			top: 0;
			left: 100%;
			margin-top: 0px;
			margin-left: 0px;
		}

		.dropdown-submenu:hover>.dropdown-menu {
			display: block;
		}


		.dropdown:hover>.dropdown-menu {
			display: block;
		}

	</style>
</head>
<body>

<div class="jumbotron text-center">
	<h1>Mapping Manage</h1>
</div>

<div class="container">
	<div class="card">
		<div class="card-body">
			<div class="alert alert-success" role="alert" id="success_alarm" style="display: none">
				File upload finished successfully!
			</div>
<!--			<h5 class="card-title">Start Uploading</h5>-->

			<button class="btn btn-primary pull-right btn-sm" id="add_record">Add Record</button>
			<table id="example" class="table table-striped table-bordered">
				<thead>
				<tr>
					<th>No</th>
					<th>File Name</th>
					<th>Table Name</th>
					<th>File Header</th>
					<th>Table Field</th>
					<th>Last Modified</th>
					<th>Action</th>
				</tr>
				</thead>
			</table>
		</div>
	</div>

</div>


<!-- loader-->
<div class="modal fade bd-example-modal-lg" data-backdrop="static" data-keyboard="false" tabindex="-1" id="loader_modal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content" style="width: 48px">
			<span class="fa fa-spinner fa-spin fa-3x"></span>
		</div>
	</div>
</div>


<!-- Dialog modal-->
<div class="modal fade" id="dialog_modal" data-backdrop="static">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title">Alert</h4>
<!--				<button type="button" class="close" data-dismiss="modal">&times;</button>-->
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				Do you want to delete this record?
			</div>

			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" id="cancel_delete">Close</button>
				<button type="button" class="btn btn-primary" id="delete_record">Delete</button>
			</div>

		</div>
	</div>
</div>

<div class="modal fade" id="form-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="form_modal_title">Add Record</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="map_form">
					<div class="form-group form-inline">
						<label for="recipient-name" class="col-form-label col-sm-3">File Name:</label>
						<input type="text" class="form-control col-sm-9" name="file_name">
					</div>
					<div class="form-group form-inline">
						<label for="recipient-name" class="col-form-label col-sm-3">Table Name:</label>
						<input type="text" class="form-control col-sm-9" name="table_name">
					</div>
					<div class="form-group form-inline">
						<label for="recipient-name" class="col-form-label col-sm-3">File Header:</label>
						<input type="text" class="form-control col-sm-9" name="f_field">
					</div>
					<div class="form-group form-inline">
						<label for="recipient-name" class="col-form-label col-sm-3">Table Field:</label>
						<input type="text" class="form-control col-sm-9" name="d_field">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" id="form_cancel">Close</button>
				<button type="button" class="btn btn-primary" id="form_submit">Save</button>
			</div>
		</div>
	</div>
</div>



</body>
</html>
<script>

	var record_id = '';

	var table;
	function update(type, id) {
		if (id === '') return;
		if (type === 'edit') {
			$.ajax({
				"url": "<?php echo base_url('UploadManager/geMap')?>",
				"type": "POST",
				"dataType": 'json',
				"data": {
					id: id
				},
				success: function (res) {
					if (res.state == '1') {

						data = res.data
						$('input[name="file_name"]').val(data.file_name);
						$('input[name="table_name"]').val(data.table_name);
						$('input[name="f_field"]').val(data.f_field);
						$('input[name="d_field"]').val(data.d_field);

						$('#form-modal').modal('show');
						record_id = id;
					}
				}
			})
		}

		if (type === 'delete') {
			record_id = id;
			$('#dialog_modal').modal('show');

		}
	}

	$('#cancel_delete').on('click', function () {
		$('#dialog_modal').modal('hide');
		record_id = '';
	});
	$('#delete_record').on('click', function () {

		$.ajax({
			"url": "<?php echo base_url('UploadManager/deleteMap')?>",
			"type": "POST",
			"dataType": 'json',
			"data": {
				id: record_id
			},
			success:function (res) {
				if (res.state == 1) {
					$('#dialog_modal').modal('hide');
					table.ajax.reload(null, false);
				}
			}
		})
	});




	$('#add_record').on('click', function () {
		$('#map_form')[0].reset();
		record_id = '';
		$('#form-modal').modal('show');
	});

	$('#form_submit').on('click', function () {
		if ($('#map_form').find('input').val() == '') {
			return;
		}

		var form_data = $('#map_form').serializeArray();

		form_data.push({name: "id", value: record_id});

		$.ajax({
			"url": "<?php echo base_url('UploadManager/saveMap')?>",
			"type": "POST",
			"dataType": 'json',
			"data": form_data,
			success:function (res) {
				if (res.state == 1) {
					$('#map_form')[0].reset();
					record_id = '';
					$('#form-modal').modal('hide');
					table.ajax.reload(null, false);
				}
			}
		})
	});

	$('#form_cancel').on('click', function () {
		record_id = '';
		$('#map_form')[0].reset();
		$('#form-modal').modal('hide');
	});

	function loader(){
		$('#loader_modal').modal('show');
	}

	function exit_loader() {
		$('#loader_modal').modal('hide');
	}


	function disp_alert(header, content) {
		$('#dialog_modal').find('.modal-title').html(header);
		$('#dialog_modal').find('.modal-body').html(content);
		$('#dialog_modal').modal('show');
	}

	function dispSuccessAlarm(content) {
		$('#success_alarm').css('display', 'block');
	}

	function hideSuccessAlart() {
		$('#success_alarm').css('display', 'none');
	}

	function hide_alert() {
		$('#dialog_modal').modal('hide');
	}

	$(document).ready(function() {
		table = $('#example').DataTable({
			"processing": true, //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"order": [[0, "ASC"]],
			// Load data for the table's content from an Ajax source
			"ajax": {
				"url": "<?php echo base_url('UploadManager/mappingList')?>",
				"type": "POST"
			},
			"columnDefs": [
				{
					"targets": [6],
					"orderable": false
				},
				{
					"targets": "_all",
					"searchable": true
				}
			],
			"columns": [
				{"data": "id" },
				{"data": "file_name" },
				{"data": "table_name" },
				{"data": "f_field" },
				{"data": "d_field" },
				{"data": "last_modified" },
				{
					"data": "action",
					"render": function (data, type, row, meta) {
						var data = "";

						data += '<div class="btn-group"> ' +
							'<button type="button" class="btn btn-info dropdown-toggle btn-sm" data-toggle="dropdown"> Action </button>' +
							'<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">' +
							'<li class="divider"></li>';

						data += '' +
							'<a href="javascript:update(\'edit\', \'' + row.id + '\')" class="dropdown-item"><i class="fa fa-pencil-square-o"></i> Edit</a>' +
							'<a href="javascript:update(\'delete\', \'' + row.id + '\')" class="dropdown-item"><i class="fa fa-times-circle"></i> Delete</a>';

						data += '<li class="divider"></li>';
						data += '</div>' +
							'</div>' +
							'';
						return data;

					}
				},
			]
		});

	} );
</script>
