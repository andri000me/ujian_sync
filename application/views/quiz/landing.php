
        <div class="body-header">
            <strong class="nomor-soal"># 001</strong>
            <div class="timer-wrapper">
                <span class="timer-minute"><?php echo round($this->session->userdata('ses_time_left')/60);?></span>:<span class="timer-seconds">00</span> menit
            </div>
        </div>
        <div class="body-content">

        </div>
        <div class="text-center">
            <a href="javascript:;" onclick="show_hide_nomor();return false" class="hidden-sm hidden-xs btn-showhide btn-warning btn-flat btn-xs btn"><i class="fa fa-chevron-up"></i></a>
        </div>
        <div class="body-footer list-nomor-soal hidden-xs hidden-sm" id="nomor-soal">
            <?php
            $nomor = 1;
            foreach ($soal as $valSoal){
                $jawaban = '&nbsp;'; $btnclass = 'bg_white';
                if ($valSoal->jawab > 0){
                    $jawaban    = $this->conv->toStr($valSoal->jawab);
                    $btnclass   = 'bg_red';
                }
                echo '<a href="javascript:;" onclick="load_soal(this);return false" qs-id="'.$valSoal->qs_id.'" soal-id="'.$valSoal->soal_id.'" class="btn btn-default btn-flat soalke_'.$valSoal->soal_id.'" style="position:relative">
                        '.$nomor.'
                        <span class="'.$btnclass.' soaljawaban jawabanke_'.$valSoal->soal_id.'">'.$jawaban.'</span>
                      </a> ';
                $nomor++;
            }
            ?>
        </div>
        <div class="body-footer hidden-sm hidden-xs">
            <div class="col-md-4 mb-10">
                <a href="javascript:;" class="btn-prev btn-block btn btn-flat btn-primary"><i class="fa fa-chevron-left"></i> SOAL SEBELUMNYA</a>
            </div>
            <div class="col-md-4 mb-10">
                <a href="javascript:" onclick="finish_test();return false" style="display:none" class="disabled btn-finish btn-block btn-flat btn-danger btn"><i class="fa fa-check-circle"></i> SELESAI TES</a>
            </div>
            <div class="col-md-4 mb-10">
                <a href="javascript:;" class="btn-next btn-block btn btn-flat btn-primary">SOAL SELANJUTNYA <i class="fa fa-chevron-right"></i></a>
            </div>
            <div class="clearfix"></div>
        </div>

        <!-- MOBILE VER -->
        <div class="body-footer list-nomor-soal hidden-md hidden-lg mobilenomorsoal" id="nomor-soal">
            <?php
            $nomor = 1;
            foreach ($soal as $valSoal){
                $jawaban = '&nbsp;'; $btnclass = 'bg_white';
                if ($valSoal->jawab > 0){
                    $jawaban    = $this->conv->toStr($valSoal->jawab);
                    $btnclass   = 'bg_red';
                }
                echo '<a href="javascript:;" onclick="load_soal(this);return false" qs-id="'.$valSoal->qs_id.'" soal-id="'.$valSoal->soal_id.'" class="btn btn-default btn-flat soalke_'.$valSoal->soal_id.'" style="position:relative">
                        '.$nomor.'
                        <span class="'.$btnclass.' soaljawaban jawabanke_'.$valSoal->soal_id.'">'.$jawaban.'</span>
                      </a> ';
                $nomor++;
            }
            ?>
        </div>
        <div class="body-footer hidden-md hidden-lg fixed-footer" style="padding:0">
            <div class="col-xs-2" style="padding:0">
                <a href="javascript:;" class="btn-prev btn-block btn btn-flat btn-primary"><i class="fa fa-chevron-left"></i></a>
            </div>
            <div class="col-xs-2" style="padding:0">
                <a href="javascript:;" onclick="mobile_show_soal(this);return false" class="btn btn-block btn-flat btn-default"><i class="fa fa-chevron-up"></i></a>
            </div>
            <div class="col-xs-2" style="padding:0">
                <a href="javascript:;" onclick="decrease_text();return false" class="btn btn-block btn-flat btn-default"><small><i class="fa fa-font"></i></small></a>
            </div>
            <div class="col-xs-2" style="padding:0">
                <a href="javascript:;" onclick="default_text();return false" class="btn btn-block btn-flat btn-default"><i class="fa fa-font"></i></a>
            </div>
            <div class="col-xs-2" style="padding:0">
                <a href="javascript:;" onclick="increase_text();return false" class="btn btn-block btn-flat btn-default"><i style="font-size:16px" class="fa fa-font"></i></a>
            </div>
            <div class="col-xs-2" style="padding:0">
                <a href="javascript:" onclick="finish_test();return false" style="display:none" class="disabled btn-finish btn-block btn-flat btn-danger btn">SELESAI</a>
                <a href="javascript:;" style="margin-top:0" class="btn-next btn-block btn btn-flat btn-primary"><i class="fa fa-chevron-right"></i></a>
            </div>
            <div class="clearfix"></div>
        </div>
        <!-- MOBILE VER -->

    <div class="overlay ov1"><strong><i class="fa fa-spin fa-spinner"></i></strong></div>
    <!--<div class="overlay2 ov2"><span>Anda sedang menggunakan PAKET DATA.<br>MATIKAN PAKET DATA ANDA</span></div>-->
<script type="text/javascript"  src="<?php echo base_url('assets/bower_components/MathJax-master/MathJax.js?config=TeX-AMS_CHTML');?>"></script>
<script>
    MathJax.Hub.Config({
        tex2jax: {inlineMath: [['$','$'], ['\\(','\\)']]}
    });

    $('#nomor-soal').find('a').eq(0).addClass('btn-primary').removeClass('btn-default').trigger('click');

    function mobile_show_soal(ob) {
        if ($(ob).find('.fa-chevron-up').length > 0){
            $(ob).html('<i class="fa fa-chevron-down"></i>');
        } else {
            $(ob).html('<i class="fa fa-chevron-up"></i>');
        }
        $('.mobilenomorsoal').slideToggle(500);
    }
    function default_text() {
        $('.isisoalnya').css({'font-size':'14px'});
        $('.pg-content').css({'font-size':'14px'});
    }
    function decrease_text() {
        var font_size = $('.isisoalnya').css('font-size');
        font_size = parseInt(font_size);
        font_size--;
        $('.isisoalnya').css({'font-size':font_size+'px'});
        $('.pg-content').css({'font-size':font_size+'px'});
    }
    function increase_text() {
        var font_size = $('.isisoalnya').css('font-size');
        font_size = parseInt(font_size);
        font_size++;
        $('.isisoalnya').css({'font-size':font_size+'px'});
        $('.pg-content').css({'font-size':font_size+'px'});
    }
    function pad(num, size) {
        var s = num+"";
        while (s.length < size) s = "0" + s;
        return s;
    }
    var seconds = 1;
    var minutes = $('.timer-minute').eq(0).text();
    //minutes = parseInt(minutes);
    window.setInterval(function () {
        //console.log(minutes);
        seconds--;
        if (seconds == 0){
            minutes--;
            seconds = 59;
            $('.timer-minute').text(minutes);
            if (minutes == 0){
                finish_test_submit();
            }
            if (minutes <= 20){
                $('.btn-finish').removeClass('disabled');
            }
            //console.log(minutes);
        }
        $('.timer-seconds').text(pad(seconds,2));
    },1000);
    <?php if (isset($seslog)){ ?>
    $('.soalke_<?php echo $seslog->soal_id;?>').addClass('btn-primary').removeClass('btn-default').trigger('click');
    <?php } else { ?>
    $('#nomor-soal').find('a').eq(0).addClass('btn-primary').removeClass('btn-default').trigger('click');
    <?php } ?>

    $('.ov1,.ov2').hide();
    $('#nomor-soal').hide();
    function show_hide_nomor() {
        $('#nomor-soal').slideToggle(500);
        if ($('.btn-showhide').find('.fa-chevron-up').length > 0){
            $('.btn-showhide').html('<i class="fa fa-chevron-down"></i>');
        } else {
            $('.btn-showhide').html('<i class="fa fa-chevron-up"></i>')
        }
    }
    function set_jawab(ob) {
        if (!check_online()){
            $('.ov2').show();
        } else {
            var pg_id   = $(ob).attr('pg-id');
            var qspg_id = $(ob).attr('qspg-id');
            var qs_id   = $(ob).attr('qs-id');
            var soal_id = $(ob).attr('soal-id');
            $.ajax({
                url     : base_url + 'quiz/set_jawab',
                type    : 'POST',
                dataType: 'JSON',
                data    : { pg_id : pg_id, qspg_id : qspg_id, qs_id : qs_id, soal_id : soal_id },
                success : function (dt) {
                    if (dt.t == 0){
                        $('.body-content').html(dt.msg);
                    } else if (dt.t == 2) {
                        window.location.href = base_url + 'login';
                    } else {
                        $('.jawabanke_'+soal_id).removeClass('bg_white').addClass('bg_red').html(dt.jawaban);
                        $('.soalpg-wrapper').find('.btn-success').removeClass('btn-success').addClass('btn-default');
                        $('.pg_'+pg_id).addClass('btn-success').removeClass('btn-default');
                    }
                }
            });
        }
    }
    function load_soal(ob) {
        if (!check_online()){
            $('.ov2').show();
        } else {
            var soal_id     = $(ob).attr('soal-id');
            var qs_id       = $(ob).attr('qs-id');
            $('.overlay').show();
            $.ajax({
                url     : base_url + 'quiz/load_soal',
                type    : 'POST',
                dataType: 'JSON',
                data    : { soal_id : soal_id, qs_id : qs_id },
                success : function (dt) {
                    if (dt.t == 0){
                        $('.body-content').html(dt.msg);
                        $('.ov1').hide();
                    } else if (dt.t == 1) {
                        $('.ov1').hide();
                        $('.ov2').show();
                    } else if (dt.t == 2){
                        window.location.href = base_url + 'login';
                    } else {
                        $('.list-nomor-soal').find('.btn-primary').removeClass('btn-primary').addClass('btn-default');
                        $('.soalke_'+soal_id).removeClass('btn-default').addClass('btn-primary');
                        $('.timer-minute').html(dt.timer);
                        $('.ov1').hide();
                        $('.nomor-soal').html('# '+dt.nomor);
                        $('.body-content').html(dt.html);
                        MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
                        //prev btn
                        var prev = $('#nomor-soal').find('.btn-primary').prev('a').length;
                        if (prev == 0){ $('.btn-prev').hide(); } else { $('.btn-prev').show(); }
                        //next btn
                        var next = $('#nomor-soal').find('.btn-primary').next('a').length;
                        if (next == 0){ $('.btn-next').hide(); $('.btn-finish').show(); } else { $('.btn-next').show(); $('.btn-finish').hide(); }
                    }
                }
            });
        }
    }
    $('.btn-next').click(function () {
        var next = $('#nomor-soal').find('.btn-primary').next('a').length;
        if (next > 0){
            $('#nomor-soal').find('.btn-primary').next('a').trigger('click');
        }
    });
    $('.btn-prev').click(function () {
        var prev = $('#nomor-soal').find('.btn-primary').prev('a').length;
        if (prev > 0){
            $('#nomor-soal').find('.btn-primary').prev('a').trigger('click');
        }
    });
    function finish_test() {
        show_modal_finish();
    }
    function check_online() {
        $.support.cors = true;
        return 1;
    }
    function show_modal_finish() {
        $('#MyModal').modal({ backdrop: 'static', keyboard: false });
    }

    function finish_test_submit() {
        $('.modal-footer button').prop({'disabled':true});
        $.ajax({
            url     : base_url + 'quiz/finish_tes',
            type    : 'POST',
            dataType: 'JSON',
            success : function (dt) {
                if (dt.t == 0) {
                    show_msg(dt.msg, 'error');
                    $('.modal-footer button').prop({'disabled': false});
                } else if (dt.t == 1){
                    window.location.href = base_url + 'login';
                } else {
                    show_msg(dt.msg);
                    $('.modal-footer button').prop({'disabled':false});
                    window.location.href = base_url + 'quiz/finish_result'
                }
            }
        });
    }
    $(window).scroll(function () {
        var scrollTop  = $(window).scrollTop();
        if (scrollTop >= 100){
            $('.header').slideUp(500);
            $('.timer-header-inner').css({'width':'100%'});
        } else {
            $('.header').slideDown(500);
            $('.timer-header-inner').css({'width':'70px'});
        }
    })
</script>
    <div id="MyModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">SELESAI TES</h4>
                    </div>
                    <div class="modal-body text-center">
                        Anda yakin ingin menyelesaikan <span class="text-info">TES</span> ini ?<br>
                        Setelah anda klik tombol <span class="text-danger">SELESAI TES</span>, maka anda tidak dapat mengikuti <span class="text-info">TES</span> ini lagi.
                    </div>
                    <div class="modal-footer">
                        <div class="col-xs-5 col-sm-5 col-md-4 mb-10">
                            <button type="button" onclick="finish_test_submit();return false" class="btn btn-danger btn-block btn-flat">SELESAI TES</button>
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-4"></div>
                        <div class="col-xs-5 col-sm-5 col-md-4 mb-10">
                            <button type="button" class="btn btn-success btn-block btn-flat" onclick="hide_modal();return false">BATAL</button>
                        </div>
                    </div>
                </div><!-- /.modal-content -->

        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div id="ModalPic" class="modal fade" tabindex="-2" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <i onclick="$('#ModalPic').modal('hide');return false" class="fa fa-close pull-right"></i>
                    Gambar
                </div>
                <div class="modal-body">
                    <img src="" width="100%">
                </div>
            </div>
        </div>
    </div>
</body>
</html>
