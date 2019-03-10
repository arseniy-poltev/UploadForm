<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DashboardBase extends CI_Controller
{
    public function loadView($view, $data)
    {
        $data['view'] = $view;
        $this->load->view('file_upload/common/header');
        $this->load->view('dashboard/include/sidebar', $data);
        $this->loadMainView($view, $data);
        $this->load->view('dashboard/include/footer');
    }

    public function loadMainView($view, $data)
    {
        $this->load->view('dashboard/include/main_header');
        $this->load->view($view, $data);
        $this->load->view('dashboard/include/main_footer');
    }
}