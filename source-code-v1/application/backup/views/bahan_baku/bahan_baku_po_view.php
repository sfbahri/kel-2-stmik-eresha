<script type="text/javascript">
$(document).ready(function(){
    
    var tokens   = '<?php echo $this->session->userdata('sess_token');?>';
    
    var data_produksi = function(){
        $('#tabel_po_bahan_baku').hide();
        $.ajax({ 
            url: base_url + 'bahan_baku/bahan_baku_po_data',
            type: "post",
            data:{<?php echo $this->security->get_csrf_token_name(); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
            dataType: "json",
            async : 'false',
            success: function(result)
            {
                var data = [];
                for ( var i=0 ; i<result.length ; i++ ) {
                    var no = i+1;
                    var link_edit   = "<a href='javascript:void(0)' onclick=\"getpopup('bahan_baku/bahan_baku_po_detail','"+tokens+"','popupedit','"+result[i].kode+"');\" data-placement='top' data-toggle='tooltip' data-original-title='Tooltip on top'><div class='btn btn-info btn-sm' title='Detail Bahan Baku' ><i class='fa fa-eye'></i></div></a>";
                    var nopo = result[i].po_tahun+''+result[i].po_bulan;
                    
                    var status;
                    if(result[i].status == 1){
                        status = '<span style="color:red">ORDER</span>';
                    }else if(result[i].status == 2){
                        status = '<span style="color:green">COMPLETE</span>';
                    }else{
                        status = '-';
                    }
                    
                    var statuss;
                    if(result[i].status == 1){
                        statuss = "<span class='btn btn-danger btn-sm fa fa-save' onclick=ubah_status_order("+result[i].kode+") title='Terima Bahan Baku Ini'></span>"; 
                    }else{
                        statuss = "<span class='btn btn-primary btn-sm fa fa-check'></span>";
                    }
                    
                    
                    data.push([nopo,result[i].pobulan,result[i].po_tahun,status,link_edit,"<a href='javascript:void(0)' onclick=\"getpopup('bahan_baku/finance_detail','"+tokens+"','popupedit','"+result[i].kode+"');\" data-placement='top' data-toggle='tooltip' data-original-title='Tooltip on top'><div class='btn btn-success btn-sm' title='Input Harga Bahan Baku' ><i class='fa fa-money'></i></div></a>",statuss]);
                }
                $('#tabel_po_bahan_baku').DataTable({
                    //"bJQueryUI"     : true,
                    data                : data,
                    deferRender         : true,
                    processing          : true,
                    ordering            : true,
                    retrieve            : false,
                    paging              : true,
                    deferLoading        : 57,
                    bDestroy            : true,
                    autoWidth           : false,
                    bFilter             : true,
                    iDisplayLength      : 10,
                    responsive: true,
                    language: {
                      searchPlaceholder: 'Cari',
                      sSearch: '',
                      lengthMenu: '_MENU_',
                    },
                });
               
            },
            beforeSend: function () {

            },
            complete: function () {
                $('#tabel_po_bahan_baku').show();
            }
        });
    }
    data_produksi();
    
    
    ubah_status_order = function(kode_bahan_baku){
    
        swal({
            title: "Order Bahan Baku Sudah Complete ?",
            text: "Jika ingin disimpan, silahkan klik button simpan",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonText: "Simpan",
            //confirmButtonColor: "#E73D4A"
            confirmButtonColor: "#286090"
        },
        function(){

            $.ajax({
                url       : base_url + 'bahan_baku/update_status_order',
                type      : "post",
                dataType  : 'json',
                data      : {kode_bahan_baku  :kode_bahan_baku,
                            <?php echo $this->security->get_csrf_token_name(); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'
                        },
                success: function (response) {
                if(response == true){ 
                        swal.close();
                        Command: toastr["success"]("Perubahan Status Bahan Baku berhasil disimpan", "Berhasil");
                        data_produksi();
                }else{
                    Command: toastr["error"]("Simpan error, data tidak berhasil disimpan", "Error");
                } 
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Command: toastr["error"]("Ajax Error !!", "Error");
            }

            });

        });
        
    
    }
    
    $("#btn_simpan").click(function(){
    
        if($("#tanggal").val() == ''){
            $("#tanggal").focus();
            Command: toastr["error"]("Silahkan isi Tanggal Sampai !", "Error !");
        }else{
    
        $.ajax({
            url       : base_url + 'bahan_baku/cek_order_po',
            type      : "post",
            dataType  : 'json',
            data      : {po_bulan : $("#po_bulan").val(),
                         po_tahun : $("#po_tahun").val(),
                        <?php echo $this->security->get_csrf_token_name(); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'
                    },
            success: function (response) {

                if(response == 1){  
                        Command: toastr["error"]("PO Bulan Ini Sudah Diorder !", "Error");
                }else{

                    swal({
                        title: "Order PO Bahan Baku ?",
                        text: "Jika ingin disimpan, silahkan klik button simpan",
                        type: "info",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true,
                        confirmButtonText: "Simpan",
                        //confirmButtonColor: "#E73D4A"
                        confirmButtonColor: "#286090"
                    },
                    function(){

                        $.ajax({
                            url       : base_url + 'bahan_baku/bahan_baku_po_simpan',
                            type      : "post",
                            dataType  : 'json',
                            data      : {kode    : $("#kode").val(),
                                        po_bulan : $("#po_bulan").val(),
                                        po_tahun : $("#po_tahun").val(),
                                        <?php echo $this->security->get_csrf_token_name(); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'
                                    },
                            success: function (response) {
                            if(response == true){  
                                    swal.close();
                                    Command: toastr["success"]("PO Bahan Baku berhasil disimpan", "Berhasil");
                                    getcontents('bahan_baku/bahan_baku_po','<?php echo $tokens;?>');
                            }else{
                                Command: toastr["error"]("Simpan error, data tidak berhasil disimpan", "Error");
                            } 
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            Command: toastr["error"]("Ajax Error !!", "Error");
                        }

                        });

                    });

                } 
        },
        error: function(jqXHR, textStatus, errorThrown) {
            Command: toastr["error"]("Ajax Error !!", "Error");
        }

        });
        
        
        }
    
        
    });
    
   //$("#po_bulan").chosen({width: "100%"});
});    
</script>
    
    <?php 
        $token = "";
        $codeAlphabet = "33434343556789934343434567812345667980909";
        $codeAlphabet.= "54979319491320389885589989898989867733333";
        $codeAlphabet.= "65987111444779789865326549845123248498565";
        $codeAlphabet.= "55555889897713687985198713498498165498987";
        $codeAlphabet.= "0123456789";
        $codeAlphabet.= "9876543210";

        $max = strlen($codeAlphabet) - 1;
        for ($i=0; $i < 5; $i++) {
            $token .= $codeAlphabet[mt_rand(0, $max)];
        } 

        $today = date("Ymd");
        $kode_produksi = $token.$today;
    ?>


    <div class="br-pageheader">
        <nav class="breadcrumb pd-0 mg-0 tx-12">
          <a class="breadcrumb-item" href="javascript:void(0)"><i class="icon fa fa-home"></i> Home</a>
          <span class="breadcrumb-item active">Data Bahan Baku</span>
        </nav>
        
    </div><!-- br-pageheader -->
    <div class="br-pagetitle">
        <i class="icon icon ion-ios-create"></i>
        <div>
          <h4>PO Bahan Baku</h4>
          <p class="mg-b-0">Halaman data bahan baku.</p>
        </div>
        <div class="">
                
        </div>
    </div>
    
      
        <!-- start content -->
    <div class="br-pagebody">
        <div class="br-section-wrapper">
            <div class="row">
            <div class="col-md-4">
                
                <div class="row">
                    <div class="col-md-12">
                    <div class="card">
                    <div class="card-header tx-medium bd-0 tx-white bg-info">Form Order Bahan Baku</div>
                    <div class="card-body">
                        <div class="table-responsive">
                        <div class="row" style="margin:5px">
                            <div class="col-md-12">
                            
                            <div class="form-group" hidden="">
                                <label for="demo-vs-definput" class="control-label">Kode Produksi (#)</label>
                                <input type="text" id="kode" name="kode" class="form-control" readonly="" value="<?php echo $kode_produksi;?>">
                            </div>
                            <div class="form-group">
                                <label for="timesheetinput3"><i class="fa fa-calendar"></i> PO Bulan</label>
                                <div class="position-relative has-icon-left">
                                    <select name="po_bulan" id="po_bulan" class="form-control">
                                    <option value="0">-Pilih Bulan-</option>
                                    <?php
                                    $bulan=array("Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
                                    $jlh_bln=count($bulan);
                                    for($c=0; $c<$jlh_bln; $c+=1){
                                            $no = $c+1; 
                                            $num_padded = sprintf("%02d", $no);
                                            echo"<option value='$num_padded'> $bulan[$c] </option>";
                                    }
                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="timesheetinput3"><i class="fa fa-calendar"></i> PO Tahun</label>
                                <div class="position-relative has-icon-left">
                                    <input type="text" class="form-control" id="po_tahun" name="po_tahun" value="<?php echo date('Y');?>">
                                </div>
                            </div>
                            <hr>
                            <button type="button" class="btn btn-info btn-min-width mr-1 mb-1" id="btn_simpan"><i class="ft-save"></i> Order </button>
                            </div> 
                        </div>
                        </div>
                    </div>
                </div>
                </div>
                </div>
                
            </div>
            
            
            <div class="col-md-8">
                <div class="card">
                <div class="card-header tx-medium bd-0 tx-white bg-info">Data PO Bahan Baku</div>
                <div class="card-body card-dashboard">
                    
                    <table id="tabel_po_bahan_baku" class="table table-striped table-bordered table-responsive" style="width: 100%">
                            <thead>
                                <tr>
                                    <th style="width: 10%">No</th>
                                    <th style="width: 10%">PO Bulan</th>
                                    <th style="width: 10%">PO Tahun</th>
                                    <th style="width: 10%">Status</th>
                                    <th style="width: 10%">Detail</th>
                                    <th style="width: 10%">Harga</th>
                                    <th style="width: 10%">Terima</th>
                                </tr>
                                </thead>
                            </table> 
                    
                    
                </div>
                </div>
                </div>
            </div>
            </div>
            
        </div>
    </div>


            