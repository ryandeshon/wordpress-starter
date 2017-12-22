<?php

	class WBM_Template{
		private $args;
		private $file;
 
		public function __get($name) {
			return $this->args[$name];
		}
 
		public function __construct($file, $args = array()) {
			$this->file = $file;
			$this->args = $args;


		}
 
		public function __isset($name){
			return isset( $this->args[$name] );
		}
 
		public function render() {
			if( locate_template($this->file) ){
				include( locate_template($this->file) );
			}
		}
	}


	function render_template($file, $args = array()){
		$file = 'includes/templates/' . $file;
		$template = new WBM_Template($file, $args);
		$template->render();
	}