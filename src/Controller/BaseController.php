<?php


namespace App\Controller;


use App\Database\DataBase;
use App\Models\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    /**
     * @var DataBase
     */
    protected $mysql;
    protected $parameters;

    /**
     * BaseController constructor.
     * @param array $dbInfo
     * @param array $parameters
     * @throws \Exception
     */
    public function __construct(array $dbInfo, array $parameters)
    {
        $mysql = new DataBase($dbInfo);
        $this->mysql = $mysql;
        $this->parameters = $parameters;
    }

    /**
     * @param array|null $parametersNew
     * @return array
     */
    protected function baseParameters(array $parametersNew = null): array
    {
        $sql = "SELECT * FROM category";
        $categories = $this->mysql->select($sql, Category::class);
        $parameters = $this->getParameter('site');
        $baseParameters = [
            'companyName' => $parameters['companyName'],
            'pageName' => 'Unnamed',
            'categories' => $categories,
        ];

        if (!empty($parametersNew)) {
            $baseParameters = array_merge($baseParameters, $parametersNew);
        }

        return $baseParameters;
    }

    /**
     * @Route("/", name="indexPage")
     * @return RedirectResponse
     */
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('homePage', ['page' => '1']);
    }

    /**
     * @param $countPage
     * @param $thisPage
     * @return int[]
     */
    protected function pagination($countPage, $thisPage): array
    {
        $pages = [];
        if ($thisPage > 0 && $thisPage <= $countPage) {
            if ($countPage == 1) {
                $pages = [1];
            } elseif ($countPage == 2) {
                $pages = [1, 2];
            } elseif ($countPage == 3) {
                $pages = [1, 2, 3];
            } else {
                if ($thisPage < $countPage) {
                    if ($thisPage > 1) {
                        $pages = [$thisPage - 1, $thisPage, $thisPage + 1];
                    } else {
                        if ($thisPage + 2 <= $countPage) {
                            $pages = [$thisPage, $thisPage + 1, $thisPage + 2];
                        }
                    }
                } else {
                    $pages = [$thisPage - 2, $thisPage - 1, $thisPage];
                }
            }
        }

        return $pages;
    }
}
