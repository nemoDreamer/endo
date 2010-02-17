<?php

require_once(APP_ROOT.CLASSES_DIR.'class.app_inflector.php');

/**
 * Globe
 * Repository of static functions
 *
 * @package default
 * @author Philip Blyth
 */
class Globe {

  // --------------------------------------------------
  // STATIC VARIABLES
  // --------------------------------------------------

  static $variables_for_layout = array(
    'title_for_layout' => '',
    'sidebar_for_layout' => ''
  );

  static $caches = array();

  const FOR_LAYOUT_SUFFIX = '_for_layout';

  // --------------------------------------------------
  // LOAD
  // --------------------------------------------------

  /**
   * Loads files in bulk.
   *
   * @param mixed $names name or array of names
   * @param string $type class/include/file/package/controller/model
   * @return bool success?
   * @author Philip Blyth
   */
  static function Load($names, $type='class', $show_errors=true)
  {
    if (!is_array($names)) {
      $names = array($names);
    }

    $type = strtolower($type);
    $constant_name = strtoupper(AppInflector::pluralize($type)).'_DIR';

    list($dir, $prefix, $suffix) = array(
      defined($constant_name) ? constant($constant_name) : '',
      $type.'.',
      '.php'
    );

    switch ($type) {
      case 'include':
        $prefix = '';
        break;
      case 'file':
        $dir = $prefix = $suffix = '';
        break;
      case 'package':
        $prefix = '';
        break;
      // case 'class':
      // case 'model':
      // case 'controller':
      //   $file = strtolower($file);
      //   break;
    }

    $success = true;

    foreach ($names as $file) {
      if ($type=='class'||$type=='model' || ($type=='controller' && ($file!='app' && $file!='endo'))) {
        $file = AppInflector::fileize($file, $type);
      }
      $filename = $prefix.$file.$suffix;
      if ($filepath=self::Find($filename, array(APP_ROOT.$dir, ENDO_ROOT.$dir, APP_PACKAGES_ROOT.$dir, ENDO_PACKAGES_ROOT.$dir))) {
        require_once($filepath);
      } else {
        if ($show_errors) {
          Error::Set(ucfirst($type)." not found in '".self::CleanDir($filepath)."'!", 'fatal');
        }
        $success = false;
      }
    }

    return $success;
  }

  static function Find($filename='', $paths=array(), $hit_cache=true)
  {
    // read cache?
    if ($hit_cache && empty(self::$caches[STR_FINDCACHE])) {
      self::$caches[STR_FINDCACHE] = self::FileGetSplit(APP_ROOT.CACHES_DIR.STR_FINDCACHE);
    }
    // check cache
    if ($hit_cache && array_key_exists($filename, self::$caches[STR_FINDCACHE])) {
      // return found in cache!
      return self::$caches[STR_FINDCACHE][$filename][0];
    } else {

      // else, cascade through paths
      foreach ($paths as $path) {
        if (file_exists($result=$path.$filename)) {
          // save found to cache
          if ($hit_cache) {
            file_put_contents(APP_ROOT.CACHES_DIR.STR_FINDCACHE, $filename."|".$result."\n", FILE_APPEND);
          }
          // return found!
          return $result;
        }
      }

      // else, check scaffolding
      $scaffold_paths = array(APP_ROOT.SMARTY_SCAFFOLD_DIR.DS, ENDO_ROOT.SMARTY_SCAFFOLD_DIR.DS);
      $scaffold_filename = ($ds_pos=strpos($filename, DS)) !== false ? substr($filename, $ds_pos+1) : $filename;
      foreach ($scaffold_paths as $path) {
        if (file_exists($result=$path.$scaffold_filename)) {
          // return scaffold!
          return $result;
        }
      }

      // not found. set error...
      Error::Set("File '$filename' not found in cascade <pre>".print_r(array_merge($paths, $scaffold_paths), true)."</pre>");
      return false;
    }
  }

  static function Init($name, $type='class', $show_errors=true)
  {
    if (self::Load($name, $type, $show_errors)) {
      $class_name = AppInflector::classify($name, $type);
      return new $class_name();
    } else {
      return new stdClass();
    }
  }

  // --------------------------------------------------
  // PATH FUNCTIONS
  // --------------------------------------------------

  static function CleanDir($path='')
  {
    return str_replace(ROOT, '', $path);
  }

  static function GetTemplate($name, $model=null, $type=null)
  {
    $name = $name==false || $name==null ? 'none' : $name;
    $type = $type!=DEFAULT_REQUEST_TYPE && $type!='php' ? $type.DS : '';
    return $filename = $model.DS.$type.$name.'.'.SMARTY_TEMPLATE_EXT;
  }

  static function FileGetSplit($filename, $delimiter='|')
  {
    $output = array();
    $file_lines = explode("\n", file_get_contents($filename));
    foreach ($file_lines as $key => $line) {
      $line = explode($delimiter, $line);
      if (!empty($line)) {
        $output[array_shift($line)] = $line;
      }
    }
    return $output;
  }

  // --------------------------------------------------
  // VIEW FUNCTIONS
  // --------------------------------------------------

  static function ForLayout($variable, $value=null, $append=false)
  {
    $variable = $variable.self::FOR_LAYOUT_SUFFIX;
    if ($append) {
      if (!array_key_exists($variable, self::$variables_for_layout)) {
        $tmp = !is_string($value) ? array($value) : $value;
      } else {
        $tmp = self::$variables_for_layout[$variable];
        if (!is_string($tmp)) {
          array_push($tmp, $value);
        } else {
          $tmp .= $value;
        }
      }
      $value = $tmp;
    }
    return self::$variables_for_layout[$variable] = $value;
  }

  static function GetForLayout($variable)
  {
    return array_get(self::$variables_for_layout, $variable.self::FOR_LAYOUT_SUFFIX);
  }

  // --------------------------------------------------
  // TOOLS
  // --------------------------------------------------

  static function ReindexCollection($collection, $current_id=0, &$current_index=0)
  {
    $index = 0;
    $output = array();
    foreach ($collection as $id => $data) {
      $output[$index] = $data;
      if ($current_id==$data->id) {
        $current_index = $index;
      }
      $index++;
    }
    return $output;
  }

  static function CollectionToOptions($collection)
  {
    foreach ($collection as $id => $object) {
      $collection[$id] = $object->{$object->name_fields[0]};
    }
    return $collection;
  }

}

?>