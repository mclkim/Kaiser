<?php

namespace Kaiser;

use Aura\Web\WebFactory;

class Request extends Singleton {
	protected $request;
	function __construct() {
		$factory = new WebFactory ( array (
				'_ENV' => $_ENV,
				'_GET' => $_GET,
				'_POST' => $_POST,
				'_COOKIE' => $_COOKIE,
				'_SERVER' => $_SERVER 
		) );
		
		$this->request = $factory->newRequest ();
	}
	function cookie($key = null, $alt = null) {
		return $this->request->cookies->get ( $key, $alt );
	}
	function url($component = null) {
		return $this->request->url->get ( $component );
	}
	function method() {
		return $this->request->method->get ();
	}
	function post($key = null, $alt = null) {
		return $this->request->post->get ( $key, $alt );
	}
	function get($key = null, $alt = null) {
		return $this->request->query->get ( $key, $alt );
	}
	function get_post($key = null, $alt = null) {
		return empty ( $this->post ( $key ) ) ? $this->get ( $key, $alt ) : $this->post ( $key, $alt );
	}
	function header($key = null, $alt = null) {
		return $this->request->headers->get ( $key, $alt );
	}
	function isXhr() {
		return $this->request->isXhr ();
	}
}
