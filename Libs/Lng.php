<?php
/**
 * Created by PhpStorm.
 * User: York
 * Date: 05.04.14
 * Time: 20:28
 */

class Lng {
	private $types;
	private $categories;
	private $langs;

	public function __construct(){
		$this->types = JL::ListData(MSQL::Results('lang_type'), 'name');
		$this->categories =  JL::ListData(MSQL::Results('lang_category'), 'name');
		$this->langs = JL::ListData(MSQL::Results('langs'), 'code');


		require(ROOT_PATH.'/langs/lang.'.strtolower($this->GetLng()).'.php');
		$GLOBALS['texts'] = $texts;
	}

	public function GetLng(){
		$lang = false;
		if(isset($_COOKIE['lang']))
			$lang = $_COOKIE['lang'];
		elseif(isset($_SESSION['lang']))
			$lang = $_SESSION['lang'];

		if(!$lang){
			setcookie('lang', 'en', 3600*24*365);
			$lang = 'en';
		}


		return $lang;
	}

	public function GetLngId(){
		$lang = $this->langs[$this->GetLng()];
		return $lang['id'];
	}

	public function T($id, $type, $category, $lang = null){

		if($lang == null)
			$lang = $this->GetLngId();
		if((int)$type == 0)
			$type = $this->types[$type]['id'];
		if((int)$category == 0)
			$category =  $this->categories[$category]['id'];

		$where = array(
			array('item_id', $id),
			array('type_id', $type),
			array('category_id', $category),
			array('lang_id', $lang),
		);
		//var_dump($where);
		$data = MSQL::Row('lang_data', $where);

		if(!$data['value']){
			if($type == 1) $id = array('propid', $id);
			elseif($type == 5) $id = array('news_id', $id);
			$typeRow = '';
			foreach($this->types as $t){
				if($t['id'] == $type){
					$typeRow = $t['name'];
					break;
				}
			}
			$categoryRow = '';
			foreach($this->categories as $c){
				if($c['id'] == $category){
					$categoryRow = $c['name'];
					break;
				}
			}
			$item = MSQL::Row($typeRow, $id);
			return $item[$categoryRow];
		}
		return $data['value'];
	}
} 