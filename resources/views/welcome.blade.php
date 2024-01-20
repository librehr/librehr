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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fabric-history@1.7.0/src/index.min.js"></script>
</head>
<body>
<div class="controls">

        <div>
        <button id="rotateChunks" onclick="RotateChunks()">Rotate Selected</button>
        <button id="rotateChunks" onclick="ChangeNumber()">Change Number</button>
        <button id="toggleEdit" onclick="ToggleEdit()">Toggle <span id="editStatus">Editing: Enabled</span></button>
        <button id="resetZoom" onclick="ResetZoom()">Reset Zoom</button>
        <button id="save" onclick="SaveCanvas()">Save Canvas</button>
        <button id="load" onclick="LoadCanvas()">Load Canvas</button>
    </div>

    <div>
        <button id="toggleDrawing"><span id="drawingStatus">Not Drawing</span></button>
        <input type="range" id="brushSizeSlider" min="1" max="50" step="1" value="5">
        <span id="brushSizeLabel">Brush Size: 5</span>
        <label for="drawingColor">Drawing Color:</label>
        <input type="color" id="drawingColor" value="#000000">
    </div>
    <div>
        <button id="add" onclick="Add()">Add a wall</button>
        <button id="addSvg" onclick="AddSvg()">Add SVG</button>
        <button id="addCircle" onclick="AddCircle()">Add Circle</button>
    </div>



    <div class="controls">
        <!-- Otros controles -->

        <label for="drawingColor">Drawing Color:</label>
        <input type="color" id="drawingColor" value="#000000">
    </div>
</div>
<canvas id="c" width="800" height="500" style="border: 1px solid #ccc;"></canvas>

<script>
    var canvas = new fabric.Canvas('c');
    var editingEnabled = true;
    var deleteIcon = "data:image/svg+xml,%3C%3Fxml version='1.0' encoding='utf-8'%3F%3E%3C!DOCTYPE svg PUBLIC '-//W3C//DTD SVG 1.1//EN' 'http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd'%3E%3Csvg version='1.1' id='Ebene_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' width='595.275px' height='595.275px' viewBox='200 215 230 470' xml:space='preserve'%3E%3Ccircle style='fill:%23F44336;' cx='299.76' cy='439.067' r='218.516'/%3E%3Cg%3E%3Crect x='267.162' y='307.978' transform='matrix(0.7071 -0.7071 0.7071 0.7071 -222.6202 340.6915)' style='fill:white;' width='65.545' height='262.18'/%3E%3Crect x='266.988' y='308.153' transform='matrix(0.7071 0.7071 -0.7071 0.7071 398.3889 -83.3116)' style='fill:white;' width='65.544' height='262.179'/%3E%3C/g%3E%3C/svg%3E";
    var img = document.createElement('img');
    img.src = deleteIcon;



    fabric.Object.prototype.transparentCorners = true;
    fabric.Object.prototype.borderColor = 'red';
    fabric.Object.prototype.cornerColor = 'green';
    fabric.Object.prototype.cornerSize = 5;
    fabric.Object.prototype.transparentCorners = false;

    fabric.Grid = fabric.util.createClass(fabric.Rect, {
        type: 'grid',
        initialize: function(options) {
            options || (options = {});

            this.callSuper('initialize', options);
            this.set({
                fill: 'white',
                stroke: options.lineColor || 'rgba(0, 0, 0, 0.3)',
                strokeWidth: options.lineWidth || 1,
                selectable: false
            });
        },
        toObject: function() {
            return fabric.util.object.extend(this.callSuper('toObject'));
        }
    });

    fabric.Grid.fromObject = function(object, callback) {
        return callback(new fabric.Grid(object));
    };


    // Crear un fondo de cuadrícula no editable
    var grid = new fabric.Grid({
        width: canvas.width,
        height: canvas.height,
        lineColor: 'rgba(0, 0, 0, 0.5)',  // Color de las líneas de la cuadrícula
        lineWidth: 1,                       // Ancho de las líneas de la cuadrícula
        selectable: false                   // Hace que la cuadrícula no sea seleccionable
    });

    // Añadir la cuadrícula al lienzo como fondo
    canvas.setBackgroundImage(grid.toDataURL(), canvas.renderAll.bind(canvas));


    function Add() {
        if (editingEnabled) {
            var rect = new fabric.Rect({
                left: 100,
                top: 50,
                fill: 'lightgray',
                width: 300,
                height: 10,
                objectCaching: false,
                stroke: 'gray',
                strokeWidth: 1,
                selectable: true,
            });



            canvas.add(rect);
            canvas.setActiveObject(rect);
        }
    }

    function AddSvg() {
        var svgElement = '<svg width="100" height="50" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="50" fill="lightgray" /></svg>';

        fabric.loadSVGFromString(svgElement, function(objects, options) {
            var svg = fabric.util.groupSVGElements(objects, options);
            svg.set({
                left: 50,
                top: 150,
                selectable: editingEnabled
            });
            canvas.add(svg);
            canvas.setActiveObject(svg);
        });
    }

    var lastTextNumber = 0; // Variable para llevar el registro del último número de texto

    function AddCircle() {
        if (editingEnabled) {
            var circle = new fabric.Circle({
                left: 200,
                top: 150,
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
            deleteSelectedObject();
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

</script>
</body>
</html>
