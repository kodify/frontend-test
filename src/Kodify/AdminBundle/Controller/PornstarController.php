<?php

namespace Kodify\AdminBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Kodify\SimpleCrudBundle\Controller\AbstractCrudController;
use Kodify\SimpleCrudBundle\Controller\CrudControllerInterface;

use Kodify\AdminBundle\Entity\Pornstar;
use Kodify\AdminBundle\Form\PornstarType;

class PornstarController extends AbstractCrudController implements CrudControllerInterface
{
    protected $controllerName = 'pornstar';
    protected $entityClass = 'Kodify\AdminBundle\Entity\Pornstar';
    protected $formClassName = 'Kodify\AdminBundle\Form\PornstarType';
    protected $formLayout = 'KodifyAdminBundle:Pornstar:form.html.twig';
    protected $pageTitle = '';

    /**
     * @Route("/pornstar/list", name="get_pornstar")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAction(Request $request)
    {
        $this->indexKey = 'id';
        $this->addAction = true;
        $this->actions = array('edit');


        return $this->renderTable();
    }

    public function defineTableHeader()
    {
        $tableHeader = array(
            array(
                'label' => 'Id',
                'sortable' => true,
                'filterable' => true,
                'default_sort_order' => 'DESC',
                'key' => 'id'
            ),
            array(
                'label' => 'Name',
                'sortable' => true,
                'filterable' => true,
                'key' => 'name',
                'filter_operator' => 'RIGHT_LIKE'
            ),
            array(
                'label' => 'Alias',
                'sortable' => true,
                'filterable' => true,
                'key' => 'alias',
                'filter_operator' => 'RIGHT_LIKE'
            ),
            array(
                'label' => 'Twitter',
                'sortable' => true,
                'filterable' => true,
                'key' => 'twitter',
                'filter_operator' => 'RIGHT_LIKE'
            ),
            array(
                'label' => 'Status',
                'sortable' => true,
                'filterable' => true,
                'key' => 'enabled',
                'type' => 'boolean'
            )
        );

        return $tableHeader;
    }

    public function getDefaultSort()
    {
        return array('name' => 'ASC');
    }

    /**
     * @Route("/pornstar/add", name="add_pornstar")
     * @Route("/pornstar/edit", name="edit_pornstar")
     * @Route("/pornstar/edit", name="post_add_pornstar")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, $destinationUrl = null)
    {
        return parent::addAction($request, $destinationUrl);
    }

    /**
     * @Route("/pornstar/autocomplete", name="pornstars_autocomplete")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function autocompleteAction(Request $request)
    {
        $list = $this->getDoctrine()->getRepository('KodifyAdminBundle:Pornstar')->getPornstarList($request->get('term'));

        $response = array();
        foreach ($list as $p) {
            $response[] = array(
                'id' => $p->getId(),
                'label' => $p->getName(),
                'imgsrc' => $p->getThumbnailUrl()
            );
        }

        return new Response(json_encode($response));
    }

    /**
     * @Route("/pornstar/validate", name="validate_pornstars")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function validateAction(Request $request)
    {
        $inputList = explode(',', $request->get('list'));
        $response = $this->getDoctrine()->getRepository('KodifyAdminBundle:Pornstar')->validatePornstarList($inputList);

        return new Response(json_encode(array_values($response)));
    }
}
