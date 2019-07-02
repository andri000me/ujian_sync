<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tes extends CI_Controller {
	public function index(){
	    if(!$this->session->userdata('login')){
	        redirect(base_url('login'));
        } elseif ($this->session->userdata('user_level') == 1){
	        redirect(base_url('quiz/landing'));
        }
        $fp = @fSockOpen('smkmuhkandanghaur.sch.id',80,$errno,$errstr,1);
        if ($fp){ $data['online'] = 1; } else { $data['online'] = 0; }
        $data['server'] = $this->dbase->dataRow('offline','server',array('sv_status'=>1));
        $data['quiz']   = $this->dbase->dataResult('offline','quiz',array('quiz_status'=>1),'quiz_id,quiz_name,quiz_date');
        $data['body']   = 'tes/home';
	    $data['menu']   = 'tes';
        $data['ruang']  = $this->dbase->dataResult('offline','ruang',array('sv_id'=>$data['server']->sv_id));
	    if ($this->session->userdata('quiz_id')){
	        $this->load->helper('string');
	        $quiz_id    = $this->session->userdata('quiz_id');
	        $dtQuiz     = $this->dbase->dataRow('offline','quiz',array('quiz_id'=>$quiz_id),'quiz_token,quiz_token_date');
	        if ($dtQuiz) {
                $token = $dtQuiz->quiz_token;
                $data['token'] = $token;
            }
        } else {
	        $dtQAct = $this->dbase->dataRow('offline','quiz',array('quiz_active'=>1),'quiz_id,quiz_token,quiz_token_date');
	        if ($dtQAct){
                $arr = array('quiz_id'=>$dtQAct->quiz_id,'quiz_token'=>$dtQAct->quiz_token,'quiz_token_date'=>$dtQAct->quiz_token_date);
                $this->session->set_userdata($arr);
            }
        }
	    if ($this->input->is_ajax_request()){
	        $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
	}
	function quiz_selected(){
	    $json['t'] = 0; $json['msg'] = '';
	    $quiz_id    = $this->input->post('quiz_id');
	    $dtQuiz     = $this->dbase->dataRow('offline','quiz',array('quiz_id'=>$quiz_id),'quiz_id,quiz_token,quiz_active');
	    if (!$dtQuiz){
	        $json['msg'] = 'Invalid parameter TES';
        } else {
	        $dtMapel    = $this->dbase->dataResult('offline','quiz_mapel',array('quiz_id'=>$quiz_id,'qm_status'=>1),'mapel_name');
	        if (!$dtMapel){
	            $json['msg'] = 'Tidak ada MAPEL';
            } else {
	            $active         = '<strong class="text-danger">TES DITUTUP</strong>';
	            if ($dtQuiz->quiz_active == 1){ $active = '<strong class="text-success">TES DIBUKA</strong>'; }
	            $json['t']      = 1;
	            $json['token']  = $dtQuiz->quiz_token;
	            $json['active'] = $active;
	            $json['status'] = $dtQuiz->quiz_active;
	            $json['data']   = $dtMapel;
            }
        }
	    die(json_encode($json));
    }
    function quiz_set_active(){
	    date_default_timezone_set('Asia/Jakarta');
	    $json['t'] = 0; $json['msg'] = '';
	    $quiz_id    = $this->input->post('quiz_id');
	    $dtQuiz     = $this->dbase->dataRow('offline','quiz',array('quiz_id'=>$quiz_id),'quiz_token,quiz_date');
	    if (!$dtQuiz){
	        $json['msg'] = 'TES TIDAK VALID';
        } else {
	        $quiz_date  = date('Y-m-d',strtotime($dtQuiz->quiz_date));
	        $now_date   = date('Y-m-d');
	        if ($quiz_date != $now_date){
	            $json['msg'] = 'Pelaksanaan tes BUKAN HARI INI'.$now_date;
            } else {
                if (strlen($dtQuiz->quiz_token) == 0){
                    $this->load->helper('string');
                    $token      = strtoupper(random_string('alpha',6));
                    $this->dbase->dataUpdate('offline','quiz',array('quiz_id !='=>$quiz_id),array('quiz_active'=>0));
                    $this->dbase->dataUpdate('offline','quiz',array('quiz_id'=>$quiz_id),array('quiz_token'=>$token,'quiz_active'=>1));
                } else {
                    $token      = $dtQuiz->quiz_token;
                    $this->dbase->dataUpdate('offline','quiz',array('quiz_id !='=>$quiz_id),array('quiz_active'=>0));
                    $this->dbase->dataUpdate('offline','quiz',array('quiz_id'=>$quiz_id),array('quiz_active'=>1));
                }
                $arr = array('quiz_id'=>$quiz_id,'quiz_token'=>$token,'quiz_token_date'=>date('Y-m-d H:i:s'));
                $this->session->set_userdata($arr);
                $json['t']      = 1;
                $json['msg']    = 'TES AKTIF BERHASIL DIRUBAH';
                $json['token']  = $token;
                $json['active'] = '<strong class="text-success">TES DIBUKA</strong>';
            }
        }
	    die(json_encode($json));
    }
    function quiz_tutup(){
        $json['t'] = 0; $json['msg'] = '';
        $quiz_id    = $this->input->post('quiz_id');
        $dtQuiz     = $this->dbase->dataRow('offline','quiz',array('quiz_id'=>$quiz_id),'quiz_token');
        if (!$dtQuiz){
            $json['msg'] = 'TES TIDAK VALID';
        } else {
            $this->dbase->dataUpdate('offline','quiz',array('quiz_id'=>$quiz_id),array('quiz_active'=>0,'quiz_token'=>NULL,'quiz_token_date'=>NULL));

            $this->session->unset_userdata(array('quiz_id','quiz_token','quiz_token_date'));
            $json['t']          = 1;
            $json['token']      = '';
            $json['active']     = '<strong class="text-danger">TES DITUTUP</strong>';
            $json['msg']        = 'TES TELAH DITUTUP';
        }
        die(json_encode($json));
    }
    function status_peserta(){
	    $json['t'] = 0; $json['msg'] = ''; $json['login'] = $json['kerja'] = $json['selesai'] = $json['jmlAll'] = $json['uploaded'] = 0;
	    $ruang_id       = $this->input->post('ruang_id');
	    $keyword        = $this->input->post('keyword');
	    $quiz_id        = $this->input->post('quiz_id');
	    $dtQuiz         = $this->dbase->dataRow('offline','quiz',array('quiz_id'=>$quiz_id),'quiz_id,quiz_jml_soal');
	    if (!$dtQuiz){
	        $json['msg'] = 'Invalid data TES';
        } else {
	        if ($ruang_id){ $sql_ruang = " AND rm.ruang_id = '".$ruang_id."' "; } else { $sql_ruang = ""; }
	        $dtPes      = $this->dbase->sqlResult('offline',"
	            SELECT    u.user_nopes,u.user_fullname,ses.ses_active,ses.ses_time_left,ses.ses_id,ses.user_id,ses.ses_upload
                FROM      tb_quiz_session AS ses
                LEFT JOIN tb_ruang_member AS rm ON rm.user_id = ses.user_id
                LEFT JOIN tb_user AS u ON rm.user_id = u.user_id
                WHERE     ses.quiz_id = '".$quiz_id."' AND ses.ses_active > 0 AND ses.ses_active < 100
                          AND (
                          u.user_nopes LIKE '%".$keyword."%' OR
                          u.user_fullname LIKE '%".$keyword."%'
                          ) AND u.user_level = 1 ".$sql_ruang."
                ORDER BY  rm.ruang_id,ses.ses_active,ses.ses_upload,u.user_nopes DESC
	        ");
	        if (!$dtPes){
	            $json['msg'] = 'Belum ada peserta';
            } else {
	            $i = 0;
	            foreach ($dtPes as $val){
	                $dtPes[$i]  = $val;
	                if ($val->ses_active == 1){
	                    $json['login']++;
                    } elseif ($val->ses_active == 2){
	                    $json['kerja']++;
                    } elseif ($val->ses_active == 99){
	                    $json['selesai']++;
                    }
                    $dtPes[$i]->jmlSelesai = 0;
                    $jmKerja = $this->dbase->sqlRow('offline',"
                        SELECT    Count(qsl.qsl_id) AS jml
                        FROM      tb_quiz_session AS qs
                        LEFT JOIN tb_quiz_session_log AS qsl ON qsl.ses_id = qs.ses_id
                        WHERE     qs.quiz_id = '".$quiz_id."' AND qs.user_id = '".$val->user_id."'
                    ");
                    if ($jmKerja){
                        $dtPes[$i]->jmlSelesai = $jmKerja->jml;
                    }
                    if ($val->ses_upload == 1){ $json['uploaded']++; }
	                $i++;
                }
                $json['jmlAll'] = count($dtPes);
	            $data['jmSoal'] = $dtQuiz->quiz_jml_soal;
	            $data['data']   = $dtPes;
	            $json['t']      = 1;
	            $json['html']   = $this->load->view('tes/status_peserta',$data,true);
            }
        }
	    die(json_encode($json));
    }
    function reset_login(){
	    $json['t'] = 0; $json['msg'] = '';
	    $ses_id     = $this->input->post('ses_id');
	    $quiz_id    = $this->input->post('quiz_id');
	    if (!$ses_id){
	        $json['msg'] = 'Pilih peserta lebih dulu';
        } elseif (count($ses_id) == 0){
	        $json['msg'] = 'Pilih peserta lebih dulu';
        } else {
	        $rs = 0; $rs_name = '';
	        foreach ($ses_id as $val){
	            $chkSes = $this->dbase->dataRow('offline','quiz_session',array('ses_id'=>$val,'ses_active !='=>99),'user_id,ses_id,ses_reset_count');
	            if ($chkSes){
	                if ($chkSes->ses_reset_count > 3){
	                    $dtUser = $this->dbase->dataRow('offline','user',array('user_id'=>$chkSes->user_id),'user_fullname');
	                    if ($dtUser){
	                        $rs_name .= $dtUser->user_fullname.', ';
                        }
	                    $rs++;
                    } else {
                        $reset = $chkSes->ses_reset_count + 1;
                        $this->dbase->dataUpdate('offline','quiz_session',array('ses_id'=>$val),array('ses_active'=>0,'ses_reset_count'=>$reset));
                    }
                }
            }
            $json['msg'] = 'Peserta berhasil direset';
	        if ($rs > 0){
	            $json['msg'] = 'Reset berhasil dilakukan, tetapi '.$rs.' terindikasi melakukan kecurangan karena melakukan reset login lebih dari 3 kali. yaitu : '.$rs_name;
            }
            $json['t'] = 1;
        }
	    die(json_encode($json));
    }
    function force_finish(){
        $json['t'] = 0; $json['msg'] = '';
        $ses_id     = $this->input->post('ses_id');
        $quiz_id    = $this->input->post('quiz_id');
        if (!$ses_id){
            $json['msg'] = 'Pilih peserta lebih dulu';
        } elseif (count($ses_id) == 0){
            $json['msg'] = 'Pilih peserta lebih dulu';
        } else {
            foreach ($ses_id as $val){
                $this->dbase->dataUpdate('offline','quiz_session',array('ses_id'=>$val),array('ses_active'=>99));
            }
            $json['t'] = 1;
        }
        die(json_encode($json));
    }
    function upload_proses(){
        $json['t'] = 0; $json['msg'] = '';
        $ses_id     = $this->input->post('ses_id');
        $dtSes      = $this->dbase->dataRow('offline','quiz_session',array('ses_id'=>$ses_id),'ses_active,user_id,quiz_id,ses_upload');
        if (!$dtSes){
            $json['msg'] = 'Tidak ada data SESI';
        } elseif ($dtSes->ses_active < 99) {
            $json['msg'] = 'Peserta belum selesai';
        } elseif ($dtSes->ses_upload == 1){
            $json['msg'] = 'Sudah terupload';
        } else {
            $user_id    = $dtSes->user_id;
            $quiz_id    = $dtSes->quiz_id;
            $dtUser     = $this->dbase->dataRow('offline','user',array('user_id'=>$user_id),'sis_id');
            if (!$dtUser){
                $json['msg'] = 'Tidak ada pengguna';
            } else {
                $sis_id     = $dtUser->sis_id;
                $dtLog      = $this->dbase->sqlResult('offline',"
                    SELECT    qsl.soal_id,qsl.pg_id,qm.mapel_id
                    FROM      tb_quiz_session_log AS qsl
                    LEFT JOIN tb_quiz_soal AS qs ON qsl.qs_id = qs.qs_id
                    LEFT JOIN tb_quiz_mapel AS qm ON qs.qm_id = qm.qm_id
                    WHERE     qsl.ses_id = '".$ses_id."' AND qs.quiz_id = '".$quiz_id."'
                ");
                if (!$dtLog){
                    $json['msg'] = 'Tidak ada yang perlu diupload';
                } else {
                    //$this->db->db_select('ujian_server'); //<<<============ HAPUS JIKA SUDAH ONLINE
                    foreach ($dtLog as $valLog){
                        $chHasil = $this->dbase->dataRow('online','quiz_hasil',array('soal_id'=>$valLog->soal_id,'sis_id'=>$sis_id,'quiz_id'=>$quiz_id,'mapel_id'=>$valLog->mapel_id),'qh_id');
                        if ($chHasil){
                            $this->dbase->dataUpdate('online','quiz_hasil',array('qh_id'=>$chHasil->qh_id),
                                array('pg_id'=>$valLog->pg_id));
                        } else {
                            $qh_id = $this->dbase->dataInsert('online','quiz_hasil',array(
                                'soal_id' => $valLog->soal_id, 'sis_id' => $sis_id, 'quiz_id' => $quiz_id,
                                'mapel_id' => $valLog->mapel_id, 'pg_id' => $valLog->pg_id
                            ));
                            if (!$qh_id){
                                $json['msg'] = 'DB ERROR';
                            } else {
                                $json['t'] = 1;
                            }
                        }
                    }
                }
                //$this->db->db_select('ujian_sync'); //<<<============ HAPUS JIKA SUDAH ONLINE
                $this->dbase->dataUpdate('offline','quiz_session',array('ses_id'=>$ses_id),array('ses_upload'=>1));
            }
        }
        die(json_encode($json));
    }
}
