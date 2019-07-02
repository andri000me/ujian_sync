<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dbase extends CI_Model {
	function __construct(){
		//$this->load->database();
	}
	function sqlResult($dbname=FALSE,$sql){
        if ($dbname){ $this->dbs = @$this->load->database($dbname, TRUE); } else { $this->dbs = @$this->load->database(); }
        if (!$this->dbs->conn_id){
            return 0;
        } else {
            $data	= $this->dbs->query($sql);
            if ($data){
                return $data->result();
            } else {
                return 0;
            }
        }
        $this->dbs->close();
	}
	function sqlRow($dbname=FALSE,$sql){
        if ($dbname){ $this->dbs = @$this->load->database($dbname, TRUE); } else { $this->dbs = @$this->load->database(); }
        if (!$this->dbs->conn_id){
            return 0;
        } else {
            $data	= $this->dbs->query($sql);
            if ($data){
                return $data->row();
            } else {
                return 0;
            }
        }
        $this->dbs->close();
	}
	function dataResult($dbname=FALSE,$table,$data=FALSE,$select=FALSE,$order=FALSE,$sort=FALSE,$limit=FALSE,$offset=FALSE){
	    //die(var_dump($dbname));
        if ($dbname){ $this->dbs = @$this->load->database($dbname, TRUE); } else { $this->dbs = @$this->load->database(); }
        if (!$this->dbs->conn_id){
            return 0;
        } else {
            if ($select){ $this->dbs->select($select); }
            if ($sort) { $sort = $sort; } else { $sort = 'desc'; }
            if ($order){ $this->dbs->order_by($order, $sort);  }
            if (!$limit) { $limit = 1000000; }
            if ($offset){ $offset = $offset; }
            if ($data){
                $query		= $this->dbs->get_where($table,$data,$limit, $offset);
            } else {
                $query		= $this->dbs->get($table,$limit, $offset);
            }
            if ($query){
                return $query->result();
            } else {
                return 0;
            }
        }
        $this->dbs->close();
	}
    function dataResultArray($dbname=FALSE,$table,$data=FALSE,$select=FALSE,$order=FALSE,$sort=FALSE,$limit=FALSE,$offset=FALSE){
        //die(var_dump($dbname));
        if ($dbname){ $this->dbs = @$this->load->database($dbname, TRUE); } else { $this->dbs = @$this->load->database(); }
        if (!$this->dbs->conn_id){
            return 0;
        } else {
            if ($select){ $this->dbs->select($select); }
            if ($sort) { $sort = $sort; } else { $sort = 'desc'; }
            if ($order){ $this->dbs->order_by($order, $sort);  }
            if (!$limit) { $limit = 1000000; }
            if ($offset){ $offset = $offset; }
            if ($data){
                $query		= $this->dbs->get_where($table,$data,$limit, $offset);
            } else {
                $query		= $this->dbs->get($table,$limit, $offset);
            }
            if ($query){
                return $query->result_array();
            } else {
                return 0;
            }
        }
        $this->dbs->close();
    }
	function dataRow($dbname=FALSE,$table,$data=FALSE,$select=FALSE){
        if ($dbname){ $this->dbs = @$this->load->database($dbname, TRUE); } else { $this->dbs = @$this->load->database(); }
        if (!$this->dbs->conn_id){
            return 0;
        } else {
            if ($select){ $this->dbs->select($select); }
            if ($data){
                $query		= $this->dbs->get_where($table,$data);
            } else {
                $query		= $this->dbs->get($table);
            }
            if ($query){
                return $query->row();
            } else {
                return 0;
            }
        }
        $this->dbs->close();
	}
	function dataDelete($dbname=FALSE,$table,$where){
        if ($dbname){ $this->dbs = @$this->load->database($dbname, TRUE); } else { $this->dbs = @$this->load->database(); }
        if (!$this->dbs->conn_id){
            return 0;
        } else {
            $this->dbs->delete($table,$where);
        }
        $this->dbs->close();
	}
	function dataUpdate($dbname=FALSE,$table,$where,$data){
        if ($dbname){ $this->dbs = @$this->load->database($dbname, TRUE); } else { $this->dbs = @$this->load->database(); }
        if (!$this->dbs->conn_id){
            return 0;
        } else {
            $this->dbs->where($where);
            $this->dbs->update($table, $data);
        }
        $this->dbs->close();
	}
	function dataInsert($dbname=FALSE,$table,$data){
        if ($dbname){ $this->dbs = @$this->load->database($dbname, TRUE); } else { $this->dbs = @$this->load->database(); }
        if (!$this->dbs->conn_id){
            return 0;
        } else {
            $this->dbs->insert($table,$data);
            return $this->dbs->insert_id();
        }
        $this->dbs->close();
	}
	function last_id(){
        if (!$this->dbs->conn_id){
            return 0;
        } else {
            return $this->dbs->insert_id();
        }
	}
	function runQuery($dbname,$sql){
        if ($dbname){ $this->dbs = @$this->load->database($dbname, TRUE); } else { $this->dbs = @$this->load->database(); }
        if ($this->dbs->query($sql)){
            return 1;
        } else {
            return 0;
        }
    }
	function dataCreate($dbname,$sql){
        if ($dbname){ $this->dbs = @$this->load->database($dbname, TRUE); } else { $this->dbs = @$this->load->database(); }
		if ($this->dbs->query($sql)){
			return 1;
		} else {
			return 0;
		}
	}
	function dataEmpty($dbname,$table){
        if ($dbname){ $this->dbs = @$this->load->database($dbname, TRUE); } else { $this->dbs = @$this->load->database(); }
	    $this->dbs->empty_table($table);
        $this->dbs->query("OPTIMIZE TABLE ".$table);
    }
}
