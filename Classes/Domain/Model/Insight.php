<?php

declare(strict_types=1);

namespace Indiz\Products\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

class Insight extends AbstractEntity
{
    protected string $title = '';
    protected string $alternativeTitle = '';
    protected string $teaser = '';
    protected string $bodytext = '';
    protected ?\DateTime $datetime = null;
    protected ?\DateTime $archive = null;
    protected string $author = '';
    protected string $authorEmail = '';
    protected string $type = '0';
    protected string $keywords = '';
    protected string $description = '';
    protected string $internalurl = '';
    protected string $externalurl = '';
    protected bool $istopnews = false;
    protected string $pathSegment = '';
    protected string $notes = '';
    protected int $hidden = 0;
    protected int $l10nParent = 0;
    protected int $sysLanguageUid = 0;

    /**
     * @var ObjectStorage<Insightcategory>
     */
    #[Extbase\ORM\Lazy]
    protected ObjectStorage $categories;

    /**
     * @var ObjectStorage<Insighttag>
     */
    #[Extbase\ORM\Lazy]
    protected ObjectStorage $tags;

    /**
     * @var ObjectStorage<FileReference>
     */
    #[Extbase\ORM\Lazy]
    #[Extbase\ORM\Cascade(['value' => 'remove'])]
    protected ObjectStorage $falMedia;

    /**
     * @var ObjectStorage<FileReference>
     */
    #[Extbase\ORM\Lazy]
    #[Extbase\ORM\Cascade(['value' => 'remove'])]
    protected ObjectStorage $falRelatedFiles;

    /**
     * @var ObjectStorage<Content>
     */
    protected $feuser = null;

    public function __construct()
    {
        $this->categories = new ObjectStorage();
        $this->tags = new ObjectStorage();
        $this->falMedia = new ObjectStorage();
        $this->falRelatedFiles = new ObjectStorage();
        $this->feuser = new ObjectStorage();
    }

    public function initializeObject(): void
    {
        $this->categories ??= new ObjectStorage();
        $this->tags ??= new ObjectStorage();
        $this->falMedia ??= new ObjectStorage();
        $this->falRelatedFiles ??= new ObjectStorage();
    }

    public function getL10nParent(): int { return $this->l10nParent; }
    public function setSysLanguageUid($sysLanguageUid): void { $this->sysLanguageUid = $sysLanguageUid; }
    public function getSysLanguageUid(): int { return $this->sysLanguageUid; }
    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): void { $this->title = $title; }
    public function getHidden(): int { return $this->hidden; }
    public function setHidden(int $hidden): void { $this->hidden = $hidden; }

    public function getAlternativeTitle(): string { return $this->alternativeTitle; }
    public function setAlternativeTitle(string $alternativeTitle): void { $this->alternativeTitle = $alternativeTitle; }

    public function getTeaser(): string { return $this->teaser; }
    public function setTeaser(string $teaser): void { $this->teaser = $teaser; }

    public function getBodytext(): string { return $this->bodytext; }
    public function setBodytext(string $bodytext): void { $this->bodytext = $bodytext; }

    public function getDatetime(): ?\DateTime { return $this->datetime; }
    public function setDatetime(?\DateTime $datetime): void { $this->datetime = $datetime; }

    public function getArchive(): ?\DateTime { return $this->archive; }
    public function setArchive(?\DateTime $archive): void { $this->archive = $archive; }

    public function getAuthor(): string { return $this->author; }
    public function setAuthor(string $author): void { $this->author = $author; }

    public function getAuthorEmail(): string { return $this->authorEmail; }
    public function setAuthorEmail(string $authorEmail): void { $this->authorEmail = $authorEmail; }

    public function getType(): string { return $this->type; }
    public function setType(string $type): void { $this->type = $type; }

    public function getKeywords(): string { return $this->keywords; }
    public function setKeywords(string $keywords): void { $this->keywords = $keywords; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): void { $this->description = $description; }

    public function getInternalurl(): string { return $this->internalurl; }
    public function setInternalurl(string $internalurl): void { $this->internalurl = $internalurl; }

    public function getExternalurl(): string { return $this->externalurl; }
    public function setExternalurl(string $externalurl): void { $this->externalurl = $externalurl; }

    public function isIstopnews(): bool { return $this->istopnews; }
    public function setIstopnews(bool $istopnews): void { $this->istopnews = $istopnews; }

    public function getPathSegment(): string { return $this->pathSegment; }
    public function setPathSegment(string $pathSegment): void { $this->pathSegment = $pathSegment; }

    public function getNotes(): string { return $this->notes; }
    public function setNotes(string $notes): void { $this->notes = $notes; }

    public function getCategories(): ObjectStorage { return $this->categories; }
    public function setCategories(ObjectStorage $categories): void { $this->categories = $categories; }
    public function addCategory(Category $category): void { $this->categories->attach($category); }
    public function removeCategory(Category $category): void { $this->categories->detach($category); }

    public function getTags(): ObjectStorage { return $this->tags; }
    public function setTags(ObjectStorage $tags): void { $this->tags = $tags; }
    public function addTag(Tag $tag): void { $this->tags->attach($tag); }
    public function removeTag(Tag $tag): void { $this->tags->detach($tag); }

    public function getRelatedLinks(): String { return $this->relatedLinks; }
    public function setRelatedLinks(String $relatedLinks): void { $this->relatedLinks = $relatedLinks; }

    public function getFalMediaCount(): int { return $this->falMedia->count(); }
    public function getFalMedia(): ObjectStorage { return $this->falMedia; }
    public function setFalMedia(ObjectStorage $falMedia): void { $this->falMedia = $falMedia; }
    public function addFalMedia(FileReference $fileReference): void { $this->falMedia->attach($fileReference); }
    public function removeFalMedia(FileReference $fileReference): void { $this->falMedia->detach($fileReference); }

    public function getFalRelatedFiles(): ObjectStorage { return $this->falRelatedFiles; }
    public function setFalRelatedFiles(ObjectStorage $falRelatedFiles): void { $this->falRelatedFiles = $falRelatedFiles; }
    public function addFalRelatedFile(FileReference $fileReference): void { $this->falRelatedFiles->attach($fileReference); }
    public function removeFalRelatedFile(FileReference $fileReference): void { $this->falRelatedFiles->detach($fileReference); }

    public function modifySlug($params){
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_products_domain_model_insight');
        $datetime = $queryBuilder->select("datetime")->from('tx_products_domain_model_insight')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($params["record"]["uid"])),
            )
            ->executeQuery()->fetchOne();
        return date("Y-m-d",$datetime) . "/" . $params["slug"];
    }

    public function getFeuser(): ?ObjectStorage
    {
        return $this->feuser;
    }

    public function setFeuser(?ObjectStorage $feuser): void
    {
        $this->feuser = $feuser;
    }
}
