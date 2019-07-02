<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quiz extends CI_Controller {
    function __construct()    {
        parent::__construct();
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }
    function index(){
        redirect(base_url('quiz/landing'));
    }
    function startup(){
        if(!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif ($this->session->userdata('ses_active') == 2){
            redirect(base_url('quiz/landing'));
        } elseif ($this->session->userdata('ses_active') == 99){
            redirect(base_url('quiz/finish_result'));
        } else {
            $dtSes = $this->dbase->dataRow('offline', 'quiz_session', array('ses_id' => $this->session->userdata('ses_id')), 'ses_active,ses_time_left');
            if (!$dtSes) {
                $this->session->sess_destroy();
                redirect(base_url('login'));
            } elseif ($dtSes->ses_active == 0) {
                $this->session->sess_destroy();
                redirect(base_url('login'));
            } elseif ($dtSes->ses_active == 2) {
                $this->session->set_userdata(array('ses_active' => 2));
                redirect(base_url('quiz/landing'));
            } elseif ($dtSes->ses_active == 99) {
                $this->session->set_userdata(array('ses_active' => 99));
                redirect(base_url('quiz/finish_result'));
            } elseif ($dtSes->ses_time_left <= 0) {
                $this->session->sess_destroy();
                redirect(base_url('login'));
            } else {
                $dtQuiz = $this->dbase->dataRow('offline', 'quiz', array('quiz_id' => $this->session->userdata('quiz_id'), 'quiz_active' => 1), 'quiz_id,quiz_name,quiz_timer');
                if (!$dtQuiz) {
                    $this->session->sess_destroy();
                    redirect(base_url('login'));
                } else {
                    $dtMapel = $this->dbase->dataResult('offline', 'quiz_mapel', array('quiz_id' => $this->session->userdata('quiz_id')), 'mapel_name');
                    $data['quiz'] = $dtQuiz;
                    $data['mapel'] = $dtMapel;
                    $data['body'] = 'quiz/startup_body';
                }
            }
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('quiz/startup',$data);
        }
    }
	public function landing(){
        if(!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif ($this->session->userdata('ses_active') == 1) {
            redirect(base_url('quiz/startup'));
        } elseif ($this->session->userdata('ses_active') == 99){
            redirect(base_url('quiz/finish_result'));
        } elseif ($this->session->userdata('ses_time_left') <= 0){
            $this->dbase->dataUpdate('offline','quiz_session',array('ses_id'=>$this->session->userdata('ses_id')),array('ses_active'=>99));
            $this->session->sess_destroy();
            redirect(base_url('login'));
        } else {
            $ses_id     = $this->session->userdata('ses_id');
            $dtSes      = $this->dbase->dataRow('offline','quiz_session',array('ses_id'=>$ses_id),'ses_active,ses_time_left');
            if (!$dtSes){
                $this->session->ses_destroy();
                redirect(base_url('login'));
            } elseif ($dtSes->ses_active == 0) {
                $this->session->sess_destroy();
                redirect(base_url('login'));
            } elseif ($dtSes->ses_active == 1){
                $this->session->set_userdata(array('ses_active'=>1));
                redirect(base_url('quiz/startup'));
            } elseif ($dtSes->ses_active == 99){
                $this->session->set_userdata(array('ses_active'=>99));
                redirect(base_url('quiz/finish_result'));
            } elseif ($dtSes->ses_time_left <= 0){
                $this->session->sess_destroy();
                redirect(base_url('login'));
            } else {
                $quiz_id    = $this->session->userdata('quiz_id');
                $user_id    = $this->session->userdata('user_id');
                $dtQuiz     = $this->dbase->dataRow('offline','quiz',array('quiz_id'=>$quiz_id),'quiz_id');
                if (!$dtQuiz){
                    $this->session->ses_destroy();
                    redirect(base_url('login'));
                } else {
                    $dtMapel    = $this->dbase->dataResult('offline','quiz_mapel',array('quiz_id'=>$quiz_id),'mapel_id');
                    if (!$dtMapel){
                        $this->session->ses_destroy();
                        redirect(base_url('login'));
                    } else {
                        $dtSoal = $this->dbase->sqlResult('offline',"
                            SELECT    qs.qs_id,qs.soal_id
                            FROM      tb_quiz_soal AS qs
                            WHERE     qs.user_id = '".$user_id."' AND qs.quiz_id = '".$quiz_id."'
                            ORDER BY  qs.qs_nomor ASC
                        ");
                        if (!$dtSoal){
                            $data['body'] = 'errors/500';
                        } else {
                            $i = 0;
                            foreach ($dtSoal as $valSoal){
                                $dtSoal[$i]     = $valSoal;
                                $dtJawab        = $this->dbase->sqlRow('offline',"
                                    SELECT    qspg.qspg_nomor
                                    FROM      tb_quiz_session_log AS ses
                                    LEFT JOIN tb_quiz_soal_pg AS qspg ON ses.qspg_id = qspg.qspg_id
                                    WHERE     qspg.qs_id = '".$valSoal->qs_id."' AND ses.ses_id = '".$ses_id."'
                                    ORDER BY  ses.qsl_created DESC
                                    LIMIT     0,1
                                ");
                                if ($dtJawab){
                                    $dtSoal[$i]->jawab  = $dtJawab->qspg_nomor;
                                } else {
                                    $dtSoal[$i]->jawab  = 0;
                                }
                                $i++;
                            }
                            //$dtQSL = $this->dbase->dataRow('offline','quiz_session_log',array('ses_id'=>$ses_id),'soal_id','qsl_id','DESC');
                            /*$dtQSL  = $this->dbase->sqlRow('offline',"
                                SELECT    qsl.soal_id
                                FROM      tb_quiz_session_log AS qsl
                                WHERE     qsl.ses_id = '".$ses_id."'
                                ORDER BY  qsl.qsl_created DESC
                                LIMIT     0,1
                            ");
                            if ($dtQSL){
                                $data['seslog'] = $dtQSL;
                            }*/
                            $this->load->library('conv');
                            $data['soal']       = $dtSoal;
                            $data['body']       = 'quiz/landing';
                            $data['menu']       = 'tulis';
                            $json['html']       = $this->load->view('quiz/landing',$data,true);
                            $json['t']          = 1;
                        }
                    }
                }
            }
        }
        if ($this->input->is_ajax_request()){
            die(json_encode($json));
        } else {
            $this->load->view('quiz/startup',$data);
        }
	}
    function start_now(){
        $json['t'] = 0; $json['msg'] = 'UNKNOWN ERROR';
        $ses_id = $this->session->userdata('ses_id');
        $dtSes  = $this->dbase->dataRow('offline','quiz_session',array('ses_id'=>$ses_id),'ses_active,ses_id');
        if (!$dtSes){
            $json['msg'] = 'Invalid Session'; $json['t'] = 1;
            $this->session->sess_destroy();
        } elseif ($dtSes->ses_active != 1){
            $json['msg'] = 'Invalid Session'; $json['t'] = 1;
            $this->session->sess_destroy();
        } else {
            $token      = strtoupper($this->input->post('token'));
            $quiz_id    = $this->session->userdata('quiz_id');
            $dtQuiz     = $this->dbase->dataRow('offline','quiz',array('quiz_id'=>$quiz_id),'quiz_token,quiz_active');
            if (!$dtQuiz){
                $json['msg'] = 'BELUM ADA TES YANG VALID, SILAHKAN HUBUNGI PENGAWAS';
            } elseif ($dtQuiz->quiz_active == 0){
                $json['msg'] = 'TES BELUM DIBUKA, SILAHKAN HUBUNGI PENGAWAS';
            } elseif (strlen(trim($token)) == 0){
                $json['msg'] = 'TOKEN BELUM DIISI';
            } elseif ($token != $dtQuiz->quiz_token){
                $json['msg'] = 'TOKEN SALAH, SILAHKAN HUBUNGI PENGAWAS UNTUK MENDAPATKAN TOKEN';
            } else {
                $arr['ses_last_time']   = date('Y-m-d H:i:s');
                $arr['ses_created']     = date('Y-m-d H:i:s');
                $arr['ses_active']      = 2;
                $this->session->set_userdata($arr);
                $this->dbase->dataUpdate('offline','quiz_session',array('ses_id'=>$ses_id),array('ses_active'=>2));
                $json['t'] = 2;
            }
        }
        die(json_encode($json));
    }
    function update_time(){
        $ses_id         = $this->session->userdata('ses_id');
        $ses_time_left  = $this->session->userdata('ses_time_left');
        $last_time      = $this->session->userdata('ses_last_time');
        $timeFirst      = strtotime($last_time);
        $timeSecond     = strtotime(date('Y-m-d H:i:s'));
        $time_sub       = $timeSecond - $timeFirst;
        $ses_time_left  = $ses_time_left - $time_sub;
        $this->session->set_userdata(array('ses_last_time'=>date('Y-m-d H:i:s'),'ses_time_left'=>$ses_time_left));
        $this->dbase->dataUpdate('offline','quiz_session',array('ses_id'=>$ses_id),array('ses_time_left'=>$ses_time_left));
    }
    function load_soal(){
        $json['t'] = 0; $json['msg'] = 'START';
        if (!$this->session->userdata('login')){
            $json['t']  = 2;
        } else {
            $quiz_id    = $this->session->userdata('quiz_id');
            $ses_id     = $this->session->userdata('ses_id');
            $dtQuiz     = $this->dbase->dataRow('offline','quiz',array('quiz_id'=>$quiz_id),'quiz_active');
            if (!$dtQuiz || !$quiz_id){
                $this->session->sess_destroy();
                $json['t']  = 2;
            } elseif ($dtQuiz->quiz_active == 0) {
                $this->session->sess_destroy();
                $json['t'] = 2;
            } else {
                $dtSes  = $this->dbase->dataRow('offline','quiz_session',array('ses_id'=>$ses_id),'ses_active');
                if (!$dtSes){
                    $this->session->sess_destroy();
                    $json['t'] = 2;
                } elseif ($dtSes->ses_active != '2') {
                    $this->session->sess_destroy();
                    $json['t'] = 2;
                } elseif ($this->session->userdata('ses_time_left') <= 0){
                    $this->dbase->dataUpdate('offline','quiz_session',array('ses_id'=>$ses_id),array('ses_active'=>99));
                    $this->session->sess_destroy();
                    $json['t'] = 2;
                } else {
                    $qs_id      = $this->input->post('qs_id');
                    $soal_id    = $this->input->post('soal_id');
                    $dtSoal     = $this->dbase->sqlRow('offline',"
                        SELECT    qs.qs_id,s.soal_id,s.soal_content,s.soal_type,qs.qs_nomor
                        FROM      tb_quiz_soal AS qs
                        LEFT JOIN tb_soal AS s ON qs.soal_id = s.soal_id
                        WHERE     qs.qs_id = '".$qs_id."'
                    ");
                    if (!$dtSoal){
                        $json['msg'] = 'ERROR SOAL';
                    } else {
                        $data['soal']   = $dtSoal;
                        $data['pg']     = array();
                        if ($dtSoal->soal_type == 'pg'){
                            $dtPG   = $this->dbase->sqlResult('offline',"
                                SELECT    qspg.qspg_id,pg.pg_id,pg.pg_content,qspg.qspg_nomor
                                FROM      tb_quiz_soal_pg AS qspg
                                LEFT JOIN tb_soal_pg AS pg ON qspg.pg_id = pg.pg_id
                                WHERE     qspg.qs_id = '".$dtSoal->qs_id."'
                                ORDER BY  qspg.qspg_nomor ASC
                            ");
                            if ($dtPG){
                                $i = 0;
                                foreach ($dtPG as $valPG){
                                    $dtPG[$i]   = $valPG;
                                    $jawab = $this->dbase->dataRow('offline','quiz_session_log',array('ses_id'=>$ses_id,'qs_id'=>$qs_id,'qspg_id'=>$valPG->qspg_id),'qspg_id');
                                    if ($jawab){ $dtPG[$i]->jawab = 1; } else { $dtPG[$i]->jawab = 0; }
                                    $i++;
                                }
                                $data['pg'] = $dtPG;
                                $this->load->library('conv');
                            }
                        }
                        $this->update_time();
                        $this->load->library('conv');
                        $json['t']      = 3;
                        $json['nomor']  = $dtSoal->qs_nomor;
                        $json['timer']  = round($this->session->userdata('ses_time_left')/60);
                        $json['html']   = $this->load->view('quiz/load_soal',$data,true);
                    }
                }
            }
        }
        die(json_encode($json));
    }
    function set_jawab(){
        $json['t'] = 0; $json['msg'] = '';
        if (!$this->session->userdata('login')){
            $json['t']  = 2;
        } else {
            $quiz_id = $this->session->userdata('quiz_id');
            $ses_id = $this->session->userdata('ses_id');
            $dtQuiz = $this->dbase->dataRow('offline', 'quiz', array('quiz_id' => $quiz_id), 'quiz_active');
            if (!$dtQuiz || !$quiz_id) {
                $this->session->sess_destroy();
                $json['t'] = 2;
            } elseif ($dtQuiz->quiz_active == 0) {
                $this->session->sess_destroy();
                $json['t'] = 2;
            } else {
                $dtSes = $this->dbase->dataRow('offline', 'quiz_session', array('ses_id' => $ses_id), 'ses_active');
                if (!$dtSes) {
                    $this->session->sess_destroy();
                    $json['t'] = 2;
                } elseif ($dtSes->ses_active != '2') {
                    $this->session->sess_destroy();
                    $json['t'] = 2;
                } elseif ($this->session->userdata('ses_time_left') <= 0) {
                    $this->dbase->dataUpdate('offline','quiz_session',array('ses_id'=>$ses_id),array('ses_active'=>99));
                    $this->session->sess_destroy();
                    $json['t'] = 2;
                } else {
                    $pg_id      = $this->input->post('pg_id');
                    $qspg_id    = $this->input->post('qspg_id');
                    $qs_id      = $this->input->post('qs_id');
                    $soal_id    = $this->input->post('soal_id');
                    $dtQsPG     = $this->dbase->dataRow('offline','quiz_soal_pg',array('qspg_id'=>$qspg_id),'qspg_nomor');
                    $dtQsLog    = $this->dbase->dataRow('offline','quiz_session_log',array(
                        'ses_id' => $ses_id, 'qs_id' => $qs_id, 'qsl_type' => 'pg'
                    ));
                    if (!$dtQsLog){
                        $qsl_id = $this->dbase->dataInsert('offline','quiz_session_log',array(
                            'ses_id' => $ses_id, 'qs_id' => $qs_id, 'qspg_id' => $qspg_id, 'qsl_type' => 'pg', 'soal_id' => $soal_id,
                            'pg_id' => $pg_id
                        ));
                    } else {
                        $qsl_id = $dtQsLog->qsl_id;
                        $this->dbase->dataUpdate('offline','quiz_session_log',array('qsl_id'=>$qsl_id),array(
                            'qspg_id'=>$qspg_id, 'pg_id' => $pg_id, 'qsl_created' => date('Y-m-d H:i:s')
                        ));
                    }
                    if ($qsl_id){
                        $this->update_time();
                        $json['t']          = 10;
                        $this->load->library('conv');
                        $json['jawaban']    = $this->conv->toStr($dtQsPG->qspg_nomor);
                    }
                }
            }
        }
        die(json_encode($json));
    }
    function finish_tes(){
        $json['t'] = 0; $json['msg'] = 'START';
        $ses_id     = $this->session->userdata('ses_id');
        $dtSes      = $this->dbase->dataRow('offline','quiz_session',array('ses_id'=>$ses_id));
        //die(var_dump($dtSes));
        if (!$dtSes){
            $this->session->ses_destroy();
            $json['t'] = 1;
        } else {
            $this->session->set_userdata(array('ses_active'=>3));
            $this->dbase->dataUpdate('offline','quiz_session',array('ses_id'=>$ses_id),array('ses_active'=>3));
            $json['t'] = 2;
        }
        die(json_encode($json));
    }
    function finish_result(){
        if(!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif ($this->session->userdata('user_level') != 1) {
            redirect(base_url('login'));
        } elseif ($this->session->userdata('ses_active') == 1) {
            redirect(base_url('quiz/startup'));
        } elseif ($this->session->userdata('ses_active') == 2) {
            redirect(base_url('quiz/landing'));
        } else {
            $arr = array('quiz_id'=>'','ses_time_left'=>'','ses_last_time'=>'','ses_created'=>'');
            $this->session->set_userdata($arr);
            $this->load->view('quiz/finish_result');
        }
    }
}
