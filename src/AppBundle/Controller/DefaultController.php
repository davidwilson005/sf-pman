<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Binder;
use Doctrine\ORM\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/add")
     * @return Response
     */
    public function addAction(Request $request)
    {
        $binder = new Binder();
        $binder->setName('Test' . rand(1, 100))
            ->setParentId(0);

        $this->getEm()->persist($binder);
        $this->getEm()->flush();

        return $this->displayMessage('Binder has been created');
    }

    /**
     * @Route("/remove/{binderId}")
     * @throws EntityNotFoundException
     * @return Response
     */
    public function removeAction($binderId)
    {
        $binder = $this->getEm()->find('AppBundle:Binder', $binderId);

        if ( ! $binder) {
            throw new EntityNotFoundException('Binder not found');
        }

        $this->getEm()->remove($binder);
        $this->getEm()->flush();


        return $this->displayMessage('Binder has been removed');
    }

    /**
     * @Route("/list")
     * @return Response
     */
    public function listAction()
    {
        //$binders = $this->getEm()->getRepository('AppBundle:Binder')->findAll();

        $binders = $this->getEm()
            ->createQuery('SELECT b FROM AppBundle:Binder b WHERE b.deletedOn IS NULL')
            ->getResult();

        $message = '';
        foreach ($binders as $binder) {
            $message .= $binder->getId() . ': ' . $binder->getName() . "<br />";
        }

        return $this->displayMessage($message);
    }

    /**
     * @Route("/show/{binderId}")
     * @throws EntityNotFoundException
     * @return Response
     */
    public function showAction($binderId)
    {
        $binder = $this->getEm()->find('AppBundle:Binder', $binderId);

        if ( ! $binder) {
            throw new EntityNotFoundException('Binder not found');
        }

        $message = 'ID: ' . $binder->getId() . "<br />" .
                   'Name: ' . $binder->getName() . "<br />";

        return $this->displayMessage($message);
    }

    private function displayMessage($message)
    {
        return $this->render('default/message.html.twig', [
            'message' => $message
        ]);
    }

    private function getEm()
    {
        return $this->getDoctrine()->getManager();
    }
}
