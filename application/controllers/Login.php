<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
    function __construct()    {
        parent::__construct();
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    public function index(){
	    if($this->session->userdata('login')){
	        redirect(base_url(''));
        }
		$this->load->view('login');
	}
	function submit_qr(){
        $json['t']  = 0; $json['msg'] = '';
        $username   = $this->input->post('user_name');
        $dtUser     = $this->dbase->dataRow('offline','user',array('user_name'=>$username,'user_status'=>1));
        if (strlen(trim($username)) == 0){
            $json['msg'] = 'Isikan username';
            $json['class'] = 'username';
        } elseif (!$dtUser){
            $json['msg'] = 'Username tidak ditemukan ';
            $json['class'] = 'username';
        } else {
            $arr = array(
                'login'=>true, 'user_id'=>$dtUser->user_id, 'user_fullname'=>$dtUser->user_fullname,
                'user_level'=>$dtUser->user_level
            );
            if ($dtUser->user_level > 1) {
                $json['t']      = 1;
                $json['lvl']    = $dtUser->user_level;
                $this->session->set_userdata($arr);
            } else {
                $arr['lvl']  = 1;
                $dtQuiz = $this->dbase->dataRow('offline','quiz',array('quiz_active'=>1));
                if (!$dtQuiz){
                    $json['msg'] = 'Tidak ada TES yang AKTIF. Hubungi PENGAWAS atau PROKTOR.';
                } else {
                    $dtSes  = $this->dbase->dataRow('offline','quiz_session',array('quiz_id'=>$dtQuiz->quiz_id,'user_id'=>$dtUser->user_id));
                    if (!$dtSes){
                        $ses_id = $this->dbase->dataInsert('offline','quiz_session',array(
                            'quiz_id' => $dtQuiz->quiz_id, 'user_id' => $dtUser->user_id, 'ses_created' => date('Y-m-d H:i:s'),
                            'ses_active' => 1, 'ses_time_left' => $dtQuiz->quiz_timer * 60
                        ));
                        $arr['quiz_id']         = $dtQuiz->quiz_id;
                        $arr['ses_id']          = $ses_id;
                        $arr['ses_time_left']   = $dtQuiz->quiz_timer * 60;
                        $arr['ses_last_time']   = date('Y-m-d H:i:s');
                        $arr['ses_created']     = date('Y-m-d H:i:s');
                        $arr['ses_active']      = 1;
                        $json['t'] = 1;
                    } elseif ($dtSes->ses_active == 0) {
                        $arr['ses_active']      = 1;
                        $arr['quiz_id']         = $dtQuiz->quiz_id;
                        $arr['ses_id']          = $dtSes->ses_id;
                        $arr['ses_time_left']   = $dtQuiz->quiz_timer * 60;
                        $arr['ses_last_time']   = date('Y-m-d H:i:s');
                        $arr['ses_created']     = date('Y-m-d H:i:s');
                        $this->dbase->dataUpdate('offline','quiz_session',array('ses_id'=>$dtSes->ses_id),array('ses_created'=>date('Y-m-d H:i:s'),'ses_active' => 1));
                        $json['t'] = 1;
                    } elseif ($dtSes->ses_active >= 1 && $dtSes->ses_active < 99){
                        $json['msg']            = 'Status anda sedang LOGIN, silahkan hubungi PENGAWAS atau PROKTOR '.$username;
                    } elseif ($dtSes->ses_active == 99){
                        $json['msg']            = 'Anda sudah menyelesaikan TES ini. Terima Kasih.';
                    }
                }
                $this->session->set_userdata($arr);
            }
        }
        die(json_encode($json));
    }
	function submit(){
	    $json['t']  = 0; $json['msg'] = '';
	    $username   = $this->input->post('username');
	    $password   = $this->input->post('password');
	    $dtUser     = $this->dbase->dataRow('offline','user',array('user_name'=>$username,'user_status'=>1));
	    if (strlen(trim($username)) == 0){
	        $json['msg'] = 'Isikan username';
	        $json['class'] = 'username';
        } elseif (!$dtUser){
	        $json['msg'] = 'Username tidak ditemukan';
            $json['class'] = 'username';
        } elseif (strlen(trim($password)) == 0){
	        $json['msg'] = 'Isikan password';
            $json['class'] = 'password';
        } elseif ($password != $dtUser->user_password){
	        $json['msg'] = 'Password salah';
            $json['class'] = 'password';
        } else {
            $arr = array(
                'login'=>true, 'user_id'=>$dtUser->user_id, 'user_fullname'=>$dtUser->user_fullname,
                'user_level'=>$dtUser->user_level
            );
            if ($dtUser->user_level > 1) {
                $json['t']      = 1;
                $json['lvl']    = $dtUser->user_level;
                $this->session->set_userdata($arr);
            } else {
                $arr['lvl']  = 1;
                $dtQuiz = $this->dbase->dataRow('offline','quiz',array('quiz_active'=>1));
                if (!$dtQuiz){
                    $json['msg'] = 'Tidak ada TES yang AKTIF. Hubungi PENGAWAS atau PROKTOR.';
                } else {
                    $dtSes  = $this->dbase->dataRow('offline','quiz_session',array('quiz_id'=>$dtQuiz->quiz_id,'user_id'=>$dtUser->user_id));
                    if (!$dtSes){
                        $ses_id = $this->dbase->dataInsert('offline','quiz_session',array(
                            'quiz_id' => $dtQuiz->quiz_id, 'user_id' => $dtUser->user_id, 'ses_created' => date('Y-m-d H:i:s'),
                            'ses_active' => 1, 'ses_time_left' => $dtQuiz->quiz_timer * 60
                        ));
                        $arr['quiz_id']         = $dtQuiz->quiz_id;
                        $arr['ses_id']          = $ses_id;
                        $arr['ses_time_left']   = $dtQuiz->quiz_timer * 60;
                        $arr['ses_last_time']   = date('Y-m-d H:i:s');
                        $arr['ses_created']     = date('Y-m-d H:i:s');
                        $arr['ses_active']      = 1;
                        $json['t'] = 1;
                    } elseif ($dtSes->ses_active == 0) {
                        $arr['ses_active']      = 1;
                        $arr['quiz_id']         = $dtQuiz->quiz_id;
                        $arr['ses_id']          = $dtSes->ses_id;
                        $arr['ses_time_left']   = $dtQuiz->quiz_timer * 60;
                        $arr['ses_last_time']   = date('Y-m-d H:i:s');
                        $arr['ses_created']     = date('Y-m-d H:i:s');
                        $this->dbase->dataUpdate('offline','quiz_session',array('ses_id'=>$dtSes->ses_id),array('ses_created'=>date('Y-m-d H:i:s'),'ses_active' => 1));
                        $json['t'] = 1;
                    } elseif ($dtSes->ses_active >= 1 && $dtSes->ses_active < 99){
                        $json['msg']            = 'Status anda sedang LOGIN, silahkan hubungi PENGAWAS atau PROKTOR';
                    } elseif ($dtSes->ses_active == 99){
                        $json['msg']            = 'Anda sudah menyelesaikan TES ini. Terima Kasih.';
                    }
                }
                $this->session->set_userdata($arr);
            }
        }
	    die(json_encode($json));
    }
}
