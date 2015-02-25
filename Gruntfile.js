module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        copy: {
            main: {
                files: [
                    {
                        expand: true,
                        flatten: true,
                        src: ['assets/swipebox/src/img/*'],
                        dest: 'assets/build/img/',
                        filter: 'isFile'
                    },
                    {
                        expand: true,
                        flatten: true,
                        src: ['assets/materialize/font/material-design-icons/*'],
                        dest: 'assets/build/font/material-design-icons/',
                        filter: 'isFile'
                    },
                    {
                        expand: true,
                        flatten: true,
                        src: ['assets/materialize/font/roboto/*'],
                        dest: 'assets/build/font/roboto/',
                        filter: 'isFile'
                    }
                ]
            }
        },
        cssmin: {
            options: {
                shorthandCompacting: false,
                roundingPrecision: -1
            },
            target: {
                files: {
                    'assets/build/css/style.min.css': ['assets/build/css/style.css']
                }
            }
        },
        sass: {
            dist: {
                files: {
                    'assets/build/css/style.css' : 'assets/sass/style.scss',
                    'assets/build/css/libraries.css' : 'assets/sass/libraries.scss'
                }
            }
        },
        uglify: {
            my_target: {
                files: {
                    'assets/build/main.js': [
                        'assets/js/jquery-2.1.1.min.js',
                        'assets/swipebox/src/js/jquery.swipebox.js',
                        'assets/materialize/bin/materialize.js',
                        'assets/js/app.js'
                    ]
                }
            }
        },
        watch: {
            css: {
                files: ['assets/sass/*.scss'],
                tasks: ['sass', 'uglify', 'cssmin', 'copy']
            }
        }
    });
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.registerTask('default',['watch']);
};
