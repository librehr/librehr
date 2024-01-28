@props([
    'record',
    'bookings'
])
@if ($record)
    @php
        $image = Storage::disk('public');
        $imageUrl = $image->url(data_get($record, 'attributes.image'));
        $imagePath = $image->path(data_get($record, 'attributes.image'));
        list($width, $height) = getimagesize($imagePath);
    @endphp

    <div style=" background-image: url('{{ asset('images/grid.webp') }}');" class="flex flex-col items-center">

        <div class="flex justify-between overflow-auto w-full">
            <div class="font-semibold mr-2">Available desks:</div>
            <ul id="circleList" class="flex-grow flex flex-row gap-4"></ul>
            <button id="saveChanges" class="bg-primary-600 text-white p-2 rounded">Save</button>
        </div>

        <div id="map" style="
            z-index: 15;
            width: 800px;
            height: 600px;
            background-color: transparent;
            background-size: auto auto;
            background-repeat: repeat;
        "></div>

    </div>


    @push('scripts')
        @script
            <script>
                $wire.on('render-map', (event) => {
                    window.app.initMap();
                    alert('hola')
                });
            </script>
        @endscript
        <script>

            document.addEventListener('livewire:initialized', () => {

                document.getElementById("saveChanges").addEventListener("click", function () {
                    window.Livewire.dispatch('add-circle', { circles: app.circles });
                });
            })

            const app = {
                bookings: false,
                availableCircleNames: @json($record->desks),
                circles: [],
                circlesLayer: null,
                mapper: null,
                predefinedCircles: @json($record->desks->whereNotNull('attributes.latlng')),
                addCircle: function(circleData) {
                    this.circles.push(circleData);
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
                getHtmlCircleData(circleData) {
                    let bookingHtml = circleData.booking ? `
                        <div>Desk taken by <span class="font-semibold">${circleData.booking.userName}</span></div>
                        ${circleData.booking.selfBooked ? `<button class="mt-2 bg-red-600 hover:bg-red-500 text-white rounded p-2">Free desk!</button>` : ''}
                    ` : `
                        <button class="mt-2 bg-red-600 hover:bg-red-500 text-white rounded p-2">Book</button>
                    `

                    let editHtml = `<button onclick="app.deleteCircle('${base64Encode(JSON.stringify(circleData))}')" class="bg-red-600 hover:bg-red-500 text-white rounded p-2">Delete</button>`

                    return '<div class="flex flex-col gap-2 p-4 min-w-[150px] text-lg"><div>Desk: <b>' + circleData.name + '</b></div> ' + (this.bookings ? bookingHtml : editHtml) + '</div>';
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
                            .bindPopup(this.getHtmlCircleData(circleData))
                            .bindTooltip(`Desk <b>${circleData.name}</b>`)
                            .openTooltip();
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
                                .bindPopup(this.getHtmlCircleData(circleData))
                                .bindTooltip(`Desk <b>${circleData.name}</b>`)
                                .openTooltip();
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
@endif
