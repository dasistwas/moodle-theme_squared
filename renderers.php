<?php

class theme_squared_core_renderer extends core_renderer {
 
    public $squaredbodyclasses = array();

	public function heading($text, $level = 2, $classes = 'main', $id = null) {
		//for section headings//
		if($level == 3) {
			$content  = html_writer::start_tag('div', array('class'=>'headingwrap1'));
			$content  .= html_writer::start_tag('div', array('class'=>'headingwrap2'));

		}
		else {
			$content = "";
		}
			
		$content .= parent::heading($text, $level, $classes, $id);
		if($level == 3) {
			$content .= html_writer::end_tag('div');
			$content .= html_writer::end_tag('div');
		}
		return $content;
	}

	function block(block_contents $bc, $region) {
		$bc = clone($bc); // Avoid messing up the object passed in.
		if (empty($bc->blockinstanceid) || !strip_tags($bc->title)) {
			$bc->collapsible = block_contents::NOT_HIDEABLE;
		}
		if ($bc->collapsible == block_contents::HIDDEN) {
			$bc->add_class('hidden');
		}
		if (!empty($bc->controls)) {
			$bc->add_class('block_with_controls');
		}

		$testb = $bc->attributes['class'];
		$ptype = $this->page->pagetype;
		if ($ptype == 'site-index' && $testb == "block_navigation  block" || $ptype == 'site-index' && $testb == "block_settings  block" || $ptype == 'site-index' && $testb == "block_settings  block block_with_controls" || $ptype == 'site-index' && $testb == "block_settings  block hidden" || $ptype == 'site-index' && $testb == "block_navigation  block block_with_controls" || $ptype == 'site-index' && $testb == "block_navigation  block hidden") {
			//$bc->add_class('dock_on_load');
			$bc->add_class("vclass");
		}
		else if ($testb == "block_course_overview  block" || $testb == "block_course_overview  block hidden" || $testb == "block_course_overview  block hidden" || $testb == "block_course_overview  block block_with_controls") {

		}
		else {
		 $bc->add_class("hidden vclass block-hider-show");
		}

		$whichblock = $this->page->blocks->get_content_for_region($region, $this);

		$nofloat = '';

		foreach ($whichblock as $key => $value){

			if($key % 2 != 0 && $value->blockinstanceid == $bc->blockinstanceid){

				$nofloat = html_writer::tag('div', '',array('class' => 'clearfix sqclear'));

				$bc->add_class("rightblock");

			}

		}

		$skiptitle = strip_tags($bc->title);
		if (empty($skiptitle)) {
			$output = '';
			$skipdest = '';
		} else {
			$output = html_writer::tag('a', get_string('skipa', 'access', $skiptitle), array('href' => '#sb-' . $bc->skipid, 'class' => 'skip-block'));
			$skipdest = html_writer::tag('span', '', array('id' => 'sb-' . $bc->skipid, 'class' => 'skip-block-to'));
		}

		$output .= html_writer::start_tag('div', $bc->attributes);
		$output .= $this->block_header($bc);
		$output .= $this->block_content($bc);
		$output .= html_writer::end_tag('div');
		$output .= $this->block_annotation($bc);
		$output .= $skipdest;
		$output .= $nofloat;

		$this->init_block_hider_js($bc);
		return $output;
	}

	/**
	 * Produces a header for a block
	 *
	 * @param block_contents $bc
	 * @return string
	 */
	protected function block_header(block_contents $bc) {

		$title = '';
		if ($bc->title) {
			$title .= html_writer::tag('div', '', array('class' => 'courseblock-icon'));
			$title .= html_writer::tag('h2', $bc->title, null);
		}

		$controlshtml = $this->block_controls($bc->controls);

		$output = '';
		if ($title || $controlshtml) {
			$output .= html_writer::tag('div', html_writer::tag('div', html_writer::tag('div', '', array('class'=>'block_action')). $title . $controlshtml, array('class' => 'title')), array('class' => 'header'));
		}
		return $output;
	}

	// Added by Georg Mai§er and David Bogner based on work of Sam Hemelryk
	protected function render_custom_menu(custom_menu $menu) {
		global $CFG;
		require_once($CFG->libdir.'/coursecatlib.php');
			
		// get the custommenuitems
		$custommenu = $menu->get_children();
		
		// get all the categories and courses from the navigation node
		$categorytree = coursecat::get(0)->get_children();// get_course_category_tree();
		//print_object($categorytree);

		// Here we build the menu.		
			
		foreach ($categorytree as $categorytreeitem) {
			foreach ($custommenu as $custommenuitem) {
				if (($categorytreeitem->name == $custommenuitem->get_title())) {
					$branch = $custommenuitem;
					$this->add_category_to_custommenu($branch, $categorytreeitem);
					break;
				}
					
			}
		}
		return parent::render_custom_menu($menu);
	}


	// Added by Georg Maißer, based on work of Sam Hemelryk
	protected function add_category_to_custommenu(custom_menu_item $parent, coursecat $category) {

		// This value allows you to change the depth of the menu you want to show (reducing the depth may help with performance issues)
		// for courses and categories:
		$show_course_category_depth = 4;
		// for modules
		$show_modules_depth = 2;
		$categorychildren = $category->get_children();
		$actual_depth = $category->depth;

		// This value allows you to decide if you want to show modules on the last depth which is still displayed
		$show_deep_modules = false;

		// We add the Categories and Subcategories to the menu
		if (!empty($categorychildren)) {
			$i = 1;
			foreach ($categorychildren as $subcategory) {
				$actual_depth = $subcategory->depth;
					
				// we want to check if the depth of the given category is below the limit specified above
				if ($actual_depth > $show_course_category_depth) {
					continue;
				}		
				// the value "1000" is chosen to add the items at the end. By choosing a lower or even negative value, you can add these items in front of the manually created custommenuitems
				$sub_parent = $parent->add($subcategory->name, new moodle_url('/course/index.php', array('categoryid' => $subcategory->id)), null, 1000+$i);				
				$this->add_category_to_custommenu($sub_parent, $subcategory);
				$i++;
			}
		}
		// all courses visible to user
		$catcourses = $category->get_courses();
		// We add the courses and modules to the categories
		if (!empty($catcourses) && ($actual_depth <= $show_course_category_depth)) {
			foreach ($catcourses as $course) {
				$course_branch = $parent->add($course->shortname, new moodle_url('/course/view.php', array('id' => $course->id)), $course->fullname);

				// We add modules the the courses

				//first we check if we shall still show modules on this level

				$modules = get_course_mods($course->id);
				$courseobject = get_course($course->id);
				if (!empty($modules) && ($actual_depth <= $show_modules_depth)) {
					foreach ($modules as $module) {
						// We don't include the module if it can't be accessed by the visiting user
						if ($module->visible == 0 || !can_access_course($courseobject)) {
							continue;
						}
						if($module->modname == "label" ){
							continue;
						}
						// the normal $module object does not include the name, so we have to make a little deviation
						$module_object = get_coursemodule_from_id($module->modname, $module->id);
						// now we have all the info we need and can just add the node to the menu
						if($module_object){
							$course_branch->add($module_object->name, new moodle_url('/mod/'.$module->modname.'/view.php', array('id' => $module->id)), $module->modname);
						}
					}
				}
			}
		}

	}


	protected function squared_prepare_textlinks($textlinks){
		$textsnippets = explode(';',$textlinks);
		foreach ($textsnippets as $value){
			$textandlinks[] = explode(',', $value,2);
		}
		$renderedtext ='';
		$lastelement = end($textandlinks);
		if(empty($lastelement[0])){
			$lastelement = prev($textandlinks);
		}
		$attributes = array();
		foreach ($textandlinks as $value){
			if(empty($value[0])){
				continue;
			}
			/*
			 if($lastelement[0] == $value[0]){
			$attributes = array('class' => 'lastelement');
			}
			*/
			$renderedtext .= html_writer::start_tag('span',$attributes);
			$renderedtext .= html_writer::tag('a',trim($value[0]), array('href' => trim($value[1])));
			$renderedtext .= html_writer::end_tag('span');
		}
		$renderedtext .= html_writer::tag('span',$this->login_info(), array('class' => 'lastelement'));
		return $renderedtext;
	}
	/**
	 * Produces the footer
	 *
	 * @return string
	 */
	public function squared_textlinks($position) {
		$textlinks = ' ';
		if($position == 'footer'){
			if (!empty($this->page->theme->settings->footertext)) {
				$textlinks = $this->squared_prepare_textlinks($this->page->theme->settings->footertext);
			}
		} else {
			if (!empty($this->page->theme->settings->headerlinks)) {
				$textlinks = $this->squared_prepare_textlinks($this->page->theme->settings->headerlinks);
			}
		}
		$content = html_writer::tag('div',$textlinks, array('class' => 'footercontent'));
		return $content;
	}

	public function squared_socialicons(){
		$content = '';
		if (!empty($this->page->theme->settings->googlepluslink)) {
			$content .= html_writer::tag('a','<img src="'.$this->pix_url('gplus', 'theme').'" class="sicons" alt="google plus" />', array('href' => $this->page->theme->settings->googlepluslink, 'class' => 'icons'));
		}
		if (!empty($this->page->theme->settings->twitterlink)) {

			$content .= html_writer::tag('a','<img src="'.$this->pix_url('twitter', 'theme').'" class="sicons" alt="twitter" />', array('href' => $this->page->theme->settings->twitterlink, 'class' => 'icons'));

		}
		if (!empty($this->page->theme->settings->facebooklink)) {

			$content .= html_writer::tag('a','<img src="'.$this->pix_url('faceb', 'theme').'" class="sicons" alt="facebook" />', array('href' => $this->page->theme->settings->facebooklink, 'class' => 'icons'));

		}
		if (!empty($this->page->theme->settings->youtubelink)) {

			$content .= html_writer::tag('a','<img src="'.$this->pix_url('youtube', 'theme').'" class="sicons" alt="youtube" />', array('href' => $this->page->theme->settings->youtubelink, 'class' => 'icons'));

		}

		return $content;
	}

	public function squared_colorguide(){
		$cat = $this->page->categories;
		if(!empty($cat)) {
			$classid = "bgcolorcat".end($cat)->id;
			if($this->page->theme->settings->$classid){
				return $this->page->theme->settings->$classid;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Return the navbar content so that it can be echoed out by the layout
	 *
	 * @return string XHTML navbar
	 */
	public function navbar() {
		$items = $this->page->navbar->get_items();
		$htmlblocks = array();
		// Iterate the navarray and display each node
		$itemcount = count($items);
		$separator = get_separator();
		for ($i=0;$i < $itemcount;$i++) {
			$item = $items[$i];
			$item->hideicon = true;
			if ($i===0) {
				$content = html_writer::tag('li', $this->render($item),array('class' => 'navbar_'.$item->key.'_'.$item->type));
			} else {
				$content = html_writer::tag('li', $separator.$this->render($item),array('class' => 'navbar_'.$item->key.'_'.$item->type.' type'.$item->type));
			}
			$htmlblocks[] = $content;
		}

		//accessibility: heading for navbar list  (MDL-20446)
		$navbarcontent = html_writer::tag('span', get_string('pagepath'), array('class'=>'accesshide'));
		$navbarcontent .= html_writer::tag('ul', join('', $htmlblocks));
		// XHTML
		return $navbarcontent;
	}
    /**
     * Return the standard string that says whether you are logged in (and switched
     * roles/logged in as another user).
     * @param bool $withlinks if false, then don't include any links in the HTML produced.
     * If not set, the default is the nologinlinks option from the theme config.php file,
     * and if that is not set, then links are included.
     * @return string HTML fragment.
     */
    public function login_info($withlinks = null) {
        global $USER, $CFG, $DB, $SESSION, $OUTPUT;

        if (during_initial_install()) {
            return '';
        }

        if (is_null($withlinks)) {
            $withlinks = empty($this->page->layout_options['nologinlinks']);
        }

        $loginpage = ((string)$this->page->url === squared_get_login_url());
        $course = $this->page->course;
        if (session_is_loggedinas()) {
            $realuser = session_get_realuser();
            $fullname = fullname($realuser, true);
            if ($withlinks) {
                $loginastitle = get_string('loginas');
                $realuserinfo = " [<a href=\"$CFG->wwwroot/course/loginas.php?id=$course->id&amp;sesskey=".sesskey()."\"";
                $realuserinfo .= "title =\"".$loginastitle."\">$fullname</a>] ";
            } else {
                $realuserinfo = " [$fullname] ";
            }
        } else {
            $realuserinfo = '';
        }

        $loginurl = squared_get_login_url();
        $subscribeurl = preg_replace('/login\/index\.php/i', 'login/signup.php', $loginurl);

        if (empty($course->id)) {
            // $course->id is not defined during installation
            return '';
        } else if (isloggedin()) {
            $context = context_course::instance($course->id);

            $fullname = fullname($USER, true);
            // Since Moodle 2.0 this link always goes to the public profile page (not the course profile page)
            if ($withlinks) {
                $linktitle = get_string('viewprofile');
                $username = "<a href=\"$CFG->wwwroot/user/profile.php?id=$USER->id\" title=\"$linktitle\">$fullname</a>";
            } else {
                $username = $fullname;
            }
            if (is_mnet_remote_user($USER) and $idprovider = $DB->get_record('mnet_host', array('id'=>$USER->mnethostid))) {
                if ($withlinks) {
                    $username .= " from <a href=\"{$idprovider->wwwroot}\">{$idprovider->name}</a>";
                } else {
                    $username .= " from {$idprovider->name}";
                }
            }
            if (isguestuser()) {
                $loggedinas = $realuserinfo.get_string('loggedinasguest');
                if (!$loginpage && $withlinks) {
                    $loggedinas .= " <a class=\"standardbutton plainlogin\" href=\"$loginurl\">".get_string('login').'</a> 
                      <span class="loginlink"><a href="'.$subscribeurl.'">'.get_string('createaccount').'</a></span>';
                }
            } else if (is_role_switched($course->id)) { // Has switched roles
                $rolename = '';
                if ($role = $DB->get_record('role', array('id'=>$USER->access['rsw'][$context->path]))) {
                    $rolename = ': '.role_get_name($role, $context);
                }
                $loggedinas = '<span class="loggedintext">'. get_string('loggedinas', 'moodle', $username).$rolename.'</span>';
                if ($withlinks) {
                    $url = new moodle_url('/course/switchrole.php', array('id'=>$course->id,'sesskey'=>sesskey(), 'switchrole'=>0, 'returnurl'=>$this->page->url->out_as_local_url(false)));
                    $loggedinas .= '('.html_writer::tag('a', get_string('switchrolereturn'), array('href'=>$url)).')';
                }
            } else {
                $loggedinas = '<span class="loggedintext">'. $realuserinfo.get_string('loggedinas', 'moodle', $username).'</span>';
                if ($withlinks) {
                    $loggedinas .= html_writer::tag('div', $OUTPUT->user_picture($USER, array('size'=>174)), array('class'=>'userimg2'))." <span class=\"loggedinlogout\"> <a href=\"$CFG->wwwroot/login/logout.php?sesskey=".sesskey()."\">".get_string('logout').'</a></span>';
                }
            }
        } else {
            $loggedinas = get_string('loggedinnot', 'moodle');
            if (!$loginpage && $withlinks) {
                $loggedinas .= " <a class=\"standardbutton plainlogin\" href=\"$loginurl\">".get_string('login').'</a>
                  <span class="loginlink"><a href="'.$subscribeurl.'">'.get_string('createaccount').'</a></span>';
            }
        }

        $loggedinas = '<div class="logininfo">'.$loggedinas.'</div>';

        if (isset($SESSION->justloggedin)) {
            unset($SESSION->justloggedin);
            if (!empty($CFG->displayloginfailures)) {
                if (!isguestuser()) {
                    if ($count = count_login_failures($CFG->displayloginfailures, $USER->username, $USER->lastlogin)) {
                        $loggedinas .= '&nbsp;<div class="loginfailures">';
                        if (empty($count->accounts)) {
                            $loggedinas .= get_string('failedloginattempts', '', $count);
                        } else {
                            $loggedinas .= get_string('failedloginattemptsall', '', $count);
                        }
                        if (file_exists("$CFG->dirroot/report/log/index.php") and has_capability('report/log:view', context_system::instance())) {
                            $loggedinas .= ' (<a href="'.$CFG->wwwroot.'/report/log/index.php'.
                                                 '?chooselog=1&amp;id=1&amp;modid=site_errors">'.get_string('logs').'</a>)';
                        }
                        $loggedinas .= '</div>';
                    }
                }
            }
        }

        return $loggedinas;
    }
	
    /**
     * Check settings and return the slideshow as defined in the settings
     * 
     * @return string the html for the slideshow
     */
    public function squared_render_slideshow(){
     $o = "";
     $settings = $this->page->theme->settings;
     
     $slides = array();
     for ($i=1; $i<=5; $i++){
        $link = "fplink$i";
        $text =  "fptext$i";
        $title =  "fptitle$i";
        $position = "fppos$i";
        if(!empty($settings->$title)){
          //defaults if empty
          $slides[$i] = array('link' => '#', 'text' => '', 'title' => $settings->$title,'position' => 2);
          if (!empty($settings->$link)){
            $slides[$i]['link'] = $settings->$link;
           }
           if (!empty($settings->$text)){
            $slides[$i]['text'] = $settings->$text;
           }
           if (!empty($settings->$position)){
            $slides[$i]['position'] = $settings->$position;
           }
        } else if ($i === 1 AND empty($settings->$title)){
          $slides[$i] = array('link' => "#", 'text' => "Change me in the theme settings", 'title' => "Default title",'position' => 2);
        }
     }
     
     // add body class if only one slide is present and prevent animation
     if (count($slides) <= 1) {
       $this->squaredbodyclasses[] = 'oneblock';
     }

     foreach($slides as $key => $slide){
      $this->squaredbodyclasses[] = "blocklayout-$key-".$slide['position'];
      $o .= html_writer::start_div("scroll-header scroll$key");
      $o .= html_writer::start_div("headerblock text headerblock1");
      $o .= html_writer::link($slide['link'], "<span>".$slide['title']."</span>", array('class' => 'inner'));
      $o .= html_writer::end_div();
      $o .= html_writer::start_div('headerblock trans headerblock2');
      $o .= html_writer::link($slide['link'], '', array('class' => 'inner'));
      $o .= html_writer::end_div();
      $o .= html_writer::div('', 'clearer2');
      $o .= html_writer::start_div('headerblock para headerblock3');
      $o .= html_writer::link($slide['link'],  "<span>".$slide['text']."</span>", array('class' => 'inner'));
      $o .= html_writer::end_div();
      $o .= html_writer::start_div('headerblock trans headerblock4');
      $o .= html_writer::link($slide['link'], '', array('class' => 'inner'));
      $o .= html_writer::end_div();
      $o .= html_writer::end_div();
     }
     return $o;
    }
    
	//end class
}