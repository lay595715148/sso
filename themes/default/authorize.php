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
      <div class="login-box">
        <div class="login-header">
          <div class="login-normal">普通登录</div>
          <div class="login-other">第三方登录</div>
        </div>
        <div class="login-form">
          <form action="" method="post">
            <?php if($user) {?><div class="login-line-info">
              <span class="login-form-span info">检测到您已登录，点击授权并登录</span>
            </div>
            <div class="login-line-info">
              <span class="login-form-span name">用户名：<?=$user['name']?></span>
            </div>
            <?php } else {?><div class="login-line-input">
              <b class="ico ico-uid"></b>
              <input name="username" class="login-form-input"/>
              <label class="login-placeholder login-placeholder-show">用户名</label>
            </div>
            <div class="login-line-input">
              <b class="ico ico-pwd"></b>
              <input name="password" type="password" class="login-form-input"/>
              <label class="login-placeholder login-placeholder-show">密码</label>
            </div>
            <?php }?>
            <div class="login-line-check">
              <?php if($error) {?><span class="failure"><?=$error?></span>
              <?php } else if($user) {?><input id="otherlogin" type="hidden" name="otherlogin" value=""/>
              <?php } else {?><input id="register" type="hidden" name="register" value=""/>
              <?php }?>
              <?php if($is_verify) {?><img src="/verify" onclick="this.src=this.src"/>
              <input id="verifyCode" type="text" name="verify_code" value=""/>
              <?php }?>
            </div>
            <div class="login-line-btn">
              <?php if($user) {?><button type="submit" onclick="javascript:$(&quot;#otherlogin&quot;).val(&quot;&quot;);" class="btn login-button">
                <span class="ui-button-text">授权并登录</span>
              </button>
              <button type="submit" onclick="javascript:$(&quot;#otherlogin&quot;).val(&quot;1&quot;);" class="btn login-button login-button-other">
                <span class="ui-button-text">其他帐号</span>
              </button>
              <?php } else {?><button type="submit" onclick="javascript:$(&quot;#register&quot;).val(&quot;&quot;);" class="btn login-button">
                <span class="ui-button-text">登  录</span>
              </button>
              <button type="submit" onclick="javascript:$(&quot;#register&quot;).val(&quot;1&quot;);" class="btn login-button login-button-reg">
                <span class="ui-button-text">注  册</span>
              </button>
              <?php }?>
            </div>
            <div class="login-line-cfg">
              <div class="lay_main" id="lay_main">
                <div class="lay_accredit_con">
                  <!-- <p class="cnt_wording">该网站已有超过1万用户使用QQ登录</p> -->
                  <p class="app_site_wording"><a class="accredit_site" id="accredit_site_link" href="<?=$client['location']?>" target="_blank"><?=$client['clientName']?></a>将获得以下权限：</p>
                  <div class="accredit_info" id="accredit_info">
                    <ul class="accredit_info_op">
                      <li class="select_all_li">
                        <input type="checkbox" id="select_all" class="checkbox oauth_checkbox_all" checked="checked">
                        <label class="oauth_item_title" for="select_all">全选</label>
                      </li>
                      <?php foreach ($scope as $s) {?><li>
                        <input name="api_choose" type="checkbox" class="checkbox oauth_checkbox" id="item_<?=$s['id']?>" value="<?=$s['id']?>" title="<?=$s['basis']?'默认授权 不可更改':''?>" checked="checked" <?=$s['basis']?'disabled="true"':''?>>
                        <label class="oauth_item_title"><?=$s['description']?></label>
                      </li>
                      <?php }?>
                    </ul>
                    <script type="text/javascript">
                    $(function() {
                        $('#select_all').on('click', function() {
                            //var checked = $('#select_all').attr('checked');
                            if($(this).is(':checked')) {
                                $('input[name=api_choose]').prop('checked', true);
                            } else {
                                $('input[name=api_choose]').prop('checked', false);
                                $('input[name=api_choose]:disabled').prop('checked', true);
                            }
                        });
                        $('input[name=api_choose]').on('click', function() {
                            var i = 0, j = 0;
                            $('input[name=api_choose]').each(function () {
                                j++; //计算复选框的总数 (altered this issue by Jli)
                                if($(this).is(':checked')) {
                                    i++;
                                }
                            })
                            if(i == j) {
                                $("#select_all").prop("checked", true);
                            } else {
                                $("#select_all").prop("checked", false);
                            }
                        });
                    });
                    </script>
                  </div>
                  <div class="oauth_tips_div">
                    <p class="oauth_tips">授权后表明你已同意 <a href="http://sso.laysoft.cn/agreement" target="_blank">登录服务协议</a></p>
                  </div>
                </div>
              </div>
              <?php var_dump($user);?>
            </div>
          </form>
        </div>
      </div>
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
  <script type="text/javascript" src="/js/login.js"></script>
</body>
</html>
