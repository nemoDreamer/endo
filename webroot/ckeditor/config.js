/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
  config.language = 'en';
  config.uiColor = '#d4d0c8';

  config.toolbar = 'EndoAdmin';

  config.toolbar_EndoAdmin = [
    ['Preview','Source','-','Templates'],
    ['Cut','Copy','Paste','PasteText','PasteFromWord'],
    ['SelectAll','RemoveFormat'],
    ['SpellChecker', 'Scayt'],
    ['Undo','Redo','-','Find','Replace'],
    ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
    // ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
    '/',
    ['Format'],
    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
    ['Link','Unlink','Anchor'],
    // ['TextColor','BGColor'],
    ['Maximize', 'ShowBlocks'],
    ['About']
  ];
};
