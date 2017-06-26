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

class NewsRelated extends \Frontend
{
	public function newsreaderChange($objTemplate)
	{
		// Choose template
		if ($objTemplate->getName() == 'mod_newsreader') {

			// Limit
			if($objTemplate->related_numberOfItems >= 0 && $objTemplate->related_numberOfItems <= 50)
			{
				$limit = $objTemplate->related_numberOfItems;
			}
			else
			{
				$limit = 4;
			}

			if($limit >= 0) {
			}
			else {
        return '';
			}

			$this->import('NewsRelatedHelper', 'Helper');
			$objArticle = $this->Helper->getRelated($objTemplate->news_archives, $objTemplate->related_match, $objTemplate->related_priority, $limit);

			if($objArticle)
			{
	      // Get page
	      global $objPage;

	      // Defaults
	      $arrArticles = array();
	      while($objArticle->next())
				{
					$arrPic = '';

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
							if ($objTemplate->thumbSize != '')
							{
								$size = deserialize($objTemplate->thumbSize);

								if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2]))
								{
									$arrArticle['size'] = $objTemplate->thumbSize;
			     			}
							}

							$arrArticle['singleSRC'] = $objModel->path;
							$this->addImageToTemplate($objTemplate, $arrArticle);

							$arrPic = array(
					      'picture' => $objTemplate->picture,
					      'alt' => $objArticle->alt,
					      'fullsize' => $objArticle->fullsize,
					      'caption' => $objArticle->caption
							);
						}
					}

					// Shorten the teaser
					$this->import('String');
					$teaser = strip_tags($objArticle->teaser,array('<strong>','<a>'));
					if(strlen($teaser) > 120)
					{
					  $teaser = $this->String->substrHtml($teaser, 120) . '...';
					}

			    $objArchive = $this->Database->prepare("SELECT tstamp, title, jumpTo FROM tl_news_archive WHERE id=?")->execute($objArticle->pid);

					if (($objTarget = \PageModel::findByPk($objArchive->jumpTo)) !== null)
					{
			    	$url = ampersand($this->generateFrontendUrl($objTarget->row(), ((isset($GLOBALS['TL_CONFIG']['useAutoItem']) && $GLOBALS['TL_CONFIG']['useAutoItem']) ?  '/' : '/items/') . ((!$GLOBALS['TL_CONFIG']['disableAlias'] && $objArticle->alias != '') ? $objArticle->alias : $objArticle->id)));
					}

					$title = specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $objArticle->headline), true);

					$arrMeta = $this->getMetaFields($objArticle);

					$more = sprintf
					(
						'<a href="%s" title="%s">%s%s</a>',
						$url,
						$title,
						$GLOBALS['TL_LANG']['MSC']['more'],
						($blnIsReadMore ? ' <span class="invisible">'.$objArticle->headlines.'</span>' : '')
					);

	        //Newsdaten hinzufügen
	        $arrArticles[] = array
	        (
		        'headline' => $objArticle->headline,
		        'id' => $objArticle->id,
		        'subheadline' => $objArticle->subheadline,
		        'teaser' => $teaser,
						'more' => $more,
		        'image' => $arrPic,
		        'url' => $url,
	          'title' => $title,
						'numberOfComments' => $arrMeta['ccount'],
						'commentCount' => $arrMeta['comments'],
	          'date' => $arrMeta['date'],
	          'timestamp' => $objArticle->date,
	          'datetime' => date('Y-m-d\TH:i:sP', $objArticle->date)
	        );
				}

				// assign articles
				$objTemplate->info = $GLOBALS['TL_LANG']['MSC']['related_info'];
	      $objTemplate->related_headline = $GLOBALS['TL_LANG']['MSC']['related_headline'];
				if(!empty($arrArticles) && is_array($arrArticles))
				{
		    	$objTemplate->newsRelated = $arrArticles;
				}
				else { return ''; }
			}
		}
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

		$intTotal = \CommentsModel::countPublishedBySourceAndParent('tl_news', $objArticle->id);
		$return['ccount'] = $intTotal;
		$return['comments'] = sprintf($GLOBALS['TL_LANG']['MSC']['commentCount'], $intTotal);

		return $return;
	}
}
