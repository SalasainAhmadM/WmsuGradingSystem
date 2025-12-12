<main>
    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center">
        <div class="login-page p-4">
            <p class="text-center">
            <img src="{{ asset('image/wmsu_logo.png')}}" alt="wmsu-logo" class="img-fluid" width="175px" height="175px">
            </p>
            <h1 class="fs-1 fw-bold my-3 mb-4 text-white text-center brand-color">MyWMSU</h1>
            <form wire:submit.prevent="login()">
                <div class="row mb-2 mx-1">
                    <label for="email" class="form-label text-white">Email</label>
                    <div class="input-group d-flex">
                        <input type="email" wire:model="detail.email" id="email" placeholder="Email"class="form-control" >
                    </div>
                    @error('detail.email') <span class="text-white">{{ $message }}</span> @enderror
                </div>
                <div class="row mb-2 mx-1">
                    <label for="password" class="form-label text-white">Password</label>
                    <div class="input-group d-flex">
                        <input 
                            type="password" 
                            wire:model="detail.password" 
                            id="password" 
                            class="form-control" 
                            placeholder="Password"
                        >
                        <button 
                            class="" 
                            type="button" 
                            id="togglePassword"
                        >
                        <svg viewBox="0 0 24 24" width="20px" class="m-2" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M15.0007 12C15.0007 13.6569 13.6576 15 12.0007 15C10.3439 15 9.00073 13.6569 9.00073 12C9.00073 10.3431 10.3439 9 12.0007 9C13.6576 9 15.0007 10.3431 15.0007 12Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M12.0012 5C7.52354 5 3.73326 7.94288 2.45898 12C3.73324 16.0571 7.52354 19 12.0012 19C16.4788 19 20.2691 16.0571 21.5434 12C20.2691 7.94291 16.4788 5 12.0012 5Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                        </button>
                    </div>
                    @error('detail.password') 
                        <span class="text-white">{{ $message }}</span> 
                    @enderror
                </div>
                <div class="row mb-2 mx-1">
                    <div class="input-group">
                        <a href="/forgot-password" wire:navigate class="form-text text-white" name="login" id="login">Forgot your password?</a>
                    </div>
                </div>
                <div class="row mb-2 mx-1">
                    <div class="input-group">
                        <button type="submit" name="login" class="btn d-flex p-2 p-sm-3 justify-content-center">LOGIN</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('togglePassword');

            const hideText = `
                <svg viewBox="0 0 24 24" width="20px" class="m-2" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.99902 3L20.999 21M9.8433 9.91364C9.32066 10.4536 8.99902 11.1892 8.99902 12C8.99902 13.6569 10.3422 15 11.999 15C12.8215 15 13.5667 14.669 14.1086 14.133M6.49902 6.64715C4.59972 7.90034 3.15305 9.78394 2.45703 12C3.73128 16.0571 7.52159 19 11.9992 19C13.9881 19 15.8414 18.4194 17.3988 17.4184M10.999 5.04939C11.328 5.01673 11.6617 5 11.9992 5C16.4769 5 20.2672 7.94291 21.5414 12C21.2607 12.894 20.8577 13.7338 20.3522 14.5" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>`;

            const eyeIcon = `<svg viewBox="0 0 24 24" width="20px" class="m-2" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M15.0007 12C15.0007 13.6569 13.6576 15 12.0007 15C10.3439 15 9.00073 13.6569 9.00073 12C9.00073 10.3431 10.3439 9 12.0007 9C13.6576 9 15.0007 10.3431 15.0007 12Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M12.0012 5C7.52354 5 3.73326 7.94288 2.45898 12C3.73324 16.0571 7.52354 19 12.0012 19C16.4788 19 20.2691 16.0571 21.5434 12C20.2691 7.94291 16.4788 5 12.0012 5Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>`;

            toggleButton.innerHTML = eyeIcon;

            toggleButton.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Toggle innerHTML
                toggleButton.innerHTML = (type === 'password') ? eyeIcon : hideText;
            });
        });
    </script>
</main>