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

    /**
     * @Route("/createtree")
     */
    public function createTreeAction()
    {
        // make three manuals
        for ($mInc = 1; $mInc <= 3; $mInc++) {

            $manual[$mInc] = new Binder();
            $manual[$mInc]->setName('Manual ' . $mInc);
            $this->getEm()->persist($manual[$mInc]);

            // make first level binders
            for ($fInc = 1; $fInc <= 3; $fInc++) {
                $fKey = $mInc.'-'.$fInc;
                $firstLevel[$fKey] = new Binder();
                $firstLevel[$fKey]->setName('First Level Binder '.$fInc);
                $firstLevel[$fKey]->setParent($manual[$mInc]);
                $this->getEm()->persist($firstLevel[$fKey]);

                // make second level binders
                for ($sInc = 1; $sInc <= 3; $sInc++) {
                    $sKey = $fKey.'-'.$sInc;
                    $secondLevel[$sKey] = new Binder();
                    $secondLevel[$sKey]->setName('Second Level Binder '.$sInc);
                    $secondLevel[$sKey]->setParent($firstLevel[$fKey]);
                    $this->getEm()->persist($secondLevel[$sKey]);

                    // make third level binders
                    for ($tInc = 1; $tInc <= 3; $tInc++) {
                        $tKey = $sKey . '-' . $tInc;
                        $thirdLevel[$tKey] = new Binder();
                        $thirdLevel[$tKey]->setName('Third Level Binder '.$tInc);
                        $thirdLevel[$tKey]->setParent($secondLevel[$sKey]);
                        $this->getEm()->persist($thirdLevel[$tKey]);
                    }

                }
            }
        }
        $this->getEm()->flush();


        return $this->displayMessage('tree created');
    }

    /**
     * @Route("/listtree")
     */
    public function listTreeAction()
    {
        /* $binders = $this->getEm()->getRepository('AppBundle:Binder')->getTree();

        foreach ($binders as $binder) {
            for ($i = 0; $i <= $binder->getLevel(); $i++) {
                echo "&nbsp;&nbsp;&nbsp;";
            }
            echo $binder->getName() . "<br />";
        }*/

        $binder = $this->getEm()->find('AppBundle:Binder', 108);

        $path = $this->getEm()->getRepository('AppBundle:Binder')->getChildren($binder);
        foreach ($path as $p) {
            for ($i = 0; $i <= $p->getLevel(); $i++) {
                echo "&nbsp;&nbsp;&nbsp;";
            }
            echo $p->getName() . '<br />';
        }

        return $this->displayMessage();
    }

    public function walkTree(Binder $binder, $indent = 0) {


        echo $binder->getName() . "<br />";

        $indent++;
        foreach ($binder->getChildren() as $child) {
            $this->walkTree($child, $indent);
        }

        return;
    }

    private function displayMessage($message = '')
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
