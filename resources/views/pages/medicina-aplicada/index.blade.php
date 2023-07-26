@extends('layouts.app')

@section('content')

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
        <livestock-medicines :user="{{ Auth::user() }}"></livestock-medicines> 
    @else
        <livestock-medicines></livestock-medicines>
    @endif 

    {{-- <livestock-medicines></livestock-medicines> --}}

@endsection