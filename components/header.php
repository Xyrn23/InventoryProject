<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Inventory Management System'; ?></title>
    <link rel="icon" type="image/svg+xml" href="/assets/logo.svg">
    <link rel="stylesheet" href="/styles/base/base.css">
    <link rel="stylesheet" href="/styles/base/components/button.css">
    <link rel="stylesheet" href="/styles/base/components/card.css">
    <link rel="stylesheet" href="/styles/base/components/form.css">
    <link rel="stylesheet" href="/styles/base/components/modal.css">
    <link rel="stylesheet" href="/styles/base/components/background-logo.css">

    <?php if (isset($pageCss)): ?>
        <link rel="stylesheet" href="/styles/pages/<?php echo $pageCss; ?>">
    <?php endif; ?>
</head>
<body>
    <img src="/assets/logo.svg" class="background-logo">
