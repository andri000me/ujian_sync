<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>UJIAN SYNC</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css');?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/font-awesome/css/font-awesome.min.css');?>">
    <!-- Select 2 -->
    <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/select2/dist/css/select2.min.css');?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url('assets/dist/css/AdminLTE.min.css');?>">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?php echo base_url('assets/dist/css/skins/skin-blue.min.css');?>">


    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <!-- jQuery 3 -->
    <script src="<?php echo base_url('assets/bower_components/jquery/dist/jquery.min.js');?>"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?php echo base_url('assets/bower_components/jquery-ui/jquery-ui.min.js');?>"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo base_url('assets/bower_components/bootstrap/dist/js/bootstrap.min.js');?>"></script>
    <!-- Slimscroll -->
    <script src="<?php echo base_url('assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js');?>"></script>
    <!-- FastClick -->
    <script src="<?php echo base_url('assets/bower_components/fastclick/lib/fastclick.js');?>"></script>
    <!-- Select 2 -->
    <script src="<?php echo base_url('assets/bower_components/select2/dist/js/select2.full.min.js');?>"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo base_url('assets/dist/js/adminlte.min.js');?>"></script>

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
            //$('#soal_content').summernote('destroy');
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
<body class="hold-transition skin-blue sidebar-mini fixed">
<div id="MyModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="hide_modal();return false" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="modal-body">
                <p>One fine body&hellip;</p>
            </div>
            <div class="modal-footer">
                <!--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="wrapper">

    <?php $this->load->view('nav/header'); ?>
    <!-- Left side column. contains the logo and sidebar -->
    <?php $this->load->view('nav/left_sidebar'); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <?php
        if (isset($body)){
            $this->load->view($body);
        } else {
            $this->load->view('dashboard');
        }
        ?>
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 2.4.0
        </div>
        <strong>Copyright &copy; 2018 SMK Muhammadiyah Kandanghaur.</strong> All rights reserved.
    </footer>

</div>
<!-- ./wrapper -->



</body>
</html>
