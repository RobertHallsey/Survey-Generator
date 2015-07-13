<?php

/**
 * Basic view class for MVC approach to output
 * This class is based on the view class found
 * in Wolf CMS. Feel free to use under GPL v3
 *
 * This class produces takes a template file that may included PHP code
 * and produces HTML code from it, using as necessary the variables passed
 * to it in an array.
 *
 */

class View {

/**
 * The class constructor only stores the template file name and the array of
 * passed values. The two arguments are optional and can be passed later.
 * Once an instance of View is created, it can be used over and over. To
 * use an instance for a different view, simply pass it a different template.
 * Template files must have a .php extension, but the extension must not be
 * specified in the argument.
 */

	protected $file; // Template file name. Don't specify extension. PHP will be assumed.
	protected $vars = array(); // Array of template variables

	public function __construct($file = '', $vars = '') {
		if ($file != '') {
			$file .= '.php';
			if (file_exists($file)) {
				$this->file = $file;
			}
			else {		
				throw new Exception('View file not found:' . $file);
			}
		}
		if ($vars != '') {
			if (is_array($vars)) {
				$this->vars = $vars;
			}
			else {		
				throw new Exception('Variables not in an array:' . $file);
			}
		}
	}

/**
 * Returns the parsed content of a template.
 *
 * This is what allows us to do this:
 * $content = new View($template, $variables)
 *
 * Instead of having to do this:
 * $view = new View($template, $variables)
 * $content = $view->render();
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
