<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Binder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Binder controller.
 *
 * @Route("binder")
 */
class BinderController extends Controller
{
    /**
     * Lists all binder entities.
     *
     * @Route("/", name="binder_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $binders = $em->getRepository('AppBundle:Binder')->findAll();

        return $this->render('binder/index.html.twig', array(
            'binders' => $binders,
        ));
    }

    /**
     * Creates a new binder entity.
     *
     * @Route("/new", name="binder_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $binder = new Binder();
        $form = $this->createForm('AppBundle\Form\BinderType', $binder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($binder);
            $em->flush();

            return $this->redirectToRoute('binder_show', array('id' => $binder->getId()));
        }

        return $this->render('binder/new.html.twig', array(
            'binder' => $binder,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a binder entity.
     *
     * @Route("/{id}", name="binder_show")
     * @Method("GET")
     */
    public function showAction(Binder $binder)
    {
        $deleteForm = $this->createDeleteForm($binder);

        return $this->render('binder/show.html.twig', array(
            'binder' => $binder,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing binder entity.
     *
     * @Route("/{id}/edit", name="binder_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Binder $binder)
    {
        $deleteForm = $this->createDeleteForm($binder);
        $editForm = $this->createForm('AppBundle\Form\BinderType', $binder);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('binder_edit', array('id' => $binder->getId()));
        }

        return $this->render('binder/edit.html.twig', array(
            'binder' => $binder,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a binder entity.
     *
     * @Route("/{id}", name="binder_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Binder $binder)
    {
        $form = $this->createDeleteForm($binder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($binder);
            $em->flush();
        }

        return $this->redirectToRoute('binder_index');
    }

    /**
     * Creates a form to delete a binder entity.
     *
     * @param Binder $binder The binder entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Binder $binder)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('binder_delete', array('id' => $binder->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
