<style>
	.select2-results__option,
	.select2-selection__choice {
		color: blue;
	}
	.select2-selection__choice__remove {
		color: red;
	}
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"/>
<div class="ia_classes_container">
    <h2>IA classes for {{ $data->name }}</h2>

    {{-- selector múltiple --}}
    <div class="d-flex mb-3 align-items-center">
        <select id="ia-available-select"
                class="form-select w-50"
                multiple>
            @foreach ($ia_available_classes as $item)
                <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
            @endforeach
        </select>
        <button id="add-ia-classes" class="btn btn-primary ms-3">
            Add selected items
        </button>
    </div>

    {{-- tabla de seleccionadas --}}
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th style="width:40px">
                    <input type="checkbox" id="check-all">
                </th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($ia_selected_classes as $row)
            <tr>
                <td class="text-center">
                    <input  type="checkbox"
                            name="delete_ids[]"
                            value="{{ $row['id'] }}">
                </td>
                <td>{{ $row['name'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <button id="delete-selected" class="btn btn-danger mt-2">
        Remove selection
    </button>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
(function initIaClasses(){
    /* ---------- select2 ---------- */
    $('#ia-available-select').select2({ placeholder : 'Select classes to add' });

    /* ---------- check-all ---------- */
    $('#check-all').on('change', function(){
        $('input[name="delete_ids[]"]').prop('checked', this.checked);
    });

    /* ---------- añadir ---------- */
    $('#add-ia-classes').on('click', function(e){
        e.preventDefault();
        const ids = $('#ia-available-select').val() || [];
        if(!ids.length){ alert('Select at least one class'); return; }
        iaAjax('add', ids);
    });

    /* ---------- eliminar ---------- */
    $('#delete-selected').on('click', function(e){
        e.preventDefault();
        const ids = $('input[name="delete_ids[]"]:checked')
                    .map((_,el)=>el.value).get();
        if(!ids.length){ alert('Nothing selected'); return; }
        iaAjax('remove', ids);
    });

    /* ---------- petición AJAX ---------- */
    function iaAjax(action, ids){
        $('.ia_classes_container').css({opacity:.4});          // mini loader
        fetch("{{ route('products.ia-classes.update', $data->id) }}", {
            method : 'POST',
            headers: {
                'Content-Type' : 'application/json',
                'X-CSRF-TOKEN' : '{{ csrf_token() }}'
            },
            body   : JSON.stringify({ action, ids })
        })
        .then(r => r.text())
        .then(html => {
            document.querySelector('.ia_classes_container')
                    .outerHTML = html;      // sustituye el bloque
			initIaClasses();
        })
        .catch(()=>alert('Error, try again'))
        .finally(()=>$('.ia_classes_container').css({opacity:1}));
    }
})();
</script>