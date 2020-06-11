import Pickr from '../../../node_modules/@simonwep/pickr/dist/pickr.min';

export default class ColorPicker {
    static create(el, input) {
        if($(el).length > 0) {
            const defaultValue = $(el).data('default');

            const colorPicker = Pickr.create({
                el,
                default: defaultValue,
                components: {
                    preview: true,
                    opacity: true,
                    hue: true,
                    interaction: {
                        hex: true,
                        rgba: true,
                        hsla: true,
                        hsva: true,
                        cmyk: true,
                        input: true,
                        clear: true,
                        save: true
                    }
                },
            });

            colorPicker.on('save', (color, instance) => {
                const colorCode = color ? color.toHEXA().toString() : '';

                input.val(colorCode);
            });
        }
    }
}
