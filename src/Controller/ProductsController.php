<?php


namespace App\Controller;


use App\Models\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends BaseController
{
    /**
     * @Route("/products/{page}", name="homePage")
     * @param int|null $page
     * @return Response
     */
    public function products(?int $page = 1): Response
    {
        $offset = ($page - 1) * $this->parameters['productOnePage'];
        $sql = "SELECT p.id, p.name, p.description, CONCAT(c.name) AS group_id, CONCAT(a.name) AS atr_name, CONCAT(av.value) AS atr_value
                FROM products AS p
                JOIN category AS c ON p.id_category = c.id
                JOIN attributes AS a ON p.id_category = a.id_category OR a.id_category IS NULL 
                JOIN attributes_value AS av ON p.id_category = av.id_category AND a.id = av.id_attribute AND p.id = av.id_product
                ORDER BY p.id DESC
                LIMIT {$this->parameters['productOnePage']} OFFSET $offset
                ";
        $mysql = $this->mysql;
        $result = $mysql->getMysql()->query($sql);

        $products = [];
        if (!empty($result)){
            /** @var Product $row */
            while (($row = $result->fetch_object(Product::class)) != false) {
                if (empty($products[$row->getId()])){
                    $products[$row->getId()]['name'] = $row->getName();
                    $products[$row->getId()]['description'] = $row->getDescription();
                    $products[$row->getId()]['group_id'] = $row->getGroupId();
                }
                $atr = json_decode($row->getAtrValue()) ?? $row->getAtrValue();
                $products[$row->getId()]['atr'][] = ['atr_name' => $row->getAtrName(), 'atr_value' => $atr];
            }
        }

        $countPage = (int)(count($products) / $this->parameters['productOnePage']);
        $pages = $this->pagination($countPage, $page);

        $parameters = $this->baseParameters([
            'pageName' => 'Главная',
            'products' => $products,
            'pages' => $pages,
        ]);

        return $this->render('shop/home.html.twig', $parameters);
    }
}
