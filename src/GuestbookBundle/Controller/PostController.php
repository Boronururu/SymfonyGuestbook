<?php

namespace GuestbookBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use GuestbookBundle\Entity\Post;
use GuestbookBundle\Form\PostType;
use Gregwar\CaptchaBundle\Type\CaptchaType;

/**
 * Post controller.
 *
 * @Route("/")
 */
class PostController extends Controller
{
    /**
     * Lists all Post entities.
     *
     * @Route("/", name="_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $page = $request->query->get('page')?$request->query->get('page'):1;
        $sort = $request->query->get('sort')?$request->query->get('sort'):'date';
        $order = $request->query->get('order')?$request->query->get('order'):'DESC';

        $posts = $em->getRepository('GuestbookBundle:Post')->getAllPosts($page, $sort, $order);
        $limit = 15;
        $maxPages = ceil($posts->count() / $limit);
        $thisPage = $page;

        return $this->render('post/index.html.twig', compact('posts', 'maxPages', 'thisPage', 'sort', 'order'));
    }

    /**
     * Creates a new Post entity.
     *
     * @Route("/new", name="_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm('GuestbookBundle\Form\PostType', $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('_index');
        }

        return $this->render('post/new.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

}
