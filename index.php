<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>开源的 Apple 苹果产品参数中心 / OpenHubweb</title>
    <meta name="description" content="一个收集 Apple 产品信息的现代化参数中心，通过参数对比，让您更了解苹果产品。">
    <meta name="keywords" content="Apple,苹果产品参数,Apple Silicon,iPad,iPhone,Mac,AirPods,OpenHubweb">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" media="(prefers-color-scheme: light)" content="#f8f9fa">
    <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#161b22">
    <meta name="apple-mobile-web-app-title" content="苹果产品参数">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link href="./public-static/img/apple-touch-icon.jpg?2556" rel="apple-touch-icon-precomposed">
    <link rel="shortcut icon" href="./public-static/img/favicon.ico">
    <link rel="bookmark" href="./public-static/img/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style type="text/css">
.main-content {
    position: relative;
}

.dock-container {
    position: absolute;
    right: 20px;
    bottom: 20px;
    background-color: var(--light-glass-bg, rgba(240, 240, 240, 0.7));
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-radius: var(--border-radius, 12px);
    border: 1px solid var(--light-border, rgba(0, 0, 0, 0.1));
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    padding: 8px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    z-index: 2000;
    opacity: 0;
    transform: translateY(20px);
    pointer-events: none;
    transition: opacity 0.4s ease, transform 0.4s ease;
}

.dock-container.visible {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
}

.dock-item {
    width: 60px;
    height: 60px;
    background-color: rgba(0,0,0,0.1);
    border-radius: 10px;
    cursor: pointer;
    background-size: cover;
    background-position: center;
    transition: transform 0.2s ease;
    border: 1px solid rgba(255,255,255,0.2);
}

.dock-item:hover {
    transform: scale(1.15);
}

@media (prefers-color-scheme: dark) {
    .dock-container {
        background-color: var(--dark-glass-bg, rgba(30, 30, 30, 0.7));
        border-color: var(--dark-border, rgba(255, 255, 255, 0.15));
        box-shadow: 0 8px 25px rgba(0,0,0,0.4);
    }
}

.dock-preview {
    position: absolute;
    background-color: rgba(20, 20, 20, 0.85);
    color: #fff;
    padding: 6px 12px;
    border-radius: 7px;
    font-size: 13px;
    font-weight: 500;
    z-index: 10001;
    white-space: nowrap;
    opacity: 0;
    transform: translateY(10px);
    pointer-events: none;
    transition: opacity 0.2s ease-out, transform 0.2s ease-out;
}

.dock-preview::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 50%;
    transform: translateX(-50%) rotate(45deg);
    width: 10px;
    height: 10px;
    background-color: rgba(20, 20, 20, 0.85);
}

@media (prefers-color-scheme: dark) {
    .dock-preview, .dock-preview::after {
        background-color: rgba(40, 40, 40, 0.9);
    }
}

.window-ghost {
    position: absolute;
    width: 100%;
    height: 100%;
    background: #ccc;
    border-radius: 12px;
    z-index: 10000;
    transform-origin: top left;
    pointer-events: none;
    offset-path: none;
    animation-fill-mode: forwards;
    animation-duration: 0.7s;
    animation-timing-function: cubic-bezier(0.6, 0, 0.8, 1);
}

@keyframes genie-minimize {
    0% {
        offset-distance: 0%;
        transform: scale(1);
        opacity: 0.8;
    }
    100% {
        offset-distance: 100%;
        transform: scale(0.05);
        opacity: 0;
    }
}

@keyframes genie-restore {
    0% {
        offset-distance: 100%;
        transform: scale(0.05);
        opacity: 0;
    }
    100% {
        offset-distance: 0%;
        transform: scale(1);
        opacity: 0.8;
    }
}

.macos-window {
    position: absolute;
    width: 800px;
    height: 600px;
    background-color: rgba(240, 240, 240, 0.75);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: var(--border-radius, 12px);
    padding: 0;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    z-index: 100;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    min-width: 400px;
    min-height: 300px;
    animation: shrink-and-settle 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid var(--light-border, rgba(0, 0, 0, 0.1));
}

@media (prefers-color-scheme: dark) {
    .macos-window {
        background-color: rgba(40, 40, 40, 0.7);
        border-color: var(--dark-border, rgba(255, 255, 255, 0.15));
    }
    .macos-window-header {
        border-bottom-color: var(--dark-border, rgba(255, 255, 255, 0.12));
    }
    .macos-window-body {
        background-color: var(--dark-glass-bg, rgba(30, 30, 30, 0.8));
    }
    .macos-window-title {
        color: var(--dark-text-secondary);
    }
}

.macos-window-header {
    height: 40px;
    display: flex;
    align-items: center;
    padding: 0 12px;
    background-color: transparent;
    cursor: move;
    flex-shrink: 0;
}

.macos-window-controls {
    display: flex;
    gap: 8px;
}

.control-btn {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 1px solid rgba(0,0,0,0.1);
}
.control-close { background-color: #ff5f57; }
.control-minimize { background-color: #ffbd2e; }
.control-maximize { background-color: #28c940; }

.macos-window-title {
    color: var(--light-text-secondary);
    font-weight: 600;
    margin: 0 auto;
    padding-right: 60px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
    
.macos-window.is-closing {
    animation: dissipate-like-smoke 0.3s ease-in forwards;
}

.macos-window-body {
    flex-grow: 1;
    overflow-y: auto;
    background-color: var(--light-glass-bg, rgba(255, 255, 255, 0.7));
    margin: 0 5px 5px 5px;
    border-radius: 8px;
}
    
.macos-window-body::-webkit-scrollbar {
    display: none;
}

.macos-window-body {
    scrollbar-width: none;
}

.macos-window-body .cd-products-comparison-table {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
    height: 100%;
}
    
@keyframes shrink-and-settle {
0% {
    opacity: 0;
    transform: scale(1.5);
    filter: blur(10px);
}
40% {
    opacity: 1;
    transform: scale(0.85);
    filter: blur(0);
}
100% {
    transform: scale(1.0);
}
}
    @keyframes dissipate-like-smoke {
        0% {
            opacity: 1;
            transform: translate(0, 0) scale(1) rotate(0deg);
            filter: blur(0);
        }
        100% {
            opacity: 0;
            transform: scale(1.25);
            filter: blur(5px);
        }
    }

    .dynamic-content-overlay.is-closing {
        animation: dissipate-like-smoke 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
    }

    @media (prefers-color-scheme: dark) {
        .dynamic-content-overlay {
            background-color: var(--dark-glass-bg, rgba(22, 27, 34, 0.75));
            border-color: var(--dark-border, rgba(255, 255, 255, 0.15));
            box-shadow: 0 12px 24px rgba(0,0,0,0.4);
        }
    }

    :root {
        --light-bg: #f8f9fa;
        --dark-bg: #0d1117;
        --light-glass-bg: rgba(255, 255, 255, 0.5);
        --dark-glass-bg: rgba(22, 27, 34, 0.6);
        --light-text: #212529;
        --dark-text: #c9d1d9;
        --light-text-secondary: #586069;
        --dark-text-secondary: #8b949e;
        --light-border: rgba(0, 0, 0, 0.1);
        --dark-border: rgba(255, 255, 255, 0.15);
        --accent-color: #007aff;
        --accent-color-hover: #0056b3;
        --sidebar-width: 260px;
        --header-height: 60px;
        --border-radius: 12px;
        --transition-speed: 0.3s;
        --light-glass-shadow: 0 8px 16px rgba(0, 0, 0, 0.08), 0 4px 8px rgba(0, 0, 0, 0.05);
        --dark-glass-shadow: 0 8px 16px rgba(0, 0, 0, 0.3), 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    *, *::before, *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    
    body {
        font-family: 'SF Pro SC', 'SF Pro Text', 'PingFang SC', sans-serif;
        background: linear-gradient(135deg, #e0e5ec, #f0f5f9);
        color: var(--light-text);
        transition: background-color var(--transition-speed) ease, color var(--transition-speed) ease;
        overflow-x: hidden;
        font-size: 14px;
    }
    
    a {
        color: var(--accent-color);
        text-decoration: none;
        transition: color var(--transition-speed);
    }
    a:hover {
        color: var(--accent-color-hover);
    }
    ul { list-style: none; }
    
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background-color: rgba(0,0,0,0.05); }
    ::-webkit-scrollbar-thumb { background-color: #c1c1c1; border-radius: 6px; }
    ::-webkit-scrollbar-thumb:hover { background-color: #a8a8a8; }
            
    #app-container {
        display: grid;
        grid-template-columns: var(--sidebar-width) 1fr;
        grid-template-rows: var(--header-height) 1fr;
        height: 100vh;
        width: 100vw;
    }
            
    .sidebar {
        grid-row: 1 / 3;
        background: var(--light-glass-bg);
        border-right: 1px solid var(--light-border);
        padding: 20px 0;
        overflow-y: auto;
        transition: all var(--transition-speed) ease-in-out;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        z-index: 10;
        border-top-left-radius: var(--border-radius);
        border-bottom-left-radius: var(--border-radius);
        box-shadow: var(--light-glass-shadow);
    }
    .sidebar-menu { list-style: none; }
            
    .sidebar-menu li.has-submenu {
        display: block;
    }
    
    .sidebar-menu li.has-submenu.open {
    }
    .sidebar-menu li.has-submenu > a {
        align-self: unset;
    }
    .sidebar-menu ul {
        list-style: none;
        padding-left: 25px;
        overflow: hidden;
    }
    
    .sidebar-menu li.has-submenu > ul {
        max-height: 0;
        opacity: 0;
        padding-top: 0;
        transition: max-height 0.5s ease-in-out, opacity 0.4s ease-in-out, padding-top 0.5s ease-in-out;
    }
    
    .sidebar-menu li.has-submenu.open > ul {
        opacity: 1;
        padding-top: 4px;
    }
    
    .sidebar-menu ul ul {
        padding-left: 20px;
    }
    
    .sidebar-menu li a {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        text-decoration: none;
        color: var(--light-text);
        font-weight: 500;
        font-size: 14px;
        transition: color var(--transition-speed), background-color var(--transition-speed);
        border-radius: 8px;
        margin: 0px 10px;
    }
    
    .sidebar-menu ul a {
        color: var(--light-text-secondary);
        font-weight: 400;
        padding: 11px 15px;
        border-radius: 6px;
        transition: all 0.2s ease-out;
    }
    .sidebar-menu ul a:hover {
        background-color: rgba(0, 122, 255, 0.1);
        transform: translateX(5px);
        color: var(--accent-color);
    }
    .sidebar-menu > li > a:hover {
        background-color: var(--accent-color);
        color: white !important;
    }
    .sidebar-menu > li > a.active {
        background-color: var(--accent-color);
        color: white !important;
    }
    .sidebar-menu > li > a.active .bi, .sidebar-menu > li > a:hover .bi {
        color: white;
    }
    .sidebar .bi {
        font-size: 1.2rem;
        margin-right: 15px;
        width: 20px;
        text-align: center;
        transition: transform var(--transition-speed) ease, color var(--transition-speed) ease;
    }
    .sidebar-menu > li > a.active .bi {
        transform: scale(1.1);
    }
    
    .main-header {
        grid-column: 2 / 3;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 25px;
        background: var(--light-glass-bg);
        border-bottom: 1px solid var(--light-border);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        z-index: 5;
        box-shadow: var(--light-glass-shadow);
    }
    .main-header .top_left_logo { display: flex; align-items: center; gap: 15px; }
    .main-header .top_left_logo img { transition: transform 0.4s ease; }
    .main-header .top_left_logo:hover img { transform: rotate(360deg); }
    .main-header .top_title { font-size: 1.1rem; font-weight: 600; color: var(--light-text) }
    .main-header .top_right_menu a {
        text-decoration:none;
        color: var(--light-text-secondary);
        font-weight: 500;
        margin: 0 8px;
        transition: color var(--transition-speed);
    }
            
    .main-content {
        grid-column: 2 / 3;
        overflow-y: auto;
        padding: 40px;
        background: linear-gradient(to bottom, rgba(255,255,255,0.05), rgba(255,255,255,0));
    }
    
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(50px); } to { opacity: 1; transform: translateY(0); } }
            
    .card, .title, .div {
        animation: fadeInUp 0.6s ease-out forwards;
        opacity: 0;
    }
    
    .hero-banner {
        width: 100%;
        height: 350px;
        border-radius: var(--border-radius);
        margin-bottom: 40px;
        background-image: url('./public-static/img/2025-hubweb-devices.webp');
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
        overflow: hidden;
        animation: fadeIn 1s ease-out;
        box-shadow: var(--light-glass-shadow);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .hero-banner::after {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: linear-gradient(45deg, rgba(0,0,0,0.3), rgba(0,0,0,0.05));
        border-radius: var(--border-radius);
        z-index: 0;
    }
    .hero-banner h1 {
        color: white;
        font-size: 2.5rem;
        font-weight: 700;
        z-index: 1;
        text-shadow: 0 4px 12px rgba(0,0,0,0.6);
    }
    
    .title {
        margin-top: 40px;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--light-border);
    }
    .title strong {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--light-text);
    }
    .title span {
        font-size: 0.9rem;
        color: var(--light-text-secondary);
    }
    
    .card {
        background-color: var(--light-glass-bg);
        border: 1px solid var(--light-border);
        border-radius: var(--border-radius);
        padding: 25px;
        margin-bottom: 20px;
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        transition: transform var(--transition-speed) ease, box-shadow var(--transition-speed) ease;
        box-shadow: var(--light-glass-shadow);
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1), 0 6px 12px rgba(0,0,0,0.08);
    }
    
    .card ul {
        list-style-type: none;
        padding-left: 5px;
        line-height: 1.8;
    }
    .card ul li {
        padding: 6px 0;
        position: relative;
        padding-left: 25px;
    }
    .card ul li::before {
        content: '\F28A';
        font-family: 'bootstrap-icons';
        position: absolute;
        left: 0;
        color: var(--accent-color);
        font-weight: bold;
    }
    
    #update-history-list {
        overflow-y: hidden;
        position: relative;
        transition: height 0.8s ease-in-out;
    }
    #update-history-list li::before {
        content: '\F633';
    }
    .more-btn-li {
        list-style: none !important;
        padding: 15px 0 !important;
        text-align: center;
    }
    .more-btn-li::before { content: '' !important; }
    .show-more-btn {
        background: var(--accent-color);
        color: white !important;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        transition: background-color var(--transition-speed), box-shadow var(--transition-speed);
        box-shadow: 0 4px 8px rgba(0, 122, 255, 0.3);
    }
    .show-more-btn:hover {
        background: var(--accent-color-hover);
        box-shadow: 0 6px 12px rgba(0, 122, 255, 0.4);
    }
    
    .div {
        padding: 15px;
        border-radius: var(--border-radius);
        font-size: 0.95rem;
        line-height: 1.8;
        background-color: var(--light-glass-bg);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        box-shadow: var(--light-glass-shadow);
        border: 1px solid var(--light-border);
        margin-bottom: 20px;
    }
    #divdown2 {
        font-size: 1.2rem;
        font-weight: 500;
        color: var(--accent-color);
        text-align: center;
        padding: 20px;
    }
    
    .spen_ab, .spen_ab a {
        color: var(--light-text-secondary);
        font-size: 12px;
    }
    .spen_ab {font-style:oblique;}
    
    @media (prefers-color-scheme: dark) {
        body {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            color: var(--dark-text);
        }
        .card {
            background-color: var(--dark-glass-bg);
            border: 1px solid var(--dark-border);
            box-shadow: var(--dark-glass-shadow);
        }
        .sidebar, .main-header {
            background: var(--dark-glass-bg);
            border-color: var(--dark-border);
            box-shadow: var(--dark-glass-shadow);
        }
        .sidebar {
            border-top-left-radius: var(--border-radius);
            border-bottom-left-radius: var(--border-radius);
        }
        .sidebar-menu li a { color: var(--dark-text); }
        .sidebar-menu ul a { color: var(--dark-text-secondary); }
        .main-header .top_title { color: var(--dark-text); }
        .main-header .top_right_menu a { color: var(--dark-text-secondary); }
        .main-header .top_right_menu a:hover, .div a:hover { color: #8ab4f8; }
        a { color: #8ab4f8; }
        .title strong { color: var(--dark-text); }
        .title span { color: var(--dark-text-secondary); }
        .div, .spen_ab, .spen_ab a {
            color: var(--dark-text-secondary);
            background-color: var(--dark-glass-bg);
            box-shadow: var(--dark-glass-shadow);
            border: 1px solid var(--dark-border);
        }
        ::-webkit-scrollbar-track { background-color: rgba(255,255,255,0.05); }
        ::-webkit-scrollbar-thumb { background-color: #444; }
        .card:hover { box-shadow: 0 12px 24px rgba(0,0,0,0.4), 0 6px 12px rgba(0,0,0,0.3); }
        .hero-banner {
            box-shadow: var(--dark-glass-shadow);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .hero-banner::after {
            background: linear-gradient(45deg, rgba(0,0,0,0.5), rgba(0,0,0,0.2));
        }
        .show-more-btn {
            box-shadow: 0 4px 8px rgba(0, 122, 255, 0.4);
        }
        .show-more-btn:hover {
            box-shadow: 0 6px 12px rgba(0, 122, 255, 0.5);
        }
        .main-content {
            background: linear-gradient(to bottom, rgba(13,17,23,0.05), rgba(13,17,23,0));
        }
    }
    
    @media (max-width: 1024px) {
        :root { --sidebar-width: 220px; }
        .main-content { padding: 25px; }
    }
    
    @media (max-width: 768px) {
        #app-container {
            grid-template-columns: 1fr;
            grid-template-rows: auto auto 1fr;
            height: auto;
        }
        .sidebar {
            grid-row: 2;
            width: 100%;
            border-right: none;
            border-bottom: 1px solid var(--light-border);
            padding: 10px;
            border-radius: 0;
            box-shadow: none;
        }
        .main-header {
            grid-row: 1;
            grid-column: 1;
            box-shadow: none;
        }
        .main-content {
            grid-row: 3;
            grid-column: 1;
        }
        .hero-banner {
            height: 250px;
            box-shadow: none;
        }
        .hero-banner h1 { font-size: 1.8rem; }
        body { font-size: 90%; }
        .card, .div {
            box-shadow: none;
        }
    }
</style>
</head>

<body>
    <div id="app-container">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li>
                    <a href="#">
                        <i class="bi bi-cpu"></i>Apple Silicon</a>
                    <ul>
                        <li>
                            <a href="./apple-silicon/chip-a/">苹果 A 系列</a>
                        </li>
                        <li>
                            <a href="./apple-silicon/chip-s/">苹果 S 系列</a>
                        </li>
                        <li>
                            <a href="./apple-silicon/chip-m/">苹果 M 系列</a>
                        </li>
                        <li>
                            <a href="./apple-silicon/chip-r/">苹果 R 系列</a>
                        </li>
                        <li>
                            <a href="./apple-silicon/chip-t/">苹果 T 系列</a>
                        </li>
                        <li>
                            <a href="./apple-silicon/chip-u/">苹果 U 系列</a>
                        </li>
                        <li>
                            <a href="./apple-silicon/chip-w/">苹果 W 系列</a>
                        </li>
                        <li>
                            <a href="./apple-silicon/chip-h/">苹果 H 系列</a>
                        </li>
                        <li>
                            <a href="./apple-silicon/chip-c/">苹果 C 系列</a>
                        </li>
                        <li>
                            <a href="./apple-silicon/chip-coprocessor/">协处理器</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-apple"></i>Apple 设备</a>
                    <ul>
                        <li>
                            <a href="#">Mac 系列</a>
                            <ul class="sub-menu">
                                <li>
                                    <a href="./apple-device/mac/macbook/">MacBook</a>
                                </li>
                                <li>
                                    <a href="./apple-device/mac/macbook-air/">MacBook Air</a>
                                </li>
                                <li>
                                    <a href="./apple-device/mac/macbook-pro/">MacBook Pro</a>
                                </li>
                                <li>
                                    <a href="./apple-device/mac/mac-imac/">iMac</a>
                                </li>
                                <li>
                                    <a href="./apple-device/mac/mac-mini/">Mac mini</a>
                                </li>
                                <li>
                                    <a href="./apple-device/mac/mac-studio/">Mac Studio</a>
                                </li>
                                <li>
                                    <a href="./apple-device/mac/mac-pro/">Mac Pro</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">iPad 系列</a>
                            <ul class="sub-menu">
                                <li>
                                    <a href="./apple-device/ipad/ipad-pro/">iPad Pro</a>
                                </li>
                                <li>
                                    <a href="./apple-device/ipad/ipad-air/">iPad Air</a>
                                </li>
                                <li>
                                    <a href="./apple-device/ipad/ipad/">iPad</a>
                                </li>
                                <li>
                                    <a href="./apple-device/ipad/ipad-mini/">iPad mini</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">Watch 系列</a>
                            <ul class="sub-menu">
                                <li>
                                    <a href="./apple-device/watch/watch-ultra/">AW Ultra</a>
                                </li>
                                <li>
                                    <a href="./apple-device/watch/watch-series/">AW Series</a>
                                </li>
                                <li>
                                    <a href="./apple-device/watch/watch-se/">AW SE</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="./apple-device/iphone/">iPhone</a>
                        </li>
                        <li>
                            <a href="./apple-device/vision/">Vision</a>
                        </li>
                        <li>
                            <a href="./apple-device/airpods/">AirPods</a>
                        </li>
                        <li>
                            <a href="./apple-device/homepod/">HomePod</a>
                        </li>
                        <li>
                            <a href="./apple-device/tv/">Apple TV</a>
                        </li>
                        <li>
                            <a href="./apple-device/ipod/">iPod touch</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-motherboard"></i>其他参数</a>
                    <ul>
                        <li>
                            <a href="./apple-device/iphone/cmos.php">iPhone 摄像头</a>
                        </li>
                        <li>
                            <a href="./apple-other/battery/">电池详细信息</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-tools"></i>苹果工具</a>
                    <ul>
                        <li>
                            <a href="./apple-tools/support/">系统兼容情况</a>
                        </li>
                        <li>
                            <a href="./apple-tools/os-version/">系统发布历史</a>
                        </li>
                        <li>
                            <a href="./apple-tools/price/">设备发售价格</a>
                        </li>
                        <li>
                            <a href="./apple-tools/document/">苹果文档汇总</a>
                        </li>
                        <li>
                            <a href="https://checkcoverage.apple.com">官网序列号查询</a>
                        </li>
                        <li>
                            <a href="./apple-tools/memory-latency/">内存延迟测试</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-bar-chart-line"></i>统计报表</a>
                    <ul>
                        <li>
                            <a href="./apple-report/cpu/">CPU 跑分报表</a>
                        </li>
                        <li>
                            <a href="./apple-report/gpu/">GPU 跑分报表</a>
                        </li>
                        <li>
                            <a href="./apple-report/ai/">AI 跑分报表</a>
                        </li>
                        <li>
                            <a href="./apple-report/disk/">Disk 读写性能</a>
                        </li>
                        <li>
                            <a href="./apple-report/battery/">充电峰值功率</a>
                        </li>
                        <li>
                            <a href="./apple-report/random-latency/">随机访问延迟</a>
                        </li>
                        <li>
                            <a href="./apple-report/ios-memory-budget/">内存占用限制</a>
                        </li>
                        <li>
                            <a href="./apple-report/frequency-voltage/">DFVS 频率电压表</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="./top-wallpaper/">
                        <i class="bi bi-image"></i>壁纸中心</a>
                </li>
                <li>
                    <a href="./top-description/">
                        <i class="bi bi-journal-text"></i>参数解读</a>
                </li>
                <li>
                    <a href="./top-discuss/">
                        <i class="bi bi-chat-quote"></i>QQ讨论群</a>
                </li>
            </ul>
        </aside>
        <header class="main-header">
            <div class="top_left_logo">
                <a href="#">
                    <img src="./public-static/img/logo.png?5224" height="30px" width="30px">
                </a>
                <div class="top_title" id="top_title">OpenHubweb / 苹果参数中心</div>
            </div>
            <div class="top_right_menu">
                <a href="https://www.github.com/CelestialSayuki/OpenHubweb">源码</a>
            </div>
        </header>
        <main class="main-content">
            <div class="hero-banner">
                <h1>现代化的 Apple 产品参数中心</h1>
            </div>
            <div class="title" id="divdown1" style="display: none;">
                <strong>倒计时（苹果 Apple 全球开发者大会）🥳</strong>
                <br>
                <span>北京时间 2025 年 6 月 10 日凌晨 1 点（太平洋时间 2025 年 6 月 9 日上午 10 点）。</span>
            </div>
            <div class="div" id="divdown2" style="display: none;">加载中......</div>
            <div class="title">
                <strong>未来工作</strong>
                <br>
                <span>说明：针对 OpenHubweb 的改造，以求与原网站功能形成区分。</span>
            </div>
            <div class="card">
                <ul>
                    <li>
                        <span>采用完全不同的UI</span>
                    </li>
                    <li>
                        <span>在网页内进行入群审核</span>
                    </li>
                    <li>
                        <span>支持各种测试提交成绩</span>
                    </li>
                    <li>
                        <span>增加Speedometer测试</span>
                    </li>
                    <li>
                        <span>完善系统支持性页面</span>
                    </li>
                    <li>
                        <span>提供部分测试工具的下载</span>
                    </li>
                </ul>
            </div>
            <div class="title">
                <strong>更新记录</strong>
                <br>
                <span>说明：网站收录的信息不定期更新以保证正确性，该处显示当前网站的更新日志。</span>
            </div>
            <div class="card">
                <ul id="update-history-list">
                    <li>
                        <span>2025.06.27：开始逐步替换UI</span>
                    </li>
                    <li>
                        <span>2025.06.23：本网站 Fork 原 hubweb.cn 并与之分离，修改了系统支持页面的样式，增加Xserve等机器统计，删掉所有广告，测试对比和统计报表合并，删除交互式色域比较，删除频率测试，删除打赏列表，允许复制文本</span>
                    </li>
                    <li>
                        <span>2025.06.23：收录 iPhone 16e 设备内部图 (文件来源 @WekiHome)，切换到内部图后双击内部图封面即可放大;</span>
                    </li>
                    <li>
                        <span>2025.06.16：收录 Apple Silicon A、M 芯片宣传口号信息 (来源: Apple 官方网站);</span>
                    </li>
                    <li>
                        <span>2025.06.14：调整 Apple Mac 设备主要参数项 (增加容量参数，合并机身接口参数)，增加内容可读性;</span>
                    </li>
                    <li class="more-btn-li">
                        <a href="javascript:void(0);" class="show-more-btn" id="show-more-updates">点击加载更多</a>
                    </li>
                </ul>
                <template id="more-updates-template">
                    <li>
                        <span>2025.06.12：新增 Apple Silicon A、M、R 芯片内置 I/O 接口信息 (Camera Serial Interface、USB4/Thunderbolt、PCIe);</span>
                    </li>
                    <li>
                        <span>2025.06.11：收录 Apple A12X/Z Bionic 芯片结构图，调整固定网站表格类标题样式，增加内容可读性;</span>
                    </li>
                    <li>
                        <span>2025.06.10：根据 WWDC25 更新网站系统兼容情况、系统发布历史等，收录新版 Metal 特征汇总表，收录 Apple Silicon CPU 优化指南 4.0;</span>
                    </li>
                    <li>
                        <span>2025.06.07：优化网站 Dark 模式样式，使其更贴合夜间访问场景;</span>
                    </li>
                    <li>
                        <span>2025.05.28：Apple Silicon A、S、M、T 系列芯片添加芯片代号信息，修正微架构描述;</span>
                    </li>
                    <li>
                        <span>2025.05.23：补充 M4 Pro/Max 芯片缓存信息及芯片图;</span>
                    </li>
                    <li>
                        <span>2025.05.19：调整“随机访问延迟”统计报表图例展示样式;</span>
                    </li>
                    <li>
                        <span>2025.05.18：网站上线“随机访问延迟”统计报表，CPU 跑分报表下架“CPU Full Random Latency”模块;</span>
                    </li>
                    <li>
                        <span>2025.05.09：网站上线“内存延迟测试”工具，以纳秒级精度评估内存读写性能，通过测量不同数据块的操作时间计算平均延迟;</span>
                    </li>
                    <li>
                        <span>2025.04.24：修正 A18 芯片 GPU 频率信息;</span>
                    </li>
                    <li>
                        <span>2025.04.15：网站上线新板块“DFVS 频率电压表”，这里收集了芯片在运行时其频率及电压的对应关系;</span>
                    </li>
                    <li>
                        <span>2025.04.13：修正 A14、M1、M2、M3、M4 系列芯片 ANE 频率信息 (信息来源 GitHub CelestialSayuki/ASi-Mac-DVFS);</span>
                    </li>
                    <li>
                        <span>2025.04.12：修正 iPad 4 设备存储规格，修正“系统发布历史”部分发布时间错误的问题;</span>
                    </li>
                    <li>
                        <span>2025.04.09：修正 iPhone 5s、6s、11 Pro 系列宣传口号，修正 iMac 显示屏 PPI 信息，修正 iPod touch 尺寸重量信息;</span>
                    </li>
                    <li>
                        <span>2025.04.07：为了更清晰地进行展示与管理，网站上关于 Watch 设备的内容拆分为 Ultra、Series、SE 这三个独立的系列板块;</span>
                    </li>
                    <li>
                        <span>2025.04.03：收录 iOS 1~12、tvOS 9~12、watchOS 1~5 系统历史版本发布信息到“系统发布历史”板块，上线“QQ讨论群”板块;</span>
                    </li>
                    <li>
                        <span>2025.04.02：修复“交互式色域比较”模块图片的颜色描述文件丢失问题，修复“壁纸中心”的媒体文件无法正常加载;</span>
                    </li>
                    <li>
                        <span>2025.03.27：新开设图标子栏目到“壁纸中心”，同时收录 371 个高清 Apple Logo 到该栏目下 (感谢 @鹽水儿 提供素材);</span>
                    </li>
                    <li>
                        <span>2025.03.26：调整“壁纸中心”页面样式，增加分页功能，修复详情页返回上一页出现的页面样式错误问题;</span>
                    </li>
                    <li>
                        <span>2025.03.24：收录苹果配件设计指南 R25 版，收录 iPad (A16) iOS 程序崩溃测试数据 (数据来源新浪微博 @Microsoft_Inc);</span>
                    </li>
                    <li>
                        <span>2025.03.18：收录历代 Apple TV 设备发售价格，更新“设备发售价格”页面展示样式，调整网站在 Windows 平台下访问的滚动条样式;</span>
                    </li>
                    <li>
                        <span>2025.03.13：更新 Apple C1 芯片参数，更新 iPhone 16e 摄像头 COMS 信息，更新 iPad Air (M3)、iPad (A16) 设备图片及技术规格地址;</span>
                    </li>
                    <li>
                        <span>2025.03.12：更新 M3 Ultra 芯片型号信息及 ANE 型号信息;</span>
                    </li>
                    <li>
                        <span>2025.03.10：新增 Xcode 基准测试到 CPU 跑分报表 (数据来源开源项目: devMEremenko/XcodeBenchmark)，修正 A18 芯片型号信息;</span>
                    </li>
                    <li>
                        <span>2025.03.08：更新网站 GPU、AI 统计报表样式，支持根据图例数值自动重新排序处理器型号信息;</span>
                    </li>
                    <li>
                        <span>2025.03.06：收录 M3 Ultra 芯片信息，更新 C1 芯片信息，收录 Mac Studio (2025)、MacBook Air (13"/15",M4,2025) 设备信息;</span>
                    </li>
                    <li>
                        <span>2025.03.05：收录 iPad (A16)、iPad Air (M3) 设备参数，更新 A、M 系列芯片参数，为 Apple Vision Pro 添加 Apple Intelligence 颜色渐变效果;</span>
                    </li>
                    <li>
                        <span>2025.03.02：更新 Apple C1 芯片参数，更新 iPhone 16e 设备参数，收录 APL0098、APL0278、APL0298、APL2298 芯片信息参数;</span>
                    </li>
                    <li>
                        <span>2025.02.28：网站下线“IPSW 固件查询”、“QQ讨论群”板块，更新 M3、M4 芯片 P-CPU 内存延迟曲线;</span>
                    </li>
                    <li>
                        <span>2025.02.20：收录 iPhone 16e 设备参数，新增 Apple Silicon C 系列芯片参数，收录 A12 (P) SPEC 2017 测试数据;</span>
                    </li>
                    <li>
                        <span>2025.02.15：网站上线新板块“壁纸中心”，这里收集了包括节日、零售店开业以及各类活动的壁纸供您欣赏，可免费下载。</span>
                    </li>
                    <li>
                        <span>2025.02.13：收录 A18 Pro 芯片跑 LLaMa/TinyLlama/Phi-2 语言模型的 Token 生成速度;</span>
                    </li>
                    <li>
                        <span>2025.02.12：更新 H2 芯片使用设备，修正 iPhone 部分设备宣传口号;</span>
                    </li>
                    <li>
                        <span>2025.02.10：更新网站 CPU 统计报表样式，支持根据图例数值自动重新排序处理器型号信息，更新 GeekBench 跑分数据;</span>
                    </li>
                    <li>
                        <span>2025.02.05：修正 Apple Watch S3 宣传语，修正 iPod touch 5 运行内存信息，修正 A4 芯片 L2 缓存信息;</span>
                    </li>
                    <li>
                        <span>2025.01.26：更新 A18 裸片尺寸 (数据来源 @万扯淡)，更新 T2 芯片使用设备;</span>
                    </li>
                    <li>
                        <span>2025.01.20：调整合并部分 iPad 及 iPhone 设备参数项;</span>
                    </li>
                    <li>
                        <span>2025.01.15：网站下线“Web 基准测试”，移除 AMD、Intel 显卡的 Mac GPU 跑分数据，移除 Intel Mac CPU 跑分数据;</span>
                    </li>
                    <li>
                        <span>2025.01.12：增加 Apple Silicon A、M 芯片媒体处理引擎硬件加速信息，修正 MacBook Pro 设备续航时长信息;</span>
                    </li>
                    <li>
                        <span>2024.12.24：收录历年 Apple 供应商名单到“苹果工具-苹果文档汇总”;</span>
                    </li>
                    <li>
                        <span>2024.12.20：收录 AMD、Intel 显卡的 Mac GPU 跑分数据，微调“系统兼容情况”页面样式;</span>
                    </li>
                    <li>
                        <span>2024.12.16：更新 Mac 系列雷雳、USB、HDMI、DP 信息，收录 Intel Mac CPU 跑分数据，完善 Apple Mx Pro/Max/Ultra 芯片 ANE 型号信息;</span>
                    </li>
                    <li>
                        <span>2024.12.15：新增 Apple A、M 芯片 GPU L2 缓存信息参数，更新 A10 Fusion CPU 频率信息;</span>
                    </li>
                    <li>
                        <span>2024.12.13：更新 AirPods 4 电池容量信息 (感谢 QQ 群友 @zsx 提供信息，来自三星 SDI);</span>
                    </li>
                    <li>
                        <span>2024.12.03：更新 MacBook、Air、Pro 充电规格及 HDMI 接口信息，更新 x86 架构 Mac 内核架构信息，更新 Apple Silicon ML 加速器描述;</span>
                    </li>
                    <li>
                        <span>2024.11.24：更新 M3 裸片尺寸 (数据来源 @万扯淡)，更新 MacBook Air 设备外设扩展描述;</span>
                    </li>
                    <li>
                        <span>2024.11.20：收录 Apple 供应商名单到“苹果工具-苹果文档汇总”，更新 M4 芯片 CPU 频率信息;</span>
                    </li>
                    <li>
                        <span>2024.11.14：收录 M4 Max LLaMa 7B 模型 Token 生成速度;</span>
                    </li>
                    <li>
                        <span>2024.11.12：收录 M4 TinyLlama 1.1B 模型 Token 生成速度，更新 M1、M1 Pro 裸片尺寸 (数据来源 @万扯淡);</span>
                    </li>
                    <li>
                        <span>2024.11.11：收录 M4 LLaMa 7B 模型 Token 生成速度，更新 M4 芯片 SPEC2017 测试数据及 CPU 随机延迟曲线;</span>
                    </li>
                    <li>
                        <span>2024.11.10：收录 M4 Pro LLaMa 7B 模型 Token 生成速度，调整统计报表最大显示宽度;</span>
                    </li>
                    <li>
                        <span>2024.11.09：收录 M4 Pro/Max 频率测试数据 (数据来源 @Geekerwan)，更新 A12X/Z 裸片尺寸 (数据来源 @万扯淡);</span>
                    </li>
                    <li>
                        <span>2024.11.08：调整 CPU 跑分报表分组，增加同代芯片的不同规格跑分数据，新增 CineBench CPU 性能跑分，修正 M4 Pro/Max E-Core 频率;</span>
                    </li>
                    <li>
                        <span>2024.11.06：收录 M 芯片 SPEC2017 测试数据，调整 CPU 跑分报表样式为单核与多核分数叠加展示;</span>
                    </li>
                    <li>
                        <span>2024.10.31：新增 MacBook Pro (14/16", M4/Pro/Max) 设备信息，收录 M4 Max 芯片信息，收录 U 芯片内部图;</span>
                    </li>
                    <li>
                        <span>2024.10.30：新增 Mac mini (2024) 设备信息，收录 M4 Pro 芯片信息;</span>
                    </li>
                    <li>
                        <span>2024.10.29：新增 iMac (24 英寸, 2024) 设备信息，更新 M4 芯片信息，收录 A18、A18 Pro 芯片 die 模具图，收录苹果配件设计指南 R24 版;</span>
                    </li>
                    <li>
                        <span>2024.10.18：收录 3DMark Solar Bay 光线追踪和 Steel Nomad Light 性能跑分测试数据;</span>
                    </li>
                    <li>
                        <span>2024.10.16：收录 iPhone 16 / Plus 内部图 (文件来源 @WekiHome)，新增 iPad mini (A17 Pro) 设备信息，更新 Apple A 芯片信息;</span>
                    </li>
                    <li>
                        <span>2024.10.09：修正 T2、A10、A10X、A14、M1 系列芯片内核架构信息 (信息来源 GitHub llvm/llvm-project);</span>
                    </li>
                    <li>
                        <span>2024.10.08：更新 A18 Pro 裸片尺寸 (信息来源 @万扯淡);</span>
                    </li>
                    <li>
                        <span>2024.09.27：更新 iPhone 16 系列无线局域网信息，更新 A18 芯片型号信息;</span>
                    </li>
                    <li>
                        <span>2024.09.26：收录 Apple Silicon ANE 运行频率，修正 A18 系列 CPU、GPU 信息，收录 A18 系列芯片频率测试数据，更新参数解读参数说明;</span>
                    </li>
                    <li>
                        <span>2024.09.25：更新 M1、M2 芯片宣传图，收录 A18 Pro SPEC 测试数据 (数据来源 @小白测评);</span>
                    </li>
                    <li>
                        <span>2024.09.24：收录 iPhone 16 Pro / Pro Max 内部图 (文件来源 @WekiHome)，收录 A17 Pro 芯片 CPU 频率测试数据 (信息来源 @Howl);</span>
                    </li>
                    <li>
                        <span>2024.09.23：Apple Silicon S 芯片与 Apple SiP IC 信息两个页面合并展示，增加 S 芯片 Die 信息及裸片尺寸 (信息来源 @万扯淡);</span>
                    </li>
                    <li>
                        <span>2024.09.22：完善 iPhone 16 Pro 系列电池信息，更新 A18 及 A18 Pro 型号信息及芯片图;</span>
                    </li>
                    <li>
                        <span>2024.09.21：收录 iPhone 16 系列自助维修手册，更新 iPhone 16 系列基带信息，收录 iPhone 16 基础款 CMOS 信息 (信息来源新浪微博 @肥威);</span>
                    </li>
                    <li>
                        <span>2024.09.20：收录苹果配件设计指南 Release 23 版，更新 iPhone 16 Pro 摄像头 CMOS 信息 (感谢 QQ 群友 @ほしぞらの夢 提供抓包信息);</span>
                    </li>
                    <li>
                        <span>2024.09.19：更新 AirPods 4、Apple Watch S10、iPhone 16 技术规格，收录 A17 (Coll-P)、A18 系列 (Tupai-P、Tahiti-P) CPU 随机延迟数据;</span>
                    </li>
                    <li>
                        <span>2024.09.18：更新 H2 芯片 die 模具图，更新 iPhone 16 系列参数信息，更新 A18 系列芯片型号、GPU、ANE 信息;</span>
                    </li>
                    <li>
                        <span>2024.09.15：更新网站 Apple 设备样式，为支持 Apple Intelligence 的设备名称添加颜色渐变效果;</span>
                    </li>
                    <li>
                        <span>2024.09.11：更新 Apple Silicon A18 系列、S10 SiP 芯片信息，更新 iPhone 16 系列摄像头 CMOS 参数;</span>
                    </li>
                    <li>
                        <span>2024.09.10：新增 A18、S10 SiP 芯片信息，新增 Apple Watch S10、AirPods 4、iPhone 16 系列等设备信息；</span>
                    </li>
                    <li>
                        <span>2024.09.09：定期更新 GeekBench CPU、GPU、AI 跑分数据，图表所展示的数据为设备性能的巅峰表现，微调统计报表展示样式;</span>
                    </li>
                    <li>
                        <span>2024.08.28：收录 Apple ANE 每核心缓存信息，修正 ANE SRAM 信息，修正 S 芯片 SLC 缓存信息，修正 M4 芯片 CPU 内核架构信息;</span>
                    </li>
                    <li>
                        <span>2024.08.27：苹果工具"配件设计指南"改名为"苹果文档汇总"，并收录历年 Metal 特征汇总表、Apple Silicon CPU 优化指南;</span>
                    </li>
                    <li>
                        <span>2024.08.23：收录 iPad Air M2 及 iPad Pro M4 设备磁盘读写性能数据，更新 CPU 及 GPU 跑分报表，更新 Apple Silicon GPU 及 NPU 信息;</span>
                    </li>
                    <li>
                        <span>2024.08.20：更新 GeekBench AI 跑分数据，微调网站 Dark 模式下的样式;</span>
                    </li>
                    <li>
                        <span>2024.08.17：更新 NE 跑分报表，收录 Mac 在 GeekBench AI 上的跑分数据，移除 GeekBench ML 跑分数据;</span>
                    </li>
                    <li>
                        <span>2024.08.16：收录 iPhone 及 iPad 在 GeekBench AI 上的跑分数据 (单精度、半精度及量化精度);</span>
                    </li>
                    <li>
                        <span>2024.08.15：更新 Apple Silicon M 系列全系芯片 Die 模具图 (tips：切换至 Die 图后双击图片可新窗口放大展示);</span>
                    </li>
                    <li>
                        <span>2024.08.02：收录 Mac Pro 2019 增强现实模型，收录 iPad Pro 11 英寸 (第 5 代) 程序崩溃测试数据 (感谢 QQ 群友 @寻找无双 提供测试数据)；</span>
                    </li>
                    <li>
                        <span>2024.08.01：收录网站所有 Apple 设备宣传口号，微调 Mac 系列 GPU 参数样式;</span>
                    </li>
                    <li>
                        <span>2024.07.23：收录 iPad Pro 11 英寸 (第 4 代) iOS 程序崩溃测试数据 (感谢 QQ 群友 @Serenitatis 提供测试数据)；</span>
                    </li>
                    <li>
                        <span>2024.07.22：新增 ANE 缓存信息，调整 Apple Silicon 参数对比页型号展示样式 (tips：切换至 Die 图后双击图片可新窗口放大展示);</span>
                    </li>
                    <li>
                        <span>2024.07.17：调整 iPhone 参数对比页型号展示样式 (tips：切换至内部图后双击图片可新窗口放大展示);</span>
                    </li>
                    <li>
                        <span>2024.07.16：更新 HomePod mini 外观颜色、传感器信息及增强现实模型，更新 M4 芯片裸片尺寸参数;</span>
                    </li>
                    <li>
                        <span>2024.06.17：收录 M4 die 模具图及芯片裸片尺寸参数;</span>
                    </li>
                    <li>
                        <span>2024.06.12：调整系统兼容情况界面，支持 Apple Intelligence 的设备系统图标增加光晕效果 (感谢群友 @鹽水儿 @寻找无双 提供思路);</span>
                    </li>
                    <li>
                        <span>2024.06.11：根据 WWDC24，更新网站系统兼容情况、系统发布历史信息、Apple 设备信息;</span>
                    </li>
                    <li>
                        <span>2024.06.04：更新 Apple 设备发布时间信息，微调 Apple 设备展示样式;</span>
                    </li>
                    <li>
                        <span>2024.06.01：更新 M2 芯片 GPU 规格信息，更新 GPU 跑分报表，微调 Apple Silicon 样式;</span>
                    </li>
                    <li>
                        <span>2024.05.28：更新 A、M 芯片内存位宽信息，更新 U 芯片宣传图，更新 iOS 程序崩溃对比页面样式;</span>
                    </li>
                    <li>
                        <span>2024.05.26：更新 A 芯片媒体引擎信息，更新 Web 基准测试数据;</span>
                    </li>
                    <li>
                        <span>2024.05.22：更新 M4 芯片内存规格，收录 M3 (Ibiza-P)、M4 (Donan-P) CPU 随机延迟数据、收录 A 芯片媒体引擎信息;</span>
                    </li>
                    <li>
                        <span>2024.05.19：更新 M4 芯片图，更新 iPad Pro (M4) 设备基带信息，完善 3DMark WLE 跑分数据 (感谢群友 @灼热的青莲 提供数据);</span>
                    </li>
                    <li>
                        <span>2024.05.16：更新 M4 芯片型号信息，收录 M4 芯片 CPU 频率测试数据 (数据来源：贴吧ID317760447);</span>
                    </li>
                    <li>
                        <span>2024.05.15：更新苹果配件设计指南 Release 22 版，更新 iPad Air (M2)、iPad Pro (M4) 设备图片及技术规格地址;</span>
                    </li>
                    <li>
                        <span>2024.05.14：更新 M3 全系芯片微架构信息，更新 M4 芯片 CPU、GPU 频率信息 (感谢群友 @LITTERTREE88 提供信息);</span>
                    </li>
                    <li>
                        <span>2024.05.12：新增 Apple Silicon 各芯片宣传图、结构图展示，修正网站 iPad 有误参数信息;</span>
                    </li>
                    <li>
                        <span>2024.05.11：更新 M4 芯片加速器、L1/L2/SLC 缓存及 GPU 信息 (感谢群友 @LITTERTREE66 提供信息);</span>
                    </li>
                    <li>
                        <span>2024.05.10：更新 M4 芯片微架构信息 (感谢群友 @LITTERTREE66 提供信息)，修正 A13 P-Core 解码发射信息 (数据来源：Anandtech)；</span>
                    </li>
                    <li>
                        <span>2024.05.09：修正 M4 芯片 P 核频率，收录 M4 芯片 CPU/GPU/NE 跑分数据，收录 M2 芯片裸片尺寸信息 (信息来源：万扯淡)；</span>
                    </li>
                    <li>
                        <span>2024.05.08：收录 iPad Air 11/13 英寸、iPad Pro 11/13 英寸设备信息，更新系统兼容情况、设备发售价格、M4 芯片参数信息；</span>
                    </li>
                    <li>
                        <span>2024.05.07：收录 M4 芯片参数，更新 Apple Silicon AMX 加速器信息；</span>
                    </li>
                    <li>
                        <span>2024.05.06：修正 A12 芯片频率测试数据，收录 CPU 随机延迟数据 (感谢QQ 群友 @win1010525 提供数据)；</span>
                    </li>
                    <li>
                        <span>2024.05.05：收录 CPU SPEC 测试数据 (数据来源：Geekerwan)；</span>
                    </li>
                    <li>
                        <span>2024.04.30：更新统计报表在 Dark 模式下的展示样式，收录 iPhone 15 系列及 HomePod mini 设备各颜色增强现实模型；</span>
                    </li>
                    <li>
                        <span>2024.04.29：收集 LLaMa/TinyLlama/Phi-2 语言模型 Q4_0/Q8_0 TG 生成速度，新增 Web 基准测试 (感谢群友 @win1010525 提供数据)；</span>
                    </li>
                    <li>
                        <span>2024.04.22：收录 M 芯片 NE 机器学习跑分报表数据；</span>
                    </li>
                    <li>
                        <span>2024.04.21：“苹果自助维修手册”合并到“Apple 设备”，方便用户快速定位设备及查看维修手册，帮助用户完成有限的维修；</span>
                    </li>
                    <li>
                        <span>2024.04.15：更新 S 系列芯片型号信息，更新 T 系列芯片型号信息、内存规格及微架构信息；</span>
                    </li>
                    <li>
                        <span>2024.04.12：收录芯片内置 AMX 矩阵运算单元信息；</span>
                    </li>
                    <li>
                        <span>2024.04.11：收录初代 iPhone 与 iPhone 3G 设备；</span>
                    </li>
                    <li>
                        <span>2024.04.08：收集 Mac 设备屏幕色彩信息，收录 Apple 设备型号标识信息，可以更好了解该设备在苹果内部的定位属于哪一代产品；</span>
                    </li>
                    <li>
                        <span>2024.04.07：更新 T2 芯片内存规格，更新 W1 芯片裸片尺寸信息及模具图，更新 H1 芯片模具图，更新 U1 芯片模具图；</span>
                    </li>
                    <li>
                        <span>2024.04.03：收录 Apple 设备重量数据；</span>
                    </li>
                    <li>
                        <span>2024.03.26：更新网站“Disk 读写性能”页面 Dark 模式下的样式，收录 M 芯片 Mac 设备磁盘读写性能数据，更新 M3 Max 芯片型号信息；</span>
                    </li>
                    <li>
                        <span>2024.03.25：原“ROM 极限读写”更名为“Disk 读写性能”，同时添加了设备的容量区分，可通过页面搜索模块查找对应的设备；</span>
                    </li>
                    <li>
                        <span>2024.03.21：更新 MacBook Air (13"/15",M3,2024) 图片及技术规格地址；</span>
                    </li>
                    <li>
                        <span>2024.03.20：更新 Apple Silicon 生产技术工艺；</span>
                    </li>
                    <li>
                        <span>2024.03.17：收录 Mac 部分设备充电峰值功耗；</span>
                    </li>
                    <li>
                        <span>2024.03.16：收录 2013 年到 2016 年发布的 iMac 设备，更新设备系统兼容情况；</span>
                    </li>
                    <li>
                        <span>2024.03.10：收录 2012.10 到 2014.12 发布的 MacBook Pro 设备，更新设备系统兼容情况；</span>
                    </li>
                    <li>
                        <span>2024.03.09：更新 CPU、GPU、NE 跑分报表数据，收录 M 芯片 OpenCL 跑分数据；</span>
                    </li>
                    <li>
                        <span>2024.03.05：收录 MacBook Air (13"/15",M3,2024) 设备，更新 M 系列芯片使用设备，更新设备系统兼容情况；</span>
                    </li>
                    <li>
                        <span>2024.03.04：收录 2014 年之前发布的 Mac 设备系统支持情况，更新"系统兼容情况"信息；</span>
                    </li>
                    <li>
                        <span>2024.02.27：更新 Apple Silicon 型号信息；</span>
                    </li>
                    <li>
                        <span>2024.02.23：更新 NE 跑分报表数据；</span>
                    </li>
                    <li>
                        <span>2024.02.22：新增“设备发售价格汇总”到苹果工具下，更新网站首页关于本站说明信息；</span>
                    </li>
                    <li>
                        <span>2024.02.21：新增“充电峰值功耗”统计报表，收录 Vision 电池详细信息，更新 A5 (APL2498/APL0498) 两款芯片模具图；</span>
                    </li>
                    <li>
                        <span>2024.02.19：新增 Watch 电池详细信息，更新 R1 芯片 CPU 信息；</span>
                    </li>
                    <li>
                        <span>2024.02.18：更新 R1 芯片图，修正 Mac mini 发布时间及 iPhone 摄像头参数，完善 iPhone 电池信息 (感谢 QQ 群友 @SiuK 提供数据)；</span>
                    </li>
                    <li>
                        <span>2024.02.04：更新 R1 型号信息、技术工艺及芯片图；</span>
                    </li>
                    <li>
                        <span>2024.02.01：收录 A 系列芯片裸片尺寸信息长宽信息 (资料参考：万扯淡、ChipWorks、Anandtech、Techinsights)；</span>
                    </li>
                    <li>
                        <span>2024.01.31：更新网站字体显示，使用 San Francisco (SF) 作为首选字族，在各尺寸的设备上浏览网站都能保持清晰和易读性；</span>
                    </li>
                    <li>
                        <span>2024.01.24：更新 A、M 系列芯片内存规格信息，收录 visionOS 历史版本发布信息，网站图片升级成 WebP 以保证图片质量以及提升加载速度；</span>
                    </li>
                    <li>
                        <span>2024.01.21：收录 Apple Silicon R 系列芯片，收录 Apple Vision Pro 增强现实查看 (需使用 Safari 浏览器加载)；</span>
                    </li>
                    <li>
                        <span>2024.01.20：新增 Vision 设备参数；</span>
                    </li>
                    <li>
                        <span>2024.01.11：更新 S 系列芯片 L2 缓存、SLC 缓存及 GPU 规格信息；</span>
                    </li>
                    <li>
                        <span>2024.01.05：更新 A17 Pro 芯片高清 Die 图，调整 Apple Silicon 参数展现样式并增加 GPU 型号、GPU 特性及 RAM 容量大小；</span>
                    </li>
                    <li>
                        <span>2023.12.14：新增新款 Mac 及 iPhone 15 系列自助维修手册；</span>
                    </li>
                    <li>
                        <span>2023.12.12：更新参数解读，修正神经引擎性能计算单位符号 (感谢 QQ 群友 @【&谪‘仙^】@LITTERTREE66 提供来源)；</span>
                    </li>
                    <li>
                        <span>2023.11.29：更新 iPhone 15 系列摄像头 CMOS 参数 (感谢 QQ 群友 @ほしぞらの夢 提供来源)；</span>
                    </li>
                    <li>
                        <span>2023.11.23：新增 S 系列芯片 SLC 缓存参数，更新 S9 SiP 芯片图，更新 S6 SiP 各 IC 芯片信息，修正 iPad Pro 输入输出信息；</span>
                    </li>
                    <li>
                        <span>2023.11.18：更新 U2 芯片型号；</span>
                    </li>
                    <li>
                        <span>2023.11.09：新增 M2 Max / M3 Max 芯片 CPU 频率测试数据；</span>
                    </li>
                    <li>
                        <span>2023.11.08：新增 iMac (24",M3,2023)、MacBook Pro (14/16"M3/Pro/Max,2023) 设备信息；</span>
                    </li>
                    <li>
                        <span>2023.11.06：完善 M3 系列芯片信息，更新 M3 系列芯片 die 模具图，收集 M3 系列 CPU 跑分数据；</span>
                    </li>
                    <li>
                        <span>2023.11.02：新增 M3 系列芯片信息；</span>
                    </li>
                    <li>
                        <span>2023.10.21：更新苹果配件设计指南 Release 21 版，修正网站摄像头光圈值表达符号；</span>
                    </li>
                    <li>
                        <span>2023.10.18：更新 iPad 全系列 Apple Pencil 兼容情况，更新 AirPods Pro (2nd Gen) 充电方式；</span>
                    </li>
                    <li>
                        <span>2023.10.17：收录 iMac Pro 2017 设备信息，更新 iPhone 15/Plus 内部图；</span>
                    </li>
                    <li>
                        <span>2023.10.12：更新 iPhone 15 Pro/Max 内部图 (感谢新浪微博 @楼斌Robin 提供素材)；</span>
                    </li>
                    <li>
                        <span>2023.10.10：更新 S9 SiP 芯片参数，修正 Apple TV 4K (3rd Gen) GPU 核心数量，修正 A15、A16 E 核解码发射数量；</span>
                    </li>
                    <li>
                        <span>2023.10.07：新增 Mac, Apple Display 及 iPhone 14 系列自助维修手册；</span>
                    </li>
                    <li>
                        <span>2023.10.03：更新 A17 Pro 参数，网站完成迁移至新服务器并启用新顶级域名 HubWeb.cn，感谢各位的关注与支持，大家一起努力进步；</span>
                    </li>
                    <li>
                        <span>2023.09.26：更新 A17 Pro die 模具图 (感谢 QQ 群友 @LITTERTREE66 提供来源)；</span>
                    </li>
                    <li>
                        <span>2023.09.25：更新 CPU、GPU、NPU、ROM 跑分报表数据；</span>
                    </li>
                    <li>
                        <span>2023.09.22：更新 iPhone 15 Pro 系列 Wi-Fi 规格及摄像头 CMOS 型号 (感谢 QQ 群友 @Tloml、@ほしぞらの夢 提供来源)；</span>
                    </li>
                    <li>
                        <span>2023.09.21：更新 A13 Bionic、A15 Pro Metal 版本 (感谢 QQ 群友 @mols 提供来源) ；</span>
                    </li>
                    <li>
                        <span>2023.09.20：“iOS 程序崩溃对比”新增 IPA 安装包下载，方便网友使用签名工具直接签名安装测试 (感谢 QQ 群友 @^_^ 提供技术支持)；</span>
                    </li>
                    <li>
                        <span>2023.09.17：新增“苹果 SiP IC 芯片汇总”到“其他参数”，此汇总页收集了苹果系统级封装的内部 IC 芯片信息；</span>
                    </li>
                    <li>
                        <span>2023.09.15：“CPU 频率测试”新增 A12X Bionic 测试数据，更新 Apple Watch (S9、Ultra 2) 、iPhone 15 系列电池容量；</span>
                    </li>
                    <li>
                        <span>2023.09.14：更新“iPhone 摄像头”参数，更新 W 系列芯片使用设备，更新 CPU 跑分报表，更新 A17 Pro 大核频率及小核缓存；</span>
                    </li>
                    <li>
                        <span>2023.09.13：新增 A17 Pro、S9 SiP、U2 芯片信息，新增 Apple Watch (S9、Ultra 2) 、iPhone 15 系列设备信息，更新“系统兼容情况”；</span>
                    </li>
                    <li>
                        <span>2023.09.09：更新网站首页 Dark 模式下的样式；</span>
                    </li>
                    <li>
                        <span>2023.09.01：更新网站首页“关于本站”说明；</span>
                    </li>
                    <li>
                        <span>2023.08.16：修正 M2/Pro/Max 芯片模具图 AMX IP 块数量标注；</span>
                    </li>
                    <li>
                        <span>2023.08.10：更新 M2 Ultra E 核心频率、微调网站 Dark 模式下的样式；</span>
                    </li>
                    <li>
                        <span>2023.08.01：更新 iPhone 14/Plus 超广角型号信息 (感谢 QQ 群友 @ほしぞらの夢 提供来源) 、修正 S6/7/8 芯片运行内存数据；</span>
                    </li>
                    <li>
                        <span>2023.07.26：全面适配 Dark 模式，手机或平板开启深色模式后，网站 (含 Web App) 将自动启用黑暗模式浏览；</span>
                    </li>
                    <li>
                        <span>2023.07.25：适配沉浸式状态栏；</span>
                    </li>
                    <li>
                        <span>2023.07.24：网站支持 Web App，使用方法：1.使用 Safari 浏览器访问网站，2.点击底部中间的”分享“按钮，选择“添加到主屏幕”；</span>
                    </li>
                    <li>
                        <span>2023.07.18：修正网站 2023 款 MacBook Pro / Mac mini 神经引擎参数；</span>
                    </li>
                    <li>
                        <span>2023.07.17：网站上线苹果工具“系统发布历史汇总 (OS Version History)”板块，该板块收集了操作系统的历史版本发布信息；</span>
                    </li>
                    <li>
                        <span>2023.07.15：更新 M2 Pro/Max 型号、网站菜单样式微调；</span>
                    </li>
                    <li>
                        <span>2023.07.11：更新 MacBook Air (M2, 2022) 蓝牙规格；</span>
                    </li>
                    <li>
                        <span>2023.06.20：更新 M2 Ultra 型号；</span>
                    </li>
                    <li>
                        <span>2023.06.14：更新 Apple W3 芯片型号、更新 M2 Ultra die 模具图；</span>
                    </li>
                    <li>
                        <span>2023.06.13：更新 MacBook Air (15",M2,2023) / Mac Studio (2023) / Mac Pro (2023) 设备技术规格、更新 GeekBench CPU/GPU 跑分数据；</span>
                    </li>
                    <li>
                        <span>2023.06.07：新增 Mac Pro 系列设备、更新 M2 Ultra 参数信息、增加 iMac 与 Mac Pro 设备到"系统兼容情况"；</span>
                    </li>
                    <li>
                        <span>2023.06.06：新增 M2 Ultra 处理器、新增 MacBook Air (15",M2,2023) / Mac Studio (2023) / Mac Pro (2023) 设备、根据 WWDC23 更新网站设备支持系统；</span>
                    </li>
                    <li>
                        <span>2023.05.18：补充 iPhone 14/14 Plus 广角摄像头型号，修正 iPhone 13 Pro/Max 超广角相位差对焦信息；</span>
                    </li>
                    <li>
                        <span>2023.05.16：新增 Mac M 系列芯片 CPU 跑分数据到“统计报表”，并微调展示样式；</span>
                    </li>
                    <li>
                        <span>2023.05.14：更新 A9 芯片模具图；</span>
                    </li>
                    <li>
                        <span>2023.05.07：新增 T2 芯片模具图，更新 A4、A5 (APL7498)、A5X、A6X 芯片模具图，修正 A5 (APL7498) L2 缓存信息、T2 芯片参数信息；</span>
                    </li>
                    <li>
                        <span>2023.05.05：修正 A16 Bionic GPU 家族信息；</span>
                    </li>
                    <li>
                        <span>2023.04.26：新增 A11 Bionic CPU 频率测试数据、修正 Apple A11 Bionic 能效核心频率；</span>
                    </li>
                    <li>
                        <span>2023.04.25：更新 GeekBench CPU/GPU/ML 跑分数据；</span>
                    </li>
                    <li>
                        <span>2023.04.23：更新苹果配件设计指南 Release 20 版；</span>
                    </li>
                    <li>
                        <span>2023.04.15：更新 Apple Silicon M 系列芯片模具图；</span>
                    </li>
                    <li>
                        <span>2023.03.27：更新 A16 Bionic 裸片尺寸、Apple Silicon 板块参数展示样式微调；</span>
                    </li>
                    <li>
                        <span>2023.03.26：更新部分 Mac 设备模具型号、更新 iMac 系列设备到 2017 年；</span>
                    </li>
                    <li>
                        <span>2023.03.14：新增“测试对比”菜单入口及调整二级菜单、新增“CPU 频率测试”对比、更新 A11 Bionic 模具图、更新 Apple U1 芯片使用设备清单；</span>
                    </li>
                    <li>
                        <span>2023.03.08：更新 iPhone 14/14 Plus 颜色参数、更新其他参数“iPhone 电池”的电芯能量及电芯规格；</span>
                    </li>
                    <li>
                        <span>2023.03.06：更新 A16 Bionic 极限读写数据；</span>
                    </li>
                    <li>
                        <span>2023.02.23：更新跑分报表数据；</span>
                    </li>
                    <li>
                        <span>2023.02.16：更新 S8 SiP 芯片 IC 图、更新 HomePod (2nd Gen) 图片及技术规格地址、更新协处理器信息；</span>
                    </li>
                    <li>
                        <span>2023.02.15：微调“统计报表”前端样式、“CPU 跑分报表”增加 GeekBench 6 得分数据；</span>
                    </li>
                    <li>
                        <span>2023.02.13：更新“iPhone 摄像头”参数相位差对焦信息；</span>
                    </li>
                    <li>
                        <span>2023.02.03：更新 Apple A/S/M 芯片 CPU 频率、更新 U1 芯片使用设备；</span>
                    </li>
                    <li>
                        <span>2023.01.31：更新 Apple Watch 电池容量；</span>
                    </li>
                    <li>
                        <span>2023.01.29：新增 HomePod 设备参数对比、更新“系统兼容情况”设备清单、更新 M2 Max 封面图、更新“iPhone 摄像头”参数；</span>
                    </li>
                    <li>
                        <span>2023.01.28：新增 MacBook Pro (14/16",2023) 设备、更新 M2 Pro/Max 芯片 GPU ALUs 数量、更新 M2 Pro 封面图、完善 Mac mini 信息；</span>
                    </li>
                    <li>
                        <span>2023.01.22：更新 M2 Pro/Max 芯片 die 模具图；</span>
                    </li>
                    <li>
                        <span>2023.01.20：更新 Apple S7 SiP 使用设备、更新 Mac 设备 CPU 及 GPU 信息、更新 M2 Pro/Max P 核处理器频率及媒体处理引擎信息；</span>
                    </li>
                    <li>
                        <span>2023.01.18：新增 M2 Pro/Max SOC 信息、新增 Mac mini 2023 设备信息；</span>
                    </li>
                    <li>
                        <span>2023.01.14：更新 AirPods 设备耳机电池电量及充电盒电池电量、跑分统计报表更新 M2 芯片得分；</span>
                    </li>
                    <li>
                        <span>2023.01.12：网站样式微调、解决手机端浏览器访问页面的宽度错位问题；</span>
                    </li>
                    <li>
                        <span>2023.01.06：新增 H 系列芯片技术工艺信息、更新 Apple H2 芯片模具图；</span>
                    </li>
                    <li>
                        <span>2023.01.03：完善 iPhone 摄像头参数，同时加入前置摄像头参数、相位差对焦、视角角度、电子防抖支持等信息；</span>
                    </li>
                    <li>
                        <span>2023.01.01：更新 iPhone 前置摄像头及激光雷达型号、更新 iPhone 后置 CMOS 数据、更新 iPad 设备无线局域网通道宽度信息；</span>
                    </li>
                    <li>
                        <span>2022.12.30：新增 Apple TV 设备参数对比、新增 A/M 芯片 GPU 家族信息、更新 iPad (10th Gen) 接口参数；</span>
                    </li>
                    <li>
                        <span>2022.12.13：更新 A16 Bionic 模具图；</span>
                    </li>
                    <li>
                        <span>2022.12.09：更新 Watch 设备及 S 系列芯片基带型号、更新 M 系列芯片支持系统；</span>
                    </li>
                    <li>
                        <span>2022.11.11：更新 U 系列、W 系列芯片使用设备；</span>
                    </li>
                    <li>
                        <span>2022.10.23：苹果工具"系统兼容情况"页面展示样式微调；</span>
                    </li>
                    <li>
                        <span>2022.10.19：新增 iPad (10th Gen)、 iPad Pro (12.9" 6th Gen、11" 4th Gen) 设备信息；</span>
                    </li>
                    <li>
                        <span>2022.10.08：更新 iPhone 14 Plus 电池参数、更新 iPhone 14 Plus 内部图；</span>
                    </li>
                    <li>
                        <span>2022.10.05：更新 iPhone 后置 CMOS 参数；</span>
                    </li>
                    <li>
                        <span>2022.09.30：新增 iMac 系列设备、更新 S8 SiP 芯片图、部分 Apple 设备新增“外观颜色”、Watch 参数页展示样式微调；</span>
                    </li>
                    <li>
                        <span>2022.09.29：新增苹果工具“设备配件设计指南”，该指南详细介绍了 Apple 设备及其设计规格 (感谢 QQ 群友 @JACKEY. 提供文件来源)；</span>
                    </li>
                    <li>
                        <span>2022.09.27：更新 iPhone 14 系列手机内部图、更新网站部分设备技术规格链接、Watch 参数页展示样式微调；</span>
                    </li>
                    <li>
                        <span>2022.09.24：更新 A16 芯片 L2 缓存、SLC 缓存及模具图；</span>
                    </li>
                    <li>
                        <span>2022.09.18：更新 A12 芯片 L1 指令缓存、完善 A16 Bionic 芯片参数信息；</span>
                    </li>
                    <li>
                        <span>2022.09.15：完善 A16 Bionic 芯片参数信息、完善 iPhone 14 Pro Max 参数信息；</span>
                    </li>
                    <li>
                        <span>2022.09.09：移除 A、M 芯片"性能"对比项、新增"统计报表"模块，该模块为可交互式，可查看各处理器性能对比图；</span>
                    </li>
                    <li>
                        <span>2022.09.08：新增 A16 Bionic、S8 SiP、H2 芯片信息、新增 AirPods Pro (2nd)、Apple Watch (SE 2、S8、Ultra)、iPhone 14 系列设备信息；</span>
                    </li>
                    <li>
                        <span>2022.09.06：网站样式微调、更新 AirPods Max 耳机电池容量、更新 Watch 设备传感器参数；</span>
                    </li>
                    <li>
                        <span>2022.08.28：Apple 设备新增"增强现实"查看 (需在 iPhone 或 iPad 上用 Safari 浏览器加载)；</span>
                    </li>
                    <li>
                        <span>2022.08.27：Apple Silicon 新增 H 系列参数对比；</span>
                    </li>
                    <li>
                        <span>2022.08.23：更新 iPhone 后置 CMOS 参数、新增 iPad 基带参数对比；</span>
                    </li>
                    <li>
                        <span>2022.08.22：Apple Silicon 新增 T 系列、U 系列、W 系列参数对比、网站菜单调整；</span>
                    </li>
                    <li>
                        <span>2022.08.19：新增苹果工具“交互式色域比较”，可查看你的显示器是否支持广色域、更新 A10X、A12、A12X、A12Z、A13 处理器模具图；</span>
                    </li>
                    <li>
                        <span>2022.08.16：更新 A 系列处理器 CPU 参数及 GPU 参数；</span>
                    </li>
                    <li>
                        <span>2022.08.13：更新 M 系列处理器模具图、更新 Mac、iPad、iPhone 设备色域参数；</span>
                    </li>
                    <li>
                        <span>2022.08.03：更新 Apple 处理器"内核架构"与"存储技术"参数，新增 Apple 处理器"内存通道"与"内存频率"，M 系列处理器参数展示样式微调；</span>
                    </li>
                    <li>
                        <span>2022.07.27：收集 2015 年之后的 MacBook Pro 设备到网站；</span>
                    </li>
                    <li>
                        <span>2022.07.21：Apple 设备新增“模具型号”参数；</span>
                    </li>
                    <li>
                        <span>2022.07.18：更新 M2 处理器芯片图及内部型号，更新 M2 处理器 GPU 频率参数；</span>
                    </li>
                    <li>
                        <span>2022.07.17：网站展示样式微调、“iPhone 电池参数”能量拆分为“电芯能量”与“电池能量”；</span>
                    </li>
                    <li>
                        <span>2022.07.15：网站新增大类参数项；</span>
                    </li>
                    <li>
                        <span>2022.07.13：新增 iPad 设备 Apple Pencil 兼容性；</span>
                    </li>
                    <li>
                        <span>2022.06.24：新增苹果 A、M 系列处理器“内存封装”参数；</span>
                    </li>
                    <li>
                        <span>2022.06.19：新增网站“参数解读”板块，主要对 Apple 苹果产品参数中心的各项参数进行解读，让你可以更好理解参数所代表的含义；</span>
                    </li>
                    <li>
                        <span>2022.06.18：新增苹果工具“iOS 程序崩溃对比”，可查看软件崩溃需要多少内存及何时发生内存警告，有助于了解任何 iOS 设备的内存分配预算；</span>
                    </li>
                    <li>
                        <span>2022.06.17：更新 Apple M 系列处理器 SLC 缓存信息、更新 Apple M2 处理器 GPU 参数、更新 Apple M1 处理器模具图；</span>
                    </li>
                    <li>
                        <span>2022.06.15：新增 Apple A 系列处理器微架构发射解码通道、新增 Apple M 系列处理器视频解码编码引擎数量；</span>
                    </li>
                    <li>
                        <span>2022.06.14：新增 Watch 设备“表背”参数；</span>
                    </li>
                    <li>
                        <span>2022.06.07：新增 M2 处理器参数、新增 MacBook Air/Pro (13",2022) 设备参数、根据 WWDC22 发布会更新网站所有设备支持系统；</span>
                    </li>
                    <li>
                        <span>2022.06.04：新增苹果工具“系统兼容情况”，可查看 Apple 设备支持的操作系统信息、更新 Watch 设备参数信息；</span>
                    </li>
                    <li>
                        <span>2022.05.25：新增苹果工具“自助维修手册”；</span>
                    </li>
                    <li>
                        <span>2022.05.23：新增 A 系列处理器 ROM 极限读写性能对比、更新 Watch 防尘防水参数；</span>
                    </li>
                    <li>
                        <span>2022.05.17：更新 iPhone 7 Plus 设备参数；</span>
                    </li>
                    <li>
                        <span>2022.05.06：新增苹果工具模块，更新 iPhone SE 3rd Gen 内部图，网站菜单栏新增 SVG 图标，网站更新记录展示方式调整；</span>
                    </li>
                    <li>
                        <span>2022.05.01：新增 iPhone 电池参数信息，更新 iPhone 后置摄像头 CMOS 型号信息；</span>
                    </li>
                    <li>
                        <span>2022.04.26：收录 iPhone 3GS 设备信息，更新 Apple Watch S5 屏幕面板参数；</span>
                    </li>
                    <li>
                        <span>2022.04.19：新增 Mac Studio 设备信息，修正错误的 MacBook Pro、Mac Mini 设备参数，更新 S7 SiP 芯片 IC 图；</span>
                    </li>
                    <li>
                        <span>2022.04.16：新增 iPhone 后置摄像头 CMOS 色彩滤波阵列、等效焦距参数；</span>
                    </li>
                    <li>
                        <span>2022.04.13：新增 Apple 处理器技术工艺光刻技术参数，更新 iPad 参数的 RAM、ROM 数据显示方式；</span>
                    </li>
                    <li>
                        <span>2022.04.12：新增 A 系列处理器 L1 指令缓存参数，更新 A、M 系列处理器 L1 数据缓存参数，更新 Watch、S 系列芯片无线局域网参数；</span>
                    </li>
                    <li>
                        <span>2022.04.10：iPhone、iPad、iPod touch 新增音频输入输出参数，更新 iPhone SE(3rd Gen) 后置 CMOS 参数，修复网站参数显示乱码问题；</span>
                    </li>
                    <li>
                        <span>2022.04.03：更新 A8X、A6X 处理器模具图；</span>
                    </li>
                    <li>
                        <span>2022.04.02：新增 MacBook Pro 设备信息 (目前只收集到 2018 年后发布的 MacBook Pro 设备信息)；</span>
                    </li>
                    <li>
                        <span>2022.04.01：更新网站样式，解决参数项错位问题；</span>
                    </li>
                    <li>
                        <span>2022.03.30：更新 S 系列处理器参数，更新 A 系列处理器参数，更新 Watch 设备电池容量；</span>
                    </li>
                    <li>
                        <span>2022.03.28：新增协处理器参数信息，更新 A12X、A12Z 二级缓存参数；</span>
                    </li>
                    <li>
                        <span>2022.03.25：新增 MacBook Air、MacBook、Mac mini 设备信息；</span>
                    </li>
                    <li>
                        <span>2022.03.20：网站数据展示风格调整，设备信息底部加入 Apple 官方技术规格地址；</span>
                    </li>
                    <li>
                        <span>2022.03.19：新增 iPhone 系列后置摄像头CMOS参数对比；</span>
                    </li>
                    <li>
                        <span>2022.03.18：新增 AirPods 设备信息；</span>
                    </li>
                    <li>
                        <span>2022.03.13：完善 iPhone 设备信息 (基带Modem型号、内部图)；</span>
                    </li>
                    <li>
                        <span>2022.03.12：新增 iPhone 设备信息；</span>
                    </li>
                    <li>
                        <span>2022.03.11：收录 iPad Air 5 设备信息；</span>
                    </li>
                    <li>
                        <span>2022.03.09：收录 M1 Ultra 处理器参数，更新 Apple A、M 系列处理器使用设备；</span>
                    </li>
                    <li>
                        <span>2022.03.06：修正 Apple A、M 系列处理器存储技术；</span>
                    </li>
                    <li>
                        <span>2022.02.22：更新 Apple A 系列处理器功耗、性能信息，新增 A、M 系列的机器学习跑分对比，更新 M1 处理器的使用设备；</span>
                    </li>
                    <li>
                        <span>2022.02.18：更新 Apple S 系列处理器内置 RAM、ROM，新增 Apple S 系列处理器主板 IC 图信息；</span>
                    </li>
                    <li>
                        <span>2022.01.21：收录 S7 处理器参数，更新 Apple 处理器参数显示样式；</span>
                    </li>
                    <li>
                        <span>2021.12.31：收录 A15 芯片 GPU 最大功耗；</span>
                    </li>
                    <li>
                        <span>2021.10.19：收录 M1 Pro/Max 处理器参数，新增 A、M 系列芯片 GPU 频率、EU 执行单元 ALU 算术逻辑单元参数；</span>
                    </li>
                    <li>
                        <span>2021.10.09：新增 A 系列、M 系列处理器模具图；</span>
                    </li>
                    <li>
                        <span>2021.10.07：更新 A 系列处理器缓存信息；</span>
                    </li>
                    <li>
                        <span>2021.09.27：收录 A15 Bionic 芯片参数信息；</span>
                    </li>
                    <li>
                        <span>2021.09.18：更新 iPod touch 全系列参数信息；</span>
                    </li>
                    <li>
                        <span>2021.09.15：更新 iPad 全系列参数信息；</span>
                    </li>
                    <li>
                        <span>2021.09.09：更新 Watch 全系列参数信息；</span>
                    </li>
                    <li>
                        <span>2021.09.02：网站全新改版、新增 Mac、iPad、iPhone、iPod、Watch 参数信息模块 (目前数据正在收集录入中)；</span>
                    </li>
                    <li>
                        <span>2021.06.08：新增苹果 S 系列处理器参数信息；</span>
                    </li>
                    <li>
                        <span>2021.06.05：新增苹果 M 系列处理器参数信息；</span>
                    </li>
                    <li>
                        <span>2021.05.11：新增苹果描述文件下载；</span>
                    </li>
                    <li>
                        <span>2021.04.02：收录 A14 Bionic 芯片参数信息；</span>
                    </li>
                    <li>
                        <span>2021.03.01：苹果产品参数中心网站上线，点击型号栏的型号图标进行参数对比，同时移除描述文件聚合页与“快捷指令”App 相关接口；</span>
                    </li>
                    <li>
                        <span>2019.05.04：新增“快捷指令”App的照片水印接口；</span>
                    </li>
                    <li>
                        <span>2019.02.25：新增 iOS 系统描述文件聚合页；</span>
                    </li>
                    <li>
                        <span>2018.04.18：网站开通 apple 二级目录，用于存放与苹果 Apple 相关的文件及资料；</span>
                    </li>
                    <li>
                        <span>2017.06.07：新增描述文件集合，可以通过安装tvOS描述文件屏蔽系统 OTA 更新；</span>
                    </li>
                </template>
            </div>
            <div class="title">
                <strong>媒体账号</strong>
                <br>
                <span>说明：关于本站维护者其他媒体的信息，网站内容错误或建议可通过下方渠道进行私信反馈。</span>
            </div>
            <div class="card">
                <b style="font-style:oblique;">维护者Bilibili：</b>
                <a href="https://space.bilibili.com/514518697">Celestial紗雪</a>
                <br>
                <b style="font-style:oblique;">维护者Twitter：</b>
                <a href="https://x.com/CelestialSayuki">@CelestialSayuki</a>
                <br>
                <b style="font-style:oblique;">维护者E-mail：</b>CelestialSayuki@qq.com
                <br>
                <b style="font-style:oblique;">维护者Telegram：</b>
                <a href="https://t.me/CelestialSayuki">@CelestialSayuki</a>
                <br>
                <b style="font-style:oblique;">原维护者IT之家：</b>番茄炒西红柿（ID：877388）
                <br>
                <b style="font-style:oblique;">原维护者Bilibili：</b>
                <a href="https://space.bilibili.com/211929380">白饭炒白米饭</a>（UID：211929380）
                <br>
                <b style="font-style:oblique;">原维护者新浪微博：</b>
                <a href="https://weibo.com/u/7215797790">白饭炒白米饭</a>（ID：7215797790）</div>
            <div style="text-align: center; margin-top: 50px; padding-bottom: 30px;">
                <spen class="spen_ab">数据参考来源：Apple、Intel、Sony、Geekbench、Anandtech、Wikipedia 等</spen>
                <br>
                <span class="spen_ab">This Website Is Not Affiliated With Apple Inc. © 2025 CelestialSayuki. All Rights Reserved.</span>
            </div>
            <div id="dock-container" class="dock-container"></div>
            <svg id="animation-svg-container" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 9999;"></svg>
            <div id="dock-preview" class="dock-preview"></div> <div id="dock-container" class="dock-container"></div>
        </main>
    </div>
                                                                         <script>
                                                                             document.addEventListener('DOMContentLoaded', () => {

                                                                                 // --- 全局变量和DOM引用 ---
                                                                                 const mainContentArea = document.querySelector('.main-content');
                                                                                 let zIndexCounter = 100;
                                                                                 const openWindows = new Map();
                                                                                 let windowIdCounter = 0;
                                                                                 const dockContainer = document.getElementById('dock-container');
                                                                                 const svgContainer = document.getElementById('animation-svg-container');
                                                                                 const dockPreview = document.getElementById('dock-preview');
                                                                                 const darkModeMatcher = window.matchMedia('(prefers-color-scheme: dark)');

                                                                                 const updateAllWindowsTheme = (isDark) => {
                                                                                     openWindows.forEach(windowData => {
                                                                                         if (isDark) {
                                                                                             windowData.element.classList.add('theme-dark');
                                                                                         } else {
                                                                                             windowData.element.classList.remove('theme-dark');
                                                                                         }
                                                                                     });
                                                                                 };
                                                                                 darkModeMatcher.addEventListener('change', e => {
                                                                                     updateAllWindowsTheme(e.matches);
                                                                                 });

                                                                                 // --- 辅助函数 ---
                                                                                 function convertRemToPx(cssText, baseFontSize = 10) {
                                                                                     return cssText.replace(/(\d*\.?\d+)\s*rem/g, (match, remValue) => `${parseFloat(remValue) * baseFontSize}px`);
                                                                                 }

                                                                                 function scopeCss(cssText, scopeSelector) {
                                                                                     const ruleRegex = /([^{}]*)(?=\{)/g;
                                                                                     return cssText.replace(ruleRegex, (match, selector) => {
                                                                                         const trimmedSelector = selector.trim();
                                                                                         if (trimmedSelector.startsWith('@') || trimmedSelector === '') return selector;
                                                                                         if (['html', 'body'].includes(trimmedSelector.toLowerCase())) return scopeSelector;
                                                                                         return trimmedSelector.split(',').map(part => `${scopeSelector} ${part.trim()}`).join(', ');
                                                                                     });
                                                                                 }

                                                                                 function rewriteCssUrls(cssText, cssBaseUrl) {
                                                                                     const urlRegex = /url\((?!['"]?data:)(['"]?)(.*?)\1\)/g;
                                                                                     return cssText.replace(urlRegex, (match, quote, url) => {
                                                                                         try {
                                                                                             const absoluteUrl = new URL(url, cssBaseUrl).href;
                                                                                             return `url(${quote}${absoluteUrl}${quote})`;
                                                                                         } catch (e) {
                                                                                             return match;
                                                                                         }
                                                                                     });
                                                                                 }

                                                                                 // --- Dock 和窗口状态管理 ---
                                                                                 function updateDockVisibility() {
                                                                                     const minimizedWindows = Array.from(openWindows.values()).filter(w => w.state === 'minimized');
                                                                                     if (minimizedWindows.length > 0) {
                                                                                         dockContainer.classList.add('visible');
                                                                                     } else {
                                                                                         dockContainer.classList.remove('visible');
                                                                                     }
                                                                                 }

                                                                                 function bringToFront(windowEl) {
                                                                                     if (parseInt(windowEl.style.zIndex) < zIndexCounter) {
                                                                                         zIndexCounter++;
                                                                                         windowEl.style.zIndex = zIndexCounter;
                                                                                     }
                                                                                 }

                                                                                 function closeWindowWithAnimation(windowEl, pageUrl) {
                                                                                     if (!windowEl || windowEl.classList.contains('is-closing')) return;
                                                                                     const windowData = openWindows.get(pageUrl);
                                                                                     if (windowData && windowData.dockItem) {
                                                                                         windowData.dockItem.remove();
                                                                                     }
                                                                                     windowEl.classList.add('is-closing');
                                                                                     windowEl.addEventListener('animationend', () => {
                                                                                         windowEl.remove();
                                                                                         openWindows.delete(pageUrl);
                                                                                         if (windowData) {
                                                                                             document.querySelectorAll(`[data-dynamic-style-for="${windowData.id}"]`).forEach(s => s.remove());
                                                                                         }
                                                                                         updateDockVisibility();
                                                                                     }, {
                                                                                         once: true
                                                                                     });
                                                                                 }

                                                                                 // --- 核心功能：创建、最小化、恢复窗口 ---
                async function createWindow(pageUrl, titleText) {
                            // **修改点**: 检查窗口是否已存在
                            if (openWindows.has(pageUrl)) {
                                const windowData = openWindows.get(pageUrl);
                                const windowEl = windowData.element;

                                // 如果窗口已最小化，则恢复它
                                if (windowData.state === 'minimized') {
                                    restoreWindow(pageUrl);
                                }
                                // 如果窗口是当前最顶层的窗口，则关闭它
                                else if (parseInt(windowEl.style.zIndex) === zIndexCounter) {
                                    closeWindowWithAnimation(windowEl, pageUrl);
                                }
                                // 否则（窗口存在但不在最顶层），则把它带到最前
                                else {
                                    bringToFront(windowEl);
                                }
                                return; // 结束函数，不执行后面的创建逻辑
                            }

                            // --- 以下是创建新窗口的逻辑，保持不变 ---

                            windowIdCounter++;
                            const windowId = `dynamic-window-${windowIdCounter}`;
                            const windowEl = document.createElement('div');
                            windowEl.id = windowId;
                            windowEl.className = 'macos-window';

                            const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                            if (isDarkMode) windowEl.classList.add('theme-dark');

                            const offset = (openWindows.size % 10) * 30;
                            windowEl.style.top = `${50 + offset}px`;
                            windowEl.style.left = `${100 + offset}px`;
                            zIndexCounter++;
                            windowEl.style.zIndex = zIndexCounter;

                            windowEl.innerHTML = `
                                <div class="macos-window-header">
                                    <div class="macos-window-controls">
                                        <span class="control-btn control-close"></span>
                                        <span class="control-btn control-minimize"></span>
                                        <span class="control-btn control-maximize"></span>
                                    </div>
                                    <div class="macos-window-title">${titleText}</div>
                                </div>
                                <div class="macos-window-body">
                                    </div>
                            `;
                            
                            windowEl.style.visibility = 'hidden';
                            
                            mainContentArea.appendChild(windowEl);

                            const windowData = {
                                id: windowId,
                                element: windowEl,
                                state: 'open',
                                rect: null,
                                dockItem: null,
                                title: titleText,
                            };
                            openWindows.set(pageUrl, windowData);

                            const header = windowEl.querySelector('.macos-window-header');
                            const closeBtn = windowEl.querySelector('.control-close');
                            const minimizeBtn = windowEl.querySelector('.control-minimize');
                            const minWidth = parseInt(getComputedStyle(windowEl).minWidth);
                            const minHeight = parseInt(getComputedStyle(windowEl).minHeight);
                            const resizeBorderWidth = 10;

                            closeBtn.onclick = (e) => {
                                e.stopPropagation();
                                closeWindowWithAnimation(windowEl, pageUrl);
                            };
                            minimizeBtn.onclick = (e) => {
                                e.stopPropagation();
                                minimizeWindow(pageUrl);
                            };

                            let action = '';
                            let startX, startY, startWidth, startHeight, startLeft, startTop;
                            let resizeDirection = '';

                            const handleMouseMoveForCursor = (e) => {
                                if (action) return;

                                const rect = windowEl.getBoundingClientRect();
                                const onTopEdge = e.clientY >= rect.top && e.clientY <= rect.top + resizeBorderWidth;
                                const onBottomEdge = e.clientY <= rect.bottom && e.clientY >= rect.bottom - resizeBorderWidth;
                                const onLeftEdge = e.clientX >= rect.left && e.clientX <= rect.left + resizeBorderWidth;
                                const onRightEdge = e.clientX <= rect.right && e.clientX >= rect.right - resizeBorderWidth;

                                let newCursor = 'default';
                                let currentResizeDir = '';

                                if (onTopEdge) { currentResizeDir += 'n'; newCursor = 'ns-resize'; }
                                if (onBottomEdge) { currentResizeDir += 's'; newCursor = 'ns-resize'; }
                                if (onLeftEdge) { currentResizeDir += 'w'; newCursor = 'ew-resize'; }
                                if (onRightEdge) { currentResizeDir += 'e'; newCursor = 'ew-resize'; }

                                if (currentResizeDir === 'nw' || currentResizeDir === 'se') newCursor = 'nwse-resize';
                                if (currentResizeDir === 'ne' || currentResizeDir === 'sw') newCursor = 'nesw-resize';

                                windowEl.style.cursor = newCursor;
                                windowEl.dataset.resizeDirection = currentResizeDir;
                            }

                            windowEl.addEventListener('mousemove', handleMouseMoveForCursor);

                            const handleWindowInteraction = (e) => {
                                if (e.target.classList.contains('control-btn') || e.target.closest('.macos-window-body')) return;

                                e.preventDefault();
                                bringToFront(windowEl);

                                startX = e.clientX;
                                startY = e.clientY;
                                startWidth = windowEl.offsetWidth;
                                startHeight = windowEl.offsetHeight;
                                startLeft = windowEl.offsetLeft;
                                startTop = windowEl.offsetTop;

                                resizeDirection = windowEl.dataset.resizeDirection || '';

                                if (resizeDirection) {
                                    action = 'resizing';
                                } else if (e.target.closest('.macos-window-header')) {
                                    action = 'dragging';
                                    header.style.cursor = 'grabbing';
                                }

                                if (action) {
                                    document.addEventListener('mousemove', performInteraction);
                                    document.addEventListener('mouseup', stopInteraction);
                                }
                            };

                            windowEl.addEventListener('mousedown', handleWindowInteraction);

                            const performInteraction = (e) => {
                                if (action === 'dragging') { drag(e); } 
                                else if (action === 'resizing') { resize(e); }
                            };

                            const drag = (e) => {
                                const newX = startLeft + e.clientX - startX;
                                const newY = startTop + e.clientY - startY;
                                windowEl.style.left = `${newX}px`;
                                windowEl.style.top = `${newY}px`;
                            };

                            const resize = (e) => {
                                const deltaX = e.clientX - startX;
                                const deltaY = e.clientY - startY;

                                let newWidth = startWidth;
                                let newHeight = startHeight;
                                let newLeft = startLeft;
                                let newTop = startTop;

                                if (resizeDirection.includes('e')) { newWidth = startWidth + deltaX; }
                                if (resizeDirection.includes('w')) { newWidth = startWidth - deltaX; newLeft = startLeft + deltaX; }
                                if (resizeDirection.includes('s')) { newHeight = startHeight + deltaY; }
                                if (resizeDirection.includes('n')) { newHeight = startHeight - deltaY; newTop = startTop + deltaY; }

                                if (newWidth >= minWidth) {
                                    windowEl.style.width = `${newWidth}px`;
                                    windowEl.style.left = `${newLeft}px`;
                                }
                                if (newHeight >= minHeight) {
                                    windowEl.style.height = `${newHeight}px`;
                                    windowEl.style.top = `${newTop}px`;
                                }
                            };

                            const stopInteraction = () => {
                                action = '';
                                header.style.cursor = 'move';
                                windowEl.style.cursor = 'default';
                                windowEl.dataset.resizeDirection = '';
                                document.removeEventListener('mousemove', performInteraction);
                                document.removeEventListener('mouseup', stopInteraction);
                            };

                            const windowBody = windowEl.querySelector('.macos-window-body');
                            try {
                                const response = await fetch(pageUrl);
                                if (!response.ok) throw new Error(`网络请求失败: ${response.status}`);
                                const htmlText = await response.text();
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(htmlText, 'text/html');
                                const newContent = doc.querySelector('.cd-products-comparison-table');

                                if (newContent) {
                                    const baseUrl = response.url;
                                    newContent.querySelectorAll('img').forEach(img => {
                                        const relativeSrc = img.getAttribute('src');
                                        if (relativeSrc) img.setAttribute('src', new URL(relativeSrc, baseUrl).href);
                                    });
                                    newContent.querySelectorAll('*').forEach(el => {
                                        if (el.hasAttribute('style')) {
                                            let inlineStyle = el.getAttribute('style');
                                            let processedStyle = convertRemToPx(inlineStyle);
                                            el.setAttribute('style', processedStyle);
                                        }
                                    });
                                    const processAndInjectCss = async (cssText, cssBaseUrl, forWindowId) => {
                                        let processedCss = convertRemToPx(cssText);
                                        processedCss = rewriteCssUrls(processedCss, cssBaseUrl);
                                        const scopeSelector = `#${forWindowId} .macos-window-body`;
                                        let finalScopedCss;
                                        if (processedCss.includes('@media (prefers-color-scheme: dark)')) {
                                            const startIndex = processedCss.indexOf('{');
                                            const endIndex = processedCss.lastIndexOf('}');
                                            const darkStyles = processedCss.substring(startIndex + 1, endIndex);
                                            finalScopedCss = scopeCss(darkStyles, `#${forWindowId}.theme-dark .macos-window-body`);
                                        } else {
                                            finalScopedCss = scopeCss(processedCss, scopeSelector);
                                        }
                                        const styleTag = document.createElement('style');
                                        styleTag.textContent = finalScopedCss;
                                        styleTag.setAttribute('data-dynamic-style-for', forWindowId);
                                        document.head.appendChild(styleTag);
                                    };
                                    for (const link of doc.querySelectorAll('link[rel="stylesheet"]')) {
                                        const absoluteUrl = new URL(link.getAttribute('href'), baseUrl).href;
                                        const cssResponse = await fetch(absoluteUrl);
                                        const cssText = await cssResponse.text();
                                        await processAndInjectCss(cssText, absoluteUrl, windowId);
                                    }
                                    for (const style of doc.querySelectorAll('style')) {
                                        await processAndInjectCss(style.textContent, baseUrl, windowId);
                                    }
                                    
                                    windowBody.appendChild(newContent);

                                } else {
                                    throw new Error('无法在目标页面找到所需内容。');
                                }
                            } catch (error) {
                                console.error('加载窗口内容时出错:', error);
                                windowBody.innerHTML = `<div style="color:red; text-align:center; padding: 50px;">内容加载失败。</div>`;
                            } finally {
                                windowEl.style.visibility = 'visible';
                            }
                        }
                                                                                 function minimizeWindow(pageUrl) {
                                                                                     const windowData = openWindows.get(pageUrl);
                                                                                     if (!windowData || windowData.state !== 'open') return;
                                                                                     const windowEl = windowData.element;
                                                                                     const dockItem = document.createElement('div');
                                                                                     dockItem.className = 'dock-item';
                                                                                     dockItem.title = windowData.title;
                                                                                     dockItem.style.backgroundImage = `url('./public-static/img/apple-touch-icon.jpg?2556')`;
                                                                                     dockItem.onclick = () => restoreWindow(pageUrl);
                                                                                     windowData.dockItem = dockItem;
                                                                                     dockContainer.appendChild(dockItem);
                                                                                     windowData.state = 'minimized';
                                                                                     windowData.rect = windowEl.getBoundingClientRect();
                                                                                     updateDockVisibility();
                                                                                     requestAnimationFrame(() => {
                                                                                         const startRect = windowData.rect;
                                                                                         const endRect = dockItem.getBoundingClientRect();
                                                                                         const parentRect = mainContentArea.getBoundingClientRect();
                                                                                         const startX = startRect.left - parentRect.left;
                                                                                         const startY = startRect.top - parentRect.top;
                                                                                         const endX = endRect.left - parentRect.left + (endRect.width / 2);
                                                                                         const endY = endRect.top - parentRect.top + (endRect.height / 2);
                                                                                         const controlX1 = startX;
                                                                                         const controlY1 = startY + (endY - startY) * 0.8;
                                                                                         const controlX2 = endX;
                                                                                         const controlY2 = endY;
                                                                                         const pathD = `M ${startX},${startY} C ${controlX1},${controlY1} ${controlX2},${controlY2} ${endX},${endY}`;
                                                                                         svgContainer.innerHTML = `<path d="${pathD}" fill="none" stroke="none" />`;
                                                                                         const ghost = document.createElement('div');
                                                                                         ghost.className = 'window-ghost';
                                                                                         ghost.style.top = `${startRect.top}px`;
                                                                                         ghost.style.left = `${startRect.left}px`;
                                                                                         ghost.style.width = `${startRect.width}px`;
                                                                                         ghost.style.height = `${startRect.height}px`;
                                                                                         ghost.style.backgroundColor = getComputedStyle(windowEl).backgroundColor;
                                                                                         ghost.style.offsetPath = `path('${pathD}')`;
                                                                                         ghost.style.animationName = 'genie-minimize';
                                                                                         document.body.appendChild(ghost);
                                                                                         windowEl.style.display = 'none';
                                                                                         ghost.onanimationend = () => {
                                                                                             ghost.remove();
                                                                                             svgContainer.innerHTML = '';
                                                                                         };
                                                                                     });
                                                                                 }

                                                                                 function restoreWindow(pageUrl) {
                                                                                     const windowData = openWindows.get(pageUrl);
                                                                                     if (!windowData || windowData.state !== 'minimized') return;
                                                                                     const windowEl = windowData.element;
                                                                                     const dockItem = windowData.dockItem;
                                                                                     const startRect = dockItem.getBoundingClientRect();
                                                                                     const endRect = windowData.rect;
                                                                                     const parentRect = mainContentArea.getBoundingClientRect();
                                                                                     const startX = startRect.left - parentRect.left + (startRect.width / 2);
                                                                                     const startY = startRect.top - parentRect.top + (startRect.height / 2);
                                                                                     const endX = endRect.left - parentRect.left;
                                                                                     const endY = endRect.top - parentRect.top;
                                                                                     const controlX1 = endX;
                                                                                     const controlY1 = endY;
                                                                                     const controlX2 = startX;
                                                                                     const controlY2 = startY + (endY - startY) * 0.2;
                                                                                     const pathD = `M ${endX},${endY} C ${controlX2},${controlY2} ${controlX1},${controlY1} ${startX},${startY}`;
                                                                                     svgContainer.innerHTML = `<path d="${pathD}" fill="none" stroke="none" />`;
                                                                                     const ghost = document.createElement('div');
                                                                                     ghost.className = 'window-ghost';
                                                                                     ghost.style.top = `${endRect.top}px`;
                                                                                     ghost.style.left = `${endRect.left}px`;
                                                                                     ghost.style.width = `${endRect.width}px`;
                                                                                     ghost.style.height = `${endRect.height}px`;
                                                                                     ghost.style.backgroundColor = getComputedStyle(windowEl).backgroundColor;
                                                                                     ghost.style.offsetPath = `path('${pathD}')`;
                                                                                     ghost.style.animationName = 'genie-restore';
                                                                                     ghost.style.animationDirection = 'reverse'; // Reverse restore animation
                                                                                     document.body.appendChild(ghost);
                                                                                     dockItem.remove();
                                                                                     windowData.dockItem = null;
                                                                                     windowData.state = 'open';
                                                                                     updateDockVisibility();
                                                                                     ghost.onanimationend = () => {
                                                                                         windowEl.style.display = 'flex';
                                                                                         bringToFront(windowEl);
                                                                                         ghost.remove();
                                                                                         svgContainer.innerHTML = '';
                                                                                     };
                                                                                 }

                                                                                 // --- 页面初始化逻辑 ---

                                                                                 // 菜单折叠功能
                                                                                 document.querySelectorAll('.sidebar-menu li').forEach(li => {
                                                                                     if (li.querySelector('ul')) li.classList.add('has-submenu');
                                                                                 });
                                                                                 document.querySelectorAll('.sidebar-menu li.has-submenu > a').forEach(menuItem => {
                                                                                     menuItem.addEventListener('click', (e) => {
                                                                                         e.preventDefault();
                                                                                         const parentLi = menuItem.parentElement;
                                                                                         parentLi.classList.toggle('open');
                                                                                         const submenu = parentLi.querySelector('ul');
                                                                                         if (submenu) {
                                                                                             if (parentLi.classList.contains('open')) {
                                                                                                 submenu.style.maxHeight = submenu.scrollHeight + "px";
                                                                                             } else {
                                                                                                 submenu.style.maxHeight = null;
                                                                                             }
                                                                                         }
                                                                                     });
                                                                                 });

                                                                                 // 侧边栏链接点击处理
                                                                                 document.querySelectorAll('.sidebar-menu a[href]').forEach(link => {
                                                                                     const href = link.getAttribute('href');
                                                                                     if (href && href !== '#' && !href.startsWith('http') && !href.startsWith('javascript:')) {
                                                                                         link.onclick = (event) => {
                                                                                             event.preventDefault();
                                                                                             event.stopPropagation();
                                                                                             createWindow(href, link.textContent.trim());
                                                                                         };
                                                                                     }
                                                                                 });

                                                                                 // "加载更多" 按钮
                                                                                 const updatesList = document.getElementById('update-history-list');
                                                                                 const showMoreBtn = document.getElementById('show-more-updates');
                                                                                 const moreButtonListItem = document.querySelector('.more-btn-li');
                                                                                 const template = document.getElementById('more-updates-template');
                                                                                 if (updatesList && showMoreBtn && moreButtonListItem && template) {
                                                                                     if (!template.content.firstElementChild) {
                                                                                         moreButtonListItem.style.display = 'none';
                                                                                     } else {
                                                                                         const updateCollapsedHeight = () => {
                                                                                             if (updatesList.classList.contains('expanded')) return;
                                                                                             let initialHeight = 0;
                                                                                             updatesList.querySelectorAll('li:not(.more-btn-li)').forEach(li => {
                                                                                                 initialHeight += li.offsetHeight;
                                                                                             });
                                                                                             initialHeight += moreButtonListItem.offsetHeight;
                                                                                             updatesList.style.height = initialHeight + 'px';
                                                                                         };
                                                                                         updateCollapsedHeight();
                                                                                         window.addEventListener('resize', updateCollapsedHeight);
                                                                                         showMoreBtn.addEventListener('click', (e) => {
                                                                                             e.preventDefault();
                                                                                             window.removeEventListener('resize', updateCollapsedHeight);
                                                                                             updatesList.classList.add('expanded');
                                                                                             updatesList.insertBefore(template.content.cloneNode(true), moreButtonListItem);
                                                                                             updatesList.style.height = updatesList.scrollHeight + 'px';
                                                                                             moreButtonListItem.remove();
                                                                                             updatesList.addEventListener('transitionend', () => {
                                                                                                 updatesList.style.height = 'auto';
                                                                                             }, {
                                                                                                 once: true
                                                                                             });
                                                                                         });
                                                                                     }
                                                                                 }

                                                                                 // 倒计时功能
                                                                                 const interval = 1000;

                                                                                 function ShowCountDown(year, month, day, hh, mm, ss, divname) {
                                                                                     const now = new Date();
                                                                                     const endDate = new Date(year, month - 1, day, hh, mm, ss);
                                                                                     const leftTime = endDate.getTime() - now.getTime();
                                                                                     const div1 = document.getElementById("divdown1");
                                                                                     const div2 = document.getElementById(divname);
                                                                                     if (leftTime > 0 && div1 && div2) {
                                                                                         div1.style.display = 'block';
                                                                                         div2.style.display = 'block';
                                                                                         const leftsecond = parseInt(leftTime / 1000);
                                                                                         const day1 = Math.floor(leftsecond / (60 * 60 * 24));
                                                                                         const hour = Math.floor((leftsecond - day1 * 24 * 60 * 60) / 3600);
                                                                                         const minute = Math.floor((leftsecond - day1 * 24 * 60 * 60 - hour * 3600) / 60);
                                                                                         const second = Math.floor(leftsecond - day1 * 24 * 60 * 60 - hour * 3600 - minute * 60);
                                                                                         div2.innerHTML = "倒计时：" + day1 + " 天 " + hour + " 小时 " + minute + " 分 " + second + " 秒";
                                                                                     } else if (div1 && div2) {
                                                                                         div1.style.display = 'none';
                                                                                         div2.style.display = 'none';
                                                                                     }
                                                                                 }
                                                                                 window.setInterval(() => ShowCountDown(2025, 6, 10, 1, 0, 0, 'divdown2'), interval);


                                                                                 // =======================================================
                                                                                 // 新增：Dock 图标悬停预览逻辑
                                                                                 // =======================================================
                                                                                 dockContainer.addEventListener('mouseover', (e) => {
                                                                                     const hoveredItem = e.target.closest('.dock-item');
                                                                                     if (!hoveredItem || !dockPreview) return;

                                                                                     const title = hoveredItem.title;
                                                                                     if (!title) return;
                                                                                     dockPreview.textContent = title;

                                                                                     const itemRect = hoveredItem.getBoundingClientRect();
                                                                                     const parentRect = mainContentArea.getBoundingClientRect();

                                                                                     dockPreview.style.opacity = '1';

                                                                                     const top = (itemRect.top - parentRect.top) - dockPreview.offsetHeight - 10;
                                                                                     const left = (itemRect.left - parentRect.left) + (itemRect.width / 2) - (dockPreview.offsetWidth / 2);

                                                                                     dockPreview.style.top = `${top}px`;
                                                                                     dockPreview.style.left = `${left}px`;

                                                                                     dockPreview.style.transform = 'translateY(0)';
                                                                                 });

                                                                                 dockContainer.addEventListener('mouseout', (e) => {
                                                                                     const hoveredItem = e.target.closest('.dock-item');
                                                                                     if (!hoveredItem || !dockPreview) return;

                                                                                     dockPreview.style.opacity = '0';
                                                                                     dockPreview.style.transform = 'translateY(10px)';
                                                                                 });

                                                                             });
                                                                         </script>
</body>
</html>
