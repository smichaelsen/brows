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
                    './assets/css/style.min.css': ['./assets/css/swipebox.css', './assets/css/style.css']
                }
            }
        },
        sass: {
            dist: {
                files: {
                    'assets/css/style.css' : 'assets/sass/style.scss',
                    'assets/css/materialize.css' : 'assets/sass/materialize.scss',
                    'assets/css/swipebox.css' : 'assets/swipebox/scss/swipebox.scss'
                }
            }
        },
        uglify: {
            my_target: {
                files: {
                    'assets/main.js': ['assets/jquery-2.1.1.min.js', 'assets/swipebox/src/js/jquery.swipebox.js']
                }
            }
        },
        watch: {
            css: {
                files: ['assets/sass/*.scss', 'assets/swipebox/scss/*.scss'],
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