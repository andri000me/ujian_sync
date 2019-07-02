<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends CI_Controller {
	public function index(){
	    if(!$this->session->userdata('login')){
	        redirect(base_url('login'));
        }
        if ($this->session->userdata('ses_id')){
	        $ses_id = $this->session->userdata('ses_id');
	        $this->dbase->dataUpdate('offline','quiz_session',array('ses_id'=>$ses_id),array('ses_active'=>99));
        }
		$this->session->sess_destroy();
	    redirect(base_url(''));
	}
}
