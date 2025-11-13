import { e as c, t as a } from "./player.5a295e80.js";
class l {
    constructor(t, e, o) {
        (this._API_URL = c.SERVER.URL_API),
            (this._action = "get"),
            (this._time = t),
            (this._key = e),
            (this._vid = o);
    }
    get() {
        return this._callIWantItAPI();
    }
    _callIWantItAPI() {
        const t = new Request(
            `${this._API_URL}?action=${this._action}&time=${this._time}&key=${this._key}&vid=${this._vid}`
        );
        return fetch(t).then((e) =>
            e.status === 200
                ? e.json()
                : (console.log("error"),
                  {
                      error: "An error occurred while trying to access the I Want It servers. Please try in a few minutes.",
                  })
        );
    }
}
class u {
    constructor(t, e, o, n) {
        (this._eventEmitter = t),
            (this._time = e),
            (this._key = o),
            (this._vid = n);
    }
    async start() {
        (this._showLoadingIcon = !0),
            (this._currentData = await this._getDataFromService(
                this._time,
                this._key,
                this._vid
            )),
            (this._showLoadingIcon = !1),
            this._notifyErrorToUser() || this._populateCurrentFrame();
    }
    end() {
        window.removeEventListener("resize", a(this._updateHotpointPositions)),
            this._emptyCurrentFrame();
    }
    async _getDataFromService(t, e, o) {
        return (
            (this._shopService = new l(t, e, o)),
            (this._data = await this._shopService.get()),
            this._data
        );
    }
    _populateCurrentFrame() {
        const t = document.querySelector(".iwt-hot-points-container");
        (this._hotpointsContainerClickHandler =
            this._hotpointsContainerClickHandler.bind(this)),
            t.addEventListener("click", this._hotpointsContainerClickHandler);
        const e = document.querySelector("template");
        this._currentData.objetos.forEach((o, n) => {
            const i = e.content.cloneNode(!0);
            o.hotpoint_icon &&
                (i
                    .querySelector(".iwt-hot-point")
                    .classList.add("iwtmdf-custom-icon"),
                (i.querySelector(".iwt-hot-point-custom-icon").src =
                    o.hotpoint_icon)),
                (i.querySelector(".iwt-hot-point").title = o.nombre),
                i
                    .querySelector(".iwt-hot-point")
                    .setAttribute("data-iwt-position", n),
                i
                    .querySelector(".iwt-hot-point")
                    .setAttribute("data-iwt-id", o.id),
                i
                    .querySelector(".iwt-hot-point")
                    .setAttribute("data-iwt-auto-open", o.auto_open),
                i
                    .querySelector(".iwt-hot-point")
                    .setAttribute("data-iwt-record-x", o.pos_x),
                i
                    .querySelector(".iwt-hot-point")
                    .setAttribute("data-iwt-record-y", o.pos_y),
                t.appendChild(i),
                this._updateHotpointPositions();
        }),
            (this._updateHotpointPositions =
                this._updateHotpointPositions.bind(this)),
            window.addEventListener("resize", a(this._updateHotpointPositions));
    }
    _emptyCurrentFrame() {
        const t = document.querySelector(".iwt-hot-points-container");
        t.removeEventListener("click", this._hotpointsContainerClickHandler),
            (t.innerHTML = "");
    }
    _updateHotpointPositions() {
        const t = document.querySelector(".iwt-hot-points-container");
        Array.from(
            t.querySelectorAll(".iwt-hot-points-container .iwt-hot-point")
        ).forEach((o) => {
            const n = o.getAttribute("data-iwt-record-x"),
                i = o.getAttribute("data-iwt-record-y"),
                r = o.offsetWidth / 2,
                s = o.offsetHeight / 2;
            (o.style.left = `${n * t.offsetWidth - r}px`),
                (o.style.top = `${i * t.offsetHeight - s}px`);
        });
    }
    _hotpointsContainerClickHandler(t) {
        (this._hotpointId = t.target.getAttribute("data-iwt-id")),
            (this._hotpointPosition =
                t.target.getAttribute("data-iwt-position"));
        let e;
        this._hotpointId &&
            ((e = this._getHotpointInfo(this._hotpointId)),
            e.auto_open === "1"
                ? (this._openNewNavigatorTab(e.url),
                  t.target.classList.remove("iwtmdf-selected"),
                  t.target.blur())
                : (t.target.classList.add("iwtmdf-selected"),
                  this._showRecordModal(this._hotpointId)));
    }
    _getTotalHotpoints() {
        return this._currentData.objetos.length;
    }
    _getHotpointInfo(t) {
        return this._currentData.objetos.find((e) => e.id == t);
    }
    _getHotPointNode(t) {
        return document
            .querySelector(".iwt-hot-points-container")
            .querySelector(`[data-iwt-position="${t}"]`);
    }
    _openNewNavigatorTab(t) {
        window.open(t).focus();
    }
    _showRecordModal(t) {
        this._enableAppUI = !1;
        const e = document.getElementById("iwt-hotpoint-record"),
            o = this._getHotpointInfo(t);
        this._populateRecordModal(e, o),
            (this._recordModalCloseButtonHandler =
                this._recordModalCloseButtonHandler.bind(this)),
            e
                .querySelector(".iwt-hotpoint-record-close")
                .addEventListener("click", this._recordModalCloseButtonHandler),
            this._getTotalHotpoints() == 1
                ? e.classList.add("iwtmdf-no-navigation")
                : e.classList.remove("iwtmdf-no-navigation"),
            (this._recordModalNavigationButtonsHandler =
                this._recordModalNavigationButtonsHandler.bind(this)),
            e
                .querySelector(".iwt-hotpoint-record-prev")
                .addEventListener(
                    "click",
                    this._recordModalNavigationButtonsHandler
                ),
            e
                .querySelector(".iwt-hotpoint-record-next")
                .addEventListener(
                    "click",
                    this._recordModalNavigationButtonsHandler
                ),
            e.showModal();
    }

    /* Funcion modificada para nueva ventana de producto */
    _populateRecordModal(t, e) {
        this._recordModalImageLoadedHandler =
            this._recordModalImageLoadedHandler.bind(this);
        const o = t.querySelector(".iwt-hotpoint-record-product-image");
        (o.src = e.imagen),
            o.addEventListener("load", this._recordModalImageLoadedHandler),
            o.parentNode.classList.add("iwtmdf-loading");
        const n = t.querySelector(".iwt-hotpoint-record-brand-logo");
        (n.src = e.logo),
            n.addEventListener("load", this._recordModalImageLoadedHandler),
            n.parentNode.classList.add("iwtmdf-loading"),
            (t.querySelector(
                ".iwt-hotpoint-record-product-name h3"
            ).textContent = e.nombre),
            (t.querySelector(".iwt-hotpoint-record-link").href = e.url),
            (t.querySelector(
                ".iwt-hotpoint-record-product-description-content"
            ).innerHTML = e.descripcion),
            // (t.querySelector(".iwt-hotpoint-record-brand-name h3").textContent =
            //     e.marca),
            (t.querySelector(".iwt-hotpoint-record-brand-link").href =
                e.url_marca);
    }
    /* ------------------------------ */

    _recordModalImageLoadedHandler(t) {
        t.currentTarget.parentNode.classList.remove("iwtmdf-loading");
    }
    _recordModalCloseButtonHandler(t) {
        this._resetRecordModal(),
            (this._enableAppUI = !0),
            document.getElementById("iwt-hotpoint-record").close();
        const o = document.querySelector(".iwt-hot-points-container");
        Array.from(
            o.querySelectorAll(".iwt-hot-points-container .iwt-hot-point")
        ).forEach((i) => i.blur());
    }
    _resetRecordModal() {
        const t = document.getElementById("iwt-hotpoint-record");
        t
            .querySelector(".iwt-hotpoint-record-prev")
            .removeEventListener(
                "click",
                this._recordModalNavigationButtonsHandler
            ),
            t
                .querySelector(".iwt-hotpoint-record-close")
                .removeEventListener(
                    "click",
                    this._recordModalCloseButtonHandler
                ),
            t
                .querySelector(".iwt-hotpoint-record-next")
                .removeEventListener(
                    "click",
                    this._recordModalNavigationButtonsHandler
                );
        const i = this._getHotPointNode(this._hotpointPosition);
        i.classList.remove("iwtmdf-selected"), i.blur();
        const r = t.querySelector(".iwt-hotpoint-record-product-image");
        (r.src = ""),
            r.removeEventListener("load", this._recordModalImageLoadedHandler);
        const s = t.querySelector(".iwt-hotpoint-record-brand-logo");
        s.removeEventListener("load", this._recordModalImageLoadedHandler),
            (s.src = "");
    }
    _recordModalNavigationButtonsHandler(t) {
        this._resetRecordModal();
        const e = t.currentTarget.classList.contains("iwt-hotpoint-record-prev")
                ? "prev"
                : "next",
            o = this._getPrevOrNextHotpointId(e);
        (this._hotpointId = o.hotpointId),
            (this._hotpointPosition = o.hotpointPosition),
            o.hotpointNode.classList.add("iwtmdf-selected"),
            this._showRecordModal(this._hotpointId);
    }
    _getPrevOrNextHotpointId(t) {
        const e = Array.from(document.querySelectorAll(".iwt-hot-point")),
            o = t === "prev" ? -1 : 1;
        let n = parseInt(this._hotpointPosition),
            i;
        for (
            ;
            (n += o),
                t === "prev" && n == -1
                    ? (n = e.length - 1)
                    : t === "next" && n == e.length && (n = 0),
                (i = e[n]),
                i.getAttribute("data-iwt-auto-open") !== "0";

        );
        return {
            hotpointNode: i,
            hotpointPosition: n,
            hotpointId: i.getAttribute("data-iwt-id"),
        };
    }
    _notifyErrorToUser() {
        if (
            this._currentData == null ||
            this._currentData.hasOwnProperty("error")
        ) {
            const t = document.getElementById("iwt-message-modal"),
                e = t.querySelector(".iwt-message-modal-content"),
                o = document.querySelector(".iwt-message-modal-button");
            return (
                (e.textContent = this._currentData
                    ? this._currentData.error
                    : "No products found in this shot"),
                (this._modalCloseButtonHandler =
                    this._modalCloseButtonHandler.bind(this)),
                o.addEventListener("click", (n) =>
                    this._modalCloseButtonHandler(t)
                ),
                (this._enableAppUI = !1),
                t.showModal(),
                !0
            );
        }
        return !1;
    }
    _modalCloseButtonHandler(t) {
        t.removeEventListener("click", (e) => this._modalCloseButtonHandler(t)),
            (this._enableAppUI = !0),
            t.close(),
            this._eventEmitter.emit("API_GET_ERROR");
    }
    set _showLoadingIcon(t) {
        t
            ? document.documentElement.classList.add("iwtmdf-shop-loading-data")
            : document.documentElement.classList.remove(
                  "iwtmdf-shop-loading-data"
              );
    }
    set _enableAppUI(t = !0) {
        const e = document.documentElement.classList;
        t ? e.remove("iwtmdf-ui-disabled") : e.add("iwtmdf-ui-disabled");
    }
}
export { u as default };
