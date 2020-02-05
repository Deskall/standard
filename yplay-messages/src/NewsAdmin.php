<?php
use SilverStripe\Admin\ModelAdmin;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Subsites\Model\Subsite;
use SilverStripe\Subsites\State\SubsiteState;

class NewsAdmin extends ModelAdmin {

    public function subsiteCMSShowInMenu(){
        return true;
    }

   private static $managed_models = array(
       'News' => array ('title' => 'Meldungen'),
       // 'NewsTemplate' => array ('title' => 'Meldungsvorlagen'),
   );


    private static $menu_icon = 'yplay-messages/img/notification-16x16.png';
    private static $url_segment = 'notifications';
    private static $menu_title = 'Meldungen';
    public $showImportForm = false;

    public function getEditForm($id = null, $fields = null) {
        $form = parent::getEditForm($id, $fields);

        

        return $form;
    }

    public function getList(){
        $list = parent::getList();
        // $list =  $list->filter('SubsiteID',SubsiteState::singleton()->getSubsiteId());
        return $list;
    }
}

