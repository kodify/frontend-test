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

use Kodify\AdminBundle\Entity\Tag;
use Kodify\AdminBundle\Form\TagType;

class TagController extends AbstractCrudController implements CrudControllerInterface
{
    protected $controllerName = 'tag';
    protected $entityClass = 'Kodify\AdminBundle\Entity\Tag';
    protected $formClassName = 'Kodify\AdminBundle\Form\TagType';
    protected $pageTitle = '';

    /**
     * @Route("/tag/list", name="get_tag")
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
                'label' => 'Status',
                'sortable' => true,
                'filterable' => true,
                'key' => 'enabled',
                'type' => 'boolean'
            )
        );

        return $tableHeader;
    }

    /**
     * @Route("/tag/add", name="add_tag")
     * @Route("/tag/add", name="post_add_tag")
     * @Route("/tag/edit", name="edit_tag")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, $destinationUrl = null)
    {
        return parent::addAction($request, $destinationUrl);
    }

    /**
     * @Route("/tag/autocomplete", name="tags_autocomplete")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function autocompleteAction(Request $request)
    {
        $list = $this->getDoctrine()->getRepository('KodifyAdminBundle:Tag')->getTagList($request->get('term'));

        $response = array();
        foreach ($list as $p) {
            $response[] = array(
                'id' => $p->getId(),
                'label' => $p->getName()
            );
        }

        return new Response(json_encode($response));
    }

    /**
     * @Route("/tag/validate", name="validate_tags")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function validateAction(Request $request)
    {
        $inputList = explode(',', $request->get('list'));
        $response = $this->getDoctrine()->getRepository('KodifyAdminBundle:Tag')->validateTagList($inputList);

        return new Response(json_encode(array_values($response)));
    }
}
