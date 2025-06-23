/*
 Technical Support：hubweb.cn
*/

// 禁止右键菜单
document.addEventListener('contextmenu', function(event) {
    event.preventDefault(); // 阻止右键菜单的默认行为
});

// 禁止图片拖动
document.addEventListener('dragstart', function(event) {
    if (event.target.tagName.toLowerCase() === 'img') {
        event.preventDefault(); // 阻止图片拖动
    }
});
