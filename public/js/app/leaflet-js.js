const bookings = false;
var map = L.map('map', {
    crs: L.CRS.Simple,
    minZoom: -5
});
alert('hola');
var bounds = [[0, 0], [600, 600]]; // Adjust according to your image size

L.imageOverlay('', bounds)
.addTo(map);

map.fitBounds(bounds);

var circles = L.layerGroup().addTo(map);
var availableCircleNames = ['A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9', 'A10'];

// Add some predefined circles on page load
var preDefinedCircles = [
    {latlng: [100, 100], name: 'A1'},
    {latlng: [200, 200], name: 'A3', booking: {userName: 'Borja', userId: '1', selfBooked: true}},
];

preDefinedCircles.forEach(function(circleData) {
    addCircle(circleData);
    // Remove predefined seats from the list of available seats
    var index = availableCircleNames.indexOf(circleData.name);
    if (index !== -1) {
        availableCircleNames.splice(index, 1);
    }
});

updateCircleList();

map.on('click', function (e) {
    if (availableCircleNames.length > 0) {
        // Find the smallest available name
        var smallestIndex = 0;
        var smallestNumber = parseInt(availableCircleNames[0].replace(/\D/g, ''));

        for (var i = 1; i < availableCircleNames.length; i++) {
            var currentNumber = parseInt(availableCircleNames[i].replace(/\D/g, ''));
            if (currentNumber < smallestNumber) {
                smallestNumber = currentNumber;
                smallestIndex = i;
            }
        }

        var smallestName = availableCircleNames[smallestIndex];
        // Remove the smallest name from the list
        availableCircleNames.splice(smallestIndex, 1);
        const circleData = {latlng: e.latlng, name: smallestName}
        addCircle(circleData);
        updateCircleList();
    }
});

function addCircle(circleData) {
    let booking;
    let fillColor = '#088F8F'
    let color = '#0FFF50'
    if (circleData.booking) {
        fillColor = '#FF0000'
        color = '#FF0000'
        booking = '<div class="flex flex-col"><div class="">Desk taken by ' +
            '<span class="font-semibold">' + circleData.booking.userName + '</span>' +
            '</div>' +
            (circleData.booking.selfBooked ? '<button class="mt-2 bg-red-600 hover:bg-red-500 text-white rounded p-2">Free desk!</button>' : '') +
            '</div>'
    } else {
        booking = '<button class="mt-2 bg-red-600 hover:bg-red-500 text-white rounded p-2">Book</button>'
    }

    var circle = L.circleMarker(circleData.latlng, {
        color: color,
        fillColor: fillColor,
        fillOpacity: 0.5,
        draggable: true,
        radius: 9
    }).bindPopup('<div class="flex flex-col gap-2 p-4 min-w-[150px] text-lg">' +
        '<div class="">' + circleData.name + '</div>' +
        (booking ? booking : '') +
        (bookings === false ?
            '<button onclick="deleteCircle(\'' + circleData.name + '\')" class="bg-red-600 hover:bg-red-500 text-white rounded  p-2">Delete</button>' : '') +
        '</div>')
        .bindTooltip("Desk <b>" + circleData.name + '</b>').openTooltip();

    circles.addLayer(circle);
}

function deleteCircle(circleName) {
    circles.eachLayer(function (circle) {
        if (circle.getPopup().getContent().includes(circleName)) {
            circles.removeLayer(circle);
            availableCircleNames.push(circleName);
        }
    });

    updateCircleList();
}

function updateCircleList() {
    var circleListElement = document.getElementById('circleList');
    circleListElement.innerHTML = '';

    // Sort circle names alphabetically, considering numbers
    availableCircleNames.sort(function(a, b) {
        // Function to extract numeric part from name
        function extractNumber(name) {
            var match = name.match(/\d+/); // Find digit sequence
            return match ? parseInt(match[0]) : NaN; // Convert digit sequence to number
        }

        // Function to extract alphabetical part from name
        function extractAlpha(name) {
            return name.replace(/\d+/g, ''); // Remove digits and leave only letters
        }

        // Compare numeric parts of names
        var numA = extractNumber(a);
        var numB = extractNumber(b);
        if (numA !== numB) {
            return numA - numB; // Sort numerically if numeric parts are different
        }

        // If numeric parts are equal, compare alphabetical parts
        var alphaA = extractAlpha(a);
        var alphaB = extractAlpha(b);
        return alphaA.localeCompare(alphaB);
    });

    availableCircleNames.forEach(function (name) {
        circleListElement.innerHTML += '<li>' + name + '</li>';
    });
}
