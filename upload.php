#!/usr/bin/php
<?php

include 'cli.php';

$u = new Uploader();
$u->init();

class Uploader {
	
	public $username;
	public $password;
	public $fileLocation;
	
	public function init() {
		CLI::nl();
		CLI::colorEcho("File Sonic Uploader Version 1.0", true, CLI::YELLOW, CLI::BLACK, true);
		CLI::nl();
		
		$this->parseUssage();
		$this->login();
		$this->upload();
	}
	
	public function login() {
		CLI::startMSG("Logging In:");
		
		$postdata = "redirect=%2F&email=".$this->username."&password=".$this->password; 
		
		$ch = curl_init(); 
		curl_setopt ($ch, CURLOPT_URL, "http://www.filesonic.com/user/login"); 
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.13 (KHTML, like Gecko) Chrome/9.0.597.107 Safari/534.13"); 
		curl_setopt ($ch, CURLOPT_TIMEOUT, 60); 
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0); 
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt ($ch, CURLOPT_COOKIEJAR, "cookies/{$this->username}.txt"); 
		curl_setopt ($ch, CURLOPT_REFERER, "http://www.filesonic.com/"); 
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata); 
		curl_setopt ($ch, CURLOPT_HTTPHEADER, array('X-Requested-With: XMLHttpRequest', 'Accept: application/json, text/javascript, */*; q=0.01'));
		curl_setopt ($ch, CURLOPT_POST, 1); 
		$response = curl_exec ($ch); 
		$response = json_decode($response);
		
		if($response->status == 'fail') {
			CLI::finishMSG("FAILED", CLI::RED);
			$this->error($response->messages->Authentication[0]);
		} else {
			CLI::finishMSG("OK");
		}
	}
	
	public function upload() {
		CLI::startMSG("Uploading:");
		
		
		$ch = curl_init("http://s120.filesonic.com");
		curl_setopt ($ch, CURLOPT_POSTFIELDS, array('upload[]'=>"@{$this->fileLocation}",'uploadFiles'=>'')); 
    	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt ($ch, CURLOPT_COOKIEFILE, "cookies/{$this->username}.txt"); 		
		$response = curl_exec ($ch); 
		
		CLI::finishMSG("OK");
	}
	
	
	public function error($message) {
		CLI::nl();
		CLI::colorEcho("An Error Has Occurred: ", true, CLI::WHITE, CLI::BLACK, true);
		CLI::colorEcho("\t$message ", true, CLI::RED, CLI::BLACK, true);
		CLI::nl();
		exit;
	}
	
	public function parseUssage() {
		global $argc, $argv;
		
		if($argc == 4) {
			$this->username = $argv[1];
			$this->password = $argv[2];
			$this->fileLocation = $argv[3];
		} else {
			$this->showUssage();
			exit;
		}
	}
		
	public function showUssage() {
		CLI::nl();
		CLI::colorEcho("Correct Ussage: ", true, CLI::WHITE, CLI::BLACK, true);
		CLI::colorEcho("\tupload.php <username> <password> <file> ", true, CLI::WHITE, CLI::BLACK, false);
		CLI::nl();
		
	}
}