<div class="d-flex justify-content-between align-items-center flex-wrap mt-4 mb-6 text-dark">
    <nav>
        <ol class="breadcrumb mb-0">
            @php
                $path = null;
            @endphp
            @foreach(explode('/', trim(parse_url(url()->full())['path'], '/')) as $segment)
            <li class="breadcrumb-item text-dark">
                @php
                    $segment_temp = explode('/', trim($segment, '/'));
                    $path .='/'.$segment;
                @endphp
                <a href="{{$path}}" class="fw-bold" style="color: black !important;" wire:navigatex>
                    {{ urldecode(htmlspecialchars_decode(str_replace('', '', ucfirst(end($segment_temp))))) }}
                </a>
            </li>
            @endforeach
        </ol>
    </nav>
</div>