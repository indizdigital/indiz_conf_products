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
class PackageelementViewHelper extends AbstractViewHelper{

    private $name = "";
  public function initializeArguments()
  {
    $this->registerArgument('slug', 'string', 'slug of productelement', false);
    $this->registerArgument('productUid', 'string', 'productUid of order', false);
    $this->registerArgument('packageUid', 'string', 'packageUid of order', false);
  }
 
	/**
     * return the category items
     *
     * @return \string
     */
	public function render() { 

        $slug = $this->arguments["slug"];
        
        $productUid = $this->arguments["productUid"];
        $packageUid = $this->arguments["packageUid"];
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

        $table = "tx_products_domain_model_package";
        $queryBuilder = $connectionPool->getQueryBuilderForTable($table);

        $queryBuilder = $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($packageUid)
                )
            );

        $dataMapper = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper::class);
        //print_r($query->executeQuery()->fetchAllAssociative());exit;
        $result = $dataMapper->map(
            Package::class,
            $queryBuilder->executeQuery()->fetchAllAssociative()
        );

        $name = $slug;
        
        if(isset($result[0])){
            foreach($result[0]->getPackageelements() as $pe){
                if($pe->getProductelement()->getUniqid() == $slug){
                    $name = $pe->getName()?:$pe->getProductelement()->getName();
                }
            }
        }
 

            
        return $name;
   
	} 

}

?>
