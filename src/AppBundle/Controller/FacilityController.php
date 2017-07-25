<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Facility;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Facility controller.
 *
 * @Route("facility")
 */
class FacilityController extends Controller
{
    /**
     * Lists all facility entities.
     *
     * @Route("/", name="facility_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $facilities = $em->getRepository('AppBundle:Facility')->findAll();

        return $this->render('facility/index.html.twig', array(
            'facilities' => $facilities,
        ));
    }

    /**
     * Creates a new facility entity.
     *
     * @Route("/new", name="facility_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $facility = new Facility();
        $form = $this->createForm('AppBundle\Form\FacilityType', $facility);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($facility);
            $em->flush();

            return $this->redirectToRoute('facility_show', array('id' => $facility->getId()));
        }

        return $this->render('facility/new.html.twig', array(
            'facility' => $facility,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a facility entity.
     *
     * @Route("/{id}", name="facility_show")
     * @Method("GET")
     */
    public function showAction(Facility $facility)
    {
        $deleteForm = $this->createDeleteForm($facility);

        return $this->render('facility/show.html.twig', array(
            'facility' => $facility,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing facility entity.
     *
     * @Route("/{id}/edit", name="facility_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Facility $facility)
    {
        $deleteForm = $this->createDeleteForm($facility);
        $editForm = $this->createForm('AppBundle\Form\FacilityType', $facility);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('facility_edit', array('id' => $facility->getId()));
        }

        return $this->render('facility/edit.html.twig', array(
            'facility' => $facility,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a facility entity.
     *
     * @Route("/{id}", name="facility_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Facility $facility)
    {
        $form = $this->createDeleteForm($facility);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($facility);
            $em->flush();
        }

        return $this->redirectToRoute('facility_index');
    }

    /**
     * Creates a form to delete a facility entity.
     *
     * @param Facility $facility The facility entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Facility $facility)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('facility_delete', array('id' => $facility->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
