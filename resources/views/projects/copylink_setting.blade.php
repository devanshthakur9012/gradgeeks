@php
    $password = base64_decode($project->password);
@endphp
{{ Form::open(['route' => ['projects.copy.link', $projectID, $slug], 'method' => 'POST']) }}
<div class="modal-body">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="thead-light">
                <tr>
                    <th> {{ __('Name') }}</th>
                    <th class="text-right"> {{ __('On/Off') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ __('Basic details') }}</td>
                    <td class="action text-right">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="basic_details" class="form-check-input"
                                @if (isset($result->basic_details) && $result->basic_details == 'on') checked="checked" @endif id="copy_link_1"
                                value="on">
                            <label class="custom-control-label" for="copy_link_1"></label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ __('Member') }}</td>
                    <td class="action text-right">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="member" class="form-check-input"
                                @if (isset($result->member) && $result->member == 'on') checked="checked" @endif id="copy_link_2"
                                value="on">
                            <label class="custom-control-label" for="copy_link_2"></label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ __('Client') }}</td>
                    <td class="action text-right">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="client" class="form-check-input"
                                @if (isset($result->client) && $result->client == 'on') checked="checked" @endif id="copy_link_21"
                                value="on">
                            <label class="custom-control-label" for="copy_link_21"></label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ __('Milestone') }}</td>
                    <td class="action text-right">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="milestone" class="form-check-input"
                                @if (isset($result->milestone) && $result->milestone == 'on') checked="checked" @endif id="copy_link_3"
                                value="on">
                            <label class="custom-control-label" for="copy_link_3"></label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ __('Activity') }}</td>
                    <td class="action text-right">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="activity" class="form-check-input"
                                @if (isset($result->activity) && $result->activity == 'on') checked="checked" @endif id="copy_link_4"
                                value="on">
                            <label class="custom-control-label" for="copy_link_4"></label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ __('Files') }}</td>
                    <td class="action text-right">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="attachment" class="form-check-input"
                                @if (isset($result->attachment) && $result->attachment == 'on') checked="checked" @endif id="copy_link_5"
                                value="on">
                            <label class="custom-control-label" for="copy_link_5"></label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ __('Bug Report') }}</td>
                    <td class="action text-right">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="bug_report" class="form-check-input"
                                @if (isset($result->bug_report) && $result->bug_report == 'on') checked="checked" @endif id="copy_link_6"
                                value="on">
                            <label class="custom-control-label" for="copy_link_6"></label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ __('Task') }}</td>
                    <td class="action text-right">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="task" class="form-check-input" id="copy_link_7"
                                @if (isset($result->task) && $result->task == 'on') checked="checked" @endif value="on">
                            <label class="custom-control-label" for="copy_link_7"></label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ __('Tracker details') }}</td>
                    <td class="action text-right">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="tracker_details" class="form-check-input"
                                @if (isset($result->tracker_details) && $result->tracker_details == 'on') checked="checked" @endif id="copy_link_8"
                                value="on">
                            <label class="custom-control-label" for="copy_link_8"></label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ __('Timesheet') }}</td>
                    <td class="action text-right">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="timesheet" class="form-check-input"
                                @if (isset($result->timesheet) && $result->timesheet == 'on') checked="checked" @endif id="copy_link_9"
                                value="on">
                            <label class="custom-control-label" for="copy_link_9"></label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ __('Progress') }}</td>
                    <td class="action text-right">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="progress" class="form-check-input"
                                @if (isset($result->progress) && $result->progress == 'on') checked="checked" @endif id="copy_link_10"
                                value="on">
                            <label class="custom-control-label" for="copy_link_10"></label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>{{ __('Password Protected') }}</td>
                    <td class="action text-right">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="password_protected"
                                class="form-check-input password_protect" id="password_protected"
                                @if (isset($result->password_protected) && $result->password_protected == 'on') checked="checked" @endif value="on">
                            <label class="custom-control-label" for="password_protected"></label>
                        </div>
                    </td>
                <tr class="passwords">
                    <td colspan="2">
                        {{-- @php
                            if((isset($request->password)) && (Hash::check($project->password, $project->password))){

                                $password = \Illuminate\Support\Facades\Crypt::decrypt($project->password)
                            }
                            @endphp --}}

                        <div class="action input-group input-group-merge  text-left ">
                            <input type="password" value="{{ $password }}"
                                class=" form-control @error('password') is-invalid @enderror" name="password"
                                autocomplete="new-password" id="password"
                                placeholder="{{ __('Enter Your Password') }}">
                            <div class="input-group-append">
                                <span class="input-group-text py-3">
                                    <a href="#" data-toggle="password-text" data-target="#password">
                                        <i class="fas fa-eye-slash" id="togglePassword"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="modal-footer">
    <div class="row col-12 m-0">
        <div class="text-start col-6">
            <a id="cp_link" href="#" class="btn btn-primary cp_link "
                data-link="{{ route('projects.link', [$currentWorkspace->slug, \Illuminate\Support\Facades\Crypt::encrypt($project->id)]) }}"
                data-toggle="tooltip" title="Copy Project Link" {{-- data-original-title="{{ __('Click to copy project link') }}" --}}><span
                    class=""></span><span class="btn-inner--text text-white"><i
                        class="ti ti-copy"></i></span></a>
            </a>
        </div>
        <div class="text-end  col-6">
            {{ Form::button(__('Cancel'), ['type' => 'button', 'class' => 'btn btn-secondary', 'data-bs-dismiss' => 'modal']) }}
            {{ Form::button(__('Save'), ['type' => 'submit', 'class' => 'btn btn-primary']) }}
        </div>
    </div>
</div>
{{ Form::close() }}

<script>
    $(document).ready(function() {
        if ($('.password_protect').is(':checked')) {
            $('.passwords').show();
        } else {
            $('.passwords').hide();
        }
        $('#password_protected').on('change', function() {
            if ($('.password_protect').is(':checked')) {
                $('.passwords').show();
            } else {
                $('.passwords').hide();
            }
        });
    });
    $(document).on('change', '#password_protected', function() {
        if ($(this).is(':checked')) {
            $('.passwords').removeClass('password_protect');
            $('.passwords').attr("required", true);
        } else {
            $('.passwords').addClass('password_protect');
            $('.passwords').val(null);
            $('.passwords').removeAttr("required");
        }
    });
</script>
<script>
    const togglePassword = document.querySelector("#togglePassword");
    const password = document.querySelector("#password");

    togglePassword.addEventListener("click", function() {
        // toggle the type attribute
        const type = password.getAttribute("type") === "password" ? "text" : "password";
        password.setAttribute("type", type);

        // toggle the icon
        // $('#togglePassword').removeClass('fa-eye-slash');
        // $('#togglePassword').removeClass('fa-eye-slash');
        this.classList.toggle("fa-eye");
        this.classList.toggle("fa-eye-slash");
    });

    // prevent form submit
    // const form = document.querySelector("form");
    // form.addEventListener('submit', function (e) {
    //     e.preventDefault();
    // });
</script>
<script>
    $('.cp_link').on('click', function() {
        var value = $(this).attr('data-link');
        var $temp = $("<input>");

        $("#cp_link").append($temp);
        $temp.val(value).select();
        document.execCommand("copy");
        $temp.remove();
        show_toastr('Success', '{{ __('Link Copy on Clipboard') }}', 'success')
    });
</script>
