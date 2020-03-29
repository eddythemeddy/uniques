module.exports = function(grunt){

	grunt.initConfig({
		less: {
			development: {
				files: {
					'assets/public/css/style.css': 'assets/public/less/scouty.less',
				}
			},
		},
		concat: {
			corecss: {
				src: [
					'node_modules/bootstrap/dist/css/bootstrap.min.css',
					'node_modules/select2/dist/css/select2.min.css',
					'node_modules/jquery.scrollbar/jquery.scrollbar.css',
					'node_modules/simplebar/dist/simplebar.min.css',
					'node_modules/datatables/media/css/jquery.dataTables.min.css',
					'node_modules/jquery-confirm/dist/jquery-confirm.min.css',
					'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',
					'node_modules/daterangepicker/daterangepicker.css',
					'node_modules/highcharts/highcharts.css',
					'assets/public/css/switchery.min.css',
					'assets/public/css/style.css'
				],
				dest: 'assets/dist/css/scouty.core.css'
			},
			corejs: {
				src: [
					'node_modules/jquery-easing/dist/jquery.easing.1.3.umd.min.js',
					'node_modules/jquery-validation/dist/jquery.validate.min.js',
					'node_modules/simplebar/dist/simplebar.min.js',
					'node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
					'node_modules/jquery-confirm/dist/jquery-confirm.min.js',
					'node_modules/jquery-validation/dist/additional-methods.js',
					'node_modules/jquery.scrollbar/jquery.scrollbar.min.js',
					'node_modules/datatables/media/js/jquery.dataTables.min.js',
					'node_modules/datatables-select/dist/js/dataTables.select.min.js',
					'node_modules/moment/min/moment.min.js',
					'node_modules/highcharts/highstock.js',
					'node_modules/highcharts/modules/data.js',
					'node_modules/jquery-form/dist/jquery.form.min.js',
					'node_modules/daterangepicker/daterangepicker.js',
					'node_modules/select2/dist/js/select2.min.js',
					'assets/public/js/switchery.min.js',
					'assets/public/js/main.js'
				],
				dest: 'assets/dist/js/scouty.core.js'
			}
		},
		cssmin: {
		  options: {
		    shorthandCompacting: true,
		    roundingPrecision: -1
		  },
		  target: {
		    files: {
		      'assets/dist/css/scouty.core.min.css': ['assets/dist/css/scouty.core.css']
		    }
		  }
		},
		uglify: {
		  corejs: {
		    files: {
		      'assets/dist/js/scouty.core.min.js': ['assets/dist/js/scouty.core.js']
		    }
		  }
		},
		watch: {
		  styles: {
		    files: [ // which files to watch
		    	'assets/public/less/*.less',
		    	'assets/public/less/**/*.less',
		    	'assets/plugins/*.css',
		    	'assets/public/js/*.js',
		    ],
		    tasks: ['default'],
		    options: {
		      nospawn: true
		    }
		  }
		}
	});

	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-uglify');

	grunt.registerTask('default', ['less','concat','cssmin','uglify']);
};