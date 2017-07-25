<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Binder;
use AppBundle\Entity\Facility;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class DefaultController extends Controller
{

    /**
     * @Route("/soft")
     */
    public function soft()
    {

        /*$binder = new Binder();
        $binder->setName('Instruction Manual');

        $facility = new Facility();
        $facility->setName('Corporate');
        $facility->setShortName('CORP');
        $facility->setAddress('123 Fake St.');
        $facility->setCity('Denver');
        $facility->setState('CO');
        $facility->setZip('80210');
        $binder->addFacility($facility);
        $this->getEm()->persist($facility);

        $facility2 = new Facility();
        $facility2->setName('Golden Branch');
        $facility2->setShortName('GLDN');
        $facility2->setAddress('217 Araphoe St.');
        $facility2->setCity('Golden');
        $facility2->setState('CO');
        $facility2->setZip('80403');
        $binder->addFacility($facility2);
        $this->getEm()->persist($facility2);

        $this->getEm()->persist($binder);
        $this->getEm()->flush();*/

        /*$facility = $this->getEm()->find('AppBundle:Facility', 2);
        $this->getEm()->remove($facility);
        $this->getEm()->flush();*/


        //$binder = $this->getEm()->find('AppBundle:Binder', 1);

        $filter = $this->getEm()->getFilters()->getFilter('softdeleteable');
        $filter->disableForEntity('AppBundle\Entity\Facility');

        $binder = $this->getEm()->createQuery('SELECT b, f FROM AppBundle:Binder b JOIN b.facilities f')->getOneOrNullResult();

        $message = '';
        foreach ($binder->getFacilities() as $facility) {
            $message .= $facility->getName() . ' (' . $facility->getShortName() . ') <br />';
        }


        return $this->render('default/message.html.twig', ['message' => $message]);
    }

    /**
     * @Route("/admin/test")
     *
     * @return Response
     */
    public function adminAction()
    {
        var_dump($this->getUser());

        return $this->displayMessage('admin');
    }

    /**
     * @Route("/acl")
     */
    public function acl()
    {
        $binder = $this->getEm()->find('AppBundle:Binder', 1);

        // creating the ACL
        $aclProvider = $this->get('security.acl.provider');
        $objectIdentity = ObjectIdentity::fromDomainObject($binder);
        $acl = $aclProvider->createAcl($objectIdentity);

        // retrieving the security identity of the currently logged-in user
        $tokenStorage = $this->get('security.token_storage');
        $user = $tokenStorage->getToken()->getUser();
        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        // grant owner access
        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        $aclProvider->updateAcl($acl);
    }

    /**
     * @Route("/")
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('default/home.html.twig');
    }

    /**
     * @Route("/add")
     *
     * @return Response
     */
    public function addAction()
    {
        $binder = new Binder();
        $binder->setName('Test' . rand(1, 100));

        $this->getEm()->persist($binder);
        $this->getEm()->flush();

        return $this->displayMessage('Binder has been created');
    }

    /**
     * @Route("/addblank")
     *
     * @return Response
     */
    public function addBlankAction()
    {
        $binder = new Binder();
        $binder->setName('');

        // validate
        $validator = $this->get('validator');
        $errors = $validator->validate($binder);
        var_dump($errors);
        exit;

        $this->getEm()->persist($binder);
        $this->getEm()->flush();

        return $this->displayMessage('Binder has been created');
    }

    /**
     * @Route("/edit/{id}")
     *
     * @param  Binder   $binder
     * @return Response
     */
    public function editAction(Binder $binder)
    {
        $binder->setName('Test Edit ' . date('m/d/Y h:is: A'));

        $this->getEm()->persist($binder);
        $this->getEm()->flush();

        return $this->displayMessage('Binder has been edited');
    }

    /**
     * @Route("/remove/{id}")
     *
     * @param  Binder   $binder
     * @return Response
     */
    public function removeAction(Binder $binder)
    {
        $this->getEm()->remove($binder);
        $this->getEm()->flush();

        return $this->displayMessage('Binder has been removed');
    }

    /**
     * @Route("/list")
     *
     * @return Response
     */
    public function listAction()
    {
        $binders = $this->getEm()->getRepository('AppBundle:Binder')->findAll();

        $message = '';
        foreach ($binders as $binder) {
            $message .= $binder->getId() . ': ' . $binder->getName() . "<br />";
        }

        return $this->displayMessage($message);
    }

    /**
     * @Route("/show/{id}")
     *
     * @param  Binder   $binder
     * @return Response
     */
    public function showAction(Binder $binder)
    {
        $message = 'ID: '   . $binder->getId() . "<br />" .
                   'Name: ' . $binder->getName() . "<br />";

        return $this->displayMessage($message);
    }

    /**
     * @Route("/createtree")
     *
     * @return Response
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
         $binders = $this->getEm()->getRepository('AppBundle:Binder')->getTree();

        foreach ($binders as $binder) {
            for ($i = 0; $i <= $binder->getLevel(); $i++) {
                echo "&nbsp;&nbsp;&nbsp;";
            }
            echo $binder->getName() . "<br />";
        }

        /*
        $binder = $this->getEm()->find('AppBundle:Binder', 108);

        $path = $this->getEm()->getRepository('AppBundle:Binder')->getChildren($binder);
        foreach ($path as $p) {
            for ($i = 0; $i <= $p->getLevel(); $i++) {
                echo "&nbsp;&nbsp;&nbsp;";
            }
            echo $p->getName() . '<br />';
        }*/

        return $this->displayMessage();
    }

    /**
     * @Route("/move")
     */
    public function move()
    {
        $manual = $this->getEm()->find('AppBundle:Binder', 1);

        $binder = $this->getEm()->find('AppBundle:Binder', 14);

        $manual->setParent($binder);

        $this->getEm()->flush();
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
