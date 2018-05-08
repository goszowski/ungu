<!DOCTYPE html>
<html>
<head>
	<title>runsite.CMS - Login</title>
	<?usetemplate('_res');?>
</head>
<body>
	<div class="app app-header-fixed">
		<div class="container">
			<div class="center-block w-xl w-auto-xs m-b-lg">
				<div class="text-2x m-v-lg text-primary">
					<i class="glyphicon glyphicon-th-large text-xl"></i> runsite.CMS
				</div>
				<div class="m-b text-sm">Type your login and password</div>
				<form method="POST" action="/admin/index.php" name="loginForm">

					<?if($request->getAttribute("error_message") != null):?>
					<div class="alert alert-danger"><?=$request->getAttribute("error_message");?></div>
					<?endif;?>

					<div class="form-group m-b-xs">
						<input type="text" name="_login" placeholder="<?=$AdminTrnsl["Login"]?>" class="form-control input-sm" autofocus required>
					</div>
					<div class="form-group m-b-xs">
						<input type="password" name="_password" placeholder="<?=$AdminTrnsl["Password"]?>" class="form-control input-sm" required>
					</div>
					<button type="submit" class="btn btn-info p-h-md m-v-lg">Sign in</button>

					<p class="text-xs">
						<a ui-sref="forgot-password">Forgot password?</a>
					</p>
				</form>
			</div>
		</div>
	</div>
</body>
</html>