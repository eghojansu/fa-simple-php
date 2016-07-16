# Simple PHP

Framework PHP yang memudahkan pembuatan aplikasi php sederhana dengan konsep
Semi OOP. Ada beberapa class yang berfungsi sebagai helper. File-file utama
tetap menggunakan procedural.

Sudah dilengkapi dependency injection dari http://github.com/Level-2/Dice, bisa 
diakses lewat App->service.

## Usage

@see sample on app/modules :)

## Not tested

Framework ini belum ada unit Test.

## Installation

1. Copy folder file ini ke htdocs/www (sesuaikan server) 
2. Buat database menggunakan phpmyadmin, kemudian import schema pada folder app/schema sesuai urutannya
3. Edit file app/config/database.php, sesuaikan setting database-nya
4. Akses http://localhost/{nama-folder-file-ini}
5. Done
