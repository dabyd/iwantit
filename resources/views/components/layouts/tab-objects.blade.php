@props( [ 'objects' ] )
@php
	$currentCount = \App\Helpers\TabCounter::incrementAndGet();
@endphp
<style>
	.table-objects .thumbnail {
		width: 60px;
		height: 60px;
	}
	.table-objects .listado {
		position: relative;
	}
	.window-dropdown {
		display: block;
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background: blue;
		padding: 5px 10px;
	}
	#close_window {
		position: absolute;
  		bottom: 0;
  		right: 10px;
	}

	.iwt-main .iwt-admin .table-objects .proyecto_objetos_cell_disabled {
		background-color: var(--palette-fire);
		border-color: var(--palette-fire);
	}

	.wide_td_price {
		width: 300px;
  		height: 200px;
	}

	/* Links para modales */
	.detailed-link, .date-link, .status-link, .price-link {
		color: #007cba;
		text-decoration: underline;
		cursor: pointer;
	}
	.detailed-link:hover, .date-link:hover, .status-link:hover, .price-link:hover {
		color: #005177;
	}

	/* Estilos para la ventana modal */
	.date-modal {
		display: none;
		position: fixed;
		z-index: 1000;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;
		background-color: rgba(0,0,0,0.5);
	}
	.date-modal-content-object,
	.date-modal-content {
		background-color: #fefefe;
		margin: 10% auto;
		padding: 20px;
		border: none;
		border-radius: 8px;
		width: 350px;
		box-shadow: 0 4px 6px rgba(0,0,0,0.1);
	}
	.date-modal-content-object {
		width: 80%;
	}
	.date-modal-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 20px;
		padding-bottom: 10px;
		border-bottom: 1px solid #eee;
	}
	.date-modal-title {
		font-size: 18px;
		font-weight: bold;
		color: #333;
	}
	.close {
		color: #aaa;
		font-size: 28px;
		font-weight: bold;
		cursor: pointer;
		line-height: 1;
	}
	.close:hover {
		color: #000;
	}
	.date-input-group {
		margin-bottom: 20px;
	}
	.date-input-group label {
		display: block;
		margin-bottom: 5px;
		font-weight: bold;
		color: #555;
	}
	#datepicker {
		width: 100%;
		padding: 8px;
		border: 1px solid #ddd;
		border-radius: 4px;
		font-size: 14px;
	}
	.modal-buttons {
		text-align: right;
		padding-top: 15px;
		border-top: 1px solid #eee;
	}
	.btn {
		padding: 8px 16px;
		margin-left: 10px;
		border: none;
		border-radius: 4px;
		cursor: pointer;
		font-size: 14px;
	}
	.btn-primary {
		background-color: #007cba;
		color: white;
	}
	.btn-primary:hover {
		background-color: #005177;
	}
	.btn-secondary {
		background-color: #6c757d;
		color: white;
	}
	.btn-secondary:hover {
		background-color: #545b62;
	}

	/* Estilos específicos para modales de estado y precio */
	.modal-options-group {
		margin-bottom: 20px;
	}
	.modal-options-group .btn {
		margin: 5px;
		display: inline-block;
	}
	.modal-options-group input {
		width: 100%;
		padding: 8px;
		border: 1px solid #ddd;
		border-radius: 4px;
		font-size: 14px;
		margin-top: 10px;
	}
	.modal-options-group input[type="radio"] {
		width: auto;
	}
	.modal-options-group label {
		display: block;
		margin: 10px 0 5px 0;
		font-weight: bold;
		color: #555;
	}

	/* Tabla detalle objeto */
	.iwt-admin .table-objects th:first-child,
	.iwt-admin .table-objects th:last-child,
	.iwt-admin .table-objects td:first-child,
	.iwt-admin .table-objects td:last-child,

	.iwt-admin .table-detail th:first-child,
	.iwt-admin .table-detail th:last-child,
	.iwt-admin .table-detail td:first-child,
	.iwt-admin .table-detail td:last-child  {
		border-radius: 0;
	}
	.iwt-admin .table-detail {
		border-spacing: 0;
  		border-collapse: inherit;
	}
	.thumbnail-object {
		width: 20%;
	}
	.thumbnail-brand {
		width: 20%;
		float: right;
	}
	.images-detail-object {
		margin-bottom: 20px;
	}
</style>
<div class="tab-{{ $currentCount }}">
	<h2>Objects</h2>
	<h3>Total product placement revenue: <span id="total_price"></span></h3>
	<h3>Total product placement revenue enabled: <span id="total_price_enabled"></span></h3>
	<table class="table-objects table table-bordered">
		<tbody>
			<tr>
				<th>ID</th>
				<th>Thumbnail</th>
				<th>Object</th>
				<th>Family</th>
				<th>Brand</th>
				<th>Time in screen</th>
				<th>State</th>
				<th>Price</th>
				<th>Date IN</th>
				<th>Date OUT</th>
			</tr>
			@foreach( $objects as $object )
				<tr class="proyectos_objetos">
					<td class="listado">{{ $object['id'] }}</td>
					<td class="listado"><img class="thumbnail" src="{{ getAbsoluteFileUrl( $object['thumbnail'] ) }}" /></td>
					<td class="listado">
						<a href="#" class="detailed-link" data-window="{{ $object['id'] }}">{{ $object['name'] }}</a>
					</td>
					<td class="listado">{{ $object['family'] }}</td>
					<td class="listado">{{ $object['brand'] }}</td>
					<td class="listado">{{ $object['time'] }}</td>
					<td class="listado">
						<a href="#" class="status-link" data-window="{{ $object['id'] }}">{{ $object['estado'] }}</a>
					</td>
					<td class="listado">
						<a href="#" class="price-link" data-window="{{ $object['id'] }}" data-precio="{{ $object['precio_s'] }}" data-time="{{ $object['segundos'] }}">{{ $object['precio'] }}</a>
					</td>
					<td class="listado">
						<a href="#" class="date-link" data-window="{{ $object['id'] }}" data-type="in">{{ $object['date_in'] }}</a>
					</td>
					<td class="listado">
						<a href="#" class="date-link" data-window="{{ $object['id'] }}" data-type="out">{{ $object['date_out'] }}</a>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>

	@foreach( $objects as $object )
		<div id="detail-window-{{ $object['id'] }}" class="date-modal">
	        <div class="date-modal-content-object">
				<div class="date-modal-header">
	                <div class="date-modal-title">Object detail: {{ $object['name'] }}</div>
    	            <span class="close" data-modal="detail-window-{{ $object['id'] }}">&times;</span>
        	    </div>
            	<div class="modal-options-group">
					<div class="images-detail-object">
						<img class="thumbnail-object" src="{{ getAbsoluteFileUrl( $object['thumbnail'] ) }}" />
						<img class="thumbnail-brand" src="{{ getAbsoluteFileUrl( $object['thumbnail_brand'] ) }}" />
					</div>
					<table class="table-detail table table-bordered">
						<tbody>
							<tr>
								<th>#</th>
								<th>TC IN</th>
								<th>TC OUT</th>
								<th>State</th>
								<th>URL</th>
							</tr>
							@foreach( $object['data'] as $key => $objetos )
								@php
									$tcin  = reset( $objetos['veces'] );
									$tcout = end( $objetos['veces'] );
									$tcin  = formatSecondsToTime( $tcin['time'] );
									$tcout = formatSecondsToTime( $tcout['time'] );
								@endphp
								<tr>
									<td>{{ $key }}</td>
									<td>{{ $tcin }}</td>
									<td>{{ $tcout }}</td>
									<td>-----</td>
									<td>-----</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	@endforeach

	<!-- Agregar este botón después de tu tabla en el blade -->
	<div style="margin-top: 20px; text-align: right;">
		<button 
			type="button" 
			id="update-hotpoints-btn" 
			class="btn btn-primary"
			onclick="updateHotpoints()"
			style="padding: 10px 20px; font-size: 16px;">
			Update Changes
		</button>
		<button 
			type="button" 
			class="btn btn-secondary"
			onclick="countPendingUpdates()"
			style="padding: 10px 20px; font-size: 16px; margin-left: 10px;">
			Count Pending
		</button>
	</div>

	<meta name="csrf-token" content="{{ csrf_token() }}">

	<!-- Modal para selector de estado (Enabled / Disabled) -->
    <div id="stateModal" class="date-modal">
        <div class="date-modal-content">
            <div class="date-modal-header">
                <div class="date-modal-title">Change State</div>
                <span class="close" data-modal="stateModal">&times;</span>
            </div>
            <div class="modal-options-group">
                <div style="margin-bottom: 10px;">
                    <input type="radio" id="statusEnabled" name="status" value="Enabled">
                    <label for="statusEnabled" style="margin-left: 5px; font-weight: normal; display: inline;">Enabled</label>
                </div>
                <div>
                    <input type="radio" id="statusDisabled" name="status" value="Disabled">
                    <label for="statusDisabled" style="margin-left: 5px; font-weight: normal; display: inline;">Disabled</label>
                </div>
            </div>
            <div class="modal-buttons">
                <button type="button" class="btn btn-secondary" data-action="cancel" data-modal="stateModal">Cancel</button>
                <button type="button" class="btn btn-primary" data-action="save" data-modal="stateModal">Save</button>
            </div>
        </div>
    </div>

	<!-- Modal para selector de precio -->
    <div id="priceModal" class="date-modal">
        <div class="date-modal-content">
            <div class="date-modal-header">
                <div class="date-modal-title">Change Price</div>
                <span class="close" data-modal="priceModal">&times;</span>
            </div>
            <div class="modal-options-group">
                <div style="margin-bottom: 8px;">
                    <button type="button" class="btn btn-secondary price-option" data-value="500" style="width: 80px; margin-right: 10px;">Low</button>
                    <span>500</span>
                </div>
                <div style="margin-bottom: 8px;">
                    <button type="button" class="btn btn-secondary price-option" data-value="1000" style="width: 80px; margin-right: 10px;">Medium</button>
                    <span>1000</span>
                </div>
                <div style="margin-bottom: 8px;">
                    <button type="button" class="btn btn-secondary price-option" data-value="1500" style="width: 80px; margin-right: 10px;">High</button>
                    <span>1500</span>
                </div>
                <div style="margin-bottom: 15px;">
                    <button type="button" class="btn btn-secondary price-option" data-value="3000" style="width: 80px; margin-right: 10px;">Extra</button>
                    <span>3000</span>
                </div>
                <label for="price_per_second_input">Price per second:</label>
                <input type="number" id="price_per_second_input" placeholder="Enter custom price" />
            </div>
            <div class="modal-buttons">
                <button type="button" class="btn btn-secondary" data-action="cancel" data-modal="priceModal">Cancel</button>
                <button type="button" class="btn btn-primary" data-action="save" data-modal="priceModal">Save</button>
            </div>
        </div>
    </div>

	<!-- Modal para selector de fecha -->
    <div id="dateModal" class="date-modal">
        <div class="date-modal-content">
            <div class="date-modal-header">
                <div class="date-modal-title">Select Date</div>
                <span class="close" data-modal="dateModal">&times;</span>
            </div>
            <div class="date-input-group">
                <label for="datepicker">Date:</label>
                <input type="text" id="datepicker" placeholder="dd/mm/yyyy">
            </div>
            <div class="modal-buttons">
                <button type="button" class="btn btn-secondary" data-action="cancel" data-modal="dateModal">Cancel</button>
                <button type="button" class="btn btn-primary" data-action="save" data-modal="dateModal">Save</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.14.1/themes/ui-lightness/jquery-ui.min.css">

<script>
	$(document).ready(function() {
		let currentElement = null;
		let currentModalType = null;
		
		// Configure datepicker in English
		$.datepicker.setDefaults({
			dateFormat: 'dd/mm/yy',
			dayNames: ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
			dayNamesMin: ['Su','Mo','Tu','We','Th','Fr','Sa'],
			dayNamesShort: ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
			monthNames: ['January','February','March','April','May','June','July','August','September','October','November','December'],
			monthNamesShort: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
			nextText: 'Next',
			prevText: 'Previous'
		});
		
		// Initialize datepicker
		$('#datepicker').datepicker();

		proyectos_objetos_row();
		
		// Function to convert date dd/mm/yyyy to Date object
		function parseDate(dateStr) {
			if (dateStr === '---' || !dateStr) return null;
			const parts = dateStr.split('/');
			if (parts.length === 3) {
				return new Date(parts[2], parts[1] - 1, parts[0]);
			}
			return null;
		}
		
		// Function to format date to dd/mm/yyyy
		function formatDate(date) {
			const day = String(date.getDate()).padStart(2, '0');
			const month = String(date.getMonth() + 1).padStart(2, '0');
			const year = date.getFullYear();
			return `${day}/${month}/${year}`;
		}

		// Format number with commas
		function formatNumber(num) {
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
		}
		
		// Event listener for date links
		$(document).on('click', '.date-link', function(e) {
			e.preventDefault();
			
			currentElement = $(this);
			currentModalType = 'date';
			const currentDate = currentElement.text();
			const dateType = currentElement.data('type');
			
			let defaultDate;
			
			if (currentDate === '---' || !currentDate) {
				// If no date, use current date or current + 1 year
				const today = new Date();
				if (dateType === 'out') {
					// For OUT date, use current date + 1 year
					defaultDate = new Date(today.getFullYear() + 1, today.getMonth(), today.getDate());
				} else {
					// For IN date, use current date
					defaultDate = today;
				}
			} else {
				// If date exists, use existing date
				defaultDate = parseDate(currentDate);
			}
			
			// Set date in datepicker
			if (defaultDate) {
				$('#datepicker').datepicker('setDate', defaultDate);
			}
			
			// Update modal title
			const typeText = dateType === 'in' ? 'Entry' : 'Exit';
			$('#dateModal .date-modal-title').text(`Select ${typeText} Date`);
			
			// Show modal
			$('#dateModal').show();
		});

		// Event listener for detailed objects	
		$(document).on('click', '.detailed-link', function(e) {
			e.preventDefault();
			
			// Show modal
			$('#detail-window-' + e.target.dataset.window).show();
		});

		// Event listener for status links
		$(document).on('click', '.status-link', function(e) {
			e.preventDefault();
			
			currentElement = $(this);
			currentModalType = 'status';
			const currentStatus = currentElement.text().trim();
			
			// Set the appropriate radio button
			if (currentStatus === 'Enabled') {
				$('#statusEnabled').prop('checked', true);
			} else {
				$('#statusDisabled').prop('checked', true);
			}
			
			// Show modal
			$('#stateModal').show();
		});

		// Event listener for price links
		$(document).on('click', '.price-link', function(e) {
			e.preventDefault();
			
			currentElement = $(this);
			currentModalType = 'price';
			const currentPrice = currentElement.data('precio') || 0;
			
			// Set the price input with current data-precio value
			$('#price_per_second_input').val(currentPrice);
			
			// Reset all price option buttons
			$('.price-option').removeClass('btn-primary').addClass('btn-secondary');
			
			// Highlight the current price option if it matches
			$('.price-option').each(function() {
				if (parseInt($(this).data('value')) === parseInt(currentPrice)) {
					$(this).removeClass('btn-secondary').addClass('btn-primary');
				}
			});
			
			// Show modal
			$('#priceModal').show();
		});

		// Price option buttons
		$(document).on('click', '.price-option', function() {
			const value = $(this).data('value');
			$('#price_per_second_input').val(value);
			
			// Update button styles
			$('.price-option').removeClass('btn-primary').addClass('btn-secondary');
			$(this).removeClass('btn-secondary').addClass('btn-primary');
		});
		
		// Universal modal close functionality
		$(document).on('click', '.close', function() {
			const modalId = $(this).data('modal');
			if (modalId) {
				$('#' + modalId).hide();
			}
			currentElement = null;
			currentModalType = null;
		});
		
		// Universal cancel button functionality
		$(document).on('click', '[data-action="cancel"]', function() {
			const modalId = $(this).data('modal');
			$('#' + modalId).hide();
			currentElement = null;
			currentModalType = null;
		});
		
		// Close modal when clicking outside
		$(window).click(function(event) {
			if (event.target.classList && event.target.classList.contains('date-modal')) {
				$(event.target).hide();
				currentElement = null;
				currentModalType = null;
			}
		});
		
		// Universal save button functionality
		$(document).on('click', '[data-action="save"]', function() {
			const modalId = $(this).data('modal');
			
			if (modalId === 'dateModal' && currentElement && currentModalType === 'date') {
				const selectedDate = $('#datepicker').datepicker('getDate');
				
				if (selectedDate) {
					const formattedDate = formatDate(selectedDate);
					currentElement.text(formattedDate);
					currentElement[0].parentElement.parentElement.dataset.updated = '1';
					
					console.log('Date updated:', {
						window: currentElement.data('window'),
						type: currentElement.data('type'),
						date: formattedDate
					});
				}
			} else if (modalId === 'stateModal' && currentElement && currentModalType === 'status') {
				const selectedStatus = $('input[name="status"]:checked').val();
				if (selectedStatus) {
					currentElement.text(selectedStatus);
					currentElement[0].parentElement.parentElement.dataset.updated = '1';
					
					console.log('Status updated:', {
						window: currentElement.data('window'),
						status: selectedStatus
					});
				}
			} else if (modalId === 'priceModal' && currentElement && currentModalType === 'price') {
				const newPricePerSecond = parseInt($('#price_per_second_input').val()) || 0;
				const timeInSeconds = parseInt(currentElement.data('time')) || 0;
				const totalPrice = newPricePerSecond * timeInSeconds;
				
				// Update data-precio attribute
				currentElement.attr('data-precio', newPricePerSecond);
				
				// Update displayed text
				if (newPricePerSecond === 0) {
					currentElement.text('No price');
				} else {
					currentElement.text(formatNumber(totalPrice));
				}
				
				currentElement[0].parentElement.parentElement.dataset.updated = '1';
				
				console.log('Price updated:', {
					window: currentElement.data('window'),
					pricePerSecond: newPricePerSecond,
					timeInSeconds: timeInSeconds,
					totalPrice: totalPrice
				});
			}
			
			$('#' + modalId).hide();
			currentElement = null;
			currentModalType = null;
			proyectos_objetos_row();
		});
		
		// Handle Enter key in date input
		$('#datepicker').keypress(function(e) {
			if (e.which === 13) { // Enter key
				$('[data-action="save"][data-modal="dateModal"]').click();
			}
		});
	});

	/**
	 * Función para actualizar los hotpoints en la base de datos
	 */
	function updateHotpoints() {
		// Obtener el project_id del elemento #version-id
		const versionIdElement = document.getElementById('version-id');
		if (!versionIdElement) {
			console.error('No se encontró el elemento #version-id');
			alert('Error: No se encontró el ID del proyecto');
			return;
		}
		
		const projectId = versionIdElement.value || versionIdElement.textContent || versionIdElement.innerText;
		if (!projectId) {
			console.error('No se pudo obtener el project_id');
			alert('Error: No se pudo obtener el ID del proyecto');
			return;
		}

		// Buscar todas las filas con data-updated="1" en la tabla .table-objects
		const updatedRows = document.querySelectorAll('.table-objects tr[data-updated="1"]');
		
		if (updatedRows.length === 0) {
			console.info('No hay cambios para actualizar');
			alert('No hay cambios para actualizar');
			return;
		}

		console.log(`Encontradas ${updatedRows.length} filas para actualizar`);
		
		const updates = [];
		let hasErrors = false;

		// Procesar cada fila actualizada
		updatedRows.forEach((row, index) => {
			try {
				// Obtener product_id del primer data-window que encuentre
				const elementWithWindow = row.querySelector('[data-window]');
				if (!elementWithWindow) {
					console.error(`Fila ${index + 1}: No se encontró elemento con data-window`);
					hasErrors = true;
					return;
				}
				const productId = elementWithWindow.getAttribute('data-window');

				// Obtener status del .status-link
				const statusElement = row.querySelector('.status-link');
				if (!statusElement) {
					console.error(`Fila ${index + 1}: No se encontró elemento .status-link`);
					hasErrors = true;
					return;
				}
				const statusText = statusElement.textContent.trim();
				const status = statusText.toLowerCase() === 'enabled';

				// Obtener precio del data-precio
				const priceElement = row.querySelector('[data-precio]');
				if (!priceElement) {
					console.error(`Fila ${index + 1}: No se encontró elemento con data-precio`);
					hasErrors = true;
					return;
				}
				const price = parseFloat(priceElement.getAttribute('data-precio')) || 0;

				// Obtener date_in del elemento con data-type="in"
				const dateInElement = row.querySelector('[data-type="in"]');
				const dateIn = dateInElement ? dateInElement.textContent.trim() : '';

				// Obtener date_out del elemento con data-type="out"
				const dateOutElement = row.querySelector('[data-type="out"]');
				const dateOut = dateOutElement ? dateOutElement.textContent.trim() : '';

				// Añadir a la lista de actualizaciones
				updates.push({
					product_id: parseInt(productId),
					status: status,
					price: price,
					date_in: dateIn === '---' ? '' : dateIn,
					date_out: dateOut === '---' ? '' : dateOut
				});

				console.log(`Fila ${index + 1} procesada:`, {
					product_id: productId,
					status: status,
					price: price,
					date_in: dateIn,
					date_out: dateOut
				});

			} catch (error) {
				console.error(`Error procesando fila ${index + 1}:`, error);
				hasErrors = true;
			}
		});

		if (hasErrors) {
			console.error('Se encontraron errores procesando las filas');
			if (!confirm('Se encontraron errores procesando algunas filas. ¿Desea continuar con las filas válidas?')) {
				return;
			}
		}

		if (updates.length === 0) {
			console.error('No se pudieron procesar las actualizaciones');
			alert('Error: No se pudieron procesar las actualizaciones');
			return;
		}

		// Preparar datos para envío
		const requestData = {
			project_id: parseInt(projectId),
			id: 0,
			updates: updates
		};

		console.log('Datos a enviar:', requestData);

		// Mostrar indicador de carga
		const originalButtonState = showLoadingState();

		// Obtener token CSRF para Laravel
		const csrfToken = document.querySelector('meta[name="csrf-token"]');
		if (!csrfToken) {
			console.error('No se encontró el token CSRF');
			alert('Error: Token de seguridad no encontrado');
			hideLoadingState(originalButtonState);
			return;
		}

		// Realizar petición AJAX
		fetch('/hotpoints/update', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
				'Accept': 'application/json'
			},
			body: JSON.stringify(requestData)
		})
		.then(response => {
			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}
			return response.json();
		})
		.then(data => {
			console.log('Respuesta del servidor:', data);
			
			if (data.success) {
				// Éxito: limpiar las marcas de data-updated
				updatedRows.forEach(row => {
					row.removeAttribute('data-updated');
				});

				// Mostrar mensaje de éxito
				const message = `Update completed:
	- Total processed: ${data.summary.total}
	- Successful: ${data.summary.successful}
	- Errors: ${data.summary.errors}`;

				alert(message);

				// Log detallado de resultados
				if (data.results) {
					console.log('Resultados detallados:', data.results);
				}
				if (data.errors) {
					console.warn('Errores encontrados:', data.errors);
				}

			} else {
				// Error en la respuesta
				console.error('Error en la respuesta:', data);
				alert(`Error: ${data.message || 'Error desconocido'}`);
			}
		})
		.catch(error => {
			console.error('Error en la petición AJAX:', error);
			alert(`Error de conexión: ${error.message}`);
		})
		.finally(() => {
			// Ocultar indicador de carga
			hideLoadingState(originalButtonState);
		});
	}

	/**
	 * Mostrar estado de carga
	 */
	function showLoadingState() {
		// Buscar botón de actualización o crear uno temporal
		const updateButton = document.querySelector('#update-hotpoints-btn');
		if (updateButton) {
			const originalText = updateButton.textContent;
			updateButton.textContent = 'Updating...';
			updateButton.disabled = true;
			return { button: updateButton, originalText: originalText };
		}
		
		// Si no hay botón, mostrar en consola
		console.log('Actualizando hotpoints...');
		return null;
	}

	/**
	 * Ocultar estado de carga
	 */
	function hideLoadingState(originalState) {
		if (originalState && originalState.button) {
			originalState.button.textContent = originalState.originalText;
			originalState.button.disabled = false;
		}
	}

	/**
	 * Función de utilidad para contar filas pendientes de actualización
	 */
	function countPendingUpdates() {
		const updatedRows = document.querySelectorAll('.table-objects tr[data-updated="1"]');
		console.log(`Filas pendientes de actualización: ${updatedRows.length}`);
		return updatedRows.length;
	}

	/**
	 * Función de utilidad para marcar una fila como actualizada (para testing)
	 */
	function markRowAsUpdated(productId) {
		const row = document.querySelector(`tr:has([data-window="${productId}"])`);
		if (row) {
			row.setAttribute('data-updated', '1');
			console.log(`Fila con product_id ${productId} marcada como actualizada`);
		} else {
			console.error(`No se encontró fila con product_id ${productId}`);
		}
	}

	// Exponer funciones globalmente para poder ser llamadas desde la consola o botones
	window.updateHotpoints = updateHotpoints;
	window.countPendingUpdates = countPendingUpdates;
	window.markRowAsUpdated = markRowAsUpdated;
</script>