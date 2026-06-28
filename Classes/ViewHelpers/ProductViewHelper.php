<?php
namespace Indiz\Products\ViewHelpers;
/*                                                                        *
 * This script is backported from the TYPO3 Flow package "TYPO3.Fluid".   *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use Indiz\Products\Domain\Model\Package;

/**
 * This class is the text color view helper for the Fluid templating engine.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class ProductViewHelper extends AbstractViewHelper{

  public function initializeArguments()
  {
    $this->registerArgument('attr', 'string', 'attr of product', true);
    $this->registerArgument('uid', 'string', 'uid of record', true);
  }
 
	/**
     * return the category items
     *
     * @return \string
     */
	public function render() {

        $uid  = (int)$this->arguments['uid'];
        $attr = $this->arguments['attr'];
        
        $product = $this->getProductByTeamMemberUid($uid);
        
        if ($product === null) {
            return '';
        }

        return $product[$attr] ?? '';

	}

    /**
     * Resolves the tt_content element whose ndz_membershortcut_record_select
     * points to the given team-member UID, then returns the product row that
     * references that content element via its fe_user field.
     */
    private function getProductByTeamMemberUid(int $uid): ?array
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

        // 1. Find the tt_content element linked to this team member
        $ttContentQb = $connectionPool->getQueryBuilderForTable('tt_content');
        $ttContentRow = $ttContentQb
            ->select('uid')
            ->from('tt_content')
            ->where(
                $ttContentQb->expr()->eq(
                    'ndz_membershortcut_record_select',
                    $ttContentQb->createNamedParameter('tx_ndz_teammember_' . $uid)
                )
            )
            ->setMaxResults(1)
            ->orderBy("uid","DESC")
            ->executeQuery()
            ->fetchAssociative();

        if (!$ttContentRow) {
            return null;
        }

        $contentUid = (int)$ttContentRow['uid'];
        
        // 2. Load the product whose fe_user field holds that content element UID
        $productTable = 'tx_products_domain_model_product';
        $productQb = $connectionPool->getQueryBuilderForTable($productTable);
        $product = $productQb
            ->select('*')
            ->from($productTable)
            ->where(
                $productQb->expr()->eq(
                    'feuser',
                    $productQb->createNamedParameter($contentUid)
                )
            )
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        return $product ?: null;
    }

}

?>
