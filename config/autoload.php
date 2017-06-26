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
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'NewsRelated',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'NewsRelated\NewsRelatedHelper' => 'system/modules/news_related/classes/NewsRelatedHelper.php',

	// Modules
	'NewsRelated\ModuleNewsRelated' => 'system/modules/news_related/modules/ModuleNewsRelated.php',
	'NewsRelated\NewsRelated'       => 'system/modules/news_related/modules/NewsRelated.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'news_related'    => 'system/modules/news_related/templates/news',
	'mod_newsrelated' => 'system/modules/news_related/templates/modules',
	'mod_newsreader'  => 'system/modules/news_related/templates/modules',
));
