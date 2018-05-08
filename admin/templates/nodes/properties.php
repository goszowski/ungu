<?
$request->setAttribute("active_tab", "properties");
$restrict_edit = $CurrentAdminUser->group->restrictNodeEdit;
$cuGroupRights = &new NodeGroupRights($CurrentAdminUser->group, $node);
$userHasWriteRights = $cuGroupRights->hasWriteRight();
?>
<?=usetemplate("nodes/_edit_header")?>

<div class="p-md pt-clear">
	<div class="p b-a no-b-t bg-white m-b">
		<form action="/admin/nodes.php" method="POST">
			<input type="hidden" name="do" value="properties_update">
			<input type="hidden" name="node_id" value="<?=$node->id?>">

			<table class="default-table table-p">
				<?if(!$nodeIsRoot):?>
				<tr>
					<td><?=$AdminTrnsl["Shortname"]?> <span class="text-danger">*</span></td>
					<td>
						<?if($validationErrors['NodeShortname']):?><div class="text-danger pl-10 pt-5"><?=$validationErrors['NodeShortname']?></div><?endif;?>
						<input type="text" class="form-control input-sm" style="width: 495px" size="45" name="NodeShortname" value="<?=$nodeMap->NodeShortname?>">
					</td>
				</tr>
				<?endif;?>

				<?if(!$restrict_edit && $userHasWriteRights):?>
				<tr>
					<td><?=$AdminTrnsl["Template"]?></td>
					<td>
						<?if($validationErrors['NodeDynamicTemplate']):?><div class="text-danger pl-10 pt-5"><?=$validationErrors['NodeDynamicTemplate']?></div><?endif;?>
						<input type="text" class="form-control input-sm" style="width: 495px"  size="45" name="NodeDynamicTemplate" value="<?=$nodeMap->NodeDynamicTemplate?>">
					</td>
				</tr>
				<tr>
					<td><?=$AdminTrnsl["Admin_URL"]?></td>
					<td>
						<?if($validationErrors['NodeAdminURL']):?><div class="text-danger pl-10 pt-5"><?=$validationErrors['NodeAdminURL']?></div><?endif;?>
						<input type="text" class="form-control input-sm" style="width: 495px"  size="45" name="NodeAdminURL" value="<?=$nodeMap->NodeAdminURL?>">
					</td>
				</tr>
				<?else:?>
				<input type="hidden" name="NodeDynamicTemplate" value="<?=$nodeMap->NodeDynamicTemplate?>">
				<input type="hidden" name="NodeAdminURL" value="<?=$nodeMap->NodeAdminURL?>">
				<?endif;?>

				<tr>
					<td>&nbsp;</td>
					<td>
						<button type="submit" class="btn btn-sm btn-primary mt-5"><?=$AdminTrnsl["Submit_changes"]?></button>
					</td>
				</tr>

				
			</table>
		</form>
	</div>
</div>

</body>
</html>
