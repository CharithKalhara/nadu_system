import {
    app,
    BrowserWindow,
    Menu,
    shell,
    dialog
} from "electron";

import path from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

let mainWindow;
let loadingWindow;

const APP_URL = "https://nadu.office";
const APP_ICON = path.join(__dirname, "nadu-icon.ico");
const APP_TITLE = "Nadu System";

function createMenu() {
    const template = [
        {
            label: "File",
            submenu: [
                {
                    label: "Dashboard",
                    click: () => {
                        mainWindow.loadURL(APP_URL);
                    }
                },
                {
                    type: "separator"
                },
                {
                    label: "Reload",
                    accelerator: "CmdOrCtrl+R",
                    click: () => {
                        mainWindow.reload();
                    }
                },
                {
                    type: "separator"
                },
                {
                    label: "Exit",
                    accelerator: "CmdOrCtrl+Q",
                    click: () => {
                        app.quit();
                    }
                }
            ]
        },

        {
            label: "Edit",
            submenu: [
                { role: "undo" },
                { role: "redo" },
                { type: "separator" },
                { role: "cut" },
                { role: "copy" },
                { role: "paste" },
                { role: "selectAll" }
            ]
        },

        {
            label: "View",
            submenu: [
                { role: "reload" },
                { role: "forceReload" },
                { type: "separator" },
                { role: "resetZoom", accelerator: "CmdOrCtrl+0" },
                { role: "zoomIn", accelerator: "CmdOrCtrl+Plus" },
                { role: "zoomOut", accelerator: "CmdOrCtrl+-" },
                { type: "separator" },
                { role: "togglefullscreen" }
            ]
        },

        {
            label: "Window",
            submenu: [
                { role: "minimize" },
                { role: "close" }
            ]
        },

        {
            label: "Help",
            submenu: [
                {
                    label: "About Nadu System",
                    click: () => {
                        dialog.showMessageBox(mainWindow, {
                            type: "info",
                            title: APP_TITLE,
                            message: APP_TITLE,
                            detail: "Desktop Application"
                        });
                    }
                }
            ]
        }
    ];

    Menu.setApplicationMenu(
        Menu.buildFromTemplate(template)
    );
}

function createWindow() {
    loadingWindow = new BrowserWindow({
        width: 1400,
        height: 900,
        minWidth: 1000,
        minHeight: 650,
        icon: APP_ICON,
        title: APP_TITLE,
        backgroundColor: "#ffffff",
        webPreferences: {
            contextIsolation: true,
            nodeIntegration: false,
            sandbox: true
        }
    });
    loadingWindow.loadFile(path.join(__dirname, "index.html"));
    loadingWindow.once("ready-to-show", () => loadingWindow?.show());

    mainWindow = new BrowserWindow({
        width: 1400,
        height: 900,

        minWidth: 1000,
        minHeight: 650,

        icon: APP_ICON,

        // Window title
        title: APP_TITLE,
        backgroundColor: "#ffffff",
        show: false,

        webPreferences: {
            preload: path.join(__dirname, "preload.js"),
            contextIsolation: true,
            nodeIntegration: false,
            sandbox: true
        }
    });

    // Start each app window at normal page zoom and enable touchpad pinch zoom.
    mainWindow.webContents.setZoomMode("isolated");
    mainWindow.webContents.setZoomFactor(1);
    mainWindow.webContents.setVisualZoomLevelLimits(1, 3);

    // --------------------------------------------------
    // Automatically save downloads to Downloads folder
    // --------------------------------------------------

    mainWindow.webContents.session.on(
        "will-download",
        (event, item) => {
            const fileName = item.getFilename();

            const downloadsPath = app.getPath("downloads");

            const filePath = path.join(
                downloadsPath,
                fileName
            );

            item.setSavePath(filePath);

            item.once("done", (event, state) => {
                if (state === "completed") {

                    shell.openPath(filePath);

                } else {

                    dialog.showErrorBox(
                        "Download Failed",
                        `Could not download:\n${fileName}`
                    );

                }
            });
        }
    );

    mainWindow.webContents.on(
        "did-finish-load",
        async () => {

            // Re-enable pinch zoom after each page navigation or refresh.
            await mainWindow.webContents.setVisualZoomLevelLimits(1, 3);

            // Keep Electron window title
            mainWindow.setTitle(APP_TITLE);

            // Keep Filament's navbar, but hide its search and user menu.
            await mainWindow.webContents.insertCSS(`
                .fi-global-search-ctn,
                .fi-topbar .fi-user-menu-trigger,
                .fi-sidebar .fi-user-menu-trigger {
                    display: none !important;
                }

                .electron-previous-button {
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    min-height: 40px;
                    padding: 0 14px;
                    border: 1px solid #d1d5db;
                    border-radius: 8px;
                    background: #fff;
                    color: #1f2937;
                    font: 600 14px Arial, sans-serif;
                    cursor: pointer;
                }

                .electron-previous-button:hover:not(:disabled) {
                    background: #f9fafb;
                }

                .electron-previous-button:disabled {
                    opacity: 0.45;
                    cursor: default;
                }

                .electron-previous-button--floating {
                    position: fixed;
                    top: 76px;
                    right: 24px;
                    z-index: 100000;
                    box-shadow: 0 2px 8px rgb(0 0 0 / 12%);
                }

                #electron-loading-indicator {
                    position: fixed;
                    inset: 0 0 auto;
                    z-index: 2147483647;
                    height: 3px;
                    overflow: hidden;
                    pointer-events: none;
                }

                #electron-loading-indicator::after {
                    content: "";
                    display: block;
                    width: 35%;
                    height: 100%;
                    background: #f59e0b;
                    transform: translateX(-110%);
                    animation: electron-loading-progress 1.1s ease-in-out infinite;
                }

                @keyframes electron-loading-progress {
                    to { transform: translateX(310%); }
                }

                #electron-loading-indicator[hidden] {
                    display: none;
                }

                a.company-workspace-topbar__return {
                    display: none !important;
                }
            `);

            // Add Previous to Filament's navbar and show loading during requests.
            await mainWindow.webContents.executeJavaScript(`
                (() => {
                    const addPreviousButton = () => {
                        const host = document.querySelector('.fi-topbar .fi-topbar-end, .company-workspace-topbar') || document.body;
                        let previous = document.querySelector('.electron-previous-button');

                        if (!previous) {
                            previous = document.createElement('button');
                            previous.type = 'button';
                            previous.className = 'electron-previous-button';
                            previous.title = 'Go to the previous page';
                            previous.innerHTML = '<span aria-hidden="true">&#8592;</span><span>Previous</span>';
                            previous.addEventListener('click', () => {
                                if (history.length > 1) history.back();
                            });
                        }

                        const isFloating = host === document.body;
                        previous.classList.toggle('electron-previous-button--floating', isFloating);
                        if (previous.parentElement !== host) host.append(previous);
                        previous.disabled = history.length <= 1;
                    };

                    addPreviousButton();
                    if (!window.__electronPreviousObserver) {
                        window.__electronPreviousObserver = new MutationObserver(addPreviousButton);
                        window.__electronPreviousObserver.observe(document.body, { childList: true, subtree: true });
                        window.addEventListener('popstate', () => {
                            const previous = document.querySelector('.electron-previous-button');
                            if (previous) previous.disabled = history.length <= 1;
                        });
                    }

                    if (!document.getElementById('electron-loading-indicator')) {
                        const indicator = document.createElement('div');
                        indicator.id = 'electron-loading-indicator';
                        indicator.setAttribute('role', 'progressbar');
                        indicator.setAttribute('aria-label', 'Loading');
                        indicator.hidden = true;
                        document.body.append(indicator);
                    }

                    if (!window.__electronLoadingHooksInstalled) {
                        window.__electronLoadingHooksInstalled = true;
                        let pendingRequests = 0;
                        let hideTimer;
                        const getIndicator = () => {
                            let currentIndicator = document.getElementById('electron-loading-indicator');
                            if (!currentIndicator) {
                                currentIndicator = document.createElement('div');
                                currentIndicator.id = 'electron-loading-indicator';
                                currentIndicator.setAttribute('role', 'progressbar');
                                currentIndicator.setAttribute('aria-label', 'Loading');
                                document.body.append(currentIndicator);
                            }
                            return currentIndicator;
                        };
                        const showLoading = () => {
                            clearTimeout(hideTimer);
                            getIndicator().hidden = false;
                        };
                        const hideLoading = () => {
                            hideTimer = setTimeout(() => {
                                if (pendingRequests === 0) getIndicator().hidden = true;
                            }, 180);
                        };

                        const originalFetch = window.fetch.bind(window);
                        window.fetch = (...args) => {
                            pendingRequests++;
                            showLoading();
                            return originalFetch(...args).finally(() => {
                                pendingRequests = Math.max(0, pendingRequests - 1);
                                if (pendingRequests === 0) hideLoading();
                            });
                        };

                        const originalSend = XMLHttpRequest.prototype.send;
                        XMLHttpRequest.prototype.send = function (...args) {
                            pendingRequests++;
                            showLoading();
                            this.addEventListener('loadend', () => {
                                pendingRequests = Math.max(0, pendingRequests - 1);
                                if (pendingRequests === 0) hideLoading();
                            }, { once: true });
                            return originalSend.apply(this, args);
                        };

                        document.addEventListener('livewire:navigating', showLoading);
                        document.addEventListener('livewire:navigated', hideLoading);
                        window.addEventListener('beforeunload', showLoading);
                        window.addEventListener('pageshow', hideLoading);
                        document.addEventListener('click', (event) => {
                            const link = event.target.closest('a[href]');
                            if (link && link.origin === window.location.origin && !link.target && !event.defaultPrevented) {
                                showLoading();
                            }
                        }, true);
                    }
                })();
            `);
            // Make sure title remains Nadu System
            mainWindow.setTitle(APP_TITLE);

            if (!mainWindow.isVisible()) {
                mainWindow.show();
                loadingWindow?.close();
                loadingWindow = null;
            }
        }
    );

    // --------------------------------------------------
    // Prevent Laravel from changing Electron title
    // --------------------------------------------------

    mainWindow.webContents.on(
        "page-title-updated",
        (event) => {

            event.preventDefault();

            mainWindow.setTitle(APP_TITLE);
        }
    );

    // --------------------------------------------------
    // External links
    // --------------------------------------------------

    mainWindow.webContents.setWindowOpenHandler(
        ({ url }) => {

            if (!url.startsWith(APP_URL)) {
                shell.openExternal(url);
            }

            return {
                action: "deny"
            };
        }
    );

    // --------------------------------------------------
    // Handle loading errors
    // --------------------------------------------------

    mainWindow.webContents.on(
        "did-fail-load",
        (event, errorCode, errorDescription) => {

            if (errorCode === -3) {
                return;
            }

            dialog.showErrorBox(
                APP_TITLE,
                `Unable to load Nadu System.\n\n${errorDescription}`
            );
        }
    );

    // Keep the web page hidden until its desktop-only styling is ready.
    mainWindow.loadURL(APP_URL);

    // --------------------------------------------------
    // Window closed
    // --------------------------------------------------

    mainWindow.on("closed", () => {
        mainWindow = null;
    });

    loadingWindow.on("closed", () => {
        loadingWindow = null;
    });
}

// --------------------------------------------------
// Electron startup
// --------------------------------------------------

app.whenReady().then(() => {

    createMenu();

    createWindow();

    app.on("activate", () => {

        if (
            BrowserWindow.getAllWindows().length === 0
        ) {
            createWindow();
        }

    });

});

// --------------------------------------------------
// Quit application
// --------------------------------------------------

app.on("window-all-closed", () => {

    if (process.platform !== "darwin") {
        app.quit();
    }

});
