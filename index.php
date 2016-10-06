<?php
session_start();

# Jabber log parser v0.2
# web-developer.name
# 2012

error_reporting(0);
$html = '';
$urls = array('Выберите лог',
    'http://beerseller.org/bot_log/ubuntu@conference.jabber.ru/',
    'http://chatlogs.jabber.ru/linux-talks@conference.jabber.ru/',
    'http://chatlogs.jabber.ru/programming@conference.jabber.ru/',
    'http://chatlogs.jabber.ru/talks@conference.jabber.ru/',
);

function web($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec($ch);
    curl_close($ch);
    return ($res);
}

//level 1
if (isset($_REQUEST['url']) && !empty($_REQUEST['url']) && $_REQUEST['url'] > 0
    && !empty($_REQUEST['year']) && !empty($_REQUEST['month']) && !empty($_REQUEST['day']) ) {
    $_SESSION['url'] = (int) $_REQUEST['url'];
    $_SESSION['year'] = (int) $_REQUEST['year'];
    $_SESSION['month'] = (int) $_REQUEST['month'];
    $_SESSION['day'] = (int) $_REQUEST['day'];
} else if (isset($_SESSION['url'])) {
    unset($_SESSION['url']);
    unset($_SESSION['year']);
    unset($_SESSION['month']);
    unset($_SESSION['day']);
    unset($_SESSION['day_arr']);
    unset($_SESSION['full_url']);
}
?>
<!DOCTYPE HTML>
<html>
    <head><title>Jabber logs parser v0.2  — Фото и видео из логов джаббер конференций</title>
        <meta charset="UTF-8" />
        <meta name="description" content="Jabber logs parser  — Фото и видео из логов джаббер конференций" />
        <meta name="keywords" content="Jabber logs parser  — Фото и видео из логов джаббер конференций" />
        <style>
            * {padding:0; margin: 0;}
            h2 {font-size: 20px; padding-left: 10px; padding-top:5px;}
            body {font-size: 11px; background-color: black; color: white;}
            .corner {padding: 10px; margin: 10px; -moz-border-radius: 10px; -webkit-border-radius: 10px; border-radius: 10px; -moz-box-shadow: 0px 0px 5px #ccc; box-shadow: 0px 0px 5px #ccc; }
        </style>
        <script type="text/javascript" src="jquery-1.8.0.min.js"></script>
        <script type="text/javascript" src="galleria/galleria-1.4.7.min.js"></script>
        <link rel="stylesheet" href="colorbox/colorbox.css" />
        <script src="colorbox/jquery.colorbox.js"></script>
    </head>
    <body>
        <form action="index.php" method="get">
            <h2><a style="color:white;text-decoration:none;text-shadow: 1px 1px 0px red;" href="javascript:location.href=location.pathname">Jabber logs parser v0.2</a></h2>
	    <p style="padding-left:10px"><i>Фото и видео из логов джаббер конференций</i></p>
	    <br>
            <p style="padding-left:10px">URL: <select name="url">
                <?php
                foreach ($urls as $key => $value) {
                    $selected = '';
                    if (isset($_SESSION['url']) && $_SESSION['url'] == $key)
                        $selected = 'selected ';
                    echo '<option ' . $selected . ' value="' . $key . '">' . $value . '</option>';
                }
                ?>
            </select>
            <?php
            //level 2
            if (isset($_SESSION['url'])) {
                if (isset($_REQUEST['year'])) {
                    $_SESSION['year'] = (int) $_REQUEST['year'];
                    if (strlen($_SESSION['year']) > 4)
                        $_SESSION['year'] = substr($_SESSION['year'], 0, 4);
                }
                if (!isset($_SESSION['year'])) {
                    $level2 = web($urls[$_SESSION['url']]);
                    preg_match_all('/<a href\=\"([0-9]{1,4})\/\">([ 0-9]{1,5})\/<\/a>/uis', $level2, $years_arr);
                    if (is_array($years_arr[1])) {
                        echo ' Выберите год: <select name="year"><option value="0">Выберите год</option>';
                        foreach ($years_arr[1] as $key => $value) {
                            $selected = '';
                            if ($_SESSION['year'] == (int) $value)
                                $selected = 'selected';
                            echo '<option ' . $selected . ' value="' . (int) $value . '">' . (int) $value . '</option>';
                        }
                        echo '</select>';
                    }
                } else {
                    echo 'Год: <input size="4" readonly="true" type="text" name="year" value="' . $_SESSION['year'] . '"> ';

                    //Level 3
                    if (isset($_REQUEST['month'])) {
                        $_SESSION['month'] = (int) $_REQUEST['month'];
                        if (strlen($_SESSION['month']) > 2)
                            $_SESSION['month'] = substr($_SESSION['month'], 0, 2);
                    }
                    if (!isset($_SESSION['month'])) {
                        $level3 = web($urls[$_SESSION['url']] . $_SESSION['year'] . '/');
                        preg_match_all('/<a href\=\"([0-9]{1,2})\/\">([ 0-9]{1,3})\/<\/a>/uis', $level3, $month_arr);
                        if (is_array($month_arr[1])) {
                            echo 'Выберите месяц: <select name="month"><option value="0">Выберите месяц</option>';
                            foreach ($month_arr[1] as $key => $value) {
                                $selected = '';
                                if ($_SESSION['month'] == (int) $value)
                                    $selected = 'selected';
                                echo '<option ' . $selected . ' value="' . (int) $value . '">' . (int) $value . '</option>';
                            }
                            echo '</select>';
                        }
                    }
                    else {
                        echo 'Месяц: <input size="4" readonly="true" type="text" name="month" value="' . $_SESSION['month'] . '"> ';

                        //Level 4
                        if (isset($_REQUEST['day'])) {
                            $_SESSION['day'] = (int) $_REQUEST['day'];
                            if (strlen($_SESSION['day']) > 2)
                                $_SESSION['day'] = substr($_SESSION['day'], 0, 2);
                        }

                        if (!isset($_SESSION['day_arr'])) {
                            $level4 = web($urls[$_SESSION['url']] . $_SESSION['year'] . '/' . $_SESSION['month'] . '/');
                            $m = '';
                            if (strlen($_SESSION['month']) == 1)
                                $m = '0';
                            if (preg_match('/\<title\>404 Not Found\<\/title\>/uis', $level4))
                                $level4 = web($urls[$_SESSION['url']] . $_SESSION['year'] . '/' . $m . $_SESSION['month'] . '/');
                            preg_match_all('/<a href\=\"([0-9]{1,2})\.html">([ 0-9]{1,3})\.html<\/a>/uis', $level4, $day_arr);

                            if (is_array($day_arr[1])) {
                                echo 'Выберите день: <select name="day"><option value="0">Выберите день</option>';
                                foreach ($day_arr[1] as $key => $value) {
                                    $selected = '';
                                    if ($_SESSION['day'] == (int) $value)
                                        $selected = 'selected';
                                    echo '<option ' . $selected . ' value="' . (int) $value . '">' . (int) $value . '</option>';
                                    $_SESSION['day_arr'][] = (int) $value;
                                    sort($_SESSION['day_arr']);
                                }
                                echo '</select>';
                            }
                        }
                        else {
                            echo 'Выберите день: <select name="day"><option value="0">Выберите день</option>';
                            foreach ($_SESSION['day_arr'] as $key => $value) {
                                $selected = '';
                                if ($_SESSION['day'] == (int) $value)
                                    $selected = 'selected';
                                echo '<option ' . $selected . ' value="' . (int) $value . '">' . (int) $value . '</option>';
                            }
                            echo '</select>';

                            //Level 5
                            $full_url = $urls[$_SESSION['url']] . $_SESSION['year'] . '/' . $_SESSION['month'] . '/' . $_SESSION['day'] . '.html';
                            $level5 = web($full_url);
                            $m = '';
                            if (strlen($_SESSION['month']) == 1)
                                $m = '0';
                            $d = '';
                            if (strlen($_SESSION['day']) == 1)
                                $d = '0';
                            if (preg_match('/\<title\>404 Not Found\<\/title\>/uis', $level5)) {
                                $full_url = $urls[$_SESSION['url']] . $_SESSION['year'] . '/' . $m . $_SESSION['month'] . '/' . $d . $_SESSION['day'] . '.html';
                                $level5 = web($full_url);
                            }
                            $_SESSION['full_url'] = $full_url;
                            preg_match_all('/((https\:|http\:|ftp\:)\/\/[a-zA-Z0-9\.\-\_\/\&\?\%]+(\.jpg|\.png|\.gif|\.jpeg))/uis', $level5, $body_arr);
                            preg_match_all('/((https\:|http\:)\/\/(www\.)?youtube\.com\/watch\?[a-zA-Z0-9\_\-\?\=\&\#]+)\</uis', $level5, $youtube_arr);
                            $html = 'var all_content = [';
                            if (is_array($body_arr[1])) {
                                $body_arr[1] = array_unique($body_arr[1]);

                                foreach ($body_arr[1] as $key => $value) {
                                    $html .= "{image: '" . $value . "' },";
                                }
                            }

                            if (is_array($youtube_arr[1])) {
                                $youtube_arr[1] = array_unique($youtube_arr[1]);
                                foreach ($youtube_arr[1] as $key => $value) {
                                    $html .= "{ video: '" . $value . "' },";
                                }
                            }

                            $html .= '];';
                        }
                    }
                }
            }
            if (isset($_SESSION['full_url']))
                echo ' <input id="log" type="button" value=" Лог ">';
            ?>
            <input onclick="location.href=location.pathname;" type="button" value="Очистить"><input type="submit" value="Выбрать"></p>
        </form>
        <div id="gallery"></div>
<?php if (isset($_SESSION['day_arr'])) { ?>
           	<script>
    <?php echo $html; ?>
                Galleria.loadTheme('galleria/themes/classic/galleria.classic.min.js');
                Galleria.run('#gallery', {width:$(document).width(),height:$(window).height()-150,imageCrop: false, imagePan: false, imagePosition: "center center", lightbox: true, dataSource: all_content, dummy: '404.jpg'});
    <?php if (isset($_SESSION['full_url'])) echo '$("#log").colorbox({iframe:true, width:"80%", height:"80%", href:"' . $_SESSION['full_url'] . '"});'; ?>
            </script>
<?php } ?>
    </body>
</html>
