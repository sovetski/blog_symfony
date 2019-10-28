<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="app_admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/blog/article/new", name="app_admin_blog_article_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newArticle(Request $request)
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article, [
            'validation_groups' => ['Default','new']
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            return $this->persistArticle($article, 'Le nouvel article a bien été crée');
        }

        return $this->render('admin/article.new.html.twig', [
            "form" => $form->createView()
        ]);
    }

    private function persistArticle(Article $article, string $message)
    {

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        $this->addFlash('success', $message);

        return $this->redirectToRoute('app_article_show', [
            'id' => $article->getId()
        ]);
    }
}
