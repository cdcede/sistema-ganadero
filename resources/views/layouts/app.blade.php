<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistema Gestion') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    {{-- <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> --}}
    {{-- <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet"> --}}

    <!-- Styles -->
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}
</head>
<body>
    <div id="app">
        <v-app app>
                @if (Auth::User())
                    @php
                        $role = Auth::user();

                        if ($role->hasRole('admin')) {
                            $role='admin';
                        }elseif($role->hasRole('ganadero')){
                            $role='ganadero';
                        }else{
                            $role='trabajador';
                        }
                    @endphp 
                    <sidebar :user="{{ Auth::user() }}"></sidebar>
                @else
                    <sidebar></sidebar>
                @endif 
                <v-main>
                    @yield('content')
                </v-main>
        </v-app>
        <v-footer app>
          <v-col
            class="text-center"
            cols="12"
          >
            <center><strong>Sistema Ganadero</strong> - &copy;2022</center>
          </v-col>
        </v-footer>
        {{-- <v-overlay :value="overlay">
            <v-progress-circular
                indeterminate
                size="64"
            ></v-progress-circular>
        </v-overlay> --}}
    </div>
</body>
</html>
