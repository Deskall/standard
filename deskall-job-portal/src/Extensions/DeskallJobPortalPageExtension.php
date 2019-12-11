<?php

use SilverStripe\ORM\DataExtension;


class DeskallJobPortalPageExtension extends DataExtension
{
    public function RegisterPage($groupcode){
    	$group = Group::get()->filter('Code',$groupcode)->first();
    	if ($group){
    		return RegisterPage::get()->filter('GroupID',$group)->first();
    	}
        return null;
    }
}