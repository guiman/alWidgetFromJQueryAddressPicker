<?php
/**
 * Input address-picker widget based on JQueryAddressPicker(https://github.com/sgruhier/jquery-addresspicker).
 *
 * @author Alvaro F. Lara <alvarofernandolara@gmail.com>
 *
 */

class alWidgetFormJQueryAddressPicker extends sfWidgetFormInput
{

  protected function configure($options = array(), $attributes = array()) {
    parent::configure($options, $attributes);

    $this->addOption('legend_text','You can drag and drop the marker to the correct location');
    $this->addOption('locality','Locality');
    $this->addOption('country','Country');
    $this->addOption('add_map', false);

    $this->addOption('template',  <<<EOF
    <div id="input">
      <label>%name%: </lable> <input id="%id%" name="%name%"> <br/>
    </div>
    <div id="extra-info">
      <label>%locality%: </label> <input id="locality" disabled=disabled> <br/>
      <label>%country%:  </label> <input id="country" disabled=disabled> <br/>
      <label>Lat:      </label> <input id="lat" disabled=disabled> <br/>
      <label>Lng:      </label> <input id="lng" disabled=disabled> <br/>
    </div>
    <div id="map"></div>
    <div id="legend">%legend%</div>
EOF
    );
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $map_input = str_replace(array('%name%','id','%locality%','%country%','%legend%'),
                             array($name,
                                   $this->generateId($name),
                                   $this->getOption('locality'),
                                   $this->getOption('country'),
                                   $this->getOption('legend')),
                             $this->getOption('template')
                             );
    $return = ($this->getOption("add_map")? $map_input : parent::render($name,$value,$attributes,$errors));

    return $return . $this->renderJavascript($this->generateId($name));
  }

  protected function renderJavascript($name)
  {
    if($this->getOption("add_map"))
    {
      $javascript = '
      var addresspickerMap = $( "#'.$name.'" ).addresspicker({
        elements: {
          map:      "#map",
          lat:      "#lat",
          lng:      "#lng",
          locality: "#locality",
          country:  "#country"
        }
      });
      var gmarker = addresspickerMap.addresspicker( "marker");
      gmarker.setVisible(true);
      addresspickerMap.addresspicker( "updatePosition");';
    } else {
      $javascript = 'var addresspicker = $( "#'.$name.'" ).addresspicker();';
    }

    return  javascript_tag($javascript);
  }

  /*
   * Required Javascripts for this widget
   */
  public function getJavaScripts()
  {
    $javascripts = array("http://maps.google.com/maps/api/js?sensor=false",
                         "/js/jquery-1.4.4.min.js",
                         "/js/jquery-ui-1.8.7.min.js",
                         "/js/jquery.ui.addresspicker.js");

    return array_merge($javascripts,parent::getJavaScripts());
  }

  /*
   * Required Stylesheets for this widget
   */
  public function getStylesheets()
  {
    return array_merge(parent::getStylesheets(),array("/css/themes/base/jquery.ui.all.css" => "screen"));
  }
}
