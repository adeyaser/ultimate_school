<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		$data['title'] = 'Dashboard';
		$data['content_view'] = 'dashboard';

		$this->load->view('templates/layout', $data);
	}
}
