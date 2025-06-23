<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>壁纸详情页面 / Apple 苹果产品参数中心 HubWeb.cn</title>
  <meta name='description' content="本站收集了 Apple 苹果公司的各产品信息，通过点击图片选中产品进行参数对比，让您更了解自己手上的产品信息。 ">
   <meta name='keywords' content="Apple苹果产品参数中心,iPad Pro,iPad Air,iPad Mini,iPad,iPhone,airpods,silicon">
  <!-- 引入 Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="./css/style.css?1678" />
  <script src="./js/wallpaper.js?15271"></script>
</head>

<body id="mainLayout">
  <div class="container mt-5">
    <!-- 返回按钮 -->
    <button class="btn btn-primary back-button" onclick="goBack()">返回上一页</button>

    <!-- 标题部分 -->
    <div class="text-center mb-4">
      <h1 id="title">加载中...</h1>
      <p class="lead" id="subtitle">加载中...</p>
      <small class="lead" id="date" style="font-size: 0.9rem;">加载中...</small>
    </div>

    <!-- 内容部分 -->
    <div class="row justify-content-center">
      <div class="col-md-8">
        <!-- 上方图片 -->
        <img id="image-top" class="content-image">

        <!-- 内容文字 -->
        <p id="content-text"></p>

        <!-- 壁纸下载标题 -->
        <div class="list-header">下载 (Download)</div>

        <!-- 壁纸列表部分 -->
        <div class="list-container" id="wallpapers">
          <!-- 动态生成的壁纸内容 -->
        </div>
      </div>
    </div>
  </div>

  <!-- 引入 Bootstrap JS 和依赖 -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>

    
    // 模拟从数据库获取的 JSON 数据
    const data = {
      "id": 1,
      "title": "Apple Perth City",
      "subtitle": "苹果在澳大利亚珀斯市中心步行区福雷斯特广场开设一家新的零售店。",
      "date": "2025-06-23",
      "content": {
        "text": "2025 年 6 月 20 日星期五，苹果宣布在澳大利亚新设一家新店：Apple Perth City。新地点位于珀斯商业区福雷斯特广场 (Forrest Place) 的中心，苹果公司原先在珀斯市中心附近的海伊街 (Hay Street) 已有一家门店。该门店于 2010 年开业，将于 6 月 25 日星期三永久关闭，而规模更大、更新的福雷斯特广场门店将于当地时间 6 月 27 日星期五上午 9 点盛大开业。",
        "image_top": "./img/20250623-apple-perth-city.webp"
      },
      "wallpapers": [
    {
        "name": "壁纸下载",
        "sizes": [
            {
                "label": "Mac",
                "url": "http://cdn.hubweb.cn/Wallpaper/Apple_Store/Apple_Perth_City/Perth-city-store_INT-Wallpapers_v1Desktop.png"
            },
            {
                "label": "iPad",
                "url": "http://cdn.hubweb.cn/Wallpaper/Apple_Store/Apple_Perth_City/Perth-city-store_INT-Wallpapers_v1iPad.png"
            },
            {
                "label": "iPhone",
                "url": "http://cdn.hubweb.cn/Wallpaper/Apple_Store/Apple_Perth_City/Perth-city-store_INT-Wallpapers_v1iPhone.png"
            }
        ]
    }
]    };
    
    // 动态渲染页面内容
    document.getElementById('title').innerText = data.title;
    document.getElementById('subtitle').innerText = data.subtitle;
    document.getElementById('date').innerText = data.date;

    // 渲染内容部分
    document.getElementById('image-top').src = data.content.image_top;
    document.getElementById('content-text').innerText = data.content.text;

    // 渲染壁纸列表
    const wallpapersContainer = document.getElementById('wallpapers');
    data.wallpapers.forEach(wallpaper => {
      const downloadButtons = wallpaper.sizes.map(size => `
        <a href="${size.url}" target="_blank" class="btn btn-primary btn-sm download-btn" download>${size.label}</a>
      `).join('');

      const wallpaperItem = `
        <div class="list-item">
          <span class="list-title">${wallpaper.name}</span>
          <div class="download-buttons">
            ${downloadButtons}
          </div>
        </div>
      `;
      wallpapersContainer.innerHTML += wallpaperItem;
    });

    // 返回上一页功能
    function goBack() {
      window.history.back();
    }
  </script>
</body>
</html>
