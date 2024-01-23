<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .controls {
            display: inline-block;
        }
    </style>
    @vite('resources/css/filament/app/theme.css')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fabric-history@1.7.0/src/index.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@1nd/fabric-history-pure-browser@1.6.0/src/index.min.js"></script></head>
<body>
<div class="flex flex-col gap-2 editor">
    <div class="flex flex-row gap-4">
        <button id="save" onclick="SaveCanvas()">Save Canvas</button>
        <button id="load" onclick="LoadCanvas()">Load Canvas</button>
      <button id="toggleEdit" onclick="ToggleEdit()">Toggle <span id="editStatus">Editing: Enabled</span></button>
        <button id="resetZoom" onclick="ResetZoom()">Reset Zoom</button>
    </div>

    <div class="flex flex-row gap-4">
        <button id="undo" onclick="undo()" class="text-red-600 bg-red-300">Undo</button>
        <button id="redo" onclick="redo()">Redo</button>
        <button id="deleteSelected" onclick="deleteSelectedObjects()">Delete Selected</button>

        <button id="rotateChunks" onclick="RotateChunks()">Rotate Selected</button>
        <button id="add" onclick="Add()">Add a wall</button>
        <button id="addRoom" onclick="AddRoom()">Add a Room</button>
        <button id="addSvg" onclick="AddSvg()">Add SVG</button>
        <button id="addCircle" onclick="AddCircle()">Add Circle</button>
        <button id="rotateChunks" onclick="ChangeNumber()">Change Circle Number</button>

    </div>

</div>
<canvas id="c" width="800" height="500" style="border: 1px solid #ccc;"></canvas>

<div>
    <button id="toggleDrawing"><span id="drawingStatus">Not Drawing</span></button>
    <input type="range" id="brushSizeSlider" min="1" max="50" step="1" value="5">
    <span id="brushSizeLabel">Brush Size: 5</span>
    <label for="drawingColor">Drawing Color:</label>
    <input type="color" id="drawingColor" value="#000000">
</div>

<script>
    var canvas = new fabric.Canvas('c');
    var editingEnabled = true;
    var deleteIcon = "data:image/svg+xml,%3C%3Fxml version='1.0' encoding='utf-8'%3F%3E%3C!DOCTYPE svg PUBLIC '-//W3C//DTD SVG 1.1//EN' 'http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd'%3E%3Csvg version='1.1' id='Ebene_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' width='595.275px' height='595.275px' viewBox='200 215 230 470' xml:space='preserve'%3E%3Ccircle style='fill:%23F44336;' cx='299.76' cy='439.067' r='218.516'/%3E%3Cg%3E%3Crect x='267.162' y='307.978' transform='matrix(0.7071 -0.7071 0.7071 0.7071 -222.6202 340.6915)' style='fill:white;' width='65.545' height='262.18'/%3E%3Crect x='266.988' y='308.153' transform='matrix(0.7071 0.7071 -0.7071 0.7071 398.3889 -83.3116)' style='fill:white;' width='65.544' height='262.179'/%3E%3C/g%3E%3C/svg%3E";
    var img = document.createElement('img');
    img.src = deleteIcon;



    // Array to store the history of canvas states
    var canvasHistory = [];
    var currentStateIndex = -1;



    canvas.on('after:render', function () {
        var ctx = canvas.getContext('2d');
        var gridSize = canvas.grid;

        // Configura las reglas del grid con un patrón de líneas
        ctx.strokeStyle = createGridPattern();
        ctx.lineWidth = 1;

        // Dibuja las reglas verticales
        for (var i = gridSize; i < canvas.width; i += gridSize) {
            ctx.beginPath();
            ctx.moveTo(i, 0);
            ctx.lineTo(i, canvas.height);
            ctx.stroke();
        }

        // Dibuja las reglas horizontales
        for (var j = gridSize; j < canvas.height; j += gridSize) {
            ctx.beginPath();
            ctx.moveTo(0, j);
            ctx.lineTo(canvas.width, j);
            ctx.stroke();
        }
    });

    // Configura el tamaño del grid
    canvas.grid = 12;  // Puedes ajustar el tamaño del grid según tus necesidades

    // Función para crear un patrón de líneas para el grid



    // Function to save the current state of the canvas
    function saveCanvasState() {
        currentStateIndex++;
        canvasHistory[currentStateIndex] = JSON.stringify(canvas.toDatalessObject());
        canvasHistory = canvasHistory.slice(0, currentStateIndex + 1);  // Remove redo states
    }

    // Function to undo the last action
    function undo() {

        if (currentStateIndex > 0) {
            currentStateIndex--;
            loadCanvasState();
        }
    }

    // Function to redo the undone action
    function redo() {
        if (currentStateIndex < canvasHistory.length - 1) {
            currentStateIndex++;
            loadCanvasState();
        }
    }

    // Function to load a specific state into the canvas
    function loadCanvasState() {
        canvas.loadFromJSON(canvasHistory[currentStateIndex], function () {
            canvas.renderAll();
        });
    }

    // Your existing functions...

    // Add event listeners for undo and redo buttons
    document.getElementById('undo').addEventListener('click', undo);
    document.getElementById('redo').addEventListener('click', redo);

    // Your existing script...

    var grid = 12;

    // create grid





    canvas.on('object:moving', function(options) {
        if (Math.round(options.target.left / grid * 2) % 1 == 0 &&
            Math.round(options.target.top / grid * 2) % 1 == 0) {
            options.target.set({
                left: Math.round(options.target.left / grid) * grid,
                top: Math.round(options.target.top / grid) * grid
            }).setCoords();
        }
    });




    function Add() {
        if (editingEnabled) {
            var rect = new fabric.Rect({
                left: Math.round(canvas.width / 3.5 / grid) * grid, // Snap to grid horizontally
                top: Math.round(canvas.height / 3.5 / grid) * grid, // Snap to grid vertically

                fill: 'lightgray',
                width: 300,
                height: 12,
                objectCaching: false,
                stroke: 'gray',
                strokeWidth: 1,
                resizable: false,
                selectable: true,
                hasControls: true,  // Activa los controles predeterminados
            });



            canvas.add(rect);
            canvas.setActiveObject(rect);
        }
    }



    function AddSvg() {
        var svgElement = '<svg width="80" height="30" xmlns="http://www.w3.org/2000/svg"><rect width="48" height="24" fill="lightgray" /></svg>';

        var ventana = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"> <!-- Marco de la ventana --> <rect x="10" y="10" width="80" height="80" fill="#c0c0c0" stroke="#000" stroke-width="1"/> <!-- Vidrio de la ventana --> <rect x="20" y="20" width="60" height="60" fill="#a0dfff" stroke="#000" stroke-width="1"/> <!-- Representación de la vista superior --> <line x1="50" y1="0" x2="50" y2="100" stroke="#000" stroke-width="0.5"/> <line x1="0" y1="50" x2="100" y2="50" stroke="#000" stroke-width="0.5"/> </svg>'

        var puerta = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"> <!-- Pared izquierda --> <rect x="0" y="0" width="10" height="200" fill="#c0c0c0" stroke="#000" stroke-width="2"/> <!-- Pared derecha --> <rect x="190" y="0" width="10" height="200" fill="#c0c0c0" stroke="#000" stroke-width="2"/> <!-- Pared superior --> <rect x="10" y="0" width="180" height="10" fill="#c0c0c0" stroke="#000" stroke-width="2"/> <!-- Pared inferior --> <rect x="10" y="190" width="180" height="10" fill="#c0c0c0" stroke="#000" stroke-width="2"/> <!-- Puerta --> <rect x="80" y="180" width="40" height="5" fill="#8b4513" stroke="#000" stroke-width="1"/> <rect x="100" y="120" width="5" height="60" fill="#8b4513" stroke="#000" stroke-width="1"/> <!-- Manija de la puerta --> <circle cx="105" cy="185" r="2" fill="#000"/> <!-- Representación de la vista superior --> <line x1="100" y1="0" x2="100" y2="200" stroke="#000" stroke-width="0.5"/> <line x1="0" y1="100" x2="200" y2="100" stroke="#000" stroke-width="0.5"/> </svg>';

        var svgElement = '<svg width="12" height="12" xmlns="http://www.w3.org/2000/svg"> <rect width="12" height="12" style="fill:white;stroke:black;stroke-width:2" /> <rect width="6" height="12" x="6" style="fill:white;stroke:black;stroke-width:2" /> </svg>'
        fabric.loadSVGFromString(svgElement, function(objects, options) {
            var svg = fabric.util.groupSVGElements(objects, options);
            svg.set({
                left: Math.round(canvas.width / 3.5 / grid) * grid, // Snap to grid horizontally
                top: Math.round(canvas.height / 3.5 / grid) * grid, // Snap to grid vertically
                selectable: editingEnabled,
                showControls: false
            });
            canvas.add(svg);
            canvas.setActiveObject(svg);
        });
    }

    var lastTextNumber = 0; // Variable para llevar el registro del último número de texto

    function AddCircle() {
        if (editingEnabled) {
            var circle = new fabric.Circle({
                left: Math.round(canvas.width / 3.5 / grid) * grid, // Snap to grid horizontally
                top: Math.round(canvas.height / 3.5 / grid) * grid, // Snap to grid vertically

                radius: 15,
                fill: 'red',
                cursorStyle: 'pointer',
                selectable: true
            });

            circle.on('mouse:over', function (e) {
                if (!circle.editable) {
                    canvas.hoverCursor = 'pointer';
                    canvas.renderAll();
                }
            });

            // Evento mouse:out para restaurar el cursor a 'move'
            circle.on('mouse:out', function (e) {
                canvas.hoverCursor = 'move';
                canvas.renderAll();
            });

            var text = new fabric.Text('' + (++lastTextNumber), {
                left: circle.left,  // Ajusta la posición X para centrar el texto
                top: circle.top,    // Ajusta la posición Y para centrar el texto
                fontSize: 15,
                cursorStyle: 'pointer',
                fontWeight: 'bold',
                originX: 'left',

                selectable: false
            });


            var group = new fabric.Group([circle, text], { selectable: true });

            text.set({
                left: circle.left*2,
                top: circle.top/2
            });

            canvas.add(group);
            canvas.setActiveObject(group);

            canvas.renderAll();
        }
    }

    function ToggleCircleEdit() {
        var activeObject = canvas.getActiveObject();
        if (activeObject && activeObject.item(0) && activeObject.item(0).type === 'circle') {
            activeObject.item(0).editable = !activeObject.item(0).editable;
        }
        canvas.renderAll();
    }


    // Mover el evento 'mousedown' fuera de la función AddCircle
    canvas.on('mouse:down', function(options) {
        if (!editingEnabled && options.target && options.target.type === 'group') {
            console.log('¡Círculo clickeado!' + JSON.stringify(options));
        }
    });

    canvas.on('mouse:dblclick', function(options) {
        var target = options.target;
        if (target && target.type === 'group' && target.containsPoint(options.e)) {
            var newText = prompt("Introduce el nuevo texto:");
            if (newText !== null) {
                target.item(1).set('text', newText);
                canvas.renderAll();
            }
        }
    });

    function ToggleEdit() {
        editingEnabled = !editingEnabled;
        canvas.forEachObject(function(obj) {
            obj.selectable = editingEnabled;
        });

        document.getElementById('editStatus').innerText = editingEnabled ? 'Editing: Enabled' : 'Editing: Disabled';
    }

    function SaveCanvas() {
        var json = JSON.stringify(canvas.toJSON(['selectable']));

        // Copiar al portapapeles
        var textarea = document.createElement('textarea');
        textarea.value = json;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);

        alert('JSON copiado al portapapeles.');
    }

    function LoadCanvas() {
        canvas.clear();
        var jsonString = prompt("Introduce el JSON aquí:");
        if (jsonString) {
            canvas.loadFromJSON(jsonString, function() {
                canvas.forEachObject(function(obj) {
                    obj.selectable = editingEnabled;
                    if (obj.type === 'group' && obj._objects) {
                        obj._objects.forEach(function(innerObj) {
                            innerObj.selectable = editingEnabled;
                        });
                    }
                });
                canvas.renderAll();
            });
        }
    }

    function renderIconClone(ctx, left, top, styleOverride, fabricObject) {
        var size = 14;
        ctx.save();
        ctx.translate(left, top);
        ctx.rotate(fabric.util.degreesToRadians(fabricObject.angle));
        ctx.drawImage(img, -size/2, -size/2, size, size);
        ctx.restore();
    }

    function renderIcon(ctx, left, top, styleOverride, fabricObject) {
        var size = this.cornerSize;
        ctx.save();
        ctx.translate(left, top);
        ctx.rotate(fabric.util.degreesToRadians(fabricObject.angle));

        ctx.fillStyle = 'blue';
        ctx.fillRect(-size/2, -size/2, size, size);

        ctx.restore();
    }

    fabric.Object.prototype.controls.deleteControl = new fabric.Control({
        x: 0.5,
        y: -0.5,
        offsetY: -16,
        offsetX: 16,
        cursorStyle: 'pointer',
        mouseUpHandler: deleteObject,
        render: renderIconClone,
        cornerSize: 10
    });

    fabric.Object.prototype.controls.clone = new fabric.Control({
        x: -0.5,
        y: -0.5,
        offsetY: -16,
        offsetX: -16,
        cursorStyle: 'pointer',
        mouseUpHandler: cloneObject,
        render: renderIcon,
        cornerSize: 10
    });


    function deleteObject(eventData, transform) {
        var target = transform.target;
        var canvas = target.canvas;
        canvas.remove(target);
        canvas.requestRenderAll();
    }

    function cloneObject(eventData, transform) {
        var target = transform.target;
        var canvas = target.canvas;
        target.clone(function(cloned) {
            cloned.left += 10;
            cloned.top += 10;
            canvas.add(cloned);
        });
    }

    var zoomLevel = 1.0;



    // Maneja el evento mouse:wheel para actualizar el texto del botón
    canvas.on('mouse:wheel', function (opt) {
        var delta = opt.e.deltaY;
        if (delta > 0) {
            zoomLevel /= 1.1;
        } else {
            zoomLevel *= 1.1;
        }
        canvas.zoomToPoint(canvas.getPointer(opt.e), zoomLevel);
        opt.e.preventDefault();
        opt.e.stopPropagation();

        // Actualiza el texto del botón después de hacer zoom
        updateZoomButtonText();
    });




    var isDragging = false;
    var lastPosX = 0;
    var lastPosY = 0;

    canvas.on('mouse:down', function (opt) {
        var evt = opt.e;
        if (evt.altKey) { // Detectar si la tecla Alt está presionada
            isDragging = true;
            lastPosX = evt.clientX;
            lastPosY = evt.clientY;
        }
    });

    canvas.on('mouse:move', function (opt) {
        if (isDragging) {
            var e = opt.e;
            var deltaPosX = e.clientX - lastPosX;
            var deltaPosY = e.clientY - lastPosY;
            lastPosX = e.clientX;
            lastPosY = e.clientY;

            canvas.relativePan({ x: deltaPosX, y: deltaPosY });
        }
    });

    canvas.on('mouse:up', function () {
        isDragging = false;
    });


    function ResetZoom() {
        zoomLevel = 1.0;
        canvas.setViewportTransform([1, 0, 0, 1, 0, 0]); // Resetea la transformación de la vista
        canvas.renderAll();
    }

    var resetZoomButton = document.getElementById('resetZoom');

    // Añade esta función para resetear el zoom y actualizar el texto del botón
    function ResetZoom() {
        zoomLevel = 1.0;
        canvas.setViewportTransform([1, 0, 0, 1, 0, 0]); // Resetea la transformación de la vista
        canvas.renderAll();

        // Actualiza el texto del botón
        updateZoomButtonText();
    }

    // Añade esta función para actualizar el texto del botón según el nivel de zoom
    function updateZoomButtonText() {
        resetZoomButton.textContent = 'Reset Zoom (' + Math.round(zoomLevel * 100) + '%)';
    }

    // ... (resto de tu código)

    // Llama a la función al inicio para establecer el texto inicial del botón
    updateZoomButtonText();

    function RotateChunks() {
        if (editingEnabled) {
            var activeObject = canvas.getActiveObject();
            if (activeObject) {
                var chunks = activeObject._objects || [activeObject];
                var angleIncrement = 45; // Puedes ajustar el ángulo según tus necesidades

                chunks.forEach(function(chunk) {
                    var newAngle = (chunk.angle + angleIncrement) % 360;
                    chunk.set('angle', newAngle).setCoords();
                });

                canvas.renderAll();
            }
        }
    }

    function ChangeNumber() {
        if (editingEnabled) {
            var activeObject = canvas.getActiveObject();
            if (activeObject) {
                var chunks = activeObject._objects || [activeObject];

                var newText = prompt("Introduce el nuevo texto:");
                if (newText !== null) {
                    // Verifica si el objeto es un grupo para actualizar el texto correctamente
                    if (activeObject.type === 'group' && chunks.length >= 2) {
                        chunks[1].set('text', newText);
                    } else {
                        // Si no es un grupo, actualiza el texto del objeto directamente
                        activeObject.set('text', newText);
                    }

                    canvas.renderAll();
                }
            }
        }
    }

    // Maneja el evento de tecla presionada
    document.addEventListener('keydown', function (e) {
        // Verifica si la tecla presionada es el código de la tecla "Backspace" (8)
        if (e.keyCode === 8) {
            e.preventDefault(); // Evita el comportamiento predeterminado (por ejemplo, retroceder en la página)
            deleteSelectedObjects();
        }
    });


    // Función para eliminar el objeto seleccionado
    function deleteSelectedObject() {
        var activeObject = canvas.getActiveObject();

        if (activeObject) {
            // Si es un grupo, elimina todos los objetos dentro del grupo
            if (activeObject.type === 'group' && activeObject._objects) {
                activeObject._objects.forEach(function(innerObj) {
                    canvas.remove(innerObj);
                });
            } else {
                // Si no es un grupo, elimina el objeto directamente
                canvas.remove(activeObject);
            }

            canvas.discardActiveObject();
            canvas.renderAll();
        }
    }


    // ...

    // Maneja el evento de tecla presionada
    document.addEventListener('keydown', function (e) {
        // Verifica si la tecla presionada es una tecla de flecha
        if (canvas.getActiveObject() && e.keyCode >= 37 && e.keyCode <= 40) {
            e.preventDefault(); // Evita el comportamiento predeterminado (por ejemplo, scroll de la página)

            var activeObject = canvas.getActiveObject();
            var step = 5; // Puedes ajustar el tamaño del paso según tus necesidades

            // Mueve el objeto en la dirección de la flecha presionada
            switch (e.keyCode) {
                case 37: // Flecha izquierda
                    activeObject.set('left', activeObject.left - step);
                    break;
                case 38: // Flecha arriba
                    activeObject.set('top', activeObject.top - step);
                    break;
                case 39: // Flecha derecha
                    activeObject.set('left', activeObject.left + step);
                    break;
                case 40: // Flecha abajo
                    activeObject.set('top', activeObject.top + step);
                    break;
            }

            activeObject.setCoords();
            canvas.renderAll();
        }
    });

    // ...
    // Maneja el evento de tecla presionada
    document.addEventListener('keydown', function (e) {
        // Verifica si la tecla presionada es la tecla "C" y si la tecla de control (Ctrl o Command) está presionada
        if ((e.key === 'c' && e.ctrlKey) || (e.key === 'c' && e.metaKey)) {
            e.preventDefault(); // Evita el comportamiento predeterminado (por ejemplo, copiar el texto en la página)
            copySelectedObject();
        }

        // Verifica si la tecla presionada es la tecla "V" y si la tecla de control (Ctrl o Command) está presionada
        if ((e.key === 'v' && e.ctrlKey) || (e.key === 'v' && e.metaKey)) {
            e.preventDefault(); // Evita el comportamiento predeterminado (por ejemplo, pegar el texto en la página)
            pasteCopiedObject();
        }
    });

    // Resto del código

    // Función para copiar el objeto seleccionado
    function copySelectedObject() {
        var activeObject = canvas.getActiveObject();
        if (activeObject) {
            fabric.copiedObject = activeObject.clone();
        }
    }

    // Función para pegar el objeto copiado
    function pasteCopiedObject() {
        if (fabric.copiedObject) {
            fabric.copiedObject.set({
                left: fabric.copiedObject.left + 10, // Ajusta la posición al pegar
                top: fabric.copiedObject.top + 10,   // Ajusta la posición al pegar
                selectable: true
            });

            canvas.add(fabric.copiedObject);
            canvas.setActiveObject(fabric.copiedObject);
            canvas.renderAll();
        }
    }

    function deleteSelectedObject() {
        var activeObject = canvas.getActiveObject();
        if (activeObject) {
            // Si es un grupo, elimina todos los objetos dentro del grupo
            if (activeObject.type === 'group' && activeObject._objects) {
                activeObject._objects.forEach(function(innerObj) {
                    canvas.remove(innerObj);
                });
            } else {
                // Si no es un grupo, elimina el objeto directamente
                canvas.remove(activeObject);
            }

            canvas.discardActiveObject();
            canvas.renderAll();
        }
    }



    var isDrawingMode = false;

    // Agrega un evento de clic al botón de dibujo
    document.getElementById('toggleDrawing').addEventListener('click', function() {
        toggleDrawingMode();
        updateDrawingStatus();
    });

    function toggleDrawingMode() {
        isDrawingMode = !isDrawingMode;
        canvas.isDrawingMode = isDrawingMode;

        if (isDrawingMode) {
            // Si el modo de dibujo está habilitado, deshabilita la selección de objetos
            canvas.selection = false;
        } else {
            // Si el modo de dibujo está deshabilitado, habilita la selección de objetos
            canvas.selection = true;
        }
    }

    // Evento de tecla para salir del modo de dibujo al presionar la tecla "Esc"
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            isDrawingMode = false;
            canvas.isDrawingMode = false;
            canvas.selection = true;
            updateDrawingStatus();
        }
    });

    function updateDrawingStatus() {
        var drawingStatusElement = document.getElementById('drawingStatus');
        if (isDrawingMode) {
            drawingStatusElement.textContent = 'Drawing';
        } else {
            drawingStatusElement.textContent = 'Not Drawing';
        }
    }

    // Agrega un evento de cambio al slider de tamaño de pincel
    document.getElementById('brushSizeSlider').addEventListener('input', function() {
        var newSize = parseInt(this.value, 10);
        updateBrushSize(newSize);
    });

    // ...

    function updateBrushSize(newSize) {
        canvas.freeDrawingBrush.width = newSize;
        document.getElementById('brushSizeLabel').textContent = 'Brush Size: ' + newSize;
    }

    document.addEventListener('keyup', ({ keyCode, ctrlKey } = event) => {
        // Check Ctrl key is pressed.
        if (!ctrlKey) {
            return
        }

        // Check pressed button is Z - Ctrl+Z.
        if (keyCode === 90) {
            canvas.undo()
        }

        // Check pressed button is Y - Ctrl+Y.
        if (keyCode === 89) {
            canvas.redo()
        }
    })


    // Agrega un evento de cambio al selector de color de dibujo
    document.getElementById('drawingColor').addEventListener('input', function() {
        var newColor = this.value;
        updateDrawingColor(newColor);
    });

    function updateDrawingColor(newColor) {
        canvas.freeDrawingBrush.color = newColor;
    }


    // Event listener for object added to the canvas
    canvas.on('object:added', function () {
        saveCanvasState();
    });

    // Event listener for object modified on the canvas
    canvas.on('object:modified', function () {
        saveCanvasState();
    });



    function deleteSelectedObjects() {
        var activeObjects = canvas.getActiveObjects();

        if (activeObjects && activeObjects.length > 0) {
            activeObjects.forEach(function(obj) {
                canvas.remove(obj);
            });

            canvas.discardActiveObject();
            canvas.requestRenderAll();
            saveCanvasState();
        }
    }




</script>
</body>
</html>
