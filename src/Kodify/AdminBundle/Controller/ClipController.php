<?php

namespace Kodify\AdminBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Kodify\SimpleCrudBundle\Controller\AbstractCrudController;
use Kodify\SimpleCrudBundle\Controller\CrudControllerInterface;

use Kodify\AdminBundle\Entity\Clip;
use Kodify\AdminBundle\Entity\Video;

class ClipController extends AbstractCrudController implements CrudControllerInterface
{
    protected $controllerName = 'clip';
    protected $entityClass = 'Kodify\AdminBundle\Entity\Clip';
    protected $formClassName = 'Kodify\AdminBundle\Form\ClipType';
    protected $pageTitle = '';
    protected $formLayout = 'KodifyAdminBundle:Clip:form.html.twig';

    /**
     * @Route("/clip/list/", name="get_clip")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAction(Request $request)
    {
        $this->indexKey = 'id';
        $this->addAction = false;
        $this->actions = array('edit');

        $this->getDoctrine()->getManager()->getRepository('Kodify\AdminBundle\Entity\Clip');

        return $this->renderTable();
    }

    public function getDefaultSort()
    {
        return array('id' => 'DESC');
    }

    public function defineTableHeader()
    {
        $tableHeader = array(
            array(
                'label' => 'Id',
                'sortable' => true,
                'filterable' => true,
                'default_sort_order' => 'DESC',
                'key' => 'id',
                'class' => 'input-micro'
            ),
            array(
                'label' => 'Video Id',
                'sortable' => true,
                'filterable' => true,
                'key' => 'Video.id',
                'class' => 'input-mini'
            ),
            array(
                'label' => 'Clip Title',
                'sortable' => true,
                'filterable' => true,
                'key' => 'title'
            ),
            array(
                'label' => 'Content Manager',
                'sortable' => true,
                'filterable' => true,
                'key' => 'contentManager'
            ),
            array(
                'label' => 'Pornstars',
                'key' => 'pornstars'
            ),
            array(
                'label' => 'Tags',
                'key' => 'tags'
            ),
            array(
                'label' => 'Timestamp',
                'sortable' => true,
                'filterable' => false,
                'key' => 'timestamp',
                'custom_cell_renderer' => 'KodifyAdminBundle:Video/Crud:cell_timestamp_renderer.html.twig'
            ),
            array(
                'label' => 'Status',
                'sortable' => true,
                'filterable' => true,
                'key' => 'status',
                'options' => Clip::getPossibleStatus(),
                'type' => 'options'
            )
        );

        return $tableHeader;
    }

    /**
     * @Route("/clip/put", name="add_video_clips")
     * @Route("/clip/put", name="post_add_video_clips")
     */
    public function addVideoClipsAction(Request $request)
    {
        if ($request->isMethod('POST')) {

            $clips = $request->get('clip');
            $totalClips = count($clips['title']);

            $clips = $request->get('clip');
            $videoId = $request->get('video_id');
            $videoRepo = $this->getDoctrine()->getRepository('KodifyAdminBundle:Video');
            $video = $videoRepo->findOneById($videoId);

            $tagRepo = $this->getDoctrine()->getRepository('KodifyAdminBundle:Tag');
            $pornstarRepo = $this->getDoctrine()->getRepository('KodifyAdminBundle:Pornstar');

            $em = $this->getDoctrine()->getManager();
            $username = $this->getUser()->getUsername();

            for ($i = 0; $i < $totalClips; $i++) {

                $invalidTags = $tagRepo->validateTagList(explode(',', $clips['tags'][$i]));
                if (count($invalidTags) > 0) {
                    $this->get('session')->setFlash('error', 'Clip has invalid tags: ' . implode(',', $invalidTags));

                    return new RedirectResponse($this->generateUrl('get_clip'));
                }

                $invalidPornstar = $pornstarRepo->validatePornstarList(explode(',', $clips['pornstars'][$i]));
                if (count($invalidPornstar) > 0) {
                    $this->get('session')->setFlash('error', 'Clip has invalid pornstars: ' . implode(',', $invalidPornstar));

                    return new RedirectResponse($this->generateUrl('get_clip'));
                }

                $obj = new Clip();

                $obj->setVideo($video);
                $obj->setTitle($clips['title'][$i]);
                $obj->setStartTime($clips['start'][$i]);
                $obj->setEndTime($clips['end'][$i]);
                $obj->setContentManager($username);
                $obj->setTimestamp(new \DateTime());
                $obj->setStatus(Clip::PENDING);
                $obj->setPornstars($clips['pornstars'][$i]);
                $obj->setTags($clips['tags'][$i]);

                $em->persist($obj);
            }

            $video->setStatus(Video::CUT);

            $em->persist($video);
            $em->flush();
        }

        $video = $videoRepo->getNextReadyVideo();
        if ($video instanceOf Video) {
            $url = $this->generateUrl('cut_video', array('id' => $video->getId()));
        } else {
            $url = $this->generateUrl('get_clip');
        }

        return new RedirectResponse($url);
    }

    /**
     * @Route("/clip/add", name="add_clip")
     * @Route("/clip/edit", name="post_add_clip")
     * @Route("/clip/edit", name="edit_clip")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request, $destinationUrl = null)
    {
        return parent::addAction($request);
    }
}
