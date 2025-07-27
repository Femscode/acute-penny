<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(isset($show_info) && $show_info)
    <meta name="title" content="Become part of our savings circle">
    <meta name="description" content="Join {{ $group->title }} to save together and get paid in turn.">
    @else
    <meta name="title" content="Discover Syncosave">
    <meta name="description" content="Join hands with the ones you love, friends, and family to grow financially.">
    @endif
    <!-- <title>{{ config('app.name', 'Syno-Save') }}</title> -->

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot  }}
        </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            @if(session('info'))

            Swal.fire({
                icon: 'info',
                title: '{{ session("info") }}'
            })

            @endif
            @if(session('message'))
            Swal.fire({
                icon: 'success',
                title: '{{ session("message") }}'
            })

            @endif

            @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '{{ session("success") }}'
            })

            @endif

            @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: '{{ session("error") }}'
            })

            @endif

        })
    </script>
</body>

</html>