<?php
/**
 * NU Standard Init Boilerplate
 * Initializes all of our classes required for the plugin to operate.
 * 
 * !!Important!! If a new class is added, you need to add the namespace to the array below.
 *
 * @package NUEDU_Trustpilot\Autoload
 */

namespace NUEDU_Trustpilot\Autoload;

/** Init */
class Init {

	/** 
	 * Array of all classes (namespaces) within this plugin.
	 * 
	 * @var array
	 */
	private $class_names = [
		'Inc\Email_API\Init',
	];

	/** Construct */
	public function __construct() {
		$this->initiate_classes();
	}

	/** Initialize all classes in the $class_names array above */
	public function initiate_classes() {
		foreach ( $this->class_names as $class_name ) {
			$full_name = 'NUEDU_Trustpilot\\' . $class_name;
			new $full_name();
		}
	}
}
