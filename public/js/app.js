document.addEventListener("readystatechange", function () {
    if (document.readyState === "complete") {
        proyectos_objetos();

        //
        // Show hide upload button
        //
        Array.prototype.forEach.call(document.querySelectorAll('.show_file_button'), (e) => {
            e.addEventListener('click', (b) => {
                b.target.style.display = 'none';
                b.target.parentElement.querySelector('strong').style.display = 'block';
                b.target.parentElement.querySelector('[type="file"]').style.display = 'block';
                b.preventDefault();
                b.stopPropagation();
            });
        })

        //
        // remove image
        //
        Array.prototype.forEach.call(document.querySelectorAll('.remove_image_button'), (e) => {
            e.addEventListener('click', (b) => {
                let form = b.target.parentElement.parentElement.parentElement.parentElement;
                form.querySelector('[name="field_to_delete"]').value = b.target.parentElement.querySelector('[name="field_name"]').value;
                form.method = 'get';
                form.action = window.location.href;
                form.submit();
            });
        })


        //
        // Submit create
        //
        Array.prototype.forEach.call(document.querySelectorAll('#submit_create'), (e) => {
            e.addEventListener('click', (b) => {
                let capa = document.querySelector('.cobertura');
                if (capa) {
                    capa.style.display = "grid";
                }
            });
        })

        //
        // Sistema de pestañas
        //
        if (document.querySelector('#tabs') && 'si' == document.querySelector('#tabs').value) {
            const tabs = [];
            for (let i = 1; i <= 9; i++) {
                const tab = document.querySelector(`.tab-${i}`);
                if (tab) {
                    tabs.push({ element: tab, id: `tab-${i}` });
                }
            }

            if (tabs.length === 0) return;

            const nav = document.createElement('div');
            nav.classList.add('tab-nav');

            let button2 = document.createElement('button');
            button2.innerText = '<';
            button2.dataset.target = 0;
            button2.addEventListener('click', (e) => {                
                document.querySelector('body').classList.toggle('project-detail-page')
                e.target.classList.toggle('iwi-rotated')
            });
            nav.appendChild(button2);

            tabs.forEach((tab, index) => {
                const h2 = tab.element.querySelector('h2');
                const label = h2 ? h2.innerText.trim() : `Tab ${index + 1}`;

                const button = document.createElement('button');
                button.innerText = label;
                button.dataset.target = tab.id;

                // if (h2) h2.style.display = 'none'; // ocultar el h2 del contenido

                if (index === 0) {
                    button.classList.add('active');
                    tab.element.classList.add('active');
                }

                button.addEventListener('click', (e) => {
                    // Ocultar todas las pestañas
                    tabs.forEach(t => t.element.classList.remove('active'));
                    document.querySelectorAll('.tab-nav button').forEach(btn => btn.classList.remove('active'));

                    // HACK-001: Añade a .iwt-admin la clase "hotpoints-editor-opened" si se abre
                    // la pestaña 4 (data-target="tab-4") y se elimina si se abre cualquier otra pestaña.
                    // CSS: public\css\app\admin.css
                    document.querySelector(".iwt-admin")?.classList.toggle("hotpoints-editor-opened", e.target.dataset.target==="tab-4" );
                    //

                    // Mostrar la activa
                    tab.element.classList.add('active');
                    button.classList.add('active');
                });

                nav.appendChild(button);
                tab.element.classList.add('tab-content');
            });

            tabs[0].element.parentNode.insertBefore(nav, tabs[0].element);
        }



        //
        // Actualización de permisos de usuarios / proyectos
        //

        // Mostramos el popup al hacer clic en el botón "Add users to this project"
        if (document.getElementById("add_user_project")) {
            document.getElementById("add_user_project").addEventListener("click", function () {
                showUserModal();
            });
        }

        // Borrar un usuario de un proyecto
        if (document.getElementById("delete_user_project")) {
            const deleteButtons = document.querySelectorAll('#delete_user_project');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function (event) {
                    const userId = event.target.dataset.userId;
                    const projectId = document.querySelector("#projectId").value;
                    const confirmModal = showDeleteModal(userId, projectId);
                    document.body.appendChild(confirmModal);
                });
            });

        }

        // Cambio de rol
        if (document.querySelector(".user-role-select")) {
            document.querySelectorAll('.user-role-select').forEach(select => {
                select.addEventListener('change', function ( event ) {
                    const userId = event.target.getAttribute('data-user-id');
                    const role = event.target.value;
                    const projectId = document.getElementById('projectId').value;
                    const csrfToken = document.querySelector("input[name='_token']").value;

                    fetch(`/projects/${projectId}/update-role`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            user_id: userId,
                            role: role
                        })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                console.log('Role updated! 🚀');
                            } else {
                                console.error('Update failed 😵', data);
                                alert(data.message || 'Error updating role');
                            }
                        })
                        .catch(err => {
                            console.error('AJAX error 🤯', err);
                            alert('Something went wrong');
                        });
                });
            });
        }

        // Json de Detections
        if (document.querySelector("#detection_objects")) {
            document.querySelector("#detection_objects").addEventListener("change", function (e) {
                lanza_ajax_deteccion( e.target );
            });
            document.querySelector("#distance_frames").addEventListener("change", function (e) {
                lanza_ajax_deteccion( document.querySelector("#detection_objects") );
            });
        }

        if (document.querySelector("#video_detection")) {
            const video = document.querySelector("#video_detection");
            video.onloadedmetadata = function () {
                const duration = video.duration;

                // Esta propiedad es experimental y sólo está en algunos navegadores como Chrome
                const totalFrames = video.webkitDecodedFrameCount || 0;

                if (totalFrames && duration) {
                    const fps = totalFrames / duration;
                    console.log("FPS estimados:", fps);
                } else {
                    console.log("No se pudo estimar FPS automáticamente.");
                }
            };
        }

        // Intercepto el ai-machine-detection
        if (document.querySelector("#ai-machine-detection")) {
            checkbuttonAIStatus();
            ajax_ia();
            const list      = document.getElementById('classesList');
            const inputs    = list ? list.querySelectorAll('.class-item') : [];
            const btnAll    = document.getElementById('btnSelectAll');
            const btnNone   = document.getElementById('btnDeselectAll');
            const btnCancel = document.getElementById('btnCloseClases');
            const countSpan = document.getElementById('selectedCount');

            const updateCount = () => {
                let count = 0;
                inputs.forEach(i => { if (i.checked) count++; });
                if (countSpan) countSpan.textContent = count;
            };

            if (btnAll) {
                btnAll.addEventListener('click', () => {
                    inputs.forEach(i => i.checked = true);
                    updateCount();
                });
            }

            if (btnNone) {
                btnNone.addEventListener('click', () => {
                    inputs.forEach(i => i.checked = false);
                    updateCount();
                });
            }

            if (btnCancel) {
                btnCancel.addEventListener('click', () => {
                    document.querySelector("#ai-classes").style.display = 'none';
                    document.querySelector("#ai-machine-detection").style.display = 'block';
                    document.querySelector("#ia_matcher_window").style.display = 'block';
                });
            }

            inputs.forEach(i => i.addEventListener('change', updateCount));

            // Inicializa contador al cargar (por si hay old() marcada)
            updateCount();
            document.querySelector("#ai-machine-detection").addEventListener('click', function (e) {
                document.querySelector("#ai-classes").style.display = 'block';
                document.querySelector("#ai-machine-detection").style.display = 'none';
                document.querySelector("#ia_matcher_window").style.display = 'none';
            });
        }
    }
})

function frameToTime(frame) {
    let fps = document.querySelector('#video_fps').value;
    // segundos totales como float
    let totalSeconds = frame / fps;

    // horas, minutos, segundos, milisegundos
    let hours = Math.floor(totalSeconds / 3600);
    let minutes = Math.floor((totalSeconds % 3600) / 60);
    let seconds = Math.floor(totalSeconds % 60);
    let millis = Math.floor((totalSeconds - Math.floor(totalSeconds)) * 1000);

    // formateo con ceros a la izquierda
    let hh = String(hours).padStart(2, "0");
    let mm = String(minutes).padStart(2, "0");
    let ss = String(seconds).padStart(2, "0");
    let ms = String(millis).padStart(3, "0");

    return `${hh}:${mm}:${ss}:${ms}`;
}

function lanza_ajax_deteccion(e) {
    const objectClass = e.value;
    const projectId   = document.querySelector("#project_id").value;
    const distance    = document.querySelector("#distance_frames").value;

    const baseUrl = window.location.origin;
    const url     = `${baseUrl}/datision-detections/${encodeURIComponent(projectId)}/${encodeURIComponent(objectClass)}/${encodeURIComponent(distance)}`;

    fetch(url)
        .then(r => {
            if (!r.ok) throw new Error("Error retrieving the data.");
            return r.json();
        })
        .then(data => {
            let container = document.querySelector("#detections_table");
            let clase = document.querySelector('#detection_objects').value;
            clase = clase.replace("-----", "/");

            if (!Array.isArray(data.detections) || data.detections.length === 0) {
                container.innerHTML = "<p>No detections were found for this object.</p>";
                return;
            }

            // ---------- Agrupar por 'group' ----------
            const grupos = new Map();   // {groupId: [ detections… ]}

            const lista = data.lista;
            data = data.detections;

            data.forEach(det => {
                if (!grupos.has(det.group)) grupos.set(det.group, []);
                grupos.get(det.group).push(det);
            });

            // ---------- Construir tabla ----------
            let html = `<table class="iwt-users table table-bordered"><tbody>
                          <tr><th>Selected</th><th>Key time</th><th>X1</th><th>Y1</th><th>X2</th><th>Y2</th></tr>`;

            // Los grupos ya vienen en orden (0,1,2…) porque PHP los generó así
            grupos.forEach((lista, groupId) => {

                // Orden seguro por frame dentro del grupo
                lista.sort((a, b) => a.frame - b.frame);

                const frameInicio = frameToTime( lista[0].frame );
                const frameFin    = frameToTime( lista[lista.length - 1].frame );
                const total = lista.length;
                let style = '';

                if (distance > 0) {
                    // Fila-cabecera del grupo
                    let todoelgrupo = '';
                    lista.forEach(det => {
                        if ('' != todoelgrupo) {
                            todoelgrupo += '|';
                        }
                        todoelgrupo += det.frame;
                    });
                    html += `<tr class="table-secondary fw-bold detected_item_group" data-grupo="${groupId}">
                            <td colspan="6">
                                <input name="detection_id" type="checkbox" value="${todoelgrupo}"> ${clase} #${groupId}
                                (TC ${frameInicio}-${frameFin} · ${total} elements)
                                <span class="detected_group_see_more">(show detections)</span>
                            </td>
                            </tr>`;
                    style = 'style="display:none;"';
                }

                // Filas de las detecciones
                lista.forEach(det => {
                    let time = frameToTime(det.frame);
                    html += `<tr class="detected_item" id="grupo-${groupId}" ${style}>
                                <td class="listado radio-container"><input name="detection_id" type="checkbox" value="${det.id}"></td>
                                <td class="listado frame-value">${time}</td>
                                <td class="listado">${det.x1}</td>
                                <td class="listado">${det.y1}</td>
                                <td class="listado">${det.x2}</td>
                                <td class="listado">${det.y2}</td>
                             </tr>`;
                });
            });

            html += "</tbody></table>";
            container.innerHTML = html;


                        data.forEach(det => {
                if (!grupos.has(det.group)) grupos.set(det.group, []);
                grupos.get(det.group).push(det);
            });

            // ---------- Construir tabla de productos ----------
            container = document.querySelector("#products_table");

            html = `<table class="iwt-users table table-bordered"><tbody>
                          <tr><th></th><th>Prodct Name</th></tr>`;

            // Los grupos ya vienen en orden (0,1,2…) porque PHP los generó así
            lista.forEach((det) => {
                html += `<tr>
                            <td class="listado radio-container"><input name="product_id"type="radio" value="${det.id}"></td>
                            <td class="listado">${det.name}</td>
                        </tr>`;
            });

            html += '</tbody></table><button id="vincular" class="btn btn-primary" disabled style="display:none;">Link</button>';
            if (!Array.isArray(lista) || lista.length === 0) {
                container.innerHTML = "<p>No products were found for this detection.</p>";
            } else {
                container.innerHTML = html;
            }

            // vuelve a enganchar listeners si los necesitas
            if (typeof eventDetectedItem === "function") eventDetectedItem();
        })
        .catch(err => {
            console.error("Error en la petición AJAX:", err);
            document.querySelector("#detections_table")
                    .innerHTML = "<p>Error cargando las detecciones.</p>";
        });
}

/* ---------- función y escuchas ---------- */
function check_selections() {
    // colecciones de checkboxes
    const detections = document.querySelectorAll('input[name="detection_id"]');
    const products   = document.querySelectorAll('input[name="product_id"]');
    const btn        = document.getElementById('vincular');

    // comprueba si al menos uno de cada grupo está marcado
    function updateButton () {
        const someDetection = [...detections].some(ch => ch.checked);
        const someProduct   = [...products].some(ch => ch.checked);

        if (btn) {
            if (someDetection && someProduct) {
                btn.style.display = '';     // visible
                btn.disabled = false;  // clicable
            } else {
                btn.style.display = 'none'; // oculto
                btn.disabled = true;   // no clicable
            }
        }
    }

    if (document.querySelector('#vincular')) {
        document.querySelector('#vincular').addEventListener('click', function () {
            const projectId = document.querySelector("#project_id").value;
            const detection_id = document.querySelector("#datision_detection_id").value;
            const product_id = document.querySelector("#datision_product_id").value;

            const baseUrl = window.location.origin;
            const url = `${baseUrl}/datision-link-detections/${encodeURIComponent(projectId)}/${encodeURIComponent(detection_id)}/${encodeURIComponent(product_id)}`;

            fetch(url)
                .then(r => {
                    if (!r.ok) throw new Error("Error retrieving the data.");
                    return r.json();
                })
                .then(data => {
                    console.log('Vinculados');
                    lanza_ajax_deteccion(document.querySelector('#detection_objects'));
                })
                .catch(err => {
                    console.error("Error en la petición AJAX:", err);
                    document.querySelector("#detections_table")
                        .innerHTML = "<p>Error cargando las detecciones.</p>";
                });
        });
    }
    // escucha cambios en todos los checkboxes
    detections.forEach(ch => ch.addEventListener('change', updateButton));
    products.forEach(ch => ch.addEventListener('change', updateButton));

    // estado inicial
    updateButton();
}

function getParentDetectedItem(obj) {
    let current = obj;

    while (current && !current.classList.contains('detected_item')) {
        current = current.parentElement;
    }

    if (!current) return null; // No encontró nada
    return current;
}

function getDetectedItemValue(obj) {
    if (!obj) return null; // No encontró nada

    current = obj.previousSibling;
//    current = getParentDetectedItem(obj);
    let tmp = current.querySelector('[name="detection_id"');


    console.log('getDetectedItemValue')
    console.log('================================================')
    console.log(current);
    console.log(tmp);
    console.log(getFirstValueFromString(tmp.value));
    console.log('================================================')

    return parseInt(getFirstValueFromString(tmp.value));

//    const firstTd = current.querySelector('.frame-value');
//    if (!firstTd) return null;
//    return parseInt(firstTd.textContent.trim(), 10);
}

function eventDetectedItem() {
    const rows = document.querySelectorAll(".detected_item");
    rows.forEach(row => {
        row.addEventListener("click", (e) => {
            e = e.target;
            let video = document.querySelector('#video_detection');
            let frame = getDetectedItemValue(e);
            video.currentTime = frame / document.querySelector('#video_fps').value;
            let rows = document.querySelectorAll('.iwt-users tr');
            rows.forEach(row => {
                row.classList.remove( 'resaltado' );
            });
            row = getParentDetectedItem(e);
            row.classList.add('resaltado');
            resaltaDetectedItem();
        });
    });

    const group = document.querySelectorAll(".detected_item_group");
    group.forEach(row => {
        row.addEventListener("click", (original) => {
            original = original.target;
            if ('TR' != original.tagName) {
                original = original.parentElement;
            }
            if ('TR' != original.tagName) {
                original = original.parentElement;
            }
            e = original.nextElementSibling;
            let video = document.querySelector( '#video_detection' );
            let frame = getDetectedItemValue(e);
            video.currentTime = frame / document.querySelector('#video_fps').value;
            console.log( 'Video frame: ' + frame )
            console.log( 'Video FPS: ' + document.querySelector('#video_fps').value )
            console.log( 'Video time position: ' + ( frame / document.querySelector('#video_fps').value ) )
            let rows = document.querySelectorAll('.detected_item_group');
            rows.forEach(row => {
                row.classList.remove( 'resaltado' );
            });

            rows = document.querySelectorAll( '.iwt-users tr' );
            rows.forEach(row => {
                row.classList.remove( 'resaltado' );
            });
            row = getParentDetectedItem(e);
            row.classList.add( 'resaltado' );
            resaltaDetectedItem();
            original.classList.add( 'resaltado' );
        });
    });

    //
    // Abrir / cerrar grupo de detecciones
    //
    const seemore = document.querySelectorAll(".detected_group_see_more");
    seemore.forEach(row => {
        row.addEventListener("click", (e) => {
            e.stopPropagation();
            e = e.target;
            let abierto = false;
            if (e.classList.contains("detected_group_see_more_opened")) {
                e.classList.remove("detected_group_see_more_opened");
                e.innerHTML = "(show detections)";
            } else {
                e.classList.add("detected_group_see_more_opened");
                e.innerHTML = "(hide detections)";
                abierto = true;
            }
            e = e.parentElement.parentElement;
            let hijos = document.querySelectorAll('.detected_item');
            hijos = document.querySelectorAll('#grupo-' + e.dataset['grupo']);
            hijos.forEach(hijo => {
                hijo.style.display = 'none';
            });
            if (abierto) {
                hijos.forEach(hijo => {
                    hijo.style.display = '';
                });
            }
        });
    });

    zoomVideo();
    check_selections();
}

function zoomVideo() {
    const video = document.getElementById('video_detection');
    const fpsEl = document.getElementById('video_fps');

    // ---- variables de estado ----
    let pane   = null;   // contenedor
    let clone  = null;   // vídeo clonado
    let onLeft = true;   // ¿pane a la izquierda?
    let scale = 15;     // zoom actual (x10 por defecto)
    let max_scale = 25;
    let min_scale = 5;

    /* 1) sincronizar vídeo con la fila resaltada */
    function syncFrame(){
        const row   = document.querySelector('.detected_item.resaltado');
        if(!row) return;
        const frame = getDetectedItemValue(row);
        const fps   = parseFloat(fpsEl.value || 25);
        video.currentTime = frame / fps;
    }

    /* 2) construir visor */
    function createPane(rect,leftSide){
        pane        = document.createElement('div');
        pane.className = 'zoom-pane';
        pane.style.left = leftSide ? 0 : rect.width/2 + 'px';
        onLeft      = leftSide;

        clone           = video.cloneNode(true);
        clone.muted     = true;
        clone.currentTime = video.currentTime;
//        clone.play();
        applyScale();                     // aplicar escala inicial

        pane.appendChild(clone);
        video.parentElement.appendChild(pane);
    }

    /* 3) quitar visor */
    function removePane(){
        if(pane){ pane.remove(); pane=null; clone=null; }
    }

    /* 4) aplicar escala al vídeo clonado */
    function applyScale(){
        if(clone){
            clone.style.transform = `scale(${scale})`;
        }
    }

    /* 5) listener de rueda: zoom + / - */
    function wheelZoom(e){
        if(!pane) return;
        e.preventDefault();      // evitar scroll de página
        if(e.deltaY < 0){        // rueda arriba → acercar
            scale = Math.min(scale + 1, max_scale);
        } else {                   // rueda abajo → alejar
            scale = Math.max(scale - 1, min_scale);
        }
        applyScale();
    }

    /* ---------- eventos principales ---------- */
    video.addEventListener('mouseenter', e=>{
        syncFrame();
        const rect     = video.getBoundingClientRect();
        const leftSide = e.offsetX <= rect.width/2;
        createPane(rect, leftSide);
    });

    video.addEventListener('mousemove', e=>{
        if(!pane) return;
        const rect        = video.getBoundingClientRect();
        const cursorLeft  = (e.clientX - rect.left) < rect.width/2;

        /* cambiar pane de lado si cruza la mitad */
        if(cursorLeft !== onLeft){
        pane.style.left = cursorLeft ? rect.width/2 + 'px' : 0;
        onLeft = !onLeft;
        }

        /* ajustar punto de ampliación */
        const xPct = (e.clientX - rect.left) / rect.width  * 100;
        const yPct = (e.clientY - rect.top)  / rect.height * 100;
        clone.style.transformOrigin = `${xPct}% ${yPct}%`;
    });

    video.addEventListener('wheel', wheelZoom);
    video.addEventListener('mouseleave', removePane);
}

function resaltaDetectedItem() {
    const video = document.querySelector("#video_detection");
    let resaltadoRow = document.querySelector(".iwt-users tr.resaltado");
    if (!video || !resaltadoRow) return;

    let tds = resaltadoRow.querySelectorAll("td");
    if (tds.length < 6) {
        resaltadoRow = resaltadoRow.nextSibling;
        tds = resaltadoRow.querySelectorAll("td");
    }
    if (tds.length < 6) return;

    const originalWidth = parseInt(document.querySelector("#video_w").value, 10);
    const originalHeight = parseInt(document.querySelector("#video_h").value, 10);

    const x1 = parseInt(tds[2].textContent.trim(), 10);
    const y1 = parseInt(tds[3].textContent.trim(), 10);
    const x2 = parseInt(tds[4].textContent.trim(), 10);
    const y2 = parseInt(tds[5].textContent.trim(), 10);

    const videoRect = video.getBoundingClientRect();
    const scaleX = videoRect.width / originalWidth;
    const scaleY = videoRect.height / originalHeight;

    const rectLeft = x1 * scaleX;
    const rectTop = y1 * scaleY;
    const rectWidth = (x2 - x1) * scaleX;
    const rectHeight = (y2 - y1) * scaleY;

    // Buscar overlay anterior y borrarlo si existe
    const existingOverlay = document.querySelector('#overlay-detected');
    if (existingOverlay) {
        existingOverlay.remove();
    }

    // Crear nuevo overlay
    let overlay = document.createElement("div");
    if (document.querySelector('#overlay-detected')) {
        overlay = document.querySelector('#overlay-detected');
    }
    overlay.id = 'overlay-detected';
    overlay.style.position = "absolute";
    overlay.style.left = rectLeft + 'px';
    overlay.style.top = rectTop + 'px';
    overlay.style.width = rectWidth + 'px';
    overlay.style.height = rectHeight + 'px';
    overlay.style.border = "2px solid red";
    overlay.style.boxSizing = "border-box";
    overlay.style.pointerEvents = "none";
    overlay.style.zIndex = "100";
    overlay.style.background = "red";
    overlay.style.animation = "flash-border 1s ease-in-out forwards";

/*
    console.log('===============================================================');
    console.log('resaltaDetectedItem');
    console.log('===============================================================');
    console.log(overlay.style);
    console.log('overlay.style.left = ' + rectLeft + 'px');
    console.log('overlay.style.top = ' + rectTop + 'px');
    console.log('overlay.style.width = ' + rectWidth + 'px');
    console.log('overlay.style.height = ' + rectHeight + 'px');
    console.log('===============================================================');
*/
    // Creamos contenedor relativo si no lo hay
    if (!document.querySelector('#overlay-detected')) {
        let wrapper = document.createElement("div");
        wrapper.id = 'video_wrapper_overlay';
        wrapper.style.position = "relative";
        video.parentNode.insertBefore(wrapper, video);
        wrapper.appendChild(video);
        wrapper.appendChild(overlay);
    }
}

function showUserModal() {
    // Creamos el modal si no existe
    let modal = document.getElementById("userModal");

    const projectId = document.querySelector("#projectId").value;

    if (!modal) {
        modal = document.createElement("div");
        modal.id = "userModal";
        modal.innerHTML = `
            <div class="modal-overlay"></div>
            <div class="modal-content">
                <h3>Add user to project</h3>
                <select id="user_select_modal"></select>
                <div id="role_container" style="display: none; margin-top: 10px;">
                    <label for="role_select_modal">Role:</label>
                    <select id="role_select_modal">
                        <option value="NO">Editor</option>
                        <option value="shared_owner">Shared owner</option>
                    </select>
                </div>
                <div style="margin-top: 20px;">
                    <button id="confirm_add_user" class="btn btn-primary">Add</button>
                    <button id="cancel_add_user" class="btn btn-secondary">Cancel</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        document.getElementById("cancel_add_user").addEventListener("click", () => {ç
            modal.remove();
        });

        document.getElementById("confirm_add_user").addEventListener("click", () => {
            const userId = document.getElementById("user_select_modal").value;
            const role = document.getElementById("role_select_modal").value;

            if (!userId) return alert("Please select a user");

            fetch('/projects/' + projectId + '/add-user', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector("input[name='_token']").value
                },
                body: JSON.stringify({ user_id: userId, role })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("User added successfully");
                    location.reload();
                } else {
                    alert("Error: " + (data.message || "Something went wrong"));
                }
            })
            .catch(err => alert("Error: " + err.message));
        });

        document.getElementById("user_select_modal").addEventListener("change", (e) => {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const userRole = selectedOption.getAttribute("data-role");
            const roleContainer = document.getElementById("role_container");
            if (userRole === "super") {
                roleContainer.style.display = "block";
            } else {
                roleContainer.style.display = "none";
            }
        });
    }

    // Cargamos los usuarios disponibles
    fetch(`/projects/${projectId}/available-users`)
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById("user_select_modal");
            select.innerHTML = '<option value="">-- Select user --</option>';
            data.forEach(user => {
                const option = document.createElement("option");
                option.value = user.id;
                option.textContent = user.name + " (" + user.role + ")";
                option.setAttribute("data-role", user.role);
                select.appendChild(option);
            });
        });

    modal.style.display = "block";
}

function showDeleteModal(userId, projectId) {
    // Modal HTML
    const modal = document.createElement('div');
    modal.classList.add('custom-modal');
    modal.innerHTML = `
        <div class="custom-modal-backdrop"></div>
        <div class="custom-modal-content">
            <h3>Confirm user removal</h3>
            <p>Are you sure you want to remove this user from the project?</p>
            <div class="custom-modal-buttons">
                <button class="btn btn-danger" id="confirm_delete">Remove</button>
                <button class="btn btn-secondary" id="cancel_delete">Cancel</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    // Eventos de los botones
    modal.querySelector('#cancel_delete').addEventListener('click', () => {
        modal.remove();
    });

    modal.querySelector('#confirm_delete').addEventListener('click', () => {
        fetch(`/projects/${projectId}/remove-user`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector("input[name='_token']").value,
            },
            body: JSON.stringify({ user_id: userId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload(); // ¡A volar!
            } else {
                alert('Something went wrong!');
                modal.remove();
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Error removing user.');
            modal.remove();
        });
    });
}

function ajax_ia() {
    const log = (...a) => console.log("[AI-LAUNCHER]", ...a);
    const btn = document.getElementById("btnLaunchAI");
    const btnData = document.getElementById("ai-machine-detection");
    const progressDiv = document.getElementById("progressAI");

    if (!btn) {
        console.warn("[AI-LAUNCHER] Button not found.");
        return;
    }

    const getProxyUrl = (el) => el.getAttribute("data-save-url") || "/ai/launch";

    const collectClasses = () =>
        Array.from(document.querySelectorAll('input.class-item[name="classes[]"]:checked'))
            .map((el) => el.value);

    const postJSON = async (url, body, { headers = {}, sameOrigin = false, timeout = 60000 } = {}) => {
        const res = await withTimeout(
            timeout,
            fetch(url, {
                method: "POST",
                headers: { "Content-Type": "application/json", Accept: "application/json", ...headers },
                body: JSON.stringify(body),
                credentials: sameOrigin ? "same-origin" : "omit",
            })
        );
        if (!res.ok) {
            const text = await res.text().catch(() => "");
            throw new Error(`HTTP ${res.status} ${res.statusText} - ${text}`);
        }
        const ct = res.headers.get("Content-Type") || "";
        return ct.includes("application/json") ? res.json() : res.text();
    };

    const setBusy = (el, busy) => {
        if (busy) {
            el.dataset.originalText = el.textContent;
            el.disabled = true;
            el.textContent = "Processing…";
        } else {
            el.disabled = false;
            if (el.dataset.originalText) el.textContent = el.dataset.originalText;
            delete el.dataset.originalText;
        }
    };

    // helper para GET JSON con timeout
    // en vez de GET directo a http://13.48.27.24:5018/..., llamamos a /ai/result (HTTPS, same-origin)
    const getJSONViaProxy = async ({ taskId, targetUrl, csrf, timeout = 20000 }) => {
        const res = await withTimeout(
            timeout,
            fetch("/ai/result", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    ...(csrf ? { "X-CSRF-TOKEN": csrf } : {}),
                },
                body: JSON.stringify({ task_id: taskId, target_url: targetUrl }),
                credentials: "same-origin",
            })
        );
        if (!res.ok) {
            const text = await res.text().catch(() => "");
            throw new Error(`HTTP ${res.status} ${res.statusText} - ${text}`);
        }
        return res.json();
    };

    // compone scheme://host:5018/v1/get_result/{taskId}
    const buildResultUrl = (baseUrl, taskId) => {
        try {
            const u = new URL(baseUrl);
            return `${u.protocol}//${u.hostname}:5018/v1/get_result/${encodeURIComponent(taskId)}`;
        } catch {
            // fallback por si baseUrl no es válida para URL()
            const cleaned = String(baseUrl).replace(/\/+$/, "");
            return `${cleaned}:5018/v1/get_result/${encodeURIComponent(taskId)}`;
        }
    };

    btn.addEventListener("click", async (e) => {
        e.preventDefault();
        try {
            setBusy(btn, true);
            btn.textContent = "Starting AI recognition task...";


            const targetUrl = btnData.getAttribute("data-url")?.trim();
            const projectId = Number(btnData.getAttribute("data-id"));
            const videoPath = btnData.getAttribute("data-video")?.trim();
            const threshold = Number(btnData.getAttribute("data-secs"));
            const csrf = btnData.getAttribute("data-csrf")?.trim();
            const proxyUrl = getProxyUrl(btnData);

            if (!targetUrl) throw new Error("No data-url found.");
            if (!projectId) throw new Error("Missing or incorrect data-id.");
            if (!videoPath) throw new Error("No data-video found.");
            if (Number.isNaN(threshold)) throw new Error("Missing or incorrect data-secs.");

            const payloadProxy = {
                target_url: targetUrl,
                id_project: projectId,
                path: videoPath,
                threshold_sec: threshold,
                classes: collectClasses(),
            };
            const headers = csrf ? { "X-CSRF-TOKEN": csrf } : {};

            log("POST Proxy (Laravel) =>", proxyUrl, payloadProxy);
            const resp = await postJSON(proxyUrl, payloadProxy, {
                sameOrigin: true,
                headers,
                timeout: 120000,
            });

            // resp can be string or json
            const data = typeof resp === "string" ? JSON.parse(resp) : resp;
            const state = data?.state;

            if (state === "PROGRESS") {
                if (progressDiv) {
                    progressDiv.textContent = data?.message || "In progress…";
                    progressDiv.style.display = "";
                    document.getElementById('btnCloseClases').style.display = 'none';
                }
            }

            if (state === "SUCCESS") {
                if (progressDiv) {
                    progressDiv.style.display = "none";
                    progressDiv.textContent = "";
                    document.getElementById('btnCloseClases').style.display = 'none';
                }
                btn.textContent = "Start new AI recognition task";
            }

            if (state === "QUEUED") {
                // First-time launch success → consultar progreso inmediato
                if (progressDiv) {
                    progressDiv.textContent = "Queued. In progress…";
                    progressDiv.style.display = "";
                    document.getElementById('btnCloseClases').style.display = 'none';
                }
            }

            let percentText = "";
            try {
                const taskId = data?.task_id;
                if (taskId) {
                    const resultUrl = buildResultUrl(targetUrl, taskId);
                    log("GET result =>", resultUrl);
                    const resultResp = await getJSONViaProxy({
                        taskId: data?.task_id,
                        targetUrl,           // el mismo data-url del botón
                        csrf,                // el data-csrf del botón
                        timeout: 20000
                    });
                    const resultData = typeof resultResp === "string" ? JSON.parse(resultResp) : resultResp;

                    console.Log(resultData);

                    if (resultData?.state === "PROGRESS" && resultData?.status) {
                        percentText = " " + resultData.status;
                        // opcional: también actualiza el progressDiv
                        if (progressDiv) {
                            progressDiv.textContent = `In progress: ${resultData.status}`;
                            progressDiv.style.display = "";
                        }
                    }
                } else {
                    // Unexpected
                    throw new Error("Unexpected response: " + JSON.stringify(data));
                }
            } catch (err2) {
                // si falla la consulta de progreso, seguimos sin % en el alert
                console.warn("[AI-LAUNCHER] Progress check failed:", err2?.message || err2);
            }
//            alert("🚀 Task queued. task_id: " + (data?.task_id || "n/a") + (percentText ? ` (${percentText})` : ""));

            checkbuttonAIStatus();

            return;
        } catch (err) {
            console.error("[AI-LAUNCHER] Error:", err);
            alert("💥 Error:\n" + (err?.message || err));
        } finally {
            setBusy(btn, false);
        }
    });
}

function checkbuttonAIStatus() {
    const btnData = document.querySelector("#ai-machine-detection");
    const targetUrl = btnData.getAttribute("data-url")?.trim();
    const projectId = Number(btnData.getAttribute("data-id"));
    const csrf = btnData.getAttribute("data-csrf")?.trim();
    const btn = document.getElementById("btnLaunchAI");

    document.querySelector("#ai-classes").style.display = 'none';
    btnData.style.display = 'block';
    document.querySelector("#ia_matcher_window").style.display = 'block';

    fetchProjectProgress({ projectId, csrf, targetUrl, timeout: 15000 })
        .then(data => {
            // data.state => "EMPTY" | "PROGRESS" | "SUCCESS" | "UNKNOWN"
            if (data.state === 'PROGRESS' && data.percent) {
                btnData.textContent = `In progress: ${data.percent}`;
                btnData.disabled = true;
            } else if (data.state === 'SUCCESS' || data.state === "EMPTY" ) {
                btnData.textContent = 'New object recognition using AI';
                btnData.disabled = false;
            } else if (data.state === 'QUEUED') {
                btnData.textContent = "Queued. In progress…";
                btnData.disabled = true;
            }
        })
        .catch(err => console.error('Progress error:', err));

}

// --- helper global ---
function withTimeout(ms, promise) {
    const ctrl = new AbortController();
    const t = setTimeout(() => ctrl.abort(), ms);
    return promise.finally(() => clearTimeout(t));
}

async function fetchProjectProgress({ projectId, csrf, targetUrl = null, timeout = 15000 }) {
  const body = { project_id: projectId };
  if (targetUrl) body.target_url = targetUrl;

  const res = await withTimeout(
    timeout,
    fetch('/ai/progress', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {})
      },
      credentials: 'same-origin',
      body: JSON.stringify(body)
    })
  );

  if (!res.ok) {
    const text = await res.text().catch(() => '');
    throw new Error(`HTTP ${res.status} ${res.statusText} - ${text}`);
  }
  return res.json();
}

//
// Gestión de objetos de proyectos
//
function proyectos_objetos() {
    if (document.querySelector("#chage_state")) {

        //
        // Todos los close window
        //
        Array.prototype.forEach.call(document.querySelectorAll('#close_window'), (e) => {
            e.addEventListener('click', (elem) => {
                elem.preventDefault();
                elem.target.parentElement.style.display = 'none';
                elem.target.parentElement.parentElement.classList.remove('wide_td_price');

            });
        });

        //
        // Para los enable - disable
        //
        Array.prototype.forEach.call(document.querySelectorAll('#chage_state'), (e) => {
            e.addEventListener('click', (elem) => {
                elem.preventDefault();
                let id = 'window-state-' + elem.target.dataset.window;
                document.querySelector('#' + id).style.display = 'block';
			    $('#stateModal').show();
            });
        });

        // Aquí se actualiza
        Array.prototype.forEach.call(document.querySelectorAll('#change_state_option'), (e) => {
            e.addEventListener('click', (elem) => {
                elem.preventDefault();
                elem.target.parentElement.style.display = 'none';
                elem.target.parentElement.parentElement.querySelector('a').innerHTML = elem.target.innerHTML;
                elem.target.parentElement.parentElement.parentElement.dataset.updated = '1';
                proyectos_objetos_row();
            });
        });

        //
        // Para los change price
        //
        Array.prototype.forEach.call(document.querySelectorAll('#chage_price'), (e) => {
            e.addEventListener('click', (elem) => {
                elem.preventDefault();
                let id = 'window-price-' + elem.target.dataset.window;
                win = document.querySelector('#' + id);
                win.style.display = 'block';
                win.parentElement.classList.add('wide_td_price');
            });
        });
        Array.prototype.forEach.call(document.querySelectorAll('#change_price_option'), (e) => {
            e.addEventListener('click', (elem) => {
                elem.preventDefault();
                elem.target.parentElement.querySelector('#price_per_second').value = elem.target.dataset.price;
            });
        });
        // Aquí se actualiza
        Array.prototype.forEach.call(document.querySelectorAll('#apply_price'), (e) => {
            e.addEventListener('click', (elem) => {
                elem.preventDefault();
                elem.target.parentElement.style.display = 'none';
                elem.target.parentElement.parentElement.parentElement.dataset.updated = '1';
                elem.target.parentElement.parentElement.classList.remove('wide_td_price');
                let a = elem.target.parentElement.parentElement.querySelector('a');
                a.dataset.precio = a.parentElement.querySelector("#price_per_second").value;
                a.innerHTML = formatearNumeroConComas( a.dataset.precio * a.dataset.time );
                proyectos_objetos_row();
            });
        });

        proyectos_objetos_row();
    }
}

function proyectos_objetos_row() {
    if (document.querySelector(".proyectos_objetos")) {
        //
        // Todos los close window
        //
        let total_price = 0;
        let total_price_enabled = 0;
        let price = 0;
        let time = 0;
        let enabled = false;
        Array.prototype.forEach.call(document.querySelectorAll('.proyectos_objetos'), (e) => {
            enabled = false;
            if ('Enabled' == e.querySelector('.status-link').innerHTML) {
                enabled = true;
                Array.prototype.forEach.call(e.querySelectorAll('td'), (e2) => {
                    e2.classList.remove("proyecto_objetos_cell_disabled");
                });
            } else {
                Array.prototype.forEach.call(e.querySelectorAll('td'), (e2) => {
                    e2.classList.add("proyecto_objetos_cell_disabled");
                });
            }
            price = e.querySelector('.price-link').dataset.precio;
            if (typeof price === 'undefined' ) {
                price = 0;
            }
            time = e.querySelector('.price-link').dataset.time;
            if (typeof time === 'undefined') {
                time = 0;
            }
            total_price += (price * time);
            if (enabled) {
                total_price_enabled += (price * time);
            }
        });
        document.querySelector('#total_price').innerHTML = formatearNumeroConComas(total_price);
        document.querySelector('#total_price_enabled').innerHTML = formatearNumeroConComas(total_price_enabled);
    }
}

function formatearNumeroConComas(numero) {
    // Asegurarse de que la entrada sea un número y sea un entero
    if (typeof numero !== 'number' || !Number.isInteger(numero)) {
        console.error("La entrada debe ser un número entero.");
        return String(numero); // Devuelve la entrada original convertida a string o un mensaje de error.
    }

    // Convertir el número a cadena para poder manipularlo
    let numeroStr = String(numero);
    let partes = [];
    let contador = 0;

    // Recorrer el string del número de derecha a izquierda
    for (let i = numeroStr.length - 1; i >= 0; i--) {
        partes.unshift(numeroStr[i]); // Añadir el dígito al principio de 'partes'
        contador++;

        // Si hemos contado 3 dígitos y no es el inicio del número, añadir una coma
        if (contador % 3 === 0 && i !== 0) {
        partes.unshift(',');
        }
    }

    // Unir todas las partes para formar la cadena final
    return partes.join('');
}

/**
 * Extrae el primer valor de un string que puede contener valores separados por '|' o un único valor.
 *
 * @param {string} inputString El string de entrada (ej: "19527|19528" o "53452").
 * @returns {string|null} El primer valor como string, o null si el inputString es nulo o vacío.
 */
function getFirstValueFromString(inputString) {
    if (inputString === null || inputString === undefined || inputString.trim() === '') {
        return null; // O puedes devolver un string vacío '' si prefieres
    }

    // Divide el string por el carácter '|'
    const values = inputString.split('|');

    // El primer elemento del array 'values' será el primer valor.
    // Si el string original no contenía '|', split() devolverá un array con un solo elemento (el string original).
    return values[0];
}
