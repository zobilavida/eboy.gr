import uglify from 'rollup-plugin-uglify';
import multiEntry from 'rollup-plugin-multi-entry';

export default {
    input: [
        'assets/js/src/event-manager.js',
        'assets/js/src/front.js',
        'assets/js/src/front-eboys.js'
    ],
    output: {
        file: 'assets/js/dist/front.min.js',
        name: 'EWP_Build',
        format: 'iife'
    },
    watch: {
        include: 'assets/js/src/**'
    },
    plugins: [
        multiEntry(),
        uglify()
    ]
}
