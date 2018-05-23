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
/*use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Component\Form\FormRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;*/



class DefaultController extends Controller
{

    //Init la session
    public function _init($request)
    {
        try {

            if(!$this->container->get('session')->isStarted())
            {
                $session = new Session();
                $session->start();
            }
            
            $session = $this->get('session');
            $session->set('currentDate', date("Y-m-d H:i:s"));
            $session->set('marque', $request->query->get('marque'));
            $session->set('typeQds', "GIR");
            $session->set('mode', $request->query->get('mode'));
            $session->set('arrayModes', array("create","edit","consult"));

            $marque = $session->get('marque');
            if ($marque == "66-nord") {
                $session->set('nomMarque', "66°Nord");
            }
            else if ($marque == "atalante"){
                $session->set('nomMarque', "Atalante");
            }


            //Bouchons
            $session->set('numDos', "TESTNUMDOS98465");
            $session->set('idpackdate' , "132");
            $session->set('numPass', "TESTNUMPASS0193");
            $guidesTMP  = '{"2444":"Sarah SCAGLIONE","1989":"Julien BALDY"}';
            $session->set('guidesTMP', $guidesTMP);
            $cieTMP     = '{"0007":"MALASYA AIRLINE","0009":"AIR FRANCE"}';
            $session->set('cieTMP', $cieTMP);

        } catch (Exception $e) {
            return new Response("Error : " . $e->getMessage());
        }
    }


    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        date_default_timezone_set('Europe/Paris');
        /*if(!isset($session))
        {
            $session = $request->getSession();
        }

        $csrfGenerator = new UriSafeTokenGenerator();
        $csrfStorage = new SessionTokenStorage($session);
        $csrfManager = new CsrfTokenManager($csrfGenerator, $csrfStorage);

        $formFactory = Forms::createFormFactoryBuilder()
            // ...
            ->addExtension(new CsrfExtension($csrfManager))
            ->getFormFactory();

        $form = $formFactory->createBuilder()
            ->add('QSD', TextType::class)
            ->getForm();*/

        $this->_init($request);  

        try {
            $session        = $this->get('session');
            $typeQds        = $session->get('typeQds');
            $marque         = $session->get('marque');
            $mode           = $session->get('mode');
            $arrayModes     = $session->get('arrayModes');
                    
            $patternRepo    = $this->getDoctrine()->getRepository(Qds2Pattern::class);
            $stepRepo       = $this->getDoctrine()->getRepository(Qds2Step::class);

            //Manque le concept de marque
            $pattern        = $patternRepo->findOneBy(array('qdspattern' => $typeQds));
            $arrayStep      = array();
            $steps          = $stepRepo->findBy(array('idpattern' => $pattern->getIdpattern()));

            $session->set('qdspattern', $pattern);

            //récupérer les step automatiquement suivant le type de questionnaire
            foreach ($steps as $value) {
                $step = $this->_getStep($pattern , $value->getSteporder(), $mode);
                array_push($arrayStep, $step);
            }

            if(in_array($mode, $arrayModes))
            {
                if($mode == 'edit')
                {
                    if($marque == "66-nord")
                    {
                        return $this->render('@App/qds/nord/edition.html.twig', array('arrayStep' => $arrayStep));
                    }
                    else if ($marque == "atalante")
                    {
                       
                    }
                    else
                    {
                        return $this->render('@App/404.html.twig', array('arrayStep' => ""));
                    }
                }
                else if ($mode == 'create')
                {
                    if($marque == "66-nord")
                    {
                        
                        //A changer par la suite, avoir un template-qds.html.twig
                        return $this->render('@App/qds/nord/qds-nord.html.twig', 
                            array('arrayStep' => $arrayStep));
                    }
                    else if ($marque == "atalante")
                    {
                        //A changer par la suite, avoir un template-qds.html.twig
                        return $this->render('@App/qds/atalante/qds-atalante.html.twig', 
                            array('arrayStep' => $arrayStep));
                    }
                    else
                    {
                        return $this->render('@App/404.html.twig', array('errorMessage' => "marque inexistante"));
                    }
                }  
            }else
            {
                return $this->render('@App/404.html.twig', array('errorMessage' => "Mode inexistant : mode=create, mode=edit, mode=consult"));
            }

        } catch (Exception $e) {
            return new Response("Error : " . $e->getMessage());
        }
    }


    /*
    *   Function _getStep qui permet de récupérer sous form d'array les données d'un type de questionnaire (avant départ, pendant voyage, etc...)
    *   @param 
    *       $pattern    - pour récupe l'id du pattern, 
    *       $stepOrder  - le numéro/ordre du step à récupe
    *   @return array $arrayFinal  
    */
    public function _getStep($pattern , $stepOrder, $mode)
    {
        try {
         
            $session = $this->get('session');

            //variable
            $arrayBlockMultiple     = array();
            $arrayIncrementResponse = array();
            $arrayFinal             = array();
            $arrayblockTmp          = array();
            $arrayquestionTmp       = array();
            $idPattern              = $pattern->getIdpattern();
        
            //repository    
            $blockRepo              = $this->getDoctrine()->getRepository(Qds2Block::class);
            $stepRepo               = $this->getDoctrine()->getRepository(Qds2Step::class);
            $questionRepo           = $this->getDoctrine()->getRepository(Qds2Question::class);
            $reponseRepo            = $this->getDoctrine()->getRepository(Qds2::class);

            //je récupé le step voulu
            $step                   = $stepRepo->findOneBy(array('idpattern' => $idPattern, 'steporder' => $stepOrder));
            $idStep                 = $step->getIdstep();
            $titleStep              = $step->getSteptitle();
            $guidesTMP              = json_decode($session->get('guidesTMP'));
            $cieTMP                 = json_decode($session->get('cieTMP'));
            $numDosTMP              = $session->get('numDos');
            $numpassTMP             = $session->get('numPass');

            //je garde le titre du step de coté
            array_push($arrayFinal, $titleStep);

            //je récupe le block 
            $blocks         = $blockRepo->findBy(array('idstep' => $idStep), array('blockorder' => 'ASC'));

            if($mode == "edit")
            {
                $responseResult = $reponseRepo->findOneBy(array('numPas' => $numpassTMP, 'numDos' => $numDosTMP));
            }

            /*var_dump($blockRepo->findAll());
            die;*/
            //pour tous les blocks
            foreach ($blocks as $block) 
            {
                $arrayBlock                     = array();
                $idBlock                        = $block->getIdblock();
                $blockTitle                     = $block->getBlocktitle();
                $blockMultiple                  = $block->getBlockmultiple();
                $blockOrder                     = $block->getBlockorder();
                $blockIcon                      = $block->getBlockIcon();
                $arrayBlock['idBlock']          = $idBlock;
                $arrayBlock['blockTitle']       = $blockTitle;
                $arrayBlock['blockMultiple']    = $blockMultiple;
                $arrayBlock['blockOrder']       = $blockOrder;
                $arrayBlock['blockIcon']        = $blockIcon;

                if (strpos($blockTitle, '[nomMarque]') !== false) {
                    $arrayBlock['blockTitle'] = $this->replaceNomMarque($blockTitle);
                }

                //je cherche les questions du bloc
                $questions = $questionRepo->findBy(array('idblock' => $idBlock), array('questionorder' => 'ASC'));

                if($blockMultiple > 1)
                {
                    $nb = 1;

                    $blockTitleTMP = $blockTitle;

                    if(strpos($blockTitle, '[nomTourLeader]') !== false)
                    {
                        //pour chaque guide
                        foreach ($guidesTMP as $key => $value) {

                            $blockTitleTMP = str_replace("[nomTourLeader]", $value, $blockTitle);
                            $arrayBlock['guideID']    = $key;
                            $arrayBlock['blockTitle'] = $blockTitleTMP;

                            foreach ($questions as $question) {

                                $arrayQuestion                          = array();
                                $idQuestion                             = $question->getIdquestion();
                                $headerQuestion                         = $question->getQuestionheader();
                                $titleQuestion                          = $question->getQuestiontitle();
                                $typeQuestion                           = $question->getQuestiontype();
                                $choiceQuestion                         = $question->getQuestionchoice();
                                $mandatoryQuestion                      = $question->getQuestionmandatory();
                                $orderQuestion                          = $question->getQuestionorder();
                                $visibleQuestion                        = $question->getQuestionvisible();
                                $responseIDQuestion                     = $question->getResponseid();

                                $arrayQuestion['idQuestion']            = $idQuestion;
                                $arrayQuestion['headerQuestion']        = $headerQuestion;
                                $arrayQuestion['titleQuestion']         = $titleQuestion;
                                $arrayQuestion['typeQuestion']          = $typeQuestion;
                                $arrayQuestion['choiceQuestion']        = $choiceQuestion;
                                $arrayQuestion['mandatoryQuestion']     = $mandatoryQuestion;
                                $arrayQuestion['orderQuestion']         = $orderQuestion;
                                $arrayQuestion['visibleQuestion']       = $visibleQuestion;

                                $splitId = explode('_', $responseIDQuestion);
                                $idReponse = $splitId[0];

                                $arrayQuestion['responseIDQuestion']    = $idReponse . "_" . $nb;

                                if($mode == "edit")
                                {
                                    //je prefix avec "set"
                                    $functionName = "get".$idReponse . "" . $nb;
                                     

                                    //je regarde si la methode exist dans ma classe entity
                                    if(method_exists($responseResult,$functionName)) 
                                    {
                                        $arrayQuestion['response'] = $responseResult->$functionName();
                                    }else
                                    {
                                        $arrayQuestion['response'] = "";
                                    }
                                }

                                $arrayBlock[$idQuestion]    = $arrayQuestion;
                            }

                            $nb = $nb + 1;
                            array_push($arrayFinal, $arrayBlock);
                        }
                    }else if(strpos($blockTitle, '[nomCIE]') !== false)
                    {
                        //Si pas de compagnie aérienne 
                        if(sizeof($cieTMP) > 0){
                            foreach ($cieTMP as $key => $value) {

                                $blockTitleTMP = str_replace("[nomCIE]", $value, $blockTitle);
                                $arrayBlock['cieID']    = $key;
                                $arrayBlock['blockTitle'] = $blockTitleTMP;

                                foreach ($questions as $question) {

                                    $arrayQuestion                          = array();
                                    $idQuestion                             = $question->getIdquestion();
                                    $headerQuestion                         = $question->getQuestionheader();
                                    $titleQuestion                          = $question->getQuestiontitle();
                                    $typeQuestion                           = $question->getQuestiontype();
                                    $choiceQuestion                         = $question->getQuestionchoice();
                                    $mandatoryQuestion                      = $question->getQuestionmandatory();
                                    $orderQuestion                          = $question->getQuestionorder();
                                    $visibleQuestion                        = $question->getQuestionvisible();
                                    $responseIDQuestion                     = $question->getResponseid();

                                    $arrayQuestion['idQuestion']            = $idQuestion;
                                    $arrayQuestion['headerQuestion']        = $headerQuestion;
                                    $arrayQuestion['titleQuestion']         = $titleQuestion;
                                    $arrayQuestion['typeQuestion']          = $typeQuestion;
                                    $arrayQuestion['choiceQuestion']        = $choiceQuestion;
                                    $arrayQuestion['mandatoryQuestion']     = $mandatoryQuestion;
                                    $arrayQuestion['orderQuestion']         = $orderQuestion;
                                    $arrayQuestion['visibleQuestion']       = $visibleQuestion;

                                    $splitId = explode('_', $responseIDQuestion);
                                    $idReponse = $splitId[0];

                                    $arrayQuestion['responseIDQuestion']    = $idReponse . "_" . $nb;

                                    if($mode == "edit")
                                    {
                                        //je prefix avec "set"
                                        $functionName = "get".$idReponse . "" . $nb;

                                        //je regarde si la methode exist dans ma classe entity
                                        if(method_exists($responseResult,$functionName)) 
                                        {
                                            $arrayQuestion['response'] = $responseResult->$functionName();
                                        }else
                                        {
                                            $arrayQuestion['response'] = "";
                                        }
                                    }

                                    $arrayBlock[$idQuestion]    = $arrayQuestion;
                                }
                                
                                $nb = $nb + 1;
                                array_push($arrayFinal, $arrayBlock);
                            }
                        }
                    }
                }
                else
                {
                    foreach ($questions as $question) {
                        $arrayQuestion                          = array();
                        $idQuestion                             = $question->getIdquestion();
                        $headerQuestion                         = $question->getQuestionheader();
                        $titleQuestion                          = $question->getQuestiontitle();
                        $typeQuestion                           = $question->getQuestiontype();
                        $choiceQuestion                         = $question->getQuestionchoice();
                        $mandatoryQuestion                      = $question->getQuestionmandatory();
                        $orderQuestion                          = $question->getQuestionorder();
                        $visibleQuestion                        = $question->getQuestionvisible();
                        $responseIDQuestion                     = $question->getResponseid();

                        $arrayQuestion['idQuestion']            = $idQuestion;
                        $arrayQuestion['headerQuestion']        = $headerQuestion;
                        $arrayQuestion['titleQuestion']         = $titleQuestion;
                        $arrayQuestion['typeQuestion']          = $typeQuestion;
                        $arrayQuestion['choiceQuestion']        = $choiceQuestion;
                        $arrayQuestion['mandatoryQuestion']     = $mandatoryQuestion;
                        $arrayQuestion['orderQuestion']         = $orderQuestion;
                        $arrayQuestion['visibleQuestion']       = $visibleQuestion;
                        $arrayQuestion['responseIDQuestion']    = $responseIDQuestion;

                        if($mode == "edit")
                        {
                            //je prefix avec "set"
                            $functionName = "get".$responseIDQuestion;

                            //je regarde si la methode exist dans ma classe entity
                            if(method_exists($responseResult,$functionName)) 
                            {
                                $arrayQuestion['response'] = $responseResult->$functionName();
                                //var_dump($arrayQuestion['response']);
                            }else
                            {
                                $arrayQuestion['response'] = "";
                            }
                        }

                        if($typeQuestion == "QCM2")
                        {
                            $arrayQuestion['choiceQuestion']    = json_decode($choiceQuestion, true);
                        }
                        if($typeQuestion == "LIST")
                        {
                            //lancer la requête 
                        }

                        $arrayBlock[$idQuestion] = $arrayQuestion;
                    }

                    //je push le bloc dans le tableau final
                    array_push($arrayFinal, $arrayBlock);
                }
            }
            return $arrayFinal;
        } catch (Exception $e) {
            return new Response("Error : " . $e->getMessage());
        }
    }


    //function de sauvegarde du qds, récupère tous les requêtes http post
    //et ajoute chaque réponse en base
    public function saveFormAction(Request $request)
    {
        try {

            $em             = $this->getDoctrine()->getManager();
            //$qdsResponse    = $this->getDoctrine()->getRepository(Qds2::class);
            $oQds           = new Qds2();
            $date           = new \DateTime();

            $numdosTMP      = $session->get('numDos');
            $numpassTMP     = $session->get('numPass');
            $idpackdateTMP  = $session->get('idpackdate');

            $form           = $request->request->all();

            $oQds->setDtcreate($date);
            $oQds->setNumPas($numpassTMP); 
            $oQds->setNumDos($numdosTMP);
            $oQds->setIdPackDates($idpackdateTMP);

            //si y'a du POST
            if ($request->getMethod() == 'POST') {
                //pour tous les valeurs récupe 
                foreach ($form as $key => $value) {

                    //j'explode la key au niveau des trois underscores
                    $listValue = explode("___", $key);

                    //je récupe que l'id reponse
                    $responseID = $listValue[0];
                    
                    //si c'est un id guide
                    if(strpos($responseID, 'id_tl') !== false){
                        $responseID = str_replace('_', '', $listValue[0]);
                    }

                    //si c'est un id cie
                    if(strpos($responseID, 'cod_cie') !== false){
                        $responseID = str_replace('_', '', $listValue[0]);
                    }

                    //je prefix avec "set"
                    $functionName = "set".$responseID;

                    //je regarde si la methode exist dans ma classe entity
                    if(method_exists($oQds,$functionName)) 
                    {
                        //var_dump($functionName);
                        //elle existe alors je l'utilise pour setter la value
                        $oQds->$functionName($value); 
                        $em->persist($oQds);
                    }
                    else
                    {   
                        //sinon si la methode n'existe pas, je regarde si elle contient un underscore our les RXX_N
                        if(strpos($functionName, '_') !== false)
                        {
                            //je l'explode
                            $splitId = explode('_', $functionName);
                            //et je concatène pour ne plus avoir l'underscore mais avoir le nom de la methode
                            $functionName = $splitId[0] . $splitId[1];
                            if(method_exists($oQds,$functionName)) 
                            {
                                $oQds->$functionName($value);
                                $em->persist($oQds);
                            }

                        }
                    }
                }
                $em->flush();
                $em->clear();
            }

            return new Response("Success");
        } catch (Exception $e) {
            return new Response("Error : " . $e->getMessage());
        }
    }

    public function replaceNomMarque($title)
    {
        $realTitle = "";
        $session = $this->get('session');
        $nomMarque = $session->get('nomMarque');
        if (strpos($title, '[nomMarque]') !== false) {
            $realTitle = str_replace("[nomMarque]",$nomMarque,$title);
        }
        return $realTitle;
    }
}
