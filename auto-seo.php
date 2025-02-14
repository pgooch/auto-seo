<?php
/*
Plugin Name: Auto SEO
Plugin URI: http://fatfolderdesign.com/auto-seo/
Description: Auto SEO is a quick, simple way to add title, meta keywords, and meta descriptions to your site all at one from a single page.
Version: 2.6.6
Author: Phillip Gooch
Author URI: https://github.com/pgooch
License: GNU General Public License v2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: auto-seo
Domain Path: /_l18n/
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
			'keyword_sets' => array('Example Set'=>array(__('Such Example','auto-seo'),__('Much Fast','auto-seo'),__('Many Preset','auto-seo'),__('Very Keyword','auto-seo'),__('So Test','auto-seo'),__('Wow','auto-seo')),),
			'tags' => array(
				'Title' => 'on',
				'Description' => 'on',
				'Keywords' => 'on',
				'Robots' => 'off',
			),
			'title' =>  __('Auto SEO, [Example Set] | [Page Title]','auto-seo'),
			'description' => __('This is an example of a description placed with the Auto SEO WordPress Plugin. Auto SEO, [Example Set], [Example Set].','auto-seo'),
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
		// Setup domain for translation support
		add_action('init',array($this,'enable_translation_support'));
	}

	/*
		This is all the elements and the attributes formally allowed in the head (to the best of my ebility). This is used so we can escape the output properly.
		The entries linline horizontally are all the common attributres, the vertical ones are attributes for that specific element.
	*/
	private function get_head_kisses_escape_settings(){
		return [
			'title'=>[
				'accesskey'=>[],'class'=>[],'contenteditable'=>[],'dir'=>[],'draggable'=>[],'enmterkeyhint'=>[],'hidden'=>[],'id'=>[],'inert'=>[],'inputmode'=>[],'lang'=>[],'popoiver'=>[],'spellcheck'=>[],'style'=>[],'tabindex'=>[],'titile'=>[],'translate'=>[],
			],
			'meta'=>[
				'charset'=>[],
				'content'=>[],
				'http-equiv'=>[],
				'name'=>[],
				'accesskey'=>[],'class'=>[],'contenteditable'=>[],'dir'=>[],'draggable'=>[],'enmterkeyhint'=>[],'hidden'=>[],'id'=>[],'inert'=>[],'inputmode'=>[],'lang'=>[],'popoiver'=>[],'spellcheck'=>[],'style'=>[],'tabindex'=>[],'titile'=>[],'translate'=>[],
			],
			'style'=>[
				'media'=>[],
				'type'=>[],
				'accesskey'=>[],'class'=>[],'contenteditable'=>[],'dir'=>[],'draggable'=>[],'enmterkeyhint'=>[],'hidden'=>[],'id'=>[],'inert'=>[],'inputmode'=>[],'lang'=>[],'popoiver'=>[],'spellcheck'=>[],'style'=>[],'tabindex'=>[],'titile'=>[],'translate'=>[],
			],
			'base'=>[
				'href'=>[],
				'target'=>[],
				'accesskey'=>[],'class'=>[],'contenteditable'=>[],'dir'=>[],'draggable'=>[],'enmterkeyhint'=>[],'hidden'=>[],'id'=>[],'inert'=>[],'inputmode'=>[],'lang'=>[],'popoiver'=>[],'spellcheck'=>[],'style'=>[],'tabindex'=>[],'titile'=>[],'translate'=>[],

			],
			'link'=>[
				'crossorigin'=>[],
				'href'=>[],
				'hreflang'=>[],
				'media'=>[],
				'referrerpolicy'=>[],
				'rel'=>[],
				'sizes'=>[],
				'title'=>[],
				'type'=>[],
				'accesskey'=>[],'class'=>[],'contenteditable'=>[],'dir'=>[],'draggable'=>[],'enmterkeyhint'=>[],'hidden'=>[],'id'=>[],'inert'=>[],'inputmode'=>[],'lang'=>[],'popoiver'=>[],'spellcheck'=>[],'style'=>[],'tabindex'=>[],'titile'=>[],'translate'=>[],
			],
			'script'=>[
				'async'=>[],
				'crossorigin'=>[],
				'defer'=>[],
				'integrity'=>[],
				'nomodule'=>[],
				'referrerpolicy'=>[],
				'src'=>[],
				'type'=>[],
				'accesskey'=>[],'class'=>[],'contenteditable'=>[],'dir'=>[],'draggable'=>[],'enmterkeyhint'=>[],'hidden'=>[],'id'=>[],'inert'=>[],'inputmode'=>[],'lang'=>[],'popoiver'=>[],'spellcheck'=>[],'style'=>[],'tabindex'=>[],'titile'=>[],'translate'=>[],
			],
			'noscript'=>[
				'accesskey'=>[],'class'=>[],'contenteditable'=>[],'dir'=>[],'draggable'=>[],'enmterkeyhint'=>[],'hidden'=>[],'id'=>[],'inert'=>[],'inputmode'=>[],'lang'=>[],'popoiver'=>[],'spellcheck'=>[],'style'=>[],'tabindex'=>[],'titile'=>[],'translate'=>[],
			]
		];
	}

	/*
		It's been translated, Yay! Now we gotta load the mo files
	*/
	public function enable_translation_support(){
		load_textdomain('auto-seo',WP_PLUGIN_DIR.'/auto-seo/_l18n/'.get_locale().'.mo'); 
	}

	/*
		Setup Back end interface

		This does all that tedious work for trivial things like the settings page and admin scripting and styling
	*/
	public function add_menu_item(){
		// This will add the settings menu item
		add_menu_page(__('Auto SEO','auto-seo'),__('Auto SEO','auto-seo'),'manage_options','auto-seo-settings',array($this,'settings_page'),'dashicons-megaphone',77);
	}
	public function settings_page(){
		// Save the settings if sending post data
		if(isset($_POST['action'])&&$_POST['action']=='update'&&current_user_can('manage_options')){
			if(check_admin_referer( 'updating-auto-seo-settings' )){
				unset($_POST['action']);
				$this->save_settings($_POST);
				add_settings_error( 'auto-seo-messages', 'save_message', __( 'Settings Updated.', 'auto-seo' ), 'success' );
			}else{
				add_settings_error( 'auto-seo-messages', 'save_message', __( 'There was an error updating the settings.', 'auto-seo' ), 'error' );
			}
		}
		// Load Settings Page, kept externally for brevity's sake
		settings_errors( 'auto-seo-messages' );
		require_once('settings.php');
	}
	public function add_settings_link($links){
		$links[] = '<a href="admin.php?page=auto-seo-settings">'.__('Settings','auto-seo').'</a>';
		return $links;
	}
	public function admin_enqueued(){
		// Load the admin scripts and styles where needed
		// JS
		wp_enqueue_script('auto-seo-admin-js',plugin_dir_url( __FILE__ ).'admin.js',array('jquery'),true,['in_footer'=>true]);
		// CSS
		wp_enqueue_style('auto-seo-admin-css',plugin_dir_url( __FILE__ ).'admin.css',[],true);
		wp_enqueue_style('auto-seo-admin-css');
	}

	/*
		Start the buffer

		All this does is init the output buffer if enabled for that post type, nothing fancy.
	*/
	public function add_meta_tags_obstart(){
		if($this->settings['post_types'][get_post_type(get_the_ID())]=='on'){
			ob_start();
		}
	}

	/*
		This is the main core of the function, it will perform a bunch of regex checks to pull out the old meta data, 
		then add in new meta data as needed to replae it. HTML comments are left were things were removed so indicate 
		where they were originally found.
	*/
	public function add_meta_tags_obget(){
		
		//this whole section can be skipped if it's not enabled for this post type
		if($this->settings['post_types'][get_post_type(get_the_ID())]=='on' || ( isset($_POST['autoseo_compatibility']) && $_POST['autoseo_compatibility']=='check' && check_ajax_referer( 'auto-seo-check-compatibility' )) ){

			// Load up the additional keyword sets that are page specific.
			$this->load_special_keyword_sets();

			// Get the head, determin what meta items were going to add/change, remove old if any, add new, output head
			$head = ob_get_clean();
			
			// Determin what sections are on, and replace them as needed.
			foreach($this->settings['tags'] as $tag => $status){
				if($status=='on' || ( isset($_POST['autoseo_compatibility']) && $_POST['autoseo_compatibility']=='check') ){
					switch($tag){
						case 'Title':
							$head = preg_replace('~<title>[^(</)]*</ ?title>~','<!-- Auto SEO was here! -->',$head);
							$title = $this->replace_keyword_brackets($this->settings['title']);
							// Try and clean off any dividers from the title, in case the last bas is missing 
							$title = esc_html(preg_replace('~[^A-Za-z0-9,\.\']+$~','',$title));

							$head .= '<!-- Auto SEO Added -->'."\n".'<title>'.esc_attr($title).'</title>'."\n";
						break;
						
						case 'Description':
							$head = preg_replace('~<meta.*name=[\'|"]?description[\'|"]?.*/ ?>~','<!-- Auto SEO was here! -->',$head);
							$description = $this->replace_keyword_brackets($this->settings['description']);
							$head .= '<!-- Auto SEO Added -->'."\n".'<meta name="description" content="'.esc_attr($description).'" />'."\n";
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
							$head .= '<!-- Auto SEO Added -->'."\n".'<meta name="keywords" data-testing="fuck" content="'.esc_attr($keywords).'" />'."\n";
						break;
						case 'Robots':
							$head = preg_replace('~<meta.*name=[\'|"]?robots[\'|"]?.*/ ?>~','<!-- Auto SEO was here! -->',$head);
							$head .= '<!-- Auto SEO Added -->'."\n".'<meta name="robots" content="'.esc_attr($this->settings['robots']).'" />'."\n";
						break;
					}
				}
			}
			// were done working with this, we can echo it out but first there is some complicated escaping that needs to be done.
			echo wp_kses($head, $this->get_head_kisses_escape_settings());
		}
	}

	/*
		Load special keyword sets

		This will load up special page/post specific keyword sets. This includings things like Page Title, Tags, and 
		Categories. Once loaded they will go into the master keywords_sets list to get replaced during the custom 
		keyword replacment step.
	*/
	private function load_special_keyword_sets(){

		// Page title
		$this->settings['keyword_sets']['Page Title'] = single_post_title('',false);

		// Prepping for the ones that may have more than one option.
		$starting_point = round((int)get_the_ID()*pi());

		// Category (one at pseudo-random);
		$categories = get_the_category(get_the_ID());
		if(!is_bool($categories) && count($categories)>0){
			foreach($categories as $n => $cat){
				$categories[$n] = $cat->cat_name;
			}
			$count = $starting_point%count($categories);
			$this->settings['keyword_sets']['Category'] = $categories[$count];

			// Add all categories in comma seperated list
			$this->settings['keyword_sets']['Categories'] = implode(', ',$categories);
		}

		// Tag (one at pseudo-random);
		$tags = get_the_tags(get_the_ID());
		if(!is_bool($tags) && count($tags)>0){
			foreach($tags as $n => $tag){
				$tags[$n] = $tag->name;
			}
			$count = $starting_point%count($tags);
			$this->settings['keyword_sets']['Tag'] = $tags[$count];

			// Add all tags in comma seperated list
			$this->settings['keyword_sets']['Tags'] = implode(', ',$tags);
		}

		// Date of the post and the author
		$this->settings['keyword_sets']['Date'] = get_the_date(null,get_the_ID());
		$this->settings['keyword_sets']['Author'] = get_the_author_meta('display_name',get_post_field('post_author',get_the_ID(),'display'));

		// Comments
		$comments = wp_count_comments(get_the_ID());
		$this->settings['keyword_sets']['Comments'] = $comments->moderated;

		// The post format (secret because it's pretty much garbage but I didn't think of that till it was built)
		$format = get_post_format(get_the_ID());
		if($format!==false){
			$this->settings['keyword_sets']['Format'] = $format;
		}else{
			$this->settings['keyword_sets']['Format'] = '';
		}

		// The type of the post using it's display name
		$type = get_post_type_object(get_post_type(get_the_ID()));
		$this->settings['keyword_sets']['Type'] = $type->labels->singular_name;

		// For testing checking when adding new types
		// echo '<pre>';
		// var_dump($this->settings['keyword_sets']);
		// echo '</pre>';

	}

	/*
		Replace brackets in strings

		This is called by replace_keyword_brackets(), as well as on it's own. This is the function that does the actual 
		replacing of those brackets with real keywords. It will return a replaced string for use by the 
		add_meta_tags_obget() function
	*/
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
		
		// If we got a string it's just a dead simple replacement.
		}else{
			return str_ireplace('['.$bracket.']',$replacement,$string);
		}
	}

	/*
		Replace keyword brackets 

		Loop through each keyword set calling bracket_replace for each. So when I said random, I lied, it's pseudo 
		random, we don't want to keywords to be different every time Google crawls, so we use the id and some random 
		math to come up with a starting point that we loop through when determining what keyword gets placed. This is 
		done here once and passed on to the bracket_replace function for it's use.
	*/
	function replace_keyword_brackets($head){
		$starting_point = round((int)get_the_ID()*pi());
		foreach($this->settings['keyword_sets'] as $set_name => $keywords){
			$head = $this->bracket_replace($head,$set_name,$keywords,$starting_point);
		}
		return $head;
	}

	/*
		Save the settings
	*/
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

// Start everything upp
$autoseo = new autoseo();
