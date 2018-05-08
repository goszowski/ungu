<?php

$GLOBALS["_CLEAR_USERS_CACHE"] = true;
$GLOBALS["_CLEAR_USER_GROUPS_CACHE"] = true;

require_once("prepend.php");

$action = (string)$request->getParameter("do"); 

if ($action == null || $action=="") {
	$action = "_default";
}

checkPrivileges($request);
$action($request);

function checkPrivileges(&$request) {
	global $session;
	$currentLoggedUser = $session->getAttribute(SESSION_LOGGED_USER_ATTR);
	if (!$currentLoggedUser->group->canManageUsers) {
		die(You_Have_not_rights_to_manage_users);
	}
}

function _default(&$request) {
	global $session;

	usetemplate("users/index");
}

function edit(&$request) {
	global $session;
	$user_id = (int)$request->getParameter("user_id");
	$user = User::findById($user_id);

	$request->setAttribute("u", $user);

	usetemplate("users/edit");
}

function update_user(&$request) {
	global $session;
	$user_id = (int)$request->getParameter("user_id");
	$user = User::findById($user_id);

	$request->setAttribute("u", $user);

    $login = $request->getParameter("login");
    $pwd1 = $request->getParameter("passwd1");
    $pwd2 = $request->getParameter("passwd2");
    $userg = $request->getParameter("user_group");
    $email = $request->getParameter("email");

    $us_ex = true;
    $ex_u = User::findByLogin($login);
    $e_u = User::findByEmail($email);
    $email_ok = Validator::checkEmail($email);

    if ($ex_u == null ) {
    	$us_ex = false;
    } else {
    	if ($ex_u->id == $user->id) {
    		$us_ex = false;
    	}
    }
    if (!$us_ex) {
        if (($e_u != null) && ($ex_u != null) && ($ex_u->id != $e_u->id)) {
        	$request->setAttribute("ERROR", "emailbad");
        	usetemplate("users/edit");
            return;
        }
        if (!$email_ok)  {
        	$request->setAttribute("ERROR", "email_invalid");
        	usetemplate("users/edit");
            return;
        }
        if (strlen($login) < 3) {
        	$request->setAttribute("ERROR", "small");
        	usetemplate("users/edit");
            return;
        }
        //if user is changing his password
        if ($pwd1 != $user->password) {
	        if (strlen($pwd1) < 3) {
	        	$request->setAttribute("ERROR", "pwshort");
	    		usetemplate("users/edit");
	            return;
	        }
	    }
        if ($pwd1 == $pwd2) {
            $ug = UserGroup::findById((int)$userg);
            Validator::validateShortname($login);

            $user->login = $login;
            if ($pwd1 != $user->password) {
            	$user->password = $pwd1;
            }
            $user->group = $ug;
            $user->email = $email;
            $user->store();
            usetemplate("users/index");
            return;

		} else {
			$request->setAttribute("ERROR", "pwbad");
    		usetemplate("users/edit");
			return;
		}
	} else {
		$request->setAttribute("ERROR", "dup");
        usetemplate("users/edit");
        return;
	}

	usetemplate("users/edit");
}

function create_user_form(&$request) {
	usetemplate("users/create");
}

function create_user(&$request) {
    $login = $request->getParameter("login");
    $pwd1 = $request->getParameter("passwd1");
    $pwd2 = $request->getParameter("passwd2");
    $userg = $request->getParameter("user_group");
    $email = $request->getParameter("email");

    $email_ok = Validator::checkEmail($email);

	if (User::findByLogin($login) != null) {
		$request->setAttribute("ERROR", "dup");
   		usetemplate("users/create");
		return;
	}
	
	if (User::findByEmail($email) != null) {
		$request->setAttribute("ERROR", "emailbad");
   		usetemplate("users/create");
		return;
	}
	if (strlen($login) < 3) {
		$request->setAttribute("ERROR", "small");
   		usetemplate("users/create");
		return;
	}
	if (!$email_ok)  {
		$request->setAttribute("ERROR", "email_invalid");
   		usetemplate("users/create");
		return;
	}
	
	if (strlen($pwd1) < 3) {
		$request->setAttribute("ERROR", "pwshort");
   		usetemplate("users/create");
		return;
	}
	if ($pwd1 == $pwd2) {
		$ug = UserGroup::findById((int)$userg);
		Validator::validateShortname($login);
		$ug->addUser($login, $pwd1, $email);
		usetemplate("users/index");
	} else {
		$request->setAttribute("ERROR", "pwbad");
   		usetemplate("users/create");
	}
}

function create_group_form(&$request) {
	usetemplate("users/create_group");
}

function create_group(&$request) {
	$name = $request->getParameter("name");
	$desc = $request->getParameter("desc");
	$can_man_class = $request->getParameter("can_man_class") == 1 ? true : false;
	$can_man_user = $request->getParameter("can_man_user") == 1 ? true : false;
	$can_man_imglib = $request->getParameter("can_man_imglib") == 1 ? true : false;
	$restrict_nodeedit = $request->getParameter("restrict_nodeedit") == 1 ? true : false;
	$is_moderator = $request->getParameter("is_moderator") == 1 ? true : false;

	if ( UserGroup::findByName($name) == null ) {
		if (strlen($name) < 2) {
			$request->setAttribute("ERROR", "small");
			usetemplate("users/create_group");
		} else {
			Validator::validateShortname($name);
			$ug = UserGroup::create($name, $desc, $can_man_class, $can_man_user, $can_man_imglib, $restrict_nodeedit, $is_moderator);

			usetemplate("users/index");
		}
	} else {
		$request->setAttribute("ERROR", "dup");
		usetemplate("users/create_group");
    }
}

function edit_group(&$request) {
    $id = (int)$request->getParameter("group_id");
    $ug = UserGroup::findById($id);
    $request->setAttribute("ug", $ug);

	usetemplate("users/edit_group");
}

function update_group(&$request) {
	$group_id = (int)$request->getParameter("group_id");
    $ug = UserGroup::findById($group_id);
	$name = $request->getParameter("name");
	$desc = $request->getParameter("desc");
	$can_man_class = $request->getParameter("can_man_class") == 1 ? true : false;
	$can_man_user = $request->getParameter("can_man_user") == 1 ? true : false;
	$can_man_imglib = $request->getParameter("can_man_imglib") == 1 ? true : false;
	$restrict_nodeedit = $request->getParameter("restrict_nodeedit") == 1 ? true : false;
	$is_moderator = $request->getParameter("is_moderator") == 1 ? true : false;

	$grp_ex = true;
	$e_ug = UserGroup::findByName($name);

    if ($e_ug == null) {
    	$grp_ex = false;
    } else {
        if ($e_ug->id == $ug->id) {
            $grp_ex = false;
        }
    }

    if (!$grp_ex) {
        if (strlen($name) < 2) {
        	$request->setAttribute("ERROR", "small");
   			usetemplate("users/edit_group");
			return;
        } else {
            $ug->name = $name;
            $ug->description = $desc;
            $ug->canManageClasses = $can_man_class;
            $ug->canManageUsers = $can_man_user;
            $ug->canManageImgLib = $can_man_imglib;
            $ug->restrictNodeEdit = $restrict_nodeedit;
            $ug->isModerator = $is_moderator;
            $ug->store();

   			usetemplate("users/index");
        }

    } else {
    	$request->setAttribute("ERROR", "dup");
 		usetemplate("users/edit_group");
    }
}

function remove_user(&$request) {
	global $session;
	$user_id = (int)$request->getParameter("user_id");
	$group_id = (int)$request->getParameter("group_id");
    $ug = UserGroup::findById($group_id);
	$user = User::findById($user_id);
	$ug->delUser($user);
	usetemplate("users/index");
}

function delete_group(&$request) {
	global $session;
	$group_id = (int)$request->getParameter("group_id");
    $ug = UserGroup::findById($group_id);
	$ug->remove();
	usetemplate("users/index");
}
?>