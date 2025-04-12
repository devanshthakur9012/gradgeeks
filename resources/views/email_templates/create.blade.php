{{ Form::open(['url' => 'email_template', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="form-group col-md-12">
        {{ Form::label('name', __('Name')) }}<x-required></x-required>
        {{ Form::text('name', null, ['class' => 'form-control ', 'required' => 'required']) }}
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
    {{ Form::submit(__('Create'), ['class' => 'btn btn-primary']) }}
</div>
{{ Form::close() }}
