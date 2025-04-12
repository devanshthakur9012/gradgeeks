<div class="favorite-list-item">
    @php
        $logo = \App\Models\Utility::get_file(config('chatify.user_avatar.folder'));
        $avatar = \App\Models\Utility::get_file('/avatars/');
    @endphp
    @if (!empty($user->avatar))
        {{-- <div data-id="{{ $user->id }}" data-action="0" class="avatar av-m"
        style="background-image : url('{{$logo.'/'.$user->avatar}}');">
    </div> --}}
        <div data-id="{{ $user->id }}" data-action="0" class="avatar av-m"
            style="background-image : url('{{ $avatar . $user->avatar }}');">
        </div>
    @else
        <div data-id="{{ $user->id }}" data-action="0" class="avatar av-m"
            style="background-image: url('{{ $logo . '/avatar.png' }}') !important;">
        </div>
    @endif
    <p>{{ strlen($user->name) > 5 ? substr($user->name, 0, 6) . '..' : $user->name }}</p>
</div>
