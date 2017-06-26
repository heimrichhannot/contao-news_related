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
 * Frontend modules
 */
$GLOBALS['FE_MOD']['news']['news_related'] = 'ModuleNewsRelated';

/**
 * Hooks
 */
if (TL_MODE == 'FE')
{
  $GLOBALS['TL_HOOKS']['parseTemplate'][] = array('NewsRelated', 'newsreaderChange');
}
