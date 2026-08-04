<!DOCTYPE html>
<html lang="en">

@include('Layouts.Partials.head')

<body>
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <div id="main-wrapper">

        @include('Layouts.Partials.header')
        
        @include('Layouts.Partials.sidebar')

        <div class="content-body">
            @yield('content')
        </div>
        @include('Layouts.Partials.footer')

    </div>

    @include('Layouts.Partials.script')
    
</body>
</html>