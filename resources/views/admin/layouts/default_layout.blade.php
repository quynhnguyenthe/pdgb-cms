<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{--CSRF Token--}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>
    <link rel="icon" type="image/x-icon" href="{{asset('images/favicon.png')}}">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://benalman.com/code/projects/jquery-throttle-debounce/jquery.ba-throttle-debounce.js"></script>
    {{--Styles css common--}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css" integrity="sha512-SbiR/eusphKoMVVXysTKG/7VseWii+Y3FdHrt0EpKgpToZeemhqHeZeLWLhJutz/2ut2Vw1uQEj2MbRF+TVBUA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @yield('style-libraries')
    <link rel="stylesheet" href="{{asset('css/main.css?v=2')}}">
    <link rel="stylesheet" href="{{asset('css/template.css?v=2')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    {{--Styles custom--}}
    @yield('styles')
</head>
<body>
<div class="wrapper d-flex flex-row">
    <!-- sidebar -->
    @include('admin.partial.sidebar')
    <section class="right-main">
        @include('admin.partial.header')
        @yield('content')
    </section>
</div>
@include('admin.partial.footer')

{{--Scripts js common--}}
{{--Scripts link to file or js custom--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js" integrity="sha512-WFN04846sdKMIP5LKNphMaWzU7YpMyCU245etK3g/2ARYbPK9Ub18eG+ljU96qKRCWh+quCY7yefSmlkQw1ANQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{asset('js/main.js')}}"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).ajaxComplete(function (event, xhr) {
        if (xhr.status == 401 || xhr.status == 419) {
            window.location.reload()
        }
    });
    $('input[type="text"]').change(function(){
        this.value = $.trim(this.value);
    });
    $('textarea').change(function(){
        this.value = $.trim(this.value);
    });
</script>
@yield('scripts')
</body>
</html>
