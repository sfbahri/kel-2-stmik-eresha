<?php 
class Order_sales_model extends CI_Model{	
    
    function __construct() {
        parent::__construct();
    }
    
    function user_nik(){
        return $this->session->userdata('sess_nik');
    }
    
    function user_outlet(){
        return $this->session->userdata('sess_outlet');
    }
    
    function tambah_baru($noos){
        $user_nik = $this->user_nik();
        
        $sql2 = "INSERT INTO order_sales (noos,
                                created_by,
                                created_at,expired_at) 
                        VALUES ('$noos',
                                '$user_nik',
                                NOW(),DATE_ADD(NOW(), INTERVAL 12 HOUR))";

        return $this->db->query($sql2);
            
    }
    
    function data(){
        $user_nik = $this->user_nik();
        $user_outlet = $this->user_outlet();
        
        if($user_nik == '00000001' || $user_nik == '00000002'){
            $sql2 = "SELECT  a.noos,
                        a.nonota,
                        b.nama AS nama_pelanggan,
                        a.created_at,
                        a.bayar,
                        CASE
                                WHEN kategori = 1 THEN 'Grosir'
                                WHEN kategori = 2 THEN 'Retail'
                                ELSE 'Tidak Ada Status'
                                END AS kategori_status,
                        c.nama as nama_karyawan,
                        DATE_FORMAT(a.created_at, '%d-%b-%Y %H:%i:%s') as created_at,
                        DATE_FORMAT(a.expired_at, '%d-%b-%Y %H:%i:%s') as expired_at
                FROM order_sales AS a
                LEFT JOIN pelanggan AS b ON a.id_pelanggan = b.id AND b.is_deleted = 0
                LEFT JOIN karyawan as c ON a.created_by = c.nik and c.is_deleted = 0
                WHERE a.`is_deleted` = 0 and a.is_deleted = 0 ORDER BY a.id DESC";
        //and a.created_by = '$user_nik'
        return $this->db->query($sql2);
        }else{
            $sql2 = "SELECT  a.noos,
                        a.nonota,
                        b.nama AS nama_pelanggan,
                        a.created_at,
                        a.bayar,
                        CASE
                                WHEN kategori = 1 THEN 'Grosir'
                                WHEN kategori = 2 THEN 'Retail'
                                ELSE 'Tidak Ada Status'
                                END AS kategori_status,
                        c.nama as nama_karyawan,
                        DATE_FORMAT(a.created_at, '%d-%b-%Y %H:%i:%s') as created_at,
                        DATE_FORMAT(a.expired_at, '%d-%b-%Y %H:%i:%s') as expired_at
                FROM order_sales AS a
                LEFT JOIN pelanggan AS b ON a.id_pelanggan = b.id AND b.is_deleted = 0
                LEFT JOIN karyawan as c ON a.created_by = c.nik and c.is_deleted = 0
                WHERE a.`is_deleted` = 0 and a.is_deleted = 0 and c.id_outlet = '$user_outlet' ORDER BY a.id DESC";
        //and a.created_by = '$user_nik'
        return $this->db->query($sql2);
        }
        
        
    }
    
    function data_select(){
        $user_nik = $this->user_nik();
        
        $sql2 = "SELECT  a.noos,
                        a.nonota,
                        b.nama AS nama_pelanggan,
                        a.created_at,
                        CASE
                                WHEN kategori = 1 THEN 'Grosir'
                                WHEN kategori = 2 THEN 'Retail'
                                ELSE 'Tidak Ada Status'
                                END AS kategori_status 
                FROM order_sales AS a
                LEFT JOIN pelanggan AS b ON a.id_pelanggan = b.id AND b.is_deleted = 0
                WHERE a.`is_deleted` = 0 and a.is_deleted = 0 ORDER BY a.id DESC ";
        //and a.created_by = '$user_nik'
        return $this->db->query($sql2);
    }
    
    /*function data_produk(){
        $sql = "SELECT  a.nama AS nama_produk,
                        a.kode AS kode_produk_header,
                        a.stok,
                        a.img,
                        a.harga
                FROM produk_header AS a 
                WHERE a.is_deleted = 0 
                ORDER BY a.id ASC";
        return $this->db->query($sql);
    }*/
    
    function data_produk(){
        
        $sql = "SELECT a.*,
                    FORMAT(a.harga, 0) AS hargas,
                    b.nama as nama_vendor,
                    a.nama AS nama_produk,
                    a.kode AS kode_produk_header,
                    a.stok,
                    a.img,
                    a.harga,
                    (SELECT count(xx.id) FROM produk as xx WHERE xx.kode_produk_header = a.kode and xx.beli = 0 and xx.id_outlet = 1 and xx.is_deleted = 0) as stok_akhir_allogio,
		    (SELECT count(xxx.id) FROM produk as xxx WHERE xxx.kode_produk_header = a.kode and xxx.beli = 0 and xxx.id_outlet = 2 and xxx.is_deleted = 0) as stok_akhir_tanah_abang
                FROM produk_header as a
                LEFT JOIN vendor as b ON a.id_vendor = b.id and b.is_deleted = 0
                WHERE a.is_deleted = 0 ORDER by a.id ASC";
        
        return $this->db->query($sql);

    }
    
    function update($data){
        $user_nik = $this->user_nik();
        $ongkos_kirim = str_replace(",","",$data['ongkos_kirim']);
        $sql = "UPDATE order_sales SET      id_pelanggan = '$data[list_member]',
                                            kategori     = '$data[kategori_pemesanan]',
                                            ongkos_kirim = '$ongkos_kirim',
                                            updated_by   = '$user_nik',
                                            updated_at   = NOW() WHERE noos = '$data[id_order_sales]'";
        
        return $this->db->query($sql);
    }
    
    function detail($data){
        $user_nik = $this->user_nik();
        
        $sql2 = "SELECT *,format(ongkos_kirim, 0) as ongkos_kirim FROM order_sales where noos = '$data[id_order_sales]' and is_deleted = 0";
        //created_by = '$user_nik' and
        return $this->db->query($sql2);
    }
    
    
    function simpan($data){
        $user_nik = $this->user_nik();
        $total_berat = 0.15 * $data['qty'];
        $total_harga = $data['harga'] * $data['qty'];
        $sql2 = "INSERT INTO order_sales_detail (noos,
                                kode_produk_header,
                                qty,
                                berat,
                                harga,
                                catatan,
                                created_by,
                                created_at) 
                        VALUES ('$data[no_order_sales]',
                                '$data[kd_produk_header]',
                                '$data[qty]',
                                '$total_berat',
                                '$total_harga',
                                 '$data[catatan]',
                                '$user_nik',
                                NOW())";

        return $this->db->query($sql2);
    }
    
    function hapus($data){
        $sql2 = "DELETE FROM order_sales_detail WHERE kode_produk_header = '$data[kd_produk_header]' and noos = '$data[no_order_sales]'";

        return $this->db->query($sql2);
    }
    
    function hapus_order_sales($data){
        
        $sql1 = "DELETE FROM order_sales WHERE noos = '$data[no_order_sales]'";
        $oke = $this->db->query($sql1);
        
        $sql2 = "DELETE FROM order_sales_detail WHERE noos = '$data[no_order_sales]'";
        $this->db->query($sql2);
        return $oke;
    }
            
    function data_produk_order($data){
        $sql = "SELECT  b.nama AS nama_produk,
                SUM(a.qty) AS count_qty,
                FORMAT(SUM(a.harga), 0) AS count_harga,
                SUM(a.berat) AS count_berat,
                a.kode_produk_header,
                a.noos,
                a.catatan
        FROM order_sales_detail AS a
        INNER JOIN produk_header AS b ON a.kode_produk_header = b.kode AND b.is_deleted = 0
        WHERE a.`is_deleted` = 0  AND a.noos = '$data[id_order_sales]' GROUP BY a.kode_produk_header ORDER by a.id ASC";
        return $this->db->query($sql);
    }
    
    function detailss($no_order_sales){
        $sql = "SELECT bayar FROM order_sales where noos = '$no_order_sales' and is_deleted = 0";
         return $this->db->query($sql);
    }
    
    function cetak_data_produk_order_header($no_order_sales){
        $user_nik = $this->user_nik();
        
        $sql2 = "SELECT  a.noos,
                        a.nonota,
                        b.nama AS nama_pelanggan,
                        b.nohp,
                        b.alamat,
                        a.created_at,
                        a.bayar,
                        a.ongkos_kirim,
                        FORMAT(a.ongkos_kirim, 0) AS ongkos_kirims,
                        CASE
                                WHEN kategori = 1 THEN 'Grosir'
                                WHEN kategori = 2 THEN 'Retail'
                                ELSE 'Tidak Ada Status'
                                END AS kategori_status,
                        DATE_FORMAT(a.expired_at, '%d-%b-%Y %H:%i:%s') as expired_at
                FROM order_sales AS a
                LEFT JOIN pelanggan AS b ON a.id_pelanggan = b.id AND b.is_deleted = 0
                WHERE a.`is_deleted` = 0 and a.noos = '$no_order_sales' and a.is_deleted = 0 ORDER BY a.id DESC ";
                //and a.created_by = '$user_nik'

        return $this->db->query($sql2);
    }
    
    function cetak_data_produk_order($no_order_sales){
        $sql = "SELECT  b.nama AS nama_produk,
                SUM(a.qty) AS count_qty,
                FORMAT(SUM(a.harga), 0) AS count_harga,
                SUM(a.berat) AS count_berat,
                a.kode_produk_header,
                a.noos,
                FORMAT(b.harga, 0) AS harga
        FROM order_sales_detail AS a
        INNER JOIN produk_header AS b ON a.kode_produk_header = b.kode AND b.is_deleted = 0
        WHERE a.`is_deleted` = 0  AND a.noos = '$no_order_sales' GROUP BY a.kode_produk_header ORDER by a.id ASC";
        return $this->db->query($sql);
    }
    
    function cetak_data_produk_order_header_all($no_order_sales){
        $sql = "SELECT  FORMAT(SUM(a.harga), 0) AS tot_harga,
	    SUM(a.berat) AS tot_berat,
        SUM(a.harga) as total_hrg
        FROM order_sales_detail AS a
        INNER JOIN produk_header AS b ON a.kode_produk_header = b.kode AND b.is_deleted = 0
        WHERE a.`is_deleted` = 0  AND a.noos = '$no_order_sales'";
        return $this->db->query($sql);
    }
    
    function bayar_order_sales($data){
        $user_nik = $this->user_nik();
        $sql = "UPDATE order_sales SET      bayar = '1',
                                            updated_by   = '$user_nik',
                                            updated_at   = NOW() WHERE noos = '$data[no_order_sales]'";
        
        return $this->db->query($sql);
        
    }
}
?>