<?php


namespace App\Controller;


use App\Models\Attributes;
use App\Models\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends BaseController
{
    /**
     * @Route("/categoris/{id}/{page}", name="categories")
     * @param int $id
     * @param int|null $page
     * @return Response
     */
    public function categoriesProduct(int $id, ?int $page = 1): Response
    {
        $sql = $this->form($_GET);
        $offset = ($page - 1) * $this->parameters['productOnePage'];
        if ($sql) {
            $sql = "SELECT p.name, p.id, p.description, c.name AS group_id
                    FROM attributes_value AS av 
                    JOIN products AS p ON av.id_product = p.id
                    JOIN category AS c ON p.id_category = c.id
                    WHERE ($sql) AND p.id_category = $id
                    GROUP BY p.id
                    ORDER BY p.id DESC
                    LIMIT {$this->parameters['productOnePage']} OFFSET $offset";
        } else {
            $sql = "SELECT p.id, p.name, p.description, c.name AS group_id, a.name AS atr_name, av.value AS atr_value
                FROM products AS p
                JOIN category AS c
                JOIN attributes AS a
                JOIN attributes_value AS av ON a.id = av.id_attribute AND p.id = av.id_product
                WHERE p.id_category = $id
                GROUP BY p.id
                ORDER BY p.id DESC
                LIMIT {$this->parameters['productOnePage']} OFFSET $offset
                ";
        }
        $mysql = $this->mysql;
        $result = $mysql->getMysql()->query($sql);

        $products = [];
        $groupName = '';
        if (!empty($result)) {
            /** @var Product $row */
            while (($row = $result->fetch_object(Product::class)) != false) {
                if (empty($groupName)) {
                    $groupName = $row->getGroupId();
                }
                if (empty($products[$row->getId()])) {
                    $products[$row->getId()]['name'] = $row->getName();
                    $products[$row->getId()]['description'] = $row->getDescription();
                }
                $atr = json_decode($row->getAtrValue()) ?? $row->getAtrValue();
                $products[$row->getId()]['atr'][] = ['atr_name' => $row->getAtrName(), 'atr_value' => $atr];
            }
        }

        $countPage = (int)(count($products) / $this->parameters['productOnePage']);
        $pages = $this->pagination($countPage, $page);

        $parameters = [
            'pageName' => $groupName,
            'products' => $products,
            'pages' => $pages,
            'productAttributes' => $this->getCategoryAttributes($id),
        ];
        $parameters = $this->baseParameters($parameters);

        return $this->render('shop/categoriesProduct.html.twig', $parameters);
    }

    private function form($request)
    {
        $string = [];
        foreach ($request as $id => $value) {
            if (!empty($value['varchar'])) {
                $string[] = "id_attribute = $id AND value = '{$value['varchar']}'";
            }
            if (!empty($value['integer'])) {
                $sqlSTR = [];
                $value = $value['integer'];
                if (!empty($value['min'])) {
                    $sqlSTR[] = " AND value > {$value['min']} - 1";
                }
                if (!empty($value['max'])) {
                    $sqlSTR[] = " AND value < {$value['max']} + 1";
                }
                if (!empty($sqlSTR)) {
                    $string[] = "id_attribute = $id" . implode('', $sqlSTR);
                }
            }
            if (!empty($value['choice'])) {
                $sqlSTR = [];
                $value = $value['choice'];
                foreach ($value as $item) {
                    $sqlSTR[] = " AND value LIKE '%$item%'";
                }
                if (!empty($sqlSTR)) {
                    $string[] = "id_attribute = $id" . implode('', $sqlSTR);
                }
            }
        }
        $sql = '';
        $countST = count($string) - 1;
        foreach ($string as $key => $item) {
            $sql .= "($item)";
            if ($countST != $key) {
                $sql .= " OR ";
            }
        }

        return $sql;
    }

    /**
     * @param int $categoryId
     * @return array|null
     */
    private function getCategoryAttributes(int $categoryId): ?array
    {
        $sql = "SELECT a.name, a.id, a.type, av.value AS value
                FROM attributes AS a 
                JOIN products AS p ON p.id_category = $categoryId
                JOIN attributes_value AS av ON a.id = av.id_attribute
                GROUP BY a.id
                ";
        /** @var Attributes[] $attributes */
        $attributes = $this->mysql->select($sql, Attributes::class);
        $sortAtr = [];
        foreach ($attributes as $attribute) {
            if ($attribute->getType() == 'choice') {
                if (empty($sortAtr[$attribute->getId()])) {
                    $attribute->setValue(json_decode($attribute->getValue()));
                } else {
                    $attribute->setValue(json_decode($attribute->getValue()));
                    $values = $sortAtr[$attribute->getId()]->getValue();
                    $attribute->setValue(array_merge($attribute->getValue(), $values));
                }
            } elseif ($attribute->getType() == 'integer') {
                if (empty($sortAtr[$attribute->getId()])) {
                    $attribute->setValueMin($attribute->getValue());
                    $attribute->setValueMax($attribute->getValue());
                }
            }
            $sortAtr[$attribute->getId()] = $attribute;
        }
        return $sortAtr;
    }
}
