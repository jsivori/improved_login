<?php
// include and register Twig auto-loader
require_once '../../view/lib/Twig/Autoloader.php'; 
Twig_Autoloader::register();

try {

	// define template directory location
	$loader = new Twig_Loader_Filesystem('../../view/templates');

	// initialize Twig environment
	$twig = new Twig_Environment($loader,array(
			//'debug'=> TRUE,
			//'cache' => 'compilation_cache',
			'auto_reload' => TRUE
		));

	$template = $twig->loadTemplate($parametro_template);
	$template->display($parametro_display);
	
} 
catch (Exception $e) {
	die ('ERROR: ' . $e->getMessage());
}
?>