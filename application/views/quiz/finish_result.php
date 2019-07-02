<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>UJIKOM</title>
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
            $('#MyModal .modal-body').html('');
            $('#soal_content').summernote('destroy');
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
            SELAMAT DATANG<br>
            <?php echo $this->session->userdata('user_fullname'); ?><br>
            <a href="<?php echo base_url('logout');?>">LOGOUT</a>
        </div>
        <div class="logo"><img src="<?php echo base_url('assets/tutwuri_warna.png');?>" width="50px"></div>
    </div>
</div>
<div class="body">
    <div class="body-header">
        &nbsp;
    </div>
    <div class="body-content">
        <div class="text-center" style="margin:50px auto;width:300px;border:solid 1px #ccc;padding:10px;">
            <strong class="text-danger">TERIMA KASIH ANDA SUDAH MENYELESAIKAN TES INI. KLIK TOMBOL LOGOUT UNTUK KELUAR</strong>
            <br><br>
            <a href="<?php echo base_url('logout');?>" class="btn btn-danger btn-flat">LOGOUT</a>
        </div>
    </div>
</div>
<div class="copyright">
    Copyright <i class="fa fa-copyright"></i> 2019 SMK Muhammadiyah Kandanghaur
</div>
</body>
</html>
