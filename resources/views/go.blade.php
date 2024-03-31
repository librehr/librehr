<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Reservation</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
    <style>
        #map {
            height: 600px;
            background-image: url(<?php echo e(asset('images/grid.webp')); ?>);
            background-size: auto auto;
            background-repeat: repeat;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body>
 <div x-data="app" x-init="initMap">

    <div class="flex flex-row overflow-auto">
        <div class="font-semibold mr-2">Available desks:</div>
        <ul id="circleList" class="flex flex-row gap-4" x-text="availableCircleNames.join(', ')"></ul>
    </div>

    <div id="map"></div>

</div>

<script>
    const app = {
        bookings: false,
        availableCircleNames: ['A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9', 'A10'],
        circles: [],
        circlesLayer: null,
        map: null,
        predefinedCircles: [
            { latlng: [100, 100], name: 'A1', booking: { userName: 'User 1', selfBooked: true } },
            { latlng: [200, 200], name: 'A2', booking: { userName: 'User 2', selfBooked: false } },
            // Agregar más círculos según sea necesario
        ],
        addCircle: function(circleData) {
            this.circles.push(circleData.name);
            this.updateCircleList();
        },
        deleteCircle: function(circleName) {
            // Eliminar el círculo del mapa y actualizar la lista de círculos disponibles
            this.circlesLayer.eachLayer((layer) => {
                if (layer instanceof L.CircleMarker) {
                    const circle = layer;
                    const circleNameLayer = circle.options.name;
                    if (circleNameLayer === circleName) {
                        this.circles = this.circles.filter(name => name !== circleName);
                        this.availableCircleNames.push(circleName);
                        this.updateCircleList();
                        this.circlesLayer.removeLayer(layer);
                    }
                }
            });
        },
        updateCircleList: function() {
            this.availableCircleNames.sort((a, b) => {
                // Extraer los números de los nombres de los círculos
                const numA = parseInt(a.match(/\d+/)[0]);
                const numB = parseInt(b.match(/\d+/)[0]);

                // Comparar los números
                if (numA === numB) {
                    // Si los números son iguales, comparar alfabéticamente
                    return a.localeCompare(b);
                } else {
                    return numA - numB; // Ordenar numéricamente en orden ascendente
                }
            });
        },

        initMap: function() {
            this.map = L.map('map', {
                crs: L.CRS.Simple,
                minZoom: -5
            });

            // Filtrar los nombres de los círculos predefinidos y eliminarlos de availableCircleNames
            this.predefinedCircles.forEach(circle => {
                const index = this.availableCircleNames.indexOf(circle.name);
                if (index !== -1) {
                    this.availableCircleNames.splice(index, 1);
                }
            });

            this.circlesLayer = L.layerGroup().addTo(this.map);

            const bounds = [[0, 0], [600, 600]];

            L.imageOverlay('css/plano.png', bounds)
                .addTo(this.map);

            this.map.fitBounds(bounds);

            // Agregar los círculos predefinidos
            this.predefinedCircles.forEach(circle => {
                const fillColor = circle.booking ? '#FF0000' : '#088F8F';
                const color = circle.booking ? '#FF0000' : '#0FFF50';
                const circleMarker = L.circleMarker(circle.latlng, {
                    color: color,
                    fillColor: fillColor,
                    fillOpacity: 0.5,
                    draggable: true,
                    radius: 9,
                    name: circle.name // Agregar el nombre del círculo como opción
                })
                    .addTo(this.circlesLayer)
                    .bindPopup(`<div class="flex flex-col gap-2 p-4 min-w-[150px] text-lg">
                <div>${circle.name}</div>
                ${circle.booking ? `
                    <div>Desk taken by <span class="font-semibold">${circle.booking.userName}</span></div>
                    ${circle.booking.selfBooked ? `<button class="mt-2 bg-red-600 hover:bg-red-500 text-white rounded p-2">Free desk!</button>` : ''}
                ` : `
                    <button class="mt-2 bg-red-600 hover:bg-red-500 text-white rounded p-2">Book</button>
                `}
                ${this.bookings === false ? `<button x-on:click="deleteCircle('${circle.name}')" class="bg-red-600 hover:bg-red-500 text-white rounded p-2">Delete</button>` : ''}
            </div>`).bindTooltip(`Desk <b>${circle.name}</b>`).openTooltip();
                this.addCircle(circle);
            });

            this.map.on('click', (e) => {
                if (this.availableCircleNames.length > 0) {
                    const smallestName = this.availableCircleNames.shift();
                    const circleData = {
                        latlng: e.latlng,
                        name: smallestName,
                        booking: null
                    };
                    const fillColor = circleData.booking ? '#FF0000' : '#088F8F';
                    const color = circleData.booking ? '#FF0000' : '#0FFF50';
                    const circleMarker = L.circleMarker(circleData.latlng, {
                        color: color,
                        fillColor: fillColor,
                        fillOpacity: 0.5,
                        draggable: true,
                        radius: 9,
                        name: smallestName // Agregar el nombre del círculo como opción
                    })
                        .addTo(this.circlesLayer)
                        .bindPopup(`<div class="flex flex-col gap-2 p-4 min-w-[150px] text-lg">
                <div>${circleData.name}</div>
                ${circleData.booking ? `
                    <div>Desk taken by <span class="font-semibold">${circleData.booking.userName}</span></div>
                    ${circleData.booking.selfBooked ? `<button class="mt-2 bg-red-600 hover:bg-red-500 text-white rounded p-2">Free desk!</button>` : ''}
                ` : `
                    <button class="mt-2 bg-red-600 hover:bg-red-500 text-white rounded p-2">Book</button>
                `}
                ${this.bookings === false ? `<button x-on:click="deleteCircle('${circleData.name}')" class="bg-red-600 hover:bg-red-500 text-white rounded p-2">Delete</button>` : ''}
            </div>`).bindTooltip(`Desk <b>${circleData.name}</b>`).openTooltip();
                    this.addCircle(circleData);
                }
            });
        }
    };
</script>
 <script>
     window.onload = (event) => {
         Echo.channel('channel-name')
             .listen('SomeEvent', (event) => {
                 console.log(event);
                 // Handle event data here
             });     };
 </script>
</body>
</html>
