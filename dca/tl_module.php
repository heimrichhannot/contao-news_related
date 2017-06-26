<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @package   news_related
 * @author    Fast & Media | Christian Schmidt <info@fast-end-media.de>
 * @license   LGPL
 * @copyright Fast & Media 2013-2017 <http://www.fast-end-media.de>
 */


/**
 * Add palettes to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['news_related'] = '{title_legend},name,type,headline;{config_legend:hide},news_archives,related_numberOfItems,related_match,related_priority,imgSize;{showtags_legend},news_showtags;{template_legend:hide},news_template,customTpl;{expert_legend},align,space,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['newsreader'] = str_replace(array('{template_legend'),array('{related_legend},related_numberOfItems,related_match,related_priority,thumbSize;{template_legend'),$GLOBALS['TL_DCA']['tl_module']['palettes']['newsreader']);

/**
 * Add Fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['related_numberOfItems'] = array
(
  'label'							=> &$GLOBALS['TL_LANG']['tl_module']['related_numberOfItems'],
  'inputType'					=> 'text',
	'eval'							=> array('tl_class' => 'w50'),
	'sql'								=> "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['related_priority'] = array
(
  'label'							=> &$GLOBALS['TL_LANG']['tl_module']['related_priority'],
	'exclude'						=> true,
	'inputType'					=> 'select',
	'options'						=> array('relevance', 'random', 'date', 'comments'),
	'reference'					=> &$GLOBALS['TL_LANG']['tl_module']['related_priority'],
	'eval'							=> array('includeBlankOption'=>true, 'helpwizard'=>true,  'tl_class'=>'w50'),
	'sql'								=> "varchar(32) NOT NULL default ''"
);

//Check if extension 'news_categories' is installed
if (in_array('news_categories', $this->Config->getActiveModules()))
{
	$arrOptions = array('tags', 'category', 'archive');
}
else {
	$arrOptions = array('tags', 'archive');
}

$GLOBALS['TL_DCA']['tl_module']['fields']['related_match'] = array
(
  'label'							=> &$GLOBALS['TL_LANG']['tl_module']['related_match'],
	'exclude'						=> true,
	'inputType'					=> 'checkbox',
	'options'						=> $arrOptions,
	'reference'					=> &$GLOBALS['TL_LANG']['tl_module']['related_match'],
	'eval'							=> array('mandatory'=>true, 'multiple'=>true, 'tl_class'=>'clr'),
	'sql'								=> "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['thumbSize'] = array
(
	'label'							=> &$GLOBALS['TL_LANG']['tl_module']['imgSize'],
	'exclude'					  => true,
	'inputType'					=> 'imageSize',
	'options'						=> \System::getImageSizes(),
	'reference'					=> &$GLOBALS['TL_LANG']['MSC'],
	'eval'							=> array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
  'sql'								=> "varchar(64) NOT NULL default ''"
);
