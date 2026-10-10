const { contextBridge, ipcRenderer } = require("electron");

contextBridge.exposeInMainWorld("electronAPI", {
    login: (credentials) => ipcRenderer.invoke("desktop:login", credentials),
    session: () => ipcRenderer.invoke("desktop:session"),
    logout: () => ipcRenderer.invoke("desktop:logout"),
    request: (request) => ipcRenderer.invoke("desktop:request", request),
    download: (request) => ipcRenderer.invoke("desktop:download", request),
});
