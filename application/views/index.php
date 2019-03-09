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
	<script src="<?= base_url() ?>/assets/js/papaparse.min.js"></script>

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


    </style>
</head>
<body>

<div class="jumbotron text-center">
    <h1>Upload Form</h1>
</div>

<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="alert alert-success" role="alert" id="success_alarm" style="display: none">
                File upload finished successfully!
            </div>
            <h5 class="card-title">Start Uploading</h5>
            <form action="/action_page.php" id="upload_form">

                <div class="form-group">
                    <label for="upload_file" class="mr-sm-4">Select File:</label>
                    <input type="file" class="form-control mb-2 mr-sm-8" id="upload_file" name="upload_file">
                </div>
                <div class="form-group">
                    <label for="table_list">Select table</label>
                    <select class="form-control mb-2 mr-sm-5" name="table_selector">
                        <?php foreach ($table_list as $re)
                            echo '<option value="' . $re['table_name'] . '">' . $re['table_name'] . '</option>';
                        ?>
                    </select>
                </div>
                <!--        <div class="form-group">-->
                <!--            <label for="geocording_service" class="mr-sm-4">Select Geocording Serveice:</label>-->
                <!--            <input type="text" class="form-control mb-2 mr-sm-8" id="geocording_service">-->
                <!--        </div>-->
                <div class="form-group">
                    <label for="email" class="mr-sm-4">Would you like to append or replace the data in the table?</label>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="table_replace_selector_1"
                               name="table_replace_selector" value="append" checked>
                        <label class="custom-control-label" for="table_replace_selector_1">Append</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="table_replace_selector_2"
                               name="table_replace_selector" value="replace">
                        <label class="custom-control-label" for="table_replace_selector_2">Replace</label>
                    </div>
                </div>

                <button class="btn btn-primary btn_load" type="button" disabled style="display: none">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    &nbsp;&nbsp;Loading...&nbsp;&nbsp;
                </button>

                <button class="btn btn-primary btn_import" type="button">
                    &nbsp;&nbsp;&nbsp;&nbsp;Import&nbsp;&nbsp;&nbsp;&nbsp;
                </button>
            </form>
        </div>
    </div>
    <div class="row p-lg-2">
        <div class="container">
            <a href="<?php echo base_url().'UploadManager/mapIndex' ?>">Manage MappingTable</a>
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
<div class="modal fade" id="dialog_modal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Modal Heading</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                Modal body..
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>


</body>
</html>

<script>


    function getFileExtension(filename) {
        return filename.split('.').pop();
    }

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

    $('.btn_import').on('click', function () {
		file_selector = $('#upload_file');

		file_selector.parse({
			complete: function(results) {
				console.log(results);
			}
		});



		return;
        hideSuccessAlart();
        formTag = $('#upload_form');
        var form = formTag[0];
        var data = new FormData(form);
        var file_path = $('#upload_file').val();

        var startIndex = (file_path.indexOf('\\') >= 0 ? file_path.lastIndexOf('\\') : file_path.lastIndexOf('/'));
        var filename = file_path.substring(startIndex);
        if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
            filename = filename.substring(1);
        }


        var extension = getFileExtension(filename);

        if (extension === 'csv' || extension === 'xls' || extension === 'xlsx' || extension === 'json') {
            // loader();
            data.append('file_name', filename);
            // $('.btn_load').css('display', 'block');
            // $('.btn_import').css('display', 'none');
            $.ajax({
                url: "<?php echo base_url(); ?>UploadManager/uploadFile",
                type: "post",
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                cache: false,
                data: data,
                dataType: "json",
                success: function (res) {
                    // $('.btn_load').css('display', 'none');
                    // $('.btn_import').css('display', 'block');
                    if (res == 'success')
                    {
                        dispSuccessAlarm("File uploaded successfully");
                        // exit_loader();

                    } else {
                        // disp_alert("Alert!", "Please select the correct file or table.");
                        // exit_loader();
                    }
                }
            })
        } else {
            disp_alert("Alert!", "Please select the correct file.");
        }

    })
</script>