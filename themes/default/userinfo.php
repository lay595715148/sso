<!DOCTYPE html>
<!--[if IE 7 ]><html class="no-js ie ie7 lte7 lte8 lte9" lang="<?=$lan?>"> <![endif]-->
<!--[if IE 8 ]><html class="no-js ie ie8 lte8 lte9" lang="<?=$lan?>"> <![endif]-->
<!--[if IE 9 ]><html class="no-js ie ie9 lte9" lang="<?=$lan?>"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html class="no-js ie ie9 lte9" lang="<?=$lan?>"><!--<![endif]-->
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></meta>
  <title><?=$title?></title>
  <link rel='shortcut icon' href='/image/favicon.ico'></link>
  <link type='text/css' href='/jquery/jquery-ui-1.10.3.custom/css/flick/jquery-ui.css' media='all' rel='stylesheet'></link>
  <link type='text/css' href='/css/base.css' rel='stylesheet'></link>
  <link type='text/css' href='/jquery/pnotify-1.2.0/devnote.css' rel='stylesheet'></link>
  <link type='text/css' href='/jquery/pnotify-1.2.0/oxygen/icons.css' rel='stylesheet'></link>
  <link type='text/css' href='/jquery/pnotify-1.2.0/jquery.pnotify.default.css' rel='stylesheet'></link>
  <link type='text/css' href='/jquery/pnotify-1.2.0/jquery.pnotify.default.icons.css' rel='stylesheet'></link>
  <link type='text/css' href='/css/main.css' rel='stylesheet'></link>
  <!--[if lt IE 9]><script type="text/javascript" src="/library/html5shiv.js"></script><![endif]-->
  <script type="text/javascript" src="/library/underscore/underscore.js"></script>
  <script type="text/javascript" src="/jquery/jquery-1.10.2.js"></script>
  <script type="text/javascript" src="/jquery/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.js"></script>
  <script type="text/javascript" src="/jquery/jquery.cookie.js"></script>
  <script type="text/javascript" src="/jquery/pnotify-1.2.0/jquery.pnotify.js"></script>
  <script type="text/javascript" src="/js/core.js"></script>
</head>
<body>
  <header class="header">
    <div class="header-inner">
      <h1 class="header-logo">
        <a href="/login" target="_blank">
          <img src="http://mimg.127.net/logo/126logo.gif">
        </a>
      </h1>
    <nav class="header-nav">
      <a href="/login" target="_blank">个人中心</a>
      <a href="/login" target="_blank">其他</a>
      <a href="/login" target="_blank">帮助信息</a>
    </nav></div></header>
  <section class="main">
    <div class="main-inner">
    <?php print_r($vars);?>
    </div>
  </section>
  <footer class="footer">
    <div class="footer-inner">
      <nav class="footer-nav">
        <a href="/login" target="_blank">关于我们</a>
        <a href="/login" target="_blank" class="last">隐私政策</a>
        <span>|</span>
        <span class="copyright">公司版权所有©1997-2014</span>
        <span>|</span>
        <a href="/login" target="_blank">ICPB2-20090191</a>
      </nav>
    </div>
  </footer>
  <iframe src="about:blank" name="login-frame" class="login-frame">登录iframe</iframe>
</body>
</html>
