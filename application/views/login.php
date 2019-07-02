<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>LOGIN</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css');?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/font-awesome/css/font-awesome.min.css');?>">
    <!-- Style -->
    <link rel="stylesheet" href="<?php echo base_url('assets/login.css');?>">

    <!-- jQuery 3 -->
    <script src="<?php echo base_url('assets/bower_components/jquery/dist/jquery.min.js');?>"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo base_url('assets/bower_components/bootstrap/dist/js/bootstrap.min.js');?>"></script>
    <!-- QR SCAN -->
    <script type="text/javascript" src="assets/bower_components/webcodecamjs-master/js/qrcodelib.js"></script>
    <script type="text/javascript" src="assets/bower_components/webcodecamjs-master/js/webcodecamjquery.js"></script>


</head>
<body>
<div class="header-wrapper">
    <div class="header-logo">
        <img src="<?php echo base_url('assets/tutwuri_warna.png');?>">
    </div>
</div>

<div class="alert alert-danger" style="margin-top:10px;">
    <div class=""></div>
</div>

<div class="body">
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Masuk</strong></div>
        <div class="panel-body">
            <form id="loginForm" class="form form-horizontal">
                <div class="form-group username">
                    <label class="col-md-4 col-xs-4 col-sm-4 control-label">Username</label>
                    <div class="col-md-8 col-xs-8 col-sm-8">
                        <input type="text" id="username" name="username" class="form-control flat" placeholder="Username" autocomplete="false">
                    </div>
                </div>
                <div class="form-group password">
                    <label for="password" class="col-md-4 col-xs-4 col-sm-4 control-label">Password</label>
                    <div class="col-md-8 col-xs-8 col-sm-8">
                        <input type="password" name="password" id="password" class="form-control flat" placeholder="Password" autocomplete="false">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <button type="submit" class="pull-right flat btn btn-flat btn-success"><i class="fa fa-send"></i> SUBMIT</button>
                        <a style="margin-right:5px" onclick="show_qr();return false" href="javascript:;" class="pull-right flat btn btn-flat btn-primary"><i class="fa fa-qrcode"></i> SCAN QR</a>
                    </div>
                </div>
            </form>
            <div id="loginqr" style="border: solid 1px #000">
                <div style="border-bottom: solid 1px #000;padding:5px">
                    <div class="col-xs-9">
                        <select class="form-control flat" id="camera-select"></select>
                    </div>
                    <div class="col-xs-2">
                        <a href="javascript:;" onclick="hide_qr();return false" class="flat btn btn-flat btn-danger">Batal</a>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="videoWrap">
                    <canvas id="webcodecam-canvas"></canvas>
                    <div class="top right"></div>
                    <div class="top left"></div>
                    <div class="bottom right"></div>
                    <div class="bottom left"></div>
                </div>
            </div>
            <div class="qrloading">
                <i class="fa fa-spin fa-refresh"></i> Loading ...
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function login_qr(user_name) {
        $('.qrloading').show();
        $('#loginqr').hide();
        $.ajax({
            url     : '<?php echo base_url('login/submit_qr');?>',
            type    : 'POST',
            dataType: 'JSON',
            data    : { user_name : user_name },
            success : function (dt) {
                //alert('xxx');
                if (dt.t == 0){
                    $('.qrloading').hide();
                    $('#loginqr').show();
                    $('.alert-danger').html(dt.msg).show();
                } else {
                    if (dt.lvl == 50) {
                        window.location.href = '<?php echo base_url('tes');?>';
                    } else if (dt.lvl == 99){
                        window.location.href = '<?php echo base_url('home');?>';
                    } else {
                        window.location.href = '<?php echo base_url('quiz/startup');?>';
                    }
                }
            }
        });
    }
    $('#loginqr,.qrloading').hide();
    function hide_qr() {
        //decoder.stop();
        $('#loginForm').show();
        $('#loginqr').hide(); $('.qrloading').hide();
    }
    function show_qr() {
        $('#loginForm').hide();
        $('#loginqr').show();
        var elem_width  = $('.videoWrap').width();
        var elem_height = parseInt(elem_width / 2);
        elem_height     = Math.round(elem_height);
        var arg = {
            DecodeQRCodeRate    : 1,
            DecodeBarCodeRate   : 1,
            codeRepetition      : true,
            tryVertical         : true,
            successTimeout      : 500,
            frameRate           : 25,
            width               : elem_width,
            height              : elem_height,
            resultFunction      : function(result) {
                //alert(result.code);
                login_qr(result.code);
                //$('body').append($('<li>' + result.format + ': ' + result.code + '</li>'));
            }
        };
        var decoder = $("#webcodecam-canvas").WebCodeCamJQuery(arg).data().plugin_WebCodeCamJQuery;
        decoder.buildSelectMenu("#camera-select");
        decoder.play();

        /*  Without visible select menu
            decoder.buildSelectMenu(document.createElement('select'), 'environment|back').init(arg).play();
        */
        $('#camera-select').on('change', function(){
            decoder.stop().play();
        });
    }
    $('.alert-danger').hide();
    $('#loginForm').submit(function () {
        $('#loginForm .has-error').removeClass('has-error');
        $('#loginForm .btn-success').html('<i class="fa fa-spin fa-refresh"></i> SUBMIT').prop('disabled',true);
        $.ajax({
            url     : '<?php echo base_url('login/submit');?>',
            type    : 'POST',
            data    : $(this).serialize(),
            dataType: 'JSON',
            success : function (dt) {
                if (dt.t == 0){
                    $('.'+dt.class).addClass('has-error');
                    $('#loginForm .btn-success').html('<i class="fa fa-send"></i> SUBMIT').prop('disabled',false);
                    $('.alert-danger').html(dt.msg).show();
                } else {
                    if (dt.lvl == 50) {
                        window.location.href = '<?php echo base_url('tes');?>';
                    } else if (dt.lvl == 99){
                        window.location.href = '<?php echo base_url('home');?>';
                    } else {
                        window.location.href = '<?php echo base_url('quiz/startup');?>';
                    }
                }
            }
        });
        return false;
    })
</script>
</body>
</html>
