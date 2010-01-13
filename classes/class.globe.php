<?php

require_once(ENDO_PACKAGES_ROOT.'AkelosInflector/Inflector.php');


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

  static $pluralize_exceptions = array(
    'missing' => 'missing', // keep this for internal
    'execute' => 'execute'  // keep this for internal
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
  public function load($names, $type='class', $show_errors=true)
  {
    if (!is_array($names)) {
      $names = array($names);
    }

    $type = strtolower($type);
    $constant_name = strtoupper(Globe::pluralize($type)).'_DIR';

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
      if ($type=='model'||$type=='controller') {
        $file = Inflector::underscore($file);
      }
      if ($type=='class'||$type=='model'||$type=='controller') {
        $file = strtolower($file);
      }
      $filename = $prefix.$file.$suffix;
      if ($filepath=Globe::find($filename, array(APP_ROOT.$dir, ENDO_ROOT.$dir, APP_PACKAGES_ROOT.$dir, ENDO_PACKAGES_ROOT.$dir))) {
        require_once($filepath);
      } else {
        if ($show_errors) {
          Error::set(ucfirst($type)." not found in '".Globe::clean_dir($filepath)."'!", 'fatal');
        }
        $success = false;
      }
    }

    return $success;
  }

  public function find($filename='', $paths=array(), $hit_cache=true)
  {
    // read cache?
    if ($hit_cache && empty(Globe::$caches[STR_FINDCACHE])) {
      Globe::$caches[STR_FINDCACHE] = Globe::file_get_split(APP_ROOT.CACHES_DIR.STR_FINDCACHE);
    }
    // check cache
    if ($hit_cache && array_key_exists($filename, Globe::$caches[STR_FINDCACHE])) {
      // return found in cache!
      return Globe::$caches[STR_FINDCACHE][$filename][0];
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
      Error::set("File '$filename' not found in cascade <pre>".print_r(array_merge($paths, $scaffold_paths), true)."</pre>");
      return false;
    }

  }

  public function init($name, $type='class', $show_errors=true)
  {
    if (Globe::load($name, $type, $show_errors)) {
      $class_name = Globe::classify($name, $type);
      return new $class_name();
    } else {
      return new stdClass();
    }
  }

  // --------------------------------------------------
  // PLURALIZE / SINGULARIZE
  // --------------------------------------------------

  public function pluralize($str='')
  {
    $str = strtolower($str);
    if (array_key_exists($str, Globe::$pluralize_exceptions)) {
      return Globe::$pluralize_exceptions[$str];
    } else {
      return Inflector::pluralize($str);
    }
  }

  public function singularize($str='')
  {
    $str = strtolower($str);
    $singularize_exceptions = array_flip(Globe::$pluralize_exceptions);
    if (array_key_exists($str, $singularize_exceptions)) {
      return $singularize_exceptions[$str];
    } else {
      return Inflector::singularize($str);
    }
  }

  /**
   * @todo find possible other uses of 'Globe::classify'
   */
  public function classify($name='', $type='class')
  {
    $type = strtolower($type);

    // unify
    if (!preg_match('/'.$type.'$/Ui', $name) && $type=='controller') {
      $name .= '_'.$type;
    }

    // split
    $words = explode('_', $name);

    foreach ($words as $key => $word) {
      // singularize/pluralize before-last?
      if ($key==count($words)-2) {
        if ($type=='controller' && ($word!='app' && $word!='endo')) {
          $word = Globe::pluralize($word);
        } elseif ($type=='model') {
          $word = Globe::singularize($word);
        }
      }
      // leave last alone
      // singularize all others
      elseif ($key!=count($words)-1) {
        $word = Globe::singularize($word);
      }
      $words[$key] = $word;
    }

    return Inflector::camelize(implode('_', $words));
  }

  // --------------------------------------------------
  // PATH FUNCTIONS
  // --------------------------------------------------

  public function clean_dir($path='')
  {
    return str_replace(ROOT, '', $path);
  }

  public function get_template($name, $model=null, $type=null)
  {
    $name = $name==false || $name==null ? 'none' : $name;
    $type = $type!=DEFAULT_REQUEST_TYPE && $type!='php' ? $type.DS : '';
    return $filename = $model.DS.$type.$name.'.'.SMARTY_TEMPLATE_EXT;
  }

  function file_get_split($filename, $delimiter='|')
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

  public function for_layout($variable, $value=null, $append=false)
  {
    $variable = $variable.Globe::FOR_LAYOUT_SUFFIX;
    if ($append) {
      if (!array_key_exists($variable, Globe::$variables_for_layout)) {
        $tmp = !is_string($value) ? array($value) : $value;
      } else {
        $tmp = Globe::$variables_for_layout[$variable];
        if (!is_string($tmp)) {
          array_push($tmp, $value);
        } else {
          $tmp .= $value;
        }
      }
      $value = $tmp;
    }
    return Globe::$variables_for_layout[$variable] = $value;
  }

  public function get_for_layout($variable)
  {
    return array_get(Globe::$variables_for_layout, $variable.Globe::FOR_LAYOUT_SUFFIX);
  }

  // --------------------------------------------------
  // TOOLS
  // --------------------------------------------------

  public function reindex_collection($collection, $current_id=0, &$current_index=0)
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

  public function collection_to_options($collection)
  {
    foreach ($collection as $id => $object) {
      $collection[$id] = $object->{$object->name_fields[0]};
    }
    return $collection;
  }

}

?>