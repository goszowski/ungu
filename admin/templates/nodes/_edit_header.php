<?
	$cuGroupRights = new NodeGroupRights($CurrentAdminUser->group, $node);
	$userHasWriteRights = $cuGroupRights->hasWriteRight();
	$request->setAttribute("userHasWriteRights", $userHasWriteRights);

	$disabledControlIfUserHasNoWriteRights = "";
	if (!$userHasWriteRights) {
		$disabledControlIfUserHasNoWriteRights = " disabled=true";
	}
	$request->setAttribute("disabledControlIfUserHasNoWriteRights", $disabledControlIfUserHasNoWriteRights);

	$restrict_edit = $CurrentAdminUser->group->restrictNodeEdit;
	$request->setAttribute("restrict_edit", $restrict_edit);

	$parents = $node->getParentList();
	$request->setAttribute("parents", $parents);
	$nodeClass = $node->getNodeClass();
?>
<html>
<head>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=<?=ADMIN_CHARSET?>">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Cache-Control" content="must-revalidate, post-check=0, pre-check=0">
<meta http-equiv="pragma" content="no-cache">
<title></title>
<link rel=stylesheet href=/admin/css.css type="text/css">
<?usetemplate("_res")?>
<script type="text/javascript" src="/admin/js/refresh_tree.js"></script>
<script language="JavaScript">
    function wop(url, w, h) {
        w = window.open(url,'node_props','resizable=yes,toolbar=no,location=no,directories=no,status=no,menubar=no,width='+w+', height='+h+',scrollbars=yes,fullscreen=no,top=100, left=100');
		w.focus();
    }
</script>

<!-- <script type="text/javascript" src="/_js/jquery.js"></script> -->
<script type="text/javascript" src="/_tools/autocomplete/jquery.autocomplete.js"></script>
<link rel="stylesheet" type="text/css" href="/_tools/autocomplete/jquery.autocomplete.css" />



</head>

<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 class=main<?if ($request->getParameter('reload') != null) {?> onLoad="refreshTree(<?=$node->id?>, <?=$node->parent_id+0?>)"<?}?>>

<div class="navbar-collapse bg-white box-shadow-md hidden-xs pt-15 pb-15 pl-25 pr-25 mb-15">
	<? if (!$CurrentAdminUser->group->canManageUsers) {array_shift($parents); } $i=0;foreach ($parents as $parent){$i++;?><?if ($i!=1) {?> : <?}?><a href="/admin/nodes.php?do=main&amp;id=<?=$parent->id?>" id="black"><nobr><?=$parent->name?></nobr></a><?}?><?if (sizeof($parents) != 0) {?> : <?}?><b><nobr><?=$node->name?></nobr></b>
</div>




<?
$msg = $request->getParameter("msg"); 
?>

<?if($msg):?>
	<div class="p-15 pt-5 pb-5">
		<div class="alert alert-info"><?=$AdminTrnsl[$msg]?></div>
	</div>
<?endif;?>


<? if (!$restrict_edit
	|| $nodeClass->checkFlag("USE_SHORTNAME")
	|| ($CurrentAdminUser->group->canManageUsers && $node->getChildrenCountForGroup($CurrentAdminUser->group->id) > 0)
	|| ($userHasWriteRights && $CurrentAdminUser->group->canManageUsers)
	|| ($userHasWriteRights && !$restrict_edit)
	) { ?>


<div class="p-md pb-clear">
	<ul class="nav nav-sm nav-tabs">
		<li class="<?if($active_tab=='editing'):?>active<?endif;?>"><a href="/admin/nodes.php?do=main&amp;id=<?=$node->id?>"><?=$AdminTrnsl["Editing"]?></a></li>

		<?if((!$restrict_edit || $nodeClass->checkFlag("USE_SHORTNAME"))):?>
		<li class="<?if($active_tab=='properties'):?>active<?endif;?>"><a href="/admin/nodes.php?do=properties&amp;node_id=<?=$node->id?>"><?=$AdminTrnsl["Properties"]?></a></li>
		<?endif;?>

		<?if($node->getChildrenCountForGroup($CurrentAdminUser->group->id) > 0):?>
		<li class="<?if($active_tab=='sorting'):?>active<?endif;?>"><a href="/admin/nodes.php?do=children_sort&amp;node_id=<?=$node->id?>"><?=$AdminTrnsl["Sort"]?></a></li>
		<?endif;?>

		<?if($userHasWriteRights && $CurrentAdminUser->group->canManageUsers):?>
		<li class="<?if($active_tab=='permissions'):?>active<?endif;?>"><a href="/admin/nodes.php?do=permissions&amp;node_id=<?=$node->id?>"><?=$AdminTrnsl["Permissions"]?></a></li>
		<?endif;?>

		<?if($userHasWriteRights && !$restrict_edit):?>
		<li class="<?if($active_tab=='dependencies'):?>active<?endif;?>"><a href="/admin/nodes.php?do=dependencies&amp;node_id=<?=$node->id?>"><?=$AdminTrnsl["Dependent_Classes"]?></a></li>
		<?endif;?>
	</ul>



</div>
<? } ?>
<!-- nodes header end -->