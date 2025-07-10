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
    
    // --- 滚动锁定辅助函数 ---
    function updateScrollLock() {
        let shouldLock = false;
        // 检查是否有任何一个“打开的”窗口处于最大化状态
        for (const data of openWindows.values()) {
            if (data.element.classList.contains('is-maximized') && data.state === 'open') {
                shouldLock = true;
                break;
            }
        }
        mainContentArea.classList.toggle('main-content-locked', shouldLock);
    }

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
            updateScrollLock();
        }, { once: true });
    }

    // --- 核心功能：创建、最小化、恢复窗口 ---
    async function createWindow(pageUrl, titleText) {
        let wasMaximized = false;
        let maximizedWindowUrl = null;

        for (const [url, data] of openWindows.entries()) {
            if (data.element.classList.contains('is-maximized') && data.state === 'open') {
                wasMaximized = true;
                maximizedWindowUrl = url;
                break;
            }
        }

        if (wasMaximized && maximizedWindowUrl !== pageUrl) {
            const windowToCloseData = openWindows.get(maximizedWindowUrl);
            if (windowToCloseData) {
                closeWindowWithAnimation(windowToCloseData.element, maximizedWindowUrl);
            }
        }
        
        if (openWindows.has(pageUrl)) {
            const windowData = openWindows.get(pageUrl);
            const windowEl = windowData.element;
            if (windowData.state === 'minimized') {
                restoreWindow(pageUrl);
            } else if (parseInt(windowEl.style.zIndex) === zIndexCounter) {
                closeWindowWithAnimation(windowEl, pageUrl);
            } else {
                bringToFront(windowEl);
            }
            return;
        }

        const isComparisonPage = pageUrl.includes('/apple-device/') || pageUrl.includes('/apple-silicon/');
        if (isComparisonPage) {
            const styleId = 'comparison-ui-style';
            if (!document.getElementById(styleId)) {
                const link = document.createElement('link');
                link.id = styleId;
                link.rel = 'stylesheet';
                link.href = './public-static/css/comparison-table-ui.css';
                document.head.appendChild(link);
            }
        }

        windowIdCounter++;
        const windowId = `dynamic-window-${windowIdCounter}`;
        const windowEl = document.createElement('div');
        windowEl.id = windowId;
        windowEl.className = 'macos-window';
        if (darkModeMatcher.matches) windowEl.classList.add('theme-dark');
        
        const offset = (openWindows.size % 10) * 30;
        windowEl.style.top = `${mainContentArea.scrollTop + 50 + offset}px`;
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
                <div class="header-actions"></div> 
            </div>
            <div class="macos-window-body"></div>`;

        windowEl.style.visibility = 'hidden';
        mainContentArea.appendChild(windowEl);

        const windowData = { id: windowId, element: windowEl, state: 'open', rect: null, preMaximizeRect: null, dockItem: null, title: titleText };
        openWindows.set(pageUrl, windowData);
        
        const header = windowEl.querySelector('.macos-window-header');
        const closeBtn = windowEl.querySelector('.control-close');
        const minimizeBtn = windowEl.querySelector('.control-minimize');
        const maximizeBtn = windowEl.querySelector('.control-maximize');
        
        closeBtn.onclick = (e) => { e.stopPropagation(); closeWindowWithAnimation(windowEl, pageUrl); };
        minimizeBtn.onclick = (e) => { e.stopPropagation(); minimizeWindow(pageUrl); };
        maximizeBtn.onclick = (e) => { e.stopPropagation(); toggleMaximize(pageUrl); };

        let action = '';
        let startX, startY, startWidth, startHeight, startLeft, startTop;
        let resizeDirection = '';
        const resizeBorderWidth = 10;
        const minWidth = parseInt(getComputedStyle(windowEl).minWidth);
        const minHeight = parseInt(getComputedStyle(windowEl).minHeight);

        const handleMouseMoveForCursor = (e) => {
            if (action || windowEl.classList.contains('is-maximized')) return;
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
        };
        windowEl.addEventListener('mousemove', handleMouseMoveForCursor);

        const handleWindowInteraction = (e) => {
            if (windowEl.classList.contains('is-maximized')) return;
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
                windowEl.style.transition = 'none'; // --[FIX]-- Disable transition on drag start
                document.addEventListener('mousemove', performInteraction);
                document.addEventListener('mouseup', stopInteraction);
            }
        };
        windowEl.addEventListener('mousedown', handleWindowInteraction);

        const performInteraction = (e) => {
            if (action === 'dragging') {
                windowEl.style.left = `${startLeft + e.clientX - startX}px`;
                windowEl.style.top = `${startTop + e.clientY - startY}px`;
            } else if (action === 'resizing') {
                const deltaX = e.clientX - startX;
                const deltaY = e.clientY - startY;
                let newWidth = startWidth, newHeight = startHeight, newLeft = startLeft, newTop = startTop;
                if (resizeDirection.includes('e')) newWidth = startWidth + deltaX;
                if (resizeDirection.includes('w')) { newWidth = startWidth - deltaX; newLeft = startLeft + deltaX; }
                if (resizeDirection.includes('s')) newHeight = startHeight + deltaY;
                if (resizeDirection.includes('n')) { newHeight = startHeight - deltaY; newTop = startTop + deltaY; }
                if (newWidth >= minWidth) { windowEl.style.width = `${newWidth}px`; windowEl.style.left = `${newLeft}px`; }
                if (newHeight >= minHeight) { windowEl.style.height = `${newHeight}px`; windowEl.style.top = `${newTop}px`; }
            }
        };

        const stopInteraction = () => {
            windowEl.style.transition = ''; // --[FIX]-- Re-enable transition on drag end
            action = '';
            header.style.cursor = 'move';
            windowEl.style.cursor = 'default';
            windowEl.dataset.resizeDirection = '';
            document.removeEventListener('mousemove', performInteraction);
            document.removeEventListener('mouseup', stopInteraction);
        };

        const windowBody = windowEl.querySelector('.macos-window-body');
        try {
            if (isComparisonPage) {
                windowBody.classList.add('comparison-container');
                windowBody.innerHTML = `<div style="text-align:center; padding:50px; color: var(--light-text-secondary);">正在加载...</div>`;
                windowEl.style.visibility = 'visible';
                
                const actionsContainer = header.querySelector('.header-actions');
                const resetButton = document.createElement('a');
                resetButton.href = '#';
                resetButton.className = 'header-btn reset-btn';
                resetButton.textContent = '重置';
                
                const filterButton = document.createElement('a');
                filterButton.href = '#';
                filterButton.className = 'header-btn filter-btn';
                filterButton.textContent = '比较';

                actionsContainer.appendChild(resetButton);
                actionsContainer.appendChild(filterButton);

                const jsonUrl = pageUrl + 'data.json';
                const gridContainer = await renderComparisonTable(jsonUrl, windowBody, pageUrl);

                if (gridContainer) {
                    const updateFilterState = () => {
                        const selectedCount = gridContainer.querySelectorAll('.product-column.selected').length;
                        if (selectedCount > 0) {
                            filterButton.classList.add('active');
                        } else {
                            filterButton.classList.remove('active');
                            gridContainer.classList.remove('is-filtering');
                        }
                    };

                    gridContainer.addEventListener('click', (e) => {
                        const product = e.target.closest('.product-column');
                        if (!product) return;
                        if (e.target.closest('a')) { e.preventDefault(); return; }
                        product.classList.toggle('selected');
                        updateFilterState();
                    });

                    filterButton.addEventListener('click', (e) => {
                        e.preventDefault();
                        if (filterButton.classList.contains('active')) {
                            gridContainer.classList.add('is-filtering');
                        }
                    });

                    resetButton.addEventListener('click', (e) => {
                        e.preventDefault();
                        gridContainer.classList.remove('is-filtering');
                        gridContainer.querySelectorAll('.product-column').forEach(p => p.classList.remove('selected'));
                        updateFilterState();
                    });

                    updateFilterState();
                    syncRowHeights(gridContainer);
                }

            } else {
               const response = await fetch(pageUrl);
               if (!response.ok) throw new Error(`网络请求失败: ${response.status}`);
               const htmlText = await response.text();
               const parser = new DOMParser();
               const doc = parser.parseFromString(htmlText, 'text/html');
               const content = doc.body.firstElementChild;
               if(content) {
                    windowBody.appendChild(content);
               } else {
                    windowBody.innerHTML = `<div style="padding: 20px;">无法加载内容或页面为空。</div>`;
               }
            }
        } catch (error) {
            console.error('加载窗口内容时出错:', error);
            windowBody.innerHTML = `<div style="color:red; text-align:center; padding: 50px;">内容加载失败。<br>${error.message}</div>`;
        } finally {
            if (windowEl.style.visibility === 'hidden') {
               windowEl.style.visibility = 'visible';
            }
            if (wasMaximized) {
                setTimeout(() => toggleMaximize(pageUrl), 50);
            }
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
        updateScrollLock();
        
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

    function toggleMaximize(pageUrl) {
        const windowData = openWindows.get(pageUrl);
        if (!windowData) return;
        const windowEl = windowData.element;

        if (windowEl.classList.contains('is-maximized')) {
            windowEl.classList.remove('is-maximized');
            const preMaximizeRect = windowData.preMaximizeRect;
            if (preMaximizeRect) {
                windowEl.style.top = preMaximizeRect.top;
                windowEl.style.left = preMaximizeRect.left;
                windowEl.style.width = preMaximizeRect.width;
                windowEl.style.height = preMaximizeRect.height;
            }
        } else {
            mainContentArea.scrollTop = 0; // 新增：在最大化前，将底层滚动到顶部
            
            let aWindowIsClosing = false;
            for (const data of openWindows.values()) {
                if (data.element.classList.contains('is-closing')) {
                    aWindowIsClosing = true;
                    break;
                }
            }
            if (!aWindowIsClosing) {
                for (const [otherUrl, otherData] of openWindows.entries()) {
                    if (otherUrl !== pageUrl && otherData.state === 'open') {
                        minimizeWindow(otherUrl);
                    }
                }
            }
            windowData.preMaximizeRect = {
                top: windowEl.style.top,
                left: windowEl.style.left,
                width: `${windowEl.offsetWidth}px`,
                height: `${windowEl.offsetHeight}px`
            };
            windowEl.classList.add('is-maximized');
        }
        updateScrollLock();
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
        ghost.style.animationDirection = 'reverse';
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

            // 新增：如果恢复的是一个最大化的窗口，确保底层也滚动到顶部
            if (windowEl.classList.contains('is-maximized')) {
                mainContentArea.scrollTop = 0;
            }
            updateScrollLock();
        };
    }

    // --- 页面初始化逻辑 ---
    document.querySelectorAll('.sidebar-menu li').forEach(li => {
        if (li.querySelector('ul')) li.classList.add('has-submenu');
    });
    document.querySelectorAll('.sidebar-menu li.has-submenu > a').forEach(menuItem => {
        menuItem.addEventListener('click', (e) => {
            e.preventDefault();
            const parentLi = menuItem.parentElement;
            const submenu = parentLi.querySelector('ul');
            if (!submenu) return;

            function adjustAncestorHeight(element, heightChange) {
                const ancestorLi = element.parentElement.closest('li.has-submenu.open');
                if (ancestorLi) {
                    const ancestorUl = ancestorLi.querySelector('ul');
                    if (ancestorUl) {
                        const currentMaxHeight = parseFloat(ancestorUl.style.maxHeight || 0);
                        const newMaxHeight = currentMaxHeight + heightChange;
                        ancestorUl.style.maxHeight = newMaxHeight + 'px';
                        adjustAncestorHeight(ancestorLi, heightChange);
                    }
                }
            }

            const wasOpen = parentLi.classList.contains('open');
            if (wasOpen) {
                const heightToSubtract = submenu.scrollHeight;
                parentLi.classList.remove('open');
                menuItem.classList.remove('active');
                submenu.style.maxHeight = null;
                adjustAncestorHeight(parentLi, -heightToSubtract);
                return;
            }

            [...parentLi.parentElement.children].forEach(sibling => {
                if (sibling !== parentLi && sibling.classList.contains('open')) {
                    const siblingSubmenu = sibling.querySelector('ul');
                    if (siblingSubmenu) {
                        const heightToSubtract = siblingSubmenu.scrollHeight;
                        sibling.classList.remove('open');
                        sibling.querySelector('a')?.classList.remove('active');
                        siblingSubmenu.style.maxHeight = null;
                        adjustAncestorHeight(sibling, -heightToSubtract);
                    }
                }
            });

            parentLi.classList.add('open');
            menuItem.classList.add('active');
            let heightToAdd = submenu.scrollHeight;
            submenu.style.maxHeight = heightToAdd + "px";
            adjustAncestorHeight(parentLi, heightToAdd);
        });
    });

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
                }, { once: true });
            });
        }
    }

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
