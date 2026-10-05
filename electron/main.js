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
                { role: "resetZoom" },
                { role: "zoomIn" },
                { role: "zoomOut" },
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
    mainWindow = new BrowserWindow({
        width: 1400,
        height: 900,

        minWidth: 1000,
        minHeight: 650,

        icon: APP_ICON,

        // Window title
        title: APP_TITLE,

        webPreferences: {
            preload: path.join(__dirname, "preload.js"),
            contextIsolation: true,
            nodeIntegration: false,
            sandbox: true
        }
    });

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

    // --------------------------------------------------
    // Load Laravel application
    // --------------------------------------------------

    mainWindow.loadURL(APP_URL);

    // --------------------------------------------------
    // Customize Laravel/Filament UI for Electron
    // --------------------------------------------------

    mainWindow.webContents.on(
        "did-finish-load",
        async () => {

            // Keep Electron window title
            mainWindow.setTitle(APP_TITLE);

            // Hide Filament top navigation
            await mainWindow.webContents.insertCSS(`
                .fi-topbar {
                    display: none !important;
                }

                .fi-simple-header {
                    display: none !important;
                }

                .fi-simple-main {
                    padding-top: 0 !important;
                }
            `);

            // Hide Laravel/Filament header elements
            await mainWindow.webContents.executeJavaScript(`
                (() => {

                    const elements =
                        [...document.querySelectorAll('*')];

                    for (const element of elements) {

                        const text =
                            element.textContent?.trim();

                        if (text === "Nadu System") {

                            const header =
                                element.closest("header");

                            if (header) {
                                header.style.display = "none";
                            }
                        }

                        if (text === "Return to dashboard") {

                            const button =
                                element.closest("a, button");

                            if (button) {
                                button.style.display = "none";
                            }
                        }
                    }

                })();
            `);

            // Make sure title remains Nadu System
            mainWindow.setTitle(APP_TITLE);
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

    // --------------------------------------------------
    // Window closed
    // --------------------------------------------------

    mainWindow.on("closed", () => {
        mainWindow = null;
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