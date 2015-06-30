<?php

/**
 * Basic view class for MVC approach to output
 * This class is based on the view class found
 * in Wolf CMS. Feel free to use under GPL v3
 *
 * This class produces HTML code for a page or section of page from
 * a "template" file that may use the "vars" you supply. A template file
 * is just a file with HTML and PHP code. It doesn't have to represent
 * an entire page. The file must have the extension .php.
 *
 */

class View {

	protected $file; // Template file name. Don't specify extension. PHP will be assumed.
	protected $vars = array(); // Array of template variables

	public function __construct($file = '', $vars = '') {
		if ($file != '') {
			$file .= '.php';
			if (file_exists($file)) {
				$this->file = $file;
			}
			else {		
				throw new Exception("View file not found!");
			}
		}
		if ($vars != '') {
			if (is_array($vars)) {
				$this->vars = $vars;
			}
			else {		
				throw new Exception('Must pass variables in an array!');
			}
		}
	}

/**
 * Returns the parsed content of a template.
 *
 * @return string Parsed content of the view.
 */
	public function __toString() {
		return $this->render();
	}

/**
 * Returns the output of a parsed template as a string.
 *
 * @return string content of parsed template.
 */
	public function render() {
		extract($this->vars, EXTR_OVERWRITE);
		ob_start();
		include $this->file;
		return ob_get_clean();
	}

/**
 * Assigns a specific file to the template.
 *
 * @param mixed $name File name.
 */
	public function assignFile($file) {
		if (file_exists($file)) {
			$this->file = $file;
		}
	}
	
/**
 * Assigns a value to a template variable.
 *
 * @param mixed $name Variable name.
 * @param mixed $value Variable value.
 */
	public function assignVar($var, $value = null) {
		if (is_array($var)) {
			$this->vars = array_merge($this->vars, $var);
		} else {
			$this->vars[$var] = $value;
		}
	}

/**
 * Displays the rendered template in the browser.
 */
	public function display() {
		echo $this->render();
	}

}
