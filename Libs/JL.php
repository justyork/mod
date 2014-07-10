<?php

/**
 * Class: JustLib
 * Version: 1.0
 *
 * Author: JustYork
 *
 */
class JL {

	public static function renderPart($templatePart, $data = array(), $direcoryPart = '/tpl/'){

		foreach($data as $key => $val){
			$$key = $val;
		}

		ob_start();
		require($_SERVER['DOCUMENT_ROOT'].$direcoryPart.$templatePart.'.php');
		$r = ob_get_contents();
		ob_end_clean();
		return $r;
	}

	/**
	 * Функция проверки на существование получаемых с сервера данных
	 *
	 * @param string
	 * #return bool
	 */
	public static function exist($param) {
		return $result = isset($_REQUEST[$param]) && !empty($_REQUEST[$param]);
	}

	public static function Exis($var){
		return isset($var) && !empty($var);
	}

	public static function ex($name, $value) {
		return isset($_REQUEST[$name]) && $_REQUEST[$name] == $value ? true : false;
	}
	public static function exFile( $name) {
		return isset( $_FILES[$name] ) && $_FILES[$name]['errors'] == 0 ? true : false;
	}
	public static function str_magic_sl($str) {
		return get_magic_quotes_gpc() ? stripslashes($str) : $str;
	}

	/**
	 * Функция обработки текста
	 *
	 * @str string
	 * #return string
	 */
	public static function cstr($str) {
		return trim(strip_tags($str, '<p>'));
	}
	public static function cbool( $bool = null ) {
		return isset( $bool ) ? '1' : '0';
	}

	/**
	 * Функция обработки чисел
	 *
	 * @int int
	 * #int int
	 */
	public static function cint($int) {
		$int = (int) $int != 0 ? $int : false;
		return $int;
	}

	/**
	 * Редирект
	 *
	 * @param string $location
	 */
	public static function redirect($location, $type = 302) {
		header('Location: ' . $location, true, $type);
	}

	/**
	 * Редирект на реферера
	 */
	public static function referer() {
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	}

	public function utf_to_win($str) {
		return iconv('utf-8', 'windows-1251', $str);
	}

	public function win_to_utf($str) {
		return iconv('utf-8', 'windows-1251', $str);
	}

	/**
	 * Преобразование	времени в текстовую строку
	 *
	 * @param datetime $datetime
	 * @param string $language
	 * @return string $date
	 */
	public static function reDate($datetime, $time_ex = false, $language = 'ru') {

		$datetime = self::expDatetime($datetime);

		switch ($datetime['date']['month']) {
			case '01' :
				$month['ru'] = 'Января';
				$month['en'] = 'January';
				break;
			case '02' :
				$month['ru'] = 'Февраля';
				$month['en'] = 'February';
				break;
			case '03' :
				$month['ru'] = 'Марта';
				$month['en'] = 'March';
				break;
			case '04' :
				$month['ru'] = 'Апреля';
				$month['en'] = 'April';
				break;
			case '05' :
				$month['ru'] = 'Мая';
				$month['en'] = 'May';
				break;
			case '06' :
				$month['ru'] = 'Июня';
				$month['en'] = 'June';
				break;
			case '07' :
				$month['ru'] = 'Июля';
				$month['en'] = 'July';
				break;
			case '08' :
				$month['ru'] = 'Августа';
				$month['en'] = 'August';
				break;
			case '09' :
				$month['ru'] = 'Сентября';
				$month['en'] = 'September';
				break;
			case '10' :
				$month['ru'] = 'Октября';
				$month['en'] = 'October';
				break;
			case '11' :
				$month['ru'] = 'Ноября';
				$month['en'] = 'November';
				break;
			case '12' :
				$month['ru'] = 'Декабря';
				$month['en'] = 'December';
				break;
		}
		switch ($datetime['date']['day']) {
			case '01':
				$datetime['date']['day'] = '1';
				break;
			case '02':
				$datetime['date']['day'] = '2';
				break;
			case '03':
				$datetime['date']['day'] = '3';
				break;
			case '04':
				$datetime['date']['day'] = '4';
				break;
			case '05':
				$datetime['date']['day'] = '5';
				break;
			case '06':
				$datetime['date']['day'] = '6';
				break;
			case '07':
				$datetime['date']['day'] = '7';
				break;
			case '08':
				$datetime['date']['day'] = '8';
				break;
			case '09':
				$datetime['date']['day'] = '9';
				break;
		}

		$date = $datetime['date']['day'] . ' ' . $month[$language] . ' ' . $datetime['date']['year'];
		$time = $datetime['time']['hour'] . ':' . $datetime['time']['min'] . ':' . $datetime['time']['sec'];

		$return = ( $time_ex == true ? $date . ' ' . $time : $date );

		return $return;
	}


	public static function dateToTime($datetime){

		list($date, $time) = explode(' ', $datetime);
		list($year, $month, $day) = explode('-', $date);
		list($hour, $min, $sec) = explode(':', $time);

		return mktime($hour, $min, $sec, $month, $day, $year);

	}



	/**
	 * Преобразование  	времени
	 *
	 * @param datetime $datetime
	 * @return array $datetime
	 */
	public static function expDatetime($datetime) {

		$datetime = explode(' ', $datetime);

		$d = explode('-', $datetime[0]);
		$t = explode(':', $datetime[1]);

		$date = array('year' => $d[0], 'month' => $d[1], 'day' => $d[2]);
		$time = array('hour' => $t[0], 'min' => $t[1], 'sec' => $t[2]);

		$datetime = array('date' => $date, 'time' => $time);

		return $datetime;
	}

	public static function msg_success($str, $style = null) {
		$style .= 'border: 1px solid #6aa268; color: #6aa268; padding: 5px; margin: 5px; background: #eaffe9;';
		return '<span style="' . $style . '" >' . $str . '</span>';
	}

	public static function msg_notice($str, $style = null) {
		$style .= 'border: 1px solid #d0cf6c; color: #d0cf6c; padding: 5px; margin: 5px; background: #ffffe9;';
		return '<span style="' . $style . '" >' . $str . '</span>';
	}

	public static function msg_error($str, $style = null) {
		$style .= 'border: 1px solid #c15a64; color: #c15a64; padding: 5px; margin: 5px; background: #ffe9eb;';
		return '<span style="' . $style . '" >' . $str . '</span>';
	}

	public static function str_word($str, $count = 20) {
		$words = explode(' ', $str);
		$new = array();
		if (count($words) < $count)
			$count = count($words);
		$words[$count - 1] = preg_replace('/[-_.,\/\\\%\$\#\@\!\?]/usi', '', $words[$count - 1]);
		for ($i = 0; $i < $count; $i++)
			$new[] = $words[$i];
		$new_str = implode(' ', $new);
		return $new_str . '...';
	}

	/**
	 * $type 	1 - chars
	 * 			2 - symb
	 */
	public static function Random($count = 10, $type = 1) {
		$chars = '1234567890ZYXWVUTSRQPONMLKJIHGFEDCBAzyxwvutsrqponmlkjihgfedcba';
		if ($type == 2)
			$chars .= '!"№;%:?*()_+=-~/\<>,.[]{}';
		$code = "";
		$clen = strlen($chars) - 1;

		while (strlen($code) < $count)
			$code .= $chars[mt_rand(0, $clen)];

		return $code;
	}

	public static function AutoLoad($classname) {
		if ( substr( $classname, 0, 2 ) === 'C_' )
			$classname = ADMIN_PATH . '/core/' . strtolower( $classname );
		else
			$classname = LIBDIR . '/' . $classname;
		@require_once $classname . '.php';
	} // AutoLoad

	/**
	 * Преобразует кирилицу в латиницу
	 * @param type $str
	 * @return type
	 */
	public static function Translit( $str ) {
		$iso9_table = array(
			'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Ѓ' => 'G`',
			'Ґ' => 'G`', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Є' => 'YE',
			'Ж' => 'ZH', 'З' => 'Z', 'Ѕ' => 'Z', 'И' => 'I', 'Й' => 'Y',
			'Ј' => 'J', 'І' => 'I', 'Ї' => 'YI', 'К' => 'K', 'Ќ' => 'K',
			'Л' => 'L', 'Љ' => 'L', 'М' => 'M', 'Н' => 'N', 'Њ' => 'N',
			'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
			'У' => 'U', 'Ў' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'TS',
			'Ч' => 'CH', 'Џ' => 'DH', 'Ш' => 'SH', 'Щ' => 'SHH', 'Ъ' => '``',
			'Ы' => 'YI', 'Ь' => '`', 'Э' => 'E`', 'Ю' => 'YU', 'Я' => 'YA',
			'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'ѓ' => 'g',
			'ґ' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'є' => 'ye',
			'ж' => 'zh', 'з' => 'z', 'ѕ' => 'z', 'и' => 'i', 'й' => 'y',
			'ј' => 'j', 'і' => 'i', 'ї' => 'yi', 'к' => 'k', 'ќ' => 'k',
			'л' => 'l', 'љ' => 'l', 'м' => 'm', 'н' => 'n', 'њ' => 'n',
			'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
			'у' => 'u', 'ў' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts',
			'ч' => 'ch', 'џ' => 'dh', 'ш' => 'sh', 'щ' => 'shh', 'ь' => '',
			'ы' => 'yi', 'ъ' => "'", 'э' => 'e`', 'ю' => 'yu', 'я' => 'ya'
		);

		$str = strtr( $str, $iso9_table );
		$str = preg_replace( "/[^A-Za-z0-9`'_\-\.]/", '-', $str );

		return $str;
	}
	public static function utf8_substr( $str, $from, $len ){
		return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.
			'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',
			'$1',$str);
	}

	public static function ListData($data, $listId = 'id'){
		$return = array();
		foreach($data as $item){
			$return[$item[$listId]] = $item;
		}
		return $return;
	}
}