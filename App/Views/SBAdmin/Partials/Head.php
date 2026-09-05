<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="Portal SITI" />
    <title><?= App\Core\Config::$APP_NAME; ?></title>
    <link rel="icon" href="/SBAdmin/images/favicon.ico" type="image/png" sizes="16x16">
    <link href="/SBAdmin/css/styles.css" rel="stylesheet" />
    <?php
    if (isset($data['FormDesign']['Styles']['CSSFiles'])) {
        foreach ($data['FormDesign']['Styles']['CSSFiles'] as $item) {
            switch ($item) {
                case 'dataTables':
                    echo '<link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />';
                    break;
                default:
                    if (strrchr($item, '.css')) {
                        echo '<link href="' . $item . '" rel="stylesheet" crossorigin="anonymous" />';
                    }
                    break;
            }
        }
    }
    if (isset($data['FormDesign']['Scripts']['Head'])) {
        foreach ($data['FormDesign']['Scripts']['Head'] as $item) {
            switch ($item) {
                case 'vue':
                    echo '<script src="/SBAdmin/js/vue.global.js"></script>';
                    break;
            }
        }
    }
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" crossorigin="anonymous"></script>
</head>