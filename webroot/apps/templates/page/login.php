<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>登录</title>

    <link rel="stylesheet" type="text/css" href="/static/css/style.css">

    <script type="text/javascript" src="/static/js/jq.min.js"></script>
    <script type="text/javascript" src="/static/js/vector.js"></script>

</head>
<body>

<div id="container">
    <div id="output">
        <div class="containerT">
            <h1>用户登录</h1>
            <form class="form" id="entry_form">
                <input type="text" placeholder="用户名" id="entry_name" name="name">
                <input type="password" placeholder="密码" id="entry_password" name="password">
                <button type="button" id="entry_btn">登录</button>
                <div id="prompt" class="prompt"><a href="/page/signup">暂无账号，去注册</a></div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        Victor("container", "output");   //登陆背景函数
        $("#entry_name").focus();
        $(document).keydown(function(event){
            if(event.keyCode==13){
                $("#entry_btn").click();
            }
        });

        $("#entry_btn").click(function () {
            let data = $("#entry_form").serialize()
            $.post("/page/login", data, function(result){
                if (result.code != 0) {
                    alert(result.message);
                    return false;
                }

                window.location.href = "/page/chatroom";
            });
        });
    });
</script>

</body>
</html>
