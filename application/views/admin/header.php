<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Paracou-Ex Admin</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-touch-icon.png">

        <link rel="stylesheet" type="text/css" href="/public/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="/public/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" type="text/css" href="/public/css/daterangepicker.css" />
        <link rel="stylesheet" type="text/css" href="/public/fontawesome/css/fontawesome-all.css" />
        <link rel="stylesheet" type="text/css" href="/public/css/main.css">

        <script type="text/javascript" src="/public/js/jquery.min.js"></script>
        <script type="text/javascript" src="/public/js/popper.min.js"></script>
        <script type="text/javascript" src="/public/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/public/js/dataTables.bootstrap4.min.js"></script>
        <script type="text/javascript" src="/public/js/moment.min.js"></script>
        <script type="text/javascript" src="/public/js/daterangepicker.js"></script>
        <script type="text/javascript" src="/public/js/bootstrap.min.js"></script>
        <script>
            $(function() {
                $('a[data-confirm]').click(function(ev) {
                        var href = $(this).attr('href');

                        if (!$('#dataConfirmModal').length) {
                                $('body').append('<div id="dataConfirmModal" class="modal" role="dialog" aria-labelledby="dataConfirmLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h3 id="dataConfirmLabel">Confirm</h3></div><div class="modal-body"></div><div class="modal-footer"><button class="btn" data-dismiss="modal" aria-hidden="true">No</button><a class="btn btn-danger" id="dataConfirmOK">Yes</a></div></div></div></div>');
                        }
                        $('#dataConfirmModal').find('.modal-body').text($(this).attr('data-confirm'));
                        $('#dataConfirmOK').attr('href', href);
                        $('#dataConfirmModal').modal({show:true});

                        return false;
                });
        });
        </script>
    </head>
<body>
    <?php if(!empty($flash_message)){
                $html = '<div class="bg-warning w-100 flash-message">';
                $html .= $flash_message;
                $html .= '</div>';
                echo $html;
            }
    ?>
