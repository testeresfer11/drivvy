<div>
    @if ($error = session('error'))
        <div class="alert alert-danger"
            {{ $error }}
        </div>
    @elseif ($success = session('success'))
        <div
            {{ $success }}
        </div>
    @endif
</div>