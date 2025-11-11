class ControlGeneral {
    constructor() {
        Producto.inicia(this);                  // No hay ningún producto activo.

        this.cursorArrastrado = false;      // Flag que indica que el cursor de la barra de tiempo principal está siendo arrastrado.
        this.currentTimeBloqueado = false;  // Flag que indica que se está modificando a mano el tiempo de reproducción del video.
                                            // y por tanto no se puede modificar automáticamente.
        this.lastStateVideoPaused = true;   // Flag que indica el último estado del video (true: pausado)
        this.total_horas = 0;               // Duración total del video (horas)
        this.total_minutos = 0;             // Duración total del video (minutos)
        this.total_segundos = 0;            // Duración total del video (segundos)

        // Guarda punteros a los objetos del DOM que se van a autilizar para no estar buscándolos constantemente.
        // Todos ellos deben existir previamente en el HTML.

        this.video = document.getElementById("video-display");
        this.baseControles = document.getElementById("base-controles");
        this.barraTiempoGeneral = document.getElementById("barra-tiempo-general");
        this.cursorTiempoGeneral = document.getElementById("cursor-tiempo-general");
        this.botonStopPlay = document.getElementById("boton-stop-play");
        this.botonAdelante = document.getElementById("boton-adelante");
        this.botonAtras = document.getElementById("boton-atras");
        this.botonAdelanteRapido = document.getElementById("boton-adelante-rapido");
        this.botonAtrasRapido = document.getElementById("boton-atras-rapido");
        this.horasActual = document.querySelector(".horas.actual");
        this.minutosActual = document.querySelector(".minutos.actual");
        this.segundosActual = document.querySelector(".segundos.actual");

        // Marcador que aparece sobre el video.

        this.marcadorEnVideo = document.getElementById("item-main");
        this.marcadorTarget = document.getElementById("item-target");
        this.marcadorBotonera = document.getElementById("item-botonera");

        this.marcadorPast = document.getElementById("item-past");
        this.marcadorNext = document.getElementById("item-next");
        this.marcadorDel = document.getElementById("item-del");
        this.marcadorPast.addEventListener("click", this.procesaBotonClick.bind(this));
        this.marcadorNext.addEventListener("click", this.procesaBotonClick.bind(this));
        this.marcadorDel.addEventListener("click", this.procesaBotonClick.bind(this));

        // Crea la ventana para cargar y salvar ficheros.

        this.ventanaLoadSave = new VentanaLoadSave();
//        this.botonCargar = document.getElementById("boton-cargar");
        this.botonSalvar = document.getElementById("boton-salvar");

        // AÑADE MANEJADORES DE EVENTOS.

//        this.botonCargar.addEventListener("click", e=> this.ventanaLoadSave.activa());
//        this.botonSalvar.addEventListener("click", e => this.ventanaLoadSave.activa(true));
        this.botonSalvar.addEventListener("click", e => {
            const datosASalvar = Producto.codificaProductos();
            document.querySelector('#capa-save').style.display = "block";
            let comando = {
                action: "save",
                id: document.querySelector('#version-id').value,
                data: datosASalvar
            };
            consultaBase(comando,
                resultado => {                  // Resultado correcto.
                    alert("DONE: Data has been saved successfully");
                    document.querySelector('#capa-save').style.display = "none";


            },
            resultado => {                  // Resultado erróneo.
                alert("DONE: Data has been saved successfully");
                document.querySelector('#capa-save').style.display = "none";
                //                alert("ERROR: Failed to save data correctly");
            });
        });

        // Los botones de retroceso y avance lento y rápido y play/stop efectuaran distintas acciones según el momento en
        // el que sean pulsados (concretamente si hay algún producto activo o no).

        this.botonStopPlay.addEventListener("click", this.procesaBotonClick.bind(this));
        this.botonAdelante.addEventListener("click", this.procesaBotonClick.bind(this));
        this.botonAtras.addEventListener("click", this.procesaBotonClick.bind(this));
        this.botonAdelanteRapido.addEventListener("click", this.procesaBotonClick.bind(this));
        this.botonAtrasRapido.addEventListener("click", this.procesaBotonClick.bind(this));

        // El video maneja dos eventos:
        // El primero se ejecuta cuando el video está listo para reproducirse y calcula y guarda el tiempo total del mismo.
        // El segundo se ejecuta cuando el video finaliza y lo pone en pausa.

        this.video.addEventListener("canplay", e=> {
            let tmp = parseInt(this.video.duration);
            this.total_horas = parseInt(tmp / 3600);
            this.total_minutos = parseInt((tmp - this.total_horas * 60) / 60);
            this.total_segundos = tmp % 60;
            document.querySelector(".horas.total").value = (this.total_horas < 10) ? "0" + this.total_horas : this.total_horas;
            document.querySelector(".minutos.total").value = (this.total_minutos < 10) ? "0" + this.total_minutos : this.total_minutos;
            document.querySelector(".segundos.total").value = (this.total_segundos < 10) ? "0" + this.total_segundos : this.total_segundos;
        });
        this.video.addEventListener("ended", e=> {
            this.video.pause();
        });

        // El manejador de evento añadido a window se ejecuta al ser modificado el tamaño del área de cliente del navegador
        // y llama al método resize por si es necesario ajustar algo.

        window.addEventListener("resize", this.resize);

        // La mayor parte de los eventos los recibe directamente el objeto document y ejcuta una u otra acción según
        // el objeto target del evento y el estado de la aplicación en el momento de su pulsación (por eso es muy importante
        // que los elementos presentes en el DOM que deban procesar algún evento estén claramente diferenciados mediante su class
        // o su id).

        document.addEventListener("mousedown", e=> {        // Evento al pulsar el botón izquierdo del ratón
            // Comprueba la pulsación sobre el Puntero que aparece sobre la posición del producto activo en el video
            if(Producto.procesaEventoMarcadorProductoSobreVideo(e)) return;

            if(this.video.duration && (e.target == this.barraTiempoGeneral || e.target == this.cursorTiempoGeneral)) {
                this.cursorArrastrado = true;
                let offX = e.clientX - this.barraTiempoGeneral.offsetLeft;
                if(offX < 0) offX = 0;
                if(offX > this.barraTiempoGeneral.clientWidth) offX = this.barraTiempoGeneral.clientWidth;
                this.video.currentTime = offX * this.video.duration / this.barraTiempoGeneral.clientWidth;
                return;
            }
            // Comprueba si el elemento forma parte de un producto y lo procesa en caso de que así sea.
            Producto.verificaYProcesaPulsacionEnProducto(e);
            // Comprueba si el elemento forma parte de la ventana load save.
            this.ventanaLoadSave.verificaYProcesaPulsacionEnVentanaLoadSave(e);
        });
        document.addEventListener("mousemove", e=> {        // Evento al mover el ratón
            // Comprueba la pulsación sobre el Puntero que aparece sobre la posición del producto activo en el video
            if(Producto.procesaEventoMarcadorProductoSobreVideo(e)) return;

            if(this.cursorArrastrado) {
                let offX = e.clientX - this.barraTiempoGeneral.offsetLeft;
                if(offX < 0) offX = 0;
                if(offX > this.barraTiempoGeneral.clientWidth) offX = this.barraTiempoGeneral.clientWidth;
                this.video.currentTime = offX * this.video.duration / this.barraTiempoGeneral.clientWidth;
                return;
            }
            // Comprueba si el elemento forma parte de un producto y lo procesa en caso de que así sea.
            Producto.verificaYProcesaPulsacionEnProducto(e);

        });
        document.addEventListener("mouseup", e=> {          // Evento al soltar el botón del ratón
            // Comprueba la pulsación sobre el Puntero que aparece sobre la posición del producto activo en el video
            if(Producto.procesaEventoMarcadorProductoSobreVideo(e)) return;

            if(this.cursorArrastrado) this.cursorArrastrado = false;
            else {
                // Comprueba si el elemento forma parte de un producto y lo procesa en caso de que así sea.
                Producto.verificaYProcesaPulsacionEnProducto(e);
            }
        });
        document.addEventListener("click", e=> {
            // Comprueba si el elemento forma parte de un producto y lo procesa en caso de que así sea.
            Producto.verificaYProcesaPulsacionEnProducto(e);
            this.ventanaLoadSave.verificaYProcesaPulsacionEnVentanaLoadSave(e);
        });

        // Evento al modificar, añadir o borrar un carácter de un input.
        document.addEventListener("input", this.procesaCambioValorInput.bind(this));

        // Evento si ha cambiado el valor input (se lanza si el valor ha cambiado al perder el foco el input o pulsar la tecla
        // de retorno estando en el mismo).
        document.addEventListener("change", this.procesaCambioValorInput.bind(this));

        // Evento al pulsar el '+' para crear otro producto.
        document.getElementById("otro-producto").addEventListener("click", e => {
            new Producto();
        });

        this.resize();
        setInterval(this.process.bind(this), 20);
    }


    // Respuesta a un evento de click en los botones de retroceso y avance lento y rápido y play/stop.
    // Estos botones fectuaran distintas acciones según el momento en el que sean pulsados (concretamente si hay algún
    // producto activo o no).

    procesaBotonClick(e) {
        if(!this.video.duration) return;
        let hayQuePausar = true;
        let tiempoActual = this.video.currentTime;
        let duracionTotal = this.video.duration;
        let boton = e.currentTarget;
        switch(boton.id) {
        case "item-past":
            Producto.procesaBotonMarcadorSobreVideo(0, tiempoActual);
            break;
        case "item-del":
            Producto.procesaBotonMarcadorSobreVideo(1, tiempoActual);
            break;
        case "item-next":
            Producto.procesaBotonMarcadorSobreVideo(2, tiempoActual);
            break;
        case "boton-stop-play":
            hayQuePausar = !this.video.paused;
            break;
        case "boton-atras":
            tiempoActual -= 1.0;
            if(tiempoActual < 0.0) tiempoActual = 0.0;
            this.video.currentTime = tiempoActual;
            break;
        case "boton-adelante":
            tiempoActual += 1.0;
            if(tiempoActual > duracionTotal) tiempoActual = duracionTotal;
            this.video.currentTime = tiempoActual;
            break;
        case "boton-atras-rapido":
            tiempoActual -= 10.0;
            if(tiempoActual < 0.0) tiempoActual = 0.0;
            this.video.currentTime = tiempoActual;
            break;
        case "boton-adelante-rapido":
            tiempoActual += 10.0;
            if(tiempoActual > duracionTotal) tiempoActual = duracionTotal;
            this.video.currentTime = tiempoActual;
            break;
        default: return;
        }
        this.currentTimeBloqueado = false;
        if(hayQuePausar) this.video.pause();
        else this.video.play();
    }

    // Se ha modificado el valor de un input

    procesaCambioValorInput(e) {
        if(!this.video.duration) return;
        let valor = e.target.value;
        let nuevoValor = "";
        let sonHoras = (e.target.matches(".horas")) ? true : false;
        let sonMinutos = (e.target.matches(".minutos")) ? true : false;
        let sonSegundos = (e.target.matches(".segundos")) ? true : false;

        if(!(sonHoras || sonMinutos || sonSegundos)) return;
        if(e.type == "input") {
            this.video.pause();
            this.currentTimeBloqueado = true;
            for(let cnt = 0; cnt < valor.length; cnt++) {
                if(valor.charAt(cnt) >= "0" && valor.charAt(cnt) <= "9") nuevoValor += valor.charAt(cnt);
            }
            if((sonMinutos || sonSegundos) && parseInt(nuevoValor) > 59) nuevoValor = nuevoValor.charAt(0);
            e.target.value = nuevoValor;
        }
        else if(e.type == "change") {
            while(e.target.value.length < 2) e.target.value = "0" + e.target.value;
            if(e.target == this.horasActual || e.target == this.minutosActual || e.target == this.segundosActual) {
                let nuevoTimer = this.getTiempoEnSegundos(this.horasActual.value, this.minutosActual.value, this.segundosActual.value);
                if(nuevoTimer > this.video.duration) nuevoTimer = this.video.duration - 0.5;
                this.video.currentTime = nuevoTimer;
            }
            else Producto.verificaYProcesaPulsacionEnProducto(e);
        }
    }


    // Procesos de control a ejecutar cada 20 milisegundos. Realiza las siguientes acciones:
    // -Coloca el indicador de posición en su lugar sobre la barra de tiempo general
    // -Posiciona el marcador sobre el video si es visible.
    // -Cambia la imagen del botón play/pause según el contenido de this.video.paused (utiliza la propiedad interna
    //  lastStateVideoPaused para no estar cambiando la imagen cada vez si no ha habido cambio de estado).
    // -Actualiza el contador de hora, minuto y segundo actual del video en el panel principal si la propiedad interna
    //  currentTimeBloqueado no está a true (si lo está es porque se está modificando dicho contado manualmente y no hay que
    //  interferir con el usuario).

    process() {
        if(!this.video.duration) return;
        this.cursorTiempoGeneral.style.left =
            this.getPosicionBarraSegunTiempo(this.video.currentTime, this.barraTiempoGeneral.clientWidth, this.video.duration) + "px";
        Producto.actualizaPosicionCursorTiempoGeneral();
        Producto.posicionaMarcadorSobreVideo();

        if(this.video.paused != this.lastStateVideoPaused) {
            this.lastStateVideoPaused = this.video.paused;
            let imagen = document.querySelector("#boton-stop-play img");
            if(imagen) imagen.src = (this.video.paused) ? document.querySelector( '#play-ico-hpe' ).value : document.querySelector( '#stop-ico-hpe' ).value;
        }

        if(!this.currentTimeBloqueado) {
            let {horas, minutos, segundos} = this.getTiempoEnHorasMinutosYSegundos(this.video.currentTime);
            this.horasActual.value = (horas < 10) ? "0" + horas : horas;
            this.minutosActual.value = (minutos < 10) ? "0" + minutos : minutos;
            this.segundosActual.value = (segundos < 10) ? "0" + segundos : segundos;
        }
    }


    // Método a llamar cuando se modifica el tamaño del área de cliente del navegador.

    resize() {
        // document.getElementById("caja-principal").style.height = (innerHeight < 600) ? "600px" : innerHeight + "px";
    }


    // AUXILIARES

    getTiempoSegunPosicionEnBarra(posicionBarra, largoBarra, duracionVideo) {
        return posicionBarra * duracionVideo / largoBarra;
    }

    getPosicionBarraSegunTiempo(tiempoVideo, largoBarra, duracionVideo) {
        return tiempoVideo * largoBarra / duracionVideo;
    }

    getTiempoEnHorasMinutosYSegundos(tiempoEnSegundos) {
        let horas = parseInt(tiempoEnSegundos / 3600);
        let minutos = parseInt((tiempoEnSegundos - horas * 60) / 60);
        let segundos = parseInt(tiempoEnSegundos) % 60;
        return {horas, minutos, segundos};
    }

    getTiempoEnSegundos(horas, minutos, segundos = 0) {
        return parseInt(horas) * 3600 + parseInt(minutos) * 60 + parseInt(segundos);
    }

}


/////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////


class Producto {
    constructor() {
        this.identificador = 0;
        this.producto = 0;
        this.nombre = "";
        this.segmentos = [];            // Segmentos de tiempo de aparición.
                                        // Cada segmento es un objeto con las siguientes propiedades:
                                        // target: Array de posiciones del puntero que aparece sobre el objeto en el video,
                                        //          con las siguientes propiedades:
                                        //          time: Momento en el que aparece en segundos.
                                        //          pcx: Posición horizontal respecto al origen del video en porcentaje.
                                        //          pcy: Posición verctical respecto al origen del video en porcentaje.
                                        // inicio: Tiempo inicial en segundos.
                                        // fin: Tiempo final en segundos.
                                        // dom: Puntero al objeto en el DOM que lo representa.

        Producto.productos.push(this);
        this.identificador = Producto.productos.length - 1;

        const contenedor = document.getElementById("contenedor-productos");
        const plantilla = document.getElementById("plantilla-producto").content;
        plantilla.querySelector('.producto').dataset.elemento = this.identificador;
        let clone = document.importNode(plantilla, true);
//        console.log(clone);
        const nuevoProducto = document.createElement("div");
        nuevoProducto.className="base-producto";
        nuevoProducto.appendChild(clone);
        contenedor.insertAdjacentElement("afterbegin", nuevoProducto);
        this.elementoBaseEnDOM = nuevoProducto;
        this.barraTiempo = nuevoProducto.querySelector(".barra-timer-producto");
        this.baseSegmentos = nuevoProducto.querySelector(".base-segmentos-tiempo");
        this.cursorInicio = nuevoProducto.querySelector(".cursor-inicio");
        this.cursorInicio.style.visibility = "hidden";
        this.cursorFin = nuevoProducto.querySelector(".cursor-final");
        this.cursorFin.style.visibility = "hidden";
        this.cursorTiempoGeneral = nuevoProducto.querySelector(".cursor-tiempo-general");

        this.horasInicio = nuevoProducto.querySelector(".horas.inicio");
        this.minutosInicio = nuevoProducto.querySelector(".minutos.inicio");
        this.segundosInicio = nuevoProducto.querySelector(".segundos.inicio");
        this.horasFinal = nuevoProducto.querySelector(".horas.final");
        this.minutosFinal = nuevoProducto.querySelector(".minutos.final");
        this.segundosFinal = nuevoProducto.querySelector(".segundos.final");

        this.activaDesactivaControlesSegmento();
        this.controlesSegmentoActivos = false;
        Producto.segmentoActivo = 0;
        Producto.productoActivo = null;
        Producto.cursorArrastrado = 0;

        contenedor.scrollTop = 0;

        prepareEvents(contenedor);
    }

    // Actualiza un segmento o genera uno nuevo sobre la barra de tiempo basándose en los tiempos indicados (en segundos).
    // (si no se indica el tiempo final se utilizará el de inicio para ambos tiempos).
    // comando:
    // 1- Genera
    // 2- Actualiza (actualiza el segmento indicado en Producto.segmento activo. tiempoInicio o tiempoFinal nulos son ignorados)
    // 3- Borra
    // 4- Posiciona los cursores.
    // Los comandos de actualizar y borrar se ejecutarán sobre el segmento indicado en Producto.segmentoActivo.
    // El comando de posicionar cursores se lee en forma de bits.
    // Devuelve un índice al segmento generado o actualizado (entre 1 y n) o 0 si no se ha podido realizar la acción.

    procesaSegmento(comando = 0, tiempoInicio = 0, tiempoFinal = 0, target = null) {
        let idSegmento = 0;
        let hayQuePosicionarCursores = false;
        let xInicial, xFinal;
        if(tiempoFinal === 0) tiempoFinal = tiempoInicio;
        switch(comando & 3) {
        case 1:                         // Hay que generar un segmento.
            if(this.getSegmentoPorTiempo(tiempoInicio) || this.getSegmentoPorTiempo(tiempoFinal)) return 0;    // Ya hay un segmento en este lugar.
            for(idSegmento = 0; idSegmento < this.segmentos.length; idSegmento++) if(tiempoInicio < this.segmentos[idSegmento].inicio) break;
            this.segmentos.splice(idSegmento, 0, { inicio: tiempoInicio, fin: tiempoFinal, dom: null });
            xInicial = Producto.controlGeneral.getPosicionBarraSegunTiempo(tiempoInicio, this.baseSegmentos.clientWidth,
                Producto.controlGeneral.video.duration);
            xFinal = Producto.controlGeneral.getPosicionBarraSegunTiempo(tiempoFinal, this.baseSegmentos.clientWidth,
                Producto.controlGeneral.video.duration);
            const segmento = document.createElement("div");
            segmento.setAttribute("class", "segmento-tiempo");
            segmento.style.left = xInicial + "px";
            segmento.style.width = (xFinal - xInicial) + "px";
            segmento.style.backgroundColor = "#FF7008";
            if(!idSegmento) this.baseSegmentos.insertAdjacentElement("afterbegin", segmento);
            else this.segmentos[idSegmento - 1].dom.insertAdjacentElement("afterend", segmento);
            this.segmentos[idSegmento].target = [];
            if(target) this.segmentos[idSegmento].target = [...target];
            this.segmentos[idSegmento++].dom = segmento;
            if(comando & 4) hayQuePosicionarCursores = true;
            break;
        case 2:                         // Hay que actualizar un segmento.
            idSegmento = Producto.segmentoActivo - 1;
            if(idSegmento < 0 || idSegmento >= this.segmentos.length || !this.segmentos[idSegmento].dom) return 0;
            if(tiempoInicio !== null) {         // Se ha modificado la posición inicial.
                if(idSegmento) {        // Hay un segmento anterior
                    if(tiempoInicio < this.segmentos[idSegmento - 1].fin) tiempoInicio = this.segmentos[idSegmento - 1].fin;
                }
                if(tiempoInicio < 0) tiempoInicio = 0;
                else if(tiempoInicio > this.segmentos[idSegmento].fin) tiempoInicio = this.segmentos[idSegmento].fin;
                Producto.controlGeneral.video.currentTime = tiempoInicio;
            }
            else tiempoInicio = this.segmentos[idSegmento].inicio;
            if(tiempoFinal !== null) {          // Se ha modificado la posición final.
                if(idSegmento < (this.segmentos.length - 1)) {        // Hay un segmento posterior
                    if(tiempoFinal > this.segmentos[idSegmento + 1].inicio) tiempoFinal = this.segmentos[idSegmento + 1].inicio;
                }
                if(tiempoFinal > Producto.controlGeneral.video.duration) tiempoFinal = Producto.controlGeneral.video.duration;
                else if(tiempoFinal < this.segmentos[idSegmento].inicio) tiempoFinal = this.segmentos[idSegmento].inicio;
                Producto.controlGeneral.video.currentTime = tiempoFinal;
            }
            else tiempoFinal = this.segmentos[idSegmento].fin;
            this.segmentos[idSegmento].inicio = tiempoInicio;
            this.segmentos[idSegmento].fin = tiempoFinal;
            xInicial = Producto.controlGeneral.getPosicionBarraSegunTiempo(tiempoInicio, this.baseSegmentos.clientWidth,
                Producto.controlGeneral.video.duration);
            xFinal = Producto.controlGeneral.getPosicionBarraSegunTiempo(tiempoFinal, this.baseSegmentos.clientWidth,
                Producto.controlGeneral.video.duration);
            this.segmentos[idSegmento].dom.style.left = xInicial + "px";
            this.segmentos[idSegmento].dom.style.width = (xFinal - xInicial) + "px";
            this.borraTargetsSobrantes(idSegmento);
            idSegmento++;
            if(comando & 4) hayQuePosicionarCursores = true;
            break;
        case 3:                         // Hay que borrar un segmento.
            break;
        default:                        // Sólo hay que posicionar los cursores. Lo hace sobre el segmento activo.
            if((comando & 4) && Producto.segmentoActivo && this.segmentos.length >= Producto.segmentoActivo) {
                xInicial = Producto.controlGeneral.getPosicionBarraSegunTiempo(this.segmentos[Producto.segmentoActivo - 1].inicio,
                    this.baseSegmentos.clientWidth, Producto.controlGeneral.video.duration);
                xFinal = Producto.controlGeneral.getPosicionBarraSegunTiempo(this.segmentos[Producto.segmentoActivo - 1].fin,
                    this.baseSegmentos.clientWidth, Producto.controlGeneral.video.duration);
                idSegmento = Producto.segmentoActivo;
                hayQuePosicionarCursores = true;
            }
        }
        if(hayQuePosicionarCursores) {           // Hay que posicionar los cursores.
            this.activaDesactivaControlesSegmento(true, this);
            this.cursorInicio.style.left = (xInicial - 8) + "px";
            this.cursorFin.style.left = (xFinal - 8) + "px";

            if(idSegmento > 0) {
                let {horas, minutos, segundos} = Producto.controlGeneral.getTiempoEnHorasMinutosYSegundos(
                    this.segmentos[idSegmento - 1].inicio);
                if(horas < 10) horas = "0" + horas;
                if(minutos < 10) minutos = "0" + minutos;
                if(segundos < 10) segundos = "0" + segundos;
                this.horasInicio.value = horas;
                this.minutosInicio.value = minutos;
                this.segundosInicio.value = segundos;
                ({horas, minutos, segundos} = Producto.controlGeneral.getTiempoEnHorasMinutosYSegundos(
                    this.segmentos[idSegmento - 1].fin));
                if(horas < 10) horas = "0" + horas;
                if(minutos < 10) minutos = "0" + minutos;
                if(segundos < 10) segundos = "0" + segundos;
                this.horasFinal.value = horas;
                this.minutosFinal.value = minutos;
                this.segundosFinal.value = segundos;
            }
        }

        return idSegmento;
    }


    // Si se hace más pequeño un segmento es posible que algunos de los marcadores de objeto que aparecen sobre el video para
    // este segmento ahora queden fuera del mismo. Este método comprueba ese caso y borra los marcadores que queden fuera del
    // segmento indicado.

    borraTargetsSobrantes(idSegmento) {
        let cnt;
        let inicio = parseInt(this.segmentos[idSegmento].inicio * 10);
        let fin = parseInt(this.segmentos[idSegmento].fin * 10);

        for(cnt = this.segmentos[idSegmento].target.length - 1; cnt >= 0; cnt--) {
            let time = parseInt(this.segmentos[idSegmento].target[cnt].time * 10);
            if(time < inicio || time > fin) {
                this.segmentos[idSegmento].target.splice(cnt, 1);       // Este target queda fuera del segmento.
            }
        }
    }

    // Activa o desactiva los controles de segmento de los productos (cursores de inicio y final, controles y botones de tiempo).
    // Si activa es true se activarán únicamente los controles del producto seleccionado.
    // Si activa es false se desactivarán los controles de todos los productos excepto los del seleccionado, si es que existe.

    activaDesactivaControlesSegmento(activa = false, seleccionado = null) {
        Producto.productos.forEach(el => {
            if(((activa && el == seleccionado) || (!activa && el != seleccionado)) && activa != el.controlesSegmentoActivos) {
                el.horasInicio.disabled = el.horasInicio.readOnly = !activa;
                el.minutosInicio.disabled = el.minutosInicio.readOnly = !activa;
                el.segundosInicio.disabled = el.segundosInicio.readOnly = !activa;
                el.horasFinal.disabled = el.horasFinal.readOnly = !activa;
                el.minutosFinal.disabled = el.minutosFinal.readOnly = !activa;
                el.segundosFinal.disabled = el.segundosFinal.readOnly = !activa;
                if(!activa) el.horasInicio.value = el.minutosInicio.value = el.segundosInicio.value = "";
                if(!activa) el.horasFinal.value = el.minutosFinal.value = el.segundosFinal.value = "";
                el.cursorInicio.style.visibility = (activa) ? "visible" : "hidden";
                el.cursorFin.style.visibility = (activa) ? "visible" : "hidden";
                let botonaux = el.elementoBaseEnDOM.querySelectorAll(".boton-inicio, .boton-final, .boton-control");
                if(activa) botonaux.forEach(bt => bt.classList.add("boton-activo"));
                else botonaux.forEach(bt => bt.classList.remove("boton-activo"));
                el.controlesSegmentoActivos = activa;
            }
        });
    }


    // Devuelve un índice al segmento al que se refiere el tiempo indicado, si es que existe, o 0 si no se ha encontrado ninguno.

    getSegmentoPorTiempo(tiempo) {
        for(let id = 0; id < this.segmentos.length; id++) {
            if(tiempo >= this.segmentos[id].inicio && tiempo <= this.segmentos[id].fin) return id + 1;
        }
        return 0;
    }


    // Devuelve un índice al segmento que se encuentra en el offset indicado, si es que existe, o 0 si no se ha encontrado ninguno.

    getSegmentoPorPosicion(offX) {
        let posible = 0;
        offX = parseInt(offX);
        for(let id = 0; id < this.segmentos.length; id++) {
            let x = parseInt(this.segmentos[id].dom.style.left);
            let w = parseInt(this.segmentos[id].dom.style.width);
            if(offX >= x && offX <= (x + w)) return id + 1;
            if(!posible && w < 2 && offX >= (x - 1) && offX <= (x + 3)) posible = id + 1;
        }
        return posible;
    }

    // Procesa los eventos de ratón y de botón que se producen sobre este producto en cuestión.

    procesaEvento(evento) {
        let currentTime = -1;
        let offX = 0;

        // Si se está arrastrando uno de los cursores, o el botón izquierdo del ratón se pulsa sobre uno de ellos o sobre la barra
        // de tiempo, se calcula el tiempo de video al que equivale la posición de la barra sobre la que se ha pulsado.

        if(this.controlesSegmentoActivos && Producto.segmentoActivo && Producto.segmentoActivo <= this.segmentos.length) {
            if(evento.type == "change") {                           // Se ha cambiado el valor de un timer manualmente.
                switch(evento.target) {
                case this.horasInicio:
                case this.minutosInicio:
                case this.segundosInicio:
                    this.procesaSegmento(6, Producto.controlGeneral.getTiempoEnSegundos(this.horasInicio.value,
                        this.minutosInicio.value, this.segundosInicio.value), null);
                    break;
                case this.horasFinal:
                case this.minutosFinal:
                case this.segundosFinal:
                    this.procesaSegmento(6, null, Producto.controlGeneral.getTiempoEnSegundos(this.horasFinal.value,
                        this.minutosFinal.value, this.segundosFinal.value));
                    break;
                }
                return;
            }
            else if(evento.type == "click") {                       // Se ha hecho "click" sobre algún objeto.
                if(evento.target.closest(".boton-inicio.atras-rapido"))
                    this.procesaSegmento(6, this.segmentos[Producto.segmentoActivo - 1].inicio - 10.0, null);
                else if(evento.target.closest(".boton-inicio.atras"))
                    this.procesaSegmento(6, this.segmentos[Producto.segmentoActivo - 1].inicio - 1.0, null);
                else if(evento.target.closest(".boton-inicio.adelante"))
                    this.procesaSegmento(6, this.segmentos[Producto.segmentoActivo - 1].inicio + 1.0, null);
                else if(evento.target.closest(".boton-inicio.adelante-rapido"))
                    this.procesaSegmento(6, this.segmentos[Producto.segmentoActivo - 1].inicio + 10.0, null);
                else if(evento.target.closest(".boton-final.atras-rapido"))
                    this.procesaSegmento(6, null, this.segmentos[Producto.segmentoActivo - 1].fin - 10.0);
                else if(evento.target.closest(".boton-final.atras"))
                    this.procesaSegmento(6, null, this.segmentos[Producto.segmentoActivo - 1].fin - 1.0);
                else if(evento.target.closest(".boton-final.adelante"))
                    this.procesaSegmento(6, null, this.segmentos[Producto.segmentoActivo - 1].fin + 1.0);
                else if(evento.target.closest(".boton-final.adelante-rapido"))
                    this.procesaSegmento(6, null, this.segmentos[Producto.segmentoActivo - 1].fin + 10.0);
                else if(evento.target.closest(".boton-control.borrar")) {
                    if(Producto.segmentoActivo && Producto.segmentoActivo <= this.segmentos.length) {
                        this.segmentos[Producto.segmentoActivo - 1].dom.remove();
                        this.segmentos.splice(Producto.segmentoActivo - 1, 1);
                        this.activaDesactivaControlesSegmento();
                        Producto.segmentoActivo = 0;
                    }
                }
                else if(evento.target.closest(".boton-control.aceptar")) {
                    this.activaDesactivaControlesSegmento();
                    Producto.segmentoActivo = 0;
                }
                return;
            }
        }
        if(evento.type == "mouseup") {     // Se deja de pulsar el botón: deja de arrastrar lo que estuviera arrastrando.
            Producto.cursorArrastrado = 0;
        }
        else if((evento.type == "mousedown" || (evento.type == "mousemove" && Producto.cursorArrastrado)) &&
          (evento.target == this.cursorInicio || evento.target == this.cursorFin || evento.target.closest(".barra-timer-producto"))) {
            offX = evento.clientX - this.baseSegmentos.getBoundingClientRect().left;
            if(offX < 0) offX = 0;
            if(offX > this.baseSegmentos.clientWidth) offX = this.baseSegmentos.clientWidth;
            currentTime = Producto.controlGeneral.getTiempoSegunPosicionEnBarra(offX,
                    this.baseSegmentos.clientWidth, Producto.controlGeneral.video.duration);
        }
        else return;        // Parece que el mouse no estaba sobre la barra ni se estaba arrastrando ningún cursor.
        if(evento.type == "mousedown") {
            Producto.controlGeneral.video.pause();
            if((evento.target == this.cursorInicio || evento.target == this.cursorFin) && Producto.segmentoActivo) {
                Producto.cursorArrastrado = (evento.target == this.cursorInicio) ? 1 : 2;
            }
            else {          // Se ha pulsado sobre la barra vacía o sobre algún segmento existente en la misma.
                if(Producto.productoActivo != this) {
                    this.activaDesactivaControlesSegmento();
                    Producto.productoActivo = this;
                }
                let seg = this.getSegmentoPorPosicion(offX);
                if(seg) {   // Se ha pulsado sobre un segmento ya existente.
                    Producto.segmentoActivo = seg;
                    this.procesaSegmento(4);
                }
                else {      // Se ha pulsado sobre la barra vacía. Hay que generar un nuevo segmento.
                    Producto.segmentoActivo = this.procesaSegmento(5, currentTime, currentTime);
                }
            }
            Producto.controlGeneral.video.currentTime = currentTime;
        }
        else if(evento.type == "mousemove" && Producto.cursorArrastrado) {  // Actualiza el segmento activo.
            if(Producto.cursorArrastrado == 1) this.procesaSegmento(6, currentTime, null);
            else this.procesaSegmento(6, null, currentTime);
        }

    }

        // Comprueba si se está pulsando sobre el video o sobre el marcador del producto y llama al proceso correspondiente.
    // Devuelve true si es así.

    static procesaEventoMarcadorProductoSobreVideo(e) {
        let offX = 0, offY = 0;
        let pctX = 0.0, pctY = 0.0;

        const aux = Producto.controlGeneral.video.getBoundingClientRect();
/*
        console.log('Producto.controlGeneral.video.getBoundingClientRect().left = ' + Producto.controlGeneral.video.getBoundingClientRect().left)
        console.log('Producto.controlGeneral.video.getBoundingClientRect().top = ' + Producto.controlGeneral.video.getBoundingClientRect().top)
        console.log('e = ' + e);
        console.log('e.clientX = ' + e.clientX )
        console.log('e.clientY = ' + e.clientY )
*/
        offX = Producto.mouseX = e.clientX - aux.left;
        offY = Producto.mouseY = e.clientY - aux.top;
/*
        console.log('Producto.controlGeneral.video.getBoundingClientRect().left = ' + Producto.controlGeneral.video.getBoundingClientRect().left )
        console.log('Producto.controlGeneral.video.getBoundingClientRect().top = ' + Producto.controlGeneral.video.getBoundingClientRect().top)
        console.log('e = ' + e);
        console.log('e.clientX = ' + e.clientX )
        console.log('e.clientY = ' + e.clientY )
        console.log('offX = ' + offX )
        console.log('offY = ' + offY )
*/


        if(e.type == "mouseup") {
            if(Producto.marcadorArrastrado !== false) {
                Producto.marcadorArrastrado = false;
                Producto.controlGeneral.marcadorEnVideo.style.visibility = "hidden";
                return true;
            }
            return false;
        }
        if(e.target == Producto.controlGeneral.marcadorTarget || e.target == Producto.controlGeneral.video) {
            if(offX < 0) offX = 0;
            else if(offX > aux.width) offX = aux.width;
            if(offY < 0) offY = 0;
            else if(offY > aux.height) offY = aux.height;
            pctX = offX / aux.width;
            pctY = offY / aux.height;
        }
        else return false;
        switch(e.type) {
        case "mousedown":
            let res = this.procesaMarcadoresSobreVideo(true);
            if(res !== false) {
                if(res.segm != Producto.segmentoActivo) {
                    Producto.segmentoActivo = res.segm;
                    Producto.productoActivo.procesaSegmento(4);
                }
                Producto.marcadorArrastrado = res.trgt;
                Producto.productoActivo.segmentos[Producto.segmentoActivo - 1].target[res.trgt].pcx = pctX;
                Producto.productoActivo.segmentos[Producto.segmentoActivo - 1].target[res.trgt].pcy = pctY;
                Producto.controlGeneral.marcadorEnVideo.style.visibility = "visible";
                Producto.controlGeneral.marcadorEnVideo.style.left = offX + "px";
                Producto.controlGeneral.marcadorEnVideo.style.top = offY + "px";
                return true;
            }
            break;
        case "mousemove":
            if(Producto.marcadorArrastrado !== false) {
                Producto.productoActivo.segmentos[Producto.segmentoActivo - 1].target[Producto.marcadorArrastrado].pcx = pctX;
                Producto.productoActivo.segmentos[Producto.segmentoActivo - 1].target[Producto.marcadorArrastrado].pcy = pctY;
                Producto.controlGeneral.marcadorEnVideo.style.left = offX + "px";
                Producto.controlGeneral.marcadorEnVideo.style.top = offY + "px";
                return true;
            }
            break;
        }
        return false;
    }


    // Procesa la pulsación de un botón del marcador sobre el video (0- Anterior  1- Borrar  2- Siguiente).

    static procesaBotonMarcadorSobreVideo(boton, tiempoActual) {
        let cnt;
        let res = Producto.procesaMarcadoresSobreVideo();

        if(!Producto.productoActivo) return;
        switch(boton) {
        case 0:                                 // Pulsado botón para ir al marcador anterior.
            if(res !== false && !(res.flg & 2) && res.segm) {
                if(res.trgt > 0) {
                    Producto.controlGeneral.video.currentTime =
                                Producto.productoActivo.segmentos[res.segm - 1].target[res.trgt - 1].time;
                }
                else if(!res.trgt) {
                    Producto.controlGeneral.video.currentTime = Producto.productoActivo.segmentos[res.segm - 1].inicio;
                }
                else {
                    for(cnt = Producto.productoActivo.segmentos[res.segm - 1].target.length - 1; cnt >= 0; cnt--) {
                        if(Producto.productoActivo.segmentos[res.segm - 1].target[cnt].time < tiempoActual) {
                            Producto.controlGeneral.video.currentTime = Producto.productoActivo.segmentos[res.segm - 1].target[cnt].time;
                            break;
                        }
                    }
                    if(cnt < 0) Producto.controlGeneral.video.currentTime = Producto.productoActivo.segmentos[res.segm - 1].inicio;
                }
            }
            break;
        case 1:                                 // Pulsado botón para borrar marcador "clave" actual.
            if(res !== false && (res.flg & 1) && res.segm && res.trgt >= 0 &&
                    res.trgt < Producto.productoActivo.segmentos[res.segm - 1].target.length) {
                Producto.productoActivo.segmentos[res.segm - 1].target.splice(res.trgt, 1);
            }
            break;
        case 2:                                 // Pulsado botón para ir al marcador siguiente.
            if(res !== false && !(res.flg & 4) && res.segm) {
                if(res.trgt >= 0) {
                    if(res.trgt < Producto.productoActivo.segmentos[res.segm - 1].target.length - 1) {
                        Producto.controlGeneral.video.currentTime =
                                    Producto.productoActivo.segmentos[res.segm - 1].target[res.trgt + 1].time;
                    }
                    else Producto.controlGeneral.video.currentTime = Producto.productoActivo.segmentos[res.segm - 1].fin;
                }
                else {
                    for(cnt = 0; cnt < Producto.productoActivo.segmentos[res.segm - 1].target.length; cnt++) {
                        if(Producto.productoActivo.segmentos[res.segm - 1].target[cnt].time > tiempoActual) {
                            Producto.controlGeneral.video.currentTime = Producto.productoActivo.segmentos[res.segm - 1].target[cnt].time;
                            break;
                        }
                    }
                    if(cnt >= Producto.productoActivo.segmentos[res.segm - 1].target.length) Producto.controlGeneral.video.currentTime = Producto.productoActivo.segmentos[res.segm - 1].inicio;
                }
            }
        }
    }


    /* Posiciona marcador sobre video si este es visible (este método es llamado desde el control general cada 20 milisegundos) */

    static posicionaMarcadorSobreVideo() {
        let posx, posy;

        if(!Producto.controlGeneral) return;
        let res = Producto.procesaMarcadoresSobreVideo();
        if (res === false) {
            Producto.controlGeneral.marcadorEnVideo.style.visibility = "hidden";
            Producto.controlGeneral.marcadorPast.style.visibility = "hidden";
            Producto.controlGeneral.marcadorDel.style.visibility = "hidden";
            Producto.controlGeneral.marcadorNext.style.visibility = "hidden";
        }
        else {
            const aux = Producto.controlGeneral.video.getBoundingClientRect();
            if(Producto.marcadorArrastrado !== false) {
                posx = parseInt(Producto.controlGeneral.marcadorEnVideo.style.left);
                posy = parseInt(Producto.controlGeneral.marcadorEnVideo.style.top);
            }
            else {
                posx = res.pcx * aux.width;
                posy = res.pcy * aux.height;
                Producto.controlGeneral.marcadorEnVideo.style.left = posx + "px";
                Producto.controlGeneral.marcadorEnVideo.style.top = posy + "px";
            }
            Producto.controlGeneral.marcadorEnVideo.style.visibility = "visible";
            Producto.controlGeneral.marcadorTarget.classList.remove("item-nokey", "item-key");
            Producto.controlGeneral.marcadorTarget.classList.add((res.flg & 1) ? "item-key" : "item-nokey");

            // Visibiliza la botonera adecuada dependiendo del tipo y la posición del marcador.

            let dd = (posx - Producto.mouseX) * (posx - Producto.mouseX) + (posy - Producto.mouseY) * (posy - Producto.mouseY);
            let px = 0;
            if(!(res.flg & 2)) {
//                Producto.controlGeneral.marcadorPast.style.visibility = (dd < 3100) ? "visible" : "hidden";
//                Producto.controlGeneral.marcadorPast.style.left = px + "px";
                px += 22;
            }
            else Producto.controlGeneral.marcadorPast.style.visibility = "hidden";
            if(res.flg & 1) {
                Producto.controlGeneral.marcadorDel.style.visibility = (dd < 3100) ? "visible" : "hidden";
                Producto.controlGeneral.marcadorDel.style.left = px + "px";
                px += 22;
            }
            else Producto.controlGeneral.marcadorDel.style.visibility = "hidden";
            if(!(res.flg & 4)) {
                Producto.controlGeneral.marcadorNext.style.visibility = (dd < 3100) ? "visible" : "hidden";
                Producto.controlGeneral.marcadorNext.style.left = px + "px";
                px += 22;
            }
            else Producto.controlGeneral.marcadorNext.style.visibility = "hidden";
            Producto.controlGeneral.marcadorBotonera.style.left = (-px * 0.5) + "px";
        }
    }


    // Comprueba si existe un marcador para el tiempo actual del video (creándolo si no existe y 'creaMarcador' es true).
    // Devuelve false si no existe el marcador.
    // Si existe el marcador (o se ha creado uno) devuelve un objeto conteniendo las siguientes propiedades:
    // segm: Índice al segmento del video en el que se encuentra el marcador (comenzando en 1).
    // trgt: Índice del marcador dentro del segmento anterior (comenzando en 0).
    // flg:
    //      -Bit 0: Se trata de un marcador "clave".
    //      -Bit 1: Se trata del primer marcador posible del segmento (el timer coincide con el del inicio del segmento).
    //      -Bit 2: Se trata del último marcador posible del segmento (el timer coincide con el del final del segmento).
    // pcx: Posición horizontal del marcador dentro del video en porcentaje.
    // pcy: Posición vertical del marcador dentro del video en porcentaje.

    // Si existen 'productoEspecifico' y 'tiempoEspecifico' los utiliza en lugar de los contenidos en 'productoActivo'
    // y 'controlGeneral' (esta opción se utiliza para crear la tabla de "tiempos posiciones" antes de generar el JSON para
    // salvar el contenido actual en la base, por el método generaListaCompletaDeTargets()).

    static procesaMarcadoresSobreVideo(creaMarcador = false, productoEspecifico = null, tiempoEspecifico = 0.0) {
        let inicio = 0, final = 0, flg = 0;
        let segm = 0, trgt = -1;
        const pa = (productoEspecifico) ? productoEspecifico : Producto.productoActivo;
        const cg = Producto.controlGeneral;

        if(!productoEspecifico && (!pa || !cg || !Producto.segmentoActivo || Producto.segmentoActivo > pa.segmentos.length))
            return false;
        if(creaMarcador && !cg.video.paused) return false;

        let tiempoActual = (productoEspecifico) ? parseInt(tiempoEspecifico * 10) : parseInt(cg.video.currentTime * 10);
        pa.segmentos.forEach((p, s) => {
            if(!segm) {
                inicio = parseInt(p.inicio * 10);       // Inicio del segmento en decimas de segundo.
                final = parseInt(p.fin * 10);           // Fin del segmento en decimas de segundo.
                if(inicio <= tiempoActual && final >= tiempoActual) {
                    segm = s + 1;
                    p.target.forEach((t, r) => {
                        if(trgt < 0) {
                            let time = parseInt(t.time * 10);
                            if(time == tiempoActual) trgt = r;
                        }
                    });
                }
            }
        });
        if(trgt >= 0) {
            let porX = pa.segmentos[segm - 1].target[trgt].pcx;
            let porY = pa.segmentos[segm - 1].target[trgt].pcy;
            flg = 1;
            if(tiempoActual <= inicio) flg |= 2;
            if(tiempoActual >= final) flg |= 4;
            return {segm: segm, trgt: trgt, flg: flg, pcx: porX, pcy: porY};         // Ya existe un marcador para este tiempo.
        }
        else if(segm) {         // No existe un marcador para este tiempo pero está en un segmento de tiempo válido.
            if(creaMarcador) {
                let time = parseFloat(tiempoActual / 10.0);
                pa.segmentos[segm - 1].target.push({time: time, pcx: 0.0, pcy: 0.0});
                pa.segmentos[segm - 1].target.sort((a, b) => a.time - b.time);
                for(let cnt = 0; cnt < pa.segmentos[segm - 1].target.length; cnt++) {
                    if(pa.segmentos[segm - 1].target[cnt].time == parseFloat(tiempoActual / 10.0)) {
                        flg = 1;
                        if(tiempoActual <= inicio) flg |= 2;
                        if (tiempoActual >= final) flg |= 4;
                        return {segm: segm, trgt: cnt, flg: flg};
                    }
                }
            }
            else {      // Averigua la posición que debería ocupar el marcador sobre el video para este tiempo.
                let tgt = [-1, -1];
                for(let cnt = 0; cnt < pa.segmentos[segm - 1].target.length; cnt++) {
                    let time = parseInt(pa.segmentos[segm - 1].target[cnt].time * 10);
                    if(tiempoActual < time) {
                        tgt[1] = cnt;
                        if(tgt[0] < 0) tgt[0] = tgt[1];
                        break;
                    }
                    else if(tiempoActual > time) tgt[0] = cnt;
                }
                if(tgt[1] < 0) tgt[1] = tgt[0];
                if(tgt[0] >= 0 && tgt[1] >= 0) {
                    const s0 = pa.segmentos[segm - 1].target[tgt[0]];
                    const s1 = pa.segmentos[segm - 1].target[tgt[1]];
                    if(tgt[0] == tgt[1]) {
                        flg = 0;
                        if(tiempoActual <= inicio) flg |= 2;
                        if(tiempoActual >= final) flg |= 4;
                        return {segm: segm, trgt : -1, flg: flg, pcx: s0.pcx, pcy: s0.pcy};
                    }
                    let ang = Math.atan2(s1.pcy - s0.pcy, s1.pcx - s0.pcx);
                    let dd = Math.sqrt((s1.pcy - s0.pcy) * (s1.pcy - s0.pcy) + (s1.pcx - s0.pcx) * (s1.pcx - s0.pcx));
                    let pt = parseFloat((tiempoActual - parseInt(s0.time * 10)) / (parseInt(s1.time * 10) - parseInt(s0.time * 10)));
                    let porX = s0.pcx + dd * pt * Math.cos(ang);
                    let porY = s0.pcy + dd * pt * Math.sin(ang);
                    flg = 0;
                    if(tiempoActual <= inicio) flg |= 2;
                    if(tiempoActual >= final) flg |= 4;
                    return {segm: segm, trgt : -1, flg: flg, pcx: porX, pcy: porY};
                }
            }
        }
        return false;      // No existe ni se puede crear un marcador para este tiempo.
    }


    static inicia(controlGeneral = null) {
        if(controlGeneral) Producto.controlGeneral = controlGeneral;
        Producto.productoActivo = null;
        Producto.segmentoActivo = 0;
        Producto.cursorArrastrado = 0;
        Producto.marcadorArrastrado = false;
        Producto.productos = [];
        Producto.mouseX = 0;
        Producto.mouseY = 0;
    }

    // Actualiza la posición de la barra de tiempo general en todos los productos.
    // Este método se llama desde el objeto ControlGeneral cada 20 milisegundos.

    static actualizaPosicionCursorTiempoGeneral() {
        if(!Producto.controlGeneral.video.duration) return;
        let posicion = null;
        Producto.productos.forEach(el => {
            if(posicion === null) posicion = Producto.controlGeneral.getPosicionBarraSegunTiempo(
                Producto.controlGeneral.video.currentTime, el.baseSegmentos.clientWidth, Producto.controlGeneral.video.duration);
            el.cursorTiempoGeneral.style.left = posicion + "px";
        });
    }

    // Comprueba si el evento recibido actúa sobre un producto y lo procesa en caso de que así sea.
    // Este método se llama desde el objeto ControlGeneral cuando se recibe un evento de ratón que no se puede procesar.

    static verificaYProcesaPulsacionEnProducto(evento) {
        if(!Producto.controlGeneral.video.duration) return;
        if((evento.type == "mouseup" || evento.type == "mousemove") && Producto.productoActivo && Producto.cursorArrastrado) {
            Producto.productoActivo.procesaEvento(evento);
            return;
        }
        const el = evento.target;
        const baseProductoEnDOM = el.closest(".base-producto");    // Devuelve el ancestro más cercano del tipo indicado.
        const producto = Producto.productos.find(e => e.elementoBaseEnDOM == baseProductoEnDOM);
        if(producto) producto.procesaEvento(evento);
    }


    // Borra el producto con el índice indicado (-1 Borra todos los productos).

    static borraProducto(indice = -1) {
        for(let cnt = 0; cnt < Producto.productos.length; cnt++) {
            if(indice < 0 || indice == cnt) {
                if(Producto.productos[cnt].elementoBaseEnDOM) Producto.productos[cnt].elementoBaseEnDOM.remove();
            }
        }
        Producto.segmentoActivo = 0;
        Producto.productoActivo = null;
        Producto.cursorArrastrado = 0;

        document.getElementById("contenedor-productos").scrollTop = 0;
    }

    // Carga los productos a partir del array de objetos que codificado en el campo data de la tabla guardada en el servidor.

    static cargaProductosAPartirDeArray(data) {
        data.forEach(dato => {
            const pr = new Producto();
            pr.identificador = dato.identificador;
            pr.producto = dato.producto;
            pr.nombre = dato.nombre;
            let el = document.querySelector('[data-elemento="' + pr.identificador + '"]');
            el.querySelector('[name="product-selected"]').value = pr.producto;
            dato.segmentos.forEach(sg => pr.procesaSegmento(1, sg.inicio, sg.fin, sg.target));
        });
        Producto.controlGeneral.video.currentTime = 0;
        Producto.controlGeneral.video.pause();
        Producto.updateRotulosListado();
    }

    static updateRotulosListado() {
        let el = document.querySelector('#contenedor-productos').querySelectorAll('.base-producto');
        el.forEach((e) => {
            let pr = e.querySelector('[name="product-selected"]').value;
            if ('' != pr) {
                let l = e.querySelector('[data-value="' + pr + '"]');
                if (l) {
                    e.querySelector('.label-product-hotpoint').innerHTML = l.innerHTML;
                    l.classList.add('selected');
                }
            }
        })
    }


    // Genera un objeto JSON con todos los productos.

    static codificaProductos() {
        const data = [];
        Producto.productos.forEach(pr => {
            let producto = {};
            producto.identificador = pr.identificador;
            producto.producto = pr.producto;
            producto.nombre = pr.nombre;
            producto.segmentos = [];
            producto.segmentos_precalculados = [];
            pr.segmentos.forEach(sg => {
                const sgm = {target: [], inicio: sg.inicio, fin: sg.fin};
                sg.target.forEach(tg => {
                    const tgt = {time: tg.time, pcx: tg.pcx, pcy: tg.pcy};
                    sgm.target.push(tgt);
                });
                producto.segmentos.push(sgm);
            });
            producto.segmentos_precalculados = [...this.generaListaCompletaDeTargets(pr)];
            data.push(producto);
        });
        return JSON.stringify(data);
    }

    static generaListaCompletaDeTargets(pr) {
        let lista = [];
        let rsp, trgt, time = 0.0;

        pr.segmentos.forEach((sg, i) => {
            time = parseFloat(parseInt(sg.inicio * 10) / 10.0);
            if(lista.length && time == lista[lista.length - 1].time) lista.pop();
            while(true) {
                rsp = this.procesaMarcadoresSobreVideo(false, pr, time);
                if(!rsp || rsp.segm != (i + 1)) break;
                trgt = {time: time,
                        pcx: parseFloat(parseInt(rsp.pcx * 10000) / 10000.0),
                        pcy: parseFloat(parseInt(rsp.pcy * 10000) / 10000.0)
                };
                lista.push(trgt);
                time = parseFloat((parseInt(time * 10) + 1) / 10.0);
            }
        });

        return lista;
    }


    // Decodifica un texto en formato JSON generado por el método anterior.

    static decodificaProductos(jsonData) {
        const data = JSON.parse(jsonData);
        return data;
    }

}
Producto.productoActivo = null;              // Puntero al producto activo (null- No nay ningún producto activo).
Producto.segmentoActivo = 0;                 // Segmento activo (1 a n,  0: Ninguno activo)
Producto.cursorArrastrado = 0;               // 1- Arrastrado el cursor de inicio  2- Arrastrado el cursor de final
Producto.marcadorArrastrado = false;         // Marcador que aparece sobre el video está siendo arrastrado (1 a n, false: Ninguno).
Producto.mouseX = 0;
Producto.mouseY = 0;
Producto.productos = [];
Producto.controlGeneral = null;


/////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////


class VentanaLoadSave {
    constructor() {
        this.ventana = document.getElementById("ventana-io");
        this.directorio = document.getElementById("lista-ficheros");
        this.nombreFichero = document.getElementById("input-fichero");
        this.botonFichero = document.getElementById("boton-fichero");
        this.ventana.style.visibility = "hidden";
        this.salvar = false;
    }


    activa(salvar = false) {
        if(this.ventana.style.visibility == "hidden") {
            this.ventana.style.visibility = "visible";
            this.salvar = salvar;
            this.botonFichero.innerHTML = (this.salvar) ? "Salvar" : "Cargar";
            let comando = {comando: "DIR"};
            consultaBase(comando,
                resultado => {
                    while(this.directorio.hasChildNodes()) this.directorio.removeChild(this.directorio.firstChild);
                    resultado.forEach(file => {
                        const elemento = document.createElement("div");
                        elemento.className = "fichero";
                        elemento.innerHTML = `${file}`;
                        this.directorio.appendChild(elemento);
                    });
                },
                resultado => { alert ("Error al comunicar con el servidor")});
        }
    }


    desactiva() {
        while(this.directorio.hasChildNodes()) this.directorio.removeChild(this.directorio.firstChild);
        this.nombreFichero.value = "";
        this.ventana.style.visibility = "hidden";
    }


    verificaYProcesaPulsacionEnVentanaLoadSave(evento) {
        if(this.ventana.style.visibility != "visible") return;
        if(evento.type == "mousedown") {
            if(!evento.target.closest("#ventana-io")) this.desactiva();
            else if(evento.target.classList.contains("fichero")) this.nombreFichero.value = evento.target.innerHTML;
        }
        else if(evento.type == "click" && evento.target == this.botonFichero) {
            if(!evento.target.closest("#ventana-io")) this.desactiva();
            else if(this.nombreFichero.value != "") {
                if(this.salvar) {                               // Salvar los datos al servidor.
                    const datosASalvar = Producto.codificaProductos();
                    let comando = {
                        action: "save",
                        id: this.nombreFichero.value,
                        data: datosASalvar
                    };
                    consultaBase(comando,
                    resultado => {                  // Resultado correcto.
                        this.desactiva();
                    },
                    resultado => {                  // Resultado erróneo.
                        this.desactiva();
                        alert("Error. No se pudieron salvar los datos correctamente");
                    });
                } else {
                    let comando = {
                        comando: "GET",
                        nombre: this.nombreFichero.value,
                    };
                    consultaBase(comando,
                    resultado => {                  // Resultado correcto.
                        this.desactiva();
                        if(resultado == "BAD") alert("Error. No se encontró el fichero");
                        else {
                            Producto.borraProducto();
                            Producto.cargaProductosAPartirDeArray(resultado);
                        }
                    },
                    resultado => {                  // Resultado erróneo.
                        this.desactiva();
                        alert("Error. No se pudieron cargar los datos correctamente");
                    });
                }
            }
        }
    }

}

/////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////

const URL_PELIS = "/api-iwi";

async function consultaBase(parametros = null, callbackOk = null, callbackBad = null) {
    const data = new FormData();
    if (parametros) for (const propiedad in parametros) data.append(propiedad, parametros[propiedad]);
    data.append('_token', document.querySelector('#token_ajax input').value);
    try {
        let respuesta = await fetch(URL_PELIS, {
            method: 'POST',
            body: data
        });
        let resultado = await respuesta.json();    // text() o json()
        if(!respuesta.ok) throw {status: respuesta.status, statusText: respuesta.statusText};
        if(callbackOk) callbackOk(resultado);
    }
    catch(err) {
        if(callbackBad) callbackBad(err.status, err.statusText || "Error desconocido");
    }
}

/////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////


document.addEventListener("readystatechange", function () {
    if (document.readyState === "complete") {
        new ControlGeneral();
        Producto.borraProducto();
        let x = document.querySelector('#hotpoints-json').value;
        x = atob(x);
        x = JSON.parse(x);
//         console.log(x);
        if ('bad' != x) {
            Producto.cargaProductosAPartirDeArray(x);
        }

        if (document.querySelector('#create_keyfile')) {
            document.querySelector('#create_keyfile').addEventListener('click', (e) => {
                let comando = {
                    action: "create_keyfile",
                    id: document.querySelector('#version-id').value,
                };
                consultaBase(comando, function(){ location.reload(); }, function(){ location.reload(); });
            })
            Array.prototype.forEach.call(document.querySelectorAll('#enable_disable_lic'), (e) => {
                e.addEventListener('click', (b) => {
                    let comando = {
                        action: "enable_disable_keyfile",
                        id: b.target.dataset.id,
                    };
                    consultaBase(comando, function(){ location.reload(); }, function(){ location.reload(); });
                });
            })
            Array.prototype.forEach.call(document.querySelectorAll('#delete_keyfile'), (e) => {
                e.addEventListener('click', (b) => {
                    let comando = {
                        action: "delete_keyfile",
                        id: b.target.dataset.id,
                    };
                    consultaBase(comando, function(){ location.reload(); }, function(){ location.reload(); });
                });
            })
            Array.prototype.forEach.call(document.querySelectorAll('#download_keyfile'), (e) => {
                e.addEventListener('click', (b) => {
                    let comando = {
                        action: "download_keyfile",
                        id: b.target.dataset.id,
                    };
                    consultaBase(comando, function(){}, function(){});
                });
            })
            //
            // Submit key file name
            //
            Array.prototype.forEach.call(document.querySelectorAll('#keyfile_name'), (e) => {
                e.addEventListener('change', (b) => {
                    let comando = {
                        action: "update_keyfile_name",
                        id: b.target.dataset.id,
                        name: b.target.value,
                    };
                    consultaBase(comando, cambiaNombre, cambiaNombre);
                });
            })
        }
    }
});

function cambiaNombre(resultado) {
    let link = document.querySelector('#df-' + resultado.id);
    if (link) {
        link.setAttribute('href', '/keyfile/' + resultado.fn);
    }
}

//
// Productos vinculados
//
function prepareEvents(contenedor) {
    Array.prototype.forEach.call(contenedor.querySelectorAll('.label-product-hotpoint'), (e) => {
        e.removeEventListener('click', update_product_hotpoint, true);
        e.addEventListener('click', update_product_hotpoint, true);
        Array.prototype.forEach.call(e.parentElement.querySelectorAll('.product-hotpoint-element'), (o) => {
            o.removeEventListener('click', update_product_hotpoint_option, true);
            o.addEventListener('click', update_product_hotpoint_option, true);
        })
    })
}
function update_product_hotpoint(e) {
    e.target.style.display = 'none';
    e.target.parentElement.querySelector('.product-hotpoint').style.display = 'block';
}
function update_product_hotpoint_option(e) {
    e = e.target;
    parent = e.parentElement.parentElement;
    parent.querySelector('.product-hotpoint').style.display = 'none';
    Array.prototype.forEach.call(parent.querySelectorAll('.product-hotpoint-element'), (o) => {
        o.classList.remove('selected');
    })
    e.classList.add('selected');
    let elem_id = parent.parentElement.dataset.elemento;
    parent.querySelector('.product-hotpoint').style.display = 'none';
    parent.querySelector('.label-product-hotpoint').innerHTML = e.innerHTML;
    parent.querySelector('[name="product-selected"]').value = e.dataset.value;
    parent.querySelector('.label-product-hotpoint').style.display = 'block';
    Producto.productos[ elem_id ].producto = e.dataset.value;
//    console.log(Producto.productos[elem_id]);
}
