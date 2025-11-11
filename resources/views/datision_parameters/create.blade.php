<div class="container">
    <h1>{{ __('datision.titles.create') }}</h1>

    <form method="POST" action="{{ route('datision-parameters.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">{{ __('datision.fields.machine_url') }}</label>
            <input type="url" name="machine_url" class="form-control @error('machine_url') is-invalid @enderror" value="{{ old('machine_url') }}" placeholder="http://13.48.27.24:5018/">
            @error('machine_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">{{ __('datision.fields.threshold_sec') }}</label>
            <input type="number" name="threshold_sec" class="form-control @error('threshold_sec') is-invalid @enderror" value="{{ old('threshold_sec', 2) }}" min="0">
            @error('threshold_sec') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button class="btn btn-primary">{{ __('datision.actions.save') }}</button>
        <a class="btn btn-link" href="{{ route('datision-parameters.index') }}">{{ __('datision.actions.cancel') }}</a>
    </form>
</div>