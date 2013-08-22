<?php if (!defined('APPLICATION')) exit();
/*
Copyright 2013 Alessandro Miliucci <lifeisfoo@gmail.com>
This file is part of VanillaStarter <https://github.com/lifeisfoo/VanillaStarter>

VanillaStarter is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

VanillaStarter is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with VanillaStarter. If not, see <http://www.gnu.org/licenses/>.

Based on the Vanilla Forums Example plugin, released under the GPL3 or (at your option) any later version.
<https://github.com/vanillaforums/Addons/tree/master/plugins/Example>
*/

// Define the plugin:
$PluginInfo['VanillaStarter'] = array(
	'Name' => 'VanillaStarter',//accept whitespaces
	'Description' => 'Quick start a new plugin development.',
	'Version' => '0.1',
	'RequiredApplications' => array('Vanilla' => '2.1a1'),
	'RequiredTheme' => FALSE, 
	'RequiredPlugins' => FALSE,
	//if you plugin rely on other plugins, replace the above line with the below
	//'RequiredPlugins' => array('AnotherPlugin' => '0.1', 'SuperPlugin' => '1.3'),
	'HasLocale' => FALSE,
	'MobileFriendly' => TRUE, // if FALSE this plugin hooks will not be executed when browsing from mobile
	'SettingsUrl' => '/plugin/example',
	'SettingsPermission' => 'Garden.AdminUser.Only',
	'Author' => "Alessandro Miliucci",
	'AuthorEmail' => 'lifeisfoo@gmail.com',
	'AuthorUrl' => 'http://forkwait.net'
	);

class VanillaStarterPlugin extends Gdn_Plugin {
	
	private $_UrlMapping = array(
		//Auto create on enable (and delete on disable) these routes
    		/*'AnotherAction' => 'plugin/Example/AnotherAction',
    		'MyGreatAction' => 'plugin/Example/MyGreatAction'*/
		);

	/**
	* Plugin constructor
	*
	* This fires once per page load, during execution of bootstrap.php. It is a decent place to perform
	* one-time-per-page setup of the plugin object. Be careful not to put anything too strenuous in here
	* as it runs every page load and could slow down your forum.
	*/
	public function __construct() {

	}

	/**
	* Base_Render_Before Event Hook
	*
	* This is a common hook that fires for all controllers (Base), on the Render method (Render), just 
	* before execution of that method (Before). It is a good place to put UI stuff like CSS and Javascript 
	* inclusions. Note that all the Controller logic has already been run at this point.
	*
	* @param $Sender Sending controller instance
	*/
	public function Base_Render_Before($Sender) {
		$Sender->AddCssFile('example.css');
		$Sender->AddJsFile('example.js');
	}

	/**
	* Create a method called "Example" on the PluginController
	*
	* One of the most powerful tools at a plugin developer's fingertips is the ability to freely create
	* methods on other controllers, effectively extending their capabilities. This method creates the 
	* Example() method on the PluginController, effectively allowing the plugin to be invoked via the 
	* URL: http://www.yourforum.com/plugin/Example/
	*
	* From here, we can do whatever we like, including turning this plugin into a mini controller and
	* allowing us an easy way of creating a dashboard settings screen or custom plugin action.
	*
	* @param $Sender Sending controller instance
	*/
	public function PluginController_Example_Create($Sender) {
	/*
	 * If you build your views properly, this will be used as the <title> for your page, and for the header
	 * in the dashboard. Something like this works well: <h1><?php echo T($this->Data['Title']); ?></h1>
	 */
	$Sender->Title('Example Plugin');
	$Sender->AddSideMenu('plugin/example');

	/*
	 * This method does a lot of work. It allows a single method (PluginController::Example() in this case) 
	 * to "forward" calls to internal methods on this plugin based on the URL's first parameter following the 
	 * real method name, in effect mimicing the functionality of as a real top level controller. 
	 *
	 * For example, if we accessed the URL: http://www.yourforum.com/plugin/Example/test, Dispatch() here would
	 * look for a method called ExamplePlugin::Controller_Test(), and invoke it. Similarly, we we accessed the
	 * URL: http://www.yourforum.com/plugin/Example/foobar, Dispatch() would find and call 
	 * ExamplePlugin::Controller_Foobar().
	 *
	 * The main benefit of this style of extending functionality is that all of a plugin's external API is 
	 * consolidated under one namespace, reducing the chance for random method name conflicts with other
	 * plugins. 
	 *
	 * Note: When the URL is accessed without parameters, Controller_Index() is called. This is a good place
	 * for a dashboard settings screen.
	 *
	 * Note: Every request like http://www.yourforum.com/plugin/Example/YOUR_ACTION_NAME
	 * will executi this function before the dedicated Controller_YOUR_ACTION_NAME() function.
	 * Good place to add global permission checks 
	 */
	$this->Dispatch($Sender, $Sender->RequestArgs); //enable the mini controller functionality
	}

	//dispatched from http://www.yourforum.com/plugin/Example
	public function Controller_Index($Sender) {
		// Prevent non-admins from accessing this page
		$Sender->Permission('Garden.Settings.Manage');

		$Sender->SetData('PluginDescription',$this->GetPluginKey('Description'));

		$Validation = new Gdn_Validation();
		$ConfigurationModel = new Gdn_ConfigurationModel($Validation);
		$ConfigurationModel->SetField(array(
			'Plugin.Example.RenderCondition'    => 'all',
			'Plugin.Example.TrimSize'     => 100
			));

		$Sender->Form = new Gdn_Form();
		// Set the model on the form.
		$Sender->Form->SetModel($ConfigurationModel);

		// If seeing the form for the first time...
		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			// Apply the config settings to the form.
			$Sender->Form->SetData($ConfigurationModel->Data);
		} else {
			$ConfigurationModel->Validation->ApplyRule('Plugin.Example.RenderCondition', 'Required');

			$ConfigurationModel->Validation->ApplyRule('Plugin.Example.TrimSize', 'Required');
			$ConfigurationModel->Validation->ApplyRule('Plugin.Example.TrimSize', 'Integer');

			$Saved = $Sender->Form->Save();
			if ($Saved) {
				$Sender->StatusMessage = T("Your changes have been saved.");
			}
		}

		// GetView() looks for files inside plugins/PluginFolderName/views/ and returns their full path. Useful!
		$Sender->Render($this->GetView('example.php'));
	}

	//dispatched from http://www.yourforum.com/plugin/Example/AnotherAction
	public function Controller_AnotherAction($Sender){

	}

	//dispatched from http://www.yourforum.com/plugin/Example/MyGreatAction
	public function Controller_MyGreatAction($Sender){

	}

	/**
	 * Add a link to the dashboard menu
	 * 
	 * By grabbing a reference to the current SideMenu object we gain access to its methods, allowing us
	 * to add a menu link to the newly created /plugin/Example method.
	 *
	 * @param $Sender Sending controller instance
	 */
	public function Base_GetAppSettingsMenuItems_Handler($Sender) {
		$Menu = &$Sender->EventArguments['SideMenu'];
		$Menu->AddLink('Add-ons', 'Example', 'plugin/example', 'Garden.AdminUser.Only');
	}

	/**
	 * Add fields to registration forms.
	 */
	public function EntryController_RegisterBeforePassword_Handler($Sender) {
	/* See 
	 * https://github.com/vanillaforums/Garden/blob/master/plugins/ProfileExtender/class.profileextender.plugin.php
	 * for an extended example
	 */
	}

	/**
	 * Required fields on registration forms.
	 */
	public function EntryController_RegisterValidation_Handler($Sender) {
	/* See 
	 * https://github.com/vanillaforums/Garden/blob/master/plugins/ProfileExtender/class.profileextender.plugin.php
	 * for an extended example
	 */
	}


	/**
	 * Hook into the rendering of each discussion link
	 *
	 * How did we find this event? We know we're trying to display a line of text when each discussion is rendered
	 * on the /discussions/ page. That page corresponds to the DiscussionsController::Index() method. This method,
	 * by default, renders the views/discussions/index.php view. That view contains this line:
	 *    <?php include($this->FetchViewLocation('discussions')); ?>
	 *
	 * So we look inside views/discussions/discussions.php. We find a loop the calls WriteDiscussion() for each 
	 * discussion in the list. WriteDiscussion() fires several events each time it is called. One of those events
	 * is called "AfterDiscussionTitle". Since we know that the parent controller context is "DiscussionsController",
	 * and that the event's name is "AfterDiscussionTitle", it is easy to see that our handler method should be called
	 *
	 *     DiscussionsController_AfterDiscussionTitle_Handler()
	 */
	public function DiscussionsController_AfterDiscussionTitle_Handler($Sender) {
		/*
		echo "<pre>";
		print_r($Sender->EventArguments['Discussion']);
		echo "</pre>";
		*/

		/*
		 * The 'C' function allows plugins to access the config file. In this call, we're looking for a specific setting
		 * called 'Plugin.Example.TrimSize', but defaulting to a value of '100' if the setting cannot be found.
		 */
		$TrimSize = C('Plugin.Example.TrimSize', 100);

		/*
		 * We're using this setting to allow conditional display of the excerpts. We have 3 settings: 'all', 'announcements', 
		 * 'discussions'. They do what you'd expect!
		 */
		$RenderCondition = C('Plugin.Example.RenderCondition', 'all');

		$Type = (GetValue('Announce', $Sender->EventArguments['Discussion']) == '1') ? "announcement" : "discussion";
		$CompareType = $Type.'s';

		if ($RenderCondition == "all" || $CompareType == $RenderCondition) {
			/*
			 * Here, we remove any HTML from the Discussion Body, trim it down to a pre-defined length, and then
			 * output it to discussions list inside a div with a class of 'ExampleDescription'
			 */
			echo Wrap(SliceString(strip_tags($Sender->EventArguments['Discussion']->Body),$TrimSize), 'div', array(
			'class'  => "ExampleDescription"
			));
		}
	}

	/**
	 * Plugin setup
	 *
	 * This method is fired only once, immediately after the plugin has been enabled in the /plugins/ screen, 
	 * and is a great place to perform one-time setup tasks, such as database structure changes, 
	 * addition/modification ofconfig file settings, filesystem changes, etc.
	 */
	public function Setup() {

		// Set up the plugin's default values
		SaveToConfig('Plugin.Example.TrimSize', 100);
		SaveToConfig('Plugin.Example.RenderCondition', "all");
		$this->_SetRoutes(); //auto URLs mapping

		/*
		// Create table GDN_Example, if it doesn't already exist
		Gdn::Structure()
		 ->Table('Example')
		 ->PrimaryKey('ExampleID')
		 ->Column('Name', 'varchar(255)')
		 ->Column('Type', 'varchar(128)')
		 ->Column('Size', 'int(11)')
		 ->Column('InsertUserID', 'int(11)')
		 ->Column('DateInserted', 'datetime')
		 ->Column('ForeignID', 'int(11)', TRUE)
		 ->Column('ForeignTable', 'varchar(24)', TRUE)
		 ->Set(FALSE, FALSE);
		*/
	}

	/**
	 * Plugin cleanup
	 *
	 * This method is fired only once, immediately before the plugin is disabled, and is a great place to 
	 * perform cleanup tasks such as deletion of unsued files and folders.
	 */
	public function OnDisable() {
		// Optional. Delete this if you don't need it.
		RemoveFromConfig('Plugin.Example.TrimSize');
		RemoveFromConfig('Plugin.Example.RenderCondition');
		$this->_SetRoutes(TRUE);
	}
	
        /**
	 * Special function automatically run upon clicking 'Remove' on your application.
         */
    	public function CleanUp() {
        	// Optional. Delete this if you don't need it.
    	}
	
	/* SUPPORT FUNCTIONS */
	
	private function _SetRoutes($Delete = FALSE)
        {
	        foreach ($this->_UrlMapping as $Short => $Real) {
	            $MatchRoute = Gdn::Router()->MatchRoute('^'.$Short.'(/?.*)$');
	            if($MatchRoute && $Delete) {
	                Gdn::Router()->DeleteRoute('^'.$Short.'(/?.*)$');
	            } elseif(!$MatchRoute && !$Delete) {
	                Gdn::Router()->SetRoute('^'.$Short.'(/?.*)$', $Real.'$1', 'Internal');
	            }
	        }   
        }
        
        private function _VarDump($Obj)
        {
        	echo "<pre>";
        	var_dump($Obj);
        	echo "</pre>";
        }

        private function _PrintR($Obj)
        {
        	echo "<pre>";
        	print_r($Obj);
        	echo "</pre>";
    	}

}
