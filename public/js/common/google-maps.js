// Google Maps 초기화 및 Places Autocomplete 검색창 구현
(function() {
    'use strict';

    // 기본 위치 (서울 시청)
    const defaultPos = { lat: 37.5665, lng: 126.9780 };
    
    let map;
    let marker;
    let autocomplete;
    let infoWindow;

    // 지도 초기화
    function initMap() {
        // 지도 생성
        map = new google.maps.Map(document.getElementById('map'), {
            center: defaultPos,
            zoom: 15,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true
        });

        // 기본 마커 생성
        marker = new google.maps.Marker({
            map: map,
            position: defaultPos,
            animation: google.maps.Animation.DROP
        });

        // 정보창 초기화
        infoWindow = new google.maps.InfoWindow();

        // Places Autocomplete 초기화
        initAutocomplete();

        // 현위치 가져오기
        getCurrentLocation();
    }

    // Places Autocomplete 검색창 초기화
    function initAutocomplete() {
        const input = document.getElementById('pac-input');
        if (!input) return;

        autocomplete = new google.maps.places.Autocomplete(input, {
            componentRestrictions: { country: 'kr' },
            fields: ['geometry', 'name', 'formatted_address', 'photos', 'place_id']
        });

        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            
            if (!place.geometry) {
                console.error('장소 정보를 찾을 수 없습니다.');
                return;
            }

            // 지도 중심 이동 및 마커 업데이트
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }

            // 마커 위치 업데이트
            marker.setPosition(place.geometry.location);
            marker.setVisible(true);

            // 장소 정보 표시
            displayPlaceInfo(place);
        });
    }

    // 장소 정보 표시
    function displayPlaceInfo(place) {
        const placeInfo = document.getElementById('place-info');
        const placeName = document.getElementById('place-name');
        const placeAddr = document.getElementById('place-addr');
        const placeThumb = document.getElementById('place-thumb');

        if (!placeInfo || !placeName || !placeAddr) return;

        // 이름과 주소 설정
        placeName.textContent = place.name || '';
        placeAddr.textContent = place.formatted_address || '';

        // 사진 설정
        if (placeThumb) {
            if (place.photos && place.photos.length > 0) {
                const photoUrl = place.photos[0].getUrl({ maxWidth: 400, maxHeight: 300 });
                placeThumb.style.backgroundImage = `url(${photoUrl})`;
                placeThumb.style.display = 'block';
            } else {
                placeThumb.style.backgroundImage = 'none';
                placeThumb.style.display = 'none';
            }
        }

        // 정보창 표시
        placeInfo.style.display = 'block';

        // 마커 클릭 시 정보창 표시
        const content = `
            <div style="padding: 8px;">
                <strong>${place.name || ''}</strong><br>
                <span style="color: #666; font-size: 12px;">${place.formatted_address || ''}</span>
            </div>
        `;
        
        infoWindow.setContent(content);
        infoWindow.open(map, marker);
    }

    // 현위치 가져오기
    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    // 지도 중심 이동
                    map.setCenter(pos);
                    map.setZoom(15);

                    // 마커 위치 업데이트
                    marker.setPosition(pos);
                    marker.setVisible(true);

                    // 현위치 정보창 표시
                    infoWindow.setContent('현재 위치');
                    infoWindow.open(map, marker);
                },
                function(error) {
                    console.error('위치 정보를 가져올 수 없습니다:', error);
                }
            );
        }
    }

    // DOM 로드 완료 시 초기화
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof google !== 'undefined' && google.maps) {
                initMap();
            } else {
                // Google Maps API 로드 대기
                window.addEventListener('load', function() {
                    if (typeof google !== 'undefined' && google.maps) {
                        initMap();
                    }
                });
            }
        });
    } else {
        if (typeof google !== 'undefined' && google.maps) {
            initMap();
        } else {
            window.addEventListener('load', function() {
                if (typeof google !== 'undefined' && google.maps) {
                    initMap();
                }
            });
        }
    }
})();
