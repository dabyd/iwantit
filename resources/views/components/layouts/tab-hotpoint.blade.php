@props( [ 'data', 'hotpointEditor', 'productos', 'hotpoints', 'video' ] )
@php
	$currentCount = \App\Helpers\TabCounter::incrementAndGet();
@endphp

@isset ( $hotpointEditor )
    <div class="tab-{{ $currentCount }}">
        <h2>Hotpoints editor</h2>
        <h3>({{ $data->name }})</h3>
        <div id="capa-save" style="display: none;">
            <h1>Saving data...</h1>
        </div>
        <div id="token_ajax" style="display: none;">
            @csrf
        </div>
        <link rel="stylesheet" href="{{ URL::asset('css/publicidadDinamica.css' ) }}">
        <input type="hidden" id="hotpoints-json" value="{{ base64_encode( $hotpoints ) }}" />
        <input type="hidden" id="version-id" value="{{ $data->id }}" />
        <div id="caja-principal">
            <div id="item-base">
                <video autoplay="true" muted id="video-display">
                    <source src="{{ $video }}" type="video/mp4" />
                </video>

                <!-- Puntero que aparece sobre la posición del producto activo en el video reproducido -->

                <div id="item-main">
                    <div id="item-target" class="item-nokey"></div>
                    <div id="item-botonera">
                        <button id="item-past" class="item-bt"><img style="width:20px; height:20px;" src="{{URL::asset('img/fastBackward.png')}}" /></button>
                        <button id="item-del" class="item-bt"><img style="width:20px; height:20px;" src="{{URL::asset('img/erase.png')}}" /></button>
                        <button id="item-next" class="item-bt"><img style="width:20px; height:20px;" src="{{URL::asset('img/fastForward.png')}}" /></button>
                    </div>
                </div>

            </div>

            <!-- Controles generales del video -->

            <div id="base-controles">
                <div style="display: flex">
                    <div id="botonera-opciones" style="display:flex">
                        <!--                    <button class="boton-opciones" id="boton-cargar">Load</button> -->
                        <button class="boton-opciones" id="boton-salvar">Save</button>
                    </div>
                    <div id="botonera-tiempo-general">
                        <button class="boton-tiempo-general" id="boton-atras-rapido">
                            <img class="img-tiempo-general" src="{{URL::asset('img/fastBackward.png')}}" />
                        </button>
                        <button class="boton-tiempo-general" id="boton-atras">
                            <img class="img-tiempo-general" src="{{URL::asset('img/backwardStop.png')}}" />
                        </button>
                        <button class="boton-tiempo-general" id="boton-stop-play">
                            <input type="hidden" id="play-ico-hpe" value="{{URL::asset('img/play.png')}}" />
                            <input type="hidden" id="stop-ico-hpe" value="{{URL::asset('img/stop.png')}}" />
                            <img class="img-tiempo-general" src="{{URL::asset('img/play.png')}}" />
                        </button>
                        <button class="boton-tiempo-general" id="boton-adelante">
                            <img class="img-tiempo-general" src="{{URL::asset('img/forwardStop.png')}}" />
                        </button>
                        <button class="boton-tiempo-general" id="boton-adelante-rapido">
                            <img class="img-tiempo-general" src="{{URL::asset('img/fastForward.png')}}" />
                        </button>
                    </div>
                </div>
                <div class="control-tiempo-general">
                    <div class="tiempo-general">
                        <input type="text" class="horas actual" value="00" maxlength="2" /><span> :</span>
                        <input type="text" class="minutos actual" value="00" maxlength="2" /><span> :</span>
                        <input type="text" class="segundos actual" value="00" maxlength="2" />
                        <br>
                        <input type="text" class="horas total" value="00" maxlength="2" readonly disabled /><span> :</span>
                        <input type="text" class="minutos total" value="00" maxlength="2" readonly disabled /><span> :</span>
                        <input type="text" class="segundos total" value="00" maxlength="2" readonly disabled />
                    </div>
                    <div id="barra-tiempo-general">
                        <div id="cursor-tiempo-general"></div>
                    </div>
                </div>
            </div>

            <!-- Botón añadir producto -->
            <div id="otro-producto">
                <img src="{{URL::asset('img/plus.png')}}" />
            </div>

            <!-- Control de productos -->
            <div id="contenedor-productos">
            </div>
        </div>
    </div>

    <!-- Ventana para cargar y salvar listas de productos -->

    <div id="ventana-io">
        <div id="lista-ficheros"></div>
        <div id="barra-ficheros">
            <input type="text" id="input-fichero" value="" maxlength="30" />
            <button id="boton-fichero">Load</button>
        </div>
    </div>

    <!-- Templates -->

    <template id="plantilla-producto">
        <div class="producto" data-elemento="">
            <div class="info-producto">
                <p class="label-product-hotpoint">No product assigned</p>
                <div class="product-hotpoint">
                    <div class="product-hotpoint-ui">
                        <div>Assigned products</div>
                        <a class="btn btn-primary close-button" href="#">
                            <i class="fa-solid fa-close" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="product-hotpoint-scroll">
                        <div class="product-hotpoint-wrapper">
                            <p class="product-hotpoint-element" data-value="0">No product assigned</p>
                            @foreach ($productos as $id => $producto)
                                <p class="product-hotpoint-element" data-value="{{$producto['id']}}">{{$producto['name']}}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
                <input type="hidden" name="product-selected" value="" />
            </div>
            <div class="timer-producto">
                <div class="barra-timer-producto">
                    <div class="base-segmentos-tiempo"></div>
                    <div class="cursor-tiempo-general"></div>
                    <div class="cursor-inicio"></div>
                    <div class="cursor-final"></div>
                </div>
                <div class="control-producto">
                    <div class="tiempo-seccion">
                        <div>
                            <span>Start</span>
                            <span>End</span>
                        </div>
                        <div style="width: 120px">
                            <input type="text" class="horas inicio" readonly disabled maxlength="2" /><span> :</span>
                            <input type="text" class="minutos inicio" readonly disabled maxlength="2" /><span> :</span>
                            <input type="text" class="segundos inicio" readonly disabled maxlength="2" />
                            <br>
                            <input type="text" class="horas final" readonly disabled maxlength="2" /><span> :</span>
                            <input type="text" class="minutos final" readonly disabled maxlength="2" /><span> :</span>
                            <input type="text" class="segundos final" readonly disabled maxlength="2" />
                        </div>
                        <div>
                            <button class="boton-inicio atras-rapido"><img class="img-tiempo-general" src="{{URL::asset('img/fastBackward.png')}}" /></button>
                            <button class="boton-inicio atras"><img class="img-tiempo-general" src="{{URL::asset('img/backwardStop.png')}}" /></button>
                            <button class="boton-inicio adelante"><img class="img-tiempo-general" src="{{URL::asset('img/forwardStop.png')}}" /></button>
                            <button class="boton-inicio adelante-rapido"><img class="img-tiempo-general" src="{{URL::asset('img/fastForward.png')}}" /></button>
                            <br>
                            <button class="boton-final atras-rapido"><img class="img-tiempo-general" src="{{URL::asset('img/fastBackward.png')}}" /></button>
                            <button class="boton-final atras"><img class="img-tiempo-general" src="{{URL::asset('img/backwardStop.png')}}" /></button>
                            <button class="boton-final adelante"><img class="img-tiempo-general" src="{{URL::asset('img/forwardStop.png')}}" /></button>
                            <button class="boton-final adelante-rapido"><img class="img-tiempo-general" src="{{URL::asset('img/fastForward.png')}}" /></button>
                        </div>
                        <div style="width: 70px">
                            <button class="boton-control borrar"><img class="img-tiempo-general" src="{{URL::asset('img/erase.png')}}" /></button>
                        </div>
                        <div style="width: 60px">
                            <button class="boton-control aceptar">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <script src="{{ URL::asset('js/publicidadDinamica.js' ) }}"></script>
@endisset
