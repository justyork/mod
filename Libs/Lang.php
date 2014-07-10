<?php

class Lang{

    public static function LoadLang( $lang, $dir_lang ) {
	    $file = $dir_lang . '/lang/' . $lang . '.php';
	    if(is_file($file))
            include $file;
	    else
		    include $dir_lang . '/lang/en.php';

    }



	public static function GetLngId(){
		$lang = MSQL::Row('langs', array('code', self::GetLng()));

		return $lang['id'];
	}
	public static function GetLng(){

		if(strpos($_SERVER['REQUEST_URI'], 'adm'))
			$lang = (isset($_GET['lang']) && $_GET['lang'] == 'en') || (isset($_COOKIE['lang']) && $_COOKIE['lang'] == 'en');
		else{
			if(isset($_COOKIE['lang']))
				$lang = $_COOKIE['lang'];
			elseif(isset($_SESSION['lang']))
				$lang = $_SESSION['lang'];
		}

		/*
		 * 		if ($sStr)
			$lang = 'en';
		else
			$lang = 'ru';
*/

		return $lang;
	}
    public static function T( $var){
        $l = self::GetLng();
        self::LoadLang( $l, ADMIN_PATH );
        
        global $lang;

        return !$lang[$var] ? ucfirst(strtolower( $var )) : $lang[$var];
    }


	public static function GetValue($id, $type, $lang, $category){
		if((int)$type == 0){
			$typeRes = MSQL::Row('lang_type', array('name', $type));
			$type = $typeRes['id'];
		}
		if((int)$category == 0){
			$typeRes = MSQL::Row('lang_type', array('name', $type));
			$type = $typeRes['id'];
		}


		$where = array(
			array('item_id', $id),
			array('type_id', $type),
			array('category_id', $category),
			array('lang_id', $lang),
		);
		$data = MSQL::Row('lang_data', $where);
		if(!$data)
			echo 'Перевод не найден';
		//var_dump($data);
		if(empty($data['value'])){
			$typeModel = MSQL::Row('lang_type', $type);
			$categoryModel = MSQL::Row('lang_category', $category);
			if($type == 1){
				$id = array('propid', $id);
			}

			$item = MSQL::Row($typeModel['name'], $id);

			return $item[$categoryModel['name']];
		}
		return $data['value'];
	}
}