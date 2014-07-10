<?php

//-----------------------------------------------------------------------------
//Помошник работы с бд
//-----------------------------------------------------------------------------

class MSQL {
    
    public static function Connect($host, $username, $password, $database){
		mysql_connect( $host, $username, $password );
		mysql_select_db( $database );
		
		return true;
	}
    /**
     * Обработать обычный запрос
     * хз зачем сделал, пусть будет,
     * потом придумаю что-нибудь
     * 
     * @param str $query
     * @return obj 
     */
    public static function Query($query) {
        $result = mysql_query($query);
        return $result;
    }
    /**
     * Обработать выборку вогнаную в $query
     * 
     * на выходе массив
     *
     * @param str $query
     * @return array 
     */
    public static function Select($query) {
         
        $result = mysql_query($query);
        
        $arr = array();
        while( $res = mysql_fetch_assoc( $result ) )
            $arr[] = $res;

        return $arr;
    }

    /**
     * Вставка строк в базу
     * 
     * Формируешь массив вида $arr['тут столбец'] = 'значение'
     * либо $arr = array( 'столбец' => 'значение' )
     * и передаешь в функцию MSQL::Insert( 'table', $arr )
     * можно это присвоить переменной получишь вставленный id
     * 
     * @param str $table
     * @param array $object
     * @return int 
     */
    public static function Insert($table, $object) {
        $columns = array();
        $values = array();

        foreach ($object as $key => $value) {
            $key = mysql_real_escape_string($key . '');
            $columns[] = '`'.$key.'`';

            if ($value === null) {
                $values[] = 'NULL';
            } else {
                $value = mysql_real_escape_string($value . '');
                $values[] = "'$value'";
            }
        }

        $columns_s = implode(',', $columns);
        $values_s = implode(',', $values);

        $query = "INSERT INTO $table ($columns_s) VALUES ($values_s)"; 
        $result = mysql_query($query) or die( mysql_error() );

        if (!$result)
            die(mysql_error());
        return mysql_insert_id();
    }

    /**
     * Обновление таблицы
     * 
     * Почти тоже самое что и вверху, 
     * но указываешь условие, 
     * к примеру "`id` = '{$id}'"
     *
     * Возвращает количество затронутых строк
     * 
     * @param str $table
     * @param array $object
     * @param str $where
     * @return type 
     */
    
    public static function Update($table, $object, $where) {
        $sets = array();
        
        foreach ($object as $key => $value) {
            $key = mysql_real_escape_string($key . '');

            if ($value === null) {
                $sets[] = "$key=NULL";
            } else {
                $value = mysql_real_escape_string($value . '');
                $sets[] = "`$key` = '$value'";
            }
        }

        $sets_s = implode(',', $sets);
        
        if(!is_array($where) && (int)$where != 0)
			$where = array('id', $where);
		if(is_array($where))
            $where = self::__where_array($where);
        $query = "UPDATE $table SET $sets_s WHERE $where"; 
        $result = mysql_query($query);

        if (!$result)
            die(mysql_error());

        return mysql_affected_rows();
    }

    /**
     * Удаление
     * 
     * Таблица, условие, все просто
     * 
     * @param str $table
     * @param str $where
     * @return int 
     */
    public static function Delete($table, $where) {
        if(!is_array($where) && (int)$where != 0)
			$where = array('id', $where);
		if(is_array($where))
            $where = self::__where_array($where);
        $query = "DELETE FROM $table WHERE $where"; 
        $result = mysql_query($query);

        if (!$result)
            die(mysql_error());

        return mysql_affected_rows();
    }
    
    /**
     * Тут самое интересное заносишь только 
     * таблицу получаешь все записи из таблицы, 
     * дальше по параметрам делаешь нужный фильтр
     * если надо пропустить что-то делаешь null
     * 
     * на выходе массив
     * 
     * @param str $table
     * @param str $where
     * @param str $order
     * @param str $limit
     * @param str $offset
     * @return array 
     */
    public static function Results($table, $where = null, $order = null, $limit = null, $offset = null, $group = null) {
        $query = "SELECT * FROM `{$table}`";

        if ($where != null){
            if(!is_array($where) && (int)$where != 0)
				$where = array('id', $where);
            if(is_array($where))
                $where = self::__where_array($where);
            $query .= ' WHERE ' . $where;
            
        }
		if ($group != null)
            $query .= ' GROUP BY ' . "`{$group}`";
        if ($order != null)
            $query .= ' ORDER BY ' . $order;

		if ($limit == '-1') 
            $query .= ' LIMIT 100500 ';
        elseif ($limit != null && (int) $limit)
            $query .= ' LIMIT ' . $limit;
		
        if ($offset != null && (int) $offset)
            $query .= ' OFFSET ' . "{$offset}";
            //echo $query;
        $res = mysql_query($query) or die(mysql_error());
         
		
        $arr = array();
        while ($var = mysql_fetch_assoc($res))
            $arr[] = $var;

        return $arr;
    }
    /**
     * также как и в предыдущем,
     * но только получаешь массив с 1 записью из базы
     * если ничего не нашлось то вернет false
     * 
     * @param str $table
     * @param str $where
     * @param str $order
     * @param str $limit
     * @param str $offset
     * @return boolean 
     */
    public static function Row($table, $where = null, $order = null, $limit = null, $offset = null) {
        $query = "SELECT * FROM `{$table}`";

		// var_dump($where);
        if ($where != null)
            if(!is_array($where) && (int)$where != 0)
				$where = array('id', $where);
            if(is_array($where))
                $where = self::__where_array($where);
            $query .= ' WHERE ' . $where;

        if ($order != null)
            $query .= ' ORDER BY ' . $order;

        if ($offset != null && (int) $offset)
            $query .= ' OFFSET ' . $offset;

        if ($limit != null && (int) $limit)
            $query .= ' LIMIT ' . $limit;
		//var_dump($query);	
		//echo $query;
        $res = mysql_query($query)
		or $var = false;
		// or die(mysql_error());
		// echo $var;
		// if((bool)$var === false)
			// return $var;
			
        $var = @mysql_fetch_assoc($res) or $var = false;

        return $var;
    }
    
    /**
     * Количество записей по запросу
     * строится все точно также
     * 
     * @param str $table
     * @param str $where
     * @param str $order
     * @param str $limit
     * @param str $offset
     * @return int 
     */
    public static function CountR($table, $where = null, $order = null, $limit = null, $offset = null) {
        $query = "SELECT * FROM `{$table}`";

        if ($where != null)
			if(!is_array($where) && (int)$where != 0)
				$where = array('id', $where);
            if(is_array($where))
                $where = self::__where_array($where);
            $query .= ' WHERE ' . $where;

        if ($order != null)
            $query .= ' ORDER BY ' . $order;

        if ($offset != null && (int) $offset)
            $query .= ' OFFSET ' . $offset;

        if ($limit != null && (int) $limit)
            $query .= ' LIMIT ' . $limit;



        $res = mysql_query($query) or die(mysql_error());
        return mysql_num_rows($res);
    } 
    
    private static function __where_array($arr) {
        
		$nsep = array('between', 'in');
		
        $where = '';
        // Если условие из одного элемента
        if(!is_array($arr[0])){ 
			if(count($arr) == 3){  
				list($i1, $op, $i2) = self::check($arr[0], $arr[1], $arr[2]);  
				
				if(strtolower($op) == 'in'){
					if(is_array($i2))
						$i2 = "(".implode(',', $i2).")";
					else
						$i2 = "({$i2})"; 
				}
				
				elseif(strtolower($op) == 'between' ){ }
				else $i2 = "'$i2'";
				
				// echo $i2;
			}
			elseif(count($arr) == 2){
				list($i1, $i2) = $arr;
				$op = '='; 
				$i2 = "'$i2'";
			}
			 
			$where = " `$i1` $op $i2 "; 
            return $where;
        }
        // Если элементов много
        $i = 0;
        foreach($arr as $item){
			
            if(in_array(strtolower($item[0]), array('and', 'or'))){
				if(count($item) == 3){
					$i1 = $item[1];
					$i2 = $item[2];
					$op = '=';
				}
				else{
					list($i1, $op, $i2) = self::check($item[1], $item[2], $item[3]);
					if(strtolower($op) == 'in'){
					if(is_array($i2))
						$i2 = "(".implode(',', $i2).")";
					else
						$i2 = "({$i2})"; 
				}
				
				}
				$condit = strtoupper($item[0]);
				
				if(!in_array(strtolower($op), $nsep))
					$i2 = "'$i2'";
					
				$where .= " $condit `$i1` $op $i2 ";
			}
			else{
				if(count($item) == 2){
					$i1 = $item[0];
					$i2 = $item[1];
					$op = '=';
				}
				elseif(count($item) == 3){
					list($i1, $op, $i2) = self::check($item[0], $item[1], $item[2]);
					if(strtolower($op) == 'in'){
						if(is_array($i2))
							$i2 = "(".implode(',', $i2).")";
						else
							$i2 = "{$i2}"; 
					}
				
				}
				
				if(!in_array(strtolower($op), $nsep))
					$i2 = "'$i2'";
					
				// Первый элемент
                if($i == 0)
                    $where = " `$i1` $op $i2 ";
                // Без оператора
                else 
                    $where .= " AND `$i1` $op $i2 "; 	
			}
			   
            $i++;    
        }
        return $where;
    }
    
    private static function check($i1, $op, $i2) {
        $operations = array('=', '!=', '<>', 'between', 'in', '<', 'not in', 'like',
            '>', '<=', '>=');
        $les = array();
        // echo $op;
        $les[0] = mysql_real_escape_string($i1);
        $les[1] = $op;
        $les[2] = $i2;
        if(!in_array(strtolower($op), $operations))
            die('Ахтунг, не верный оператор!');    
        // var_dump($les); 
        return $les;
    }
}