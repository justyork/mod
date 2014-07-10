<?php

class Auth {

    public static $tbl_session = 'users_sessions';
    public static $tbl_users = 'users';
    public  $sid;
    public  $uid;
    public  $user_agent;
    public  $ip;

    # Логин

    public static function Login($name, $psw) {
        $name = $_POST['name'];
        $psw = $_POST['password'];

        $errors = array();
        $user = MSQL::Row(self::$tbl_users, "`login` = '{$name}'");
        if (!$user)
            $errors[] = 'Пользователь не найден';
        $pass = md5($psw . $user['salt'] . SECRET_KEY);
        // echo SECRET_KEY;
		 
        if ( $pass != $user['password'])
            $errors[] = 'Не верный пароль';

        if (count($errors) == 0) {
            self::CreateSession($user['id']);
            return true;
        }
        else
            return false;
    }

    # Выход

    public static function Logout() {
        $user_id = self::GetUid();
        self::DeleteSessionUser($user_id);
    }

    public static function Get( $field = null ) {
        $user_id = self::GetUid();

        $data = MSQL::Row(self::$tbl_users, " `id` = '{$user_id}'");
        $ret = $field == null ? $data : $data[$field];
		if( isset( $data ) )
			return $ret;
		else return null;
    }

    # Удаляем сессию по юзеру

    private static function DeleteSessionUser($user_id) {
        MSQL::Delete(self::$tbl_session, "`user_id` = '{$user_id}'");
        $_SESSION['sid'] = null;
        return true;
    }

    # Создаем сессию

    public static function CreateSession($user_id) {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $ip = $_SERVER['REMOTE_ADDR'];

        $sid = JL::Random(20, 2);
        $session = md5($sid . $user_agent . $ip);

        $obj = array(
            'sid' => $session,
            'user_id' => $user_id,
            'session_start' => time(),
            'last_active' => time()
        );
        MSQL::Insert(self::$tbl_session, $obj);

        $_SESSION['sid'] = $sid;
        return $sid;
    }

    # Читстим сессии

    public static function clearSessions() {
        $min = time() - 60 * 40;
        $t = "last_active < '%s'";
        $where = sprintf($t, $min);
        MSQL::Delete(self::$tbl_session, $where);
    }

    # проверка пользователя на моготу

    public static function Can( ) {
        if (self::CheckSession())
            return true;
        else
            return false;
    }
    
    
    public static function Access( $access ){
        
        if( !self::Can() )
            return false;

	    $user_group = self::Get( 'group' );

	    $q = "SELECT * FROM `action_p` as `a`
                    INNER JOIN `action_groups_p` AS `ag`
                        ON `a`.`id` = `ag`.`id_action`
                    INNER JOIN `groups` AS `g`
                        ON `g`.`id` = `ag`.`id_groups`
                    WHERE `a`.`name` = '{$access}'
                        AND `g`.`id` = '{$user_group}'";

        $data = MSQL::Select( $q );
         
        if( count( $data ) != 0 )
            return true;
        else
            return false;
    }
    public static function CheckSession() {
        $sid = self::GetSid();
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $session = md5($sid . $user_agent . $ip);

        $ses = MSQL::Row(self::$tbl_session, "`sid` = '{$session}'");
        if ($ses) {
            $_SESSION['sid'] = $sid;
            return true;
        } else {
            $_SESSION['sid'] = null;
            return false;
        }
    }

    # Обновление ссессии

    private static function UpdateSession( $sid ) {
        $obj = array(
            'last_active' => time()
        );
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $session = md5($sid . $user_agent . $ip);

        $where = "`sid` = '{$session}'";
        MSQL::Update(self::$tbl_session, $obj, $where);
    }
    
    public static function GetUserById( $id ) {
        return MSQL::Row( 'users', "`id` = '{$id}'" );
    }
    
    public static function GetUid() {
        // Проверка кеша.
        if ( $uid != null)
            return  $uid;

        // Берем по текущей сессии.
        $sid = self::GetSid();
        if ($sid == null)
            return null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $session = md5($sid . $user_agent . $ip);


        $result = MSQL::Row(self::$tbl_session, "`sid` = '{$session}'");


        // Если сессию не нашли - значит пользователь не авторизован.
        if (!$result)
            return null;

        // Если нашли - запоминм ее.
        $uid = $result['user_id'];

        return $uid;
    }

    //
    // Функция возвращает идентификатор текущей сессии
    // результат	- SID
    //
    public static function GetSid() {

        // Проверка кеша.
        if ( $sid != null)
            return  $sid;

        // Ищем SID в сессии.
        $sid = $_SESSION['sid'];

        // Если нашли, попробуем обновить last_active в базе. 
        
        if ($sid != null)
            self::UpdateSession($sid);
        else
            $sid = null;

        // Возвращаем, наконец, SID.
        return $sid;
    }
    public static function log( $comment = '' ){
        $ip = $_SERVER['REMOTE_ADDR'];
        $user = self::GetUid();
        
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        
        $object = array(
            'user'  => $user,
            'ip'    => $ip,
            'page'  => $url,
            'comment'  => $comment,
            'date'  => date( 'Y-m-d H:i:s' )
        );
        
        MSQL::Insert( 'logs', $object );
           
    }

}