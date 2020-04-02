<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:103:"E:\project\ncov-report-manage-system-PHP\src\public/../application/superadmin2020\view\login\index.html";i:1585826897;}*/ ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>疫情防控管理系统</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
   
   <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/static/admin/css/font.css">
    <link rel="stylesheet" href="/static/admin/css/xadmin.css">
    <link rel="stylesheet" href="/static/admin/css/swiper-3.4.2.min.css">
    <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript" src="/static/admin/js/swiper-3.4.2.jquery.min.js"></script>
    <script src="/static/admin/lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="/static/admin/js/xadmin.js"></script>

</head>
<body>
    <div class="login-logo"><h1>总后台</h1></div>
    <div class="login-box">
        <form class="layui-form layui-form-pane" method="post" action="<?php echo url('superadmin2020/login/dologin'); ?>">
              
            <h3>登录你的帐号</h3>
            <label class="login-title" for="username">帐号</label>
            <div class="layui-form-item">
                <label class="layui-form-label login-form"><i class="iconfont">&#xe6b8;</i></label>
                <div class="layui-input-inline login-inline">
                  <input type="text" name="username" lay-verify="required" placeholder="请输入你的帐号" autocomplete="off" class="layui-input">
                </div>
            </div>
            <label class="login-title" for="password">密码</label>
            <div class="layui-form-item">
                <label class="layui-form-label login-form"><i class="iconfont">&#xe82b;</i></label>
                <div class="layui-input-inline login-inline">
                  <input type="password" name="password" lay-verify="required" placeholder="请输入你的密码" autocomplete="off" class="layui-input">
                </div>
            </div>
            <?php if($verify): ?>
            <div>
                <label class="login-title" for="password">验证码</label>
                <p align="center" style="margin:10px auto 10px;"><img src="<?php echo captcha_src(); ?>" onclick="this.src='<?php echo captcha_src(); ?>'"/></p>
                <div class="layui-form-item">
                    <label class="layui-form-label login-form"><i class="iconfont">&#xe6a4;</i></label>
                    <div class="layui-input-inline login-inline"> 
                     <input type="input" name="code" class="layui-input" placeholder="请输入您的验证码" lay-verify="required" autocomplete="off">
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <div class="form-actions">
                <button class="btn btn-warning pull-right" lay-submit lay-filter="login"  type="submit">登录</button> 
            </div>
        </form>
    </div>
	
   
</body>
</html>