<?php
/**
 * Copyright (c) 2015 Lukas Reschke lukas@owncloud.com
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OCP\AppFramework\Http;

use OCP\AppFramework\Http;

/**
 * Class ContentSecurityPolicyHelper is a simple helper which allows applications
 * to modify the Content-Security-Policy sent by ownCloud. Per default only
 * JavaScript, stylesheets, images, fonts, media and connections from the same
 * domain ('self') are allowed.
 *
 * Even if a value gets modified above defaults will still get appended. Please
 * notice that ownCloud ships already with sensible defaults and those policies
 * should require no modification at all for most use-cases.
 *
 * @package OCP\AppFramework\Http
 */
class ContentSecurityPolicyHelper {
	/** @var bool Whether inline JS snippets are allowed */
	private $inlineScriptAllowed = false;
	/**
	 * @var bool Whether eval in JS scripts is allowed
	 * TODO: Disallow per default
	 * @link https://github.com/owncloud/core/issues/11925
	 */
	private $evalScriptAllowed = true;
	/** @var array Domains from which scripts can get loaded */
	private $allowedScriptDomains = [
		'\'self\'',
	];
	/**
	 * @var bool Whether inline CSS is allowed
	 * TODO: Disallow per default
	 * @link https://github.com/owncloud/core/issues/13458
	 */
	private $inlineStyleAllowed = true;
	/** @var array Domains from which CSS can get loaded */
	private $allowedStyleDomains = [
		'\'self\'',
	];
	/** @var array Domains from which images can get loaded */
	private $allowedImageDomains = [
		'\'self\'',
	];
	/** @var array Domains to which connections can be done */
	private $allowedConnectDomains = [
		'\'self\'',
	];
	/** @var array Domains from which media elements can be loaded */
	private $allowedMediaDomains = [
		'\'self\'',
	];
	/** @var array Domains from which object elements can be loaded */
	private $allowedObjectDomains = [];
	/** @var array Domains from which iframes can be loaded */
	private $allowedFrameDomains = [];
	/** @var array Domains from which fonts can be loaded */
	private $allowedFontDomains = [
		'\'self\'',
	];

	/**
	 * Whether inline JavaScript snippets are allowed or forbidden
	 * @param bool $state
	 * @return $this
	 */
	public function inlineScriptState($state = false) {
		$this->inlineScriptAllowed = $state;
		return $this;
	}

	/**
	 * Whether eval in JavaScript is allowed or forbidden
	 * @param bool $state
	 * @return $this
	 */
	public function evalScriptState($state = true) {
		$this->evalScriptAllowed= $state;
		return $this;
	}

	/**
	 * Allows to execute JavaScript files from a specific domain. Use * to
	 * allow JavaScript from all domains.
	 * @param string $domain Domain to whitelist. Any passed value needs to be properly sanitized.
	 * @return $this
	 */
	public function addAllowedScriptDomain($domain) {
		$this->allowedScriptDomains[] = $domain;
		return $this;
	}

	/**
	 * Whether inline CSS snippets are allowed or forbidden
	 * @param bool $state
	 * @return $this
	 */
	public function inlineStyleState($state = true) {
		$this->inlineStyleAllowed = $state;
		return $this;
	}

	/**
	 * Allows to execute CSS files from a specific domain. Use * to allow
	 * CSS from all domains.
	 * @param string $domain Domain to whitelist. Any passed value needs to be properly sanitized.
	 * @return $this
	 */
	public function addAllowedStyleDomain($domain) {
		$this->allowedStyleDomains[] = $domain;
		return $this;
	}

	/**
	 * Allows using fonts from a specific domain. Use * to allow
	 * fonts from all domains.
	 * @param string $domain Domain to whitelist. Any passed value needs to be properly sanitized.
	 * @return $this
	 */
	public function addAllowedFontDomain($domain) {
		$this->allowedFontDomains[] = $domain;
		return $this;
	}

	/**
	 * Allows embedding images from a specific domain. Use * to allow
	 * images from all domains.
	 * @param string $domain Domain to whitelist. Any passed value needs to be properly sanitized.
	 * @return $this
	 */
	public function addAllowedImageDomain($domain) {
		$this->allowedImageDomains[] = $domain;
		return $this;
	}

	/**
	 * To which remote domains the JS can bind to.
	 * @param string $domain Domain to whitelist. Any passed value needs to be properly sanitized.
	 * @return $this
	 */
	public function addAllowedConnectDomain($domain) {
		$this->allowedConnectDomains[] = $domain;
		return $this;
	}

	/**
	 * To which remote domains the JS can bind to.
	 * @param string $domain Domain to whitelist. Any passed value needs to be properly sanitized.
	 * @return $this
	 */
	public function addAllowedMediaDomain($domain) {
		$this->allowedMediaDomains[] = $domain;
		return $this;
	}

	/**
	 * From which domains objects such as <object>, <embed> or <applet> are executed
	 * @param string $domain Domain to whitelist. Any passed value needs to be properly sanitized.
	 * @return $this
	 */
	public function addAllowedObjectDomain($domain) {
		$this->allowedObjectDomains[] = $domain;
		return $this;
	}

	/**
	 * Which domains can be embedded in an iframe
	 * @param string $domain Domain to whitelist. Any passed value needs to be properly sanitized.
	 * @return $this
	 */
	public function addAllowedFrameDomain($domain) {
		$this->allowedFrameDomains[] = $domain;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPolicy() {
		$policy = "default-src 'none';";

		if(!empty($this->allowedScriptDomains)) {
			$policy .= 'script-src ' . implode(' ', $this->allowedScriptDomains);
			if($this->inlineScriptAllowed) {
				$policy .= ' \'unsafe-inline\'';
			}
			if($this->evalScriptAllowed) {
				$policy .= ' \'unsafe-eval\'';
			}
			$policy .= ';';
		}

		if(!empty($this->allowedStyleDomains)) {
			$policy .= 'style-src ' . implode(' ', $this->allowedStyleDomains);
			if($this->inlineStyleAllowed) {
				$policy .= ' \'unsafe-inline\'';
			}
			$policy .= ';';
		}

		if(!empty($this->allowedImageDomains)) {
			$policy .= 'img-src ' . implode(' ', $this->allowedImageDomains);
			$policy .= ';';
		}

		if(!empty($this->allowedFontDomains)) {
			$policy .= 'font-src ' . implode(' ', $this->allowedFontDomains);
			$policy .= ';';
		}

		if(!empty($this->allowedConnectDomains)) {
			$policy .= 'connect-src ' . implode(' ', $this->allowedConnectDomains);
			$policy .= ';';
		}

		if(!empty($this->allowedMediaDomains)) {
			$policy .= 'media-src ' . implode(' ', $this->allowedMediaDomains);
			$policy .= ';';
		}

		if(!empty($this->allowedObjectDomains)) {
			$policy .= 'object-src ' . implode(' ', $this->allowedObjectDomains);
			$policy .= ';';
		}

		if(!empty($this->allowedFrameDomains)) {
			$policy .= 'frame-src ' . implode(' ', $this->allowedFrameDomains);
			$policy .= ';';
		}

		return rtrim($policy, ';');
	}
}
