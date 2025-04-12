{{ Form::model($webhook, ['route' => ['webhook.update', [$currentWorkspace->slug, $webhook->id]], 'method' => 'PUT', 'class' => 'py-3 px-3 needs-validation', 'novalidate']) }}
<div class="form-group">
    {{ Form::label('module', __('Module'), ['class' => 'form-label']) }}<x-required></x-required>
    {{ Form::select('module', $module, null, ['class' => 'form-control select', 'id' => 'module', 'required' => 'required']) }}
</div>
<div class="form-group">
    {{ Form::label('url', __('Url'), ['class' => 'form-label']) }}<x-required></x-required>
    {{ Form::text('url', null, ['class' => 'form-control', 'placeholder' => __('Enter Webhook Url'), 'required' => 'required']) }}
</div>
<div class="form-group">
    {{ Form::label('method', __('Method'), ['class' => 'form-label']) }}<x-required></x-required>
    {{ Form::select('method', $method, null, ['class' => 'form-control select', 'id' => 'method', 'required' => 'required']) }}
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="submit" class="btn  btn-primary">{{ __('Update') }}</button>
</div>
{{ Form::close() }}
