## TES ONLINE PROGRAMMER - PT SMS

 Cara menjalankan projek :
 1. git clone repository terlebih dahulu :
    - https://github.com/triwulandaribms/TriWulandari_tes_online_PTSMS
  
 2. jika belum membuat model dan melakukan migration :
    -  php artisan make:model namaModel
    -  php artisan make:migration namaModel
    -  php artisan migrate

 3. lalu jalankan perintah :
    - php artisan serve
  
 4. lalu hit endpoint untuk registrasi
   
 5. setelah itu hit endpoint login untuk mendapatkan token
   
 6. selanjutnya ketika akan hit endpoint barang dan pembelian serta list, update dan hapus user :
     - harus memasukan token terlebih dahulu 
     - dengan klik ke menu authorization pada postman
     - lalu auth type pilih Bearer Token
     - dan masukan token 
    
   

 Dokumentasi API

 [Download postman collection](<DOKUMENTASI API.postman_collection.json>)
