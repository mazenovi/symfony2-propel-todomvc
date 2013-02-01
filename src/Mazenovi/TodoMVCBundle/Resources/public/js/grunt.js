
module.exports = function(grunt) {
  grunt.initConfig({

    less: {
      compile: {
        options: {
          paths: ["../css"]
        },
        files: {
          "../css/styles.css": ["assets/main.less"],
          "../css/ie7.css": ["../../bmatznerfontawesome/less/font-awesome-ie7.less"]
        }
      }
    },

    concat: {
      dist: {
        src: [
          "../../bmatznerrequire/js/require.min.js",
          "../../fosjsrouting/js/router.js",
        ],
        dest: "build/require.js",
        separator: ";"
      }
    },

  	requirejs: {
      mainConfigFile: "main.js",
      out: "build/app.js",
      name: "main"
    },

    mincss: {
      "../css/all.css": [
        "../css/styles.css",
        "../css/base.css"
      ]
    }

  });

  /*
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-css');
  grunt.loadNpmTasks('grunt-requirejs');
  */
  grunt.registerTask("debug", "concat requirejs less mincss");  
};

