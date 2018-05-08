<?php

/**
 * �������� ������������ ��������� ����� ������
 * @package base
 */
class Validator {
    /**
     * ��������� �� ����. ����
     * @param email string
     * @access public
     * @return bool
     */
    function checkEmail ( $email ) {
    	if (!preg_match("/^(\".*\"|[A-Za-z0-9_\x2D]+(\.[A-Za-z0-9_\x2D]+)*)@(\[\d{1,3}(\.\d{1,3}){3}]|[A-Za-z0-9_\x2D]+(\.[A-Za-z0-9_\x2D]+)+)$/",
    	$email, $matches)) {
    		return false;
    	}

    	return true;
    }

    /**
     * ��������� �� ����. login
     *
     * ������ ����� - ������ �����,
     * ���������(�� ������ 2, �� ������ 15) - �����, �����, _ (����� ���.)
     * @param login string
     * @access public
     * @return bool
     */
    function isCorrectLogin ( $login ) {
    	if(ereg("^([A-Za-z]{1})([A-Za-z0-9_]{2,15})$", $login, $matches))
    	return true;
    	else
    	return false;
    }

    /**
     * ��������� �� ����. password
     *
     * �� ������ 3, �� ����� 16 �������� �� ����.:
     * a-zA-Z0-9,_
     * @param password string
     * @access public
     * @return bool
     */
    function isCorrectPassword ( $password ) {
    	if(ereg("^[A-Za-z0-9_,]{3,16}$", $password, $matches))
    	return true;
    	else
    	return false;
    }

    /**
     * ��������� �� ����. url
     * @param url string
     * @access public
     * @return void
     */
    function isCorrectURL ( $url ) {
    	if(!ereg( "^http|https(:\/\/)([_0-9a-zA-Z_-]+\\.){1,}([a-z]{2,3})$", $url ))
    	return false;
    	else
    	return true;
    }

    /**
     * ��������� �� ����. url
     * @param url string
     * @access public
     * @return void
     */
    function isCorrectDate ( $date ) {
    	$ok = false;
    	if ( ereg ( "([0-9]{4})-([0-9]{2})-([0-9]{2})", $date, $regs) ) {
    		if ( checkdate( $regs[2], $regs[3], $regs[1] ) ) {
    			$ok = true;
    		}
    	}

    	return  $ok;
    }

    function validateShortname($shortName) {
    	if (strlen($shortName) == 0)
    	return true;
    	if(ereg("^([A-Za-z0-9_\\-]{1,100})$", $shortName, $matches))
    	//if(ereg("^([A-Za-z0-9_\\]{1,30})$", $shortName, $matches)) //kazancev 21/01/2009
    	//if(ereg("^([A-Za-z0-9_\\]{1,100})$", $shortName, $matches)) //kazancev 30/04/2010
    	return true;
    	else
    	return false;
    }

}

?>