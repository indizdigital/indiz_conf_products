<?php
namespace Indiz\Products\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Indiz\Products\Services\Mailer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Indiz\Products\Domain\Repository\ProductRepository;
use Indiz\Products\Domain\Repository\ProductelementRepository;
use Indiz\Products\Domain\Repository\CategoryRepository;
use Indiz\Products\Domain\Repository\OrderRepository;
use Indiz\Products\Domain\Repository\TagRepository;

class ProductController extends ActionController
{
    public function __construct(
        protected readonly ProductRepository $productRepository,
        protected readonly CategoryRepository $categoryRepository,
        protected readonly TagRepository $tagRepository,
        protected readonly ProductelementRepository $productElementRepository,
        protected readonly OrderRepository $orderRepository,
        protected readonly Mailer $mailer
    ) {}

    public function indexAction(): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign("categories",$this->categoryRepository->findAll());
        $this->view->assign("tags",$this->tagRepository->findAll());
        
        $this->view->assign('productscount',$this->productRepository->countAll());
        $searchquery = $this->getSearch();

        if(($this->request->hasArgument("categories") && !empty($this->request->getArgument("categories"))) || 
        ($this->request->hasArgument("tags") && !empty($this->request->getArgument("tags")))){
            $categories = $this->request->hasArgument("categories")?$this->request->getArgument("categories"):[];
            $tags = $this->request->hasArgument("tags")?$this->request->getArgument("tags"):[];
            if(!empty($categories)){
                $this->view->assign("selectedCategories",array_flip($categories));
            }
            if(!empty($tags)){
                $this->view->assign("selectedTags",array_flip($tags));
            }
            $this->view->assign('products',$this->productRepository->findByAttributes($categories,$tags,$searchquery));  
        }elseif($searchquery){
            $this->view->assign('products',$this->productRepository->findByAttributes([],[],$searchquery));
            $this->view->assign("selectedCategories",[]);
        }else{
            $this->view->assign('products',$this->productRepository->findByAttributes([],[],""));  
            $this->view->assign("selectedCategories",[]);
        }
        
        return $this->htmlResponse();
    }

    public function getSearch(){
        $searchquery = "";
        if($this->request->hasArgument("searchquery")){
            $searchquery = $this->request->getArgument("searchquery");
            $this->view->assign("searchquery",$searchquery);
        }
        return $searchquery;
    }

    /**
     * @param \Indiz\Products\Domain\Model\Product $product
     */	
    public function showAction(\Indiz\Products\Domain\Model\Product $product): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign('product',$product);
        return $this->htmlResponse();
    }

    /**
     *	import a csv and create products
    */	
    public function importAction(): \Psr\Http\Message\ResponseInterface
    {
        $uploadedFiles = $this->request->getUploadedFiles();
        if(true){
            $tags = ["Agentic AI","AI","Artificial Intelligence","Automation","Audio","Backup","CaaS","Chat","CM","CMS","Completions","CRM","DBaaS","Dedicated","Development","DNSaaS","Embeddings","GPU","IaaS","Kubernetes","LBaaS","LLM","LLMaaS","Managed","Managed Service","OpenAI API","PaaS","Rerank","SaaS","Shared","Storage","Transcriptions"];
            foreach($tags as $tag){
                $tagObj = new \Indiz\Products\Domain\Model\Tag();
                $tagObj->setName($tag);
                $tagObj->setPid(2);
            $this->tagRepository->add($tagObj);
            }
        }
        
        
        if (isset($uploadedFiles['csvFile']) && !empty($uploadedFiles['csvFile'])) {
            $file = $uploadedFiles['csvFile'];
            $csvPath = $file->getTemporaryFileName();
            $handle = fopen($csvPath, 'r');
            if ($handle !== false) {
                while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                    $product = new \Indiz\Products\Domain\Model\ProductElement();
                    $product->setName($data[0]);
                    $product->setPrice((float)$data[1]);
                    $product->setUnit($data[2]);
                    $product->setPid(2);
                    $this->productElementRepository->add($product);
                }
                
                fclose($handle);
                //$this->persistenceManager->persistAll();
            }
        }
        
        return $this->htmlResponse();
    }
    /**
     * send a mail with selected product
     * @param string $ordername
     * @param string $name
     * @param string $email
     * @param string $street
     * @param string $postalcode
     * @param string $city
     * @param string $country
     * @param string $company
     * @param array $packageelements
     * @param int $packageUid
     * @param int $productUid
     */
    public function orderAction($ordername,$name, $email, $street, $postalcode, $city, $country, $company,$packageelements, $packageUid = null, $productUid = null): \Psr\Http\Message\ResponseInterface
    {
        $receiver = "tech@indiz.digital";
           $vars =[
            'ordername'=>$ordername,
            'name' => $name,
            'email' => $email,
            'street' => $street,
            'postalcode' => $postalcode,
            'city' => $city,
            'country' => $country,
            'company' => $company,
            'package_uid' => $packageUid,
            'product_uid' => $productUid,
            'packageelements' => $packageelements
        ];
        
        $this->createOrder($vars);
        if ($productUid) {
            // Assuming you have a method to find package by uid, e.g., in ProductRepository or a PackageRepository
            $product = $this->productRepository->findByUid($productUid);
            $vars['product'] = $product;
            if ($product) {
                
                
                $this->mailer->send($receiver,$email,"Order", $vars);
                
                $this->view->assign('message', 'Mail sent successfully.');
            } else {
                $this->view->assign('message', 'Package not found.');
            }
        } else {
            $this->view->assign('message', 'No package selected.');
        }
        $this->view->assign('products', $this->productRepository->findAll());
        return $this->htmlResponse();
    }

    public function createOrder($vars): void
    {
        // Here you would typically create an Order object and persist it to the database
        // For example:
        
        $order = new \Indiz\Products\Domain\Model\Order();
        $order->setPid($this->settings["orderpid"]);
        $order->setOrdername($vars['ordername']);
        $order->setName($vars['name']);
        $order->setEmail($vars['email']);
        $order->setStreet($vars['street']);
        $order->setPostalcode($vars['postalcode']);
        $order->setCity($vars['city']);
        $order->setCountry($vars['country']);
        $order->setCompany($vars['company']);
        $order->setPackageUid(isset($vars['package_uid']) ? $vars['package_uid'] : 0);
        $order->setProductUid($vars['product_uid']);
        $this->orderRepository->add($order);
        
    }
}
