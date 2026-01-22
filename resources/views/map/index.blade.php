@php
$gNum = $gNum ?? "03";
$gName = $gName ?? "Accommodation and surrounding areas";
@endphp

@extends('layouts.user')

@section('content')
<div id="mainContent" class="container map_wrap">
    @include('components.user-header')
    <div class="contents">
    
        <div class="stitle">{{ $gName }}<div class="location"><a href="{{ route('home') }}" class="home">Home</a><span><strong>{{ $gName }}</strong></span></div></div>
        
        <div class="google_map map-wrap">
            <div>
                <input id="pac-input" type="text" placeholder="Search places">
                <div class="place-info" id="place-info" style="display:none;">
                    <div class="thumb" id="place-thumb"></div>
                    <div class="name" id="place-name"></div>
                    <div class="addr" id="place-addr"></div>
                </div>
            </div>
            <div id="map"></div>
        </div>
        
    </div>
</div>

@include('components.user-footer')

@push('scripts')
@php
$apiKey = config('services.google.maps_api_key');
@endphp
<script src="https://maps.googleapis.com/maps/api/js?key={{ $apiKey }}&libraries=places&language=en"></script>
<script src="/js/common/google-maps.js"></script>
@endpush

@endsection
