<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Qds2Pattern;
use AppBundle\Entity\Qds2Question;
use AppBundle\Entity\Qds2Step;
use AppBundle\Entity\Qds2Block;
use AppBundle\Entity\Qds2;
use AppBundle\Controller\Consultation;
use \Datetime;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;




class BackController extends Controller
{

	    /**
     * @Route("/", name="homepage")
     */
    public function nordAction(Request $request)
    {
        date_default_timezone_set('Europe/Paris');


        return $this->render('@App/back/index.html.twig', array('arrayStep' => ""));
    }

}