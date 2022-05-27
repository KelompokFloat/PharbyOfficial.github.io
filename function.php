<?php
session_start();


//Membuat koneksi ke database
$conn = mysqli_connect("localhost","root","","stockbarang_pharby");

//Menambah barang baru
if(isset($_POST['addnewbarang'])){
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];

    //soal gambar
    $allowed_extension = array('png','jpg');
    $nama = $_FILES['file']['name']; //ngambil nama gambar
    $dot = explode('.',$nama);
    $ekstensi = strtolower(end($dot)); //mengambil ekstensinya
    $ukuran = $_FILES['file']['size']; //ngambil size file
    $file_tmp = $_FILES['file']['tmp_name']; //ngambil lokasi file
    

    //penamaan file -> enkripsi
    $images = md5(uniqid($nama,true) . time()).'.'.$ekstensi; //menggabungkan nama file yg dienkripsi dengan ekstensinya


    //proes upload gambar
    if(in_array($ekstensi, $allowed_extension) === true){
        //validasi ukuran file
        if($ukuran < 15000000){
            move_uploaded_file($file_tmp, 'images/'.$images);

            $addtotable = mysqli_query($conn, "INSERT INTO stock (namabarang, deskripsi, stock, images) VALUES('$namabarang','$deskripsi','$stock','$images')");
            if($addtotable){
                header('location:index.php');  
            } else {
                echo 'Gagal' ;
                header('location:index.php');
            }
                } else {
                    //kalau file lebih dari 15 mb
                    echo '
                    <script>
                    alert("Ukuran terlalu besar");
                    window.location.href="index.php";
                    </script>
                    ';
                }
            } else {
                //kalau gambarnya tidak png/jpg
                echo '
                <script>
                    alert("File harus png/jgp");
                    window.location.href="index.php";
                </script>
                ';
            }
    
};


    //Menambah barang masuk
    if(isset($_POST['barangmasuk'])){
        $barangnya = $_POST['barangnya'];
        $penerima = $_POST['penerima'];
        $qty = $_POST['qty'];

        $cekstocksekarang = mysqli_query($conn,"SELECT * FROM stock WHERE idbarang='$barangnya'");
        $ambildatanya = mysqli_fetch_array($cekstocksekarang);

        $stocksekarang = $ambildatanya['stock'];
        $tambahkanstocksekarangdenganquantity = $stocksekarang+$qty;

        $addtomasuk = mysqli_query($conn,"INSERT INTO masuk (idbarang, keterangan, qty) VALUES('$barangnya','$penerima', '$qty')");
        $updatestockmasuk = mysqli_query($conn,"update stock set stock='$tambahkanstocksekarangdenganquantity' where idbarang='$barangnya'");
        if($addtomasuk&&$updatestockmasuk){
            header('location:masuk.php');
        } else {
            echo 'Gagal' ;
            header('location:masuk.php');
        }
    }

    


    //Menambah barang keluar
    if(isset($_POST['addbarangkeluar'])){
        $barangnya = $_POST['barangnya'];
        $penerima = $_POST['penerima'];
        $qty = $_POST['qty'];

        $cekstocksekarang = mysqli_query($conn,"SELECT * FROM stock WHERE idbarang='$barangnya'");
        $ambildatanya = mysqli_fetch_array($cekstocksekarang);

        $stocksekarang = $ambildatanya['stock'];
        $tambahkanstocksekarangdenganquantity = $stocksekarang-$qty;

        $addtokeluar = mysqli_query($conn,"INSERT INTO keluar (idbarang, penerima, qty) VALUES('$barangnya','$penerima','$qty')");
        $updatestockmasuk = mysqli_query($conn,"UPDATE stock SET stock='$tambahkanstocksekarangdenganquantity' WHERE idbarang='$barangnya'");
        if($addtokeluar&&$updatestockmasuk){
            header('location:keluar.php');
        } else {
            echo 'Gagal' ;
            header('location:keluar.php');
        }
    }


    //Update info barang
    if(isset($_POST['updatebarang'])){
        $idb = $_POST['idb'];
        $namabarang = $_POST['namabarang'];
        $deskripsi = $_POST['deskripsi'];
        
        //soal gambar
    $allowed_extension = array('png','jpg');
    $nama = $_FILES['file']['name']; //ngambil nama gambar
    $dot = explode('.',$nama);
    $ekstensi = strtolower(end($dot)); //mengambil ekstensinya
    $ukuran = $_FILES['file']['size']; //ngambil size file
    $file_tmp = $_FILES['file']['tmp_name']; //ngambil lokasi file
    

    //penamaan file -> enkripsi
    $images = md5(uniqid($nama,true) . time()).'.'.$ekstensi; //menggabungkan nama file yg dienkripsi dengan ekstensinya

    if($ukuran==0){
        //jika tidak ingin upload
        $update = mysqli_query($conn,"UPDATE stock SET namabarang='$namabarang', deskripsi='$deskripsi' WHERE idbarang='$idb'");
        if($update){
            header('location:index.php');
        } else {
        echo 'Gagal' ;
        header('location:index.php');
        }
    } else {
        //jika ingin
        move_uploaded_file($file_tmp, 'images/'.$images);
        $update = mysqli_query($conn,"UPDATE stock SET namabarang='$namabarang', deskripsi='$deskripsi', images='$images' WHERE idbarang='$idb'");
        if($update){
            header('location:index.php');
        } else {
        echo 'Gagal' ;
        header('location:index.php');
        }
    }
        
    }


    //Menghapus barang dari stock
    if(isset($_POST['hapusbarang'])){
        $idb = $_POST['idb'];

        $gambar = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang='$idb'");
        $get = mysqli_fetch_array($gambar);
        $img = 'images/'.$get['images'];
        unlink($img);

        $hapus = mysqli_query($conn,"DELETE FROM stock WHERE idbarang='$idb'");
        if($hapus){
            header('location:index.php');
        } else {
        echo 'Gagal' ;
        header('location:index.php');
        }
    }





    //Mengubah Data barang masuk
    if(isset($_POST['updatebarangmasuk'])){
        $idb = $_POST['idb'];
        $idm = $_POST['idm'];
        $deskripsi = $_POST['keterangan'];
        $qty = $_POST['qty'];

        $lihatstock = mysqli_query($conn,"select * from stock where idbarang='$idb'");
        $stocknya = mysqli_fetch_array($lihatstock);
        $stockskrg = $stocknya['stock'];

        $qtyskrg = mysqli_query($conn,"select * from masuk where idmasuk='$idm'");
        $qtynya = mysqli_fetch_array($qtyskrg);
        $qtyskrg = $qtynya['qty'];

        if($qty>$qtyskrg){
            $selisih = $qty-$qtyskrg;
            $kurangin = $stockskrg + $selisih;
            $kurangistocknya = mysqli_query($conn,"update stock set stock='$kurangin' where idbarang='$idb'");
            $updatenya = mysqli_query($conn,"update masuk set qty='$qty', keterangan='$deskripsi' where idmasuk='$idm'");
                if($kurangistocknya&&$updatenya){
                    header('location:masuk.php');
                    } else {
                    echo 'Gagal' ;
                    header('location:masuk.php');
                }

        } else {
            $selisih = $qtyskrg-$qty;
            $kurangin = $stockskrg - $selisih;
            $kurangistocknya = mysqli_query($conn,"update stock set stock='$kurangin' where idbarang='$idb'");
            $updatenya = mysqli_query($conn,"update masuk set qty='$qty', keterangan='$deskripsi' where idmasuk='$idm'");
                if($kurangistocknya&&$updatenya){
                    header('location:masuk.php');
                    } else {
                    echo 'Gagal' ;
                    header('location:masuk.php');
                }
        }
    }




    //Menghapus barang masuk
    if(isset($_POST['hapusbarangmasuk'])){
        $idb = $_POST['idb'];
        $qty = $_POST['kty'];
        $idm = $_POST['idm'];

        $getdatastock = mysqli_query($conn,"select * from stock where idbarang='$idb'");
        $data = mysqli_fetch_array($getdatastock);
        $stok = $data['stock'];

        $selisih = $stok-$qty;


        $update = mysqli_query($conn, "update stock set stock='$selisih' where idbarang='$idb'");
        $hapusdata = mysqli_query($conn, "delete from masuk where idmasuk='$idm'");

        if($update&&$hapusdata){
            header('location:masuk.php');
        } else {
            header('location:masuk.php');
        }

    }



    //Mengubah data barang keluar
    if(isset($_POST['updatebarangkeluar'])){
        $idb = $_POST['idb'];
        $idk = $_POST['idk'];
        $penerima = $_POST['penerima'];
        $qty = $_POST['qty'];

        $lihatstock = mysqli_query($conn,"select * from stock where idbarang='$idb'");
        $stocknya = mysqli_fetch_array($lihatstock);
        $stockskrg = $stocknya['stock'];

        $qtyskrg = mysqli_query($conn,"select * from keluar where idkeluar='$idk'");
        $qtynya = mysqli_fetch_array($qtyskrg);
        $qtyskrg = $qtynya['qty'];

        if($qty>$qtyskrg){
            $selisih = $qty-$qtyskrg;
            $kurangin = $stockskrg - $selisih;
            $kurangistocknya = mysqli_query($conn,"update stock set stock='$kurangin' where idbarang='$idb'");
            $updatenya = mysqli_query($conn,"update keluar set qty='$qty', penerima='$penerima' where idkeluar='$idk'");
                if($kurangistocknya&&$updatenya){
                    header('location:keluar.php');
                    } else {
                    echo 'Gagal' ;
                    header('location:keluar.php');
                }

        } else {
            $selisih = $qtyskrg-$qty;
            $kurangin = $stockskrg + $selisih;
            $kurangistocknya = mysqli_query($conn,"update stock set stock='$kurangin' where idbarang='$idb'");
            $updatenya = mysqli_query($conn,"update keluar set qty='$qty', penerima='$penerima' where idkeluar='$idk'");
                if($kurangistocknya&&$updatenya){
                    header('location:keluar.php');
                    } else {
                    echo 'Gagal' ;
                    header('location:keluar.php');
                }
        }
    }




    //Menghapus barang keluar
    if(isset($_POST['hapusbarangkeluar'])){
        $idb = $_POST['idb'];
        $qty = $_POST['kty'];
        $idk = $_POST['idk'];

        $getdatastock = mysqli_query($conn,"select * from stock where idbarang='$idb'");
        $data = mysqli_fetch_array($getdatastock);
        $stok = $data['stock'];

        $selisih = $stok+$qty;


        $update = mysqli_query($conn, "update stock set stock='$selisih' where idbarang='$idb'");
        $hapusdata = mysqli_query($conn, "delete from keluar where idkeluar='$idk'");

        if($update&&$hapusdata){
            header('location:keluar.php');
        } else {
            header('location:keluar.php');
        }

    }


?>