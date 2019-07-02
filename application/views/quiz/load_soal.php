<?php
if ($soal){
    ?>
    <div style="margin:10px 10px 50px 10px" class="isisoalnya">
        <?php echo $soal->soal_content; ?>
    </div>
    <?php
    if ($pg){
        echo '<div class="soalpg-wrapper">';
        $nomorPG = 1;
        foreach ($pg as $valPG){
            $btn_succes = 'btn-default';
            if ($valPG->jawab == 1){ $btn_succes = 'btn-success'; }
            echo '<div class="pg-items">
                    <a href="javascript:;" pg-id="'.$valPG->pg_id.'" qspg-id="'.$valPG->qspg_id.'" qs-id="'.$soal->qs_id.'" soal-id="'.$soal->soal_id.'" onclick="set_jawab(this);return false" class="btn '.$btn_succes.' pg-items-option pg_'.$valPG->pg_id.'">'.$this->conv->toStr($nomorPG).'</a>                    
                    <div class="pg-content">
                        '.$valPG->pg_content.'
                    </div>
                    <div class="clearfix"></div>
                  </div>';
            $nomorPG++;
        }
        echo '</div>';
    }
}
?>
<script>
    $('.body-content img').click(function () {
        var url     = $(this).attr('src');
        $('#ModalPic .modal-body img').attr({'src':url});
        $('#ModalPic').modal('show');
    });
</script>
