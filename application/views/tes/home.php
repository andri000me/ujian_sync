<section class="content-header">
    <h1>
        STATUS TES
        <small></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo base_url('');?>" data-target="dashboard" onclick="load_page(this);return false"><i class="fa fa-home"></i> Beranda</a> </li>
        <li class="active">Status Tes</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">STATUS TES</h3>

            <div class="box-tools pull-right">
                <button onclick="$('.serverwrapper').slideToggle(500);return false" type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body serverwrapper">
            <form id="formnya">
                <table width="100%" class="table table-bordered">
                    <tr>
                        <td width="200px">STATUS REMOTE SERVER</td>
                        <td width="">
                            <?php
                            if ($online){
                                echo '<strong class="text-success">ONLINE</strong>';
                            } else {
                                echo '<strong class="text-danger">OFFLINE</strong>';
                            }
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
                        <td>STATUS TES</td>
                        <td class="quizstatus"></td>
                    </tr>
                    <tr>
                        <td>NAMA TES</td>
                        <td>
                            <select name="quiz_id" id="quiz_id" style="width:100%" class="form-control" onchange="quiz_selected();">
                                <option value="">== PILIH TES ==</option>
                                <?php
                                if ($quiz){
                                    foreach ($quiz as $val){
                                        echo '<option value="'.$val->quiz_id.'">'.$val->quiz_name.' - '.date('d F Y, H:i',strtotime($val->quiz_date)).'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>MATA PELAJARAN</td>
                        <td>
                            <ol class="mapel">

                            </ol>
                        </td>
                    </tr>
                    <tr>
                        <td>TOKEN</td>
                        <td class="tokennya text-danger" style="font-weight:bold;font-size:20px">
                            <?php if (isset($token)){ echo $token; } ?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <button style="margin-top:5px" type="submit" onclick="$('#formnya').submit();return false" class="btn-submit btn btn-success btn-flat"><i class="fa fa-floppy-o"></i> SUBMIT DAN BUKA TES</button>
                            <button style="margin-top:5px" type="button" class="btn-close btn btn-flat btn-danger" onclick="tutup_tes();return false"><i class="fa fa-close"></i> SUBMIT DAN TUTUP TES</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <!-- /.box -->
    <div class="box pesertawrapper">
        <div class="box-header with-border">
            <h3 class="box-title">STATUS PESERTA</h3>

            <div class="box-tools pull-right">
                <button type="button" onclick="$('.pestable').slideToggle(500);return false" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body table-responsive no-padding pestable">
            <div class="col-md-12" style="margin:5px 0">
                <a style="margin-top:5px" href="" onclick="load_table();return false" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-refresh"></i> Refresh</a>
                <a style="margin-top:5px" href="" onclick="upload_peserta();return false" class="btn-delete disabled btn btn-success btn-sm btn-flat"><i class="fa fa-cloud-upload"></i> UPLOAD HASIL</a>
                <a style="margin-top:5px" href="" onclick="reset_login();return false" class="btn-delete disabled btn btn-danger btn-sm btn-flat"><i class="fa fa-recycle"></i> RESET LOGIN</a>
                <a style="margin-top:5px" href="" onclick="force_finish();return false" class="btn-delete disabled btn btn-warning btn-sm btn-flat"><i class="fa fa-sign-out"></i> PAKSA SELESAI</a>
                <div class="clearfix"></div>
                <div class="col-md-4" style="margin:5px 0;padding:0 5px 0 0" onchange="load_table()">
                    <select id="ruang_id" style="width:100%" class="form-control">
                        <option value="">== Ruang == </option>
                        <?php
                        if($ruang){
                            foreach ($ruang as $val){
                                echo '<option value="'.$val->ruang_id.'">'.$val->ruang_name.'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-8 no-padding" style="margin:5px 0">
                    <input class="form-control keyword" onkeyup="load_table()">
                </div>
            </div>
            <div style="margin:20px">
                <span style="display:inline-block;width:100px">Login</span>: <strong style="display:inline-block;width:100px" class="jmlLogin">0</strong><br>
                <span style="display:inline-block;width:100px">Mengerjakan</span>: <strong style="display:inline-block;width:100px" class="jmlKerja">0</strong><br>
                <span style="display:inline-block;width:100px">Selesai</span>: <strong style="display:inline-block;width:100px" class="jmlSelesai">0</strong><br>
                <span style="display:inline-block;width:100px">Total Peserta</span>: <strong style="display:inline-block;width:100px" class="jmlAll">0</strong><br>
                <span style="display:inline-block;width:100px">Terupload</span>: <strong style="display:inline-block;width:100px" class="jmlupload">0</strong>
            </div>
            <form id="formTbPes">
                <input type="hidden" name="quiz_id" id="tbquiz_id" value="<?php if ($this->session->userdata('quiz_id')){ echo $this->session->userdata('quiz_id'); } ?>">
                <table id="tbPes" width="100%" class="table table-bordered">
                    <thead>
                    <tr>
                        <th width="30px"><input type="checkbox" onclick="cbxall(this)"></th>
                        <th width="150px">Nomor Peserta</th>
                        <th width="">Nama Peserta</th>
                        <th width="150px">Status Login</th>
                        <th width="100px">Sisa Waktu</th>
                        <th width="50px">Jml Soal</th>
                        <th width="50px">Diker jakan</th>
                        <th width="100px">Status Upload</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </form>
        </div>
        <div class="overlay"><i class="fa fa-refresh fa-spin"></i> </div>
    </div>
</section>
<div id="MyModal2" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">UPLOAD HASIL</h4>
            </div>
            <div class="modal-body text-center">
                <div class="progress">
                    <div id="progressBar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                        0%
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-warning btn-flat" onclick="$('#MyModal2').modal('hide');return false">TUTUP</a>
            </div>
        </div><!-- /.modal-content -->

    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
    window.setInterval(function () {
        if ($('.pesertawrapper').is(':hidden')){
        } else {
            var dtlen = $('#tbPes tbody input:checkbox:checked').length;
            if (dtlen == 0){
                load_table();
            }
        }
    },60000);
    $('.pesertawrapper').hide();
    <?php
    if ($this->session->userdata('quiz_id')){
        echo '$("#quiz_id").val('.$this->session->userdata('quiz_id').');
              quiz_selected();
              $(".pesertawrapper").show();
              load_table();';
    }
    ?>
    //$('#quiz_id,#ruang_id').select2();
    function upload_peserta() {
        var dtlen   = $('#tbPes tbody input:checkbox:checked').length;
        var konfir  = confirm('Upload hasil peserta ke SERVER UTAMA ?');
        if (dtlen == 0){
            show_msg('Pilih peserta lebih dulu','error');
        } else if (konfir) {
            $('#MyModal2 .modal-footer .btn').addClass('disabled');
            $('#progressBar').attr({'aria-valuenow':0}).css({'width':'0%'}).html('0%');
            $('#MyModal2').modal({ backdrop: 'static', keyboard: false });
            var datanya     = $('#tbPes tbody input:checkbox:checked');
            var total       = datanya.length;
            $.each(datanya,function (i,v) {
                $.ajax({
                    url     : base_url + 'tes/upload_proses',
                    type    : 'POST',
                    dataType: 'JSON',
                    async   : false,
                    cache   : false,
                    data    : { ses_id : datanya[i].value },
                    success : function (dt) {
                        var persen = Math.round((i / total) * 100);
                        $('#progressBar').attr({'aria-valuenow':persen}).css({'width':persen+'%'}).html(persen+'%');
                        if ( i + 1 >= total){
                            $('#progressBar').attr({'aria-valuenow':100}).css({'width':'100%'}).html('100%');
                            $('#MyModal2 .modal-footer .btn').removeClass('disabled');
                            load_table();
                        }
                    }
                });
            })
        }
    }
    function force_finish() {
        var dtlen   = $('#tbPes tbody input:checkbox:checked').length;
        var konfir  = confirm('Anda yakin ingin memaksa peserta ini untuk SELESAI TES ?\nPeserta akan langsung LOGOUT setelah anda klik tombol OK');
        if (dtlen == 0){
            show_msg('Pilih peserta lebih dulu','error');
        } else if (konfir) {
            $.ajax({
                url     : base_url + 'tes/force_finish',
                type    : 'POST',
                dataType: 'JSON',
                data    : $('#formTbPes').serialize(),
                success : function (dt) {
                    if (dt.t == 0){
                        show_msg(dt.msg,'error');
                        load_table();
                    } else {
                        show_msg(dt.msg);
                        load_table();
                    }
                }
            });
        }
    }
    function reset_login() {
        var dtlen   = $('#tbPes tbody input:checkbox:checked').length;
        var konfir  = confirm('Anda yakin ingin mereset peserta ini ?');
        if (dtlen == 0){
            show_msg('Pilih peserta lebih dulu','error');
        } else if (konfir){
            $.ajax({
                url     : base_url + 'tes/reset_login',
                type    : 'POST',
                dataType: 'JSON',
                data    : $('#formTbPes').serialize(),
                success : function (dt) {
                    if (dt.t == 0){
                        show_msg(dt.msg,'error');
                        load_table();
                    } else {
                        show_msg(dt.msg);
                        load_table();
                    }
                }
            })
        }
    }
    function cbxall(ob) {
        if ($(ob).prop('checked') == false){
            $('#tbPes input:checkbox').prop({'checked':false});
            $('.btn-delete').addClass('disabled');
        } else {
            $('#tbPes input:checkbox').prop({'checked':true});
            $('.btn-delete').removeClass('disabled');
        }
    }
    function load_table() {
        var keyword     = $('.keyword').val();
        var quiz_id     = $('#quiz_id').val();
        var ruang_id    = $('#ruang_id').val();
        $('.overlay').show();
        $.ajax({
            url     : base_url + 'tes/status_peserta',
            type    : 'POST',
            dataType: 'JSON',
            data    : { quiz_id : quiz_id, keyword : keyword, ruang_id : ruang_id },
            success : function (dt) {
                $('.jmlLogin').html(dt.login);
                $('.jmlKerja').html(dt.kerja);
                $('.jmlSelesai').html(dt.selesai);
                $('.jmlAll').html(dt.jmlAll);
                $('.jmlupload').html(dt.uploaded);
                if (dt.t == 0){
                    $('.overlay').hide();
                    $('#tbPes tbody').html('<tr><td colspan="6">'+dt.msg+'</td></tr>');
                    $('#tbPes thead input:checkbox').prop({'checked':false});
                    $('.btn-delete').addClass('disabled');
                } else {
                    $('#tbquiz_id').val(quiz_id);
                    $('#tbPes tbody').html(dt.html);
                    $('.overlay').hide();
                    $('#tbPes thead input:checkbox').prop({'checked':false});
                    $('.btn-delete').addClass('disabled');
                }
            }
        })
    }
    function tutup_tes() {
        var quiz_id     = $('#quiz_id').val();
        var konfirm     = confirm('Anda yakin ingin menutup TES ini?\nPeserta yang sedang mengerjakan akan DIPAKSA LOGOUT');
        if (!quiz_id){
            show_msg('Pilih DATA TES','error');
        } else if (konfirm){
            $('#formnya .btn-close').addClass('disabled').html('<i class="fa fa-spin fa-refresh"></i> SUBMIT DAN TUTUP TES');
            $.ajax({
                url     : base_url + 'tes/quiz_tutup',
                type    : 'POST',
                dataType: 'JSON',
                data    : { quiz_id : quiz_id },
                success : function (dt) {
                    if (dt.t == 0){
                        $('#formnya .btn-close').removeClass('disabled').html('<i class="fa fa-close"></i> SUBMIT DAN TUTUP TES');
                        show_msg(dt.msg,'error');
                        $('.pesertawrapper').hide();
                    } else {
                        $('#formnya .btn-close').removeClass('disabled').html('<i class="fa fa-close"></i> SUBMIT DAN TUTUP TES');
                        show_msg(dt.msg);
                        $('.pesertawrapper').hide();
                        $('.tokennya').html('');
                        $('.quizstatus').html(dt.active);
                    }
                }
            });
        }
    }
    $('#formnya').submit(function () {
        var konfirm = confirm('Anda yakin ingin merubah data TES yang aktif?\nPastikan tidak ada peserta yang sedang LOGIN');
        if (konfirm){
            $('#formnya .btn-submit').addClass('disabled').html('<i class="fa fa-spin fa-refresh"></i> SUBMIT DAN BUKA TES');
            $.ajax({
                url     : base_url + 'tes/quiz_set_active',
                type    : 'POST',
                dataType: 'JSON',
                data    : $(this).serialize(),
                success : function (dt) {
                    if (dt.t == 0){
                        $('#formnya .btn-submit').removeClass('disabled').html('<i class="fa fa-floppy-o"></i> SUBMIT DAN BUKA TES');
                        show_msg(dt.msg,'error');
                        $('.pesertawrapper').hide();
                    } else {
                        $('#formnya .btn-submit').removeClass('disabled').html('<i class="fa fa-floppy-o"></i> SUBMIT DAN BUKA TES');
                        show_msg(dt.msg);
                        $('.tokennya').html(dt.token);
                        $('.quizstatus').html(dt.active);
                        $('.pesertawrapper').show();
                        load_table();
                    }
                }
            });
        }
        return false;
    });
    function quiz_selected() {
        var quiz_id     = $('#quiz_id').val();
        $.ajax({
            url     : base_url + 'tes/quiz_selected',
            type    : 'POST',
            dataType: 'JSON',
            data    : { quiz_id : quiz_id },
            success : function (dt) {
                if (dt.t == 0){
                    $('.mapel').html('<li>'+dt.msg+'</li>');
                    $('.pesertawrapper').hide();
                    $('.quizstatus,.tokennya').html('');
                } else {
                    if (dt.status == 1){
                        $('.pesertawrapper').show();
                    } else {
                        $('.pesertawrapper').hide();
                    }
                    $('.mapel').html('');
                    $.each(dt.data,function (i,v) {
                        $('.mapel').append('<li>'+v.mapel_name+'</li>');
                    });
                    $('.quizstatus').html(dt.active);
                    $('.tokennya').html(dt.token);
                }
            }
        });
    }

</script>