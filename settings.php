<div class="wrap auto-seo-settings">
	<?php screen_icon(); ?>
	<h2><?php echo __('Auto SEO Settings','auto-seo') ?></h2>           
	<form method="post" action="">
		<input type="hidden" name="action" value="update" />
		<h3 class="title"><?php echo __('Active Post Types','auto-seo') ?></h3>
		<table class="form-table">
			<p class="description"><?php echo __('Which post types would you like Auto SEO to run on?','auto-seo') ?></p>
			<tr valign="top">
				<th scope="row"><?php echo __('Enable in Posts Types','auto-seo') ?></th>
				<td>
					<p class="description"><?php echo __('Auto SEO will only run an the selected page types.','auto-seo') ?></p>
					<?php $post_types = get_post_types(array('show_ui'=>true),'objects');
					foreach($post_types as $name => $data){ ?>
						<label for="post_types[<?php echo $name ?>]" class="one_fifth">
							<input type="hidden" name="post_types[<?php echo $name ?>]" value="off" />
							<input name="post_types[<?php echo $name ?>]" type="checkbox" id="post_types[<?php echo $name ?>]" value="on" <?php echo ($this->settings['post_types'][$name]=='on'?'checked':'') ?> >
							<?php echo $data->labels->name ?>
						</label>
					<?php } ?>
				</td>
			</tr>
		</table>

		<h3 class="title"><?php echo __('Keyword Sets','auto-seo') ?></h3>
		<table class="form-table keyword-sets">
			<p class="description"><?php echo __('Using a set name in brackets (ie <code>[Example Set]</code>) in a meta tag will insert a randomly selected keyword in that set. Keywords should be comma separated. Deleting a keyword sets name will remove the set.','auto-seo') ?></p>
			<tr valign="top">
				<th scope="col"><?php echo __('Set Name','auto-seo') ?></th>
				<th scope="col" class="pseudo_header"><?php echo __('Set Keywords','auto-seo') ?></th>
			</tr>
			<?php 
			// Load saved sets
			$sets = $this->settings['keyword_sets'];
			// Add the blank set used to create new ones
			$sets['_blank'] = array('');
			// Loop through and add each set to the page
			foreach($sets as $name => $keywords){ ?>
				<tr valign="top" <?php echo ($name=='_blank'?'style="display:none;"':'') ?>>
					<td class="top-align"><input type="text" name="keyword_set-name[]" value="<?php echo $name ?>" /></td>
					<td><textarea name="keyword_set-words[]"><?php echo stripslashes(implode(', ',$keywords)) ?></textarea></td>
				</tr>
			<?php } ?>
		</table>
		<a href="#add-keyword-set"><?php echo __('Add Keyword Set','auto-seo') ?></a><br/>
		<br/>
		<table class="form-table keyword-sets">
			<p class="description"><?php echo __('In addition to the custom keyword sets defined above the following built-in keyword sets are available.','auto-seo') ?></p>
			<tr valign="top">
				<td scope="col"><input type="text" name="" value="Page Title" disabled /></td>
				<td scope="col"><?php echo __('The page title. This will be blank on the blog home page.','auto-seo') ?></td>
			</tr>
			<tr valign="top">
				<td scope="col"><input type="text" name="" value="Date" disabled /></td>
				<td scope="col"><?php echo __('The post date.','auto-seo') ?></td>
			</tr>
			<tr valign="top">
				<td scope="col"><input type="text" name="" value="Author" disabled /></td>
				<td scope="col"><?php echo __('The name of the posts author.','auto-seo') ?></td>
			</tr>
			<tr valign="top">
				<td scope="col"><input type="text" name="" value="Type" disabled /></td>
				<td scope="col"><?php echo __('The posts type, either Post, Page, or a Custom Type.','auto-seo') ?></td>
			</tr>
			<tr valign="top">
				<td scope="col"><input type="text" name="" value="Category" disabled /></td>
				<td scope="col"><?php echo __('A randomly selected category assigned to that page. Like the custom examples this will be the same randomly selected category each page load unless you modifiy the pages categories.','auto-seo') ?></td>
			</tr>
			<tr valign="top">
				<td scope="col"><input type="text" name="" value="Categories" disabled /></td>
				<td scope="col"><?php echo __('A comma-seperated list of all the categories assigned to the page.','auto-seo') ?></td>
			</tr>
			<tr valign="top">
				<td scope="col"><input type="text" name="" value="Tag" disabled /></td>
				<td scope="col"><?php echo __('A randomly selected tag assigned to that post. Like the custom examples this will be the same randomly selected category each page load unless you modifiy the posts tags.','auto-seo') ?></td>
			</tr>
			<tr valign="top">
				<td scope="col"><input type="text" name="" value="Tags" disabled /></td>
				<td scope="col"><?php echo __('A comma-seperated list of all the tags assigned to the post.','auto-seo') ?></td>
			</tr>

		</table>

		<h3 class="title"><?php echo __('Meta Tag Options','auto-seo') ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="scale"><?php echo __('Tags Enabled','auto-seo') ?></label></th>
				<td>
					<p class="description"><?php echo __('Check which meta tags you want Auto SEO to create / overwrite.','auto-seo') ?></p>
					<?php $tags = array('Title','Description','Keywords','Robots');
					foreach($tags as $n => $name){ ?>
						<label for="tags[<?php echo $name ?>]" class="one_fifth">
							<input type="hidden" name="tags[<?php echo $name ?>]" value="off" />
							<input name="tags[<?php echo $name ?>]" type="checkbox" id="tags[<?php echo $name ?>]" value="on" <?php echo ($this->settings['tags'][$name]=='on'?'checked':'') ?> >
							<?php echo $name ?>
						</label>
					<?php } ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="title"><?php echo __('Title','auto-seo') ?></label></th>
				<td>
					<input name="title" type="text" id="title" value="<?php echo stripslashes($this->settings['title']) ?>" class="regular-text">
					<p class="description"><?php echo __('You can use any of the above Keyword Set Names in square Brackets (ie <code>[Example Set]</code>) to place a random keyword from that set.','auto-seo') ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="description"><?php echo __('Description','auto-seo') ?></label></th>
				<td>
				<textarea name="description" id="description"><?php echo stripslashes($this->settings['description']) ?></textarea>
					<p class="description"><?php echo __('You can use any of the above Keyword Set Names in square Brackets (ie <code>[Example Set]</code>) to place a random keyword from that set.','auto-seo') ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="keywords"><?php echo __('Number of Keywords','auto-seo') ?></label></th>
				<td>
					<input name="keywords" type="number" id="keywords" value="<?php echo $this->settings['keywords'] ?>" class="regular-text">
					<p class="description"><?php echo __('How many keywords you want placed in the keywords meta tag. Keywords are pulled from all sets.','auto-seo') ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="robots"><?php echo __('Robots','auto-seo') ?></label></th>
				<td><select name="robots" id="robots">
						<option value="INDEX, FOLLOW" <?php echo ($this->settings['robots']=='INDEX, FOLLOW'?'selected':'') ?>><?php echo __('Index Page, Follow Links','auto-seo') ?></option>
						<option value="INDEX, NOFOLLOW" <?php echo ($this->settings['robots']=='INDEX, NOFOLLOW'?'selected':'') ?>><?php echo __('Index Page, Don\'t Follow Links','auto-seo') ?></option>
						<option value="NOINDEX, NOFOLLOW" <?php echo ($this->settings['robots']=='NOINDEX, NOFOLLOW'?'selected':'') ?>><?php echo __('Don\'t index page or follow links','auto-seo') ?></option>
					</select>
					<p class="description"><?php echo __('Controls how search engines index and search your site, may be overridden by ROBOTS.TXT file.','auto-seo') ?></p>
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" id="submit" class="button button-primary" value="Save Changes">
		</p>
	</form>

	<h3 class="title"><?php echo __('Compatibility','auto-seo') ?></h3>
	<a name="compatibility"></a>
	<p class="description compatibility"><?php echo __('Auto SEO is currently checking compatibility...','auto-seo') ?></p>
	<script>
		/* If this was anything more than a single very simple script I would have put it in it's own JS file. */
		function checkCompatibility(){
			jQuery('p.description.compatibility').html('Auto SEO is currently checking compatibility...');
			jQuery.post('<?php echo get_site_url() ?>',{'autoseo_compatibility':'check'},function(r){
				if(r.match(/<!-- Auto SEO Added -->/g).length>0){
					var new_string = '<?php echo __('Auto SEO appears to be working.','auto-seo') ?>';
				}else{
					var new_string = '<?php echo __('Auto SEO does not appear to be compatible with your current Theme and Plugin Combination.','auto-seo') ?>';
				}
				new_string += ' <a href="#compatibility" onclick="checkCompatibility()"><?php echo __('Click here to run compatibility check again','auto-seo') ?>.</a>';
				jQuery('p.description.compatibility').html(new_string);
			});
		}
		checkCompatibility();
	</script>
	<br/>
	<h3 class="title"><?php echo __('Miscellaneous','auto-seo') ?></h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="nothing"><?php echo __('Have a problem','auto-seo') ?></label></th>
			<td>
				<?php echo __('Having trouble getting the plug-in working? Expected results? Feel like direction your repentant rage at someone far far away? Just have a general usage question? You can try the <a href="http://wordpress.org/support/plugin/auto-seo" target="_blank">plug-ins support form</a> or, if you want an answer from the source, feel free to email me at <a href="mailto:phillip.gooch@gmail.com" target="_blank">phillip.gooch@gmail.com</a>.','auto-seo') ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="nothing">Check out the code</label></th>
			<td>
				<?php echo __('Want to see how it all works, you can check out the on the <a href="http://plugins.svn.wordpress.org/auto-seo/trunk/" target="_blank">WordPress SVN</a> or, even better, <a href="https://github.com/pgooch/auto-seo" target="_blank">fork it on GitHub</a>. Feel free to make changes and submit pull requests, good ideas will be added to the master branch.','auto-seo') ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Donate</th>
			<td>
				<?php echo __('Like the plug-in and want to support further development? Thanks! You can use the paypal button below to donate any amount you want. Don\'t like PayPal? send me an email, we can figure something out.<br/>','auto-seo') ?>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_donations">
					<input type="hidden" name="business" value="phillip.gooch@gmail.com">
					<input type="hidden" name="lc" value="US">
					<input type="hidden" name="item_name" value="Auto SEO Plugin">
					<input type="hidden" name="no_note" value="0">
					<input type="hidden" name="currency_code" value="USD">
					<input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_SM.gif:NonHostedGuest">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
			</td>
		</tr>
	</table>
</div>



