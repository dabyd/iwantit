@props( [ 'data', 'ai_url', 'datision', 'threshold_secs', 'ia_clases', 'video', 'video_fps', 'video_w', 'video_h' ] )
@php
	$currentCount = \App\Helpers\TabCounter::incrementAndGet();
@endphp

@isset( $datision )
    <style>
        .detection_list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-gap: 20px;
            align-items: start;
            /* Asegura que ambas columnas empiecen desde arriba */
        }

        #detections_table {
            max-height: 100vh;
            /* Esto es opcional si quieres limitar su altura */
            overflow-y: auto;
            /* Activa scroll interno si quieres */
        }

        /* Cabecera sticky */
        #detections_table th {
            position: sticky;
            top: 0;
            z-index: 1;
            /* Que se quede encima de los td al hacer scroll */
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            /* Opcional pero sexy */
        }

        #video_detection {
            position: sticky;
            top: 0;
            align-self: start;
            /* Muy importante para que no se intente centrar */
        }

        .iwt-admin .table .resaltado .listado {
            background-color: #909090;
            /* Color de fondo para los objetos resaltados */
        }

        .detected_item_group.resaltado td {
            background-color: #e0a0a0;
            /* Color de fondo para los objetos resaltados */
        }

        .detected_group_see_more {
            text-decoration: underline dotted;
            cursor: pointer;
        }

        @keyframes flash-border {
            0% {
                background: red;
            }

            33% {
                background: white;
            }

            66% {
                background: red;
            }

            100% {
                background: transparent;
            }

            /* fondo se desvanece, el borde se queda */
        }

        /* visor contenedor */
        .zoom-pane {
            position: absolute;
            top: 0;
            overflow: hidden;
            width: 50%;
            height: 100%;
            pointer-events: none;
            z-index: 200;
        }

        /* vídeo clonado escalado */
        .zoom-pane video {
            position: absolute;
            top: 0;
            left: 0;
            transform-origin: 0 0;
            /* se actualiza con el ratón */
        }

        .radio-container {
            width: 20px;
        }

        /* Estilos del modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
        }

        .modal-content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
    </style>
    <div class="tab-{{ $currentCount }}">
        <h2>AI Objects detection</h2>
        <input type="hidden" name="csrf-token" content="{{ csrf_token() }}">
        <button
            id="ai-machine-detection"
            class="btn btn-primary"
            data-url="{{ $ai_url }}"
            data-secs="{{ $threshold_secs }}"
            data-id="{{ $data->id }}"
            data-video="{{ $video }}"
            data-csrf="{{ csrf_token() }}"
            data-save-url="/ai/launch">
            New object recognition using AI
        </button>
        <br />
        <div id="ai-classes" style="display: none;padding: 10px;margin: 10px;border: 1px solid;border-radius: 10px;">
            <div class="d-flex gap-2 mb-3">
                <button type="button" class="btn btn-outline-primary" id="btnSelectAll">Select all</button>
                <button type="button" class="btn btn-outline-secondary" id="btnDeselectAll">Deselect all</button>
                <button type="button" class="btn btn-outline-secondary" id="btnCloseClases">Cancel</button>
                <button type="button" class="btn btn-success" id="btnLaunchAI">Start new AI recognition task</button>
                <div id="progressAI" style="display:none"></div>
            </div>

            @if(empty($ia_clases))
            <p class="text-muted">No classes available.</p>
            @else
            <style>
                /* Ajusta tamaño y borde por si acaso */
                input[type="checkbox"].form-check-input {
                    appearance: none;
                    -webkit-appearance: none;
                    -moz-appearance: none;
                    padding: 8px;
                    width: 14px;
                    /* más pequeño */
                    height: 14px;
                    /* más pequeño */
                    border: 1px solid #fff;
                    border-radius: 3px;
                    background-color: #0a2240;
                    display: inline-block;
                    position: relative;
                    cursor: pointer;
                    vertical-align: middle;
                }

                input[type="checkbox"].form-check-input:checked::after {
                    content: "✔";
                    color: #30f030;
                    font-size: 15px;
                    /* ajustado al nuevo tamaño */
                    position: absolute;
                    top: -1px;
                    left: 1px;
                }

                #btnLaunchAI {
                    position: absolute;
                    right: 10px;
                }

                #ai-classes {
                    position: relative;
                }
            </style>
            <div class="list-group mb-3" id="classesList" style="display: grid;grid-template-columns: repeat( 5, 1fr );">
                @foreach($ia_clases as $row)
                @php
                // Marca como checked si venía de un submit previo (old())
                $checked = in_array($row['id'], old('classes', []));
                @endphp
                <label class="list-group-item d-flex align-items-center gap-2">
                    <input
                        type="checkbox"
                        name="classes[]"
                        value="{{ $row['name'] }}"
                        class="form-check-input me-2 class-item"
                        @checked($checked)>
                    <span>{{ $row['name'] }}</span>
                </label>
                @endforeach
            </div>
            @endif

            <div class="small text-muted">
                Selected: <span id="selectedCount">0</span>
            </div>
        </div>
        <div id="ia_matcher_window">
            <input type="hidden" id="project_id" value="{{ $data->id }}" />
            <select id="detection_objects">
                <option selected>Choose one</option>
                <?php foreach ($datision as $elemento): ?>
                    <option value="{{$elemento['option']}}">{{ $elemento['class'] }} ({{ $elemento['objects_count'] }} times, {{ $elemento['detections_count'] }} detections) </option>
                <?php endforeach; ?>
            </select>
            @php
            $default_frames = \App\Http\Controllers\DatisionParameterController::getValue('frames');
            if ( $default_frames < 1 ) {
                $default_frames=1;
                }
                @endphp
                <label for="distance_frames"> Distance frames (0 for disable agrupation):</label>
                <input type="text" id="distance_frames" value="{{ $default_frames }}" />
                <input type="hidden" id="video_h" value="{{ $video_h }}" />
                <input type="hidden" id="video_w" value="{{ $video_w }}" />
                <input type="hidden" id="video_fps" value="{{ $video_fps }}" />
                <div class="detection_list">
                    <div id="detections_table"></div>
                    <div class="video_and_objects">
                        <video muted id="video_detection" style="width: 100%; height: auto;">
                            <source src="{{ $video }}" type="video/mp4" />
                        </video>
                        
                        <!-- Botón New Product justo debajo del video -->
                        <div style="margin-top: 10px; margin-bottom: 15px;">
                            <button type="button" class="btn btn-success" id="btnNewProduct">
                                <i class="fas fa-plus"></i> New Product
                            </button>
                        </div>
                        
                        <div id="products_table"></div>
                    </div>
                </div>
        </div>
    </div>

    <!-- Modal para nuevo producto -->
    <div id="newProductModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>New Product</h3>
                <button type="button" class="modal-close" id="closeModal">&times;</button>
            </div>
            
            <form id="newProductForm">
                @csrf
                <div class="form-group mb-3">
                    <label for="productName" class="form-label"><strong>Name *</strong></label>
                    <input type="text" class="form-control" id="productName" name="name" required placeholder="Product name">
                </div>

                <div class="form-group mb-3">
                    <label for="productBrand" class="form-label"><strong>Brand</strong></label>
                    <select class="form-control" id="productBrand" name="brands_id">
                        <option value="">No brand selected</option>
                        @php
                            $brands = \Illuminate\Support\Facades\DB::table('brands')->get();
                        @endphp
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="productUrl" class="form-label"><strong>URL</strong></label>
                    <input type="text" class="form-control" id="productUrl" name="url" placeholder="Product URL">
                </div>

                <div class="form-group mb-3">
                    <label for="productStatus" class="form-label"><strong>Status</strong></label>
                    <select class="form-control" id="productStatus" name="disabled">
                        <option value="0">Enabled</option>
                        <option value="1">Disabled</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="productAutoOpen" class="form-label"><strong>Auto open URL</strong></label>
                    <select class="form-control" id="productAutoOpen" name="auto_open">
                        <option value="1">Disabled</option>
                        <option value="0">Enabled</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="productImage" class="form-label"><strong>Image</strong></label>
                    <input type="file" class="form-control" id="productImage" name="filename" accept="image/*">
                </div>

                <div class="form-group mb-3">
                    <label for="productIcon" class="form-label"><strong>Icon hotpoint</strong></label>
                    <input type="file" class="form-control" id="productIcon" name="icono" accept="image/*">
                </div>

                <div class="alert alert-info">
                    <small><strong>Selected detection class:</strong> <span id="selectedDetectionClass">None selected</span></small>
                </div>
            </form>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelProduct">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveProduct">Save Product</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('newProductModal');
        const btnNewProduct = document.getElementById('btnNewProduct');
        const closeModal = document.getElementById('closeModal');
        const cancelProduct = document.getElementById('cancelProduct');
        const saveProduct = document.getElementById('saveProduct');
        const form = document.getElementById('newProductForm');
        const detectionSelect = document.getElementById('detection_objects');
        const selectedClassSpan = document.getElementById('selectedDetectionClass');

        // Abrir modal
        btnNewProduct.addEventListener('click', function() {
            // Actualizar la clase seleccionada
            updateSelectedClass();
            modal.style.display = 'block';
        });

        // Cerrar modal
        function closeModalFunc() {
            modal.style.display = 'none';
            form.reset();
        }

        closeModal.addEventListener('click', closeModalFunc);
        cancelProduct.addEventListener('click', closeModalFunc);

        // Cerrar modal al hacer clic fuera
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModalFunc();
            }
        });

        // Actualizar clase seleccionada cuando cambie el select
        detectionSelect.addEventListener('change', updateSelectedClass);

        function updateSelectedClass() {
            const selectedOption = detectionSelect.options[detectionSelect.selectedIndex];
            if (selectedOption.value && selectedOption.value !== 'Choose one') {
                selectedClassSpan.textContent = selectedOption.text;
            } else {
                selectedClassSpan.textContent = 'None selected';
            }
        }

        // Guardar producto
        saveProduct.addEventListener('click', function() {
            const formData = new FormData(form);
            
            // Obtener el ID de la clase seleccionada del select detection_objects
            const selectedDetectionOption = detectionSelect.options[detectionSelect.selectedIndex];
            let detectionClassId = null;
            
            if (selectedDetectionOption.value && selectedDetectionOption.value !== 'Choose one') {
                // Extraer el ID de la opción seleccionada
                detectionClassId = selectedDetectionOption.value;
            }

            // Validar que se haya ingresado un nombre
            if (!formData.get('name').trim()) {
                alert('Please enter a product name');
                return;
            }

            // Deshabilitar botón mientras se procesa
            saveProduct.disabled = true;
            saveProduct.textContent = 'Saving...';

            // Enviar datos
            fetch('{{ route("products.guarda_ia") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Si se seleccionó una clase de detección, asociarla al producto
                    if (detectionClassId && data.product_id) {
                        return fetch('/products/' + data.product_id + '/associate-ia-class', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                ia_class_id: detectionClassId
                            })
                        });
                    }
                    return Promise.resolve(data);
                }
                throw new Error(data.message || 'Error creating product');
            })
            .then(response => {
                if (response && response.json) {
                    return response.json();
                }
                return response;
            })
            .then(data => {
                alert('Product created successfully!');
                closeModalFunc();
                // Opcional: recargar la página o actualizar la tabla de productos
                // location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating product: ' + error.message);
            })
            .finally(() => {
                saveProduct.disabled = false;
                saveProduct.textContent = 'Save Product';
            });
        });
    });
    </script>
@endisset