<?php

require_once 'database.php';

function compressImage($source, $destination, $quality = 70)
{
    $info = getimagesize($source);

    if ($info["mime"] === "image/jpeg") {
        $image = imagecreatefromjpeg($source);
        imagejpeg($image, $destination, $quality);
    } elseif ($info["mime"] === "image/png") {
        $image = imagecreatefrompng($source);
        $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
        $white = imagecolorallocate($bg, 255, 255, 255);
        imagefill($bg, 0, 0, $white);
        imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
        imagejpeg($bg, $destination, $quality);
        imagedestroy($bg);
    }
}

function generateProductCode($db, $name)
{
    $prefix = strtoupper(substr(preg_replace("/[^A-Za-z]/", "", $name), 0, 3));
    if ($prefix === "") {
        $prefix = "PRD";
    }

    $stmt = $db->prepare(
        "SELECT code FROM products WHERE code LIKE ? ORDER BY code DESC LIMIT 1",
    );
    $stmt->execute(["{$prefix}-%"]);
    $lastCode = $stmt->fetchColumn();

    if ($lastCode) {
        $lastNumber = (int) substr($lastCode, 4);
        $newNumber = str_pad($lastNumber + 1, 6, "0", STR_PAD_LEFT);
    } else {
        $newNumber = "000001";
    }

    return "{$prefix}-{$newNumber}";
}
