<form class="needs-validation" novalidate method="post"
    action="{{ route('zoom-meeting.store', $currentWorkspace->slug) }}">
    @csrf
    <div class="modal-body">
        <div class="row">
            @if ($currentWorkspace->is_chagpt_enable())
                <div class="text-end col-12">
                    <a href="#" data-size="lg" data-ajax-popup-over="true" class="btn btn-sm btn-primary"
                        data-url="{{ route('generate', ['zoom meeting']) }}" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="{{ __('Generate with AI') }}"
                        data-title="{{ __('Generate Zoom Meeting Topic') }}">
                        <i class="fas fa-robot px-1"></i>{{ __('Generate with AI') }}
                    </a>
                </div>
            @endif
            <div class="form-group col-md-12">
                {{ Form::label('title', __('Topic'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => __('Enter Meeting Title'), 'required' => 'required']) }}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('projects', __('Projects'), ['class' => 'col-form-label']) }}
                {{-- {{ Form::select('project_id', $projects, null, ['class' => 'form-control ', 'id' => 'project_id', 'data-toggle' => 'select']) }} --}}
                {{ Form::select('project_id', $projects, null, ['placeholder' => 'Select Project', 'id' => 'project_id', 'data-toggle' => 'select', 'class' => 'form-control', 'tabindex' => '2']) }}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('users', __('Members'), ['class' => 'col-form-label']) }}
                <div id="members-div">
                    {{ Form::select('members[]', [], null, ['class' => 'form-control multi-select', 'placeholder' => __('Select Members'), 'id' => 'members', 'data-toggle' => 'select', 'multiple' => 'multiple']) }}
                </div>
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('datetime', __('Start Date / Time'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::text('start_date', null, ['class' => 'form-control date', 'placeholder' => __('Select Date/Time'), 'required' => 'required']) }}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('duration', __('Duration'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::number('duration', null, ['class' => 'form-control', 'placeholder' => __('Enter Duration'), 'required' => 'required']) }}
            </div>

            <div class="form-group col-md-6">
                {{ Form::label('password', __('Password'), ['class' => 'col-form-label']) }}
                {{ Form::password('password', ['class' => 'form-control', 'placeholder' => __('Enter Password')]) }}
            </div>
            @if ($currentWorkspace->is_googlecalendar_enabled == 'on')
                <div class="form-group col-md-6">
                    {{ Form::label('synchronize_type', __('Synchroniz in Google Calendar ?'), ['class' => 'col-form-label']) }}
                    <div class=" form-switch">
                        <input type="checkbox" class="form-check-input mt-2" name="synchronize_type" id="switch-shadow"
                            value="google_calender">
                        <label class="form-check-label" for="switch-shadow"></label>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class=" modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
    </div>

</form>
<link rel="stylesheet" href="{{ asset('assets/custom/libs/bootstrap-daterangepicker/daterangepicker.css') }}">
<script src="{{ asset('assets/custom/libs/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<style>
    .applyBtn .rounded-pill {
        background: #584ed2 !important;
        color: #fff !important;
    }
</style>
<script>
    $(document).ready(function() {
        var workspace_id = "{{ $currentWorkspace->id }}";

        function getMembers(project_id) {

            $("#members-div").html('');
            $('#members-div').append(
                '<select class="form-control" id="members" name="members[]" multiple></select>');

            $.get("{{ route('projects.members', ['workspace_id ', 'project_id']) }}".replace('workspace_id',
                workspace_id).replace('project_id', project_id), function(data) {
                var list = '';
                $('.js-data-example-ajax').empty();
                if (data.length > 0) {
                    list += "<option value=''> {{ __('Select Project') }}</option>";
                } else {
                    list += "<option value=''> {{ __('No Projects') }} </option>";
                }

                $.each(data, function(i, item) {
                    list += "<option value='" + item.id + "'>" + item.name + "</option>"
                });
                $('#members').html(list);
                var multipleCancelButton = new Choices(
                    '#members', {
                        removeItemButton: true,

                    }
                );

                // $('#members').Choices();
            });
        }

        $("#project_id").change(function() {
            var project_id = $(this).val();
            getMembers(project_id);
        });
    });

    $('.date').daterangepicker({
        "singleDatePicker": true,
        "timePicker": true,
        "locale": {
            "format": 'MM/DD/YYYY H:mm'
        },
        "timePicker24Hour": true,
    }, function(start, end, label) {
        // console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    });
    // getMembers($('#project_id').val());
</script>
