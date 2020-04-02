<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:113:"E:\project\ncov-report-manage-system-PHP\src\public/../application/superadmin2020\view\index\update_password.html";i:1585210556;s:95:"E:\project\ncov-report-manage-system-PHP\src\application\superadmin2020\view\public\header.html";i:1585210556;s:93:"E:\project\ncov-report-manage-system-PHP\src\application\superadmin2020\view\public\left.html";i:1585210556;s:95:"E:\project\ncov-report-manage-system-PHP\src\application\superadmin2020\view\public\footer.html";i:1585210556;}*/ ?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>疫情防控后台管理系统</title>
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
<!-- 顶部开始 -->
<div class="container">
    <div class="logo"><a href="<?php echo url('index/index'); ?>">疫情防控总后台</a></div>
    <div class="open-nav"><i class="iconfont">&#xe699;</i></div>
    <ul class="layui-nav right" lay-filter="">
        <li class="layui-nav-item">
            <a href="javascript:;"><?php echo $myname; ?></a>
            <dl class="layui-nav-child"> <!-- 二级菜单 -->
                <dd><a href="<?php echo url('login/index'); ?>">退出</a></dd>
            </dl>
        </li>
    </ul>
</div>
<!-- 顶部结束 -->
<!-- 中部开始 -->
<div class="wrapper">
    <!-- 左侧菜单开始 -->
<div class="left-nav">
    <div id="side-nav">
        <ul id="nav">
            <li class="list" current>
                <a href="<?php echo url('index/index'); ?>">
                    <i class="iconfont">&#xe761;</i>
                    首页
                </a>
            </li>
            <li class="list">
                <a href="<?php echo url('organization/addorg'); ?>">
                    <i class="iconfont">&#xe70b;</i>
                    开通新机构
                </a>
            </li>
            <li class="list">
                <a href="<?php echo url('organization/index'); ?>">
                    <i class="iconfont">&#xe70b;</i>
                    所有机构列表
                </a>
            </li>
            <li class="list">
                <a href="<?php echo url('index/updatepassword'); ?>">
                    <i class="iconfont">&#xe70b;</i>
                    修改本人密码
                </a>
            </li>
        </ul>
    </div>
</div>
<!-- 左侧菜单结束 -->

    <!-- 右侧主体开始 -->
    <div class="page-content">
        <div class="content">
            <!-- 右侧内容框架，更改从这里开始 -->
            <blockquote class="layui-elem-quote">
                首次登录需要重置密码!
            </blockquote>
            <form class="layui-form xbs" method="post" action="<?php echo url('superadmin2020/index/updatepw'); ?>">
                <div class="layui-form-pane" style="text-align: center;">
                    <div class="layui-form-item" style="display: inline-block;">
                        <label class="layui-form-label xbs768">原密码</label>
                        <div class="layui-input-inline xbs768">
                            <input type="password" class="layui-input" lay-verify="required" placeholder="原密码验证" name="old_passwd">
                        </div>
                    </div>

                </div>
                <div class="layui-form-pane" style="text-align: center;">
                    <div class="layui-form-item" style="display: inline-block;">
                        <label class="layui-form-label xbs768">新密码</label>
                        <div class="layui-input-inline xbs768">
                            <input type="password" class="layui-input" lay-verify="required" placeholder="字母和数字组合,不小于8位" name="new_passwd1">
                        </div>
                    </div>

                </div>
                <div class="layui-form-pane" style="text-align: center;">
                    <div class="layui-form-item" style="display: inline-block;">
                        <label class="layui-form-label xbs768">重复密码</label>
                        <div class="layui-input-inline xbs768">
                            <input type="password" class="layui-input"  lay-verify="required" placeholder="字母和数字组合,不小于8位" name="new_passwd2">
                        </div>
                    </div>

                </div>
                <div class="layui-form-pane" style="text-align: center;">
                    <div class="layui-form-item" style="display: inline-block;">
                        <button class="layui-btn" lay-submit="" >提交</button>

                    </div>

                </div>

            </form>

            <!-- 右侧内容框架，更改从这里结束 -->
        </div>
    </div>
    <!-- 右侧主体结束 -->
</div>
<!-- 中部结束 -->




<script>

</script>
</body>
</html>

