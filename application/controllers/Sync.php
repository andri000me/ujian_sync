<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sync extends CI_Controller {
    function __construct()    {
        parent::__construct();
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }
	public function index(){
	    if(!$this->session->userdata('login')){
	        redirect(base_url('login'));
        } elseif ($this->session->userdata('user_level') == 1){
	        redirect(base_url('quiz/landing'));
        }
        //check ONLINE REMOTE dan LOCAL SERVER
        $data['remote'] = '<strong class="text-danger">OFFLINE</strong>';
        $local  = $this->dbase->dataRow('offline','server',array());
	    if ($local){
	        $server_id      = $local->sv_id;
	        $data['local']  = '<strong class="text-danger">BELUM SUBMIT</strong>';
	    } else {
	        $data['local']  = '<strong class="text-success">ONLINE</strong>';
	        $server_id      = '';
	    }
        if (strlen($server_id) == 0){
            $remote = $this->dbase->dataRow('online','server',array());
            if ($remote){
                $data['remote'] = '<strong class="text-success">ONLINE</strong>';
            }
        } else {
            $remote = $this->dbase->dataRow('online','server',array('server_kode'=>$server_id));
            if ($remote){
                $data['remote'] = '<strong class="text-success">ONLINE</strong>';
            } else {
                $data['remote'] = '<strong class="text-warning">STANDBY</strong>';
            }
        }
        $data['server'] = $this->dbase->dataRow('offline','server',array('sv_status'=>1));
        $data['body']   = 'sync/home';
	    $data['menu']   = 'sync';
	    if ($this->input->is_ajax_request()){
	        $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
	}
	function submit_server(){
	    $json['t'] = 0; $json['msg'] = '';
	    $sv_id      = $this->input->post('sv_id');
	    $dtServer   = $this->dbase->dataRow('online','server',array('server_kode'=>$sv_id));
	    if (strlen(trim($sv_id)) == 0){
	        $json['msg'] = 'Masukkan ID SERVER';
        } elseif (!$dtServer){
	        $json['msg'] = 'ID server salah';
        } elseif ($dtServer->server_activated == 1){
	        $json['msg'] = 'ID server sudah dipakai. Hubungi Panitia untuk mereset ID Server ini';
        } else {
	        $sv_n = $this->dbase->dataInsert('offline','server',array('jn_id'=>$dtServer->jn_id,'sv_tapel'=>$dtServer->server_tapel,'sv_id'=>$sv_id,'sv_name'=>$dtServer->server_name,'sv_password'=>''));
	        @$this->dbase->dataUpdate('online','server',array('server_kode'=>$sv_id),array('server_activated'=>1,'server_ip'=>$this->input->ip_address()));
	        if ($sv_n){
                $this->dbase->dataUpdate('online','server',array('server_kode'=>$sv_id),array('server_activated'=>1,'server_ip'=>$this->input->ip_address()));
            }
	        $json['t'] = 1;
	        $json['sv_name'] = $dtServer->server_name;
        }
	    die(json_encode($json));
    }
    function delete_all(){
        $json['t'] = 0; $json['msg'] = '';
        $dtServer = $this->dbase->dataRow('offline','server',array());
        if (!$dtServer){
            $json['msg'] = 'SERVER belum di SUMBIT';
        } else {
            ini_set('max_execution_time',100000);
            $sql = "
                    SET FOREIGN_KEY_CHECKS = 0; 
                    TRUNCATE table tb_media;
                    TRUNCATE table tb_quiz;
                    TRUNCATE table tb_quiz_mapel;
                    TRUNCATE table tb_quiz_session;
                    TRUNCATE table tb_quiz_session_log;
                    TRUNCATE table tb_quiz_soal;
                    TRUNCATE table tb_quiz_soal_pg;
                    TRUNCATE table tb_ruang;
                    TRUNCATE table tb_ruang_member;
                    TRUNCATE table tb_server;
                    TRUNCATE table tb_soal;
                    TRUNCATE table tb_soal_pg;
                    SET FOREIGN_KEY_CHECKS = 1;
                   ";
            $array  = array('tb_media','tb_quiz','tb_quiz_mapel','tb_quiz_session','tb_quiz_session_log','tb_quiz_soal','tb_quiz_soal_pg','tb_ruang','tb_ruang_member',
                'tb_server','tb_soal','tb_soal_pg');
            $this->dbase->runQuery('offline',"SET FOREIGN_KEY_CHECKS = 0");
            foreach ($array as $table){
                $this->dbase->runQuery('offline',"
                    TRUNCATE table ".$table."
                ");
            }
            $this->dbase->runQuery('offline',"SET FOREIGN_KEY_CHECKS = 1");
            $this->dbase->dataDelete('offline','user',array('user_level'=>1));
            $this->dbase->runQuery('offline',"ALTER TABLE tb_user AUTO_INCREMENT = 2");
            $this->dbase->runQuery('offline',"OPTIMIZE TABLE tb_user;");
            $files = glob(FCPATH.'assets/upload/*'); // get all file names
            foreach($files as $file){ // iterate files
                if(is_file($file))
                    @unlink($file); // delete file
            }
            $json['t']      = 1;
            $json['msg']    = 'SERVER BERHASIL DIRESET';
        }
        die(json_encode($json));
    }
    function check_sync(){
	    $json['t'] = 0; $json['msg'] = 'START';
	    $dtServer   = $this->dbase->dataRow('offline','server',array());
	    if (!$dtServer){
	        $json['msg'] = 'Silahkan masukkan ID SERVER kemudian klik SUBMIT';
        } else {
            ini_set("memory_limit","1024M");
	        //DATA PESERTA
            $data[0]    = new stdClass();
            $data[0]->data_name = 'DATA 1 <label class="text-muted pull-right">Peserta</label>';
            $data[0]->type      = 1;
            $dtPesON    = $this->dbase->sqlResult('online',"
                SELECT    rm.rm_id
                FROM      tb_ruang_member AS rm
                LEFT JOIN tb_server_ruang AS sr ON rm.sr_id = sr.sr_id
                LEFT JOIN tb_server AS s ON sr.server_id = s.server_id
                WHERE     s.server_kode = '".$dtServer->sv_id."' AND rm.rm_status = 1
            ");
            if ($dtPesON){ $data[0]->b = count($dtPesON); } else { $data[0]->b = 0; }
            $dtPesOF    = $this->dbase->dataResult('offline','user',array('user_status'=>1,'user_level'=>1),'user_id');
            $data[0]->a = count($dtPesOF);
            //DATA RUANG
            $data[1]            = new stdClass();
            $data[1]->data_name = 'DATA 2 <label class="text-muted pull-right">Ruang dan Sesi</label>';
            $data[1]->type      = 2;
            $dtRuangON  = $this->dbase->sqlResult('online',"
                SELECT    rm.sis_id
                FROM      tb_ruang_member AS rm
                LEFT JOIN tb_server_ruang AS sr ON rm.sr_id = sr.sr_id AND sr.sr_status = 1
                LEFT JOIN tb_server AS s ON sr.server_id = s.server_id
                WHERE     s.server_kode = '".$dtServer->sv_id."' AND rm.rm_status = 1  AND rm.rm_type = 'pes'
            ");
            $sis_id     = '';
            if ($dtRuangON) {
                $sis_id = array(); $xx = 0;
                foreach ($dtRuangON as $val){
                    $sis_id[$xx]    = "'".$val->sis_id."'";
                    $xx++;
                }
                $sis_id     = implode(",",$sis_id);
                $data[1]->b = count($dtRuangON);
            } else {
                $data[1]->b = 0;
            }
            $data[1]->a = count($this->dbase->dataResult('offline','ruang_member',array('rm_status'=>1)));
            //DATA QUIZ
            $data[2]            = new stdClass();
            $data[2]->data_name = 'DATA 3 <label class="text-muted pull-right">Tes</label>';
            $data[2]->type      = 3;
            $dtQuizON   = $this->dbase->sqlResult('online',"
                SELECT    q.quiz_id
                FROM      tb_quiz AS q
                LEFT JOIN tb_jenis_nilai AS jn ON q.jn_id = jn.jn_id
                WHERE     q.quiz_tapel = '".$dtServer->sv_tapel."' AND q.jn_id = '".$dtServer->jn_id."' AND q.quiz_status = 1
            ");
            if ($dtQuizON){
                $data[2]->b     = count($dtQuizON);
                $q = 0;
                $quiz_id = array();
                foreach ($dtQuizON as $val){
                    $quiz_id[$q] = $val->quiz_id;
                    $q++;
                }
                $quiz_id = implode(",",$quiz_id);
            } else {
                $data[2]->b     = 0;
                $quiz_id        = '';
            }
            $data[2]->a     = count($this->dbase->dataResult('offline','quiz',array('quiz_status'=>1),'quiz_id'));
            //DATA MAPEL
            $data[3]        = new stdClass();
            $data[3]->data_name = 'DATA 4 <label class="text-muted pull-right">Mata Pelajaran</label>';
            $data[3]->type      = 4;
            $dtMapelON      = $this->dbase->sqlResult('online',"
                SELECT    qm.quiz_id,qm.mapel_id,qm.qm_id
                FROM      tb_quiz_mapel AS qm
                LEFT JOIN tb_mapel AS m ON qm.mapel_id = m.mapel_id AND m.mapel_status = 1
                LEFT JOIN tb_quiz AS q ON qm.quiz_id = q.quiz_id AND q.quiz_status = 1
                WHERE     qm.quiz_id IN (".$quiz_id.") AND qm.qm_status = 1
            ");
            if ($dtMapelON){
                $data[3]->b     = count($dtMapelON);
                $i = 0; $mapel_id = array(); $qm_id = array();
                foreach ($dtMapelON as $val){
                    $mapel_id[$i]   = $val->mapel_id;
                    $qm_id[$i]      = $val->qm_id;
                    $i++;
                }
                $mapel_id   = implode(",",$mapel_id);
                $qm_id      = implode(",",$qm_id);
            } else {
                $data[3]->b     = 0;
                $mapel_id       = $qm_id = '';
            }
            $data[3]->a     = count($this->dbase->dataResult('offline','quiz_mapel',array('qm_status'=>1)));

            //DATA BANK SOAL
            $data[4]            = new stdClass();
            $data[4]->data_name = 'DATA 5 <label class="text-muted pull-right">Bank Soal</label>';
            $data[4]->type      = 5;
            $dtSoalON           = $this->dbase->sqlResult('online',"
                SELECT      pg.pg_id
                FROM        tb_soal_pg AS pg
                LEFT JOIN   tb_soal AS s ON pg.soal_id = s.soal_id AND s.soal_status = 1
                WHERE       pg.pg_status = 1 AND s.mapel_id IN (".$mapel_id.")
            ");
            if ($dtSoalON){
                $data[4]->b     = count($dtSoalON);
            } else {
                $data[4]->b     = 0;
            }

            $data[4]->a     = count($this->dbase->dataResult('offline','soal_pg',array('pg_status'=>1),'pg_id'));
            //DISTRIBUSI SOAL
            $data[5]                = new stdClass();
            $data[5]->data_name     = 'DATA 6 <label class="text-muted pull-right">Distribusi Soal</label>';
            $data[5]->type          = 6;
            $dataQS         = $this->dbase->sqlResult('online',"
                SELECT    qspg.qspg_id
                FROM      tb_quiz_soal_pg AS qspg
                LEFT JOIN tb_quiz_soal AS qs ON qspg.qs_id = qs.qs_id
                WHERE     qs.qm_id IN (".$qm_id.") AND qs.sis_id IN (".$sis_id.") AND qs.qs_status = 1
            ");
            if ($dataQS){
                $data[5]->b     = count($dataQS);
            } else {
                $data[5]->b     = 0;
            }
            $data[5]->a         = count($this->dbase->dataResult('offline','quiz_soal_pg',array()));
            //GAMBAR
            $data[6]        = new stdClass();
            $data[6]->data_name     = 'DATA 7 <label class="text-muted pull-right">Gambar Soal dan Jawaban</label>';
            $data[6]->type          = 7;
            $data[6]->b             = $this->dbase->dataRow('online','media',array('media_status'=>1),'COUNT(media_id) AS cnt')->cnt;
            $data[6]->a             = $this->dbase->dataRow('offline','media',array('media_status'=>1),'COUNT(media_id) AS cnt')->cnt;

            $dataS['data']  = $data;
            $json['t']      = 1;
            $json['html']   = $this->load->view('sync/data_home',$dataS,TRUE);
        }
	    die(json_encode($json));
    }
    function start_sync(){
	    $json['t'] = 1; $json['msg'] = 'START';
	    $what   = $this->input->post('what');
	    $svLocal= $this->dbase->dataRow('offline','server',array());
	    if (!$svLocal){
	        $json['msg'] = 'Silahkan masukkan ID SERVER kemudian klik SUBMIT';
        } else {
            if ($what == 0 && $what > 5){
                $json['msg'] = 'Invalid data';
            } else {
                //134217728
                //12587008
                ini_set('max_execution_time',10000);
                if ($what == 1){ //data peserta
                    $dtPes  = $this->dbase->sqlResult('online',"
                        SELECT    s.sis_id,s.sis_username,s.sis_password,s.sis_fullname,sv.server_kode,s.sis_nopes
                        FROM      tb_ruang_member AS rm
                        LEFT JOIN tb_server_ruang AS sr ON rm.sr_id = sr.sr_id
                        LEFT JOIN tb_server AS sv ON sr.server_id = sv.server_id
                        LEFT JOIN tb_siswa AS s ON rm.sis_id = s.sis_id AND s.sis_status = 1
                        WHERE     rm.rm_status = 1 AND sv.server_kode = '".$svLocal->sv_id."'
                    ");
                    if (!$dtPes){
                        $json['msg'] = 'TIDAK ADA PESERTA';
                    } else {
                        $json['t']      = 1;
                        $json['type']   = 1;
                        $json['data']   = $dtPes;
                    } //data peserta
                } elseif ($what == 2){ //data ruaang dan member
                    $dtRmem  = $this->dbase->sqlResult('online',"
                        SELECT    rm.sr_id,rm.sis_id,rm.rm_sesi,sr.sr_name,s.server_kode,sr.sr_id
                        FROM      tb_ruang_member AS rm
                        LEFT JOIN tb_server_ruang AS sr ON rm.sr_id = sr.sr_id AND sr.sr_status = 1
                        LEFT JOIN tb_server AS s ON sr.server_id = s.server_id
                        WHERE     s.server_kode = '".$svLocal->sv_id."' AND rm.rm_status = 1 AND rm.rm_type = 'pes'
                    ");
                    if (!$dtRmem){
                        $json['msg']    = 'TIDAK ADA RUANG DAN PESERTANYA';
                    } else {
                        $json['t']      = 1;
                        $json['type']   = 2;
                        $json['data']   = $dtRmem;
                    }
                } elseif ($what == 3){ //data QUIZ
                    $dtQuiz = $this->dbase->sqlResult('online',"
                        SELECT    q.quiz_id,jn.jn_id,jn.jn_name,q.quiz_name,q.quiz_start,q.quiz_tapel,q.quiz_timer,q.quiz_jml_soal,
                                  q.quiz_random_soal,q.quiz_random_pg
                        FROM      tb_quiz AS q
                        LEFT JOIN tb_jenis_nilai AS jn ON q.jn_id = jn.jn_id
                        WHERE     q.quiz_tapel = '".$svLocal->sv_tapel."' AND q.jn_id = '".$svLocal->jn_id."'
                    ");
                    if (!$dtQuiz){
                        $json['msg']    = 'TIDAK ADA TES';
                    } else {
                        $i = 0;
                        foreach ($dtQuiz as $val){
                            $dtQuiz[$i]     = $val;
                            $dtQuiz[$i]->server_kode = $svLocal->sv_id;
                            $i++;
                        }
                        $json['t']      = 1;
                        $json['type']   = 3;
                        $json['data']   = $dtQuiz;
                    }
                } elseif ($what == 4){ //data MAPEL
                    $dtQuiz     = $this->dbase->dataResult('online','quiz',array('quiz_tapel'=>$svLocal->sv_tapel,'jn_id'=>$svLocal->jn_id),'quiz_id');
                    $quiz_id    = '';
                    if ($dtQuiz){
                        $quiz_id = array();
                        $q = 0;
                        foreach ($dtQuiz as $val){
                            $quiz_id[$q] = $val->quiz_id;
                            $q++;
                        }
                        $quiz_id = implode(",",$quiz_id);
                    }
                    $dtMapelON      = $this->dbase->sqlResult('online',"
                        SELECT    qm.quiz_id,qm.mapel_id,m.mapel_name,m.mapel_sing,qm.qm_id
                        FROM      tb_quiz_mapel AS qm
                        LEFT JOIN tb_mapel AS m ON qm.mapel_id = m.mapel_id AND m.mapel_status = 1
                        LEFT JOIN tb_quiz AS q ON qm.quiz_id = q.quiz_id AND q.quiz_status = 1
                        WHERE     q.quiz_id IN (".$quiz_id.") AND qm.qm_status = 1
                    ");
                    if (!$dtMapelON){
                        $json['msg']    = 'TIDAK ADA TES';
                    } else {
                        $i = 0;
                        foreach ($dtMapelON as $val){
                            $dtMapelON[$i]     = $val;
                            $dtMapelON[$i]->server_kode = $svLocal->sv_id;
                            $i++;
                        }
                        $json['t']      = 1;
                        $json['type']   = 4;
                        $json['data']   = $dtMapelON;
                    }
                } elseif ($what == 5){ //BANK SOAL
                    $dtMapel = $this->dbase->dataResult('offline','quiz_mapel',array('qm_status'=>1),'mapel_id');
                    if (!$dtMapel) {
                        $json['msg'] = 'Tidak ada MAPEL';
                    } else {
                        $mapel_id = array();
                        $i        = 0;
                        foreach ($dtMapel as $val){
                            $mapel_id[$i]   = $val->mapel_id;
                            $i++;
                        }
                        $mapel_id       = implode(",",$mapel_id);
                        $dtSoal         = $this->dbase->sqlResult('online',"
                            SELECT      pg.pg_nomor,pg.pg_content,pg.pg_id,s.soal_id,s.soal_nomor,
                                        s.soal_content,s.soal_type
                            FROM        tb_soal_pg AS pg
                            LEFT JOIN   tb_soal AS s ON pg.soal_id = s.soal_id AND s.soal_status = 1
                            WHERE       pg.pg_status = 1 AND s.mapel_id IN (".$mapel_id.")
                        ");
                        if (!$dtSoal){
                            $json['msg'] = 'Tidak ada data soal';
                        } else {
                            $i = 0;
                            foreach ($dtSoal as $val){
                                $dtSoal[$i]     = $val;
                                $dtSoal[$i]->server_kode = $svLocal->sv_id;
                                $i++;
                            }
                            $json['t']      = 1;
                            $json['type']   = 5;
                            $json['data']   = $dtSoal;
                        }
                    }
                } elseif ($what == 6){ //DISTRIBUSI SOAL
                    ini_set('memory_limit',10000000000);
                    ini_set('max_execution_time',100000);
                    $dtMapel        = $this->dbase->dataResult('offline','quiz_mapel',array('qm_status'=>1),'qm_id,quiz_id');
                    $dtSis          = $this->dbase->dataResult('offline','user',array('user_level'=>1),'sis_id');
                    $qm_id = $sis_id = '';
                    if ($dtMapel){
                        $i = 0;
                        $qm_id = array();
                        foreach ($dtMapel as $val){
                            $qm_id[$i]  = $val->qm_id;
                            $i++;
                        }
                        $qm_id = implode(",",$qm_id);
                    }
                    if ($dtSis){
                        $i = 0; $sis_id = array();
                        foreach ($dtSis as $val){
                            $sis_id[$i] = "'".$val->sis_id."'";
                            $i++;
                        }
                        $sis_id = implode(",",$sis_id);
                    }
                    $dataQS         = $this->dbase->sqlResult('online',"
                        SELECT    qs.qs_id,qs.soal_id,qs.sis_id,qs.qm_id,qs.mapel_id,qs.soal_nomor,
                                  qspg.qspg_id,qspg.qspg_nomor,qspg.pg_id,qm.quiz_id
                        FROM      tb_quiz_soal_pg AS qspg
                        LEFT JOIN tb_quiz_soal AS qs ON qspg.qs_id = qs.qs_id
                        LEFT JOIN tb_quiz_mapel AS qm ON qs.qm_id = qm.qm_id
                        WHERE     qs.qm_id IN (".$qm_id.") AND qs.sis_id IN (".$sis_id.") AND qs.qs_status = 1
                    ");
                    if (!$dataQS){
                        $json['msg'] = 'Tidak ada soal TES';
                    } else {
                        $json['sv_id']  = $svLocal->sv_id;
                        $json['t']      = 1;
                        $json['type']   = 6;
                        $json['data']   = $dataQS;
                    }
                } elseif ($what == 7){
                    $dtMedia    = $this->dbase->dataResult('online','media',array('media_status'=>1),'media_name,media_url,media_id,mapel_id,media_type');
                    if (!$dtMedia){
                        $json['msg'] = 'Tidak ada data media';
                    } else {
                        $json['t']      = 1;
                        $json['sv_id']  = $svLocal->sv_id;
                        $json['type']   = 7;
                        $json['data']   = $dtMedia;
                    }
                }
            }
        }
	    die(json_encode($json));
    }
    function insert_pes(){
	    $json['t'] = 0; $json['msg'] = '';
	    $datanya            = $this->input->post('data');
	    $sv_id              = $datanya['server_kode'];
	    $sis_id             = $datanya['sis_id'];
	    $sis_username       = $datanya['sis_username'];
	    $sis_password       = $datanya['sis_password'];
	    $sis_fullname       = $datanya['sis_fullname'];
	    $sis_nopes          = $datanya['sis_nopes'];
	    $chkSv              = $this->dbase->dataRow('offline','server',array('sv_id'=>$sv_id));
	    if (!$chkSv){
	        $json['msg'] = 'Error SERVER';
        } else {
            $chkUser    = $this->dbase->dataRow('offline','user',array('sis_id'=>$sis_id));
            if (!$chkUser){
                $this->dbase->dataInsert('offline','user',array(
                    'sis_id' => $sis_id, 'user_name' => $sis_username, 'user_fullname' => $sis_fullname, 'user_password' => $sis_password,
                    'user_level' => 1, 'user_nopes' => $sis_nopes
                ));
                $json['t'] = 1;
            }
        }
	    die(json_encode($json));
    }
    function insert_ruang(){
        $json['t'] = 0; $json['msg'] = '';
        $datanya            = $this->input->post('data');
        $sv_id              = $datanya['server_kode'];
        $sr_name            = $datanya['sr_name'];
        $rm_sesi            = $datanya['rm_sesi'];
        $sis_id             = $datanya['sis_id'];
        $sr_id              = $datanya['sr_id'];
        $chkSv              = $this->dbase->dataRow('offline','server',array('sv_id'=>$sv_id));
        if (!$chkSv){
            $json['msg'] = 'Error SERVER';
        } else {
            $chkUser        = $this->dbase->dataRow('offline','user',array('sis_id'=>$sis_id));
            if ($chkUser){
                $user_id        = $chkUser->user_id;
                $chkRuang       = $this->dbase->dataRow('offline','ruang',array('ruang_id'=>$sr_id),'ruang_id');
                if (!$chkRuang){
                    $this->dbase->dataInsert('offline','ruang',array(
                        'ruang_id' => $sr_id, 'ruang_name' => $sr_name, 'sv_id' => $sv_id
                    ));
                }
                $this->dbase->dataInsert('offline','ruang_member',array(
                    'ruang_id' => $sr_id, 'user_id' => $user_id, 'rm_sesi' => $rm_sesi
                ));
                $json['t'] = 1;
            }
        }
        die(json_encode($json));
    }
    function insert_quiz(){
	    $json['t'] = 0; $json['msg'] = '';
        $datanya            = $this->input->post('data');
        $sv_id              = $datanya['server_kode'];
        $quiz_id            = $datanya['quiz_id'];
        $jn_id              = $datanya['jn_id'];
        $jn_name            = $datanya['jn_name'];
        $quiz_name          = $datanya['quiz_name'];
        $quiz_start         = $datanya['quiz_start'];
        $quiz_tapel         = $datanya['quiz_tapel'];
        $quiz_timer         = $datanya['quiz_timer'];
        $quiz_jml_soal      = $datanya['quiz_jml_soal'];
        $quiz_random_soal   = $datanya['quiz_random_soal'];
        $quiz_random_pg     = $datanya['quiz_random_pg'];
        $chkQuiz            = $this->dbase->dataRow('offline','quiz',array('quiz_name'=>$quiz_name),'quiz_id');
        if (!$chkQuiz){
            $q_id = $this->dbase->dataInsert('offline','quiz',array(
                'quiz_id' => $quiz_id, 'quiz_last_id' => $quiz_id, 'quiz_name' => $quiz_name, 'quiz_date' => $quiz_start,
                'quiz_timer' => $quiz_timer, 'quiz_random_soal' => $quiz_random_soal, 'quiz_random_pg' => $quiz_random_pg, 'quiz_jml_soal' => $quiz_jml_soal,
                'jn_name' => $jn_name
            ));
            if ($q_id){
                $json['t'] = 1;
            }
        }
	    die(json_encode($json));
    }
    function insert_mapel(){
        $json['t'] = 0; $json['msg'] = '';
        $datanya            = $this->input->post('data');
        $sv_id              = $datanya['server_kode'];
        $quiz_id            = $datanya['quiz_id'];
        $mapel_id           = $datanya['mapel_id'];
        $mapel_name         = $datanya['mapel_name'];
        $mapel_sing         = $datanya['mapel_sing'];
        $qm_id              = $datanya['qm_id'];
        $chkMapel           = $this->dbase->sqlRow('offline',"
            SELECT    qm.qm_id,qm.quiz_id,qm.mapel_name,qm.mapel_id,q.quiz_last_id
            FROM      tb_quiz_mapel AS qm
            LEFT JOIN tb_quiz AS q ON qm.quiz_id = q.quiz_id AND q.quiz_status = 1
            WHERE     qm.mapel_id = '".$mapel_id."' AND q.quiz_last_id = '".$quiz_id."'
        ");
        if (!$chkMapel){
            $dtQuiz = $this->dbase->dataRow('offline','quiz',array('quiz_last_id'=>$quiz_id),'quiz_id');
            if ($dtQuiz){
                $this->dbase->dataInsert('offline','quiz_mapel',array(
                    'quiz_id' => $dtQuiz->quiz_id, 'mapel_name' => $mapel_name, 'mapel_id' => $mapel_id, 'qm_id' => $qm_id
                ));
                $json['t'] = 1;
            }
        }
        die(json_encode($json));
    }
    function insert_soal(){
        $json['t'] = 0; $json['msg'] = '';
        $datanya            = $this->input->post('data');
        $pg_nomor           = $datanya['pg_nomor'];
        $pg_content         = $datanya['pg_content'];
        $pg_id              = $datanya['pg_id'];
        $soal_id            = $datanya['soal_id'];
        $soal_nomor         = $datanya['soal_nomor'];
        $soal_content       = $datanya['soal_content'];
        $soal_type          = $datanya['soal_type'];
        $server_kode        = $datanya['server_kode'];
        $chkServer          = $this->dbase->dataRow('offline','server',array('sv_id'=>$server_kode),'sv_id');
        if (!$chkServer){
            $json['msg'] = 'Invalid ID SERVER';
        } else {
            $chkSoal    = $this->dbase->dataRow('offline','soal',array('soal_id'=>$soal_id,'soal_status'=>1),'soal_status');
            if (!$chkSoal){
                $arr_soal   = array(
                    'soal_id' => $soal_id, 'soal_content' => $soal_content, 'soal_type' => $soal_type, 'soal_nomor' => $soal_nomor
                );
                $this->dbase->dataInsert('offline','soal',$arr_soal);
            }
            $arr_pg     = array(
                'pg_id' => $pg_id, 'soal_id' => $soal_id, 'pg_content' => $pg_content, 'pg_nomor' => $pg_nomor
            );
            $this->dbase->dataInsert('offline','soal_pg',$arr_pg);
        }
        $json['t'] = 1;
        die(json_encode($json));
    }
    function insert_soal_tes(){
        $json['t'] = 0; $json['msg'] = '';
        $datanya            = $this->input->post('data');
        //die(var_dump($datanya));
        $qs_id          = $datanya['qs_id'];
        $soal_id        = $datanya['soal_id'];
        $sis_id         = $datanya['sis_id'];
        $qm_id          = $datanya['qm_id'];
        $mapel_id       = $datanya['mapel_id'];
        $soal_nomor     = $datanya['soal_nomor'];
        $qspg_id        = $datanya['qspg_id'];
        $qspg_nomor     = $datanya['qspg_nomor'];
        $pg_id          = $datanya['pg_id'];
        $server_kode    = $this->input->post('sv_id');
        $quiz_id        = $datanya['quiz_id'];
        $dtServer       = $this->dbase->dataRow('offline','server',array('sv_id'=>$server_kode),'sv_id');
        if (!$dtServer){
            $json['msg'] = 'Invalid SERVER';
        } else {
            $dtUser     = $this->dbase->dataRow('offline','user',array('sis_id'=>$sis_id,'user_level'=>1),'user_id');
            if (!$dtUser){
                $json['msg'] = 'Tidak ada data USER';
            } else {
                $counter    = 0;
                $user_id    = $dtUser->user_id;
                $dtQS       = $this->dbase->dataRow('offline','quiz_soal',array('qs_id'=>$qs_id),'qs_id');
                if (!$dtQS){
                    $this->dbase->dataInsert('offline','quiz_soal',array(
                        'qs_id' => $qs_id, 'qm_id' => $qm_id, 'user_id' => $user_id, 'soal_id' => $soal_id, 'qs_nomor' => $soal_nomor,
                        'mapel_id' => $mapel_id, 'quiz_id' => $quiz_id
                    ));
                    $counter++;
                }
                $dtQSPG     = $this->dbase->dataRow('offline','quiz_soal_pg',array('qspg_id'=>$qspg_id),'qspg_id');
                if (!$dtQSPG){
                    $this->dbase->dataInsert('offline','quiz_soal_pg',array(
                        'qspg_id' => $qspg_id, 'qs_id' => $qs_id, 'pg_id' => $pg_id, 'qspg_nomor' => $qspg_nomor, 'user_id' => $user_id,
                        'quiz_id' => $quiz_id, 'qm_id' => $qm_id
                    ));
                    $counter++;
                }
                if ($counter == 0){
                    $json['msg'] = 'Tidak ada yang perlu disyncron';
                } else {
                    $json['t']  = 1;
                }
            }
        }
        die(json_encode($json));
    }
    function insert_media(){
	    $json['t'] = 0; $json['msg'] = '';
	    $datanya        = $this->input->post('data');
	    $sv_id          = $this->input->post('sv_id');
	    $dtSV           = $this->dbase->dataRow('offline','server',array('sv_id'=>$sv_id));
	    if (!$dtSV){
	        $json['msg'] = 'Tidak ada server';
        } else {
	        $media_url      = $datanya['media_url'];
	        $media_id       = $datanya['media_id'];
	        $mapel_id       = $datanya['mapel_id'];
	        $media_type     = $datanya['media_type'];
	        $media_name     = $datanya['media_name'];
	        $src_file       = 'https://ujian.smkmuhkandanghaur.sch.id/assets/upload/'.$media_url;
	        $dst_file       = FCPATH . 'assets/upload/'.$media_url;
	        $file_copy      = @copy($src_file,$dst_file);
	        if (!$file_copy){
	            $json['msg'] = 'Gagal Copy';
            } else {
	            $media_id   = $this->dbase->dataInsert('offline','media',array(
	                'media_id' => $media_id, 'media_name' => $media_name, 'mapel_id' => $mapel_id, 'media_url' => $media_url,
                    'media_type' => $media_type
                ));
	            if (!$media_id){
	                $json['msg'] = 'DB ERROR';
                } else {
	                $json['t'] = 1;
                }
            }
        }
	    die(json_encode($json));
    }
    function insert_pic(){
        $dir = new DirectoryIterator(dirname(__FILE__));
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                var_dump($fileinfo->getFilename());
            }
        }
    }
}
