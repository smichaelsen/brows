module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        cssmin: {
            options: {
                shorthandCompacting: false,
                roundingPrecision: -1
            },
            target: {
                files: {
                    './assets/css/style.min.css': ['./assets/css/style.css']
                }
            }
        },
        sass: {
            dist: {
                files: {
                    'assets/css/style.css' : 'assets/sass/style.scss',
                    'assets/css/materialize.css' : 'assets/sass/materialize.scss'
                }
            }
        },
        uglify: {
            my_target: {
                files: {
                    'assets/main.js': ['assets/jquery-2.1.1.min.js', 'assets/fancybox/jquery.fancybox.js']
                }
            }
        },
        watch: {
            css: {
                files: 'assets/sass/*.scss',
                tasks: ['sass', 'uglify', 'cssmin']
            }
        }
    });
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.registerTask('default',['watch']);
}