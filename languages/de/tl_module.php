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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_module']['related_numberOfItems'] = array('Anzahl der ähnlichen Beiträge','Geben Sie hier die Anzahl der Ähnlichen Beiträge an');

$GLOBALS['TL_LANG']['tl_module']['related_priority'] = array('Priorität', 'Bitte wählen Sie aus welche Beiträge bevorzugt dargestellt werden sollen. Bei allen Einstellungen werden zunächst Beiträge berücksichtigt, die 1 oder mehr übereinstimmende Schlagwörter haben. Bei "Relevanz" werden zuerst die Beiträge mit den meisten übereinstimmenden Schlagwörtern berücksichtigt.');

$GLOBALS['TL_LANG']['tl_module']['related_match'] = array('Grundlage für Übereinstimmungen', 'Wählen Sie hier aus auf welcher Grundlage ähnliche Beiträge ermittelt werden sollen.');

$GLOBALS['TL_LANG']['tl_module']['related_priority']['random'] = 'Zufall';
$GLOBALS['TL_LANG']['tl_module']['related_priority']['relevance'] = 'Relevanz (Anzahl Schlagwörter)';
$GLOBALS['TL_LANG']['tl_module']['related_priority']['date'] = 'Aktuelle Beiträge';
$GLOBALS['TL_LANG']['tl_module']['related_priority']['comments'] = 'Anzahl der Kommentare';

$GLOBALS['TL_LANG']['tl_module']['related_match']['tags'] = 'Schlagwörter';
$GLOBALS['TL_LANG']['tl_module']['related_match']['category'] = 'Nachrichtenkategorie';
$GLOBALS['TL_LANG']['tl_module']['related_match']['archive'] = 'Nachrichtenarchiv';

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_module']['related_legend'] = 'Ähnliche Beiträge';
