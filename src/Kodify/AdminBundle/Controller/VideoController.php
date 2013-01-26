<?php

namespace Kodify\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Kodify\SimpleCrudBundle\Controller\AbstractCrudController;
use Kodify\SimpleCrudBundle\Controller\CrudControllerInterface;

use Kodify\AdminBundle\Entity\Video;
use Kodify\AdminBundle\Entity\Clip;

class VideoController extends AbstractCrudController implements CrudControllerInterface
{
    protected $pageTitle = '';
    protected $controllerName = 'video';
    protected $entityClass = 'Kodify\AdminBundle\Entity\Video';
    const DUPLICATED_DOWNLOAD = 'download';
    const DUPLICATED_DISCARD = 'discard';

    /**
     * @Route("/video/list", name="get_video")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAction(Request $request)
    {
        $this->indexKey = 'id';
        $this->addAction = false;
        $this->actions = array(
            array(
                'route_name' => 'cut',
                'ico' => 'share-alt',
                'label' => 'Cut',
            )
        );

        return $this->renderTable();
    }

    protected function getcustom_row_class_renderer()
    {
        return 'KodifyAdminBundle:Video/Crud:row_class_renderer.html.twig';
    }

    protected function getcustom_action_button_renderer()
    {

        return 'KodifyAdminBundle:Video/Crud:duplicated_videos_action_renderer.html.twig';
    }


    /**
     * @param string  $videoId
     *
     * @Route("/video/cancelCut/{id}", name="cancel_url", requirements={"id"="\d+"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cancelCutVideoAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $video = $em->getRepository($this->entityClass)->findOneById($id);
        $currentUsername = $this->getUser()->getUsername();

        if ($video->getStatus() == Video::BLOCKED && $video->getBlockedBy() == $currentUsername) {

            $video->setStatus(Video::READY);
            $video->setBlockedBy('');

            $em->persist($video);
            $em->flush();
        }

        return new RedirectResponse($this->generateUrl('get_video'));
    }

    /**
     * @param string  $id
     *
     * @Route("/video/markAsUnsuitable/{id}", name="unsuitable_url", requirements={"id"="\d+"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function markAsUnsuitableAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $videoRepo = $em->getRepository($this->entityClass);
        $video = $videoRepo->findOneById($id);
        $video->setStatus(Video::CANCELLED);

        $em->persist($video);
        $em->flush();

        $video = $videoRepo->getNextReadyVideo();
        if ($video instanceOf Video) {
            $url = $this->generateUrl('cut_video', array('id' => $video->getId()));
        } else {
            $url = $this->generateUrl('get_video');
        }

        return new RedirectResponse($url);
    }

    /**
     * @Route("/video/cut/{id}", name="cut_video", requirements={"id"="\d+"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cutVideoAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $video = $em->getRepository($this->entityClass)->findOneById($id);

        $currentUsername = $this->getUser()->getUserName();

        if (!$video instanceof Video) {
            $response = new RedirectResponse($this->generateUrl('get_video'));

            return $response;
        }

        if ($video->getStatus() == Video::BLOCKED && $video->getBlockedBy() != $currentUsername) {
            $this->get('session')->setFlash('error', 'This video is blocked by ' . $video->getBlockedBy());
            $list = $this->generateUrl('get_video');
            $response = new RedirectResponse($list);

            return $response;
        } else if (Video::DUPLICATE_WARNING == $video->getStatus()) {
            $action = $this->getRequest()->get('action');
            if (static::DUPLICATED_DISCARD != $action && static::DUPLICATED_DOWNLOAD != $action) {
                $this->get('session')->setFlash('error', 'This video can not be cut');
                $list = $this->generateUrl('get_video');

                return new RedirectResponse($list);
            }
            if (static::DUPLICATED_DISCARD == $action) {
                $video->setStatus(Video::MARKED_AS_DUPLICATED);
                $this->get('session')->setFlash('info', 'Okay, video will be deleted.');
            } else {
                $video->setStatus(Video::MARKED_AS_NOT_DUPLICATED);
                $this->get('session')->setFlash(
                    'info',
                    'Success! I\'ll download this video, will be available for cutting shortly.'
                );
            }
            $em->persist($video);
            $em->flush();
            $list = $this->generateUrl('get_video');

            return new RedirectResponse($list);
        } else if ($video->getStatus() != Video::READY && $video->getStatus() != Video::BLOCKED) {
            $this->get('session')->setFlash('error', 'This video can not be cut');
            $list = $this->generateUrl('get_video');
            $response = new RedirectResponse($list);

            return $response;
        }

        $video->setStatus(Video::BLOCKED);
        $video->setBlockedBy($currentUsername);

        $em->persist($video);
        $em->flush();

        $pornstarsRoute = $this->generateUrl('pornstars_autocomplete');
        $tagsRoute = $this->generateUrl('tags_autocomplete');
        $postRoute = $this->generateUrl('add_video_clips');
        $cancelUrl = $this->generateUrl('cancel_url', array('id' => $video->getId()));
        $unsuitableUrl = $this->generateUrl('unsuitable_url', array('id' => $video->getId()));
        $validateTagsUrl = $this->generateUrl('validate_tags');
        $validatePornstarsUrl = $this->generateUrl('validate_pornstars');

        $title = basename($video->getOriginalName());
        if ($pos = strpos($title, '?')) {
            $title = substr($title, 0, $pos);
        }

        $s3BucketName = $this->container->getParameter('s3_bucket_name');
        $s3BucketPath = $this->container->getParameter('s3_bucket_path');
        $completeUrl = $s3BucketPath . '/'. $s3BucketName . '/' . $video->getFilename();
        $thumbnailInterval = $this->container->getParameter('video_thumbnail_interval_seconds');

        return $this->render(
            'KodifyAdminBundle:Video:cut_form.html.twig',
            array(
                'title' => $title,
                'video_url' => $completeUrl,
                'video_id' => $video->getId(),
                'thumbnail_list' => $video->getThumbnails($thumbnailInterval),
                'autocomplete_pornstars' => $pornstarsRoute,
                'autocomplete_tags' => $tagsRoute,
                'post_url' => $postRoute,
                'cancel_url' => $cancelUrl,
                'validate_tags_url' => $validateTagsUrl,
                'validate_pornstars_url' => $validatePornstarsUrl,
                'unsuitable_url' => $unsuitableUrl,
                's3_bucket_path' => $s3BucketPath . '/' . $s3BucketName . '/',
                'userName' => $this->getUser()->getUserName(),
                'duration' => $video->getDurationInMilliseconds() / 1000,
            )
        );
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
                'label' => 'Original filename',
                'sortable' => true,
                'filterable' => true,
                'key' => 'originalName',
                'filter_operator' => 'RIGHT_LIKE',
                'custom_cell_renderer' => 'KodifyAdminBundle:Video/Crud:cell_original_filename_renderer.html.twig'
            ),
            array(
                'label' => 'Timestamp',
                'sortable' => true,
                'filterable' => false,
                'key' => 'timestamp',
                'custom_cell_renderer' => 'KodifyAdminBundle:Video/Crud:cell_timestamp_renderer.html.twig'
            ),
            array(
                'label' => 'Duration',
                'sortable' => true,
                'filterable' => false,
                'key' => 'durationInMilliseconds',
                'custom_cell_renderer' => 'KodifyAdminBundle:Video/Crud:cell_duration_renderer.html.twig'
            ),
            array(
                'label' => 'Status',
                'sortable' => true,
                'filterable' => true,
                'key' => 'status',
                'options' => Video::getPossibleStatus(),
                'type' => 'options'
            ),
            array(
                'label' => 'Blocked by',
                'sortable' => false,
                'filterable' => false,
                'key' => 'blockedBy'
            )
        );

        return $tableHeader;
    }

    public function getDefaultSort()
    {
        return array('id' => 'ASC');
    }
}
