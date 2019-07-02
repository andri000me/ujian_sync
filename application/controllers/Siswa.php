<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Siswa extends CI_Controller {
    function index(){
        $this->load->view('errors');
    }
	function add_siswa(){
        if($_SERVER['REQUEST_METHOD']=='POST'){
            //Mendapatkan Nilai Variable
            $user_name      = $_POST['user_name'];
            $user_fullname  = $_POST['user_fullname'];
            $user_password  = $_POST['user_password'];

            $user_id = $this->dbase->dataInsert('offline','user',array('user_name'=>$user_name,'user_fullname'=>$user_fullname,'user_password'=>$user_password));
            if (!$user_id){
                echo 'Gagal';
            } else {
                echo 'Berhasil';
            }
        }
    }
    function show_data_all(){
        $dtUser = $this->dbase->dataResult('offline','user',array('user_status'=>1,'user_level'=>1),'user_id,user_name,user_fullname,user_password');
        if ($dtUser){
            $i = 0;
            $data   = array();
            foreach ($dtUser as $val){
                $data[$i] = array('user_id'=>$val->user_id,'user_name'=>$val->user_name,'user_fullname'=>$val->user_fullname,'user_password'=>$val->user_password);
                $i++;
            }
            echo json_encode(array('result'=>$data));
        } else {
            echo json_encode(array('result'=>array()));
        }
    }
    function show_data(){
	    $user_id    = $this->uri->segment(3);
	    if (strlen($user_id) > 0){
            $dtUser = $this->dbase->dataResult('offline','user',array('user_status'=>1,'user_level'=>1,'user_id'=>$user_id));
        } else {
            $dtUser = $this->dbase->dataResult('offline','user',array('user_status'=>1,'user_level'=>1));
        }
	    if ($dtUser){
	        $i = 0;
	        $data   = array();
	        foreach ($dtUser as $val){
	            $data[$i] = array('user_id'=>$val->user_id,'user_name'=>$val->user_name,'user_fullname'=>$val->user_fullname,'user_password'=>$val->user_password);
	            $i++;
            }
	        echo json_encode(array('result'=>$data));
        } else {
            echo json_encode(array('result'=>array()));
        }
    }
    function update(){
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $user_id        = $_POST['user_id'];
            $user_name      = $_POST['user_name'];
            $user_fullname  = $_POST['user_fullname'];
            $user_password  = $_POST['user_password'];
            $dtUser = $this->dbase->dataRow('offline','user',array('user_id'=>$user_id));
            if (!$dtUser){
                echo 'Tidak ada user';
            } else {
                $this->dbase->dataUpdate('offline','user',array('user_id'=>$user_id),array('user_name'=>$user_name,'user_password'=>$user_password,'user_fullname'=>$user_fullname));
                echo 'Berhasil';
            }
        }
    }
    function delete(){
	    $user_id    = $this->uri->segment(3);
	    if (!$user_id){
	        echo 'Tidak ada data';
        } else {
            $this->dbase->dataUpdate('offline','user',array('user_id'=>$user_id),array('user_status'=>0));
            echo 'Berhasil';
        }
    }
}
