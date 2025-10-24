<?php
/**
 * index.php (root shim)
 * -----------------------------------------------
 * File ini berfungsi sebagai "jembatan" agar domain utama
 * (misalnya https://namadomainmu.com) langsung membuka
 * aplikasi yang ada di folder /public tanpa harus mengetik /public.
 *
 * Pastikan file ini berada di /home/USERNAME/public_html/index.php
 * dan folder /public berisi file index.php utama aplikasi todolist.
 */

// Jalankan file utama di folder public
require __DIR__ . '/public/index.php';
