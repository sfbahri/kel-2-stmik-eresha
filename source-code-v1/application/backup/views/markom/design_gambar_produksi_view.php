<script type="text/javascript">
$(document).ready(function(){
    
    var tokens   = '<?php echo $this->session->userdata('sess_token');?>';
    
    var data_gambar_produksi = function(){
        $('#tabel_sablon').hide();
        $.ajax({ 
            url: base_url + 'markom/design_gambar_data_so',
            type: "post",
            data:{<?php echo $this->security->get_csrf_token_name(); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},
            dataType: "json",
            async : 'false',
            success: function(result)
            {
                var data = [];
                for ( var i=0 ; i<result.length ; i++ ) {
                   
                   var no = i+1;
                 
                    var link_edit = "<a href='javascript:void(0)' onclick=\"getpopup('markom/design_gambar_produksi_edit','"+tokens+"','popupedit','"+result[i].noso+"');\"><div class='btn btn-primary btn-sm' title='Edit No & Nama SO' ><i class='fa fa-pencil'></i></div></a>";    
                    var link_lihat_gambar = "<a href='javascript:void(0)' onclick=\"getcontents('markom/design_gambar_produksi_upload','"+tokens+"','"+result[i].noso+"');\"><div class='btn btn-info btn-sm' title='Lihat Data Gambar' ><i class='fa fa-file-image-o'></i></div></a>";
                   
                    var status_bahanbaku;
                    if(result[i].tanggal_terima_produksi == null){
                        status_bahanbaku = '<span style="color:red">Produksi Belum Terima Bahan Baku</span>';
                    }else{
                        status_bahanbaku = '<span style="color:green">Produksi Sudah Terima Bahan Baku</span>';
                    }
                   
                    data.push([no,result[i].noso,result[i].nama,status_bahanbaku,link_edit,link_lihat_gambar]);
                   
                }
                $('#tabel_sablon').DataTable({
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
                $('#tabel_sablon').show();
            }
        });
    }
    data_gambar_produksi();
    
});    
</script>
    
    <div class="br-pageheader">
        <nav class="breadcrumb pd-0 mg-0 tx-12">
          <a class="breadcrumb-item" href="javascript:void(0)"><i class="icon fa fa-home"></i> Home</a>
          <span class="breadcrumb-item active">Data Gambar Produksi</span>
        </nav>
    </div><!-- br-pageheader -->
    <div class="br-pagetitle">
        <i class="icon icon ion-ios-analytics"></i>
        <div>
          <h4>Draft Gambar Produksi</h4>
          <p class="mg-b-0">Halaman data gambar produksi.</p>
        </div>
    </div><!-- d-flex -->

      
    <!-- start content -->
    <div class="br-pagebody">
        <div class="br-section-wrapper">
            <h6 class="br-section-label">Tabel Data Gambar By NO. SO</h6>
            <p class="br-section-text"><button class="btn btn-info btn-sm" onclick="getpopup('markom/design_gambar_produksi_tambah','<?php echo $this->session->userdata('sess_token');?>','popup_tambah')"><i class="fa fa-plus-circle"></i> Tambah NO. SO</button></p>

            <div class="table-wrapper table-responsive">
            <table id="tabel_sablon" class="table  display nowrap">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kategori Gambar (By NO. SO)</th>
                        <th>Nama Tema</th>
                        <th>Status</th>
                        <th>Edit</th>
                        <th>Lihat Gambar</th>
                    </tr>
                </thead>
            </table> 
            </div>
            
        </div>
    </div>
        <!-- end content -->


    

