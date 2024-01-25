<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fabric.js - Cuadrado</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js" integrity="sha512-CeIsOAsgJnmevfCi2C7Zsyy6bQKi43utIjdA87Q0ZY84oDqnI0uwfM9+bKiIkI75lUeI00WG/+uJzOmuHlesMA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @vite('resources/css/filament/app/theme.css')

</head>
<body>

<div class="w-full grid grid-cols-12 p-4 gap-4 fabricjs">
    <div>
        <div class="flex flex-col gap-2">
            <button id="createSquare">Cuadrado</button>
            <button id="createCircle">Circulo</button>
            <hr>
            <button id="sendToBack" style="display: none;" class="bg-gray-600">Enviar al Fondo</button>
            <button id="bringToFront" style="display: none;" class="bg-gray-600">Llevar al Frente</button>
            <button id="deleteObject" style="display: none;">Remove</button>
            <button id="cloneObject" style="display: none;">Clone</button>
            <button id="rotateObject" style="display: none;">Rotar 45 Grados</button>
            <input type="color" id="colorPicker" value="#000000" style="display: none;">
            <button id="changeColor" style="display: none;">Cambiar Color</button>

            <button id="exportPNG">Exportar como PNG</button>
        </div>
    </div>
    <div class="col-span-6 flex flex-col items-start justify-start">
        <div class="flex gap-2 mb-2">
            <button id="centerTop" class="bg-yellow-600">Top</button>
            <button id="centerLeft" class="bg-yellow-600">Izquierda</button>
            <button id="centerCenter" class="bg-yellow-600">Centro</button>
            <button id="centerRight" class="bg-yellow-600">Derecha</button>
            <button id="centerBottom" class="bg-yellow-600">Abajo</button>
            <button id="centerMiddle" class="bg-yellow-600">Medio</button>
        </div>
        <canvas id="canvas" width="600" height="580" class="border"></canvas>
    </div>
    <div class="col-span-4 flex flex-col">
        <div id="svgContainer" class="grid grid-cols-2 max-h-96"></div>
    </div>

</div>

<script>
    const fabric2 = new fabric.Canvas('canvas');
    let selectedObject;

    // SVG en un array
    const svgArray = [
        '<svg width="100" height="100"><circle cx="50" cy="50" r="40" stroke="black" stroke-width="3" fill="red" /></svg>',
    ];


    // Llena el contenedor con SVGs
    const svgContainer = document.getElementById('svgContainer');
    svgArray.forEach((svgString, index) => {
        const svgDiv = document.createElement('div');
        svgDiv.innerHTML = svgString;
        svgDiv.style.marginRight = '10px';
        svgDiv.style.cursor = 'pointer';
        svgDiv.addEventListener('click', () => selectSVG(index));
        svgContainer.appendChild(svgDiv);
    });

    const svgPreviewElement = document.getElementById('svgPreview');


    for (let i = 0; i < 600 / 10; i++) {
        //fabric2.add(new fabric.Line([i * 10, 0, i * 10, 400], { stroke: '#ccc', selectable: false }));
    }

    for (let j = 0; j < 400 / 10; j++) {
        //fabric2.add(new fabric.Line([0, j * 10, 600, j * 10], { stroke: '#ccc', selectable: false }));
    }

    fabric2.grid = 10;

    fabric2.on('object:moving', (options) => {
        const grid = fabric2.grid;

        options.target.set({
            left: Math.round(options.target.left / grid) * grid,
            top: Math.round(options.target.top / grid) * grid
        }).setCoords();
    });

    fabric2.on('selection:created', (options) => {
        selectedObject = options.target;
        showObjectButtons();
    });

    fabric2.on('selection:updated', (options) => {
        selectedObject = options.target;
        showObjectButtons();
    });

    fabric2.on('selection:cleared', () => {
        selectedObject = null;
        hideObjectButtons();
    });

    function showObjectButtons() {
        document.getElementById('deleteObject').style.display = 'block';
        document.getElementById('cloneObject').style.display = 'block';
        document.getElementById('rotateObject').style.display = 'block';
        document.getElementById('colorPicker').style.display = 'block';
        document.getElementById('changeColor').style.display = 'block';
        document.getElementById('sendToBack').style.display = 'block';
        document.getElementById('bringToFront').style.display = 'block';
    }

    function hideObjectButtons() {
        document.getElementById('deleteObject').style.display = 'none';
        document.getElementById('cloneObject').style.display = 'none';
        document.getElementById('rotateObject').style.display = 'none';
        document.getElementById('colorPicker').style.display = 'none';
        document.getElementById('changeColor').style.display = 'none';
        document.getElementById('sendToBack').style.display = 'none';
        document.getElementById('bringToFront').style.display = 'none';
    }

    document.getElementById('sendToBack').addEventListener('click', () => {
        sendToBack();
    });

    document.getElementById('bringToFront').addEventListener('click', () => {
        bringToFront();
    });

    document.getElementById('exportPNG').addEventListener('click', () => {
        const filename = prompt("Ingrese el nombre del archivo (sin extensión):") || 'canvas';
        exportCanvasAsPNG(filename);
    });

    // Move selected object or group with arrow keys
    window.addEventListener('keydown', (e) => {
        const activeObject = fabric2.getActiveObject();
        if (activeObject) {
            switch (e.key) {
                case 'ArrowUp':
                    activeObject.top -= 10;
                    break;
                case 'ArrowDown':
                    activeObject.top += 10;
                    break;
                case 'ArrowLeft':
                    activeObject.left -= 10;
                    break;
                case 'ArrowRight':
                    activeObject.left += 10;
                    break;
            }


            if ((e.ctrlKey || e.metaKey) && e.key === 'c') {
                if (activeObject) {
                    const clonedObject = fabric.util.object.clone(activeObject);
                    fabric2.add(clonedObject);
                    fabric2.setActiveObject(clonedObject);
                }
            }

            fabric2.requestRenderAll();
        }
    });

    document.getElementById('createSquare').addEventListener('click', () => {
        const rect = new fabric.Rect({
            left: fabric2.width / 2 - 100,
            top: fabric2.height / 2 - 100,
            fill: 'lightgray',
            width: 165,
            height: 160,
            originX: 'left',
            originY: 'top',
            centeredRotation: true,
            centeredScaling: false,
            cornerColor: 'black',
            cornerStyle: 'circle',
            hasBorders: false,
            lockRotation: false,
            resizable: true,
            objectCaching: true,
            lockScalingX: false,
            lockScalingY: false,
            selectable: true,
        });

        fabric2.add(rect);
        fabric2.setActiveObject(rect);
    });

    document.getElementById('createCircle').addEventListener('click', () => {
        const circle = new fabric.Circle({
            left: fabric2.width / 2 - 100,
            top: fabric2.height / 2 - 100,
            fill: 'lightgray',
            radius: 80, // Usa el radio en lugar de width y height
            originX: 'left',
            originY: 'top',
            centeredRotation: true,
            centeredScaling: false,
            cornerColor: 'black',
            cornerStyle: 'circle',
            hasBorders: false,
            lockRotation: false,
            resizable: true,
            objectCaching: true,
            lockScalingX: false,
            lockScalingY: false,
            selectable: true,
        });

        fabric2.add(circle);
        fabric2.setActiveObject(circle);
    });

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' || e.key === 'Delete') {
            const activeObject = fabric2.getActiveObject();
            if (activeObject) {
                fabric2.remove(activeObject);
            }
        }
    });

    document.getElementById('deleteObject').addEventListener('click', () => {
        const activeObject = fabric2.getActiveObject();
        if (activeObject) {
            fabric2.remove(activeObject);
        }
    });

    document.getElementById('cloneObject').addEventListener('click', () => {
        const activeObject = fabric2.getActiveObject();
        if (activeObject) {
            const clonedObject = fabric.util.object.clone(activeObject);
            fabric2.add(clonedObject);
            fabric2.setActiveObject(clonedObject);
        }
    });

    document.getElementById('rotateObject').addEventListener('click', () => {
        const activeObject = fabric2.getActiveObject();
        if (activeObject) {
            activeObject.angle += 45;
            activeObject.centeredRotation = true;
            fabric2.requestRenderAll();
        }
    });

    document.getElementById('colorPicker').addEventListener('input', (e) => {
        const colorPicker = document.getElementById('colorPicker');
        const newColor = colorPicker.value;
        const activeObject = fabric2.getActiveObject();

        if (activeObject) {
            activeObject.fill = newColor;
            activeObject.set('backgroundColor', newColor)
            fabric2.requestRenderAll();
        }
    });

    document.getElementById('centerLeft').addEventListener('click', () => {
        centerObject('left');
    });

    document.getElementById('centerRight').addEventListener('click', () => {
        centerObject('right');
    });

    document.getElementById('centerCenter').addEventListener('click', () => {
        centerObject('center');
    });

    document.getElementById('centerTop').addEventListener('click', () => {
        centerObject('top');
    });

    document.getElementById('centerBottom').addEventListener('click', () => {
        centerObject('bottom');
    });

    document.getElementById('centerMiddle').addEventListener('click', () => {
        centerObject('middle');
    });


    document.getElementById('svgSelect').addEventListener('change', (e) => {
        const selectedIndex = parseInt(e.target.value, 10);
        loadSVG(selectedIndex);
    });

    function centerObject(position) {
        const activeObject = fabric2.getActiveObject();
        if (activeObject) {
            switch (position) {
                case 'top':
                    activeObject.set('top', 0);
                    break;
                case 'left':
                    activeObject.set('left', 0);
                    break;
                case 'right':
                    activeObject.set('left', fabric2.width - activeObject.width / 2 - activeObject.width / 2);
                    break;
                case 'center':
                    activeObject.set('left', fabric2.width / 2 - activeObject.width / 2);
                    break;
                case 'bottom':
                    activeObject.set('top', fabric2.height - activeObject.height / 2 - activeObject.height / 2);
                    break;
                case 'middle':
                    activeObject.set('left', fabric2.width / 2 - activeObject.width / 2);
                    activeObject.set('top', fabric2.height / 2 - activeObject.height / 2);

                    break;
            }
            fabric2.requestRenderAll();
        }
    }

    function exportCanvasAsPNG(filename) {
        const dataURL = fabric2.toDataURL({ format: 'png' });
        const link = document.createElement('a');
        link.href = dataURL;
        link.download = `${filename}.png`;
        link.click();
    }

    function selectSVG(selectedIndex) {
        const svgString = svgArray[selectedIndex];
        fabric.loadSVGFromString(svgString, (objects, options) => {
            const svgObject = fabric.util.groupSVGElements(objects, options);
            fabric2.add(svgObject);
        });
    }

    function sendToBack() {
        const activeObject = fabric2.getActiveObject();

        if (activeObject) {
            fabric2.sendToBack(activeObject);
        }
    }

    function bringToFront() {
        const activeObject = fabric2.getActiveObject();

        if (activeObject) {
            fabric2.bringToFront(activeObject);
        }
    }

</script>

</body>
</html>
