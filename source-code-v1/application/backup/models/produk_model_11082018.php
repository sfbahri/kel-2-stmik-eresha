<?php 
class Produk_model extends CI_Model{	
    
    function __construct() {
        parent::__construct();
    }
    
    function user_nik(){
        return $this->session->userdata('sess_nik');
    }

    function data_produk($kode){
        $sql = "SELECT * FROM produk WHERE is_deleted = 0 and kode_produksi = $kode ORDER by id ASC";
        return $this->db->query($sql);
    }
    
    function data(){
        
        $sql = "SELECT *,FORMAT(harga, 0) AS hargas FROM produk_header WHERE is_deleted = 0 ORDER by id ASC";
        return $this->db->query($sql);
        
        
//        $sql = "SELECT  a.`kode_produksi`,
//                        a.`kode`,
//                        a.`beli`,
//                        b.`gambar`,
//                        a.`harga`,
//                        FORMAT(a.harga, 0) AS hargas,
//                        a.`img_qrcode`,
//                        b.`qrcode`
//                FROM produk AS a
//                INNER JOIN produksi AS b ON a.`kode_produksi` = b.`kode` AND b.`is_deleted` = 0
//                WHERE a.`is_deleted` = 0";
//        return $this->db->query($sql);
        
//        $sql = "SELECT * FROM produk WHERE is_deleted = 0 ORDER by id ASC";
//        /*$sql = "SELECT 
//                        *,(SELECT COUNT(a.kode) AS total_produk FROM produk AS a WHERE a.kode_produksi = b.kode_produksi AND a.is_deleted = 0) totalproduk 
//                        FROM produk AS b WHERE b.is_deleted = 0 ORDER BY b.id ASC";*/
//                        //AND b.kode_produksi = '3282120180610'
//        return $this->db->query($sql);
    }

    function data_detail($kode_produksi){
        $sql = "SELECT * FROM produk WHERE is_deleted = 0 and kode_produksi = '$kode_produksi' ORDER by id ASC";
        return $this->db->query($sql);
    }
    
    function cari($data){
        $sql = "SELECT * FROM produk WHERE kode = '$data[kode_produk]' and is_deleted = 0";
        return $this->db->query($sql);
    }
    
    function cek_produk($kodeproduk){
        $sql = "SELECT * FROM produk WHERE kode = '$kodeproduk'";
        return $this->db->query($sql);
    }
    
    function cek_gudang($kodeproduk){
        
        $user = $this->session->userdata('sess_id_users');
        
        $sql = "UPDATE  produk SET     cek_gudang = 1,
                                        updated_by       = '$user',
                                        updated_at       = NOW() WHERE kode = '$kodeproduk'";
        
        return $this->db->query($sql);
    }


    function created_labelx($img_qrcode,$kode_produksi_produk,$data){
        
        $user_nik = $this->user_nik();
        
        $sql = "INSERT INTO produk (kode_produksi,
                                    kode,
                                    harga,
                                    stok,
                                    img_qrcode,
                                    created_by,
                                    created_at) 
                            VALUES ('$data[kode_produksi]',
                                    '$kode_produksi_produk',
                                    '$data[harga]',
                                    '1',
                                    '$img_qrcode',
                                    '$user_nik',
                                    NOW())";
                                    
        return $this->db->query($sql);
    }
    
    
    function created_label($img_qrcode,$harga_jual,$kode_produksi,$kode_produksi_produk,$inisial,$nama){
        
        $user_nik = $this->user_nik();
        
        $harga = str_replace(",","",$harga_jual);
        
        $sql = "INSERT INTO produk (kode_produk_header,
                                    nama,
                                    inisial,
                                    kode,
                                    harga,
                                    stok,
                                    id_outlet,
                                    img_qrcode,
                                    created_by,
                                    created_at) 
                            VALUES ('$kode_produksi',
                                    '$nama',
                                    '$inisial',
                                    '$kode_produksi_produk',
                                    '$harga',
                                    '1',
                                    '1',
                                    '$img_qrcode',
                                    '$user_nik',
                                    NOW())";
            return $this->db->query($sql);
        
    }
    
    function simpan_manual($data){
        
        $user_nik = $this->user_nik();
        
        $harga = str_replace(",","",$data['harga_jual']);
        $harga_modal = str_replace(",","",$data['harga_modal']);
        
        $stok = $data['small_warna_1_jumlah']+$data['small_warna_2_jumlah']+$data['small_warna_3_jumlah']+$data['medium_warna_1_jumlah']+$data['medium_warna_2_jumlah']+$data['medium_warna_3_jumlah']+$data['large_warna_1_jumlah']+$data['large_warna_2_jumlah']+$data['large_warna_3_jumlah'];
        
        $sql = "INSERT INTO produk_header (kode_produksi,
                                    kode,
                                    nama,
                                    harga,
                                    harga_modal,
                                    S1_inisial,
                                    S2_inisial,
                                    S3_inisial,
                                    M1_inisial,
                                    M2_inisial,
                                    M3_inisial,
                                    L1_inisial,
                                    L2_inisial,
                                    L3_inisial,
                                    S1_jumlah,
                                    S2_jumlah,
                                    S3_jumlah,
                                    M1_jumlah,
                                    M2_jumlah,
                                    M3_jumlah,
                                    L1_jumlah,
                                    L2_jumlah,
                                    L3_jumlah,
                                    stok,
                                    img_qrcode,
                                    created_by,
                                    created_at) 
                            VALUES ('$data[kode_produksi]',
                                    '$data[kode_utama]',
                                    '$data[nama]',
                                    '$harga',
                                    '$harga_modal',
                                    '$data[small_warna_1_inisial]',
                                    '$data[small_warna_2_inisial]',
                                    '$data[small_warna_3_inisial]',
                                    '$data[medium_warna_1_inisial]',
                                    '$data[medium_warna_2_inisial]',
                                    '$data[medium_warna_3_inisial]',
                                    '$data[large_warna_1_inisial]',
                                    '$data[large_warna_2_inisial]',
                                    '$data[large_warna_3_inisial]',
                                    '$data[small_warna_1_jumlah]',
                                    '$data[small_warna_2_jumlah]',
                                    '$data[small_warna_3_jumlah]',
                                    '$data[medium_warna_1_jumlah]',
                                    '$data[medium_warna_2_jumlah]',
                                    '$data[medium_warna_3_jumlah]',
                                    '$data[large_warna_1_jumlah]',
                                    '$data[large_warna_2_jumlah]',
                                    '$data[large_warna_3_jumlah]',
                                    '$stok',
                                    '$data[img_qrcode]',
                                    '$user_nik',
                                    NOW())";
            return $this->db->query($sql);
    }
    
}
?>