<?

require_once("prepend.php");

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

	$serverFilesDirPath = FIELD_SERVERFILES_DIR;
	$cdirPath = $request->getParameter("cdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}
	$dirs = list_dir_contents_only_dirs($serverFilesDirPath.$cdirPath);
	$files = list_dir_contents_only_files($serverFilesDirPath.$cdirPath);
	//SimpleDateFormat df = new SimpleDateFormat("dd.MM.yyyy HH:mm:ss");
	$cdirname = substr($cdirPath, strrpos($cdirPath, "/")+1);
	$cdir1 = substr($cdirPath, 0, strrpos($cdirPath, '/'));

	$request->setAttribute("cdirname", $cdirname);
	$request->setAttribute("cdir1", $cdir1);
	$request->setAttribute("cdirPath", $cdirPath);

	$request->setAttribute("dirs", $dirs);
	$request->setAttribute("files", $files);

	usetemplate("filelib/main");
}

function dir_createform(&$request) {
	global $session;

	$serverFilesDirPath = FIELD_SERVERFILES_DIR;
	$cdirPath = $request->getParameter("cdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}

	$cname = $request->getParameter("cname");
	$request->setAttribute("cdirPath", $cdirPath);

	usetemplate("filelib/folder_create");
}

function dir_create(&$request) {
	global $session;

	$serverFilesDirPath = FIELD_SERVERFILES_DIR;
	$cdirPath = $request->getParameter("cdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}

	$cname = $request->getParameter("cname");

	$dname = $request->getParameter("dname");
	$newDirPath = $serverFilesDirPath . $cdirPath . "/" . $dname;
	mkdir($newDirPath);

	header("Location: /admin/filelib.php?cdir=" . $cdirPath . "&cname=" . $cname);
	die();
}

function file_uploadform(&$request) {
	global $session;

	$serverFilesDirPath = FIELD_SERVERFILES_DIR;
	$cdirPath = $request->getParameter("cdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}

	$cname = $request->getParameter("cname");
	$cdirname = substr($cdirPath, strrpos($cdirPath, "/")+1);
	$cdir1 = substr($cdirPath, 0, strrpos($cdirPath, '/'));

	$request->setAttribute("cdirPath", $cdirPath);

	usetemplate("filelib/file_upload");
}

function file_upload(&$request) {
	global $session;

	$serverFilesDirPath = FIELD_SERVERFILES_DIR;
	$cdirPath = $request->getParameter("cdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}

	$cname = $request->getParameter("cname");
	$cdirname = substr($cdirPath, strrpos($cdirPath, "/")+1);
	$cdir1 = substr($cdirPath, 0, strrpos($cdirPath, '/'));

	$filevar = $request->getParameter("dname");
	$tmpFilePath = $filevar->tmp_name;
	$fname = $filevar->name;
	$newFilePath = $serverFilesDirPath . $cdirPath . "/" . $fname;
	copy($tmpFilePath, $newFilePath);

	header("Location: /admin/filelib.php?cdir=" . $cdirPath . "&cname=" . $cname);
	die();
}

function rename_form(&$request) {
	global $session;

	$serverFilesDirPath = FIELD_SERVERFILES_DIR;
	$cdirPath = $request->getParameter("cdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}

	$cname = $request->getParameter("cname");

	$fname = $request->getParameter("fname");
	$fpath = $cdirPath . '/' . $fname;
	$file = $serverFilesDirPath . $fpath;
	$newfname = $fname;

	$request->setAttribute("cdirPath", $cdirPath);
	$request->setAttribute("newfname", $newfname);
	$request->setAttribute("fname", $fname);

	usetemplate("filelib/rename");
}

function _rename(&$request) {
	global $session;

	$serverFilesDirPath = FIELD_SERVERFILES_DIR;
	$cdirPath = $request->getParameter("cdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}

	$cname = $request->getParameter("cname");

	$fname = $request->getParameter("fname");
	$fpath = $cdirPath . '/' . $fname;
	$file = $serverFilesDirPath . $fpath;
	$newfname = "";
	$newfname = $request->getParameter("newfname");
	$newFilePath = $serverFilesDirPath . $cdirPath . "/" . $newfname;
	rename($file, $newFilePath);

	header("Location: /admin/filelib.php?cdir=" . $cdirPath . "&cname=" . $cname);
	die();
}

function delete(&$request) {
	global $session;

	$serverFilesDirPath = FIELD_SERVERFILES_DIR;
	$cdirPath = $request->getParameter("cdir");
	if ($cdirPath == null) {
		$cdirPath = "";
	}
	$cname = $request->getParameter("cname");

	$fname = $request->getParameter("fname");
	$fpath = $cdirPath . '/' . $fname;
	$fullPath = $serverFilesDirPath . $fpath;
	if (is_dir($fullPath)) {
		rmdir($fullPath);
	} else {
		@unlink($fullPath);
	}

	header("Location: /admin/filelib.php?cdir=" . $cdirPath . "&cname=" . $cname);
	die();
}