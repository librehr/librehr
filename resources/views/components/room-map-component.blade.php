@props([
    'record',
    'bookings'
])
@php
    $image = Storage::disk('public');
    $imageUrl = $image->url(data_get($record, 'attributes.image'));
    $imagePath = $image->path(data_get($record, 'attributes.image'));
    list($width, $height) = getimagesize($imagePath);
@endphp
<div>

    <style>
        #map {
            width: 850px;
            height: 600px;
            background-image: url('{{ asset('images/grid.webp') }}');
            background-size: auto auto;
            background-repeat: repeat;
        }
    </style>

    <div class="flex flex-row overflow-auto">
        <div class="font-semibold mr-2">Available desks:</div>
        <ul id="circleList" class="flex flex-row gap-4"></ul>
    </div>
    <button id="saveChanges">save</button>
    <div id="map"></div>

</div>


@push('scripts')

<script>

    document.addEventListener('livewire:initialized', () => {
        document.getElementById("saveChanges").addEventListener("click", function () {
            window.Livewire.dispatch('add-circle', { refreshPosts: true });
        });
    })

    const app = {
        bookings: false,
        availableCircleNames: @json($record->desks),
        circles: [],
        circlesLayer: null,
        mapper: null,
        predefinedCircles: [
            { latlng: [100, 100], name: 'A1', booking: { userName: 'User 1', selfBooked: true } },
            { latlng: [200, 200], name: 'A2', booking: { userName: 'User 2', selfBooked: false } },
            // Add more circles as needed
        ],
        addCircle: function(circleData) {
            this.circles.push(circleData.name);
            this.updateCircleList();
        },
        deleteCircle: function(circleString) {
            let circleData = JSON.parse(base64Decode(circleString))
            this.circlesLayer.eachLayer((layer) => {
                if (layer instanceof L.CircleMarker) {
                    const circle = layer;
                    const circleNameLayer = circle.options.name;
                    if (circleNameLayer === circleData.name) {
                        this.circles = this.circles.filter(circleDataCircle => circleDataCircle.name !== circleData.name);
                        this.availableCircleNames.push(circleData);
                        this.updateCircleList();
                        this.circlesLayer.removeLayer(layer);
                    }
                }
            });
        },
        updateCircleList: function() {
            this.availableCircleNames.sort((a, b) => {
                const numA = parseInt(a.name.match(/\d+/)[0]);
                const numB = parseInt(b.name.match(/\d+/)[0]);
                console.log('numA:', numA, 'numB:', numB, 'a.name:', a.name, 'b.name:', b.name);
                if (numA === numB) {
                    return a.name.localeCompare(b.name);
                } else {
                    return numA - numB;
                }
            });
            this.renderCircleList();

        },
        renderCircleList: function() {
            const circleList = document.getElementById('circleList');
            circleList.innerHTML = '';
            this.availableCircleNames.forEach(circle => {
                const listItem = document.createElement('li');
                listItem.textContent = circle.name;
                circleList.appendChild(listItem);
            });
        },
        initMap: function() {
            this.availableCircleNames = this.availableCircleNames.filter(circle => {
                return !this.predefinedCircles.find(predefinedCircle => predefinedCircle.name === circle.name);
            });
            this.circlesLayer = L.layerGroup().addTo(this.mapper);
            const halfHeight = {{ $height }} * 1.2;
            const halfWidth = {{ $width }} * 1.2;
            const bounds = [
                [-halfHeight, -halfWidth],
                [{{ $height }}, {{ $width }}]
            ];
            L.imageOverlay('{{ $imageUrl }}', bounds).addTo(this.mapper);
            this.mapper.fitBounds(bounds);

            this.predefinedCircles.forEach(circleData => {
                const fillColor = circleData.booking ? '#FF0000' : '#088F8F';
                const color = circleData.booking ? '#FF0000' : '#0FFF50';
                const circleMarker = L.circleMarker(circleData.latlng, {
                    color: color,
                    fillColor: fillColor,
                    fillOpacity: 0.5,
                    draggable: true,
                    radius: 9,
                    name: circleData.name
                }).addTo(this.circlesLayer)
                    .bindPopup(`<div class="flex flex-col gap-2 p-4 min-w-[150px] text-lg">
                    <div>${circleData.name}</div>
                    ${circleData.booking ? `
                        <div>Desk taken by <span class="font-semibold">${circleData.booking.userName}</span></div>
                        ${circleData.booking.selfBooked ? `<button class="mt-2 bg-red-600 hover:bg-red-500 text-white rounded p-2">Free desk!</button>` : ''}
                    ` : `
                        <button class="mt-2 bg-red-600 hover:bg-red-500 text-white rounded p-2">Book</button>
                    `}
                    ${this.bookings === false ? `<button onclick="app.deleteCircle('${base64Encode(JSON.stringify(circleData))}')" class="bg-red-600 hover:bg-red-500 text-white rounded p-2">Delete</button>` : ''}
                </div>`).bindTooltip(`Desk <b>${circleData.name}</b>`).openTooltip();
                this.addCircle(circleData);
            });

            this.mapper.on('click', (e) => {
                if (this.availableCircleNames.length > 0) {
                    let circleData = this.availableCircleNames.shift();
                    circleData['latlng'] = e.latlng;
                    const fillColor = circleData.booking ? '#FF0000' : '#088F8F';
                    const color = circleData.booking ? '#FF0000' : '#0FFF50';
                    const circleMarker = L.circleMarker(circleData.latlng, {
                        color: color,
                        fillColor: fillColor,
                        fillOpacity: 0.5,
                        draggable: true,
                        radius: 9,
                        name: circleData.name
                    }).addTo(this.circlesLayer)
                        .bindPopup(`<div class="flex flex-col gap-2 p-4 min-w-[150px] text-lg">
                        <div>${circleData.name}</div>
                        ${circleData.booking ? `
                            <div>Desk taken by <span class="font-semibold">${circleData.booking.userName}</span></div>
                            ${circleData.booking.selfBooked ? `<button class="mt-2 bg-red-600 hover:bg-red-500 text-white rounded p-2">Free desk!</button>` : ''}
                        ` : `
                            <button class="mt-2 bg-red-600 hover:bg-red-500 text-white rounded p-2">Book</button>
                        `}
                        ${this.bookings === false ? `<button onclick="app.deleteCircle('${base64Encode(JSON.stringify(circleData))}')" class="bg-red-600 hover:bg-red-500 text-white rounded p-2">Delete</button>` : ''}
                    </div>`).bindTooltip(`Desk <b>${circleData.name}</b>`).openTooltip();
                    this.addCircle(circleData);
                }
            });
        }
    };

    // Función para codificar en Base64
    function base64Encode(str) {
        return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, (match, p1) => String.fromCharCode(parseInt(p1, 16))));
    }

    // Función para decodificar Base64
    function base64Decode(str) {
        return decodeURIComponent(Array.prototype.map.call(atob(str), c => {
            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
        }).join(''));
    }

    // Inicializa el mapa cuando el DOM está listo
    document.addEventListener('DOMContentLoaded', function() {
        app.mapper = L.map('map', {
            crs: L.CRS.Simple,
            minZoom: -5
        });
        app.initMap();


    });
</script>
@endpush
