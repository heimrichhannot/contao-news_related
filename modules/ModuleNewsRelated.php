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
 * Run in a custom namespace, so the class can be replaced
 */
namespace NewsRelated;

class ModuleNewsRelated extends \ModuleNews
{

	/**
	 * Load different classes for further string modifications
	 */
	public function strClass()
	{
		$strStringClass = version_compare(VERSION . '.' . BUILD, '3.5.1', '<') ? '\String' : '\StringUtil';

		return $strStringClass;
	}


	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_newsrelated';

	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['news_related'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

    $this->news_archives = deserialize($this->news_archives);

		// Return if there are no archives
		if (!is_array($this->news_archives) || empty($this->news_archives))
		{
			return '';
		}

		return parent::generate();
	}


	/**
	 * Generate module
	 */
	protected function compile()
	{
    //limit
		if($this->related_numberOfItems >= 0 && $this->related_numberOfItems <= 50)
		{
			$limit = $this->related_numberOfItems;
		}
		else {
			$limit = 4;
		}

    if($limit > 0)
		{

			$this->import('NewsRelatedHelper', 'Helper');
			$objArticles = $this->Helper->getRelated($this->news_archives, $this->related_match, $this->related_priority, $limit);

			if($objArticles) {

				$this->Template->articles = $this->parseArticles($objArticles);

				if(count($this->Template->articles) == 0) {
		    	return '';
				}

				// Read news archive
		    $objArchive = $this->Database->prepare("SELECT tstamp, title, jumpTo FROM tl_news_archive WHERE id=?")->execute($objArticles->pid);
		    $this->Template->archive = $objArchive;

		    // Assign articles
				$this->Template->info = $GLOBALS['TL_LANG']['MSC']['related_info'];
		    $this->Template->related_headline = $GLOBALS['TL_LANG']['MSC']['related_headline'];
			}
		}
	}


	/**
	 * Parse an item and return it as string
	 * @param object
	 * @param boolean
	 * @param string
	 * @param integer
	 * @return string
	 */
	protected function parseArticle($objArticle, $blnAddArchive=false, $strClass='', $intCount=0)
	{
    $strStringClass = $this->strClass();

		global $objPage;

		$objTemplate = new \FrontendTemplate($this->news_template);
		$objTemplate->setData($objArticle->row());

		$objTemplate->class = (($objArticle->cssClass != '') ? ' ' . $objArticle->cssClass : '') . $strClass;
		$objTemplate->newsHeadline = $objArticle->headline;
		$objTemplate->subHeadline = $objArticle->subheadline;
		$objTemplate->hasSubHeadline = $objArticle->subheadline ? true : false;

		// Read news archive
    $objArchive = $this->Database->prepare("SELECT tstamp, title, jumpTo FROM tl_news_archive WHERE id=?")->execute($objArticle->pid);
    $objTemplate->archive = $objArchive;

		if (($objTarget = \PageModel::findByPk($objArchive->jumpTo)) !== null)
		{
    	$url = ampersand($this->generateFrontendUrl($objTarget->row(), ((isset($GLOBALS['TL_CONFIG']['useAutoItem']) && $GLOBALS['TL_CONFIG']['useAutoItem']) ?  '/' : '/items/') . ((!$GLOBALS['TL_CONFIG']['disableAlias'] && $objArticle->alias != '') ? $objArticle->alias : $objArticle->id)));
		}
    $title = specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $objArticle->headline), true);

    $objTemplate->linkHeadline = '<a href="'.$url.'" title="'.$title.'">'.$objArticle->headline.'</a>';
		$objTemplate->link = $url;

		$more = sprintf('<a href="%s" title="%s">%s%s</a>',
			$url,
			$title,
			$GLOBALS['TL_LANG']['MSC']['more'],
			($blnIsReadMore ? ' <span class="invisible">'.$objArticle->headlines.'</span>' : '')
		);

		$objTemplate->more = $more;

		$objTemplate->count = $intCount;
		$objTemplate->text = '';

		// Clean the RTE output
		if ($objArticle->teaser != '')
		{
			if ($objPage->outputFormat == 'xhtml')
			{
				$objTemplate->teaser = $strStringClass::toXhtml($objArticle->teaser);
			}
			else
			{
				$objTemplate->teaser = $strStringClass::toHtml5($objArticle->teaser);
			}

			$objTemplate->teaser = $strStringClass::encodeEmail($objTemplate->teaser);

			// Shorten the teaser
			$objTemplate->teaser = strip_tags($objTemplate->teaser,array('<strong>','<a>'));
			if(strlen($objTemplate->teaser) > 120)
			{
			  $objTemplate->teaser = $strStringClass::substrHtml($objTemplate->teaser, 120).'...';
			}
		}

		// Display the "read more" button for external/article links
		if ($objArticle->source != 'default')
		{
			$objTemplate->text = true;
		}

		// Compile the news text
		else
		{
			$objElement = \ContentModel::findPublishedByPidAndTable($objArticle->id, 'tl_news');

			if ($objElement !== null)
			{
				while ($objElement->next())
				{
					$objTemplate->text .= $this->getContentElement($objElement->id);
				}
			}
		}

		$arrMeta = $this->getMetaFields($objArticle);

		// Add the meta information
		$objTemplate->date = $arrMeta['date'];
		$objTemplate->hasMetaFields = !empty($arrMeta);
		$objTemplate->numberOfComments = $arrMeta['ccount'];
		$objTemplate->commentCount = $arrMeta['comments'];
		$objTemplate->timestamp = $objArticle->date;
		$objTemplate->author = $arrMeta['author'];
		$objTemplate->datetime = date('Y-m-d\TH:i:sP', $objArticle->date);

		$objTemplate->addImage = false;

		// Add an image
		if ($objArticle->addImage && $objArticle->singleSRC != '')
		{
			$objModel = \FilesModel::findByUuid($objArticle->singleSRC);

			if ($objModel === null)
			{
				if (!\Validator::isUuid($objArticle->singleSRC))
				{
					$objTemplate->text = '<p class="error">'.$GLOBALS['TL_LANG']['ERR']['version2format'].'</p>';
				}
			}
			elseif (is_file(TL_ROOT . '/' . $objModel->path))
			{
				// Do not override the field now that we have a model registry (see #6303)
				$arrArticle = $objArticle->row();

				// Override the default image size
				if ($this->imgSize != '')
				{
					$size = deserialize($this->imgSize);

					if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]))
					{
						$arrArticle['size'] = $this->imgSize;
     			}
				}

				$arrArticle['singleSRC'] = $objModel->path;
				$this->addImageToTemplate($objTemplate, $arrArticle);
			}
		}

		if($this->news_showtags)
		{
			$this->Session->set('news_showtags', $this->news_showtags);
			$this->Session->set('news_jumpto', $this->tag_jumpTo);
			$this->Session->set('news_tag_named_class', $this->tag_named_class);
		}

		$objTemplate->enclosure = array();
		// Add enclosures
		if ($objArticle->addEnclosure)
		{
			$this->addEnclosuresToTemplate($objTemplate, $objArticle->row());
		}

		// HOOK: add custom logic
		if (isset($GLOBALS['TL_HOOKS']['parseArticles']) && is_array($GLOBALS['TL_HOOKS']['parseArticles']))
		{
			foreach ($GLOBALS['TL_HOOKS']['parseArticles'] as $callback)
			{
				$this->import($callback[0]);
				$this->{$callback[0]}->{$callback[1]}($objTemplate, $objArticle->row(), $this);
			}
		}

		return $objTemplate->parse();
	}


	/**
	 * Parse one or more items and return them as array
	 * @param object
	 * @param boolean
	 * @return array
	 */
	protected function parseArticles($objArticles, $blnAddArchive=false)
	{
		$limit = $objArticles->count();

		if ($limit < 1)
		{
			return array();
		}

		$count = 0;
		$arrArticles = array();

		while ($objArticles->next())
		{
			$arrArticles[] = $this->parseArticle($objArticles, $blnAddArchive, ((++$count == 1) ? ' first' : '') . (($count == $limit) ? ' last' : '') . ((($count % 2) == 0) ? ' odd' : ' even'), $count);
		}

		return $arrArticles;
	}


	/**
	 * Return the meta fields of a news article as array
	 * @param object
	 * @return array
	 */
	protected function getMetaFields($objArticle)
	{

		global $objPage;
		$return = array();

		$return['date'] = \Date::parse($objPage->datimFormat, $objArticle->date);

		if (!$objArticle->noComments && in_array('comments', \ModuleLoader::getActive()) && $objArticle->source == 'default')
		{
			$intTotal = \CommentsModel::countPublishedBySourceAndParent('tl_news', $objArticle->id);
			$return['ccount'] = $intTotal;
			$return['comments'] = sprintf($GLOBALS['TL_LANG']['MSC']['commentCount'], $intTotal);
		}

		return $return;
	}

}
