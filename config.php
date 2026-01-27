<?php
require 'vendor/autoload.php';
use Aws\S3\S3Client;

$bucket = 'nugwebphps3';  // Sesuaikan dengan nama bucket S3
$region = 'us-east-1'; // Sesuaikan dengan region AWS Academy

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => $region
    // Tidak perlu key/secret karena kita pakai IAM Role di EC2
]);

?>
