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
use Indiz\Products\Domain\Model\Order;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;


class ProductController extends ActionController
{
    public function __construct(
        protected readonly ProductRepository $productRepository,
        protected readonly CategoryRepository $categoryRepository,
        protected readonly TagRepository $tagRepository,
        protected readonly ProductelementRepository $productElementRepository,
        protected readonly OrderRepository $orderRepository,
        protected readonly Mailer $mailer,
        private readonly PersistenceManager $persistenceManager
    ) {}

    public function filterAction(): \Psr\Http\Message\ResponseInterface
    {
        $allowed = ['categories', 'tags', 'searchquery', 'pagesize', 'page'];
        $filter = [];
        foreach ($allowed as $key) {
            if ($this->request->hasArgument($key)) {
                $filter[$key] = $this->request->getArgument($key);
            }
        }
        $feUser = $this->request->getAttribute('frontend.user');
        $feUser->setKey('ses', 'productFilter', $filter);
        $feUser->storeSessionData();

        return $this->redirect('index');
    }

    public function indexAction(): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign("categories",$this->categoryRepository->findAll());
        $this->view->assign("tags",$this->tagRepository->findAll());

        // Merge session filter as base; direct request arguments override
        $sessionFilter = $this->request->getAttribute('frontend.user')->getKey('ses', 'productFilter') ?? [];
        $this->request->getAttribute('frontend.user')->setKey('ses', 'productFilter',[]);
        $getArg = function (string $key, mixed $default = null) use ($sessionFilter) {
            if ($this->request->hasArgument($key)) {
                return $this->request->getArgument($key);
            }
            return $sessionFilter[$key] ?? $default;
        };

        $pagesize    = (int) $getArg('pagesize', 12);
        if(!$pagesize){
            $pagesize = 12;
        }
        $page        = (int) $getArg('page', 0);
        $searchquery = $getArg('searchquery', '');
        $categories  = $getArg('categories', []);
        $tags        = $getArg('tags', []);

        if ($searchquery) {
            $this->view->assign('searchquery', $searchquery);
        }

        $productsallcount = $this->productRepository->countAll();
        $this->view->assign('productsallcount', $productsallcount);
        $this->view->assign('currentpage', $page);

        // Singular 'category' comes from the URL route enhancer; normalise into the array used everywhere else
        if ($this->request->hasArgument("category") && !empty($this->request->getArgument("category"))) {
            $this->setCategoryMeta($this->request->getArgument("category"));
            $categories = [(int)$this->request->getArgument("category")];
            $this->view->assign("selectedCategories", array_flip($categories));
            $this->view->assign('products', $this->productRepository->findByAttributes($categories, [], $searchquery, $page, $pagesize));
            $this->view->assign('productscount', $this->productRepository->findByAttributes($categories, [], $searchquery));
            return $this->htmlResponse();
        }

        if (!empty($categories) || !empty($tags)) {
            if (!empty($categories)) {
                $this->view->assign("selectedCategories", array_flip((array)$categories));
            }
            if (!empty($tags)) {
                $this->view->assign("selectedTags", array_flip((array)$tags));
            }

            if(count($categories)){
                $this->setCategoryMeta($categories[0]);
            }
            $this->view->assign('products', $this->productRepository->findByAttributes($categories, $tags, $searchquery, $page, $pagesize));
            $productscount = $this->productRepository->findByAttributes($categories, $tags, $searchquery);
        } elseif ($searchquery) {
            $this->view->assign('products', $this->productRepository->findByAttributes([], [], $searchquery, $page, $pagesize));
            $this->view->assign("selectedCategories", []);
            $productscount = $this->productRepository->findByAttributes([], [], $searchquery);
        } else {
            $this->view->assign('products', $this->productRepository->findByAttributes([], [], "", $page, $pagesize));
            $this->view->assign("selectedCategories", []);
            $productscount = $this->productRepository->findByAttributes([], [], "");
        }

        $this->view->assign('pages', array_fill(0, ceil($productscount / $pagesize), 1));
        $this->view->assign('productscount', $productscount);
        $this->view->assign('pagesize', $pagesize);

        return $this->htmlResponse();
    }

    public function setCategoryMeta($cat){
        $c_category = $this->categoryRepository->findByUid($cat);

        if ($c_category->getDescription()) {
            $manager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)
                ->getManagerForProperty('description');
            $manager->addProperty('description', strip_tags($c_category->getDescription()), [], true);
        }
    }

    /**
     * @param \Indiz\Products\Domain\Model\Product $product
     */	
    public function showAction(\Indiz\Products\Domain\Model\Product $product): \Psr\Http\Message\ResponseInterface
    {
        $description = $product->getDescription();
        if ($description) {
            $manager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)
                ->getManagerForProperty('description');
            $manager->addProperty('description', strip_tags($description), [], true);
        }

        $this->view->assign('product', $product);
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
     * @param Order $order
     */
    public function orderAction(Order $order): \Psr\Http\Message\ResponseInterface
    {
        $receiver = "tech@indiz.digital";
        $order->setPid($this->settings["orderpid"]);
        
        $cc = $receiver;
        $packageelements = $this->request->hasArgument("packageelements")?$this->request->getArgument("packageelements"):[];

        if ($order->getProductUid()) {
            $product = $this->productRepository->findByUid($order->getProductUid());
            $vars['product'] = $product;
            $vars['order'] = $order;
            if(isset($packageelements[$order->getPackageUid()])){
                $vars['packageelements'] = $packageelements[$order->getPackageUid()];
                $order->setData(json_encode($vars['packageelements']));
            }
            
            $this->orderRepository->add($order);
            $this->persistenceManager->persistAll();
        
            // Assuming you have a method to find package by uid, e.g., in ProductRepository or a PackageRepository
             if ($product) {
                
                $subject = ($order->getOrdertype()?"Order for ":"Config check for ") . $order->getOrdername();
                $template = "Order";
                $this->mailer->send($receiver,$cc,$subject,$template, $vars);
                
                $message = 'Mail sent successfully.';
            } else {
                $message =  'Package not found.';
            }
        } else {
            $message =  'No package selected.';
        }
        echo $message;exit;
        
        return $this->redirect("finish",NULL,NULL,["order"=>$order->getUid(),"message"=>urlencode($message)]);
    }

    /**
     * @param Order $order
     * @param string $message
     */
    public function finishAction(Order $order,$message){
        $this->view->assign('order', $order);
        return $this->htmlResponse();
    }

    public function createOrder($vars): void
    {
        // Here you would typically create an Order object and persist it to the database
        // For example:
        
        /*$order = new \Indiz\Products\Domain\Model\Order();
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
        $order->setProductUid($vars['product_uid']);*/
        $this->orderRepository->add($order);
        
    }
}
