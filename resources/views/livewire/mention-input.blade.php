<div
    x-data
    x-init='
        ()=> {
            let tribute = new Tribute({
                trigger: "@",
                values: @json($mentionables),

                selectTemplate: function(item) {
                    if (typeof item === "undefined") return null;



            tribute.attach($refs.textarea);
        }
    '
    wire:ignore
>
    <p contenteditable="true" data-tribute="true" x-ref="textarea" class="border"></p>
</div>
