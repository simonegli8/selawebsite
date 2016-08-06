<?php
defined('is_running') or die('Not an entry point...');

class Admin_GoogleAnalytics
{
  const mVersion           ='1.0';
  var $mKey                = 'UA-NNNNNNNN-N';
  var $mDisplayFeatures    = 0;

  function Admin_GoogleAnalytics()
  {
    $this->loadConfig();

    $cmd                   = common::GetCommand();

    switch($cmd){
      case 'saveConfig':
        $this->saveConfig();
        break;
    }
		$this->showForm();
  }

  function showForm()
  {
    global $langmessage;
    echo '<h1>GoogleAnalytics (v'.self::mVersion.')</h1>';

    echo '<h2>Tracking-ID</h2>';

    echo '<form action="'.common::GetUrl('Admin_GoogleAnalytics').'" method="post">';

    echo '<p>Enter your Tracking-ID for Universal Analytics.</br>';
    echo '<input type="text" name="key" value="'.$this->mKey.'" class="gpinput" style="width:200px" />';
    echo '</p>';

    echo '<p>Require Display Features plugin.</br>';
    echo '<select name="displayFeatures">';
    if( $this->mDisplayFeatures == 1)
    {
      echo '  <option value="0">No</option>';
      echo '  <option value="1" selected="selected">Yes</option>';
    }
    else
    {
      echo '  <option value="0" selected="selected">No</option>';
      echo '  <option value="1">Yes</option>';
    }
    echo '</select>';
    echo '</p>';

    echo '<input type="hidden" name="cmd" value="saveConfig" />';

    echo '<input type="submit" value="'.$langmessage['save_changes'].'" class="gpsubmit"/>';
    echo '</p>';
    echo '</form>';
  }

  function saveConfig()
  {
    global                   $addonPathData;
    global                   $langmessage;

    $configFile            = $addonPathData.'/config.php';
    $config                = array();
    $config['key']         = $_POST['key'];
    $config['displayFeatures'] = $_POST['displayFeatures'];
    $this->mKey            = $config['key'];
    $this->mDisplayFeatures = $config['displayFeatures'];

    if( !gpFiles::SaveArray($configFile,'config',$config) )
    {
      message($langmessage['OOPS']);
      return false;
    }

    message($langmessage['SAVED']);
    return true;
  }

  function loadConfig()
  {
    global                   $addonPathData;

    $configFile            = $addonPathData.'/config.php';
    include_once $configFile;

    if (isset($config)) {
      $this->mKey             = $config['key'];
      $this->mDisplayFeatures = $config['displayFeatures'];
    }
  }
}
?>

