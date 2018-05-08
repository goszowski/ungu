<?

DEFINE("dtformat", "dd.MM.yyyy HH:mm:ss");

DEFINE('MAX_IMAGE_SIDE', 500);
DEFINE('IMAGE_THUMBNAIL_SIDE', 100);
DEFINE('_IMG_RESIZE_STRATEGY_BYWIDTH', 0);
DEFINE('_IMG_RESIZE_STRATEGY_BYGREATERSIDE', 1);
DEFINE('_IMG_RESIZE_STRATEGY_BYSMALLERSIDE', 2);

function getParentDir($path) {
	return substr($path, 0, strrpos($path, "/"));
}

function FILE_COMPARATOR_BY_NAME ($o1, $o2) {
	$o1d = is_dir($o1);
	$o2d = is_dir($o2);
	if ($o1d && !$o2d) {
		return -1;
	} else if (!$o1d && $o2d) {
		return 1;
	} else {
		if ($o1 == $o2) {
			return 0;
		}
	    return ($o1 < $o2) ? -1 : 1;
	}
}

function FILE_COMPARATOR_BY_LASTMODIFIED ($o1, $o2) {
	$o1d = is_dir($o1);
	$o2d = is_dir($o2);
	if ($o1d && !$o2d) {
		return -1;
	} else if (!$o1d && $o2d) {
		return 1;
	} else {
		$m1 = filemtime($o1);
		$m2 = filemtime($o2);
		if ($m1 == $m2) {
			return 0;
		}
	    return ($m1 < $m2) ? -1 : 1;
	}
}


class ImgLibFolder {
	var $dir = "";

	function ImgLibFolder($path) {
		$this->dir = $path;
		if (!file_exists($this->dir)) {
			die("Can't instantiate ImgLibFolder object for non-existent path : '$path'");
		}
	}

	function getName() {
		return basename($this->dir);
	}

	function getAbsolutePath() {
		return $this->dir;
	}

	function getRelPath() {
		$imglibDirPath = FIELD_IMGLIB_DIR;
		if (strlen($this->dir) == strlen($imglibDirPath)) {
			return "";
		} else {
			return str_replace("\\", "/", substr($this->dir, strlen($imglibDirPath) + 1));
		}
	}

	function getParent() {
		if ($this->isRoot()) {
			return null;
		}
		$relPath = $this->getRelPath();
		$i = strrpos($relPath, "/");
		if ($i !== false) {
			return ImgLibFolder::findByRelDir(substr($relPath, 0, $i));
		} else {
			return ImgLibFolder::findByRelDir("");
		}
	}


	function getParentRelPath() {
		if ($this->isRoot()) {
			return null;
		}
		$relPath = $this->getRelPath();
		$i = strrpos($relPath, "/");
		if ($i !== false) {
			return substr($relPath, 0, $i);
		} else {
			return "";
		}
	}

	function isRoot() {
		$imglibDirPath = FIELD_IMGLIB_DIR;
		return $this->dir == $imglibDirPath;
	}

	function containsFile($fname) {
		return file_exists($this->dir . "/" . $fname);
	}

	function isEmpty() {
		return (sizeof(list_dir_contents($this->dir)) == 0);
	}

	function renameTo($newName) {
		if ($this->isRoot()) {
			return;
		}

		$oldRelPath = $this->getRelPath();
		$parentRelPath = $this->getParentRelPath();
		$newRelPath = $newName;
		if (strlen($parentRelPath) != 0) {
			$newRelPath = $parentRelPath . '/' . $newName;
		}

		rename($this->dir, getParentDir($this->dir) . "/" . $newName);
		$imglibThumbnalesDirPath = FIELD_IMGLIB_THUMBNAILS_DIR;
		$tdir = $imglibThumbnalesDirPath . '/' . $this->getRelPath();
		$parent = $this->getParent();
		rename($tdir, $imglibThumbnalesDirPath . '/' . $parent->getRelPath() . '/' . $newName);
		
		$this->dir = getParentDir($this->dir) . "/" . $newName;
	}

	function moveTo($mdirRelPath) {
		if ($this->isRoot()) {
			return;
		}

		$oldRelPath = $this->getRelPath();
		$newRelPath = $this->getName();
		if (strlen($mdirRelPath) != 0) {
			$newRelPath = $mdirRelPath . '/' . $this->getName();
		}

		$imglibDirPath = FIELD_IMGLIB_DIR;
		$imglibThumbnalesDirPath = FIELD_IMGLIB_THUMBNAILS_DIR;

		rename($this->dir, $imglibDirPath . '/' . $newRelPath);
		$tdir = $imglibThumbnalesDirPath . '/' . $this->getRelPath();
		rename($tdir, $imglibThumbnalesDirPath . '/' . $newRelPath);

		$this->dir = $imglibDirPath . '/' . $newRelPath;
	}

	function getSubfolders() {
		$dirs = list_dir_contents_only_dirs($this->dir);
		$folders = array();

		usort($dirs, "FILE_COMPARATOR_BY_NAME");
		foreach ($dirs as $dir) {
			$folders[] = new ImgLibFolder($dir);
		}

		return $folders;
	}

	function& getSubfolder($fName) {
		if ($this->isRoot()) {
			return ImgLibFolder::findByRelDir($fName);
		} else {
			return ImgLibFolder::findByRelDir($this->getRelPath() . '/' . $fName);
		}
	}

	function getImage($fName) {
		$image = new ImgLibImage($this, $fName);
		return $image;
	}

	function getImages() {
		$imagesFiles = list_dir_contents_only_files($this->dir);
		$images = array();
		usort($imagesFiles, "FILE_COMPARATOR_BY_LASTMODIFIED");
		foreach ($imagesFiles as $i) {
			$images[] = new ImgLibImage($this, basename($i));
		}

		return $images;
	}

	function findRoot() {
		$imglibDirPath =  FIELD_IMGLIB_DIR;
		$folder = new ImgLibFolder($imglibDirPath);
		return $folder;
	}

	public static function findByRelDir($relDir) {
		$imglibDirPath = FIELD_IMGLIB_DIR;
		$imglibThumbnalesDirPath = FIELD_IMGLIB_THUMBNAILS_DIR;

		if (strlen($relDir) == 0) {
			$folder = new ImgLibFolder($imglibDirPath);
		} else {
			if (!file_exists($imglibDirPath . '/' . $relDir) || !file_exists($imglibThumbnalesDirPath . '/' . $relDir)) {
				$subdirs = explode('/', $relDir);
				$dir = "";
				foreach($subdirs as $subdir) {
					$dir .= '/' . $subdir;
					if (!file_exists($imglibDirPath . $dir) || !file_exists($imglibThumbnalesDirPath . $dir)) {
						@mkdir($imglibDirPath . $dir, 0777);
						@mkdir($imglibThumbnalesDirPath . $dir, 0777);
					}
				}
				$relDir = substr($dir, 1);
			}
			$folder = new ImgLibFolder($imglibDirPath . '/' . $relDir);
		}

		return $folder;
	}

	function createSubfolder($name) {
		$sfFiledir = $this->dir . '/' . $name;
		mkdir($sfFiledir, 0777);

		$sf = new ImgLibFolder($sfFiledir);

		$imglibThumbnalesDirPath = FIELD_IMGLIB_THUMBNAILS_DIR;
		$thumbnailsDir = $imglibThumbnalesDirPath . '/' . $sf->getRelPath();
		mkdir($thumbnailsDir, 0777);

		return $sf;
	}

	function& createImage($tmpFilePath, $fileName, $thumbnailSide=IMAGE_THUMBNAIL_SIDE, $maxSide=MAX_IMAGE_SIDE, $resize_strategy=_IMG_RESIZE_STRATEGY_BYWIDTH) {
		$image = &ImgLibImage::create($this, $tmpFilePath, $fileName, $thumbnailSide, $maxSide, $resize_strategy);
		return $image;
	}

	function remove() {
		if (!$this->isEmpty()) {
			die("Can't remove non-empty folder.");
		}
		rmdir($this->dir);
		$imglibThumbnalesDirPath = FIELD_IMGLIB_THUMBNAILS_DIR;
		$thumbnailsDir = $imglibThumbnalesDirPath . '/' . $this->getRelPath();
		rmdir($thumbnailsDir);
	}
}

class Image {
	var $im = null;
	var $width = 0;
	var $height = 0;
	var $isgif = false;
	var $resizable = true;

	function Image($filename = null) {
		if ($filename != null) {
			$this->resizable = true;
			$this->isgif = false;
			$s = getimagesize($filename);
			switch ($s[2]) {
				case 1 :
					$this->im = imagecreatefromgif($filename);
					//echo "gif!<br>";
					$this->isgif = true;
					break;
				case 2 :
					$this->im = imagecreatefromjpeg($filename);
					break;
				case 3 :
					$this->im = imagecreatefrompng($filename);
					break;
				case 6 :
					$this->im = imagecreatefromwbmp($filename);
					break;
				default:
					$this->resizable = false;
					$this->filename = $filename;
					die("Unknown image type: " . $filename);
			}
			$this->width = $s[0];
			$this->height = $s[1];
		}
	}

	function resizeTo($twidth, $theight) {
		$im = imagecreatetruecolor  ($twidth, $theight);
		imagecopyresampled ($im, $this->im, 0, 0, 0, 0, $twidth, $theight, $this->width, $this->height);//imagecopyresized
		$thumbnail = new Image();
		$thumbnail->im = $im;
		$thumbnail->isgif = $this->isgif;
		$thumbnail->resizable = $this->resizable;
		$thumbnail->filename = $this->filename;
		$thumbnail->width = $twidth;
		$thumbnail->height = $theight;
		return $thumbnail;
	}

	function saveTo($filename, $qual=90) {
		if ($this->resizable) {
			if ($this->isgif) {
				imagegif($this->im, $filename);
			} else {
				if (file_exists($filename)) {
					unlink($filename);
				}
				imagejpeg($this->im, $filename, $qual);
			}
		} else {
			$filename = str_replace("//", "/", $filename);
			$this->filename = str_replace("//", "/", $this->filename);
			if ($this->filename != $filename) {
				copy($this->filename, $filename);
				@chmod($filename, 0755);
			}
		}
	}
}

class ImgLibImage {
	var $folder = null;
	var $file = "";
	var $name = "";
	var $width = 0;
	var $height = 0;

	var $thumbnailWidth = 0;
	var $thumbnailHeight = 0;

	function ImgLibImage(&$folder, $fileName) {
		$this->folder = $folder;
		$this->file = $folder->getAbsolutePath() . '/' . $fileName;
		$this->name = basename($this->file);

		$s = @getimagesize($this->file);
		$this->width = $s[0];
		$this->height = $s[1];

		$thumbnailFile = FIELD_IMGLIB_THUMBNAILS_DIR . "/" . $folder->getRelPath() . "/" . $this->name;
		$s = @getimagesize($thumbnailFile);

		$this->thumbnailWidth = $s[0];
		$this->thumbnailHeight = $s[1];
	}

	function getName() {
		return basename($this->file);
	}

	function getHeight() {
		return $this->height;
	}

	function getWidth() {
		return $this->width;
	}

	function getThumbnailHeight() {
		return $this->thumbnailHeight;
	}

	function getThumbnailWidth() {
		return $this->thumbnailWidth;
	}

	function getFileSize() {
		return @filesize($this->file);
	}

	function compileUrl($folderRelPath, $imageName) {
		if (strlen($folderRelPath) == 0) {
			return "/" . $imageName;
		} else {
			return "/" . $folderRelPath . "/" . $imageName;
		}
	}

	function getUrl() {
		$folderRelPath = $this->folder->getRelPath();
		return $this->compileUrl($folderRelPath, $this->getName());
	}

	function replaceFile($newFilePath, $thumbnailSide=IMAGE_THUMBNAIL_SIDE, $maxSide=MAX_IMAGE_SIDE, $resize_strategy=_IMG_RESIZE_STRATEGY_BYWIDTH) {
		ImgLibImage::_resize($this->folder, $this->getName(), $newFilePath, $thumbnailSide, $maxSide, $resize_strategy);
	}

	function& create(&$folder, $tmpFilePath, $fileName, $thumbnailSide=IMAGE_THUMBNAIL_SIDE, $maxSide=MAX_IMAGE_SIDE, $resize_strategy=_IMG_RESIZE_STRATEGY_BYWIDTH) {
		$url = $folder->getRelPath() . '/' . $fileName;

		return ImgLibImage::_resize($folder, $fileName, $tmpFilePath, $thumbnailSide, $maxSide, $resize_strategy);
	}

	function& _resize(&$folder, $fileName, $tmpFilePath, $thumbnailSide=IMAGE_THUMBNAIL_SIDE, $maxSide=MAX_IMAGE_SIDE, $resize_strategy=_IMG_RESIZE_STRATEGY_BYWIDTH) {
		$imglibDirPath = FIELD_IMGLIB_DIR;
		$imglibThumbnalesDirPath = FIELD_IMGLIB_THUMBNAILS_DIR;

		$destFileUrl = str_replace("//", "/", $folder->getRelPath() . "/" . $fileName);
		
		$image = new Image($tmpFilePath);

		$width = $image->width;
		$height = $image->height;

		//do not just copy!! need to restrict by $maxSide pixels
		if ($image->resizable &&
				(
					($resize_strategy == _IMG_RESIZE_STRATEGY_BYWIDTH && $width > $maxSide)
					||
					($resize_strategy == _IMG_RESIZE_STRATEGY_BYGREATERSIDE && ($height > $maxSide || $width > $maxSide))
					||
					($resize_strategy == _IMG_RESIZE_STRATEGY_BYSMALLERSIDE && ($height > $maxSide && $width > $maxSide))
				)
			)  {

			if ($resize_strategy == _IMG_RESIZE_STRATEGY_BYSMALLERSIDE) {
				if ($width > $height) {
					$resizedHeight = $maxSide;
					$resizedWidth = (int)($width * $maxSide / $height);
				} else {
					$resizedWidth = $maxSide;
					$resizedHeight = (int)($height * $maxSide / $width);
				}
			} else if ($resize_strategy == _IMG_RESIZE_STRATEGY_BYWIDTH || ($resize_strategy == _IMG_RESIZE_STRATEGY_BYGREATERSIDE && $width > $height)) {
				$resizedWidth = $maxSide;
				$resizedHeight = (int)($maxSide * $height / $width);
			} else {
				$resizedHeight = $maxSide;
				$resizedWidth = (int)($maxSide * $width / $height);
			}
			$resizedImage = $image->resizeTo($resizedWidth, $resizedHeight);
			$resizedImage->saveTo($imglibDirPath . '/' . $destFileUrl);
			$width = $resizedWidth;
			$height = $resizedHeight;
		} else {
			$newPath = str_replace("//", "/", $imglibDirPath . '/' . $destFileUrl);
			if ($tmpFilePath != $newPath) {
				copy($tmpFilePath, $imglibDirPath . '/' . $destFileUrl);
				@chmod($imglibDirPath . '/' . $destFileUrl, 0755);
			}
		}

		if ($image->resizable &&
				(
					($resize_strategy == _IMG_RESIZE_STRATEGY_BYWIDTH && $width > $thumbnailSide)
					||
					($resize_strategy == _IMG_RESIZE_STRATEGY_BYGREATERSIDE && ($height > $thumbnailSide || $width > $thumbnailSide))
					||
					($resize_strategy == _IMG_RESIZE_STRATEGY_BYSMALLERSIDE && ($height > $thumbnailSide && $width > $thumbnailSide))
				)
			)  {
			if ($resize_strategy == _IMG_RESIZE_STRATEGY_BYSMALLERSIDE) {
				if ($width > $height) {
					$thumbnailHeight = $thumbnailSide;
					$thumbnailWidth = (int)($width * $thumbnailSide / $height);
				} else {
					$thumbnailWidth = $thumbnailSide;
					$thumbnailHeight = (int)($height * $thumbnailSide / $width);
				}
			} else if ($resize_strategy == _IMG_RESIZE_STRATEGY_BYWIDTH || ($resize_strategy == _IMG_RESIZE_STRATEGY_BYGREATERSIDE && $width > $height)) {
				$thumbnailWidth = $thumbnailSide;
				$thumbnailHeight = (int)($thumbnailSide * $height / $width);
			} else {
				$thumbnailHeight = $thumbnailSide;
				$thumbnailWidth = (int)($thumbnailSide * $width / $height);
			}
			$thumbnaleImage = $image->resizeTo($thumbnailWidth, $thumbnailHeight);
			$thumbnaleImage->saveTo($imglibThumbnalesDirPath . '/' . $destFileUrl);
		} else {
			$oldPath = str_replace("//", "/", $imglibDirPath . '/' . $destFileUrl);
			$newPath = str_replace("//", "/", $imglibThumbnalesDirPath . '/' . $destFileUrl);

			if ($oldPath != $newPath) {
				$res = copy($oldPath, $newPath);
				@chmod($newPath, 0755);
			}
		}

		$imglibImage = new ImgLibImage($folder, $fileName);
		return $imglibImage;
	}

	function moveTo($newFolder) {
		$oldFolderRelPath = $this->folder->getRelPath();
		$newFolderRelPath = $newFolder->getRelPath();
		$imageName = $this->getName();

		$oldUrl = $this->getUrl();
		$newUrl = $this->compileUrl($newFolderRelPath, $imageName);

		$imglibDirPath = FIELD_IMGLIB_DIR;
		$imglibThumbnalesDirPath = FIELD_IMGLIB_THUMBNAILS_DIR;

		rename($this->file, $imglibDirPath . $newUrl);

		$tfile = $imglibThumbnalesDirPath . $oldUrl;
		rename($tfile, $imglibThumbnalesDirPath . $newUrl);

		$this->folder = $newFolder;
		$this->file = $this->folder->getAbsolutePath() . '/' . $imageName;
		$this->name = basename($this->file);

//		$conn = DBUtils::getConnection();
//		$sql = "UPDATE dbm_img_lib_images SET folder = '" . $newFolderRelPath . "' WHERE folder ='" . $oldFolderRelPath . "' AND name='" . $imageName . "'";
//
//		$stmt = $conn->prepareStatement($sql);
//		$stmt->executeUpdate();
	}

	function remove() {
		
		$folderRelPath = $this->folder->getRelPath();
		//_dump($folderRelPath);exit;//kazancev
		$imageName = $this->getName();

		@unlink($this->file);
		@unlink(FIELD_IMGLIB_THUMBNAILS_DIR . '/' . $folderRelPath . '/' . $imageName);

//		$conn = DBUtils::getConnection();
//		$sql = "DELETE FROM dbm_img_lib_images WHERE folder ='" . $folderRelPath . "' AND name='" . $imageName . "'";
//
//		$stmt = $conn->prepareStatement($sql);
//		$stmt->executeUpdate();
//
//		$sql = "UPDATE dbm_field_values SET value_str='' WHERE value_str='".$this->getUrl()."'";
//		$stmt = $conn->prepareStatement($sql);
//		$stmt->executeUpdate();
	}

	public static function findByUrl($url) {
		if (strlen($url) == 0) return null;
		$pos = strrpos($url, '/');
		$folderRelPath = ($pos === false || $pos == 0) ? "" : substr($url, 1, $pos-1);
		$imageName = substr($url, $pos + 1);
		$folder = ImgLibFolder::findByRelDir($folderRelPath);
		$i = new ImgLibImage($folder, $imageName);
		if (isset($i->error) and $i->error == true) {
			return null;
		} else {
			return $i;
		}
	}
}

?>