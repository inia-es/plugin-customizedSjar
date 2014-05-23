<?php

/**
 * @file IndexHandler.inc.php
 *
 * Copyright (c) 2003-2011 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * With contributions from:
 * 	- 2014 Instituto Nacional de Investigacion y Tecnologia Agraria y Alimentaria
 *
 * @class IndexHandler
 * @ingroup pages_index
 *
 * @brief Handle site index requests.
 */

// $Id$


import('classes.handler.Handler');

class CustomizedSjarHandler extends Handler {
	/**
	 * Constructor
	 **/
	function CustomizedSjarHandler() {
		parent::Handler();
//	$this->addCheck(new HandlerValidatorJournal($this));
//		$this->addCheck(new HandlerValidatorRoles($this, true, null, null, array(ROLE_ID_EDITOR)));
		$plugin =& PluginRegistry::getPlugin('generic', $parentPluginName);
		$this->plugin =& $plugin;
			}
	

	/**
	 * If no journal is selected, display list of journals.
	 * Otherwise, display the index page for the selected journal.
	 * @param $args array
	 * @param $request Request
	 */
	function index($args, &$request) {
		$this->validate();
		$this->setupTemplate();
	  $plugin =& Registry::get('plugin');
		$this->plugin =& $plugin;
		$plugin =& $this->plugin;
		$router =& $request->getRouter();
		$templateManager =& templateManager::getManager();
		$journalDao =& DAORegistry::getDAO('JournalDAO');
		$journalPath = $router->getRequestedContextPath($request);
		$templateManager->assign('helpTopicId', 'user.home');
		$journal =& $router->getContext($request);
		if ($journal) {
			// Assign header and content for home page
			$templateManager->assign('displayPageHeaderTitle', $journal->getLocalizedPageHeaderTitle(true));
			$templateManager->assign('displayPageHeaderLogo', $journal->getLocalizedPageHeaderLogo(true));
			$templateManager->assign('displayPageHeaderTitleAltText', $journal->getLocalizedSetting('homeHeaderTitleImageAltText'));
			$templateManager->assign('displayPageHeaderLogoAltText', $journal->getLocalizedSetting('homeHeaderLogoImageAltText'));
			$templateManager->assign('additionalHomeContent', $journal->getLocalizedSetting('additionalHomeContent'));
			$templateManager->assign('homepageImage', $journal->getLocalizedSetting('homepageImage'));
			$templateManager->assign('homepageImageAltText', $journal->getLocalizedSetting('homepageImageAltText'));
			$templateManager->assign('journalDescription', $journal->getLocalizedSetting('description'));

			$displayCurrentIssue = $journal->getSetting('displayCurrentIssue');
			$issueDao =& DAORegistry::getDAO('IssueDAO');
			$issue =& $issueDao->getCurrentIssue($journal->getId(), true);
			if ($displayCurrentIssue && isset($issue)) {
				import('pages.issue.IssueHandler');
				// The current issue TOC/cover page should be displayed below the custom home page.
				IssueHandler::setupIssueTemplate($issue);
			}

			// Display creative commons logo/licence if enabled
			$templateManager->assign('displayCreativeCommons', $journal->getSetting('includeCreativeCommons'));

			$enableAnnouncements = $journal->getSetting('enableAnnouncements');
			if ($enableAnnouncements) {
				$enableAnnouncementsHomepage = $journal->getSetting('enableAnnouncementsHomepage');
				if ($enableAnnouncementsHomepage) {
					$numAnnouncementsHomepage = $journal->getSetting('numAnnouncementsHomepage');
					$announcementDao =& DAORegistry::getDAO('AnnouncementDAO');
					$announcements =& $announcementDao->getNumAnnouncementsNotExpiredByAssocId(ASSOC_TYPE_JOURNAL, $journal->getId(), $numAnnouncementsHomepage);
					$templateManager->assign('announcements', $announcements);
					$templateManager->assign('enableAnnouncementsHomepage', $enableAnnouncementsHomepage);
				}
			}

			$templateManager->display($plugin->getTemplatePath() .'journal.tpl');
		} else {
			$site =& Request::getSite();

			if ($site->getRedirect() && ($journal = $journalDao->getJournal($site->getRedirect())) != null) {
				$request->redirect($journal->getPath());
			}

			$templateManager->assign('intro', $site->getLocalizedIntro());
			$templateManager->assign('journalFilesPath', $request->getBaseUrl() . '/' . Config::getVar('files', 'public_files_dir') . '/journals/');
			$journals =& $journalDao->getEnabledJournals();
			$templateManager->assign_by_ref('journals', $journals);
			$templateManager->setCacheability(CACHEABILITY_PUBLIC);
			$templateManager->display('index/site.tpl');
		}
	}
}

?>
