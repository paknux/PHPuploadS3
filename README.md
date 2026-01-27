# Dokumentasi Deployment Aplikasi PHP CRUD & AWS S3

Project ini mendokumentasikan langkah-langkah mendeploy aplikasi PHP CRUD yang menyimpan file (asset) di **AWS S3** menggunakan **EC2 Ubuntu 24.04** di lingkungan **AWS Academy**.

---

## 🏗️ 1. Persiapan Infrastruktur AWS

### A. Membuat Instance EC2
1. Login ke AWS Academy Learner Lab.
2. Launch Instance dengan spesifikasi:
   - **Nama:** `PHP-S3-Server`
   - **AMI:** Ubuntu 24.04 LTS.
   - **Instance Type:** t2.micro (Free Tier).
   - **Key Pair:** Pilih atau buat baru.
   - **Security Group:** Izinkan **HTTP (80)** dan **SSH (22)**.
3. Hubungkan ke instance via SSH.

### B. Membuat dan Konfigurasi S3 Bucket
Jalankan perintah ini melalui terminal (setelah konfigurasi AWS CLI) untuk membuat bucket bernama `nugwebphps3`:

```bash
# Membuka Public Access Block
aws s3api put-public-access-block --bucket nugwebphps3 --public-access-block-configuration "BlockPublicAcls=false,IgnorePublicAcls=false,BlockPublicPolicy=false,RestrictPublicBuckets=false"

# Mengatur Policy agar file bisa diakses publik (Read Only)
aws s3api put-bucket-policy --bucket nugwebphps3 --policy '{
    "Version":"2012-10-17",
    "Statement":[{"Sid":"PublicReadGetObject","Effect":"Allow","Principal":"*","Action":"s3:GetObject","Resource":"arn:aws:s3:::nugwebphps3/*"}]
}'




## 🛠 Langkah 1: Persiapan dan Instalasi Server
Jalankan perintah berikut pada terminal EC2 Ubuntu 24.04 untuk menginstal Apache, PHP, dan dependensi lainnya:

```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Install Apache, PHP, dan ekstensi yang diperlukan
sudo apt install -y apache2 php php-cli php-curl php-xml php-mbstring libapache2-mod-php unzip


# Install Composer secara global
curl -sS [https://getcomposer.org/installer](https://getcomposer.org/installer) | php
sudo mv composer.phar /usr/local/bin/composer

# Install AWS SDK for PHP di direktori project
cd /var/www/html
sudo composer require aws/aws-sdk-php

# Atur izin folder agar web server bisa menulis file
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html

"# PHPuploadS3" 
