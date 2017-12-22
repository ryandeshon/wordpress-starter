<?php
		// This unique salt is generated from your wordpress security keys.
		// Prevent direct access to this file.
		defined( "ABSPATH" ) or die();
		
		function gfef_get_salt() {
			$salt = "50a5e0200def80edf7024df9230e1f8a";
			if (strpos($salt, "%%SALT%%")) {
				$salt = false;
			} 
			return $salt;
		}
