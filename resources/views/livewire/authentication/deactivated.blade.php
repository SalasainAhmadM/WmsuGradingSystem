<main>
    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center">
    <div class="login-page p-4">
        <p class="text-center">
        <img src="{{ asset('image/wmsu_logo.png')}}" alt="wmsu-logo" class="img-fluid" width="175px" height="175px">
        </p>
        <div class="d-flex justify-content-center text-white">
            <h3>Account Deactivated!</h3>
        </div>
        <div id="emailHelp" class="form-text d-flex justify-content-center">
            <a href="/logout" wire:navigate>Logout</a>
        </div>
    </div>
    </div>
</main>