<x-layouts.table 
	:controller="$controller" 
	:datas="$items"
    :canCreate="false"
    :canDelete="false"
/>
<!--
<div class="container">
    <h1>{{ __('datision.titles.index') }}</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a class="btn btn-primary mb-3" href="{{ route('datision-parameters.create') }}">
        {{ __('datision.actions.create') }}
    </a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('datision.fields.machine_url') }}</th>
                <th>{{ __('datision.fields.threshold_sec') }}</th>
                <th>{{ __('datision.labels.created_at') }}</th>
                <th>{{ __('datision.labels.actions') }}</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($items as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td><a href="{{ $item->machine_url }}" target="_blank" rel="noopener">{{ $item->machine_url }}</a></td>
                <td>{{ $item->threshold_sec }}</td>
                <td>{{ $item->created_at }}</td>
                <td class="d-flex gap-2">
                    <a class="btn btn-sm btn-secondary" href="{{ route('datision-parameters.edit', $item) }}">{{ __('datision.actions.edit') }}</a>
                    <a class="btn btn-sm btn-outline-info" href="{{ route('datision-parameters.show', $item) }}">{{ __('datision.actions.show') }}</a>
                    <form method="POST" action="{{ route('datision-parameters.destroy', $item) }}" onsubmit="return confirm('{{ __('datision.confirm.delete') }}')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" type="submit">{{ __('datision.actions.delete') }}</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">{{ __('datision.messages.empty') }}</td></tr>
        @endforelse
        </tbody>
    </table>

    {{ $items->links() }}
</div>
-->