<?

require_once("prepend.php");

function isImage($filePath) {
	$s = getimagesize($filePath);
	return in_array($s[2], array(1,2,3,6));
}

$action = (string)$request->getParameter("do");

if ($action == null || $action=="") {
	$action = "_default";
}

$cname = $request->getParameter("cname");
$request->setAttribute("cname", $cname);

$action($request);
function _default(&$request) {
	main($request);
}

function main(&$request) {
	global $session;

	$cdirPath = $request->getParameter("cdir");

	if ($cdirPath === null) {
		$cdirPath = $session->getAttribute("IMG_LIB_LAST_OPENED_CDIR");
	}
	if ($cdirPath === null || $cdirPath == "/") {
		$cdirPath = "";
	}

	$session->setAttribute("IMG_LIB_LAST_OPENED_CDIR", $cdirPath);

	$cdir = ImgLibFolder::findByRelDir($cdirPath);
	$dirs = $cdir->getSubfolders();
	$images = $cdir->getImages();

	$request->setAttribute("cdir", $cdir);
	$request->setAttribute("dirs", $dirs);
	$request->setAttribute("images", $images);

	usetemplate("imglib/main");
}

function image_addform(&$request) {
	global $session;

	$cname = $request->getParameter("cname");
	$cdirPath = $request->getParameter("cdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}
	$cdir = ImgLibFolder::findByRelDir($cdirPath);

	$request->setAttribute("cdir", $cdir);

	usetemplate("imglib/image_add");
}

function image_add(&$request) {
	global $session, $AdminTrnsl;

	$cname = $request->getParameter("cname");
	$cdirPath = $request->getParameter("cdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}
	$cdir = ImgLibFolder::findByRelDir($cdirPath);

	$file = $request->getParameter("file");
	$resize_strategy = $request->getParameter("resize_strategy");

	$tmFilePath = $file->tmp_name;
	$fname = $file->name;

	if ($cdir->containsFile($fname)) {
		$errmsg = $AdminTrnsl["ImgLibImageFileExists"];
	} else if (!isImage($tmFilePath)) {
		$errmsg = $AdminTrnsl["ImgLibBadImageFile"];
	} else {
		$cdir->createImage($tmFilePath, $fname, IMAGE_THUMBNAIL_SIDE, MAX_IMAGE_SIDE, constant("_IMG_RESIZE_STRATEGY_".strtoupper($resize_strategy)));
		header("Location: /admin/imglib.php?cdir=" . $cdir->getRelPath() . "&cname=" . $cname);
		die();
	}

	$request->setAttribute("errmsg", $errmsg);
	$request->setAttribute("cdir", $cdir);

	usetemplate("imglib/image_add");
}

function image_edit(&$request) {
	global $session;

	$cdirPath = $request->getParameter("cdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}
	$cdir = ImgLibFolder::findByRelDir($cdirPath);

	$fname = $request->getParameter("fname");
	$image = $cdir->getImage($fname);

	$request->setAttribute("image", $image);
	$request->setAttribute("cdir", $cdir);

	usetemplate("imglib/image_edit");
}

function image_update(&$request) {
	global $session, $AdminTrnsl;

	$cdirPath = $request->getParameter("cdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}
	$cdir = ImgLibFolder::findByRelDir($cdirPath);

	$fname = $request->getParameter("fname");
	$resize_strategy = $request->getParameter("resize_strategy");

	$image = $cdir->getImage($fname);

	$file = $request->getParameter("file");
	$tmFilePath = $file->tmp_name;

	if (file_exists ($tmFilePath) && filesize($tmFilePath) != 0 && isImage($tmFilePath)) {
		$image->replaceFile($tmFilePath, IMAGE_THUMBNAIL_SIDE, MAX_IMAGE_SIDE, constant("_IMG_RESIZE_STRATEGY_".strtoupper($resize_strategy)));
	} else {
		$request->setAttribute("file_error", $AdminTrnsl["ImgLibBadImageFile"]);
	}

	$request->setAttribute("image", $image);
	$request->setAttribute("cdir", $cdir);

	usetemplate("imglib/image_edit");
}

function image_moveform(&$request) {
	global $session;

	$cname = $request->getParameter("cname");
	$cdirPath = $request->getParameter("oldcdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}
	$cdir = &ImgLibFolder::findByRelDir($cdirPath);
	$fname = $request->getParameter("fname");
	$image = $cdir->getImage($fname);

	$request->setAttribute("image", $image);
	$request->setAttribute("cdir", $cdir);

	usetemplate("imglib/image_move");
}

function image_move(&$request) {
	global $session;

	$cname = $request->getParameter("cname");
	$cdirPath = $request->getParameter("oldcdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}
	$cdir = &ImgLibFolder::findByRelDir($cdirPath);
	$fname = $request->getParameter("fname");
	$image = $cdir->getImage($fname);

	$newCdirPath = $request->getParameter("cdir");
	$newCdir = ImgLibFolder::findByRelDir($newCdirPath);
	$image->moveTo($newCdir);
	header("Location: /admin/imglib.php?cdir=" . $newCdir->getRelPath() . "&cname=" . $cname);
	die();
}

function image_delete(&$request) {
	global $session;

	$cname = $request->getParameter("cname");
	$cdirPath = $request->getParameter("cdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}
	$cdir = ImgLibFolder::findByRelDir($cdirPath);

	$fname = $request->getParameter("fname");
	$image = $cdir->getImage($fname);

	$image->remove();

	header("Location: /admin/imglib.php?cdir=" . $cdir->getRelPath() . "&cname=" . $cname);
	die();
}

function folder_createform(&$request) {
	global $session;

	$cname = $request->getParameter("cname");
	$cdirPath = $request->getParameter("cdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}
	$cdir = ImgLibFolder::findByRelDir($cdirPath);

	$request->setAttribute("cdir", $cdir);

	usetemplate("imglib/folder_create");
}

function folder_create(&$request) {
	global $session;

	$cname = $request->getParameter("cname");
	$cdirPath = $request->getParameter("cdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}
	$cdir = ImgLibFolder::findByRelDir($cdirPath);

	$dname = $request->getParameter("dname");
	if (!$cdir->containsFile($dname)) {
		$cdir->createSubfolder($dname);
		header("Location: /admin/imglib.php?cdir=" . $cdir->getRelPath() . "&cname=" . $cname);
		die();
	} else {
		$errormsg = $AdminTrnsl["ImgLibFolderExists"];
	}

	$request->setAttribute("cdir", $cdir);
	$request->setAttribute("errormsg", $errormsg);

	usetemplate("imglib/folder_create");
}

function folder_renameform(&$request) {
	global $session;

	$cname = $request->getParameter("cname");
	$cdirPath = $request->getParameter("cdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}

	$cdir = ImgLibFolder::findByRelDir($cdirPath);
	$action = $request->getParameter("do");
	$olddname = $request->getParameter("olddname");
	$dir = $cdir->getSubfolder($olddname);
	$dname = $olddname;

	$request->setAttribute("dname", $dname);
	$request->setAttribute("olddname", $olddname);
	$request->setAttribute("cdir", $cdir);
	$request->setAttribute("dir", $dir);

	usetemplate("imglib/folder_rename");
}

function folder_rename(&$request) {
	global $session;

	$cname = $request->getParameter("cname");
	$cdirPath = $request->getParameter("cdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}
	$cdir = ImgLibFolder::findByRelDir($cdirPath);
	$action = $request->getParameter("do");
	$olddname = $request->getParameter("olddname");
	$dir = $cdir->getSubfolder($olddname);
	$dname = $olddname;

	$dname = $request->getParameter("dname");
	$dir->renameTo($dname);

	header("Location: /admin/imglib.php?cdir=" . $dir->getRelPath() . "&cname=" . $cname);
	die();
}

function folder_moveform(&$request) {
	global $session;

	$cname = $request->getParameter("cname");
	$cdirPath = $request->getParameter("oldcdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}
	$cdir = ImgLibFolder::findByRelDir($cdirPath);

	$request->setAttribute("cdir", $cdir);

	usetemplate("imglib/folder_move");
}

function folder_move(&$request) {
	global $session;

	$cname = $request->getParameter("cname");
	$cdirPath = $request->getParameter("oldcdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}
	$cdir = ImgLibFolder::findByRelDir($cdirPath);

	$newcdir = $request->getParameter("cdir");
	$cdir->moveTo($newcdir);
	header("Location: /admin/imglib.php?cdir=" . $cdir->getRelPath() . "&cname=" . $cname);
	die();
}

function folder_delete(&$request) {
	global $session;

	$cname = $request->getParameter("cname");
	$cdirPath = $request->getParameter("cdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}
	$cdir = ImgLibFolder::findByRelDir($cdirPath);

	$dirName = $request->getParameter("dirName");
	$dir = $cdir->getSubfolder($dirName);
	$dir->remove();

	header("Location: /admin/imglib.php?cdir=" . $cdir->getRelPath() . "&cname=" . $cname);
	die();
}
