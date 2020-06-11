@extends('layouts.app')

@section('content')
    <style>
        #map {
            height: 500px;
            width: 100%;
        }
    </style>
    <div class="row justify-content-center">
        <div class="col-12 mt-2">
            <div class="row">
                <div class="col-6">
                    <h1>{{ __('messages.studentmap') }}</h1>
                </div>
            </div>
        </div>
        <div class="col-12">
            @if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}  
                </div><br/>
            @endif
            @include('partials.error')
            <div id="map"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var students = <?php echo $students_json; ?>;
        function initMap() 
        {
            var bounds = new google.maps.LatLngBounds();
            var map = new google.maps.Map(document.getElementById('map'),  {zoom:5});
            $.each(students, function(i, student){
               var addr = new google.maps.LatLng(student.addr_latitude, student.addr_longitude);
                var marker = new google.maps.Marker({position: addr, map: map});
                
                var infowindow = new google.maps.InfoWindow({
                    content: `
                        <div>
                            <h3>${student.name}</h3>
                            <p>${student.address}</p>
                        </div>
                    `,
                    maxWidth: 500
                });
                
                marker.addListener('click', function() {
                    infowindow.open(map, marker);
                });
                bounds.extend(addr);
            });
            map.fitBounds(bounds);
        }
    </script>
    <script defer src="https://maps.googleapis.com/maps/api/js?key={{ $google_map_api_key  }}&callback=initMap"></script>
@endpush