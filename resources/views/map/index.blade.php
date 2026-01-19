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
            <div id="map">
                <iframe id="mapFrame" src="https://www.google.com/maps?q=37.5665,126.9780&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
        
    </div>
</div>

@include('components.user-footer')

@push('scripts')
<script>
$(document).ready(function() {
    // 기본 위치 (서울 시청)
    const defaultPos = { lat: 37.5665, lng: 126.9780 };
    
    // 현위치 가져오기
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                // 구글맵 Embed URL 업데이트 (좌표로 마커 표시)
                // 구글맵 Embed는 자체 검색창을 포함하고 있음
                const mapUrl = `https://www.google.com/maps?q=${lat},${lng}&output=embed`;
                document.getElementById("mapFrame").src = mapUrl;
            },
            function(error) {
                console.error("위치 정보를 가져올 수 없습니다:", error);
            }
        );
    }
});
</script>
@endpush

@endsection
