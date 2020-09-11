<?php

/* Migrate ElementForm to DeskallForm */

use SilverStripe\Dev\BuildTask;
use DNADesign\ElementalUserForms\Model\ElementForm;

class MigrateFormElement extends BuildTask
{

    protected $title = 'MigrateFormElement';

    protected $description = 'Ersetzt Formular BLock durch neuen DeskallForm';

    public function run($request)
    {
        $count = 0;
        $forms = ElementForm::get();
        foreach ($forms as $form) {
             print_r($form->toMap());
            // $newForm = new FormBlock();
            // $newForm->write();
            $count ++;
        }
        
        echo 'Finished migrating ' . $count . ' forms<br>';
    }
}
