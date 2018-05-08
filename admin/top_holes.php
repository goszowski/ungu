<?
require_once('../WEB-INF/prepend.php');

require_once('../_site_settings.php');

//getSpecHoles(19279, 19282, 2);

if ($_GET["del"]) {
	$sql = "DELETE FROM cms_spec_holes WHERE id='{$_GET["del"]}' ";
	DBUtils::execUpdate($sql);
}

if ($_POST["do"]=="add" && $_POST["club_id"]) {
	
	$club_id = (int)$_POST["club_id"];
	$course_id = (int)$_POST["course_id"];
	$hole_num = (int)$_POST["hole_num"];
	
	$items = getSpecHoles($club_id, $course_id, $hole_num);
	
	//_dump($items);
	
	if (is_array($items)) {
		header("Location: ?do=exist");
		exit;
	}
	
	$sql = "INSERT INTO cms_spec_holes (club_id, course_id, hole_num) VALUES ('{$club_id}', '{$course_id}', '{$hole_num}') ";
	$rs = DBUtils::execUpdate($sql);
	header("Location: ?do=done");
	exit;
}

?>
<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
<title></title>
<link rel=stylesheet href=/admin/css.css type="text/css">

<script type="text/javascript" src="/_js/jquery.js"></script>
<script type="text/javascript" src="/_tools/autocomplete/jquery.autocomplete.js"></script>
<link rel="stylesheet" type="text/css" href="/_tools/autocomplete/jquery.autocomplete.css" />

</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<tr class=top4>
<td height=40 id=pad18 colspan=2 class=head>
Quick Search Club
</td>
</tr>
</table>

<table width=100% border=0 cellpadding=0 cellspacing=0 class=top42><td><img src=/admin/_img/s.gif width=1 height=5></td></table>

<script>
$().ready(function() {

	$("#search_club").autocomplete("/request.php?do=clubs_id_admin", {
		width: 360,
		minChars: 2,
		selectFirst: true
	}).result(function(event, item) {
		
		$("#club_id").val(item[1]);
		
		  	$("select#sel_course").css('cursor', 'progress');
		    $.getJSON("/requestjson.php?do=courses_by_club_admin", {club_id: $("#club_id").val(), ajax: 'true'}, function(j){
		      var options = '';
		      for (var i = 0; i < j.length; i++) {
		      	var str_sel = ' ';
		        options += '<option value="' + j[i].id + '"' + str_sel + '>' + j[i].name + '</option>';
		      }
		      $("select#sel_course").html(options);
		      $("select#sel_course").css('cursor', 'auto');
		      
		    })
		  
	});
	
});
</script>

<table width=100% border=0 cellpadding=4 cellspacing=0>
<td id=pad18 class=h4>

<?if ($_GET["do"]=="done") {?>
<h2 style="color:green">Added successfully</h2>
<?}elseif ($_GET["do"]=="exist") {?>
<h2 style="color:red">Already exists</h2>
<?}?>

<form method="POST">
<br>
<input type="text" id="search_club" style="width:400px;">  Select Club (for Gold or Silver)

<input type="hidden" id="club_id" name="club_id" value="0">
<br><br>

<select id="sel_course" name="course_id" style="width:200px;">
<option>first select club
</select> Select Course (for Gold or Silver)
<br><br>

<input type="text" id="hole_num" name="hole_num" style="width:40px;" maxlength="2"> Hole Num (for Gold)
<br><br>

<input type="hidden" name="do" value="add">
<input type=submit class=button5 value="Add to list" >
</form>
</td>
</table>

<?
$sql = "
	SELECT 
		sh.id id,
		sh.hole_num hole_num,
		club.name club_name,
		course.name course_name
	FROM cms_spec_holes sh
	INNER JOIN dbm_nodes club ON club.id=sh.club_id
	LEFT JOIN dbm_nodes course ON course.id=sh.course_id
	ORDER BY club_name ASC, course_name ASC, hole_num ASC
";
$rs = DBUtils::execSelect($sql);
$gold_items = array();
$silver_items = array();
while ($rs->next()) {
	$tmp = array();
	$tmp["id"] = $rs->getInt("id");
	$tmp["hole_num"] = $rs->getInt("hole_num");
	$tmp["club_name"] = $rs->getString("club_name");
	$tmp["course_name"] = $rs->getString("course_name");
	if ($tmp["hole_num"]) {
		$gold_items[] = $tmp;
	} else {
		$silver_items[] = $tmp;
	}
}

?>

<style>
.cell2 {
	float:left;padding:10px;
	border:1px solid #D8D8D8;
	height:60px;
}
</style>

<div style="float:left;">
	<h2 style="padding:20px">Gold</h2>
	<ul style="list-style-type:none;font-size:15px;padding-left:10px;">	
	<?foreach ($gold_items as $item){?>
		<li style="padding-left:0px;">
			<div class="cell2" style="width:200px;"><?=$item["club_name"]?></div>
			<div class="cell2" style="width:80px;"><?=$item["course_name"]?></div>
			<div class="cell2" style="width:20px;"><?=$item["hole_num"]?></div>
			<div class="cell2" style="width:20px;"><a href="?del=<?=$item["id"]?>" onclick="return confirm('Are you sure?');">Del</a></div>
		</li>
			
	<?}?>
	</ul>
</div>

<div style="float:left;">
	<h2 style="padding:20px">Silver</h2>
	<ul style="list-style-type:none;font-size:15px;padding-right:10px;">	
	<?foreach ($silver_items as $item){?>
		<li style="padding-left:0px;">
			<div class="cell2" style="width:200px;"><?=$item["club_name"]?></div>
			<div class="cell2" style="width:100px;"><?=$item["course_name"]?></div>
			<div class="cell2" style="width:20px;"><a href="?del=<?=$item["id"]?>" onclick="return confirm('Are you sure?');">Del</a></div>
		</li>
	<?}?>
	</ul>
</div>

</body>
</html>