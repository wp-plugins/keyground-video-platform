<? 

require_once dirname(__FILE__).'/../keygroundwp.class.php';
$kgwp = new keyground();


if($_POST['reset-submit'])
{

	
?>

<div class="wrap">
<h1>Are you sure you want to reset?</h1> 
It will erase your posts and install from Keyground.
</div>

<form name="reset-form" method="post">
<p class="submit"><input type="submit" name="reset-sure" id="submit" class="button-primary" value="Yes,Reset!"  />
<input type="submit" name="reset-no" id="submit" class="button-primary" value="No."  /></p>
</form> 

<?php	
return;
}

if($_POST['reset-sure'])
{
	update_option('kg_auto_update',"no",'','yes');
	$kgwp->setupPlugin();
	
}

if($_POST['update-submit'])
{
	$kgwp->applyUpdates();
	
}


	if($_POST['api_key']!=''){
		$api_key=get_option('kg_api_key');
		if($api_key==''){
			
			add_option('kg_api_key',$_POST['api_key'],'','yes');

			


			echo "Everything ok. All values saved.";
		} else {

			update_option('kg_api_key',$_POST['api_key'],'','yes');

			
			echo "Everything ok. All values saved.";
		}

		
		
		$auto_update=get_option("kg_auto_update");
		
		if($_POST['auto_update']=="yes")
		{
			
			if($auto_update)
			{
				update_option('kg_auto_update',$_POST['auto_update'],'','yes');
			}
			else
			{
				add_option('kg_auto_update',$_POST['auto_update'],'','yes');
			}
		}
		else
		{
			if($auto_update)
			{
				update_option('kg_auto_update',"no",'','yes');
			}
			else
			{
				add_option('kg_auto_update',"no",'','yes');
			}
		}
		

		//echo "***".$_POST['auto_update'];
		
		
	} 
	

	$api_key=get_option('kg_api_key');
	$auto_update=get_option("kg_auto_update");
	

//$kgwp->applyUpdates();
	
?>

<div class="wrap">
<h1>Keyground Options</h1> 
<form name="keyground-admin-form" method="post">
<table class="form-table">

	
	<tr> 
		<th><label for="api_key">Api Key</label></th> 
		<td>
			<input type="text" name="api_key" id="api-key" value="<?=$api_key?>" class="regular-text" /> 
		
		</td> 
	</tr> 
	
	<tr> 
		<th><label for="api_key">Auto-Update</label></th> 
		<td>
			<input type="checkbox" name="auto_update" id="auto_update" <?php if($auto_update=="yes"){echo "CHECKED";} ?> value="yes" class="regular-text" > </> 
		<i>Update from Keyground whenever you login as admin.</i>
		</td> 
	</tr>
	
</table>
<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save Information"  /></p>
</form> 
</div>


<?php if($auto_update!="yes"){ ?>
<div class="wrap">
<form name="update-form" method="post">
<span class="submit"><input type="submit" name="update-submit" id="submit" class="button-primary" value="Update Wordpress from Keyground"  /></span>
</form> 
</div>
<?php }?>

<div class="wrap">
<form name="reset-form" method="post">
<span class="submit"><input type="submit" name="reset-submit" id="submit" class="button-primary" value="Reset"  /></span>
</form> 
</div>

