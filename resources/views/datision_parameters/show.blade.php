<div class="container">
    <h1>{{ __('datision.titles.show') }}</h1>

    <dl class="row">
        <dt class="col-sm-3">{{ __('datision.fields.machine_url') }}</dt>
        <dd class="col-sm-9"><a href="{{ $item->machine_url }}" target="_blank" rel="noopener">{{ $item->machine_url }}</a></dd>

        <dt class="col-sm-3">{{ __('datision.fields.threshold_sec') }}</dt>
        <dd class="col-sm-9">{{ $item->threshold_sec }}</dd>
    </dl>

    <a class="btn btn-secondary" href="{{ route('datision-parameters.edit', $item) }}">{{ __('datision.actions.edit') }}</a>
    <a class="btn btn-link" href="{{ route('datision-parameters.index') }}">{{ __('datision.actions.back') }}</a>
</div>