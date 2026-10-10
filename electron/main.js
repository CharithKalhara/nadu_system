import {
    app,
    BrowserWindow,
    Menu,
    shell,
    dialog,
    ipcMain,
    net,
    safeStorage,
} from "electron";

import fs from "fs/promises";
import path from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

let mainWindow;

const APP_URL = "https://nadu.office";
const APP_ICON = path.join(__dirname, "nadu-icon.ico");
const APP_TITLE = "Nadu System";

function sessionFilePath() {
    return path.join(app.getPath("userData"), "cloud-session.bin");
}

async function readSessionToken() {
    if (!safeStorage.isEncryptionAvailable()) {
        throw new Error("Secure local session storage is unavailable on this computer.");
    }

    try {
        const encrypted = await fs.readFile(sessionFilePath());

        return safeStorage.decryptString(encrypted);
    } catch (error) {
        if (error.code === "ENOENT") {
            return null;
        }

        throw error;
    }
}

async function saveSessionToken(token) {
    if (!safeStorage.isEncryptionAvailable()) {
        throw new Error("Secure local session storage is unavailable on this computer.");
    }

    await fs.writeFile(sessionFilePath(), safeStorage.encryptString(token));
}

async function clearSessionToken() {
    await fs.rm(sessionFilePath(), { force: true });
}

function desktopApiUrl(endpoint) {
    if (typeof endpoint !== "string" || !endpoint.startsWith("/api/desktop/") || endpoint.includes("..")) {
        throw new Error("Invalid desktop API request.");
    }

    const url = new URL(endpoint, APP_URL);

    if (url.origin !== APP_URL) {
        throw new Error("Invalid desktop API host.");
    }

    return url;
}

async function cloudRequest(endpoint, { method = "GET", body, token } = {}) {
    const headers = { Accept: "application/json" };

    if (body !== undefined) {
        headers["Content-Type"] = "application/json";
    }

    if (token) {
        headers.Authorization = `Bearer ${token}`;
    }

    const response = await net.fetch(desktopApiUrl(endpoint), {
        method,
        headers,
        body: body === undefined ? undefined : JSON.stringify(body),
    });
    const contentType = response.headers.get("content-type") ?? "";
    const result = contentType.includes("application/json")
        ? await response.json()
        : { message: await response.text() };

    if (!response.ok) {
        const validationMessage = result.errors
            ? Object.values(result.errors).flat().join(" ")
            : null;
        throw new Error(validationMessage || result.message || `Cloud request failed (${response.status}).`);
    }

    return result;
}

async function requireSessionToken() {
    const token = await readSessionToken();

    if (!token) {
        throw new Error("Please sign in to the cloud system.");
    }

    return token;
}

function registerDesktopApi() {
    ipcMain.handle("desktop:login", async (_event, credentials) => {
        const result = await cloudRequest("/api/desktop/login", {
            method: "POST",
            body: credentials,
        });
        await saveSessionToken(result.token);

        return { user: result.user };
    });

    ipcMain.handle("desktop:session", async () => {
        const token = await readSessionToken();

        if (!token) {
            return null;
        }

        try {
            return await cloudRequest("/api/desktop/me", { token });
        } catch (error) {
            if (error.message.includes("session expired") || error.message.includes("sign in again")) {
                await clearSessionToken();

                return null;
            }

            throw error;
        }
    });

    ipcMain.handle("desktop:logout", async () => {
        const token = await readSessionToken();

        try {
            if (token) {
                await cloudRequest("/api/desktop/logout", { method: "POST", token });
            }
        } finally {
            await clearSessionToken();
        }

        return { success: true };
    });

    ipcMain.handle("desktop:request", async (_event, request) => {
        if (!request || typeof request !== "object") {
            throw new Error("Invalid request.");
        }

        return cloudRequest(request.endpoint, {
            method: request.method ?? "GET",
            body: request.body,
            token: await requireSessionToken(),
        });
    });

    ipcMain.handle("desktop:download", async (_event, request) => {
        if (!request || typeof request.endpoint !== "string") {
            throw new Error("Invalid download request.");
        }

        const token = await requireSessionToken();
        const response = await net.fetch(desktopApiUrl(request.endpoint), {
            headers: { Authorization: `Bearer ${token}`, Accept: "application/octet-stream" },
        });

        if (!response.ok) {
            throw new Error("Could not download the document from the cloud system.");
        }

        const disposition = response.headers.get("content-disposition") ?? "";
        const fileName = disposition.match(/filename="?([^";]+)"?/i)?.[1] ?? "Nadu Document.docx";
        const save = await dialog.showSaveDialog(mainWindow, {
            defaultPath: fileName.replace(/[<>:"/\\|?*]/g, "_"),
            filters: [{ name: "Word document", extensions: ["docx"] }],
        });

        if (save.canceled || !save.filePath) {
            return { canceled: true };
        }

        await fs.writeFile(save.filePath, Buffer.from(await response.arrayBuffer()));

        return { canceled: false, filePath: save.filePath };
    });
}

function createMenu() {
    const template = [
        {
            label: "File",
            submenu: [
                {
                    label: "Dashboard",
                    click: () => {
                        mainWindow.loadFile(path.join(__dirname, "index.html"));
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
        backgroundColor: "#f8fafc",

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

    mainWindow.loadFile(path.join(__dirname, "index.html"));

    // --------------------------------------------------
    // Keep the native Laravel/Filament navigation visible in the desktop app.
    // --------------------------------------------------

    mainWindow.webContents.on(
        "did-finish-load",
        () => {

            // Keep Electron window title
            mainWindow.setTitle(APP_TITLE);

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

            shell.openExternal(url);

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
    registerDesktopApi();

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
