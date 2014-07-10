<?php
//------------------------------------------------------------------------
// Работа с почтой
//------------------------------------------------------------------------

class Mail{
	
	//--------------------------------------------------------------------
	// Отправка почты
	//--------------------------------------------------------------------
	/**
	* @( string )$to 		= Получатель письма
	* @( string )$subject	= Тема письма
	* @( string )$message 	= Сообщение
	* @( string )$from 		= Отправитель
	*
	* # bool	
	*/
	public static function send( $to, $subject, $message, $from ){
		$headers = self::Headers();
		$subject = self::Subject( $subject );
		$from = self::From( $from );
		
		if( mail( $to, $subject, $message, $headers ) ) 
			return true; 
		else 
			return false; 
	}
	
	//--------------------------------------------------------------------
	// Установка заголовков страницы
	//--------------------------------------------------------------------
	private static function Headers(){
		$headers = "Content-type: text/plain; charset=\"utf-8\"\r\n"; 
		$headers .= "From: <". $from .">\r\n"; 
		$headers .= "MIME-Version: 1.0\r\n"; 
		$headers .= "Date: ". date('D, d M Y h:i:s O') ."\r\n"; 

		return $headers; 
	}
	
	//--------------------------------------------------------------------
	// Формирование темы письма
	//--------------------------------------------------------------------
	private static function Subject( $subject ) { 
       return '=?utf-8?b?'. base64_encode( $subject ) .'?='; 
	}	 
	
	//--------------------------------------------------------------------
	// Отправитель письма
	//--------------------------------------------------------------------
	private static function From( $from ) 
	{ 
		return trim( preg_replace( '/[\r\n]+/', ' ', $from ) ); 
	}
	
}
