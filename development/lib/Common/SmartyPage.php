<?php
require_once(dirname(__FILE__) . '/../../Smarty/Smarty.class.php');
require_once(dirname(__FILE__) . '/../Server/namespace.php');
require_once(dirname(__FILE__) . '/../External/SmartyValidate/SmartyValidate.class.php');
require_once('Validators/namespace.php');

class SmartyPage extends Smarty
{
	public $Validators;
		
	private $Resources = null;
	private $RootPath = null;
	private $IsValid = true;
	
	public function __construct(Resources &$resources = null, $rootPath = null)
	{
		$base = dirname(__FILE__) . '/../../';
		
		$this->debugging = true;
		$this->template_dir = $base . 'tpl';
		$this->compile_dir = $base . 'tpl_c';
		$this->config_dir = $base . 'configs';
		$this->cache_dir = $base . 'cache';
		
		$this->compile_check = true;
		$this->force_compile = true;
		$this->caching = false;
		
		if (is_null($resources))
		{
			$resources = Resources::GetInstance();
		}
		
		$this->Resources =& $resources;
		$this->RootPath = $rootPath;
		
		$this->register_function('translate', array($this, 'SmartyTranslate'));
		$this->register_function('constant', array($this, 'GetConstant'));
		$this->register_function('html_link', array($this, 'PrintLink'));
		$this->register_function('html_image', array($this, 'PrintImage'));
		$this->register_function('control', array($this, 'DisplayControl'));
		$this->register_function('validator', array($this, 'Validator'));
		
		$this->Validators = new PageValdiators();
		
	}
		
	public function IsValid()
	{
		return $this->IsValid;
	}

	public function PrintLink($params, &$smarty)
	{
		$string = $this->Resources->GetString($params['key']);
		if (!isset($params['title']))
		{
			$title = $string;
		}
		else
		{
			$title = $this->Resources->GetString($params['title']);
		}
		
		return "<a href=\"{$params['href']}\" title=\"$title\">$string</a>";
	}
	
	public function SmartyTranslate($params, &$smarty) 
	{
		/// SHOULD THIS BE CHANGED TO REGISTER THE RESOURCE OBJECT WITH SMARTY AND ACCESS IT FROM THE TEMPLATES? ///
		if (!isset($params['args']))
		{
			return $this->Resources->GetString($params['key'], '');
		}
		return $this->Resources->GetString($params['key'], explode(',', $params['args']));
	}
	
	public function GetConstant($params, &$smarty)
	{
		if (defined($params['echo'])) 
		{
			return eval('return ' . $params['echo'] . ';');
		}
		else
		{
			throw new Exception(sprintf('Constant %s is not defined', $params['echo']));	
		}
	}
	
	public function PrintImage($params, &$smarty)
	{
		$alt = isset($params['alt']) ? $params['alt'] : '';
		$width = isset($params['width']) ? $params['width'] : '';
		$height = isset($params['height']) ? $params['height'] : '';
		$imgPath = sprintf('%simg/%s', $this->RootPath, $params['src']);	
		
		return "<img src=\"$imgPath\" alt=\"$alt\" width=\"\" height=\"\" />";
	}
	
	public function DisplayControl($params, &$smarty)
	{
		$type = $params['type'];
		require_once("Controls/$type.php");
		
		$control = new $type($this);
		
		foreach($params as $key => $val)
		{
			if ($key != 'type')
			{
				$control->Set($key, $val);
			}
		}
		
		$control->PageLoad();
	}
	
	public function Validator($params, &$smarty)
	{
		$validator = $this->Validators->Get($params['id']);
		if (!$validator->IsValid())
		{
			$this->IsValid = false;
			return $this->SmartyTranslate(array('key' => $params['key']), $smarty);
		}
		return;
	}
}
?>