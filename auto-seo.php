<?php
/*
Plugin Name: Auto SEO
Plugin URI: http://fatfolderdesign.com/auto-seo/
Description: Auto SEO is a quick, simple way to add title, meta keywords, and meta descriptions to your site all at one from a single page.
Version: 2.1.2
Author: Phillip Gooch
Author URI: mailto:phillip.gooch@gmail.com
License: Undecided
*/

class autoseo {

	public $settings = array();

	public function __construct(){
		// Load the settings
		$settings=get_option('auto-seo-settings','[]');
		$settings=json_decode($settings,true);
		$settings=array_merge(array(
			// These are the defaults
			'post_types' => array(
				'post' => 'off',
				'page' => 'on',
				'attachment' => 'on',
			),
			'keyword_sets' => array('Example Set'=>array('Such Example','Much Fast','Many Preset','Very Keyword','So Test','Wow'),),
			'tags' => array(
				'Title' => 'on',
				'Description' => 'on',
				'Keywords' => 'on',
				'Robots' => 'off',
			),
			'title' => 'Auto SEO, [Example Set] | [Page Title].',
			'description' => 'This is an example of a description placed with the Auto SEO WordPress Plugin. Auto SEO, [Example Set], [Example Set].',
			'keywords' => 13,
			'robots' => 'INDEX, FOLLOW',
		),(array)$settings);
		// Check any settings that need to be overridden (for whatever reason notes)
		$this->settings = $settings;
		// Add the settings menu item
		add_action('admin_menu',array($this,'add_menu_item'));
		// Add settings link to plugin page
		add_filter('plugin_action_links_'.plugin_basename(__FILE__),array($this,'add_settings_link'));
		// Load up all the admin scripts and styles
		add_action('admin_enqueue_scripts',array($this,'admin_enqueued'));
		// Do the actual adding of the meta tags, we need to use an output buffer to grab whatever the head is, so we can regex it and remove the old stuff.
		add_filter('get_header',array($this,'add_meta_tags_obstart'),0);
		add_filter('wp_head',array($this,'add_meta_tags_obget'),9001);
	}

	public function add_menu_item(){
		// This will add the settings menu item
		add_menu_page('Auto SEO','Auto SEO','manage_options','auto-seo-settings',array($this,'settings_page'),'dashicons-megaphone',77);
	}
	public function settings_page(){
		// Save the settings if sending post data
		if(isset($_POST['action'])&&$_POST['action']=='update'){
			echo '<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>Settings saved.</strong></p></div>';
			unset($_POST['action']);
			$this->save_settings($_POST);
		}
		// Load Settings Page, kept externally for brevity's sake
		require_once('settings.php');
	}
	public function add_settings_link($links){
		$links[] = '<a href="admin.php?page=auto-seo-settings">Settings</a>';
		return $links;
	}
	public function admin_enqueued(){
		// Load the admin scripts and styles where needed
		// JS
		wp_enqueue_script('auto-seo-admin-js',plugin_dir_url( __FILE__ ).'admin.js',array('jquery'));
		// CSS
		wp_enqueue_style('auto-seo-admin-css',plugin_dir_url( __FILE__ ).'admin.css');
		wp_enqueue_style('auto-seo-admin-css');
	}
	public function add_meta_tags_obstart(){
		// All this does is init the output buffer if enabled for that post type, nothing fancy.
		if($this->settings['post_types'][get_post_type(get_the_ID())]=='on'){
			ob_start();
		}
	}
	public function add_meta_tags_obget(){
		//this whole section can be skipped if it's not enabled for this post type
		if($this->settings['post_types'][get_post_type(get_the_ID())]=='on' || $_POST['autoseo_compatibility']=='check'){
			// Get the head, determin what meta items were going to add/change, remove old if any, add new, output head
			$head = ob_get_clean();
			// Determin what sections are on, and replace them as needed.
			foreach($this->settings['tags'] as $tag => $status){
				if($status=='on' || $_POST['autoseo_compatibility']=='check'){
					switch($tag){
						case 'Title':
							preg_match_all('~<title>([^(</)]*)</ ?title>~',$head,$matches);
							$head = preg_replace('~<title>[^(</)]*</ ?title>~','<!-- Auto SEO was here! -->',$head);
							$title = $this->bracket_replace($this->settings['title'],'Page Title',$matches[1][0]);
							$title = $this->replace_keyword_brackets($title);
							$head .= '<!-- Auto SEO Added -->'."\n".'<title>'.$title.'</title>'."\n";
						break;
						case 'Description':
							$head = preg_replace('~<meta.*name=[\'|"]?description[\'|"]?.*/ ?>~','<!-- Auto SEO was here! -->',$head);
							$description = $this->replace_keyword_brackets($this->settings['description']);
							$head .= '<!-- Auto SEO Added -->'."\n".'<meta name="description" content="'.$description.'" />'."\n";
						break;
						case 'Keywords':
							$head = preg_replace('~<meta.*name=[\'|"]?keywords[\'|"]?.*/ ?>~','<!-- Auto SEO was here! -->',$head);
							// No, don't look at this, I'm cheating.
							// First I'm going to need an array with all the keyword set names in it
							$keyword_sets_names = array();
							foreach($this->settings['keyword_sets'] as $name => $keywords){
								$keyword_sets_names[] = $name;
							}
							// Then I'm going to need a starting point for this (much like when replacing brackets)
							$set = round(((int)get_the_ID()*pi())%count($keyword_sets_names));
							// Noew were going to create a string with all the needed keyword brackets in ti.
							$keywords = '';
							for($i=$this->settings['keywords'];$i>0;$i--){
								$keywords .= ', ['.$keyword_sets_names[$set].']';
								// And again, much like bracket replacement, were going to add to the $set and make sure it's still within range
								$set++;
								if($set==count($keyword_sets_names)){
									$set=0;
								}
							}
							// Now we can run it through the bracket replacer and output it like the others
							$keywords = $this->replace_keyword_brackets(substr($keywords,2));
							$head .= '<!-- Auto SEO Added -->'."\n".'<meta name="keywords" content="'.$keywords.'" />'."\n";
						break;
						case 'Robots':
							$head = preg_replace('~<meta.*name=[\'|"]?robots[\'|"]?.*/ ?>~','<!-- Auto SEO was here! -->',$head);
							$head .= '<!-- Auto SEO Added -->'."\n".'<meta name="robots" content="'.$this->settings['robots'].'" />'."\n";
						break;
					}
				}
			}
		}
		// were done working with this, we can echo it out and be done with it.
		echo $head;
	}
	public function bracket_replace($string,$bracket,$replacement,$starting_point=0){
		// Replace the brackets in strings with the desired variable
		if(is_array($replacement)){
			// Before we loop lets trim down that starting point since it will probably be much larger than the number of keywords.
			$starting_point = $starting_point%count($replacement);
			// We are got an array then we know we need to look for the page ID and do some looping to replace them all.
			while(strpos($string,'['.$bracket.']')!==false){
				// replace and bump up the starting point to we don't spam the keyword over and over again
				$string = preg_replace('~\['.$bracket.'\]~',$replacement[$starting_point],$string,1);
				$starting_point++;
				// before we finish with this loop iteration make sure were still within the replacements array size
				if($starting_point==count($replacement)){
					$starting_point = 0;
				}
			}
			return $string;
		}else{ // $replacement is string
			// If we got a string it's just a dead simple replacement.
			return str_ireplace('['.$bracket.']',$replacement,$string);
		}
	}
	function replace_keyword_brackets($head){
		// Loop through each keyword set calling bracket_replace for each.
		// So when I said random, I lied, it's pseudo random, we don't want to keywords to be different every time Google crawls, so we use the id and 
		// some random math to come up with a starting point that we loop through when determining what keyword gets placed. This is done here once and
		// passed on to the bracket_replace function for it's use.
		$starting_point = round((int)get_the_ID()*pi());
		foreach($this->settings['keyword_sets'] as $set_name => $keywords){
			$head = $this->bracket_replace($head,$set_name,$keywords,$starting_point);
		}
		return $head;
	}
	// Semi Static Functions, should not need to be dramatically changed between plugins
	public function save_settings($save_options){
		// Convert the keywords sets into a single item, clean up when done
		foreach($save_options['keyword_set-name'] as $n => $name){
			if(trim($name)!=''&&trim($save_options['keyword_set-words'][$n])!=''){
				$keywords = explode(',',$save_options['keyword_set-words'][$n]);
				foreach($keywords as $i => $w){$keywords[$i]=trim($w);}
				$save_options['keyword_sets'][trim($name)] = $keywords;
			}
		}
		unset($save_options['keyword_set-name']);
		unset($save_options['keyword_set-words']);		
		// This will take the existing settings, merge the update with them, then update both wordpress and the $this->settings var
		$options=$this->settings;
		$options=array_merge($options,$save_options);
		$options=json_encode($options);
		update_option('auto-seo-settings',$options);
		$this->settings = json_decode($options,true);
	}
}
$autoseo = new autoseo();