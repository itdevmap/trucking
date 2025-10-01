<?php
require('fpdf.php');

class PDF_ViewPref extends FPDF {

    protected $DisplayPreferences = '';

    // setter yang jelas
    function SetViewerPreferences($preferences) {
        $this->DisplayPreferences = $preferences;
    }

    function _putcatalog()
    {
        parent::_putcatalog();

        if (strpos($this->DisplayPreferences,'FullScreen') !== false)
            $this->_out('/PageMode /FullScreen');

        if ($this->DisplayPreferences) {
            $this->_out('/ViewerPreferences <<');

            if (strpos($this->DisplayPreferences,'HideMenubar') !== false)
                $this->_out('/HideMenubar true');
            if (strpos($this->DisplayPreferences,'HideToolbar') !== false)
                $this->_out('/HideToolbar true');
            if (strpos($this->DisplayPreferences,'HideWindowUI') !== false)
                $this->_out('/HideWindowUI true');
            if (strpos($this->DisplayPreferences,'DisplayDocTitle') !== false)
                $this->_out('/DisplayDocTitle true');
            if (strpos($this->DisplayPreferences,'CenterWindow') !== false)
                $this->_out('/CenterWindow true');
            if (strpos($this->DisplayPreferences,'FitWindow') !== false)
                $this->_out('/FitWindow true');

            $this->_out('>>');
        }
    }
}
?>
