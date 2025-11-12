## TES ONLINE PROGRAMMER - PT SMS

 Cara menjalankan projek :
 
 1. git clone repository terlebih dahulu :
    - https://github.com/triwulandaribms/TriWulandari_tes_online_PTSMS
    ```bash
      cd mini_project
    ```
 2. install depedencies
    ```bash
      composer install
    ```
 3. Salin file .env.example menjadi .env :
   ```bash
      cp .env.example .env
   ```
 4. generate application key :
   ```bash
    php artisan key:generate
   ```
 5. Atur konfigurasi database di file .env
   
 6. jika belum membuat model dan melakukan migration :
    -  ```bash
         php artisan make:model namaModel
       ```
    -  ```bash
         php artisan make:migration namaModel
       ```
    -  ```bash 
         php artisan migrate
       ```
 7. lalu jalankan server :
    - php artisan serve
  
 8. lalu hit endpoint untuk registrasi
   
 9.  setelah itu hit endpoint login untuk mendapatkan token
   
 10. selanjutnya ketika akan hit endpoint barang dan pembelian serta list, update dan hapus user :
     - harus memasukan token terlebih dahulu 
     - dengan klik ke menu authorization pada postman
     - lalu auth type pilih Bearer Token
     - dan masukan token 
    
   

 Dokumentasi API

 [Download postman collection](<DOKUMENTASI API.postman_collection.json>)
