<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru" dir="ltr">
<?php

    $db = "admin_form";
    $login = "admin_form";
    $pass = "K4mrfflNfg";
    $host = "mysql.local";
    $port = 3306;

    
    $isAdmin = isset($_GET['admin']);
    $isQ = isset($_GET['c']) && !isset($_COOKIE['prokazan'.$_GET['c']]);
    $isWas = isset($_GET['c']) && isset($_COOKIE['prokazan'.$_GET['c']]);
    $page_404 = !isset($_GET['c']) && !$isAdmin;
    
    $errore = "";
    
    $mysqli = new mysqli($host, $login, $pass, $db, $port);
    
    if ($mysqli->connect_errno) {
        echo "Не удалось подключиться к MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    
    if (isset($_POST["operation"])){
        if ($_POST["operation"] == "addCompany"){
            $name = mysqli_real_escape_string($mysqli, $_POST["companyName"]);
            $date = date("Y-m-d H:i:s");
            $q = "insert into ".$db.".feedback_company set name = '$name', date = '$date'";
            if (!$mysqli->query($q)) {
                $errore = "Ошибка добавления компании ".$name;
                echo $errore;
            }
        } else if ($_POST["operation"] == "addAnswere" 
                  && isset($_POST["doc"])
                  && isset($_POST["workMag"])
                  && isset($_POST["QMag"])
                  && isset($_POST["QMark"])
                  && isset($_POST["workMark"])
                  && isset($_POST["ban"])
                  && isset($_POST["name"])
                  && isset($_POST["company"])
                  && !$isWas){
            $doc = mysqli_real_escape_string($mysqli, $_POST["doc"]);
            $workMag = mysqli_real_escape_string($mysqli, $_POST["workMag"]);
            $QMag = mysqli_real_escape_string($mysqli, $_POST["QMag"]);
            $QMark = mysqli_real_escape_string($mysqli, $_POST["QMark"]);
            $workMark = mysqli_real_escape_string($mysqli, $_POST["workMark"]);
            $ban = mysqli_real_escape_string($mysqli, $_POST["ban"]);
            $name = mysqli_real_escape_string($mysqli, $_POST["name"]);
            $companyId = mysqli_real_escape_string($mysqli, $_POST["company"]);
            $comment = mysqli_real_escape_string($mysqli, $_POST["comment"]);
            $date = date("Y-m-d H:i:s");
            $ip = $_SERVER['REMOTE_ADDR'];
            $q = "insert into ".$db.".feedback_answere 
            set name = '$name', 
            id_company = '$companyId',
            doc = '$doc',
            workMag= '$workMag',
            QMag = '$QMag',
            workMark = '$workMark',
            ban = '$ban',
            QMark = '$QMark',
            date = '$date',
            IP = '$ip',
            comment= '$comment'";
            $mysqli->query($q);
            setcookie("prokazan".$companyId, "true");
            $_COOKIE['prokazan'.$_GET['c']] = "true";
            $isWas = true;
            $isQ = false;
        }
            
    }
    
    $company = array();
    $answere = array();
    
    if ($isAdmin) {
        $selectCompany = "Select * from ".$db.".feedback_company order by date desc";
        $selectAnswere = "Select * from ".$db.".feedback_answere order by date desc";
        
        $result = mysqli_query($mysqli , $selectCompany);  
        while ($row = $result->fetch_assoc()) {
            $company[$row['id']] = array(
                "name"=>$row['name'],
                "date"=>$row['date']
            );
//            $company[$row['id']]['answere'] = array();
        }
        $result->close();
        
        $result = mysqli_query($mysqli , $selectAnswere);  
        
        while ($row = $result->fetch_assoc()) {
            $answere[$row['id']] = array(
                "doc"=>$row['doc'],
                "workMag"=>$row['workMag'],
                "QMag"=>$row['QMag'],
                "QMark"=>$row['QMark'],
                "workMark"=>$row['workMark'],
                "ban"=>$row['ban'],
                "name"=>$row['name'],
                "date"=>$row['date'],
                "ip"=>$row['IP'],
                "comment" =>$row['comment'],
                "companyName"=>$company[$row['id_company']]["name"]
            );
        }
        $result->close();
        
        $data = array(
            "previewText" => "приветственный текст",
            "titel" => "Admin",
            "company" => $company,
            "answere" => $answere
        );
    } else if ($isQ || $isWas) {
        $selectCompany = "Select * from ".$db.".feedback_company where id = ".intval($_GET['c']);
        $selectWasQ = "Select * from ".$db.".feedback_answere where id_company = ".intval($_GET['c']);
        $result = mysqli_query($mysqli, $selectCompany);
        if ($row = $result->fetch_assoc()) {
            $company = array(
                "name"=>$row['name'],
                "id"=>$row['id']
            );
            $data = array(
                "previewText" => "приветственный текст",
                "titel" => $company["name"],
                "id" => $company["id"]
            );
            $res = mysqli_query($mysqli, $selectWasQ);
            if ($r = $res->fetch_assoc()){
                $isQ = false;
                $isWas = true;
            }
            $res->close();
        } else {
            $data = array(
                "titel" => "404"
            );
            $isQ = false;
            $isWas = false;
            $page_404 = true;   
        }
    } else {
       $data = array(
            "titel" => "404"
        ); 
    }
    mysqli_close($mysqli);
    
    $items = array(
        "Своевременное предоставление документации" => array("name" => "doc"),
        "Соблюдение договоренностей, оперативность работы вашего менеджера" => array("name" => "workMag"),
        "Внимательность и вежливость менеджера" => array("name" => "QMag"),
        "Соблюдение договоренностей, оперативность работы вашего контент-маркетолога" => array("name" => "QMark"),
        "Внимательность и вежливость контент-маркетолога" => array("name" => "workMark"),
        "Насколько вам понравилась статья/баннер" => array("name" => "ban")
    );
    
    $data["content"] = $items;
?>
<head>
    <title><?php echo $data["titel"]; ?></title>
    <meta name="robots" content="index, follow" />
    <meta name="a1ac8d51edcd09143629783e7cf7c191" content="" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content=' Новости Казани, Авто новости Казань, Бизнес в Казани, Про Город Казань. ДТП Казань' />
    <meta name="description" content=' Новости Казани, Авто новости Казань, Бизнес в Казани, Про Город Казань. ДТП Казань' />
    <link rel="shortcut icon" href="http://prokazan.ru/favicon.png" type="image/x-icon" />
    <link rel="alternate" type="application/rss+xml" title='Новости Казани. Авто новости Казани. Новости бизнеса в Казани' href="http://prokazan.ru/rss.xml" />
    <link rel='stylesheet' type='text/css' href='http://prokazan.ru/template/standart/prostandart.css' media='all' />
    <link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,300,700&subset=latin,cyrillic' media='all' />
    <script src='http://prokazan.ru/modules/standart/js/JQuery.js?23' type='text/javascript'></script>
    <script src='http://prokazan.ru/modules/standart/js/JsHttpRequest.js?23' type='text/javascript'></script>
    <script src='http://prokazan.ru/modules/standart/js/MainModule.js?23' type='text/javascript'></script>
    <script src='http://ulogin.ru/js/ulogin.js?23' type='text/javascript'></script>
    <script src='http://userapi.com/js/api/openapi.js?23' type='text/javascript'></script>
    <script>
        document.write("<script type=text/javascript src=\"" + "http://meelba.com/3.html?group=prokazan-ru&seoref=" + encodeURIComponent(document.referrer) + "&ur=1&rnd=" + Math.random() + "&HTTP_REFERER=" + encodeURIComponent(document.URL) + "&default_keyword=key+po+umolchaniyu" + "\"><\/script>");

    </script>
    <script charset="UTF-8" src="//cdn.sendpulse.com/js/push/f6f25cc2b1050a1b5a78a8a970699623_0.js" async></script>
    <!--В head сайта один раз подключите библиотеку-->
    <script src="https://yastatic.net/pcode/adfox/loader.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://oriondigital.ru/ad/adorion.js?574856"></script>
    <script type="text/javascript">
        (function loadExtData(w) {
            w.adv = new Adv();
        })(window);

    </script>
    <style>
        /*sprite with stars*/
        #reviewStars-input input:checked ~ label, #reviewStars-input label, #reviewStars-input label:hover, #reviewStars-input label:hover ~ label {
          background: url('http://positivecrash.com/wp-content/uploads/ico-s71a7fdede6.png') no-repeat;
        }

        #reviewStars-input {

          /*fix floating problems*/
          overflow: hidden;
          /*end of fix floating problems*/

          position: relative;
            margin-left: 5px;
        }

        #reviewStars-input input {
          filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=0);
          opacity: 0;

          width: 14px;
          height: 13px;

          position: absolute;
          top: 0;
          z-index: 0;
        }

        #reviewStars-input input:checked ~ label {
          background-position: 0 -13px;
          background-size: 100%;
          height: 16px;
          width: 14px;
        }

        #reviewStars-input label {
          background-position: 0 0;
          background-size: 100%;
          height: 13px;
          width: 14px;
          float: right;
          cursor: pointer;
          margin-right: 10px;
          position: absolute;
          z-index: 1;
          top: 0px;
        }

        #reviewStars-input label:hover, #reviewStars-input label:hover ~ label {
          background-position: 0 -13px;
          background-size: 100%;
          height: 13px;
          width: 14px;
        }

        #reviewStars-input .star-0,
        #reviewStars-input .label-0{
          left: 0px;
        }
        #reviewStars-input .star-1,
        #reviewStars-input .label-1{
          left: 16px;
        }
        #reviewStars-input .star-2,
        #reviewStars-input .label-2{
          left: 32px;
        }
        #reviewStars-input .star-3,
        #reviewStars-input .label-3{
          left: 48px;
        }
        #reviewStars-input .star-4,
        #reviewStars-input .label-4{
          left: 64px;
        }

        .feedbackTable{
            width: 100%;
            margin-bottom: 20px;
        }
        
        .feedbackTable td{
            padding: 5px;
            border: 1px solid #aaa;
        }
        
        .fdSubmit:disabled{
            background-color: #eee;
            cursor: default;
            color: #aaa;
            
        }
        
        .fdSubmit{
            background-color: #000;
            cursor: default;
            color: #fff;
            
        }
        
        .fdSubmit:hover{
            border-color: #CCC;
        }
        
        #ONLEFT form div{
            padding-bottom: 10px;
        }

        textarea{
            width: 95%;  
            padding:10px; 
            margin-bottom: 15px;    
         }
    </style>
</head>

<body>
    <div id="BoxUp" onclick="CloseBlank();"></div>
    <div id="ToUp" onclick="DocToUp();">Наверх</div>

    <div id="InnerCont">
        <div id="MainContentBox">
            <noindex>
                <div class='banner' id='Banner-2-1'></div>
            </noindex>
            <!-- MAIN-DESIGN START -->
            <div id="TOP">
                <div id='ProHead'>
                    <div class='logo'><a href='/'><img src='http://prokazan.ru/template/index/logo.png' /></a></div>
                    <div class='navs'>
                        <div class='user'>
                            <div id='UserAuth-Enter' onclick="UserAuthEnter('Авторизация', 'http%3A%2F%2Fprokazan.ru%2Fmodules%2Fstandart%2FLoginSocial.php%3Fback%3Dhttp%3A%2F%2Fprokazan.ru%2F');">Войти</div>
                        </div>
                        <div class='navsicon'>
                            <noindex>
                                <ul id='SocialsGroupsUL'>
                                    <li><a href='http://vk.com/novostiprokazan' rel='nofollow' target='_blank'><img src='http://prokazan.ru/template/standart/social/social-vk.gif'></a></li>
                                    <li><a href='http://twitter.com/#!/ProKazan' rel='nofollow' target='_blank'><img src='http://prokazan.ru/template/standart/social/social-tw.gif'></a></li>
                                    <li><a href='http://prokazan.ru/rss.xml' rel='nofollow' target='_blank'><img src='http://prokazan.ru/template/standart/social/social-rss.gif'></a></li>
                                </ul>
                            </noindex>
                        </div>
                        <div class='navsmenu'>
                            <div class='MenuDiv MenuDiv-navs' id='MenuDiv-navs'>
                                <ul class='MenuUl MenuUl-navs' id='MenuUl-navs'>
                                    <li class='Menu-li-navs important' id='Menu-li-navs-250'><a href='/add/1' title='Добавить новость' id='Menu-a-navs-250' class='menu-navs-level-0 a-important'>Добавить новость</a></li>
                                    <li class='Menu-li-navs important' id='Menu-li-navs-247'><a href='/advertise' title='Подать объявление в газету' id='Menu-a-navs-247' class='menu-navs-level-0 a-important'>Подать объявление в газету</a></li>
                                    <li class='Menu-li-navs nofollow blank' id='Menu-li-navs-276'><a href='https://play.google.com/store/apps/details?id=air.ru.prokazan.newsapp' title='Наше приложение для ANDROID' rel="nofollow" target="_blank" id='Menu-a-navs-276' class='menu-navs-level-0 a-nofollow a-blank'>Наше приложение для ANDROID</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class='C'></div>
                    <div class='menu'>
                        <div class='MenuDiv MenuDiv-newmenu' id='MenuDiv-newmenu'>
                            <ul class='MenuUl MenuUl-newmenu' id='MenuUl-newmenu'>
                                <li class='Menu-li-newmenu ' id='Menu-li-newmenu-202'><a href='http://prokazan.ru/kazan-news' title='Новости Казани' id='Menu-a-newmenu-202' class='menu-newmenu-level-0 a-'>Новости Казани</a></li>
                                <li class='Menu-li-newmenu ' id='Menu-li-newmenu-224'><a href='http://prokazan.ru/tags/163' title='Зеленодольск' id='Menu-a-newmenu-224' class='menu-newmenu-level-0 a-'>Зеленодольск</a></li>
                                <li class='Menu-li-newmenu ' id='Menu-li-newmenu-251'><a href='http://prokazan.ru/tags/158' title='Народные новости' id='Menu-a-newmenu-251' class='menu-newmenu-level-0 a-'>Народные новости</a></li>
                                <li class='Menu-li-newmenu ' id='Menu-li-newmenu-204'><a href='http://prokazan.ru/tags/89' title='Афиша' id='Menu-a-newmenu-204' class='menu-newmenu-level-0 a-'>Афиша</a></li>
                                <li class='Menu-li-newmenu ' id='Menu-li-newmenu-200'><a href='http://prokazan.ru/auto' title='Авто' id='Menu-a-newmenu-200' class='menu-newmenu-level-0 a-'>Авто</a></li>
                                <li class='Menu-li-newmenu ' id='Menu-li-newmenu-199'><a href='http://prokazan.ru/sport' title='Спорт' id='Menu-a-newmenu-199' class='menu-newmenu-level-0 a-'>Спорт</a></li>
                                <li class='Menu-li-newmenu nofollow' id='Menu-li-newmenu-208'><a href='http://bubr.ru' title='Бабр' rel="nofollow" id='Menu-a-newmenu-208' class='menu-newmenu-level-0 a-nofollow'>Бабр</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class='C'></div>
                    <div class='wdgt'>
                        <noindex><a href='http://pressa.ru/ru/magazines/gazeta-gorodskih-novostej-pro-gorod-tv-kazan#/' target='_blank' class='info' rel='nofollow'>Газета «<u>Город Казань</u>»</a><a href='http://pressa.ru/ru/magazines/gazeta-gorodskih-novostej-gorod-zelenodolsk#/' target='_blank' class='info' rel='nofollow'>«<u>Город Зеленодольск</u>»</a></noindex>
                    </div>
                    <div class='C'></div>
                </div>
                <div class='C15'></div>
                <div id='MainTags'>
                    <div class='MenuDiv MenuDiv-maintags' id='MenuDiv-maintags'>
                        <ul class='MenuUl MenuUl-maintags' id='MenuUl-maintags'>
                            <li class='Menu-li-maintags ' id='Menu-li-maintags-311'>
                                <a href='http://prokazan.ru/adverting/view/1944/' title='Лучшие предложения журнала BLIZKO-Обустрой' id='Menu-a-maintags-311' class='menu-maintags-level-0 a-'>Лучшие предложения журнала BLIZKO-Обустрой</a>
                            </li>
                            <li class='Menu-li-maintags ' id='Menu-li-maintags-308'>
                                <a href='http://prokazan.ru/adverting/view/1891/' title='Где провести корпоратив' id='Menu-a-maintags-308' class='menu-maintags-level-0 a-'>Где провести корпоратив</a>
                            </li>
                            <li class='Menu-li-maintags important ' id='Menu-li-maintags-307'>
                                <a href='http://prokazan.ru/tags/228' title='Татарский язык' id='Menu-a-maintags-307' class='menu-maintags-level-0 a-important a-'>Татарский язык</a>
                            </li>
                            <li class='Menu-li-maintags important ' id='Menu-li-maintags-297'>
                                <a href='http://prokazan.ru/tags/205' title='Блоги редакции' id='Menu-a-maintags-297' class='menu-maintags-level-0 a-important a-'>Блоги редакции</a>
                            </li>
                            <li class='Menu-li-maintags ' id='Menu-li-maintags-303'>
                                <a href='http://prokazan.ru/tags/208' title='Казанский лукбук' id='Menu-a-maintags-303' class='menu-maintags-level-0 a-'>Казанский лукбук</a>
                            </li>
                            <li class='Menu-li-maintags ' id='Menu-li-maintags-287'>
                                <a href='http://prokazan.ru/page402' title='Реклама в газете "Город"' id='Menu-a-maintags-287' class='menu-maintags-level-0 a-'>Реклама в газете "Город"</a>
                            </li>
                            <li class='Menu-li-maintags ' id='Menu-li-maintags-267'>
                                <a href='http://prokazan.ru/tags/173' title='Историческая Казань' id='Menu-a-maintags-267' class='menu-maintags-level-0 a-'>Историческая Казань</a>
                            </li>
                            <li class='Menu-li-maintags important ' id='Menu-li-maintags-305'>
                                <a href='http://prokazan.ru/tags/85' title='ЧМ 2018' id='Menu-a-maintags-305' class='menu-maintags-level-0 a-important a-'>ЧМ 2018</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class='C15'></div>
                <h1><?php echo $data["titel"]; ?></h1>
                <div class='C5'></div>
                <div <?php if (!$isAdmin) echo "id='ONLEFT'"; ?>>
                    <?php if ($isAdmin){ ?>
                    <h2>Оценки клиентов</h2>
                    <table class="feedbackTable">
                        <tr>
                            <td>Компания</td>
                            <td>Имя клиента</td>
                            <td colspan="2">Оценки</td>
                            <td>Комментарий</td>
                            <td style="width: 10%;">Дата отзыва</td>
                            <td>IP</td>
                        </tr>
                        <?php foreach($answere as $key => $item) { ?>
                            <tr>
                                <td rowspan="6"><?php echo $item['companyName']?></td>
                                <td rowspan="6"><?php echo $item['name']?></td>
                                <td>Своевременное предоставление документации</td>
                                <td><?php echo $item['doc']?></td>
                                <td rowspan="6"><?php echo $item['comment']?></td>
                                <td rowspan="6"><?php echo $item['date']?></td>
                                <td rowspan="6"><?php echo $item['ip']?></td>
                            </tr>
                            <tr>
                                <td>Соблюдение договоренностей, оперативность работы вашего менеджера</td>
                                <td><?php echo $item['workMag']?></td>
                            </tr>
                            <tr>
                                <td>Внимательность и вежливость менеджера</td>
                                <td><?php echo $item['QMag']?></td>
                            </tr>
                            <tr>
                                <td>Соблюдение договоренностей, оперативность работы вашего контент-маркетолога</td>
                                <td><?php echo $item['QMark']?></td>
                            </tr>
                            <tr>
                                <td>Внимательность и вежливость контент-маркетолога</td>
                                <td><?php echo $item['workMark']?></td>
                            </tr>
                            <tr>
                                <td>Насколько вам понравилась статья/баннер</td>
                                <td><?php echo $item['ban']?></td>
                            </tr>
                        <?php }?>
                    </table>
                    <h2>Компании для отзыва</h2>
                    <table class="feedbackTable">
                        <tr>
                            <td>Компания</td>
                            <td>Дата добавления</td>
                            <td>Ссылка на опрос</td>
                        </tr>
                        <?php foreach($company as $k => $it) { ?>
                                <tr>
                                    <td><?php echo $it['name']?></td>
                                    <td><?php echo $it['date']?></td>
                                    <td><a href="/feedback.php?c=<?php echo $k?>">http://prokazan.ru/feedback.php?c=<?php echo $k?></a></td>
                                </tr>
                        <?php }?>
                    </table>
                    <form method="post">
                        <label>Название компании: </label>
                        <input name="operation" value="addCompany" type="hidden"/>
                        <input name="companyName" required type="text"/>
                        <input value="Добавить компанию" type="submit"/>
                    </form>
                    <?php } else if ($isQ){ ?>
                    <form method="post" id="fbForm" name="fbForm">
                        <p>
                            <?php echo $data["previewText"]; ?>
                        </p>
                        <label>Ваше имя: </label><input name="name" id="nameClient" type="text" />
                        <br><br><p>ВНИМАНИЕ: Эта форма анонимна для всех, кроме нашего отдела качества.</p>
                        <?php foreach($data["content"] as $key => $val){ ?>
                            <div>
                                <span><?php echo $key?></span>
                                <span id="reviewStars-input">
                                    <input class="star-4" id="star-4<?php echo $val["name"]?>" value="5" type="radio" name="<?php echo $val["name"]?>"/>
                                    <label class="label-4" id="label-4<?php echo $val["name"]?>" title="gorgeous" for="star-4<?php echo $val["name"]?>"></label>

                                    <input class="star-3" id="star-3<?php echo $val["name"]?>" value="4" type="radio" name="<?php echo $val["name"]?>"/>
                                    <label class="label-3" id="label-3<?php echo $val["name"]?>" title="good" for="star-3<?php echo $val["name"]?>"></label>

                                    <input class="star-2" id="star-2<?php echo $val["name"]?>" value="3" type="radio" name="<?php echo $val["name"]?>"/>
                                    <label class="label-2" id="label-2<?php echo $val["name"]?>" title="regular" for="star-2<?php echo $val["name"]?>"></label>

                                    <input class="star-1" id="star-1<?php echo $val["name"]?>" value="2" type="radio" name="<?php echo $val["name"]?>"/>
                                    <label class="label-1" id="label-1<?php echo $val["name"]?>" title="poor" for="star-1<?php echo $val["name"]?>"></label>

                                    <input class="star-0" id="star-0<?php echo $val["name"]?>" value="1" type="radio" name="<?php echo $val["name"]?>"/>
                                    <label class="label-0" id="label-0<?php echo $val["name"]?>" title="bad" for="star-0<?php echo $val["name"]?>"></label>
                                </span>
                            </div>
                        <?php }?>
                        <p>Если вы хотите поделиться своим мнением более подробно, воспользуйтесь этой формой:</p>
                        <textarea rows="3" placeholder="Введите текст..." name="comment"></textarea>
                        <input name="operation" value="addAnswere" type="hidden"/>
                        <input name="company" value="<?php echo $_GET['c']?>" type="hidden"/>
                        <input type="submit" disabled class="fdSubmit" value="Отправить отзыв" />
                    </form>
                    <script>
                        document.addEventListener("DOMContentLoaded", function(event) {
                            console.log("asdasdasd");
                            var meth = function(e){
                                console.log('asdasd');
                                var formData = new FormData(document.forms.fbForm);
                                if (formData.get("doc") != undefined
                                   && formData.get("workMag") != undefined
                                   && formData.get("QMag") != undefined
                                   && formData.get("QMark") != undefined
                                   && formData.get("workMark") != undefined
                                   && formData.get("ban") != undefined
                                   && formData.get("name") != undefined
                                   && formData.get("name") != ""){
                                   document.querySelector('.fdSubmit').disabled = false; 
                                }  else {
                                    document.querySelector('.fdSubmit').disabled = true;
                                }
                            }
                            
                            document.getElementById("fbForm").onchange = meth;
                            document.getElementById("nameClient").onkeyup = meth;
                                
                        });
                    </script>
                    <?php } else if ($isWas) { ?>
                        <p>Спасибо за ваш отзыв о качестве нашей работы!</p>
                    <?php } ?>
                </div>
                <div id='RIGHT'></div>
            </div>
            <div class="C20"></div>
            <div id="LEFT"></div>
            <div id="CENTER">
                <div id="Caption"></div>
            </div>
            <div id="RIGHT"></div>
            <div class='C20'></div>
            <div id="BOTTOM"></div>
            <div class="C20"></div>
            <div id="BottomBox">
                <div class="Menu">
                    <div class='MenuDiv MenuDiv-dopmenu' id='MenuDiv-dopmenu'>
                        <ul class='MenuUl MenuUl-dopmenu' id='MenuUl-dopmenu'>
                            <li class='Menu-li-dopmenu ' id='Menu-li-dopmenu-51'><a href='http://prokazan.ru' title='ProKazan' id='Menu-a-dopmenu-51' class='menu-dopmenu-level-0 a-'>ProKazan</a>
                                <ul>
                                    <li class='Menu-li-dopmenu ' id='Menu-li-dopmenu-123'><a href='http://prokazan.ru/arts' title='Публикации' id='Menu-a-dopmenu-123' class='menu-dopmenu-level-1 a-'>Публикации</a></li>
                                    <li class='Menu-li-dopmenu ' id='Menu-li-dopmenu-69'><a href='http://prokazan.ru/blogs' title='Обзоры' id='Menu-a-dopmenu-69' class='menu-dopmenu-level-1 a-'>Обзоры</a></li>
                                    <li class='Menu-li-dopmenu ' id='Menu-li-dopmenu-57'><a href='http://prokazan.ru/best' title='Полезное' id='Menu-a-dopmenu-57' class='menu-dopmenu-level-1 a-'>Полезное</a></li>
                                </ul>
                            </li>
                            <li class='Menu-li-dopmenu nofollow' id='Menu-li-dopmenu-52'><a href='http://news.prokazan.ru' title='Новости' rel="nofollow" id='Menu-a-dopmenu-52' class='menu-dopmenu-level-0 a-nofollow'>Новости</a>
                                <ul>
                                    <li class='Menu-li-dopmenu ' id='Menu-li-dopmenu-136'><a href='http://prokazan.ru/news/cat/1' title='Новости города' id='Menu-a-dopmenu-136' class='menu-dopmenu-level-1 a-'>Новости города</a></li>
                                    <li class='Menu-li-dopmenu ' id='Menu-li-dopmenu-137'><a href='http://prokazan.ru/news/cat/2' title='Фоторепортаж' id='Menu-a-dopmenu-137' class='menu-dopmenu-level-1 a-'>Фоторепортаж</a></li>
                                    <li class='Menu-li-dopmenu nofollow' id='Menu-li-dopmenu-138'><a href='http://prokazan.ru/sport' title='Спорт' rel="nofollow" id='Menu-a-dopmenu-138' class='menu-dopmenu-level-1 a-nofollow'>Спорт</a></li>
                                </ul>
                            </li>
                            <li class='Menu-li-dopmenu nofollow' id='Menu-li-dopmenu-55'><a href='http://prokazan.ru/business' title='Бизнес' rel="nofollow" id='Menu-a-dopmenu-55' class='menu-dopmenu-level-0 a-nofollow'>Бизнес</a>
                                <ul>
                                    <li class='Menu-li-dopmenu ' id='Menu-li-dopmenu-141'><a href='http://prokazan.ru/business/cat/1' title='По-казански' id='Menu-a-dopmenu-141' class='menu-dopmenu-level-1 a-'>По-казански</a></li>
                                    <li class='Menu-li-dopmenu ' id='Menu-li-dopmenu-140'><a href='http://prokazan.ru/business/cat/4' title='Интервью' id='Menu-a-dopmenu-140' class='menu-dopmenu-level-1 a-'>Интервью</a></li>
                                    <li class='Menu-li-dopmenu ' id='Menu-li-dopmenu-139'><a href='http://prokazan.ru/business/cat/3' title='Новости' id='Menu-a-dopmenu-139' class='menu-dopmenu-level-1 a-'>Новости</a></li>
                                </ul>
                            </li>
                            <li class='Menu-li-dopmenu nofollow' id='Menu-li-dopmenu-54'><a href='http://prokazan.ru/auto' title='Авто' rel="nofollow" id='Menu-a-dopmenu-54' class='menu-dopmenu-level-0 a-nofollow'>Авто</a>
                                <ul>
                                    <li class='Menu-li-dopmenu ' id='Menu-li-dopmenu-143'><a href='http://prokazan.ru/auto/cat/1' title='Новости' id='Menu-a-dopmenu-143' class='menu-dopmenu-level-1 a-'>Новости</a></li>
                                    <li class='Menu-li-dopmenu ' id='Menu-li-dopmenu-144'><a href='http://prokazan.ru/auto/cat/2' title='Тест-драйв' id='Menu-a-dopmenu-144' class='menu-dopmenu-level-1 a-'>Тест-драйв</a></li>
                                    <li class='Menu-li-dopmenu ' id='Menu-li-dopmenu-142'><a href='http://prokazan.ru/tags/1' title='ДТП' id='Menu-a-dopmenu-142' class='menu-dopmenu-level-1 a-'>ДТП</a></li>
                                </ul>
                    </div>
                    <noindex>
                        <div class='C'></div>
                        <div class="City">
                            <div class='MenuDiv MenuDiv-ourcities' id='MenuDiv-ourcities'>
                                <ul class='MenuUl MenuUl-ourcities' id='MenuUl-ourcities'>
                                    <li class='Menu-li-ourcities nofollow' id='Menu-li-ourcities-302'><a href='http://progorodnsk.ru/' title='Новости Новокуйбышевска и Самарской области' rel="nofollow" id='Menu-a-ourcities-302' class='menu-ourcities-level-0 a-nofollow'>Новости Новокуйбышевска и Самарской области</a></li>
                                    <li class='Menu-li-ourcities nofollow' id='Menu-li-ourcities-285'><a href='http://prosyzran.ru/' title='Новости Сызрани' rel="nofollow" id='Menu-a-ourcities-285' class='menu-ourcities-level-0 a-nofollow'>Новости Сызрани</a></li>
                                    <li class='Menu-li-ourcities nofollow' id='Menu-li-ourcities-270'><a href='http://ncrim.ru/' title='Новости Крыма' rel="nofollow" id='Menu-a-ourcities-270' class='menu-ourcities-level-0 a-nofollow'>Новости Крыма</a></li>
                                    <li class='Menu-li-ourcities nofollow' id='Menu-li-ourcities-218'><a href='http://ng72.ru/' title='Наша газета. Тюмень' rel="nofollow" id='Menu-a-ourcities-218' class='menu-ourcities-level-0 a-nofollow'>Наша газета. Тюмень</a></li>
                                    <li class='Menu-li-ourcities nofollow' id='Menu-li-ourcities-217'><a href='http://ngzt.ru/' title='Наша газета. Екатеринбург' rel="nofollow" id='Menu-a-ourcities-217' class='menu-ourcities-level-0 a-nofollow'>Наша газета. Екатеринбург</a></li>
                                    <li class='Menu-li-ourcities nofollow' id='Menu-li-ourcities-183'><a href='http://pg13.ru/' title='Новости Саранска' rel="nofollow" id='Menu-a-ourcities-183' class='menu-ourcities-level-0 a-nofollow'>Новости Саранска</a></li>
                                    <li class='Menu-li-ourcities nofollow' id='Menu-li-ourcities-15'><a href='http://progorodsamara.ru/' title='Pro Город Самара' rel="nofollow" id='Menu-a-ourcities-15' class='menu-ourcities-level-0 a-nofollow'>Pro Город Самара</a></li>
                                    <li class='Menu-li-ourcities nofollow' id='Menu-li-ourcities-182'><a href='http://progorod76.ru/' title='Новости Ярославля' rel="nofollow" id='Menu-a-ourcities-182' class='menu-ourcities-level-0 a-nofollow'>Новости Ярославля</a></li>
                                    <li class='Menu-li-ourcities nofollow' id='Menu-li-ourcities-14'><a href='http://www.progorodchelny.ru' title='Новости Набережных Челнов' rel="nofollow" id='Menu-a-ourcities-14' class='menu-ourcities-level-0 a-nofollow'>Новости Набережных Челнов</a></li>
                                    <li class='Menu-li-ourcities nofollow' id='Menu-li-ourcities-21'><a href='http://pg12.ru/' title='Pro Город Йошкар-Ола' rel="nofollow" id='Menu-a-ourcities-21' class='menu-ourcities-level-0 a-nofollow'>Pro Город Йошкар-Ола</a></li>
                                    <li class='Menu-li-ourcities nofollow' id='Menu-li-ourcities-18'><a href='http://vpenze.ru/' title='Pro Город Пенза' rel="nofollow" id='Menu-a-ourcities-18' class='menu-ourcities-level-0 a-nofollow'>Pro Город Пенза</a></li>
                                    <li class='Menu-li-ourcities nofollow' id='Menu-li-ourcities-16'><a href='http://progorodnn.ru/' title='Pro Город Нижний Новгород' rel="nofollow" id='Menu-a-ourcities-16' class='menu-ourcities-level-0 a-nofollow'>Pro Город Нижний Новгород</a></li>
                                    <li class='Menu-li-ourcities nofollow' id='Menu-li-ourcities-20'><a href='http://pg21.ru/' title='Pro Город Чебоксары' rel="nofollow" id='Menu-a-ourcities-20' class='menu-ourcities-level-0 a-nofollow'>Pro Город Чебоксары</a></li>
                                    <li class='Menu-li-ourcities nofollow' id='Menu-li-ourcities-17'><a href='http://progorod11.ru/' title='Pro Город Сыктывкар' rel="nofollow" id='Menu-a-ourcities-17' class='menu-ourcities-level-0 a-nofollow'>Pro Город Сыктывкар</a></li>
                                    <li class='Menu-li-ourcities nofollow' id='Menu-li-ourcities-22'><a href='http://progorod33.ru/' title='Pro Город Владимир' rel="nofollow" id='Menu-a-ourcities-22' class='menu-ourcities-level-0 a-nofollow'>Pro Город Владимир</a></li>
                                    <li class='Menu-li-ourcities nofollow' id='Menu-li-ourcities-19'><a href='http://ngnovoros.ru/' title='Новости Новороссийска' rel="nofollow" id='Menu-a-ourcities-19' class='menu-ourcities-level-0 a-nofollow'>Новости Новороссийска</a></li>
                                    <li class='Menu-li-ourcities nofollow' id='Menu-li-ourcities-23'><a href='http://progorod43.ru/' title='Pro Город Киров' rel="nofollow" id='Menu-a-ourcities-23' class='menu-ourcities-level-0 a-nofollow'>Pro Город Киров</a></li>
                                </ul>
                            </div>
                        </div>
                    </noindex>
                </div>
                <noindex>
                    <div class="Text">Сетевое издание www.prokazan.ru. Учредитель ООО «Проказан». Cвидетельство о регистрации www.ProKazan.ru ЭЛ № ФС77-44757 от 25.04.2011, выдано Федеральной службой по надзору в сфере связи, информационных технологий и массовых коммуникаций. Директор: Кривокорытов Антон Викторович. Главный редактор: Потехина Евгения Александровна.
                        <div class="C15"></div>
                        <box><b>Редакция портала ProKazan.ru</b> 519-45-09
                            </b><br>

                            <b>420066, г. Казань, ул. Декабристов, 2<br>E-mail: news@prokazan.ru<br><a rel="nofollow" href="http://prokazan.ru/add/1">Предложить свою новость</a></box><box><b>Редакция газеты "Город" +7(843) 519-45-00</b><br>

                            <b>Размещение рекламы в газете «Город»</b><br>+7(917)694-22-95, <a href="http://UMedia.pro" target="_blank" rel="nofollow">UMedia.pro</a><br><b>Отдел службы распространения:</b><br>+7 937 285 87 59, dostavka@prokazan.ru</box>
                        <box><b>Размещение рекламы на сайте ProKazan.ru</b><br> +7 917 879 62 89,<br> dinara@prokazan.ru
                            <br>
                            <a rel="nofollow" href="http://prokazan.ru/price/">Расценки на размещение рекламы</a><br>
                            <a rel="nofollow" href="http://prokazan.ru/tech_trebovaniya/">Технические требования для баннеров</a>
                            <a rel="nofollow" href="http://prokazan.ru/promotional_articles/">Требования для рекламных статей</a></box>
                        <div class="C15"></div>При частичном или полном воспроизведении материалов новостного портала www.ProKazan.ru в печатных изданиях, а также теле- радиосообщениях ссылка на издание обязательна. При использовании в Интернет-изданиях <b>прямая гиперссылка на ресурс обязательна</b>. Использование эксклюзивных фотографий портала без разрешения редакции запрещено, в случае нарушения данных требований будут применены нормы законодательства РФ. Редакция портала не несет ответственности за комментарии и материалы пользователей, размещенные на сайте ProKazan.ru и его субдоменах. <br>Материалы, отмеченные знаком <img src='http://prokazan.ru/template/standart/info.png' style='margin:0 3px; widtrh:12px; height:12px; vertical-align:middle;'>, размещены на коммерческой основе (реклама). Возрастная категория сайта 16+</div>
                </noindex>
                <noindex>
                    <div class="EndText">
                        <div class='CopyEnd'>Городской портал Казани — <a href="http://prokazan.ru">ProKazan.ru</a> © 2009-2016<br>Портал работает по технологии «<a href="http://smisite.ru" target="_blank">ProSmi</a>» © 2012-2016</div>
                        <noindex>
                            <div class='Counters' style=''><a href="http://umedia.pro" target="_blank" rel="nofollow"><img src="http://umedia.pro/template/logosites.png" title="U-media" alt="U-media" style="width:55px !important; height:31px; border:none; margin:0 5px;"></a>

                                <!--LiveInternet counter-->
                                <script type="text/javascript">
                                    <!--
                                    document.write("<a href='http://www.liveinternet.ru/click;ProKazan' " +
                                        "target=_blank><img src='//counter.yadro.ru/hit;ProKazan?t18.6;r" +
                                        escape(document.referrer) + ((typeof(screen) == "undefined") ? "" :
                                            ";s" + screen.width + "*" + screen.height + "*" + (screen.colorDepth ?
                                                screen.colorDepth : screen.pixelDepth)) + ";u" + escape(document.URL) +
                                        ";h" + escape(document.title.substring(0, 80)) + ";" + Math.random() +
                                        "' alt='' title='LiveInternet: показано число просмотров за 24" +
                                        " часа, посетителей за 24 часа и за сегодня' " +
                                        "border='0' width='88' height='31'></a>")
                                    //-->

                                </script>
                                <!--/LiveInternet-->

                                <!-- Rating@Mail.ru counter -->
                                <script type="text/javascript">
                                    //<![CDATA[
                                    var _tmr = _tmr || [];
                                    _tmr.push({
                                        id: "1684050",
                                        type: "pageView",
                                        start: (new Date()).getTime()
                                    });
                                    (function(d, w) {
                                        var ts = d.createElement("script");
                                        ts.type = "text/javascript";
                                        ts.async = true;
                                        ts.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//top-fwz1.mail.ru/js/code.js";
                                        var f = function() {
                                            var s = d.getElementsByTagName("script")[0];
                                            s.parentNode.insertBefore(ts, s);
                                        };
                                        if (w.opera == "[object Opera]") {
                                            d.addEventListener("DOMContentLoaded", f, false);
                                        } else {
                                            f();
                                        }
                                    })(document, window);
                                    //]]>

                                </script><noscript><div style="position:absolute;left:-10000px;">
<img rel="nofollow" src="//top-fwz1.mail.ru/counter?id=1684050;js=na" style="border:0;" height="1" width="1" alt="Рейтинг@Mail.ru" />
</div></noscript>
                                <!-- //Rating@Mail.ru counter -->
                                <!-- Rating@Mail.ru logo -->
                                <a href="http://top.mail.ru/jump?from=1684050">
<img src="//top-fwz1.mail.ru/counter?id=1684050;t=479;l=1" 
style="border:0;" height="31" width="88" rel="nofollow" alt="Рейтинг@Mail.ru" /></a>
                                <!-- //Rating@Mail.ru logo -->


                                <script>
                                    (function(i, s, o, g, r, a, m) {
                                        i['GoogleAnalyticsObject'] = r;
                                        i[r] = i[r] || function() {
                                            (i[r].q = i[r].q || []).push(arguments)
                                        }, i[r].l = 1 * new Date();
                                        a = s.createElement(o),
                                            m = s.getElementsByTagName(o)[0];
                                        a.async = 1;
                                        a.src = g;
                                        m.parentNode.insertBefore(a, m)
                                    })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
                                    ga('create', 'UA-25924782-2', 'auto');
                                    ga('send', 'pageview');

                                </script>

                                <!-- Yandex.Metrika counter -->
                                <script type="text/javascript">
                                    (function(w, c) {
                                        (w[c] = w[c] || []).push(function() {
                                            try {
                                                w.yaCounter7655743 = new Ya.Metrika({
                                                    id: 7655743,
                                                    enableAll: true,
                                                    webvisor: true
                                                });
                                            } catch (e) {}
                                        });
                                    })(window, "yandex_metrika_callbacks");

                                </script>
                                <script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script><noscript><span><img src="//mc.yandex.ru/watch/7655743" style="position:absolute; left:-9999px;" alt="" /></span></noscript>
                                <!-- /Yandex.Metrika counter -->
                                <script type="text/javascript">
                                    var _gaq = _gaq || [];
                                    _gaq.push(['_setAccount', 'UA-39062606-1']);
                                    _gaq.push(['_setDomainName', 'prokazan.ru']);
                                    _gaq.push(['_trackPageview']);

                                    (function() {
                                        var ga = document.createElement('script');
                                        ga.type = 'text/javascript';
                                        ga.async = true;
                                        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                                        var s = document.getElementsByTagName('script')[0];
                                        s.parentNode.insertBefore(ga, s);
                                    })();

                                </script>

                                <a href="http://smisite.ru" rel="nofollow"><img src="http://smisite.ru/light.png" title="Сайт работает по технологии ProSMI"></a>

                                <!-- tns-counter.ru -->
                                <script type="text/javascript">
                                    (function(win, doc, cb) {
                                        (win[cb] = win[cb] || []).push(function() {
                                            try {
                                                tnsCounterProkazan_ru = new TNS.TnsCounter({
                                                    'account': 'prokazan_ru',
                                                    'tmsec': 'prokazan_total'
                                                });
                                            } catch (e) {}
                                        });

                                        var tnsscript = doc.createElement('script');
                                        tnsscript.type = 'text/javascript';
                                        tnsscript.async = true;
                                        tnsscript.src = ('https:' == doc.location.protocol ? 'https:' : 'http:') +
                                            '//www.tns-counter.ru/tcounter.js';
                                        var s = doc.getElementsByTagName('script')[0];
                                        s.parentNode.insertBefore(tnsscript, s);
                                    })(window, this.document, 'tnscounter_callback');

                                </script>
                                <noscript>
	<img src="//www.tns-counter.ru/V13a****prokazan_ru/ru/UTF-8/tmsec=prokazan_total/" width="0" height="0" alt="" />
</noscript>
                                <!--/ tns-counter.ru -->

                                <!-- orion wam tag -->
                                <script type='text/javascript'>
                                    var wamid = '3575';
                                    var typ = '3';
                                    var Wvar = [];
                                    Wvar.push("domain", location.host);
                                    (function() {
                                        var w = document.createElement("script");
                                        w.type = "text/javascript";
                                        w.src = document.location.protocol + "//cstatic.weborama.fr/js/wam/customers/wamfactory_dpm.wildcard.min.js?rnd=" + new Date().getTime();
                                        w.async = true;
                                        var body = document.getElementsByTagName('script')[0];
                                        body.parentNode.insertBefore(w, body);
                                    })();

                                </script>
                                <!-- end orion wam tag -->


                            </div>
                        </noindex>
                    </div>
                </noindex>
                <div class="C20"></div>
                <div class="C30"></div>
            </div>
            <!-- MAIN-DESIGN END -->
        </div>
    </div>
    <div id="LeftCont"></div>
    <div id="RightCont"></div>
    <div class="actionBanner1 ToUp" id="Banner-17-1"></div>
    <div class="actionBanner2 ToUp" id="Banner-25-1"></div>
    <div class='bannerdown' id='Banner-3-1'></div>

    <script id="js-mpf-mediator-init" data-counter="2771411">
        ! function(e) {
            function t(t, n) {
                if (!(n in e)) {
                    for (var r, a = e.document, i = a.scripts, o = i.length; o--;)
                        if (-1 !== i[o].src.indexOf(t)) {
                            r = i[o];
                            break
                        }
                    if (!r) {
                        r = a.createElement("script"), r.type = "text/javascript", r.async = !0, r.defer = !0, r.src = t, r.charset = "UTF-8";;
                        var d = function() {
                            var e = a.getElementsByTagName("script")[0];
                            e.parentNode.insertBefore(r, e)
                        };
                        "[object Opera]" == e.opera ? a.addEventListener ? a.addEventListener("DOMContentLoaded", d, !1) : e.attachEvent("onload", d) : d()
                    }
                }
            }
            t("//top-fwz1.mail.ru/js/code.js", "_tmr"), t("//mediator.imgsmail.ru/2/mpf-mediator.min.js", "_mediator")
        }(window);

    </script>

    <input type="hidden" id="BoxCount" value="0" /><input type="hidden" id="gidvk" value="0" /><input type="hidden" id="DomainId" value="0" /><input type="hidden" id="UserId" value="0" />
    <div class="actionBanner ToUp" id="Banner-17-1">
        <div class="closeban">Закрыть [X]</div>
    </div>
    <div id="ProPodlogka"></div>
    <script type="text/javascript">
        adv.banner(function(webmd) {
            console.log("Showing banner 1", webmd);
            var wmclusters = webmd["clusters"].toString();
            var audiences = webmd["audiences"].toString();
            wmclusters = wmclusters.replace(/,/g, ":");
            audiences = audiences.replace(/,/g, ":");
            window.Ya.adfoxCode.create({
                ownerId: 251657,
                containerId: "adfox_149699503432432432",
                params: {
                    pp: "g",
                    ps: "cjof",
                    p2: "fkuh",
                    puid1: webmd["socio_demographics"]["age"],
                    puid2: webmd["socio_demographics"]["gender"],
                    puid3: webmd["socio_demographics"]["social_class"],
                    puid4: wmclusters,
                    puid5: audiences
                },
                onRender: function() {
                    console.log("otag_rendered");
                },
                onError: function(error) {
                    console.log("otag_error");
                },
                onStub: function() {
                    console.log("otag_stub");
                }
            });
        });

    </script>
</body>
<script type="text/javascript">
    var _acic = {
        dataProvider: 10
    };
    (function() {
            var e = document.createElement("script");
            e.type = "text/javascript";
            e.async = true ";var t=document.getElementsByTagName("
            script ")[0];t.parentNode.insertBefore(e,t)})()

</script>

</html>
<!-- CountSQL: 5 | TimeSQL: 0.001c. | TotalTime: 0.243c. -->
