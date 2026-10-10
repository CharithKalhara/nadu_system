const app = document.getElementById("app");
const modal = document.getElementById("modal");
const modalForm = document.getElementById("modal-form");
const modalBody = document.getElementById("modal-body");
const modalFooter = document.getElementById("modal-footer");
const state = { user: null, page: "dashboard", company: null, search: "", pageNumber: 1, caseOptions: null, toastTimer: null };

const CASE_FIELDS = [
    { section: "Case details" },
    { key: "nadu_ankaya", label: "Case number", required: true },
    { key: "dun_dinaya", label: "Case date", type: "date" },
    { key: "kalaya", label: "Period" },
    { section: "Debtor 1" },
    { key: "nayakaru1_nama", label: "Name" },
    { key: "nayakaru1_samajika_ankaya", label: "Member number" },
    { key: "nayakaru1_lipinaya1", label: "Address line 1" },
    { key: "nayakaru1_lipinaya2", label: "Address line 2" },
    { key: "nayakaru1_lipinaya3", label: "Address line 3" },
    { section: "Debtor 2" },
    { key: "nayakaru2_nama", label: "Name" },
    { key: "nayakaru2_samajika_ankaya", label: "Member number" },
    { key: "nayakaru2_lipinaya1", label: "Address line 1" },
    { key: "nayakaru2_lipinaya2", label: "Address line 2" },
    { key: "nayakaru2_lipinaya3", label: "Address line 3" },
    { section: "Guarantor 1" },
    { key: "aepakaru1_nama", label: "Name" },
    { key: "aepakaru1_samajika_ankaya", label: "Member number" },
    { key: "aepakaru1_lipinaya1", label: "Address line 1" },
    { key: "aepakaru1_lipinaya2", label: "Address line 2" },
    { key: "aepakaru1_lipinaya3", label: "Address line 3" },
    { section: "Guarantor 2" },
    { key: "aepakaru2_nama", label: "Name" },
    { key: "aepakaru2_samajika_ankaya", label: "Member number" },
    { key: "aepakaru2_lipinaya1", label: "Address line 1" },
    { key: "aepakaru2_lipinaya2", label: "Address line 2" },
    { key: "aepakaru2_lipinaya3", label: "Address line 3" },
    { section: "Amounts and dates" },
    { key: "dun_naya_mudala", label: "Loan amount", type: "number" },
    { key: "arawul_mudala", label: "Dispute amount", type: "number" },
    { key: "poli_prathishathaya", label: "Interest rate", type: "number" },
    { key: "awasan_mudal_bendima", label: "Final payment date", type: "date" },
    { key: "dina_ganuna", label: "Number of days", type: "number" },
    { key: "apal_pa", label: "Appeal amount 1", type: "number" },
    { key: "apal_vi", label: "Appeal amount 2", type: "number" },
    { key: "mul_mudala", label: "Principal", type: "number" },
    { key: "poliya", label: "Interest", type: "number" },
    { key: "nadu_gasthu", label: "Case fees", type: "number" },
    { key: "total", label: "Total", type: "number" },
];

const DOCUMENT_LABELS = {
    sithasi: "Sithasi",
    envelope: "Envelope",
    statement: "Statement",
    cover_page: "Cover Page",
    thinduwa_yawima: "Thinduwa Yawima",
    thinduwa_written: "Thinduwa Written",
    thirakawara_journal: "Thirakawara Journal",
    mudritha_pradanaya: "Mudritha Pradanaya",
    mudritha_pradanaya_second_page: "Mudritha Pradanaya (Second Page)",
    wibaga_dinaya_1: "1 Wibaga Dinaya",
    wibaga_dinaya_2: "2 Wibaga Dinaya",
    mul_kola_2: "Mul Kola 2",
    hethupata: "Hethupata",
    sithasi_and_envelope: "Sithasi and Envelope",
};

const DOCUMENT_FIELDS = {
    sithasi: ["nadu_ankaya_format", "teeraka", "karyalaya", "wibhaga_dinaya", "welawa"],
    thinduwa_yawima: ["teeraka_name_with_initials", "thepal_gasthu"],
    thinduwa_written: ["thinduwa_labadena_dinaya", "gewia_yuthu_dinaya", "abiyachana_idiripath_kala_yuthu_dinaya", "thepal_gasthu"],
    thirakawara_journal: ["samithiya_lipinaya", "niyojithaya", "niyojithaya_lipinaya_1", "niyojithaya_lipinaya_2", "niyojithaya_lipinaya_3", "first_sithasiya_date", "first_sithasiya_post_office", "first_sithasiya_receipt_no", "second_sithasiya_date", "second_sithasiya_post_office", "second_sithasiya_receipt_no"],
    wibaga_dinaya_1: ["first_sithasiya_date", "second_sithasiya_date", "wibaga_sthanaya", "niyojithaya"],
    wibaga_dinaya_2: ["first_sithasiya_date", "second_sithasiya_date", "wibaga_sthanaya", "niyojithaya"],
    mul_kola_2: ["nadu_ankaya_format", "samithiya_lipinaya", "niyojithaya", "niyojithaya_lipinaya_1", "niyojithaya_lipinaya_2", "niyojithaya_lipinaya_3"],
};

const SETTING_LABELS = {
    nadu_ankaya_format: "Case number format",
    teeraka: "Judge",
    teeraka_name_with_initials: "Judge name with initials",
    karyalaya: "Court",
    wibhaga_dinaya: "Trial date",
    thinduwa_labadena_dinaya: "Judgment date",
    gewia_yuthu_dinaya: "Payment due date",
    abiyachana_idiripath_kala_yuthu_dinaya: "Appeal submission date",
    thepal_gasthu: "Postage fee",
    welawa: "Time (24 hour)",
    samithiya_lipinaya: "Society address",
    niyojithaya: "Representative",
    niyojithaya_lipinaya_1: "Representative address 1",
    niyojithaya_lipinaya_2: "Representative address 2",
    niyojithaya_lipinaya_3: "Representative address 3",
    first_sithasiya_date: "First notice date",
    first_sithasiya_post_office: "First post office",
    first_sithasiya_receipt_no: "First receipt number",
    second_sithasiya_date: "Second notice date",
    second_sithasiya_post_office: "Second post office",
    second_sithasiya_receipt_no: "Second receipt number",
    wibaga_sthanaya: "Trial location",
};

function escapeHtml(value) {
    return String(value ?? "").replace(/[&<>"']/g, (char) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[char]);
}

async function api(endpoint, method = "GET", body) {
    return window.electronAPI.request({ endpoint, method, body });
}

function toast(message, isError = false) {
    const node = document.getElementById("toast");
    node.textContent = message;
    node.className = `toast show${isError ? " error" : ""}`;
    window.clearTimeout(state.toastTimer);
    state.toastTimer = window.setTimeout(() => node.classList.remove("show"), 3200);
}

function loading(message = "Loading…") {
    return `<div class="loading-state"><span class="loading-dots"><i></i><i></i><i></i></span><span>${escapeHtml(message)}</span></div>`;
}

function openModal({ title, kicker = "", content, submitLabel = "Save changes", onSubmit }) {
    document.getElementById("modal-title").textContent = title;
    document.getElementById("modal-kicker").textContent = kicker;
    modalBody.innerHTML = content;
    modalFooter.innerHTML = `<button type="button" class="button" data-close-modal>Cancel</button><button type="submit" class="button button--primary">${escapeHtml(submitLabel)}</button>`;
    modalForm.onsubmit = async (event) => {
        event.preventDefault();
        const submit = modalFooter.querySelector('[type="submit"]');
        submit.disabled = true;
        submit.textContent = "Saving…";

        try {
            await onSubmit(new FormData(modalForm));
            modal.close();
        } catch (error) {
            toast(error.message, true);
        } finally {
            submit.disabled = false;
            submit.textContent = submitLabel;
        }
    };
    modalFooter.querySelector("[data-close-modal]").addEventListener("click", () => modal.close());
    modal.showModal();
}

function fieldHtml({ key, label, type = "text", value = "", required = false, hint = "" }) {
    const inputType = type === "time" ? "time" : type;
    const safeValue = type === "date" && value ? String(value).slice(0, 10) : type === "time" && value ? String(value).slice(0, 5) : value;
    return `<div class="field"><label for="field-${escapeHtml(key)}">${escapeHtml(label)}${required ? " *" : ""}</label><input id="field-${escapeHtml(key)}" name="${escapeHtml(key)}" type="${escapeHtml(inputType)}" value="${escapeHtml(safeValue)}" ${required ? "required" : ""} ${type === "number" ? 'step="0.01"' : ""}>${hint ? `<span class="field__hint">${escapeHtml(hint)}</span>` : ""}</div>`;
}

function formFromFields(fields, values = {}) {
    return `<div class="form-grid">${fields.map((field) => field.section
        ? `<div class="form-section">${escapeHtml(field.section)}</div>`
        : fieldHtml({ ...field, value: values[field.key] ?? "" })).join("")}</div>`;
}

function renderShell(content) {
    const companyName = state.company?.company_name;
    const nav = [
        ["dashboard", "⌂", "Dashboard"],
        ["companies", "▦", "Companies"],
        ...(state.user?.role === "admin" ? [["users", "♙", "Users"]] : []),
        ...(companyName ? [["company", "▣", companyName], ["cases", "▤", "Nadu Records"]] : []),
        ...(companyName && state.user?.role === "admin" ? [["documents", "▧", "Documents"]] : []),
    ];
    app.innerHTML = `<div class="app-shell"><header class="topbar"><div class="brand"><span class="brand__mark">N</span><span class="brand__name">Nadu System</span></div><div class="topbar__spacer"></div><span class="topbar__user">${escapeHtml(state.user.name)} · ${escapeHtml(state.user.role)}</span><button class="button button--dark button--small" data-action="logout">Sign out</button></header><div class="workspace"><aside class="sidebar"><p class="nav-label">Workspace</p>${nav.map(([page, icon, label]) => `<button class="nav-button ${state.page === page ? "active" : ""}" data-page="${escapeHtml(page)}"><span class="nav-button__icon">${icon}</span><span>${escapeHtml(label)}</span></button>`).join("")}</aside><section class="main" id="content">${content}</section></div></div>`;
    attachShellEvents();
}

function attachShellEvents() {
    app.querySelectorAll("[data-page]").forEach((button) => button.addEventListener("click", () => {
        state.page = button.dataset.page;
        state.search = "";
        state.pageNumber = 1;
        renderPage();
    }));
    app.querySelector('[data-action="logout"]')?.addEventListener("click", async () => {
        try { await window.electronAPI.logout(); } catch { /* Clear local sign-in even if offline. */ }
        state.user = null;
        state.company = null;
        renderLogin();
    });
}

function pageHeading(title, subtitle = "", actions = "") {
    return `<div class="page-heading"><div><p class="eyebrow">Nadu System</p><h1>${escapeHtml(title)}</h1>${subtitle ? `<p>${escapeHtml(subtitle)}</p>` : ""}</div><div class="button-row">${actions}</div></div>`;
}

async function renderPage() {
    if (!state.user) return renderLogin();
    const content = document.getElementById("content");
    if (!content) return;
    content.innerHTML = loading();

    try {
        switch (state.page) {
            case "companies": return await renderCompanies(content);
            case "company": return await renderCompany(content);
            case "cases": return await renderCases(content);
            case "documents": return await renderDocuments(content);
            case "users": return await renderUsers(content);
            default: return await renderDashboard(content);
        }
    } catch (error) {
        content.innerHTML = `<div class="card error-card"><strong>Could not load this page.</strong><p>${escapeHtml(error.message)}</p><button class="button button--small" data-retry>Try again</button></div>`;
        content.querySelector("[data-retry]").addEventListener("click", renderPage);
    }
}

async function renderDashboard(content) {
    const [summary, companies] = await Promise.all([api("/api/desktop/dashboard"), api("/api/desktop/companies")]);
    state.companies = companies.data;
    content.innerHTML = `${pageHeading("Dashboard", "Your companies, case records, and generated documents.", `<button class="button" data-page="companies">All companies</button>`)}<div class="stats-grid">${[["Companies", summary.companies, "Configured in the system"], ["Nadu records", summary.records, "Across available companies"], ...(summary.documents !== null ? [["Documents", summary.documents, "Generated in the system"]] : []), ...(summary.users !== null ? [["Users", summary.users, "Registered users"]] : [])].map(([label, value, note]) => `<article class="card stat-card"><div class="stat-card__label">${escapeHtml(label)}</div><div class="stat-card__value">${Number(value).toLocaleString()}</div><div class="stat-card__note">${escapeHtml(note)}</div></article>`).join("")}</div><section class="section"><div class="section-heading"><h2>Companies</h2><button class="button button--small" data-page="companies">View all →</button></div>${companyCards(companies.data.slice(0, 4))}</section>`;
    content.querySelectorAll('[data-page="companies"]').forEach((button) => button.addEventListener("click", () => { state.page = "companies"; renderPage(); }));
    content.querySelectorAll("[data-company]").forEach((card) => card.addEventListener("click", () => selectCompany(card.dataset.company)));
}

function companyCards(companies) {
    if (!companies.length) return `<div class="card table-empty">No companies are available for this account.</div>`;
    return `<div class="company-grid">${companies.map((company) => `<article class="card company-card" data-company="${company.id}"><div class="company-card__top"><h3>${escapeHtml(company.company_name)}</h3><span class="badge ${company.status === "typing" ? "badge--typing" : ""}">${escapeHtml(company.status)}</span></div><div class="company-card__stats"><span>Open workspace →</span></div></article>`).join("")}</div>`;
}

async function renderCompanies(content) {
    const result = await api("/api/desktop/companies");
    state.companies = result.data;
    const createAction = `<button class="button button--primary" data-create-company>New Company</button>`;
    content.innerHTML = `${pageHeading("Companies", "Open a company workspace to manage its cases and documents.", createAction)}${companyCards(result.data)}`;
    content.querySelectorAll("[data-company]").forEach((card) => card.addEventListener("click", () => selectCompany(card.dataset.company)));
    content.querySelector("[data-create-company]")?.addEventListener("click", showCreateCompany);
}

async function selectCompany(id) {
    const details = await api(`/api/desktop/companies/${encodeURIComponent(id)}`);
    state.company = { ...details.company, records_count: details.records_count, documents_count: details.documents_count };
    state.page = "company";
    state.search = "";
    await renderShell(loading());
    await renderPage();
}

async function renderCompany(content) {
    if (!state.company) { state.page = "companies"; return renderCompanies(content); }
    const details = await api(`/api/desktop/companies/${state.company.id}`);
    state.company = { ...details.company, records_count: details.records_count, documents_count: details.documents_count };
    const settingsAction = state.user.role === "admin" ? `<button class="button" data-edit-company>Company settings</button><button class="button button--danger" data-delete-company>Delete company</button>` : "";
    const documentCard = state.user.role === "admin" ? `<article class="card quick-card" data-target="documents"><h3>Documents</h3><p>Generate and download company documents.</p><div class="quick-card__count">${Number(details.documents_count ?? 0).toLocaleString()}</div></article>` : "";
    const documentStat = state.user.role === "admin" ? `<article class="card stat-card"><div class="stat-card__label">Documents</div><div class="stat-card__value">${Number(details.documents_count ?? 0).toLocaleString()}</div><div class="stat-card__note">Generated for this company</div></article>` : "";
    content.innerHTML = `${pageHeading(state.company.company_name, "Company workspace", settingsAction)}<div class="stats-grid"><article class="card stat-card"><div class="stat-card__label">Nadu records</div><div class="stat-card__value">${Number(details.records_count).toLocaleString()}</div><div class="stat-card__note">In this company</div></article>${documentStat}</div><section class="section"><div class="section-heading"><h2>Quick actions</h2><span class="badge ${state.company.status === "typing" ? "badge--typing" : ""}">${escapeHtml(state.company.status)}</span></div><div class="quick-grid"><article class="card quick-card" data-target="cases"><h3>Nadu Records</h3><p>View and edit cases for this company.</p><div class="quick-card__count">${Number(details.records_count).toLocaleString()}</div></article>${documentCard}</div></section>`;
    content.querySelectorAll("[data-target]").forEach((card) => card.addEventListener("click", () => { state.page = card.dataset.target; state.pageNumber = 1; renderShell(loading()); renderPage(); }));
    content.querySelector("[data-edit-company]")?.addEventListener("click", () => showCompanySettings(state.company));
    content.querySelector("[data-delete-company]")?.addEventListener("click", deleteCompany);
}

function ensureCompany() {
    if (!state.company) { state.page = "companies"; renderShell(loading()); renderPage(); return false; }
    return true;
}

async function renderCases(content) {
    if (!ensureCompany()) return;
    const query = new URLSearchParams({ page: state.pageNumber, per_page: 50, search: state.search });
    const result = await api(`/api/desktop/companies/${state.company.id}/cases?${query}`);
    const rows = result.data;
    content.innerHTML = `${pageHeading("Nadu Records", state.company.company_name, `<button class="button" data-action="company">Company dashboard</button><button class="button button--primary" data-new-case>Add case</button>`)}<div class="toolbar"><div class="search"><input type="search" placeholder="Search case number, debtor, or member…" value="${escapeHtml(state.search)}" data-case-search></div><span class="muted">${Number(result.total).toLocaleString()} records</span></div><div class="card table-wrap">${rows.length ? `<table><thead><tr><th>Case number</th><th>Debtor</th><th>Member number</th><th>Case date</th><th></th></tr></thead><tbody>${rows.map((row) => `<tr><td><strong>${escapeHtml(row.nadu_ankaya)}</strong></td><td>${escapeHtml(row.nayakaru1_nama)}</td><td>${escapeHtml(row.nayakaru1_samajika_ankaya)}</td><td>${escapeHtml(row.dun_dinaya)}</td><td><div class="table-actions"><button class="button button--small" data-edit-case="${row.id}">Edit</button><button class="button button--small button--danger" data-delete-case="${row.id}">Delete</button></div></td></tr>`).join("")}</tbody></table>` : `<div class="table-empty">No records found. Add a case or change your search.</div>`}<footer class="table-footer"><span>Showing ${rows.length ? result.from : 0}–${result.to ?? 0} of ${result.total}</span><div class="button-row"><button class="button button--small" data-page-number="${Math.max(1, state.pageNumber - 1)}" ${state.pageNumber <= 1 ? "disabled" : ""}>Previous</button><span>Page ${result.current_page} of ${result.last_page}</span><button class="button button--small" data-page-number="${Math.min(result.last_page, state.pageNumber + 1)}" ${state.pageNumber >= result.last_page ? "disabled" : ""}>Next</button></div></footer></div>`;
    content.querySelector('[data-action="company"]').addEventListener("click", () => { state.page = "company"; renderShell(loading()); renderPage(); });
    content.querySelector("[data-new-case]").addEventListener("click", () => showCaseForm());
    const search = content.querySelector("[data-case-search]");
    search.addEventListener("input", () => {
        window.clearTimeout(state.searchTimer);
        state.searchTimer = window.setTimeout(() => { state.search = search.value; state.pageNumber = 1; renderPage(); }, 250);
    });
    content.querySelectorAll("[data-page-number]").forEach((button) => button.addEventListener("click", () => { state.pageNumber = Number(button.dataset.pageNumber); renderPage(); }));
    content.querySelectorAll("[data-edit-case]").forEach((button) => button.addEventListener("click", () => showCaseForm(rows.find((row) => row.id === Number(button.dataset.editCase)))));
    content.querySelectorAll("[data-delete-case]").forEach((button) => button.addEventListener("click", () => deleteCase(button.dataset.deleteCase)));
}

function readFormData(formData) {
    return Object.fromEntries([...formData.entries()].map(([key, value]) => [key, value === "" ? null : value]));
}

function showCaseForm(record = null) {
    openModal({
        title: record ? "Edit case record" : "Add case record",
        kicker: state.company.company_name,
        content: formFromFields(CASE_FIELDS, record ?? {}),
        onSubmit: async (form) => {
            const body = readFormData(form);
            const endpoint = `/api/desktop/companies/${state.company.id}/cases${record ? `/${record.id}` : ""}`;
            await api(endpoint, record ? "PUT" : "POST", body);
            toast(record ? "Case record updated." : "Case record created.");
            await renderPage();
        },
    });
}

async function deleteCase(id) {
    if (!confirm("Delete this Nadu record? This cannot be undone.")) return;
    try {
        await api(`/api/desktop/companies/${state.company.id}/cases/${id}`, "DELETE");
        toast("Case record deleted.");
        await renderPage();
    } catch (error) { toast(error.message, true); }
}

async function deleteCompany() {
    if (!confirm(`Delete ${state.company.company_name} and its entire case table? This permanently deletes all its case records.`)) return;
    try {
        await api(`/api/desktop/companies/${state.company.id}`, "DELETE");
        state.company = null;
        state.page = "companies";
        toast("Company and case table deleted.");
        await renderShell(loading());
        await renderPage();
    } catch (error) { toast(error.message, true); }
}

async function renderDocuments(content) {
    if (!ensureCompany()) return;
    const result = await api(`/api/desktop/companies/${state.company.id}/documents?per_page=100`);
    const rows = result.data;
    content.innerHTML = `${pageHeading("Documents", state.company.company_name, `<button class="button" data-action="company">Company dashboard</button><button class="button button--primary" data-new-document>Create document</button>`)}<div class="card table-wrap">${rows.length ? `<table><thead><tr><th>Document type</th><th>File name</th><th>Case</th><th>Generated by</th><th>Generated on</th><th></th></tr></thead><tbody>${rows.map((row) => `<tr><td>${escapeHtml(DOCUMENT_LABELS[row.document_type] ?? row.document_type)}</td><td>${escapeHtml(row.file_name)}</td><td>${escapeHtml(row.nadu?.nadu_ankaya ?? (row.bulk_record_count ? `${row.bulk_record_count} cases` : "—"))}</td><td>${escapeHtml(row.user?.name ?? "—")}</td><td>${escapeHtml(row.created_at?.slice(0, 16).replace("T", " ") ?? "")}</td><td><div class="table-actions"><button class="button button--small" data-download="${row.id}">Download</button><button class="button button--small button--danger" data-delete-document="${row.id}">Delete</button></div></td></tr>`).join("")}</tbody></table>` : `<div class="table-empty">No documents have been generated for this company.</div>`}</div>`;
    content.querySelector('[data-action="company"]').addEventListener("click", () => { state.page = "company"; renderShell(loading()); renderPage(); });
    content.querySelector("[data-new-document]").addEventListener("click", showDocumentForm);
    content.querySelectorAll("[data-download]").forEach((button) => button.addEventListener("click", () => downloadDocument(button.dataset.download)));
    content.querySelectorAll("[data-delete-document]").forEach((button) => button.addEventListener("click", () => deleteDocument(button.dataset.deleteDocument)));
}

function docFieldType(key) {
    if (key.endsWith("_date") || ["wibhaga_dinaya", "thinduwa_labadena_dinaya", "gewia_yuthu_dinaya", "abiyachana_idiripath_kala_yuthu_dinaya"].includes(key)) return "date";
    if (key === "welawa") return "time";
    if (key === "thepal_gasthu") return "number";
    return "text";
}

async function showDocumentForm() {
    try {
        state.caseOptions = await api(`/api/desktop/companies/${state.company.id}/documents/options`);
        const options = state.caseOptions;
        const typeOptions = options.types.map((type) => `<option value="${escapeHtml(type)}">${escapeHtml(DOCUMENT_LABELS[type] ?? type)}</option>`).join("");
        const caseList = options.cases.map((record) => `<label class="check-option"><input type="checkbox" name="case-check" value="${record.id}"><span>${escapeHtml(record.nadu_ankaya)}${record.nayakaru1_nama ? ` · ${escapeHtml(record.nayakaru1_nama)}` : ""}</span></label>`).join("");
        openModal({
            title: "Create document",
            kicker: state.company.company_name,
            submitLabel: "Create and save",
            content: `<div class="field"><label for="document-type">Document type *</label><select id="document-type" name="document_type" required>${typeOptions}</select></div><div id="document-extra"></div>`,
            onSubmit: async (form) => {
                const type = form.get("document_type");
                const scope = type === "sithasi_and_envelope" ? null : form.get("scope");
                const body = readFormData(form);
                body.scope = scope;
                const selected = [...modalBody.querySelectorAll('[name="case-check"]:checked')].map((check) => Number(check.value));
                if (scope === "selected") body.nadu_ids = selected;
                if (type === "sithasi_and_envelope") body.nadu_id = modalBody.querySelector('[name="nadu_id"]').value;
                const result = await api(`/api/desktop/companies/${state.company.id}/documents`, "POST", body);
                toast(result.message);
                await renderPage();
            },
        });
        const updateFields = () => {
            const type = modalBody.querySelector('[name="document_type"]').value;
            const isCombined = type === "sithasi_and_envelope";
            const specific = (DOCUMENT_FIELDS[type] ?? []).map((key) => fieldHtml({ key, label: SETTING_LABELS[key] ?? key, type: docFieldType(key), value: options.settings?.[key] ?? "", required: requiredDocumentFields(type).includes(key) })).join("");
            modalBody.querySelector("#document-extra").innerHTML = `${specific ? `<div class="form-grid">${specific}</div>` : ""}${isCombined ? `<div class="field"><label>Case *</label><select name="nadu_id" required>${options.cases.map((record) => `<option value="${record.id}">${escapeHtml(record.nadu_ankaya)} — ${escapeHtml(record.nayakaru1_nama)}</option>`).join("")}</select></div>` : `<div class="field"><label for="scope">Generate for *</label><select id="scope" name="scope"><option value="all">All records in this company</option><option value="selected">Selected records</option></select></div><div class="field" data-selected-cases hidden><label>Select records *</label><div class="check-list">${caseList || `<span class="muted">No cases found in this company.</span>`}</div></div>`}`;
            modalBody.querySelector('[name="scope"]')?.addEventListener("change", (event) => {
                modalBody.querySelector("[data-selected-cases]").hidden = event.target.value !== "selected";
            });
        };
        modalBody.querySelector('[name="document_type"]').addEventListener("change", updateFields);
        updateFields();
    } catch (error) { toast(error.message, true); }
}

function requiredDocumentFields(type) {
    return {
        sithasi: ["nadu_ankaya_format", "teeraka", "karyalaya", "wibhaga_dinaya", "welawa"],
        thinduwa_yawima: ["teeraka_name_with_initials", "thepal_gasthu"],
        thinduwa_written: ["thinduwa_labadena_dinaya", "gewia_yuthu_dinaya", "abiyachana_idiripath_kala_yuthu_dinaya", "thepal_gasthu"],
        thirakawara_journal: ["niyojithaya", "niyojithaya_lipinaya_1", "first_sithasiya_date", "first_sithasiya_post_office", "first_sithasiya_receipt_no"],
        wibaga_dinaya_1: ["first_sithasiya_date", "second_sithasiya_date", "wibaga_sthanaya", "niyojithaya"],
        wibaga_dinaya_2: ["first_sithasiya_date", "second_sithasiya_date", "wibaga_sthanaya", "niyojithaya"],
        mul_kola_2: ["nadu_ankaya_format", "samithiya_lipinaya", "niyojithaya", "niyojithaya_lipinaya_1"],
    }[type] ?? [];
}

async function downloadDocument(id) {
    try {
        const result = await window.electronAPI.download({ endpoint: `/api/desktop/companies/${state.company.id}/documents/${id}/download` });
        if (!result.canceled) toast(`Saved document to ${result.filePath}`);
    } catch (error) { toast(error.message, true); }
}

async function deleteDocument(id) {
    if (!confirm("Delete this document record? The generated file stays on the server.")) return;
    try {
        await api(`/api/desktop/companies/${state.company.id}/documents/${id}`, "DELETE");
        toast("Document record deleted.");
        await renderPage();
    } catch (error) { toast(error.message, true); }
}

function showCompanySettings(company) {
    const fields = [
        { key: "company_name", label: "Company name", required: true },
        { key: "status", label: "Status", type: "select", options: ["typing", "completed"] },
        ...Object.keys(SETTING_LABELS).map((key) => ({ key, label: SETTING_LABELS[key], type: docFieldType(key) })),
    ];
    const content = `<div class="form-grid">${fields.map((field) => field.type === "select"
        ? `<div class="field"><label>${escapeHtml(field.label)}</label><select name="${field.key}">${field.options.map((option) => `<option value="${option}" ${company.status === option ? "selected" : ""}>${escapeHtml(option)}</option>`).join("")}</select></div>`
        : fieldHtml({ ...field, value: company[field.key] ?? "" })).join("")}</div>`;
    openModal({
        title: "Company settings",
        kicker: "Document defaults",
        content,
        onSubmit: async (form) => {
            const result = await api(`/api/desktop/companies/${company.id}`, "PATCH", readFormData(form));
            state.company = { ...state.company, ...result.company };
            toast("Company settings saved.");
            await renderPage();
        },
    });
}

function showCreateCompany() {
    const statusField = state.user.role === "admin"
        ? `<div class="field"><label>Status *</label><select name="status"><option value="completed">Completed</option><option value="typing">Typing</option></select></div>`
        : `<input type="hidden" name="status" value="typing"><p class="muted">This company will be created in Typing status.</p>`;
    openModal({
        title: "New company",
        content: `<div class="field"><label>Company name *</label><input name="company_name" required maxlength="255"></div>${statusField}`,
        onSubmit: async (form) => {
            await api("/api/desktop/companies", "POST", readFormData(form));
            toast("Company and case table created.");
            await renderPage();
        },
    });
}

async function renderUsers(content) {
    if (state.user.role !== "admin") { state.page = "dashboard"; return renderDashboard(content); }
    const result = await api("/api/desktop/users");
    content.innerHTML = `${pageHeading("Users", "Manage sign-in accounts and access roles.", `<button class="button button--primary" data-new-user>Add user</button>`)}<div class="card table-wrap"><table><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Created</th><th></th></tr></thead><tbody>${result.data.map((user) => `<tr><td><strong>${escapeHtml(user.name)}</strong></td><td>${escapeHtml(user.email)}</td><td><span class="badge ${user.role === "typing" ? "badge--typing" : ""}">${escapeHtml(user.role)}</span></td><td>${escapeHtml(user.created_at?.slice(0, 10) ?? "")}</td><td><div class="table-actions"><button class="button button--small" data-edit-user="${user.id}">Edit</button><button class="button button--small button--danger" data-delete-user="${user.id}">Delete</button></div></td></tr>`).join("")}</tbody></table></div>`;
    content.querySelector("[data-new-user]").addEventListener("click", () => showUserForm());
    content.querySelectorAll("[data-edit-user]").forEach((button) => button.addEventListener("click", () => showUserForm(result.data.find((user) => user.id === Number(button.dataset.editUser)))));
    content.querySelectorAll("[data-delete-user]").forEach((button) => button.addEventListener("click", () => deleteUser(button.dataset.deleteUser)));
}

function showUserForm(user = null) {
    openModal({
        title: user ? "Edit user" : "Add user",
        content: `<div class="form-grid"><div class="field"><label>Name *</label><input name="name" required value="${escapeHtml(user?.name)}"></div><div class="field"><label>Email *</label><input name="email" type="email" required value="${escapeHtml(user?.email)}"></div><div class="field"><label>Role *</label><select name="role"><option value="admin" ${user?.role === "admin" ? "selected" : ""}>Administrator</option><option value="typing" ${!user || user.role === "typing" ? "selected" : ""}>Typing</option></select></div><div class="field"><label>${user ? "New password (optional)" : "Password *"}</label><input name="password" type="password" ${user ? "minlength=8" : "required minlength=8"} autocomplete="new-password"></div></div>`,
        onSubmit: async (form) => {
            const body = readFormData(form);
            if (!body.password) delete body.password;
            await api(`/api/desktop/users${user ? `/${user.id}` : ""}`, user ? "PUT" : "POST", body);
            toast(user ? "User updated." : "User created.");
            await renderPage();
        },
    });
}

async function deleteUser(id) {
    if (!confirm("Delete this user account?")) return;
    try { await api(`/api/desktop/users/${id}`, "DELETE"); toast("User deleted."); await renderPage(); }
    catch (error) { toast(error.message, true); }
}

function renderLogin(message = "Sign in using your Nadu System account.") {
    app.innerHTML = `<section class="login-page"><div class="login-card"><div class="login-card__brand"><span class="brand__mark">N</span><div><h1>Nadu System</h1><p>Secure desktop workspace</p></div></div><p>${escapeHtml(message)}</p><form id="login-form"><div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" autocomplete="username" required></div><div class="field"><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required></div><div class="field" id="login-error" hidden></div><button class="button button--primary button--full" type="submit">Sign in</button></form><p class="field__hint login-hint">Connects securely to the Nadu System cloud server. Your data stays in the company database.</p></div></section>`;
    app.querySelector("#login-form").addEventListener("submit", async (event) => {
        event.preventDefault();
        const button = event.currentTarget.querySelector('[type="submit"]');
        const error = app.querySelector("#login-error");
        button.disabled = true;
        button.textContent = "Signing in…";
        error.hidden = true;
        try {
            const credentials = Object.fromEntries(new FormData(event.currentTarget).entries());
            const result = await window.electronAPI.login(credentials);
            state.user = result.user;
            state.company = null;
            state.page = "dashboard";
            await renderShell(loading());
            await renderPage();
        } catch (exception) {
            error.textContent = exception.message;
            error.hidden = false;
        } finally {
            button.disabled = false;
            button.textContent = "Sign in";
        }
    });
}

async function startApp() {
    try {
        const session = await window.electronAPI.session();
        if (!session) return renderLogin();
        state.user = session.user;
        await renderShell(loading());
        await renderPage();
    } catch (error) {
        renderLogin(`Could not reach the cloud system. ${error.message}`);
    }
}

startApp();
