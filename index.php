<?php 
include 'config.php'; 

// Logika Upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_upload'])) {
    $file = $_FILES['file_upload'];
    try {
        $result = $s3->putObject([
            'Bucket' => $bucket,
            'Key'    => time() . '_' . basename($file['name']), // Tambah timestamp biar nama file unik
            'SourceFile' => $file['tmp_name']
            // ACL dihapus, pastikan Bucket Policy sudah di-set ke Public Read
        ]);
        $msg = "<div class='alert alert-success'>File berhasil diupload!</div>";
    } catch (Exception $e) {
        $msg = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

// Logika Delete
if (isset($_GET['delete'])) {
    $s3->deleteObject([
        'Bucket' => $bucket,
        'Key'    => $_GET['delete']
    ]);
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMK Cloud Storage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .img-preview { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="text-center mb-4">🚀 SMK Cloud Asset Manager</h2>
            
            <?php if(isset($msg)) echo $msg; ?>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form action="" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                        <input type="file" name="file_upload" class="form-control" required>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Daftar Asset di S3</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Preview</th>
                                <th>Nama File</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $objects = $s3->listObjectsV2(['Bucket' => $bucket]);
                            if (isset($objects['Contents'])) {
                                foreach ($objects['Contents'] as $object) {
                                    $fileUrl = $s3->getObjectUrl($bucket, $object['Key']);
                                    $ext = strtolower(pathinfo($object['Key'], PATHINFO_EXTENSION));
                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    
                                    echo "<tr>
                                        <td class='align-middle'>";
                                    if ($isImage) {
                                        echo "<img src='{$fileUrl}' class='img-preview border shadow-sm'>";
                                    } else {
                                        echo "<div class='img-preview bg-secondary text-white d-flex align-items-center justify-content-center small'>File</div>";
                                    }
                                    echo "</td>
                                        <td class='align-middle'><code>{$object['Key']}</code></td>
                                        <td class='text-center align-middle'>
                                            <a href='{$fileUrl}' target='_blank' class='btn btn-sm btn-info text-white'>Buka</a>
                                            <a href='index.php?delete={$object['Key']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin hapus?\")'>Hapus</a>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' class='text-center py-4 text-muted'>Belum ada file di S3</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <p class="text-center mt-4 text-muted small">Powered by AWS Academy - LabRole</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>