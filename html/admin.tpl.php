<div class="wrap">
<h1>Keyground Options</h1> 
<form name="keyground-admin-form" method="post" action="?page=keyground&action=saveOptions">
<table class="form-table">
	<tr> 
		<th><label for="api_key">Api Key</label></th> 
		<td>
			<input type="text" name="api_key" id="api-key" value="<?=$this->apiKey?>" class="regular-text" /> 
		</td> 
	</tr> 
	<!-- 
	<tr> 
		<th><label for="api_key">Auto-Update</label></th> 
		<td>
			<input type="checkbox" name="auto_update" id="auto_update" <?php if($this->autoUpdate=="yes"){echo "CHECKED";} ?> value="yes" class="regular-text" > </> 
			<i>Update from Keyground whenever you login as admin.</i>
		</td> 
	</tr>
	 -->
</table>
<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save Information"  /></p>
</form> 
</div>

 <!-- 
<?php if($this->autoUpdate!="yes"){ ?>
<div class="wrap">
<form name="update-form" method="post">
<span class="submit"><input type="submit" name="update-submit" id="submit" class="button-primary" value="Update Wordpress from Keyground"  /></span>
</form> 
</div>
<?php }?>

<div class="wrap">
<form name="reset-form" method="post" action="?page=keyground&action=resetDialog" >
<span class="submit"><input type="submit" name="reset-submit" id="submit" class="button-primary" value="Reset"  /></span>
</form> 
</div>

 -->