{{ Form::open(['route' => ['client.import'], 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12 mb-6">
            {{ Form::label('file', __('Download sample Client CSV file'), ['class' => 'form-control-label']) }}
            <a href="{{ asset(Storage::url('uploads/sample')) . '/sample_clients_.csv' }}"
                class="btn btn-xs btn-primary btn-icon-only width-auto">
                <i class="ti ti-download"></i> {{ __('Download') }}
            </a>
        </div>
        <div class="col-md-12">
            {{ Form::label('file', __('Select CSV File'), ['class' => 'form-control-label']) }}<x-required></x-required>
            <div class="choose-file  mt-3">
                <label for="file" class="">
                    <div class=" bg-primary"> <i class="ti ti-upload px-1"></i>{{ __('Choose file here') }}</div>
                    <input type="file" class="form-control choose_file_custom" name="file" id="file"
                        data-filename="upload_file" required>
                </label>
                <p class="upload_file"></p>
            </div>
        </div>
        <input type="hidden" value="{{ $slug }}" name="slug">
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Upload') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}
