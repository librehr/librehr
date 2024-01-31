@props([
    'record',
    'bookings',
    'selected'
])
@if (data_get($record, 'attributes.image', null) === null)
No image map uploaded.
@endif
@if ($record && data_get($record, 'attributes.image', null) !== null)
<div class="flex flex-col items-center">
    <div class="flex justify-between overflow-auto w-full">
        <div class="font-semibold mr-2">Available desks:</div>
        <ul id="circleList" class="flex-grow flex flex-row gap-4"></ul>
        @if(!$bookings)
            <button id="saveChanges" class="bg-primary-600 text-white p-2 rounded">Save</button>@else
        @endif
    </div>

    <div id="map" class="test" style="
            z-index: 15;
            width: 800px;
            height: 600px;
            background-color: transparent;
            background-size: auto auto;
            background-repeat: repeat;
        "></div>
</div>
    @php
        $myUserId = auth()->id();
        $selected = \Carbon\Carbon::parse($selected);
        $image = Storage::disk('public');
        $imageUrl = $image->url(data_get($record, 'attributes.image'));
        $imagePath = $image->path(data_get($record, 'attributes.image'));
        list($width, $height) = getimagesize($imagePath);
    @endphp
    @script
        <script>
        document.addEventListener('livewire:initialized', () => {
            document.getElementById("saveChanges").addEventListener("click", function () {
                window.Livewire.dispatch('add-circle', { circles: app.circles });
            });
        })

        let app = {
            bookings: @json($bookings),
            availableCircleNames: @json($record->desks),
            circles: [],
            circlesLayer: null,
            predefinedCircles: @json($record->desks->whereNotNull('attributes.latlng')),
            mapper: null,
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
                let bookingHtml = circleData.bookings.length > 0 ? `
                        <div>Desk taken by <span class="font-semibold">${(circleData.bookings[0].contract.user.name)}</span></div>
 <hr class="my-2">
                        <div class="flex flex-row gap-2">
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
</svg>

<span class="text-red-600">Not available</span>
                        </div>

                        ${circleData.bookings.length > 0 && circleData.bookings[0].contract.user_id === {{ $myUserId }} ? `<button class="mt-2 bg-red-600 hover:bg-red-500 text-white rounded p-2" onclick="window.Livewire.dispatch('free-seat', { seat: '${base64Encode(JSON.stringify(circleData))}' })">Free desk!</button>` : ''}
                    ` : `
                        <hr class="my-2">
                        <div class="flex flex-row gap-2">
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
</svg>

<span>Available</span>
                        </div>
                        <span>{{ $selected ? $selected->format('F j,  Y') : null }}</span>
                        <button onclick="window.Livewire.dispatch('book-seat', { seat: '${base64Encode(JSON.stringify(circleData))}' })" class="mt-2 bg-red-600 hover:bg-red-500 text-white rounded p-2">Book</button>
                    `

                let editHtml = `<button onclick="app.deleteCircle('${base64Encode(JSON.stringify(circleData))}')" class="bg-red-600 hover:bg-red-500 text-white rounded p-2">Delete</button>`

                return '<div class="flex flex-col gap-2 p-6 min-w-[220px] text-lg"><div><span class="text-xl font-semibold">Desk ' + circleData.name + '</span></div> ' + (this.bookings ? bookingHtml : editHtml) + '</div>';
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
                    [{{ $height*(2.5) }}, {{ $width*(2.5) }}]
                ];
                L.imageOverlay('{{ $imageUrl }}', bounds).addTo(this.mapper);
                this.mapper.fitBounds(bounds);

                this.predefinedCircles.forEach(circleData => {
                    const fillColor = circleData.bookings.length > 0 ? '#FF0000' : '#088F8F';
                    const color = circleData.bookings.length > 0 ? '#FF0000' : '#0FFF50';
                    const circleMarker = L.circleMarker(circleData.latlng, {
                        color: color,
                        fillColor: fillColor,
                        fillOpacity: 0.5,
                        draggable: true,
                        radius: 9,
                        name: circleData.name
                    }).addTo(this.circlesLayer)
                        .bindPopup(this.getHtmlCircleData(circleData))
                        .bindTooltip(`Desk <b>${circleData.name}</b>`);
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

        setTimeout(function () {
            if (app.mapper) {
                app.mapper.remove()
            }

            app.mapper = L.map('map', {
                crs: L.CRS.Simple,
                minZoom: -5
            });

            app.initMap();
        },10)

        Livewire.on('render-map', (event) => {
            setTimeout(function () {
                if (app.mapper) {
                    app.mapper.remove()
                }
                app.mapper = L.map('map', {
                    crs: L.CRS.Simple,
                    minZoom: -5
                });
                // problema aqui
                app.predefinedCircles = @js($record->desks->whereNotNull('attributes.latlng'));
                app.initMap();
            },100)
        });

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
    </script>
    @endscript
@endif
