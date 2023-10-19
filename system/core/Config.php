<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if(!defined('DEMO_MODE'))
define('DEMO_MODE',false);
$confighost = 'localhost';
$configdatabase = 'soadb';
$configuser = 'soauser';
$configpassword = 'soapass';
$configinstalled = 'NOT_INSTALLED';
$configverssion_id = '711'; 
$configapp_store = '9638'; 
$configapp_version = '7.1.1';
$configapp_name = 'soa';
$configversion_date = '2020-06-17';
$configversion_lastupdate = '2020-06-17';

global $confighost;
global $configdatabase;
global $configuser;
global $configpassword;
global $configinstalled;
global $configapp_store;
global $configverssion_id;
global $configapp_version;
global $configapp_name;
global $configversion_date;

if(!class_exists('CI_Config')) {	
	class CI_Config {
		function db_host() {
			global $confighost;
			return $confighost;
		}
		function db_name() {
			global $configdatabase;
			return $configdatabase;
		}
		function db_username() {
			global $configuser;
			return $configuser;
		}
		function db_password() {
			global $configpassword;
			return $configpassword;
		}
		function installed() {
			global $configinstalled;
			return $configinstalled;
		}				
	}
}

$heda = $enterKey = '';