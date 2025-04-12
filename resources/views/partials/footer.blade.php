@php
    use App\Models\Utility;
@endphp
<footer class="dash-footer">
    <div class="footer-wrapper">
        <div>
            <span class="text-muted" style="text-align: left;">
                &copy; {{ date('Y') }}
                {{ Utility::getValByName('footer_text') ? Utility::getValByName('footer_text') : config('app.name', 'Taskly') }}
            </span>
        </div>
    </div>
</footer>
