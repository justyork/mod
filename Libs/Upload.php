<?php

/**
 * Class: Upload
 * Version: 1.0
 *
 * Author: JustYork
 *
 */
class Upload {

    /**
     * Получить разрешение файла
     */
    public static function ext($filename) {
        $path_info = pathinfo($filename);
        return $path_info['extension'];
    }

    /**
     * Загрузка файла на сервер
     *
     * @file 		- ссылка на файл
     * @dir  		- директория куда будет скопирован файл
     * @ext  		- Допустимые расширения файла
     * @filesize	- Максимальный размер файла
     *
     */
    public static function File($file, $rname = 'random', $dir, /* $ext = array(), */ $filesize = 2097152) {
        
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/' . $dir . '/';
        $tmp_file_name = $file['tmp_name'];
        $fsize = $file['size'];
        $ftype = $file['type'];
        $name = $file['name'];
        $fname = pathinfo($name);

        $type = $fname['extension'];
        if( $rname == 'random' )
            $name = md5(sprintf('%x%x', rand(1000, 9999), time()));
        else
            $name = $rname;
        $errors = array();
        //if( !in_array( $fname['extension'], $ext ) )
        //	$errors[] = 'Разширение файла не соответствует допустимым.';

        if (empty($tmp_file_name) or empty($fsize)
                or empty($ftype) or !is_file($tmp_file_name))
            $errors[] = 'Файл не дошёл, код ошибки: ' . $file['error'];

        if (!$fsize)
            $errors[] = 'Странная ошибка =/ возможно файл недоступен или у него нет размера.';
        if ($fsize > $filesize)
            $errors[] = 'Размер загружаемого файла не должен превышать ' . $filesize . ' Байт.';

        // Создаем директорию если нету
        if (!is_dir( $dir ) )
            @mkdir( $dir, 0777, true );
        if (!is_dir( $dir ))
            $errors[] = 'Нет директории для загрузки.';
        //echo $_SERVER['DOCUMENT_ROOT'] . '/' . $dir . $name . '.' . $type;
        // Копируем...
        if (!copy($tmp_file_name, $dir . $name . '.' . $type))
            $errors[] = 'Не удалось скопировать файлы';


        if (count($errors) > 0) {
            @unlink($tmp_file_name);
            return $errors;
        }
        else
            return array('name' => $name, 'type' => $type);
    }

    public static function MultiFileUpload($File, $rname = 'random', $dir, $type = 1,/* $ext, */ $filesize = 2097152) {
        $Fnames = array();
        $i = 0; 
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/' . $dir . '/';
        
		if(!is_dir($dir))
			mkdir($dir, 0777);
		

        foreach ($File['tmp_name'] as $f => $file) {
            
            $tmp_file_name = $file;
            $fsize = $File['size'][$f];
            $ftype = $File['type'][$f];
            $name = $File['name'][$f];

            $fname = pathinfo($name);


            $errors = array();
            if (!$fsize)
                $errors[] = 'Странная ошибка =/ возможно файл недоступен или у него нет размера.';
            if ($fsize > $filesize)
                $errors[] = 'Размер загружаемого файла не должен превышать ' . $filesize . ' Байт.';

            // Создаем директорию если нету
            !is_dir($dir) ? @mkdir($dir, 0777, true) : '';
            if (!is_dir($dir))
                $errors[] = 'Нет директории для загрузки.';
            
            if( $rname == 'random' )
                $name = md5( sprintf( '%x%x', rand(1000, 9999), time() ) ) . '.' . $fname['extension'];
            else{
	            if($type == 1){
		            $picture = MSQL::Row( 'images', "`propid` = '{$rname}'", "`id` DESC" );
		            $file_name = explode( '.', $picture['imgname'] );
					$fragment = explode( 'pic', $file_name[0] );

					if( (int)$fragment[1] != 0 )
						$iterator = $fragment[1] + $i + 1;
					else 
						$iterator = 1 + $i;
					
	            }
	            elseif($type == 2){
		            $pictures = MSQL::Results( 'dev_images', "`dev_id` = '{$rname}'", 'id' );
					
					$maxId = 0;
					foreach($pictures as $pic){
						list($file_name, $ext) = explode( '.', $pic['path'] );
						list($itemId, $imgId) = explode('pic', $file_name);
						 
						
						if($maxId < $imgId)
							$maxId = $imgId;
					}
					 
					$iterator = $maxId + 1;
					
	            }
	            
                $name = $rname . 'pic' . $iterator . '.' . $fname['extension'];
            }
            
            // Копируем...
            if (!copy($tmp_file_name, $dir . $name))
                $errors[] = 'Не удалось скопировать файлы';


            if (count($errors) > 0) {
                @unlink($tmp_file_name);
	            $ret = array('err' => $errors, 'code' => 500);
                return $ret;
            }
            else
                $Fnames[] = $name;
            $i++;
        }
	    $ret = array('files' => $Fnames, 'code' => 200);
        return $ret;
    }


	public static function MultiFile($File, $rname = 'random', $dir, $type = 1, $filesize = 2097152){
		$files = self::MultiFileUpload($File, $rname, $dir, $type, $filesize);

		return $files['files'];

	}

    private static function Name($str_count = 10) {
        $symbols = '0123456789abcdefghijklmnopqrstuvwxyz';
        $filename = substr(str_shuffle($symbols), 0, $str_count);
        return $filename;
    }

}