<?php

emptyResource(SMARTY_COMPILE_DIR);
emptyResource(SMARTY_CACHE_DIR);
emptyResource(WEB_ROOT.'uploads/lessons/stamped'); // !!!
createEmptyFile(APP_ROOT.CACHES_DIR, 'find');

// --------------------------------------------------
// FUNCTIONS
// --------------------------------------------------

function emptyResource($path)
{
  removeResource($path, false);
  createEmptyFile($path);
}

function removeResource( $_target, $remove_last=true ) {

  //file?
  if( is_file($_target) ) {
    if( is_writable($_target) ) {
      if( @unlink($_target) ) {
        return true;
      }
    }

    return false;
  }

  //dir?
  if( is_dir($_target) ) {
    if( is_writeable($_target) ) {
      foreach( new DirectoryIterator($_target) as $_res ) {
        if( $_res->isDot() ) {
          unset($_res);
          continue;
        }

        if( $_res->isFile() ) {
          removeResource( $_res->getPathName() );
        } elseif( $_res->isDir() ) {
          removeResource( $_res->getRealPath() );
        }

        unset($_res);
      }

      if ( $remove_last ) {
        if( @rmdir($_target) ) {
          return true;
        }
        // go on to return false...
      } else {
        return true;
      }
    }

    return false;
  }
}

function createEmptyFile($path, $name='empty')
{
  if (substr($path, -1)!=DS) {
    $path .= DS;
  }

  $handle = fopen($path.$name, 'w');
  fclose($handle);

}

?>