<section class="content-header">
    <h1>
        SINGKONG BOLED
        <small></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo base_url('');?>" data-target="dashboard" onclick="load_page(this);return false"><i class="fa fa-home"></i> Beranda</a> </li>
        <li class="active">Singkong Boled</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <!-- Default box -->
    <div class="box">
        <div class="overlay"><div class="fa fa-spin fa-refresh"></div></div>
        <div class="box-header with-border">
            <h3 class="box-title">SYNC</h3>

            <div class="box-tools pull-right">

            </div>
        </div>
        <div class="box-body">
            <form id="syncForm">
                <table width="100%" class="table table-bordered">
                    <tr>
                        <td width="200px">STATUS REMOTE SERVER</td>
                        <td width="">
                            <?php
                            echo $remote;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>STATUS LOCAL SERVER</td>
                        <td class="local">
                            <?php
                            if ($server){
                                $disabled = 'disabled';
                                $nameH = 'sv_id'; $nameI = ''; $sv_id = $server->sv_id;
                                $server_name = $server->sv_name;
                                echo '<strong class="text-success">ONLINE</strong>';
                            } else {
                                $disabled = '';
                                $nameH = ''; $nameI = 'sv_id'; $sv_id = '';
                                $server_name = '';
                                echo '<strong class="text-danger">OFFLINE</strong>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>NAMA SERVER</td>
                        <td class="sv_name"><?php echo $server_name; ?></td>
                    </tr>
                    <tr>
                        <td>ID SERVER</td>
                        <td>
                            <input <?php echo $disabled;?> type="text" name="<?php echo $nameI;?>" id="sv_id" value="<?php echo $sv_id;?>" class="form-control" placeholder="ID SERVER">
                            <input type="hidden" name="<?php echo $nameH;?>" class="sv_id" value="<?php echo $sv_id;?>">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="right">
                            <a href="javascript:;" class="btn-delete btn btn-sm btn-danger btn-flat" onclick="delete_all();return false"><i class="fa fa-trash"></i> HAPUS DATA</a>
                            <button <?php echo $disabled;?> type="submit" class="btn-submit btn btn-sm btn-success btn-flat"><i class="fa fa-floppy-o"></i> SUBMIT</button>
                        </td>
                    </tr>
                </table>
            </form>
            <div class="syncdata panel panel-primary">
                <div class="panel-heading">
                    <a class="pull-right btn btn-xs btn-success btn-flat" onclick="load_table();return false"><i class="fa fa-refresh"></i> Reload</a>
                    <strong class="">SYNCHRON</strong>
                </div>
                <div class="panel-body no-padding">
                    <table id="tbSync" width="100%" class="table table-bordered">
                        <thead>
                        <tr>
                            <th width="">NAMA DATA</th>
                            <th width="120px">DATA LOKAL</th>
                            <th width="120px">DATA REMOTE</th>
                            <th width="100px">AKSI</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /.box -->

</section>

<script>
    $('.overlay').hide();
    <?php if ($server){ echo 'load_table();'; } else { echo '$(".syncdata").hide();'; } ?>
    function load_table() {
        $('.overlay').show();
        $.ajax({
            url     : base_url + 'sync/check_sync',
            type    : 'POST',
            dataType: 'JSON',
            data    : { },
            success : function (dt) {
                if (dt.t == 0){
                    $('#tbSync tbody').html('<tr><td colspan="4">'+dt.msg+'</td></tr>');
                } else {
                    $('#tbSync tbody').html(dt.html);
                }
                $('.overlay').hide();
            }
        })
    }
    function start_sync(ob){
        var what    = $(ob).attr('data');
        $.ajax({
            url     : base_url + 'sync/start_sync',
            type    : 'POST',
            dataType: 'JSON',
            data    : { what : what },
            success : function(dt){
                if (dt.t == 0){
                    show_msg(dt.msg,'error');
                } else {
                    var dtlen   = dt.data.length;
                    var eyoy    = 0;
                    $.each(dt.data,function (i,v) {
                        var persen = Math.round((i / dtlen) * 100);
                        if (dt.type == 1){ //insert peserta
                            $('.pwr_1').show();
                            $.ajax({
                                url     : base_url + 'sync/insert_pes',
                                type    : 'POST',
                                dataType: 'JSON',
                                data    : { data : dt.data[i] },
                                async   : false,
                                cache   : false,
                                success : function (dta) {
                                    $('.progress_'+dt.type).attr('aria-valuenow', persen).css('width', persen+'%').text(persen+'%');
                                    $('.lokal_'+dt.type).html(i + 1);
                                    if (i + 1 == dtlen){
                                        $('.progress_'+dt.type).attr('aria-valuenow', 100).css('width', 100+'%').text(100+'%');
                                        $('.btn-1').addClass('disabled').addClass('btn-success').removeClass('btn-default').html('<i class="fa fa-stop-circle"></i>');
                                        $('.btn-2').removeClass('disabled');
                                        $('.btn-2').trigger('click');
                                        $('.pwr_1').hide();
                                    }
                                    if (dta.t == 0){ eyoy++; if (eyoy >= 10){ return false; } }
                                }
                            });
                        } else if (dt.type == 2){//insert ruang dan member
                            $('.pwr_2').show();
                            $.ajax({
                                url     : base_url + 'sync/insert_ruang',
                                type    : 'POST',
                                data    : { data : dt.data[i] },
                                async   : false,
                                cache   : false,
                                dataType: 'JSON',
                                success : function (dta) {
                                    $('.progress_'+dt.type).attr('aria-valuenow', persen).css('width', persen+'%').text(persen+'%');
                                    $('.lokal_'+dt.type).html(i + 1);
                                    if (i + 1 == dtlen){
                                        $('.progress_'+dt.type).attr('aria-valuenow', 100).css('width', 100+'%').text(100+'%');
                                        $('.btn-2').addClass('disabled').addClass('btn-success').removeClass('btn-default').html('<i class="fa fa-stop-circle"></i>');
                                        $('.btn-3').removeClass('disabled');
                                        $('.btn-3').trigger('click');
                                        $('.pwr_2').hide();
                                    }
                                    if (dta.t == 0){ eyoy++; if (eyoy >= 10){ return false; } }
                                }
                            });
                        } else if (dt.type == 3){ //data QUIZ
                            $('.pwr_3').show();
                            $.ajax({
                                url     : base_url + 'sync/insert_quiz',
                                type    : 'POST',
                                data    : { data : dt.data[i] },
                                async   : false,
                                cache   : false,
                                dataType: 'JSON',
                                success : function (dta) {
                                    $('.progress_'+dt.type).attr('aria-valuenow', persen).css('width', persen+'%').text(persen+'%');
                                    $('.lokal_'+dt.type).html(i + 1);
                                    if (i + 1 == dtlen){
                                        $('.progress_'+dt.type).attr('aria-valuenow', 100).css('width', 100+'%').text(100+'%');
                                        $('.btn-3').addClass('disabled').addClass('btn-success').removeClass('btn-default').html('<i class="fa fa-stop-circle"></i>');
                                        $('.btn-4').removeClass('disabled');
                                        $('.btn-4').trigger('click');
                                        $('.pwr_3').hide();
                                    }
                                    if (dta.t == 0){ eyoy++; if (eyoy >= 10){ return false; } }
                                }
                            });
                        } else if (dt.type == 4){ //data MAPEL
                            $('.pwr_4').show();
                            $.ajax({
                                url     : base_url + 'sync/insert_mapel',
                                type    : 'POST',
                                data    : { data : dt.data[i] },
                                async   : false,
                                cache   : false,
                                dataType: 'JSON',
                                success : function (dta) {
                                    $('.progress_'+dt.type).attr('aria-valuenow', persen).css('width', persen+'%').text(persen+'%');
                                    $('.lokal_'+dt.type).html(i + 1);
                                    if (i + 1 == dtlen){
                                        $('.progress_'+dt.type).attr('aria-valuenow', 100).css('width', 100+'%').text(100+'%');
                                        $('.btn-4').addClass('disabled').addClass('btn-success').removeClass('btn-default').html('<i class="fa fa-stop-circle"></i>');
                                        $('.btn-5').removeClass('disabled');
                                        $('.btn-5').trigger('click');
                                        //$('.remote_6').html(dta.remote);
                                        $('.btn-6').addClass('btn-default').removeClass('btn-success').html('<i class="fa fa-play-circle"></i>');
                                        $('.pwr_4').hide();
                                    }
                                    if (dta.t == 0){ eyoy++; if (eyoy >= 10){ return false; } }
                                }
                            });
                        } else if (dt.type == 5){ //data SOAL
                            $('.pwr_5').show();
                            $.ajax({
                                url     : base_url + 'sync/insert_soal',
                                type    : 'POST',
                                data    : { data : dt.data[i] },
                                async   : false,
                                cache   : false,
                                dataType: 'JSON',
                                success : function (dta) {
                                    $('.progress_'+dt.type).attr('aria-valuenow', persen).css('width', persen+'%').text(persen+'%');
                                    $('.lokal_'+dt.type).html(i + 1);
                                    if (i + 1 == dtlen){
                                        $('.progress_'+dt.type).attr('aria-valuenow', 100).css('width', 100+'%').text(100+'%');
                                        $('.btn-5').addClass('disabled').addClass('btn-success').removeClass('btn-default').html('<i class="fa fa-stop-circle"></i>');
                                        $('.btn-6').removeClass('disabled');
                                        $('.btn-6').trigger('click');
                                        $('.pwr_5').hide();
                                    }
                                    if (dta.t == 0){ eyoy++; if (eyoy >= 10){ return false; } }
                                }
                            });
                        } else if (dt.type == 6){ //data QUIZ SOAL
                            $('.pwr_6').show();
                            $.ajax({
                                url     : base_url + 'sync/insert_soal_tes',
                                type    : 'POST',
                                data    : { data : dt.data[i], sv_id : dt.sv_id },
                                async   : false,
                                cache   : false,
                                dataType: 'JSON',
                                success : function (dta) {
                                    $('.progress_'+dt.type).attr('aria-valuenow', persen).css('width', persen+'%').text(persen+'%');
                                    $('.lokal_'+dt.type).html(i + 1);
                                    if (i + 1 == dtlen){
                                        $('.progress_'+dt.type).attr('aria-valuenow', 100).css('width', 100+'%').text(100+'%');
                                        $('.btn-6').addClass('disabled').addClass('btn-success').removeClass('btn-default').html('<i class="fa fa-stop-circle"></i>');
                                        $('.btn-7').trigger('click');
                                        $('.pwr_6').hide();
                                    }
                                    if (dta.t == 0){ eyoy++; if (eyoy >= 10){ return false; } }
                                }
                            });
                        } else if (dt.type == 7){
                            $('.pwr_7').show();
                            $.ajax({
                                url     : base_url + 'sync/insert_media',
                                type    : 'POST',
                                dataType: 'JSON',
                                async   : false,
                                cache   : false,
                                data    : { data : dt.data[i], sv_id : dt.sv_id },
                                success : function (dta) {
                                    $('.progress_'+dt.type).attr('aria-valuenow', persen).css('width', persen+'%').text(persen+'%');
                                    $('.lokal_'+dt.type).html(i + 1);
                                    if (i + 1 == dtlen){
                                        $('.progress_'+dt.type).attr('aria-valuenow', 100).css('width', 100+'%').text(100+'%');
                                        $('.btn-7').addClass('disabled').addClass('btn-success').removeClass('btn-default').html('<i class="fa fa-stop-circle"></i>');
                                        show_msg('Synchron Berhasil. Mohon lihat kesamaan data kiri dan kanan agar lebih pasti.');
                                        $('.pwr_7').hide();
                                    }
                                    if (dta.t == 0){ eyoy++; if (eyoy >= 10){ return false; } }
                                }
                            })
                        }
                    })
                }
            }
        })
    }
    function delete_all() {
        var konfir      = confirm('Anda yakin ingin menghapus SELURUH DATA yang ada di SERVER ini?');
        if (konfir){
            $('.btn-delete').addClass('disabled').html('<i class="fa fa-spin fa-refresh"></i> HAPUS DATA');
            $.ajax({
                url     : base_url + 'sync/delete_all',
                type    : 'POST',
                dataType: 'JSON',
                data    : {},
                success : function (dt) {
                    if (dt.t == 0){
                        $('.btn-delete').removeClass('disabled').html('<i class="fa fa-trash"></i> HAPUS DATA');
                        show_msg(dt.msg,'error');
                    } else {
                        $('.btn-delete').removeClass('disabled').html('<i class="fa fa-trash"></i> HAPUS DATA');
                        $('.btn-submit').prop({'disabled':false});
                        $('.local').html('<strong class="text-danger">OFFLINE</strong>');
                        $('#sv_id').attr({'name':'sv_id'}).prop({'disabled':false}).val('');
                        $('.sv_id').attr({'name':''}).val('');
                        $('.sv_name').html('');
                        $('.syncdata').hide();
                    }
                }
            })
        }
    }
    $('#syncForm').submit(function () {
        var konfirm     = confirm('Apakah ID SERVER ini benar ?');
        var sv_id       = $('#sv_id').val();
        if (sv_id.length == 0){
            show_msg('Masukkan ID SERVER','error');
        } else if (konfirm){
            $('#syncForm .btn-submit').prop({'disabled':true}).html('<i class="fa fa-spin fa-refresh"></i> SUBMIT');
            $.ajax({
                url     : base_url + 'sync/submit_server',
                type    : 'POST',
                dataType: 'JSON',
                data    : { sv_id : sv_id },
                success : function (dt) {
                    if (dt.t == 0){
                        $('#syncForm .btn-submit').prop({'disabled':false}).html('<i class="fa fa-floppy-o"></i> SUBMIT');
                        show_msg(dt.msg,'error');
                    } else {
                        $('#syncForm .btn-submit').prop({'disabled':true}).html('<i class="fa fa-floppy-o"></i> SUBMIT');
                        $('.sv_name').html(dt.sv_name);
                        $('.local').html('<strong class="text-success">ONLINE</strong>');
                        $('#sv_id').attr({'name':''}).prop({'disabled':true});
                        $('.sv_id').attr({'name':'sv_id'}).val(dt.sv_id);
                        $('.syncdata').show();
                        load_table();
                    }
                }
            })
        }
        return false;
    })
</script>