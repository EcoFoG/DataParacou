<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        
        <link rel="stylesheet" href="/public/css/bootstrap.min.css">
        <link rel="stylesheet" href="/public/css/main.css">
        <link rel="stylesheet" type="text/css" href="/public/css/select2.min.css">
        <link rel="stylesheet" href="/public/css/jquery-ui.css">
        
        <script type="text/javascript" src="/public/css/pdfmake.min.js"></script>
        <script type="text/javascript" src="/public/css/jquery.min.js"></script>
        <script type="text/javascript" src="/public/css/select2.js"></script>
        <script type="text/javascript" src="/public/css/vfs_fonts.js"></script>
        <script type="text/javascript" src="/public/css/jquery-ui.js"></script>
    </head>
    <body>
    <!--[if lt IE 8]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <?php
            $arr = $this->session->flashdata();
            if(!empty($arr['flash_message'])){
                $html = '<div class="bg-warning container flash-message">';
                $html .= $arr['flash_message'];
                $html .= '</div>';
                echo $html;
            }


        ?>
