<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imagen Interactiva con Alpine.js</title>
    <script src="//unpkg.com/alpinejs" defer></script>
    @vite('resources/css/filament/app/theme.css')

    <style>
        #container {
            position: relative;
            width: 600px;
            height: 600px;
            display: grid;
            grid-template-columns: repeat(16, 1fr);
            grid-template-rows: repeat(16, 1fr);
        }

        .number {
            position: absolute;
            cursor: pointer;
        }

        .preview {
            position: absolute;
            background-color: rgba(0, 255, 0, 0.3);
            border: 2px solid #00FF00;
            pointer-events: none;
        }
    </style>
</head>

<body x-data="app"
      class="font-sans">

<select x-model="selectedNumber" class="p-2 m-2 bg-blue-500 text-white rounded">
    <template x-for="number in numbers">
        <option :value="number" x-text="number"></option>
    </template>
</select>

<button @click="saveToJson" class="p-2 m-2 bg-green-500 text-white rounded">Guardar Posiciones</button>
<button @click="loadFromJson" class="p-2 m-2 bg-blue-500 text-white rounded">Cargar Posiciones</button>


<div id="container" @mousemove="updatePreviewPosition" @click="addNumber" class="relative">
    <img src="{{ asset('css/map.png') }}" alt="Imagen Interactiva" width="600" height="600"
         class="absolute border shadow">

    <!-- Vista previa -->
    <div x-show="showPreview" class="preview" :style="{ left: previewPosition.x + 'px', top: previewPosition.y + 'px', width: container.offsetWidth / 16 + 'px', height: container.offsetHeight / 16 + 'px' }"></div>
</div>
<script>
    const app = {
        gridSize: 16,
        maxElements: 16,
        numbers: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        selectedNumber: 1,
        selectedNumbers: [],
        history: [],
        showPreview: false,
        previewPosition: { x: 0, y: 0 },
        container: null,

        saveToJson() {
            const jsonData = JSON.stringify(this.selectedNumbers);
            const blob = new Blob([jsonData], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'positions.json';
            a.click();
            URL.revokeObjectURL(url);
        },

        async loadFromJson() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'application/json';
            input.addEventListener('change', async (event) => {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = async () => {
                        // Limpiar las posiciones existentes
                        this.selectedNumbers = [];
                       // await this.updateSelectAndNextNumber();

                        const jsonData = JSON.parse(reader.result);
                        this.selectedNumbers = jsonData;

                        await this.updateSelectAndNextNumber();
                        this.renderNumbers();
                    };
                    reader.readAsText(file);
                }
            });
            input.click();
        },

        // ... (código anterior) ...

        async updateSelectAndNextNumber() {
            // Eliminar los números seleccionados de la lista
            this.selectedNumbers.forEach(selectedNumber => {
                const index = this.numbers.indexOf(selectedNumber.number);
                if (index !== -1) {
                    this.numbers.splice(index, 1);
                }
            });



            await this.$nextTick();

            // Establecer el próximo número después de cargar las posiciones
            this.selectedNumber = this.numbers.length > 0 ? this.numbers[0] : null;
        },

        renderNumbers() {
            // Limpiar la pantalla antes de renderizar los números
            //this.container.innerHTML = '';

            // Renderizar los números guardados
            this.selectedNumbers.forEach(num => {
                // Eliminar el número de this.numbers si ya está presente
                const numIndex = this.numbers.indexOf(num.number);
                if (numIndex !== -1) {
                    this.numbers.splice(numIndex, 1);
                }

                const numberDiv = document.createElement('div');
                numberDiv.className = 'number bg-green-500 flex justify-center items-center w-6 h-6 ml-1.5 mt-1.5 rounded-full absolute hover:bg-red-400';
                numberDiv.textContent = num.number;
                numberDiv.style.left = `${num.x}px`;
                numberDiv.style.top = `${num.y}px`;

                numberDiv.addEventListener('click', async () => {
                    await new Promise(resolve => setTimeout(resolve, 0));

                    this.numbers.push(num.number);
                    this.container.removeChild(numberDiv);
                    this.updateSelect();

                    // Eliminar el número de la lista this.selectedNumbers
                    this.selectedNumbers = this.selectedNumbers.filter(num => num.number !== num.number);
                });

                this.container.appendChild(numberDiv);
            });
        },


        addNumber() {
            if (this.showPreview && this.selectedNumber <= this.maxElements) {
                const gridSize = this.gridSize;
                const gridX = Math.floor(this.previewPosition.x / (this.container.offsetWidth / gridSize));
                const gridY = Math.floor(this.previewPosition.y / (this.container.offsetHeight / gridSize));

                const isOccupied = this.selectedNumbers.some(num => {
                    const numGridX = Math.floor(num.x / (this.container.offsetWidth / gridSize));
                    const numGridY = Math.floor(num.y / (this.container.offsetHeight / gridSize));
                    return numGridX === gridX && numGridY === gridY;
                });

                if (!isOccupied) {
                    this.history.push([...this.selectedNumbers]);

                    const newNumber = {
                        number: this.selectedNumber,
                        x: gridX * (this.container.offsetWidth / gridSize),
                        y: gridY * (this.container.offsetHeight / gridSize)
                    };

                    this.selectedNumbers.push(newNumber);

                    const numberDiv = document.createElement('div');
                    numberDiv.className = 'number bg-green-500 flex justify-center items-center w-6 h-6 ml-1.5 mt-1.5 rounded-full absolute hover:bg-red-400';
                    numberDiv.textContent = this.selectedNumber;
                    numberDiv.style.left = `${newNumber.x}px`;
                    numberDiv.style.top = `${newNumber.y}px`;

                    numberDiv.addEventListener('click', async () => {
                        await new Promise(resolve => setTimeout(resolve, 0));

                        this.numbers.push(newNumber.number);
                        this.container.removeChild(numberDiv);
                        this.updateSelect();

                        // Eliminar el número de la lista this.selectedNumbers
                        this.selectedNumbers = this.selectedNumbers.filter(num => num.number !== newNumber.number);
                    });


                    this.container.appendChild(numberDiv);

                    this.numbers = this.numbers.filter(num => num !== newNumber.number);
                    this.updateSelect();

                    this.showPreview = false;
                }
            }
        },


        updatePreviewPosition(event) {
            if (!this.container) {
                return; // Salir si el contenedor aún no está disponible
            }

            const x = event.clientX - this.container.getBoundingClientRect().left;
            const y = event.clientY - this.container.getBoundingClientRect().top;

            const gridSize = this.gridSize;
            const gridX = Math.floor(x / (this.container.offsetWidth / gridSize)) * (this.container.offsetWidth / gridSize);
            const gridY = Math.floor(y / (this.container.offsetHeight / gridSize)) * (this.container.offsetHeight / gridSize);

            this.previewPosition = { x: gridX, y: gridY };
            this.showPreview = true;
        },

        updateSelect() {
            // Actualizar el select después de realizar cambios en los números
            this.numbers.sort((a, b) => a - b); // Ordenar la lista de números de menor a mayor

            // Actualizar el select con los números ordenados
            this.$nextTick(() => {
                this.selectedNumber = this.numbers[0];
            });
        },
    };

    document.addEventListener('DOMContentLoaded', () => {
        app.container = document.getElementById('container');
        Alpine.data('app', () => app);
    });
</script>


</body>

</html>
