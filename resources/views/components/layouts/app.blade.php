<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" >
    <head>
        <style>
            #backToTop {
                z-index: 99;
                position: fixed;
                bottom: 20px;
                right: 20px;
                background-color: #2c0268;
                color: white;
                border: none;
                border-radius: 50%;
                padding: 10px;
                font-size: 18px;
                width: 50px;
                height: 50px;
                cursor: pointer;
                display: none;
            }
            #backToTop:hover {
                background-color: #00bad1;
            }
            .form-loader {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                backdrop-filter: blur(3px);
                -webkit-backdrop-filter: blur(25px) saturate(180%);
                width: 100%;
                height: 100%;
                color: #fff;
                text-align: center;
                margin: 0 auto;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
                background: rgba(0, 0, 0, .5);
                z-index: 20;
            }
        </style>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Dashboard' }} | GRADING SYSTEM </title>

        <link rel="icon" href="{{asset('image/wmsu_logo.webp')}}">
        <link rel="stylesheet" href="{{asset('css/style.css')}}">

        <!-- boxicon -->
        <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

        <!-- bootstrap-5 -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <link rel="stylesheet" href="{{ asset('bootstrap/bootstrap-5.0.2/css/bootstrap.min.css')}}">
        <script src="{{ asset('bootstrap/bootstrap-5.0.2/js/bootstrap.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="{{ asset('jquery/jquery-3.7.1/jquery.min.js')}}" ></script>
        @livewireStyles
    </head>
    <body class="login" >
        {{ $slot }}
        <button id="backToTop" class="back-to-top">
            <i class="ti ti-arrow-up "></i>
        </button>
        <script>
            let backToTopButton = document.getElementById("backToTop");
            window.onscroll = function() {
                if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
                    backToTopButton.style.display = "block";
                } else {
                    backToTopButton.style.display = "none";
                }
            };
            backToTopButton.onclick = function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            };
        </script>
        @livewireScripts
        @stack('footer-scripts')
    </body>
</html>
