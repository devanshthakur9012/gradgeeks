 <div class="modal-body">
     <div class="row">
         <div class="col-md-12 col-12 text-center">
             <span class="invite-warning"></span>
         </div>
         <div class="col-md-12 col-12 form-group invite_user_div">
             {{ Form::label('username', __('Name'), ['class' => 'col-form-label']) }}<x-required></x-required>
             {{ Form::text('username', null, ['class' => 'form-control', 'placeholder' => __('Please enter name')]) }}
         </div>
         <div class="col-md-12 form-group">
             {{ Form::label('invite_email', __('Email'), ['class' => 'col-form-label']) }}<x-required></x-required>
             {{ Form::text('invite_email', null, ['class' => 'form-control', 'placeholder' => __('Enter email address')]) }}
         </div>
         <div class="col-md-5 mb-3 invite_user_div">
             <label for="password_switch">{{ __('Login is enable') }}</label>
             <div class="form-check form-switch custom-switch-v1 float-end">
                 <input type="checkbox" name="password_switch" class="form-check-input input-primary pointer"
                     value="on" id="password_switch">
                 <label class="form-check-label" for="password_switch"></label>
             </div>
         </div>
         <div class="col-md-12 form-group ps_div d-none">
             <label for="userpassword" class="form-label">{{ __('Password') }}</label><x-required></x-required>
             <input class="form-control" name="userpassword" type="password" autocomplete="new-password"
                 id="userpassword" placeholder="{{ __('Please enter password') }}">
         </div>
     </div>
 </div>
 <div class="modal-footer">
    <button type="button" class="btn  btn-secondary " data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="button" class="btn  btn-primary check-invite-members">{{ __('Invite') }}</button>
</div>

 <script type="text/javascript">
     $(document).ready(function() {
         $(document).on('change', '#password_switch', function() {
             if ($(this).is(':checked')) {
                 $('.ps_div').removeClass('d-none');
                 $('#password').attr("required", true);
             } else {
                 $('.ps_div').addClass('d-none');
                 $('#password').val(null);
                 $('#password').removeAttr("required");
             }
         });
     });
     $(function() {

         $('.check-invite-members').on('click', function(e) {

             var ele = $(this);
             var emailele = $('#invite_email');

             var email = emailele.val();

             $('.email-error-message').remove();
             if (email == '') {
                 emailele.focus().after(
                     '<span class="email-error-message error-message">{{ __('This field is required.') }}</span>'
                 );
                 return false;
             }

             if (!isEmail(email)) {
                 emailele.focus().after(
                     '<span class="email-error-message error-message">{{ __('Please enter valid email address.') }}</span>'
                 );
                 return false;

             } else {

                 $.ajax({
                     url: '{{ route('user.exists', '__slug') }}'.replace('__slug',
                         '{{ $currentWorkspace->slug }}'),
                     dataType: 'json',
                     data: {
                         'email': email
                     },
                     //  success: function(data) {

                     //      if (data.code == '202') {
                     //          $('#commonModel').modal('hide');
                     //          show_toastr(data.status, data.success, 'success');
                     //      } else if (data.code == '200') {
                     //          $('#commonModel').modal('hide');
                     //          show_toastr(data.status, data.success, 'success');
                     //          location.reload();
                     //      } else if (data.code == '404') {
                     //          $('.invite_user_div').show();
                     //          $('.invite-warning').text(data.error).show();
                     //      }
                     //      ele.removeClass('check-invite-members').off('click').addClass(
                     //          'invite-members');
                     //  }
                     success: function(data) {
                         if (data.code === 200) {
                             show_toastr(data.status, data.message, 'success');
                             $("#commonModal").modal('hide');
                             setTimeout(function() {
                                 if (data.url) {
                                     location.href = data.url;
                                 } else {
                                     location.reload();
                                 }
                             }, 1000);
                         } else if (data.code === 202) {
                             show_toastr(data.status, data.message, 'error');
                             $("#commonModal").modal('hide');
                         } else if (data.code === 404) {
                             $('.invite_user_div').show();
                             $('.invite-warning').text(data.error).show();
                         }
                         ele.removeClass('check-invite-members').off('click').addClass(
                             'invite-members');
                     }
                 });
             }
         });

         $(document).on('click', '.invite-members', function() {

             var useremail = $('#invite_email').val();
             var username = $('#username').val();
             if ($('#password_switch').is(':checked')) {
                 var password_switch = $('#password_switch').val();
             }
             if (!$('.ps_div').hasClass('d-none')) {
                 var userpassword = $('#userpassword').val();
             } else {
                 var userpassword = null;
             }

             $('.username-error-message').remove();
             if (username == '') {
                 $('#username').focus().after(
                     '<span class="username-error-message error-message">{{ __('This field is required.') }}</span>'
                 );
                 return false;
             }

             $('.userpassword-error-message').remove();
             if (userpassword == '') {
                 $('#userpassword').focus().after(
                     '<span class="userpassword-error-message error-message">{{ __('This field is required.') }}</span>'
                 );
                 return false;
             }

             $('.email-error-message').remove();
             if (useremail == '') {
                 $('#invite_email').focus().after(
                     '<span class="email-error-message error-message">{{ __('This field is required.') }}</span>'
                 );
                 return false;
             }

             if (!isEmail(useremail)) {

                 $('#invite_email').focus().after(
                     '<span class="email-error-message error-message">{{ __('Please enter valid email address.') }}</span>'
                 );
                 return false;

             } else {

                 $.ajax({
                     url: '{{ route('users.invite.update', '__slug') }}'.replace('__slug',
                         '{{ $currentWorkspace->slug }}'),
                     method: 'POST',
                     dataType: 'json',
                     data: {
                         'useremail': useremail,
                         'username': username,
                         'userpassword': userpassword,
                         'password_switch': password_switch,
                     },
                     success: function(data) {
                        //  console.log(data);
                         $('#commonModel').modal('hide');
                         if (data.code == '200') {
                             show_toastr(data.status, data.success, 'success');
                             location.reload();
                         } else {

                             show_toastr(data.status, data.error, 'error');
                         }
                     }
                 });
             }
         });

     });
 </script>
