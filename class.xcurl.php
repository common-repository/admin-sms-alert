<?php

// @author Arvin Castro
// @email  arvin@sudocode.net
// @url    http://sudocode.net/source/includes/class-xcurl-php/
// @date   June 28, 2010

class xcurl_profile {
    
    private $name;
    
    function xcurl_profile($name = null) {
        if(!$name) $name = md5(time().mt_rand());
        $this->name = $name;
    }
    
    function post($url, $var, $options=null) {
        $data = ($options) ? $options: array();
        $data['post'] = $var;
        return $this->request($url, $data);
    }
    
    function get($url, $var=null, $options=null) {
        $data = ($options) ? $options: array();
        $data['get'] = $var;
        return $this->request($url, $data);
    }
    
    function call   ($url, $data=null) { return $this->request($url, $data); }
    function fetch  ($url, $data=null) { return $this->request($url, $data); }
    function request($url, $data=null) {
        $data['profile'] = $this->name;
        return xcurl::request($url, $data);
    }
    
    function setCacheDirectory($path, $minutes = 5) {
        xcurl::setCacheDirectory($path, $minutes, $this->name);
    }
    
    function clearCookies($url = null) {
        xcurl::clearCookies($url, $this->name);
    }
    
    function setSession($xoauth) {
        xcurl::setSession($xoauth, $this->name);
    }
    
    function setHeaders($headers) {
        xcurl::setHeaders($headers, $this->name);
    }
}

class xcurl {
        
    public static $settings;
    
    function xcurl() {
        
    }
    
    function instance($name=null) { return self::profile($name); }
    function  profile($name=null) {
        return new xcurl_profile($name);
    }
    
    function post($url, $var, $options=null) {
        $data = ($options) ? $options: array();
        $data['post'] = $var;
        return self::request($url, $data);
    }
    
    function get($url, $var=null, $options=null) {
        $data = ($options) ? $options: array();
        $data['get'] = $var;
        return self::request($url, $data);
    }
    
    function call   ($url, $data=null) { return self::request($url, $data); }
    function fetch  ($url, $data=null) { return self::request($url, $data); }
    function request($url, $data=null) {
        
        // Profile
        $profile = ($data['profile']) ? $data['profile'] : 'default';
        
        // xoauth support
        if(self::$settings[$profile]['xoauth']) $data['xoauth'] = self::$settings[$profile]['xoauth'];
        if($data['xoauth'] AND !$data['nosession']) {
            list($url, $data) = $data['xoauth']->sign($url, $data);
        }
        
        $ch = curl_init();
                
        // Set POST, GET, COOKIES, HEADERS
		if($data['post']) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data['post']);
		}		
		if($data['get']) {
		    $url .= '?'. self::toQueryString($data['get'], $data['no-urlencode']);
	    }
		if($data['cookies']) {
		    self::setCookies(self::toCookieString($data['cookies']), $profile);
		}
        if($data['headers']) {
            $headers = array_merge((array) self::$settings[$profile]['headers'], $data['headers']);
        }
        
        // Get cached data if available
        if(self::$settings[$profile]['xcache'] && !$data['post'] && !$data['nocache']) {
            $data = self::getCacheData($url, $profile);
            
            // If there is cached data, return
            if($data) {
                curl_close($ch);
                return $data;
            }
        }
        
		// Get stored cookies
		$cookieString = self::getCookieString($url, $profile);
		if($cookieString) {
    		curl_setopt($ch, CURLOPT_COOKIE, $cookieString);
		}
        
        // Prepare and set headers
        if($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, self::toHeadersArray($headers));
        } elseif(self::$settings[$profile]['headers']) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, self::toHeadersArray(self::$settings[$profile]['headers']));
        }
		
        // Check URL for username and password
		if($user = parse_url($url, PHP_URL_USER)) {
    		if($pass = parse_url($url, PHP_URL_PASS)) $user.= ":$pass";    		
    		// Remove user:pass@ from URL
    		$url = str_replace("$user@", '', $url);
    		$data['userpwd'] = $user;
		}
			
		if($data['userpwd']) {
    		curl_setopt($ch, CURLOPT_USERPWD, $data['userpwd']);
		}
		if(isset($data['ssl-verifypeer'])) {
    	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $data['ssl-verifypeer']);	
		}
		if(isset($data['x-www-form-urlencoded'])) {
    	    curl_setopt($ch, CURLOPT_POST, $data['x-www-form-urlencoded']);	
		}
		if(isset($data['nobody'])) {
    	    curl_setopt($ch, CURLOPT_NOBODY, $data['nobody']);	
		}
		
		curl_setopt($ch, CURLOPT_URL, rtrim($url, '&'));	
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$raw       = curl_exec($ch);
		$error     = curl_error($ch);
		$response  = self::parseResponse($raw);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
				
		$data = array (
		    'url'       => $url,
		    'http_code' => $http_code,
		    'headers'   => $response['headers'],
		    'data'      => $response['data'],
		    'request'   => $data
		);
		
		if($raw === false) $data['error'] = $error;
		switch($http_code) {
    		case 200: case 201: case 202: case 204: case 205: case 206:
    		    $data['successful'] = true;  break;
    		default:
    		    $data['successful'] = false; break;
		}
		
        // Set cookies
        if($data['headers']['set-cookie']) self::setCookies($data['headers']['set-cookie'], $profile);
        
		// Set cache data
        if(self::$settings[$profile]['xcache'] && !$data['post'] && !$data['error']) {
            self::setCacheData($url, $data, $profile);
        }
        
		return $data;
    }
	
	function parseResponse($data) {
    	$blankline = strrpos($data, "\r\n\r\n");
    	$headers   = self::parseHeaders(substr($data, 0, $blankline));
    	$body      = substr($data, $blankline + 4);
    	
    	return array(
    	    'headers' => $headers,
    	    'data' => $body
    	);
	}
	
	function toHeadersArray($assoc) {
    	$array = array();
    	foreach($assoc as $key => $value)
    	    $array[] = "$key: $value";
    	return $array;
	}
    
	function toQueryString($array, $donturlencode = false) {
    	$string = '';
    	foreach($array as $key => $value) {
        	if(!$donturlencode AND $value) $value = urlencode($value);
        	$string .= $key .'='. $value .'&';
    	}
    	return $string;
	}
	
	function fromQueryString($queryString) {
    	$pairs = explode('&', $queryString);    	
    	$array= array();
    	foreach($pairs as $pair) {
    	    list($key, $value) = explode('=', $pair, 2);
    	    $array[$key] = $value;
	    }
    	return $array;
	}
	
	function toCookieString($array) {
    	$string = '';
    	foreach((array) $array as $key => $value)
        	$string .= $key .'='. $value .'; ';
    	return $string;
	}
	
	function fromCookieString($string) {    	
    	$cookies = array();
    	$cookies['data'] = array();
    	
    	$array = explode('; ', rtrim($string, '; '));
        foreach($array as $field) {
            list($key, $value) = explode('=', $field, 2);
            $key = trim($key);
            if(in_array(strtolower($key), array('domain', 'expires', 'path', 'secure', 'comment', 'max-age', 'httponly'))) {
                if(!$value) $value = true;
                $cookies[strtolower($key)] = $value;
            } else {
                $cookies['data'][$key] = $value;
            }
        }
        
        if(!$cookie['domain']) $cookie['domain'] = '*';
    	return $cookies;
	}
	
	// http://www.php.net/manual/en/function.http-parse-headers.php#77241
	function parseHeaders($data) {
        $headers = array();
        $fields  = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $data));
        foreach( $fields as $field ) {
            if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
                $match[1] = strtolower(preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1]))));
                if(isset($headers[$match[1]]) ) {
                    $headers[$match[1]] = array_merge((array) $headers[$match[1]], (array) $match[2]);
                } else {
                    $headers[$match[1]] = trim($match[2]);
                }
            }
        }
        return $headers;
	}
	
	// Custom headers
	function setHeaders($headers, $profile = 'default') {
	    self::$settings[$profile]['headers'] = array_merge((array) self::$settings[$profile]['headers'], $headers);
	}	
	
	// cookies support
	function setCookies($array, $profile) {
    	if(!is_array($array)) $array = array($array);
    	
    	foreach($array as $string) {
            $cookie = self::fromCookieString($string);            
            if(!$cookie['domain']) $cookie['domain'] = '*';            
            if(isset(self::$settings[$profile]['cookies'][$cookie['domain']])) {
                // Merge new data to old one overwriting old values
                if(self::$settings[$profile]['cookies'][$cookie['domain']]['data'])
                    $cookie['data'] = array_merge(self::$settings[$profile]['cookies'][$cookie['domain']]['data'], $cookie['data']);           
            }
            self::$settings[$profile]['cookies'][$cookie['domain']] = $cookie;
        }
	}
	
	function getCookieString($url, $profile) {
    	$url     = parse_url($url);
    	$cookies = array();
    	
    	foreach((array) self::$settings[$profile]['cookies'] as $domain => $cookie) {
        	// Check for URL domain match
            if(false !== strpos($url['host'], $domain) || $domain == '*') { 
                // Add Cookies
                $cookies = array_merge($cookies, $cookie['data']);
            }
        }
        return self::toCookieString($cookies);
	}
	
	function clearCookies($url = null, $profile = 'default') {
    	if($url = null) {
        	self::$settings[$profile]['cookies'] = array();
    	} elseif(self::$settings[$profile]['cookies']) {
        	foreach((array) array_keys(self::$settings[$profile]['cookies']) as $key) {
        	    if(false !== strpos($key, $url))
        	        self::$settings[$profile]['cookies'][$key] = array();
        	}
    	}
	}
	
	// See class.xoauth.php
	function setSession($xoauth, $profile = 'default') {
    	self::$settings[$profile]['xoauth'] = $xoauth;
	}
	
	// Caching support	
	function setCacheDirectory($path, $minutes = 5, $profile = 'default') {
    	if(is_dir($path)) {        	
    	    self::$settings[$profile]['xcache'] = rtrim($path, '/').'/';
    	    self::$settings[$profile]['xcachelength'] = $minutes;
	        return true;	        
	    } else return false;	    
	}
	
	function setCacheData($url, $data, $profile) {
    	$hash = md5($url);
    	file_put_contents(self::$settings[$profile]['xcache'].$hash.'.tmp', serialize($data));
	}
	
	function getCacheData($url, $profile) {
    	$hash = md5($url);
    	$file = self::$settings[$profile]['xcache'].$hash.'.tmp';
    	
    	if(file_exists($file) && (filemtime($file) - mktime()) < (self::$settings[$profile]['xcachelength'] * 60)) {
    	    return unserialize(file_get_contents($file));
	    } else return null;
	}
}

?>
