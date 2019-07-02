<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>UJIAN</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css');?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/font-awesome/css/font-awesome.min.css');?>">

    <link rel="stylesheet" href="<?php echo base_url('assets/client.css');?>">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <!-- jQuery 3 -->
    <script src="<?php echo base_url('assets/bower_components/jquery/dist/jquery.min.js');?>"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo base_url('assets/bower_components/bootstrap/dist/js/bootstrap.min.js');?>"></script>

    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/sweetalert/sweetalert.css');?>">
    <script src="<?php echo base_url('assets/plugins/sweetalert/sweetalert.min.js');?>"></script>

    <script>
        $.fn.modal.Constructor.prototype.enforceFocus = function() {

        };
        var base_url = '<?php echo base_url('');?>';
        function load_page(ob) {
            var url = $(ob).attr('href');
            var tgt = $(ob).attr('data-target');
            $('.content-wrapper').load(url);
            $('.sidebar-menu .active').removeClass('active');
            $('.'+tgt).addClass('active');
            window.history.pushState({href: url}, '', url);
        }
        function show_modal(ob) {
            var title = $(ob).attr('title');
            if (!title){ title = 'Forms'; }
            var url = $(ob).attr('href');
            $('#MyModal .modal-title').html(title);
            $('#MyModal .modal-body').html('<i class="fa fa-spin fa-refresh"></i>').load(url);
            $('#MyModal').modal({ backdrop: 'static', keyboard: false });
        }
        function hide_modal() {
            $('#MyModal').modal('hide');
        }
        function show_msg(msg,type) {
            header = 'Gagal';
            if (!type){
                type = 'success';
                header = 'Berhasil';
            }
            swal(header, msg, type);
        }
        $('#MyModal').on('hidden.bs.modal',function (e) {
            console.log('x');
        })
    </script>
</head>
<body>
    <div class="header">
        <div class="inner">
            <div class="pull-right header-status">
                <?php if ($this->session->userdata('ses_active') == 2){ ?>
                    <span class="welcome-msg">SELAMAT MENGERJAKAN</span><br>
                    <?php echo $this->session->userdata('user_fullname'); ?><br>
                <?php } else { ?>
                    <span class="welcome-msg">SELAMAT DATANG</span><br>
                    <?php echo $this->session->userdata('user_fullname'); ?><br>
                    <a class="btn-logout" href="<?php echo base_url('logout');?>">LOGOUT</a>
                <?php } ?>
            </div>
            <div class="logo"><img src="<?php echo base_url('assets/tutwuri_warna.png');?>" width="50px"></div>
        </div>
    </div>
    <div class="body">
        <?php
        if (isset($body)){
            $this->load->view($body);
        } else {
            $this->load->view('quiz/startup_body');
        }
        ?>
    </div>
<div class="copyright">
    Copyright <i class="fa fa-copyright"></i> 2019 SMK Muhammadiyah Kandanghaur
</div>
<script>
    $('#formSubmit').submit(function () {
        $('#formSubmit button').prop({'disabled':true}).html('<i class="fa fa-refresh fa-spin"></i> SUBMIT');
        $.ajax({
            url     : base_url + 'quiz/start_now',
            type    : 'POST',
            dataType: 'JSON',
            data    : $(this).serialize(),
            success : function (dt) {
                if (dt.t == 0){
                    show_msg(dt.msg,'error');
                    $('#formSubmit button').prop({'disabled':false}).html('<i class="fa fa-send"></i> SUBMIT');
                } else if (dt.t == 1){
                    $('#formSubmit button').prop({'disabled':false}).html('<i class="fa fa-send"></i> SUBMIT');
                    window.location.href = base_url + 'login';
                } else {
                    $('#formSubmit button').prop({'disabled':false}).html('<i class="fa fa-send"></i> SUBMIT');
                    $.ajax({
                        url     : base_url + 'quiz/landing',
                        dataType: 'JSON',
                        success : function (dts) {
                            $('.welcome-msg').html('SELAMAT MENGERJAKAN');
                            $('.btn-logout').hide();
                            $('.body').html(dts.html);
                        }
                    });

                    //window.location.href = base_url + 'quiz/landing';
                }
            }
        });
        return false;
    });
</script>
</body>
</html>
