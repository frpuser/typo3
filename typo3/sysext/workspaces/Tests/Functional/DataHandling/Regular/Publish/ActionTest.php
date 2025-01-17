<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace TYPO3\CMS\Workspaces\Tests\Functional\DataHandling\Regular\Publish;

use TYPO3\CMS\Workspaces\Tests\Functional\DataHandling\Regular\AbstractActionTestCase;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequestContext;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\ResponseContent;

/**
 * Functional test for the DataHandler
 */
class ActionTest extends AbstractActionTestCase
{
    /**
     * @var string
     */
    protected $assertionDataSetDirectory = 'typo3/sysext/workspaces/Tests/Functional/DataHandling/Regular/Publish/DataSet/';

    /**
     * @test
     */
    public function verifyCleanReferenceIndex(): void
    {
        // The test verifies the imported data set has a clean reference index by the check in tearDown()
        self::assertTrue(true);
    }

    /**
     * @test
     * See DataSet/createContentRecords.csv
     */
    public function createContents(): void
    {
        parent::createContents();
        $this->actionService->publishRecords(
            [
                self::TABLE_Content => [$this->recordIds['newContentIdFirst'], $this->recordIds['newContentIdLast']],
            ]
        );
        $this->assertAssertionDataSet('createContents');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageId));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Testing #1', 'Testing #2'));
    }

    /**
     * @test
     */
    public function createContentAndCopyContent(): void
    {
        parent::createContentAndCopyContent();
        $this->actionService->publishRecord(self::TABLE_Content, $this->recordIds['newContentId']);
        $this->actionService->publishRecord(self::TABLE_Content, $this->recordIds['copiedContentId']);
        $this->assertAssertionDataSet('createContentAndCopyContent');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageId));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Testing #1', 'Testing #1 (copy 1)'));
    }

    /**
     * @test
     * See DataSet/modifyContentRecord.csv
     */
    public function modifyContent(): void
    {
        parent::modifyContent();
        $this->actionService->publishRecord(self::TABLE_Content, self::VALUE_ContentIdSecond);
        $this->assertAssertionDataSet('modifyContent');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageId));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Testing #1'));
    }

    /**
     * @test
     */
    public function hideContent(): void
    {
        parent::hideContent();
        $this->actionService->publishRecord(self::TABLE_Content, self::VALUE_ContentIdSecond);
        $this->assertAssertionDataSet('hideContent');

        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())->withPageId(self::VALUE_PageId),
            (new InternalRequestContext())->withBackendUserId(self::VALUE_BackendUserId)
        );
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2'));
    }

    /**
     * @test
     */
    public function hideContentAndMoveToDifferentPage(): void
    {
        parent::hideContent();
        parent::moveContentToDifferentPage();
        $this->actionService->publishRecord(self::TABLE_Content, self::VALUE_ContentIdSecond);
        $this->assertAssertionDataSet('hideContentAndMoveToDifferentPage');

        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())->withPageId(self::VALUE_PageId),
            (new InternalRequestContext())->withBackendUserId(self::VALUE_BackendUserId)
        );
        $responseSectionsSource = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSectionsSource, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1'));
        self::assertThat($responseSectionsSource, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2'));
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())->withPageId(self::VALUE_PageIdTarget),
            (new InternalRequestContext())->withBackendUserId(self::VALUE_BackendUserId)
        );
        $responseSectionsTarget = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSectionsTarget, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2'));
    }

    /**
     * @test
     */
    public function deleteContent(): void
    {
        parent::deleteContent();
        $this->actionService->publishRecord(self::TABLE_Content, self::VALUE_ContentIdSecond);
        $this->assertAssertionDataSet('deleteContent');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageId));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1'));
        self::assertThat($responseSections, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2'));
    }

    /**
     * @test
     */
    public function deleteLocalizedContentAndDeleteContent(): void
    {
        // this test will not rely on a translated page, because it only tests the act of publishing.
        // The actual content of frontend response does not matter much, and it would increase the scope
        // of the test, when a translated page is also published here.
        parent::deleteLocalizedContentAndDeleteContent();
        $this->actionService->publishRecords(
            [
                self::TABLE_Content => [self::VALUE_ContentIdThird, self::VALUE_ContentIdThirdLocalized],
            ]
        );
        $this->assertAssertionDataSet('deleteLocalizedContentNDeleteContent');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageId)->withLanguageId(self::VALUE_LanguageId));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #3', '[Translate to Dansk:] Regular Element #3'));
    }

    /**
     * @test
     */
    public function copyContent(): void
    {
        parent::copyContent();
        $this->actionService->publishRecord(self::TABLE_Content, $this->recordIds['copiedContentId']);
        $this->assertAssertionDataSet('copyContent');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageId));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2 (copy 1)'));
    }

    /**
     * @test
     */
    public function copyContentToLanguage(): void
    {
        // Create and publish translated page first
        $translatedPageResult = $this->actionService->copyRecordToLanguage(self::TABLE_Page, self::VALUE_PageId, self::VALUE_LanguageId);
        $this->actionService->publishRecord(self::TABLE_Page, $translatedPageResult[self::TABLE_Page][self::VALUE_PageId]);
        parent::copyContentToLanguage();
        $this->actionService->publishRecord(self::TABLE_Content, $this->recordIds['localizedContentId']);
        $this->assertAssertionDataSet('copyContentToLanguage');

        // Set up "dk" to not have overlays
        $languageConfiguration = $this->siteLanguageConfiguration;
        $languageConfiguration[self::VALUE_LanguageId]['fallbackType'] = 'free';
        $this->setUpFrontendSite(1, $languageConfiguration);
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageId)->withLanguageId(self::VALUE_LanguageId));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('[Translate to Dansk:] Regular Element #3', '[Translate to Dansk:] Regular Element #2'));
    }

    /**
     * @test
     */
    public function copyContentToLanguageFromNonDefaultLanguage(): void
    {
        // Create and publish translated page first
        $translatedPageResult = $this->actionService->copyRecordToLanguage(self::TABLE_Page, self::VALUE_PageId, self::VALUE_LanguageIdSecond);
        $this->actionService->publishRecord(self::TABLE_Page, $translatedPageResult[self::TABLE_Page][self::VALUE_PageId]);
        parent::copyContentToLanguageFromNonDefaultLanguage();
        $this->actionService->publishRecord(self::TABLE_Content, $this->recordIds['localizedContentId']);
        $this->assertAssertionDataSet('copyContentToLanguageFromNonDefaultLanguage');

        // Set up "dk" to not have overlays
        $languageConfiguration = $this->siteLanguageConfiguration;
        $languageConfiguration[self::VALUE_LanguageIdSecond]['fallbackType'] = 'free';
        $this->setUpFrontendSite(1, $languageConfiguration);
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageId)->withLanguageId(self::VALUE_LanguageIdSecond));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('[Translate to Deutsch:] [Translate to Dansk:] Regular Element #3'));
    }

    /**
     * @test
     */
    public function localizeContent(): void
    {
        // Create and publish translated page first
        $translatedPageResult = $this->actionService->copyRecordToLanguage(self::TABLE_Page, self::VALUE_PageId, self::VALUE_LanguageId);
        $this->actionService->publishRecord(self::TABLE_Page, $translatedPageResult[self::TABLE_Page][self::VALUE_PageId]);
        parent::localizeContent();
        $this->actionService->publishRecord(self::TABLE_Content, $this->recordIds['localizedContentId']);
        $this->assertAssertionDataSet('localizeContent');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageId)->withLanguageId(self::VALUE_LanguageId));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('[Translate to Dansk:] Regular Element #1', '[Translate to Dansk:] Regular Element #2'));
    }

    /**
     * @test
     */
    public function localizeContentAfterMovedContent(): void
    {
        parent::localizeContentAfterMovedContent();
        $this->actionService->publishRecord(self::TABLE_Content, $this->recordIds['localizedContentId']);
        $this->assertAssertionDataSet('localizeContentAfterMovedContent');
    }

    /**
     * @test
     */
    public function localizeContentAfterMovedInLiveContent(): void
    {
        parent::localizeContentAfterMovedInLiveContent();
        $this->actionService->publishRecord(self::TABLE_Content, $this->recordIds['localizedContentId']);
        $this->assertAssertionDataSet('localizeContentAfterMovedInLiveContent');
    }

    /**
     * @test
     */
    public function localizeContentFromNonDefaultLanguage(): void
    {
        // Create and publish translated page first
        $translatedPageResult = $this->actionService->copyRecordToLanguage(self::TABLE_Page, self::VALUE_PageId, self::VALUE_LanguageIdSecond);
        $this->actionService->publishRecord(self::TABLE_Page, $translatedPageResult[self::TABLE_Page][self::VALUE_PageId]);
        parent::localizeContentFromNonDefaultLanguage();
        $this->actionService->publishRecord(self::TABLE_Content, $this->recordIds['localizedContentId']);
        $this->assertAssertionDataSet('localizeContentFromNonDefaultLanguage');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageId)->withLanguageId(self::VALUE_LanguageIdSecond));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('[Translate to Deutsch:] [Translate to Dansk:] Regular Element #1', '[Translate to Deutsch:] [Translate to Dansk:] Regular Element #3'));
    }

    /**
     * @test
     */
    public function changeContentSorting(): void
    {
        parent::changeContentSorting();
        $this->actionService->publishRecord(self::TABLE_Content, self::VALUE_ContentIdFirst);
        $this->assertAssertionDataSet('changeContentSorting');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageId));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1', 'Regular Element #2'));
    }

    /**
     * @test
     */
    public function changeContentSortingAfterSelf(): void
    {
        parent::changeContentSortingAfterSelf();
        $this->actionService->publishRecord(self::TABLE_Content, self::VALUE_ContentIdFirst);
        $this->assertAssertionDataSet('changeContentSortingAfterSelf');

        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())->withPageId(self::VALUE_PageId),
            (new InternalRequestContext())->withBackendUserId(self::VALUE_BackendUserId)->withWorkspaceId(self::VALUE_WorkspaceId)
        );
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1', 'Regular Element #2'));
    }

    /**
     * @test
     */
    public function moveContentToDifferentPage(): void
    {
        parent::moveContentToDifferentPage();
        $this->actionService->publishRecord(self::TABLE_Content, self::VALUE_ContentIdSecond);
        $this->assertAssertionDataSet('moveContentToDifferentPage');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageId));
        $responseSectionsSource = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSectionsSource, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1'));
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageIdTarget));
        $responseSectionsTarget = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSectionsTarget, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2'));
    }

    /**
     * @test
     */
    public function moveContentToDifferentPageAndChangeSorting(): void
    {
        parent::moveContentToDifferentPageAndChangeSorting();
        $this->actionService->publishRecords([
            self::TABLE_Content => [self::VALUE_ContentIdFirst, self::VALUE_ContentIdSecond],
        ]);
        $this->assertAssertionDataSet('moveContentToDifferentPageNChangeSorting');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageIdTarget));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1', 'Regular Element #2'));
    }

    /**
     * @test
     */
    public function moveContentToDifferentPageAndHide(): void
    {
        parent::moveContentToDifferentPageAndHide();
        $this->actionService->publishRecord(self::TABLE_Content, self::VALUE_ContentIdSecond);
        $this->assertAssertionDataSet('moveContentToDifferentPageAndHide');

        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())->withPageId(self::VALUE_PageIdTarget),
            (new InternalRequestContext())->withBackendUserId(self::VALUE_BackendUserId)->withWorkspaceId(self::VALUE_WorkspaceId)
        );
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionDoesNotHaveRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #2'));
    }

    /**
     * Page records
     */

    /**
     * @test
     */
    public function createPage(): void
    {
        parent::createPage();
        $this->actionService->publishRecord(self::TABLE_Page, $this->recordIds['newPageId']);
        $this->assertAssertionDataSet('createPage');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId($this->recordIds['newPageId']));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Testing #1'));
    }

    /**
     * @test
     */
    public function createPageAndSubPageAndSubPageContent(): void
    {
        parent::createPageAndSubPageAndSubPageContent();
        $this->actionService->publishRecord(self::TABLE_Page, $this->recordIds['newPageId']);
        $this->assertAssertionDataSet('createPageAndSubPageAndSubPageContent');

        // Sub page is not published together with parent page
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())->withPageId($this->recordIds['newSubPageId']),
            (new InternalRequestContext())->withBackendUserId(self::VALUE_BackendUserId)->withWorkspaceId(self::VALUE_WorkspaceId)
        );
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Testing #1 #1'));
    }

    /**
     * @test
     */
    public function modifyPage(): void
    {
        parent::modifyPage();
        $this->actionService->publishRecord(self::TABLE_Page, self::VALUE_PageId);
        $this->assertAssertionDataSet('modifyPage');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageId));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Testing #1'));
    }

    /**
     * @test
     */
    public function deletePage(): void
    {
        parent::deletePage();
        $this->actionService->publishRecord(self::TABLE_Page, self::VALUE_PageId);
        $this->assertAssertionDataSet('deletePage');

        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())->withPageId(self::VALUE_PageId)
        );
        self::assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function deleteContentAndPage(): void
    {
        parent::deleteContentAndPage();
        $this->actionService->publishRecord(self::TABLE_Page, self::VALUE_PageId);
        $this->assertAssertionDataSet('deleteContentAndPage');

        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())->withPageId(self::VALUE_PageId)
        );
        self::assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function localizeNestedPagesAndContents(): void
    {
        parent::localizeNestedPagesAndContents();
        // Will publish only the page translation, not it's content elements
        $this->actionService->publishRecord(self::TABLE_Page, $this->recordIds['localizedParentPageId']);
        $this->assertAssertionDataSet('localizeNestedPagesAndContents');
    }

    /**
     * @test
     */
    public function copyPage(): void
    {
        parent::copyPage();
        $this->actionService->publishRecords(
            [
                self::TABLE_Page => [$this->recordIds['newPageId']],
                self::TABLE_Content => [$this->recordIds['newContentIdFirst'], $this->recordIds['newContentIdLast']],
            ]
        );
        $this->assertAssertionDataSet('copyPage');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId($this->recordIds['newPageId']));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Relations'));
    }

    /**
     * @test
     */
    public function copyPageFreeMode(): void
    {
        parent::copyPageFreeMode();
        $this->actionService->publishRecords(
            [
                self::TABLE_Page => [$this->recordIds['newPageId']],
                self::TABLE_Content => [$this->recordIds['newContentIdTenth'], $this->recordIds['newContentIdTenthLocalized'],  $this->recordIds['newContentIdTenthLocalized2']],
            ]
        );
        $this->assertAssertionDataSet('copyPageFreeMode');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId($this->recordIds['newPageId']));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Target'));
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #10'));
    }

    /**
     * @test
     */
    public function localizePage(): void
    {
        parent::localizePage();
        $this->actionService->publishRecord(self::TABLE_Page, $this->recordIds['localizedPageId']);
        $this->assertAssertionDataSet('localizePage');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageId)->withLanguageId(self::VALUE_LanguageId));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('[Translate to Dansk:] Relations'));
    }

    /**
     * @test
     */
    public function localizePageHiddenHideAtCopyFalse(): void
    {
        parent::localizePageHiddenHideAtCopyFalse();
        $this->actionService->publishRecord(self::TABLE_Page, $this->recordIds['localizedPageId']);
        $this->assertAssertionDataSet('localizePageHiddenHideAtCopyFalse');
    }

    /**
     * @test
     */
    public function localizePageNotHiddenHideAtCopyFalse(): void
    {
        parent::localizePageNotHiddenHideAtCopyFalse();
        $this->actionService->publishRecord(self::TABLE_Page, $this->recordIds['localizedPageId']);
        $this->assertAssertionDataSet('localizePageNotHiddenHideAtCopyFalse');
    }

    /**
     * @test
     */
    public function localizePageNotHiddenHideAtCopyDisableHideAtCopyUnset(): void
    {
        parent::localizePageNotHiddenHideAtCopyDisableHideAtCopyUnset();
        $this->actionService->publishRecord(self::TABLE_Page, $this->recordIds['localizedPageId']);
        $this->assertAssertionDataSet('localizePageNotHiddenHideAtCopyDisableHideAtCopyUnset');
    }

    /**
     * @test
     */
    public function localizePageHiddenHideAtCopyDisableHideAtCopyUnset(): void
    {
        parent::localizePageHiddenHideAtCopyDisableHideAtCopyUnset();
        $this->actionService->publishRecord(self::TABLE_Page, $this->recordIds['localizedPageId']);
        $this->assertAssertionDataSet('localizePageHiddenHideAtCopyDisableHideAtCopyUnset');
    }

    /**
     * @test
     */
    public function localizePageNotHiddenHideAtCopyDisableHideAtCopySetToFalse(): void
    {
        parent::localizePageNotHiddenHideAtCopyDisableHideAtCopySetToFalse();
        $this->actionService->publishRecord(self::TABLE_Page, $this->recordIds['localizedPageId']);
        $this->assertAssertionDataSet('localizePageNotHiddenHideAtCopyDisableHideAtCopySetToFalse');
    }

    /**
     * @test
     */
    public function localizePageHiddenHideAtCopyDisableHideAtCopySetToFalse(): void
    {
        parent::localizePageHiddenHideAtCopyDisableHideAtCopySetToFalse();
        $this->actionService->publishRecord(self::TABLE_Page, $this->recordIds['localizedPageId']);
        $this->assertAssertionDataSet('localizePageHiddenHideAtCopyDisableHideAtCopySetToFalse');
    }

    /**
     * @test
     */
    public function localizePageNotHiddenHideAtCopyDisableHideAtCopySetToTrue(): void
    {
        parent::localizePageNotHiddenHideAtCopyDisableHideAtCopySetToTrue();
        $this->actionService->publishRecord(self::TABLE_Page, $this->recordIds['localizedPageId']);
        $this->assertAssertionDataSet('localizePageNotHiddenHideAtCopyDisableHideAtCopySetToTrue');
    }

    /**
     * @test
     */
    public function localizePageHiddenHideAtCopyDisableHideAtCopySetToTrue(): void
    {
        parent::localizePageHiddenHideAtCopyDisableHideAtCopySetToTrue();
        $this->actionService->publishRecord(self::TABLE_Page, $this->recordIds['localizedPageId']);
        $this->assertAssertionDataSet('localizePageHiddenHideAtCopyDisableHideAtCopySetToTrue');
    }

    /**
     * @test
     */
    public function createPageAndChangePageSorting(): void
    {
        parent::createPageAndChangePageSorting();
        $this->actionService->publishRecord(self::TABLE_Page, $this->recordIds['newPageId']);
        $this->assertAssertionDataSet('createPageAndChangePageSorting');
    }

    /**
     * @test
     */
    public function createPageAndMoveCreatedPage(): void
    {
        parent::createPageAndMoveCreatedPage();
        $this->actionService->publishRecord(self::TABLE_Page, $this->recordIds['newPageId']);
        $this->assertAssertionDataSet('createPageAndMoveCreatedPage');
    }

    /**
     * @test
     */
    public function changePageSorting(): void
    {
        parent::changePageSorting();
        $this->actionService->publishRecord(self::TABLE_Page, self::VALUE_PageId);
        $this->assertAssertionDataSet('changePageSorting');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageId));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Relations'));
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1', 'Regular Element #2'));
    }

    /**
     * @test
     */
    public function changePageSortingAfterSelf(): void
    {
        parent::changePageSortingAfterSelf();
        $this->actionService->publishRecord(self::TABLE_Page, self::VALUE_PageId);
        $this->assertAssertionDataSet('changePageSortingAfterSelf');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageId));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Relations'));
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1', 'Regular Element #2'));
    }

    /**
     * @test
     */
    public function movePageToDifferentPage(): void
    {
        parent::movePageToDifferentPage();
        $this->actionService->publishRecord(self::TABLE_Page, self::VALUE_PageId);
        $this->assertAssertionDataSet('movePageToDifferentPage');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageId));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Relations'));
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1', 'Regular Element #2'));
    }

    /**
     * @test
     */
    public function movePageToDifferentPageTwice(): void
    {
        parent::movePageToDifferentPageTwice();
        $this->actionService->publishRecord(self::TABLE_Page, self::VALUE_PageId);
        $this->assertAssertionDataSet('movePageToDifferentPageTwice');
    }

    /**
     * @test
     */
    public function movePageLocalizedToDifferentPageTwice(): void
    {
        parent::movePageLocalizedToDifferentPageTwice();
        $this->actionService->publishRecord(self::TABLE_Page, self::VALUE_PageId);
        $this->assertAssertionDataSet('movePageLocalizedToDifferentPageTwice');
    }

    /**
     * @test
     */
    public function movePageLocalizedInLiveToDifferentPageTwice(): void
    {
        parent::movePageLocalizedInLiveToDifferentPageTwice();
        $this->actionService->publishRecord(self::TABLE_Page, self::VALUE_PageId);
        $this->assertAssertionDataSet('movePageLocalizedInLiveToDifferentPageTwice');
    }

    /**
     * @test
     */
    public function movePageLocalizedInLiveWorkspaceChangedToDifferentPageTwice(): void
    {
        parent::movePageLocalizedInLiveWorkspaceChangedToDifferentPageTwice();
        $this->actionService->publishRecord(self::TABLE_Page, self::VALUE_PageId);
        $this->assertAssertionDataSet('movePageLocalizedInLiveWorkspaceChangedToDifferentPageTwice');
    }

    /**
     * @test
     */
    public function movePageLocalizedInLiveWorkspaceDeletedToDifferentPageTwice(): void
    {
        parent::movePageLocalizedInLiveWorkspaceDeletedToDifferentPageTwice();
        $this->actionService->publishRecord(self::TABLE_Page, self::VALUE_PageId);
        $this->assertAssertionDataSet('movePageLocalizedInLiveWorkspaceDeletedToDifferentPageTwice');
    }

    /**
     * @test
     */
    public function movePageToDifferentPageAndChangeSorting(): void
    {
        parent::movePageToDifferentPageAndChangeSorting();
        $this->actionService->publishRecords([
            self::TABLE_Page => [self::VALUE_PageId, self::VALUE_PageIdTarget],
        ]);
        $this->assertAssertionDataSet('movePageToDifferentPageNChangeSorting');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageId));
        $responseSectionsPage = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSectionsPage, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Relations'));
        self::assertThat($responseSectionsPage, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('Regular Element #1', 'Regular Element #2'));
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageIdWebsite));
        $responseSectionsWebsite = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSectionsWebsite, $this->getRequestSectionStructureHasRecordConstraint()
            ->setRecordIdentifier(self::TABLE_Page . ':' . self::VALUE_PageIdWebsite)->setRecordField('__pages')
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Target', 'Relations', 'DataHandlerTest'));
    }

    /**
     * @test
     * @see https://forge.typo3.org/issues/33104
     * @see https://forge.typo3.org/issues/55573
     */
    public function movePageToDifferentPageAndCreatePageAfterMovedPage(): void
    {
        parent::movePageToDifferentPageAndCreatePageAfterMovedPage();
        $this->actionService->publishRecords([
            self::TABLE_Page => [self::VALUE_PageIdTarget, $this->recordIds['newPageId']],
        ]);
        $this->assertAssertionDataSet('movePageToDifferentPageNCreatePageAfterMovedPage');

        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(self::VALUE_PageIdWebsite));
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionStructureHasRecordConstraint()
            ->setRecordIdentifier(self::TABLE_Page . ':' . self::VALUE_PageIdWebsite)->setRecordField('__pages')
            ->setTable(self::TABLE_Page)->setField('title')->setValues('Target', 'Testing #1', 'DataHandlerTest'));
    }

    /**
     * @test
     */
    public function changeContentSortingAndCopyDraftPage(): void
    {
        parent::changeContentSortingAndCopyDraftPage();
        $this->actionService->publishRecord(self::TABLE_Content, self::VALUE_ContentIdFirst);
        $this->assertAssertionDataSet('changeContentSortingAndCopyDraftPage');
    }

    /**
     * @test
     */
    public function createContentAndCopyDraftPage(): void
    {
        parent::createContentAndCopyDraftPage();
        $this->actionService->publishRecords([
            self::TABLE_Content => [$this->recordIds['newContentId']],
            self::TABLE_Page => [$this->recordIds['copiedPageId']],
        ]);
        $this->assertAssertionDataSet('createContentAndCopyDraftPage');

        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())->withPageId($this->recordIds['copiedPageId']),
            (new InternalRequestContext())->withBackendUserId(self::VALUE_BackendUserId)->withWorkspaceId(self::VALUE_WorkspaceId)
        );
        $responseSectionsDraft = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSectionsDraft, $this->getRequestSectionHasRecordConstraint()
            ->setTable(static::TABLE_Content)->setField('header')->setValues('Testing #1'));
    }

    /**
     * @test
     */
    public function createContentAndLocalize(): void
    {
        parent::createContentAndLocalize();
        $this->actionService->publishRecords([
            self::TABLE_Content => [$this->recordIds['newContentId']],
        ]);
        $this->assertAssertionDataSet('createContentAndLocalize');

        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())->withPageId(self::VALUE_PageId)->withLanguageId(self::VALUE_LanguageId),
            (new InternalRequestContext())->withBackendUserId(self::VALUE_BackendUserId)->withWorkspaceId(self::VALUE_WorkspaceId)
        );
        $responseSections = ResponseContent::fromString((string)$response->getBody())->getSections();
        self::assertThat($responseSections, $this->getRequestSectionHasRecordConstraint()
            ->setTable(self::TABLE_Content)->setField('header')->setValues('[Translate to Dansk:] Testing #1'));
    }

    /**
     * @test
     */
    public function createPageAndCopyDraftParentPage(): void
    {
        parent::createPageAndCopyDraftParentPage();
        $this->actionService->publishRecords([
            self::TABLE_Page => [$this->recordIds['newPageId'], $this->recordIds['copiedPageId']],
        ]);
        $this->assertAssertionDataSet('createPageAndCopyDraftParentPage');
    }

    /**
     * @test
     */
    public function createPlaceholdersAndDeleteDraftParentPage(): void
    {
        parent::createPlaceholdersAndDeleteDraftParentPage();
        $this->actionService->publishRecord(self::TABLE_Page, self::VALUE_ParentPageId);
        $this->assertAssertionDataSet('createPlaceholdersAndDeleteDraftParentPage');
    }
}
