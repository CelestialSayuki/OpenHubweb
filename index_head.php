<!DOCTYPE html>
<html>
 <head>
  <meta charset="utf-8" />
  <title>Apple苹果产品参数中心页面顶部框架</title> 
  <meta name='description' content="本站收集了 Apple 苹果公司的各产品信息，通过点击图片选中产品进行参数对比，让您更了解自己手上的产品信息。 ">
  <meta name='keywords' content="Apple苹果产品参数中心,iPad Pro,iPad Air,iPad Mini,iPad,iPhone,airpods,silicon">
  <meta name="robots" content="noindex,nofllow,noarchive">
  <link rel="stylesheet" type="text/css" href="./public-static/css/kekems.css?15257" /> 
  <link rel="stylesheet" type="text/css" href="./public-static/css/kekems.dark.css?15257" /> 
 </head> 
 <body> 
  <div class="top_max"> 
   <div class="top_left_logo">
    <a href="./index_main.php" target="admin_main"><img src="./public-static/img/logo.png?5224" height="30px" width="30px" style="margin-top:5px;" /></a>
   </div>
   <div class="top_title" id="top_title">开源的 Apple 苹果产品参数中心 / OpenHubweb</div>
   <div class="top_right_menu">
    <a href="javascript:void(0);" onclick="indexmain()">首页</a> - 
    <a href="javascript:void(0);" onclick="thisrefresh()">刷新</a> -
    <a href="https://www.github.com/" target="_blank">源码</a>
   </div>
  </div>   
 </body>
</html>
<script>
function thisrefresh(){window.parent.frames["admin_main"].location.reload();}
function indexmain(){window.parent.frames["admin_main"].location.href="./index_main.php";}
</script>
