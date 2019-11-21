<?php
// src/Controller/CoreController.php
namespace App\Controller ;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 

use Symfony\Component\Form\AbstractType;

use App\Form\NotesType ;
use App\Form\PhrasesType ;
use App\Form\EquipesType ;
use App\Form\JuresType ;
use App\Form\CadeauxType ;
use App\Form\ClassementType ;
use App\Form\PrixType ;
use App\Form\EditionType;
use App\Form\MemoiresType;
use App\Form\MemoiresinterType;
use App\Form\ConfirmType;


use App\Entity\Equipes ;
use App\Entity\Eleves ;
use App\Entity\Edition ;
use App\Entity\Totalequipes ;
use App\Entity\Jures ;
use App\Entity\Notes ;
use App\Entity\Pamares;
use App\Entity\Visites ;
use App\Entity\Phrases ;
use App\Entity\Classement ;
use App\Entity\Prix ;
use App\Entity\Cadeaux ;
use App\Entity\Liaison ;
use App\Entity\Memoires;
use App\Entity\Memoiresinter;
use App\Entity\Equipesadmin;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextaeraType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Howtomakeaturn\PDFInfo\PDFInfo;
use Orbitale\Component\ImageMagick\Command;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request ;
use Symfony\Component\HttpFoundation\RedirectResponse ;
use Symfony\Component\HttpFoundation\Response ;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SwiftmailerBundle\Swiftmailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\RichText\Run;

class JuryController extends AbstractController
{
    /**
     * @Route("cyberjury/accueil", name="cyberjury_accueil")
     */
	public function accueil()
 
        {
            	$user=$this->getUser();
		$nom=$user->getUsername();

		$repositoryJure = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Jures')
			;

		$jure=$repositoryJure->findOneByNomJure($nom);
		$id_jure = $jure->getId();
                
 		$attrib = $jure->getAttributions();
                
		$repositoryEquipes = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Equipes')
			;
                $repositoryNotes = $this->getDoctrine()
		->getManager()
		->getRepository('App:Notes')
		;

		$listEquipes=array();
                $progression=array();
                
 		foreach ($attrib as $key => $value) 
		{
			$equipe=$repositoryEquipes->findOneByLettre($key);
			$listEquipes[$key] = $equipe;
			$id = $equipe->getId();
                        $note=$repositoryNotes->EquipeDejaNotee($id_jure ,$id);
			$progression[$key] = (!is_null($note)) ? 1 : 0 ;

		}
                usort($listEquipes, function($a, $b) {
                return $a->getOrdre() <=> $b->getOrdre();
                });
             
                $content = $this->renderView('cyberjury/accueil.html.twig', 
			array('listEquipes' => $listEquipes,'progression'=>$progression,'jure'=>$jure)
			);
                
  
                
		return new Response($content);

   
        }
        
        /**
	* @Security("is_granted('ROLE_JURY')")
        *
        * @Route( "/infos_equipe/{id}", name ="cyberjury_infos_equipe",requirements={"id_equipe"="\d{1}|\d{2}"}) 
	*/
	public function infos_equipe(Request $request, Equipes $equipe, $id)
	{
		$user=$this->getUser();
		$nom=$user->getUsername();

		$repositoryJure = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Jures')
			;

		$jure=$repositoryJure->findOneByNomJure($nom);
		$id_jure = $jure->getId();

		$note=$repositoryNotes = $this->getDoctrine()
		->getManager()
		->getRepository('App:Notes')
		->EquipeDejaNotee($id_jure,$id)
		;
		$progression = (!is_null($note)) ? 1 : 0 ;

		$repositoryEquipes = $this
		->getDoctrine()
		->getManager()
		->getRepository('App:Equipes');

		$lettre=$equipe->getLettre();

		$repositoryTotEquipes = $this
		->getDoctrine()
		->getManager()
		->getRepository('App:Totalequipes');

		$equipe=$repositoryTotEquipes->findOneByLettreEquipe($lettre);
		$eq=$repositoryEquipes->findOneByLettre($lettre);

		$repositoryEleves = $this
		->getDoctrine()
		->getManager()
		->getRepository('App:Eleves');

		$listEleves=$repositoryEleves->findByLettreEquipe($lettre);

		$content = $this->renderView('cyberjury/infos.html.twig',
			array(
				'equipe'=>$equipe, 
				'eq'=>$eq,
				'listEleves'=>$listEleves, 
				'id_equipe'=>$id,
				'progression'=>$progression,
				'jure'=>$jure
				)
			);
		return new Response($content);   
        }
        
 	/**
	* @Security("is_granted('ROLE_JURY')")
         * 
         * @Route("/lescadeaux", name="cyberjury_lescadeaux")
         * 
	*/
	public function lescadeaux(Request $request)
	{
            $user=$this->getUser();
            $nom=$user->getUsername();

            $repositoryJure = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Jures')
			;

            $jure=$repositoryJure->findOneByNomJure($nom);
            $id_jure = $jure->getId();

            $repositoryCadeaux = $this->getDoctrine()
                                       ->getManager()
                                       ->getRepository('App:Cadeaux');
            $ListCadeaux  = $repositoryCadeaux ->getListCadeaux();

            $content = $this->renderView('cyberjury/lescadeaux.html.twig',
			array('ListCadeaux' => $ListCadeaux,
                                'jure'=>$jure)
 			);
	return new Response($content);
	}       
         
 	/**
	* @Security("is_granted('ROLE_JURY')")
         * 
         * @Route("/lesprix", name="cyberjury_lesprix")
         * 
	*/
	public function lesprix(Request $request)
	{
            $user=$this->getUser();
            $nom=$user->getUsername();

            $repositoryJure = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Jures')
			;

            $jure=$repositoryJure->findOneByNomJure($nom);
            $id_jure = $jure->getId();
                $repositoryPrix = $this->getDoctrine()
		->getManager()
		->getRepository('App:Prix');
                

		
		$ListPremPrix = $repositoryPrix->findByClassement('1er');
		$ListDeuxPrix = $repositoryPrix->findByClassement('2ème');
		$ListTroisPrix = $repositoryPrix->findByClassement('3ème');

                $repositoryJure = $this
                        ->getDoctrine()
			->getManager()
			->getRepository('App:Jures')
			;

		$jure=$repositoryJure->findOneByNomJure($nom);
		$id_jure = $jure->getId();

		$content = $this->renderView('cyberjury/lesprix.html.twig',
			array('ListPremPrix' => $ListPremPrix, 
                              'ListDeuxPrix' => $ListDeuxPrix, 
                              'ListTroisPrix' => $ListTroisPrix,
                              'jure'=>$jure)
			);
		return new Response($content);
	}   
        
 /**
	* @Security("is_granted('ROLE_JURY')")
         * 
         * @Route("palmares", name="cyberjury_palmares")
         * 
	*/
	public function palmares(Request $request)
	{
            $user=$this->getUser();
            $nom=$user->getUsername();

            $repositoryJure = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Jures')
			;

            $jure=$repositoryJure->findOneByNomJure($nom);
            $id_jure = $jure->getId();
		$repositoryEquipes = $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipes');
		$em=$this->getDoctrine()->getManager();

		$repositoryClassement = $this->getDoctrine()
		->getManager()
		->getRepository('App:Classement');
		
		$NbrePremierPrix=$repositoryClassement
			->findOneByNiveau('1er')
			->getNbreprix(); 

		$NbreDeuxPrix = $repositoryClassement
			->findOneByNiveau('2ème')
			->getNbreprix(); 

		$NbreTroisPrix = $repositoryClassement
			->findOneByNiveau('3ème')
			->getNbreprix(); 

		$ListPremPrix = $repositoryEquipes->palmares(1,0, $NbrePremierPrix); // classement par rang croissant 
		$offset = $NbrePremierPrix  ; 
		$ListDeuxPrix = $repositoryEquipes->palmares(2, $offset, $NbreDeuxPrix);
		$offset = $offset + $NbreDeuxPrix  ; 
		$ListTroisPrix = $repositoryEquipes->palmares(3, $offset, $NbreTroisPrix);

		$rang=0; 

		foreach ($ListPremPrix as $equipe) 
		{
			$niveau = '1er'; 
			$equipe->setClassement($niveau);
			$rang = $rang + 1 ; 			
			$equipe->setRang($rang);
			$em->persist($equipe);
			$em->flush();
		}

		foreach ($ListDeuxPrix as $equipe) 
		{
			$niveau = '2ème';
			$equipe->setClassement($niveau);
			$rang = $rang + 1 ; 			
			$equipe->setRang($rang);
			$em->persist($equipe);
			$em->flush();
		}
		foreach ($ListTroisPrix as $equipe) 
		{
			$niveau = '3ème';
			$equipe->setClassement($niveau);
			$rang = $rang + 1 ; 			
			$equipe->setRang($rang);
			$em->persist($equipe);
			$em->flush();
		}

		$content = $this->renderView('cyberjury/palmares.html.twig',
			array('ListPremPrix' => $ListPremPrix, 
			      'ListDeuxPrix' => $ListDeuxPrix,
			      'ListTroisPrix' => $ListTroisPrix,
			      'NbrePremierPrix' => $NbrePremierPrix, 
			      'NbreDeuxPrix' => $NbreDeuxPrix, 
			      'NbreTroisPrix' => $NbreTroisPrix,
                              'jure'=>$jure)
			);
		return new Response($content);
	}       
        
        /**
        * 
	* @Security("is_granted('ROLE_JURY')")
        *
        * @Route("/evaluer_une_equipe/{id}", name="cyberjury_evaluer_une_equipe", requirements={"id_equipe"="\d{1}|\d{2}"})
        *   
	*/
  	public function evaluer_une_equipe(Request $request, Equipes $equipe, $id)
	{
		$user=$this->getUser();
		$nom=$user->getUsername();
		$repositoryEquipes = $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipes');

                $lettre=$equipe->getLettre();

		$repositoryJures = $this->getDoctrine()
		->getManager()
		->getRepository('App:Jures');
                $jure=$repositoryJures->findOneByNomJure($nom);
		$id_jure = $jure->getId();
		$attrib = $jure->getAttributions();   
		
		$em=$this->getDoctrine()->getManager();

		$notes = $repositoryNotes = $this->getDoctrine()
		->getManager()
		->getRepository('App:Notes')
		->EquipeDejaNotee($id_jure, $id);

		$flag=0; 

		if(is_null($notes))
		{	
			$notes = new Notes();			
			$notes->setEquipe($equipe);
			$notes->setJure($jure);
			$progression = 0; 

			if($attrib[$lettre]==1)
			{	
       			$form = $this->createForm(NotesType::class, $notes, array('EST_PasEncoreNotee'=> true, 'EST_Lecteur'=> true,));
				$flag=1;
			}
			else
			{
				$notes->setEcrit(0);
       			$form = $this->createForm(NotesType::class, $notes, array('EST_PasEncoreNotee'=> true, 'EST_Lecteur'=> false,));
			}
		}
		else
		{
			$notes=$this->getDoctrine()
			->getManager()
			->getRepository('App:Notes')
			->EquipeDejaNotee($id_jure,$id); 
			$progression = 1; 

			if($attrib[$lettre]==1)
			{
       			$form = $this->createForm(NotesType::class, $notes, array('EST_PasEncoreNotee'=> false, 'EST_Lecteur'=> true,));
				$flag=1;
			}
			else
			{
			$notes->setEcrit('0');
       		$form = $this->createForm(NotesType::class, $notes, array('EST_PasEncoreNotee'=> false, 'EST_Lecteur'=> false,));
			}
		}

		// Si la requête est en post, c'est que le visiteur a soumis le formulaire. 
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			// création et gestion du formulaire. 

			$em->persist($notes);
			$em->flush();
			$request -> getSession()->getFlashBag()->add('notice', 'Notes bien enregistrées');
			// puis on redirige vers la page de visualisation de cette note dans le tableau de bord
			return $this->redirectToroute('cyberjury_tableau_de_bord');
		}
		// Si on n'est pas en POST, alors on affiche le formulaire. 

		$content = $this->renderView('cyberjury/evaluer.html.twig', 
			array(
				'equipe'=>$equipe,
				'form'=>$form->createView(),
				'flag'=>$flag,
				'progression'=>$progression,
				'jure'=>$jure
				  ));
		return new Response($content);
		
	}
      
        /**
	 * @Security("is_granted('ROLE_JURY')")
         * 
         * @Route("/tableau_de_bord", name ="cyberjury_tableau_de_bord")
         * 
	*/
	public function tableau(Request $request)
	{
		$user=$this->getUser();
		$nom=$user->getUsername();

		$repositoryJure = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Jures')
			;

		$jure=$repositoryJure->findOneByNomJure($nom);
		$id_jure = $jure->getId();

		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('App:Notes')
		;
		$em=$this->getDoctrine()->getManager();

		$MonClassement = $repository->MonClassement($id_jure);
		
		$repository = $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipes')
		;
		$em=$this->getDoctrine()->getManager();

		$listEquipes = array();
		$j=1;
		foreach($MonClassement as $notes)
		{
			$id = $notes->getEquipe();
			$equipe = $repository->find($id);
			$listEquipes[$j]['id']= $equipe->getId();
			$listEquipes[$j]['lettre']=$equipe->getLettre();
			$listEquipes[$j]['titre']=$equipe->getTitreProjet();
                        $listEquipes[$j]['isef']=$equipe->getIsef();
			$listEquipes[$j]['exper']=$notes->getExper();
			$listEquipes[$j]['demarche']=$notes->getDemarche();
			$listEquipes[$j]['oral']=$notes->getOral();
			$listEquipes[$j]['origin']=$notes->getOrigin();
			$listEquipes[$j]['wgroupe']=$notes->getWgroupe();
			$listEquipes[$j]['ecrit']=$notes->getEcrit();
			$listEquipes[$j]['points']=$notes->getPoints();
			$j++;
		}

		$content = $this->renderView('cyberjury/tableau.html.twig', 
			array('listEquipes'=>$listEquipes,'jure'=>$jure)
			);
		return new Response($content);
	}
        
        /**
         * 
	 * @Security("is_granted('ROLE_JURY')")
         * 
         * 
         * @Route("/phrases_amusantes/{id}", name = "cyberjury_phrases_amusantes",requirements={"id_equipe"="\d{1}|\d{2}"})
	 */
	public function phrases(Request $request, Equipes $equipe, $id)
	{

		$user=$this->getUser();
		$nom=$user->getUsername();

		$repositoryJure = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Jures')
			;

		$jure=$repositoryJure->findOneByNomJure($nom);
		$id_jure = $jure->getId();

		$notes = $repositoryNotes = $this->getDoctrine()
		->getManager()
		->getRepository('App:Notes')
		->EquipeDejaNotee($id_jure, $id);
		$progression = (!is_null($notes)) ? 1 : 0 ;

		$repositoryPhrases = $this->getDoctrine()
		->getManager()
		->getRepository('App:Phrases');

		$repositoryLiaison = $this->getDoctrine()
		->getManager()
		->getRepository('App:Liaison');

		$repositoryEquipes = $this
		->getDoctrine()
		->getManager()
		->getRepository('App:Equipes');

		$em=$this->getDoctrine()->getManager();

		$form = $this->createForm(EquipesType::class, $equipe, array('Attrib_Phrases'=> true, 'Attrib_Cadeaux'=> false));
	
		// Si la requête est en post, c'est que le visiteur a soumis le formulaire. 
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			// création et gestion du formulaire. 
			
			$em->persist($equipe);
			$em->flush();
			$request -> getSession()->getFlashBag()->add('notice', 'Phrase et prix amusants bien enregistrés');
			
			return $this->redirectToroute('cyberjury_accueil');
		}
		// Si on n'est pas en POST, alors on affiche le formulaire. 

		$content = $this->renderView('cyberjury\phrases.html.twig', 
			array(
				'equipe'=>$equipe,
				'form'=>$form->createView(),
				'progression'=>$progression,
				'jure'=>$jure
				  ));
		return new Response($content);
        }    
        
         /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatjury/charge_eleves", name="secretariatjury_charge_eleves")
         * 
         */
	public function charge_eleves(Request $request)
	{ 

            $defaultData = ['message' => 'Charger le fichier Éleves'];
            $form = $this->createFormBuilder($defaultData)
                            ->add('fichier',      FileType::class)
                            ->add('Envoyer',      SubmitType::class)
                            ->getForm();
            
            
            $form->handleRequest($request);                            
            if ($form->isSubmitted() && $form->isValid()) 
                {
                $data=$form->getData();
                $fichier=$data['fichier'];
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fichier);
                $worksheet = $spreadsheet->getActiveSheet();
            
                $highestRow = $worksheet->getHighestRow();              
 
                $em = $this->getDoctrine()->getManager();
                 
                for ($row = 1; $row <= $highestRow; ++$row) 
                   { 
                    $eleve=new eleves();
                    $value = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $eleve->setNom($value);
                    $value = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    $eleve->setPrenom($value);
                    $value = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    $eleve->setClasse($value);
                    $value = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                    $eleve->setLettreEquipe($value);
 
                    $em->persist($eleve);
                    }                     
                    $em->flush();
                    return $this->redirectToRoute('secretariatjury_accueil');
                }
                $content = $this
                        ->renderView('secretariatjury\uploadexcel.html.twig', array('form'=>$form->createView(),));
                return new Response($content); 
        }
        /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatjury/attrib_cadeaux/{id_equipe}", name="secretariatjury_attrib_cadeaux",  requirements={"id_equipe"="\d{1}|\d{2}"}))
         * 
	*/
	public function attrib_cadeaux(Request $request, $id_equipe)
	{		
		$repositoryEquipes = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Equipes')
			;
		$equipe = $repositoryEquipes->find($id_equipe);

		$repositoryCadeaux = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Cadeaux')
			;
		$cadeau = $equipe->getCadeau();

		if(is_null($cadeau))
		{
                    $flag = 0; 
                    $form = $this->createForm(EquipesType::class, $equipe, 
			array(
				'Attrib_Phrases'=> false, 
				'Attrib_Cadeaux'=> true, 
				'Deja_Attrib'=>false,
				));
                    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) 
			{
			
			$em=$this->getDoctrine()->getManager();
			$em->persist($equipe);
			$cadeau = $equipe->getCadeau();
			$cadeau->setAttribue(1);
			$em->persist($cadeau);
			$em->flush();

			$request -> getSession()->getFlashBag()->add('notice', 'Notes bien enregistrées');
			return $this->redirectToroute('secretariat_attrib_cadeaux', array('id_equipe'=>$id_equipe));	
			}
                    $content = $this->renderView('secretariat/attrib_cadeaux.html.twig', 
			array(
				'equipe'=>$equipe,
				'form'=>$form->createView(),
				'attribue'=> $flag,
				  ));

                    return new Response($content);
                }

		else
		{
                    $flag = 1; 
                    $em=$this->getDoctrine()->getManager();

                    $form = $this->createForm(EquipesType::class, $equipe, 
			array(
				'Attrib_Phrases'=> false, 
				'Attrib_Cadeaux'=> true, 
				'Deja_Attrib'=>true,
				));
		
                    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) 
			{
			$em->persist($cadeau);
	
			if($cadeau->getAttribue())
                            {
                            $em->persist($equipe);
				
                            }	
			else
                            {
                            $equipe->setCadeau(NULL);
                            $em->persist($equipe);	
                            }	
			$em->flush();
                        $request -> getSession()->getFlashBag()->add('notice', 'Notes bien enregistrées');
			return $this->redirectToroute('secretariat_attrib_cadeaux', array('id_equipe'=>$id_equipe));	
			}

                    $content = $this->renderView('secretariat/attrib_cadeaux.html.twig', 
			array(
				'equipe'=>$equipe,
				'form'=>$form->createView(),
				'attribue'=> $flag,
				  ));

                    return new Response($content);
		}

	}
        	public function charge_equipe2(Request $request)
	{ 

            $defaultData = ['message' => 'Charger le fichier Équipe2'];
            $form = $this->createFormBuilder($defaultData)
                            ->add('fichier',      FileType::class)
                            ->add('Envoyer',      SubmitType::class)
                            ->getForm();
            
            $repositoryTotEquipes = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Totalequipes');
            
            $form->handleRequest($request);                            
            if ($form->isSubmitted() && $form->isValid()) 
                {
                $data=$form->getData();
                $fichier=$data['fichier'];
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fichier);
                $worksheet = $spreadsheet->getActiveSheet();
            
                $highestRow = $worksheet->getHighestRow();              
 
                $em = $this->getDoctrine()->getManager();
                 
                for ($row = 1; $row <= $highestRow; ++$row) 
                   {                       
                   $equipe= new equipes(); 
                   $value = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                   $lettre=$value;
                   $equipe->setLettre($lettre) ;
                   $info=$repositoryTotEquipes->findOneByLettreEquipe($lettre);
        	   $equipe->setInfoequipe($info);
                   
                   $nomEq=$repositoryTotEquipes->getTotEquipesNom($lettre);
                   if($nomEq)
                       {
                       $nomEquipe=$nomEq[0]['nomEquipe'];
                       }
                   $equipe->setTitreProjet($nomEquipe);
                   
                   $value = $worksheet->getCellByColumnAndRow(2, $row)->getValue(); 
                   $equipe->setOrdre($value) ;
                   $value = $worksheet->getCellByColumnAndRow(3, $row)->getValue(); 
                   $equipe->setHeure($value) ;
                   $value = $worksheet->getCellByColumnAndRow(4, $row)->getValue(); 
                   $equipe->setSalle($value) ;
                   $value = $worksheet->getCellByColumnAndRow(5, $row)->getValue(); 
                   $equipe->setIsef($value) ;
                   
                   $em->persist($equipe);

                    }
                    $em->flush();
                    return $this->redirectToRoute('secretariatjury_accueil');
                }
        $content = $this
                        ->renderView('secretariatjury\uploadexcel.html.twig', array('form'=>$form->createView(),));
	return new Response($content);          
        }
         /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatjury/charge_equipe1", name="secretariatjury_charge_equipe1")
         * 
         */
	public function charge_equipe1(Request $request)
	{ 

            $defaultData = ['message' => 'Charger le fichier Équipe'];
            $form = $this->createFormBuilder($defaultData)
                            ->add('fichier',      FileType::class)
                            ->add('Envoyer',      SubmitType::class)
                            ->getForm();
            $form->handleRequest($request);                            
            if ($form->isSubmitted() && $form->isValid()) 
                {
                $data=$form->getData();
                $fichier=$data['fichier'];
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fichier);
                $worksheet = $spreadsheet->getActiveSheet();
            
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
                
 
                $em = $this->getDoctrine()->getManager();
                $lettres = range('A', 'Z');
                $row=1;
               foreach ($lettres as $lettre)
                   {                       
                   $equipe= new totalequipes(); 
                   $value = $worksheet->getCellByColumnAndRow(1, $row)->getValue(); 
                   $equipe->setNumeroEquipe($value) ; 
                   $value = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                   $equipe->setLettreEquipe($value) ;
                   $value = $worksheet->getCellByColumnAndRow(3, $row)->getValue(); 
                   $equipe->setNomEquipe($value) ;
                   $value = $worksheet->getCellByColumnAndRow(4, $row)->getValue(); 
                   $equipe->setNomLycee($value) ;
                   $value = $worksheet->getCellByColumnAndRow(5, $row)->getValue(); 
                   $equipe->setDenominationLycee($value) ;
                   $value = $worksheet->getCellByColumnAndRow(6, $row)->getValue(); 
                   $equipe->setLyceeLocalite($value) ;
                   $value = $worksheet->getCellByColumnAndRow(7, $row)->getValue(); 
                   $equipe->setLyceeAcademie($value) ; 
                   $value = $worksheet->getCellByColumnAndRow(8, $row)->getValue(); 
                   $equipe->setPrenomProf1($value) ; 
                   $value = $worksheet->getCellByColumnAndRow(9, $row)->getValue(); 
                   $equipe->setNomProf1($value) ; 
                   $value = $worksheet->getCellByColumnAndRow(10, $row)->getValue(); 
                   $equipe->setPrenomProf2($value) ; 
                   $value = $worksheet->getCellByColumnAndRow(11, $row)->getValue(); 
                   $equipe->setNomProf2($value) ; 
                   
                   $em->persist($equipe);

                   $row +=1;
                    }
                    $em->flush();

                  return $this->redirectToRoute('secretariatjury_accueil');
            }
        $content = $this
                        ->renderView('secretariatjury\uploadexcel.html.twig', array('form'=>$form->createView(),));
	return new Response($content);          
        }
         /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatjury/charge_jures", name="secretariatjury_charge_jures")
         * 
         */
	public function charge_jures(Request $request)
	{ 

            $defaultData = ['message' => 'Charger le fichier Jures'];
            $form = $this->createFormBuilder($defaultData)
                            ->add('fichier',      FileType::class)
                            ->add('Envoyer',      SubmitType::class)
                            ->getForm();
            
            
            $form->handleRequest($request);                            
            if ($form->isSubmitted() && $form->isValid()) 
                {
                $data=$form->getData();
                $fichier=$data['fichier'];
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fichier);
                $worksheet = $spreadsheet->getActiveSheet();
            
                $highestRow = $worksheet->getHighestRow();              
 
                $em = $this->getDoctrine()->getManager();
                $lettres = range('A','Z') ;
                for ($row = 1; $row <= $highestRow; ++$row) 
                   { 
                    $jure = new jures();   
                    $value = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $jure->setPrenomJure($value);
                    $value = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    $jure->setNomJure($value);
                    $value = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    $jure->setInitialesJure($value);
                    $colonne = 4;
                    foreach ($lettres as $lettre)
                        {
                        $value = $worksheet->getCellByColumnAndRow($colonne, $row)->getValue();

                        $method ='set'.$lettre;
                        $jure->$method($value);
 
                        $colonne +=1;
                        }                    
                    $em->persist($jure);
                    }                     
                    $em->flush();
                    return $this->redirectToRoute('secretariatjury_accueil');
                }
                $content = $this
                        ->renderView('secretariatjury\uploadexcel.html.twig', array('form'=>$form->createView(),));
                return new Response($content);
        }
	/**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatjury/vueglobale", name="secretariatjury_vueglobale")
         * 
         */
	public function vueglobale(Request $request)
	{
		$repositoryNotes = $this
		->getDoctrine()
		->getManager()
		->getRepository('App:Notes')
		;

		$repositoryJures = $this
		->getDoctrine()
		->getManager()
		->getRepository('App:Jures')
		;
		$listJures = $repositoryJures->findAll();
		
		$repositoryEquipes = $this
		->getDoctrine()
		->getManager()
		->getRepository('App:Equipes')
		;
		$listEquipes = $repositoryEquipes->findAll();

		$nbre_equipes = 0; 
		foreach ($listEquipes as $equipe)
		{
			$nbre_equipes = $nbre_equipes + 1 ; 
			$id_equipe = $equipe->getId(); 
			$lettre = $equipe->getLettre(); 

			$nbre_jures=0; 
			foreach ($listJures as $jure) 
			{	
				$id_jure = $jure->getId();
				$nbre_jures=$nbre_jures+1; 	

				$method = 'get'.ucfirst($lettre); 
				$statut = $jure->$method();
			
				if(is_null($statut))
				{
					$progression[$nbre_equipes][$nbre_jures] = 'ras' ;

				}
				elseif ($statut==1) 
				{
			        $note = $repositoryNotes->EquipeDejaNotee($id_jure, $id_equipe);
					$progression[$nbre_equipes][$nbre_jures] = (is_null($note)) ? 'zero' : $note->getSousTotal() ;
				}
				else
				{
			        $note = $repositoryNotes->EquipeDejaNotee($id_jure, $id_equipe);
					$progression[$nbre_equipes][$nbre_jures] = (is_null($note)) ? 'zero' : $note->getPoints() ;
				}	
			}
                }

		$content = $this->renderView('secretariatjury/vueglobale.html.twig', array(
			'listJures'=>$listJures, 
			'listEquipes'=>$listEquipes,
			'progression'=>$progression, 
			'nbre_equipes'=>$nbre_equipes, 
			'nbre_jures'=>$nbre_jures,
			));
		return new Response($content);
	}
	
	/**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatjury/classement", name="secretariatjury_classement")
         * 
	*/	
	public function classement(Request $request)
	{

		$repositoryEquipes = $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipes');

		$repositoryNotes = $this->getDoctrine()
		->getManager()
		->getRepository('App:Notes');

		$em=$this->getDoctrine()->getManager();
		$listEquipes = $repositoryEquipes->findAll();

		foreach ($listEquipes as $equipe)
		{
			$listesNotes=$equipe->getNotess();
			$nbre_notes = $equipe->getNbNotes(); 

			$nbre_notes_ecrit=0; 			
			$points_ecrit = 0 ; 		
			$points = 0 ; 
			
			
			if ($nbre_notes==0) 
				{
					$equipe->setTotal(0);
					$em->persist($equipe);
					$em->flush();
				}	
			else
			{
				foreach ($listesNotes as $note) 
				{
					$points = $points + $note->getPoints(); 
					
					$nbre_notes_ecrit = ($note->getEcrit()) ? $nbre_notes_ecrit +1 : $nbre_notes_ecrit ; 
					$points_ecrit = $points_ecrit + $note->getEcrit()*5; 
				}
				$moyenne_oral = $points/$nbre_notes; 
				$moyenne_ecrit = ($nbre_notes_ecrit) ? $points_ecrit/$nbre_notes_ecrit : 0 ;

				$total =  $moyenne_oral + $moyenne_ecrit ; 
				$equipe->setTotal($total);
				$em->persist($equipe);
				$em->flush();
			}
		}


		$qb = $repositoryEquipes->createQueryBuilder('e');
		$qb ->select('COUNT(e)') ;
		$nbre_equipes = $qb->getQuery()->getSingleScalarResult(); 
		
		$classement = $repositoryEquipes->classement(0,0, $nbre_equipes);
                
		$rang=0; 
		
		foreach ($classement as $equipe) 
		{
			$rang = $rang + 1 ; 
			$equipe->setRang($rang);
			$em->persist($equipe);	
		}
                $em->flush();

		$content = $this->renderView('secretariatjury/classement.html.twig', 
			array('classement' => $classement )
			);
		return new Response($content);
	}
        /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatjury/edition_cadeaux", name="secretariat_edition_cadeaux")
         * 
	*/
	public function edition_cadeaux(Request $request)
	{
		$listEquipes = $this->getDoctrine()
			->getManager()
			->getRepository('App:Equipes')
			->getEquipesCadeaux();

		$content = $this->renderView('secretariatjury/edition_cadeaux2.html.twig', array('listEquipes' => $listEquipes));
		return new Response($content);
	}
	
	/**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariat/edition_phrases", name="secretariat_edition_phrases")
         * 
	*/
	public function edition_phrases(Request $request)
	{
		$listEquipes = $this->getDoctrine()
			->getManager()
			->getRepository('App:Equipes')
			->getEquipesPhrases();

		$content = $this->renderView('secretariatjury/edition_phrases.html.twig', array('listEquipes' => $listEquipes));
		return new Response($content);
	}
        /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatjury/edition_maj", name="secretariatjury_edition_maj")
         * 
         */
	public function edition_maj(Request $request)
	{ 
        $repositoryEdition = $this->getDoctrine()
                                ->getManager()
                                ->getRepository('App:Edition');
        $ed = $repositoryEdition->findOneByEd('ed');     
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(EditionType::class, $ed);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) 
            {
            $em->persist($ed);
            $em->flush();
            return $this->redirectToroute('secretariatjury_accueil');
            }
        $content = $this->renderView('secretariatjury\edition_maj.html.twig', array('form'=>$form->createView(),));
	return new Response($content);          
        }
        /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatjury/edition_visites", name="secretariatjury_edition_visites")
         * 
	*/
	public function edition_visites(Request $request)
	{
		//$user=$this->getUser();
		$listEquipes = $this->getDoctrine()
			->getManager()
			->getRepository('App:Equipes')
			->getEquipesVisites();
		$content = $this->renderView('secretariatjury/edition_visites.html.twig', array('listEquipes' => $listEquipes));
		return new Response($content);
	}
        /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
        * 
         * @Route("/secretariatjury/modifier_prix/{id_prix}", name="secretariatjury_modifier_prix", requirements={"id_prix"="\d{1}|\d{2}"}))
	*/
	public function modifier_prix(Request $request, $id_prix)
	{
		$repositoryPrix = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Prix');

		$repositoryClassement = $this->getDoctrine()
		->getManager()
		->getRepository('App:Classement');

		$prix = $repositoryPrix->find($id_prix); 
		$em=$this->getDoctrine()->getManager();

		$form = $this->createForm(PrixType::class, $prix);
				
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid() ) 
		{
			$em->persist($prix);			
			$em->flush();

			$classement = $repositoryClassement->findOneByNiveau('1er');
			$nbrePremPrix = $repositoryPrix->getNbrePrix('1er');
			$classement->setNbreprix($nbrePremPrix); 
			$em->persist($classement);			

			$classement = $repositoryClassement->findOneByNiveau('2ème');			
			$nbreDeuxPrix = $repositoryPrix->getNbrePrix('2ème');
			$classement->setNbreprix($nbreDeuxPrix); 
			$em->persist($classement);			


			$classement = $repositoryClassement->findOneByNiveau('3ème');			
			$nbreTroisPrix = $repositoryPrix->getNbrePrix('3ème');
			$classement->setNbreprix($nbreTroisPrix); 
			$em->persist($classement);			
			$em->flush();


			$request -> getSession()->getFlashBag()->add('notice', 'Modifications bien enregistrées');
			return $this->redirectToroute('secretariat_lesprix');

		}
		$content = $this->renderView('secretariat/modifier_prix.html.twig', 
			array(
				'prix'=>$prix,
				'form'=>$form->createView(),
				  ));
		return new Response($content);
	}
       
	
	/**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatjury/palmares_definitif", name="secretariatjury_palmares_definitif")
         * 
	*/
	public function palmares_definitif(Request $request)
	{
		$repositoryEquipes = $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipes');
		$em=$this->getDoctrine()->getManager();

		$repositoryClassement = $this->getDoctrine()
		->getManager()
		->getRepository('App:Classement');
		
		$NbrePremierPrix=$repositoryClassement
			->findOneByNiveau('1er')
			->getNbreprix(); 

		$NbreDeuxPrix = $repositoryClassement
			->findOneByNiveau('2ème')
			->getNbreprix(); 

		$NbreTroisPrix = $repositoryClassement
			->findOneByNiveau('3ème')
			->getNbreprix(); 

		$ListPremPrix = $repositoryEquipes->palmares(1,0, $NbrePremierPrix);
		$offset = $NbrePremierPrix  ; 
		$ListDeuxPrix = $repositoryEquipes->palmares(2, $offset, $NbreDeuxPrix);
		$offset = $offset + $NbreDeuxPrix  ; 
		$ListTroisPrix = $repositoryEquipes->palmares(3, $offset, $NbreTroisPrix);

		$rang=0; 

		foreach ($ListPremPrix as $equipe) 
		{
			$niveau = '1er'; 
			$equipe->setClassement($niveau);
			$rang = $rang + 1 ; 			
			$equipe->setRang($rang);
			$em->persist($equipe);
		}

		foreach ($ListDeuxPrix as $equipe) 
		{
			$niveau = '2ème';
			$equipe->setClassement($niveau);
			$rang = $rang + 1 ; 			
			$equipe->setRang($rang);
			$em->persist($equipe);
		}
		foreach ($ListTroisPrix as $equipe) 
		{
			$niveau = '3ème';
			$equipe->setClassement($niveau);
			$rang = $rang + 1 ; 			
			$equipe->setRang($rang);
			$em->persist($equipe);
			
		}
                $em->flush();
		$content = $this->renderView('secretariat/palmares_definitif.html.twig',
			array('ListPremPrix' => $ListPremPrix, 
			      'ListDeuxPrix' => $ListDeuxPrix,
			      'ListTroisPrix' => $ListTroisPrix,
			      'NbrePremierPrix' => $NbrePremierPrix, 
			      'NbreDeuxPrix' => $NbreDeuxPrix, 
			      'NbreTroisPrix' => $NbreTroisPrix)
			);
		return new Response($content);
	}
        /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatjury/mise_a_zero", name="secretariatjury_mise_a_zero")
         * 
	*/
	public function RaZ(Request $request)
	{
		$repositoryEquipes = $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipes');

		$repositoryPalmares = $this->getDoctrine()
		->getManager()
		->getRepository('App:Palmares');

		$prix = $repositoryPalmares->findOneByCategorie('prix');
                $em=$this->getDoctrine()->getManager();

		$ListeEquipes = $repositoryEquipes->findAll();
		
		foreach ($ListeEquipes as $equipe)
    		{
            	$equipe->setPrix(null);
		$em->persist($equipe);
    		}

		foreach (range('A','Z') as $i)
    		{
                    $method = 'set'.ucfirst($i);
                    if (method_exists($prix, $method))
                        {
                            $prix = $prix->$method(null);
                            $em->persist($prix);
        		} 
        	}
		$em->flush();    
                $content = $this->renderView('secretariatjury/RaZ.html.twig');

		return new Response($content);
	}
        /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatjury/excel_jury", name="secretariatjury_tableau_excel_palmares_jury")
         * 
	*/
	public function tableau_excel_palmares_jury(Request $request)
	{
		$repositoryEquipes = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Equipes')
			;
                
                $nbreEquipes = $repositoryEquipes
			->createQueryBuilder('e')
                        ->select('COUNT(e)') 
		 	->getQuery()
		 	->getSingleScalarResult(); 
                
		$listEquipes = $this->getDoctrine()
			->getManager()
			->getRepository('App:Equipes')
			->getEquipesPalmaresJury();

		$repositoryEleves = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Eleves');

		foreach ($listEquipes as $equipe) 
		{
			$lettre=$equipe->getLettre();
			$lesEleves[$lettre] = $repositoryEleves->findByLettreEquipe($lettre);
		}
		$spreadsheet = new Spreadsheet();
                $spreadsheet->getProperties()
                        ->setCreator("Olymphys")
                        ->setLastModifiedBy("Olymphys")
                        ->setTitle("Palmarès de la 26ème édition - Février 2019")
                        ->setSubject("Palmarès")
                        ->setDescription("Palmarès avec Office 2005 XLSX, generated using PHP classes.")
                        ->setKeywords("office 2005 openxml php")
                        ->setCategory("Test result file");
                $spreadsheet->getActiveSheet()->getPageSetup()
                        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE); 
                $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');
                $spreadsheet->getDefaultStyle()->getFont()->setSize(6);
                $spreadsheet->getDefaultStyle()->getAlignment()->setWrapText(true);

                $sheet = $spreadsheet->getActiveSheet();
                
                $vcenterArray=[
                'vertical'     => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'textRotation' => 0,
                'wrapText'     => TRUE
                    ];
		$styleText=array('font'=>array(
                                 'bold'=>false,
                                 'size'=>14,
                                 'name'=>'Calibri',
                                    ),
                                    );
		$styleTitre=array('font'=>array(
                                'bold'=>true,
                                'size'=>16,
                                'name'=>'Calibri',
                                ),                  			
                  		);

                $ligne=1;
                foreach ($listEquipes as $equipe) 
                {
                    $lettre = $equipe->getLettre();

                    $sheet->setCellValue('A'.$ligne, 'Prix')
                          ->setCellValue('B'.$ligne, $equipe->getClassement());
                    if($equipe->getPrix()!==null)
                    {
                    $sheet ->setCellValue('C'.$ligne, $equipe->getPrix()->getPrix() );
                    }
                    $sheet->getStyle('A'.$ligne.':C'.$ligne)->getAlignment()->applyFromArray($vcenterArray);                           
        
                    $sheet->getStyle('A'.$ligne.':C'.$ligne)
                          ->applyFromArray($styleTitre) ;  
                    $sheet->getRowDimension($ligne)->setRowHeight(30);
                    $ligne = $ligne+1; 
                    $sheet->getRowDimension($ligne)->setRowHeight(30);
                    $sheet->mergeCells('A'.$ligne.':C'.$ligne);
                    if ($equipe->getPhrases() != null)
                        {
                        $sheet->setCellValue('A'.$ligne, $equipe->getPhrases()->getPhrase().' '.$equipe->getLiaison()->getLiaison().' '.$equipe->getPhrases()->getPrix());
                        }
                    $sheet->getStyle('A'.$ligne)->getAlignment()->applyFromArray($vcenterArray);
                    $sheet->getStyle('A'.$ligne.':C'.$ligne)
                          ->applyFromArray($styleText);            

                    $ligne = $ligne+1; 
                    $lignep = $ligne + 1; 
                    $sheet->getRowDimension($ligne)->setRowHeight(20);
                    $sheet->mergeCells('A'.$ligne.':A'.$lignep);
                    $sheet->setCellValue('A'.$ligne, 'Vous êtes l\'équipe')
                           ->setCellValue('B'.$ligne, $equipe->getLettre())
                           ->setCellValue('C'.$ligne, $equipe->getTitreProjet());
                    $sheet->getStyle('C'.$ligne)->getAlignment()->applyFromArray($vcenterArray);
                    $sheet->getStyle('A'.$ligne)->getAlignment()
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    $sheet->getStyle('A'.$ligne.':C'.$ligne)
                          ->applyFromArray($styleText); 
                    $ligne = $ligne+1; 
                    $sheet->getRowDimension($ligne)->setRowHeight(30);
                    $sheet->setCellValue('B'.$ligne, 'AC. '.$equipe->getInfoequipe()->getLyceeAcademie())
                          ->setCellValue('C'.$ligne, 'LYCÉE '.$equipe->getInfoequipe()->getnomLycee()."\n".$equipe->getInfoequipe()->getLyceeLocalite() );
 

                    $sheet->getStyle('B'.$ligne)->getAlignment()->applyFromArray($vcenterArray);
                    $sheet->getStyle('C'.$ligne)->getAlignment()->applyFromArray($vcenterArray);
                    $sheet->getStyle('A'.$ligne.':C'.$ligne)
                          ->applyFromArray($styleText);

                    $ligne = $ligne+1; 
                    $lignep = $ligne + 1;
                    $sheet->getRowDimension($ligne)->setRowHeight(40);
                    $sheet->mergeCells('A'.$ligne.':A'.$lignep);
                    $sheet->setCellValue('A'.$ligne, 'Nos partenaires vous offrent')
                          ->setCellValue('B'.$ligne, 'une visite de laboratoire : ');
                    if($equipe->getVisite()!==null)
                    {
                    $sheet->setCellValue('C'.$ligne, $equipe->getVisite()->getIntitule());
                    }
                    $sheet->getStyle('A'.$ligne.':C'.$ligne)->getAlignment()->applyFromArray($vcenterArray);
                    $sheet->getStyle('A'.$ligne)->getAlignment()
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $sheet->getStyle('A'.$ligne.':C'.$ligne)
                          ->applyFromArray($styleText);
                    
                    $ligne = $ligne+1; 
                    $sheet->getRowDimension($ligne)->setRowHeight(30);
                    $sheet->setCellValue('B'.$ligne, 'du matériel scientifique : ');
                    if ($equipe->getCadeau() !== null)
                        {
                        $sheet->setCellValue('C'.$ligne, $equipe->getCadeau()->getContenu().' offert par '.$equipe->getCadeau()->getFournisseur());
                         }
                         
                    $sheet->getStyle('B'.$ligne.':C'.$ligne)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('A'.$ligne.':C'.$ligne)
                          ->applyFromArray($styleText);
                    
                    $ligne = $ligne+2; 

                }
                $nblignes= 5*$nbreEquipes;
                
                $sheet->getColumnDimension('A')->setWidth(32);
		$sheet->getColumnDimension('B')->setWidth(40);
		$sheet->getColumnDimension('C')->setWidth(120);

                
		$spreadsheet->getActiveSheet()->getStyle('A1:C'.$nblignes)
                            ->getAlignment()->setWrapText(true);

                $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
                $spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);
                $spreadsheet->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
                $spreadsheet->getActiveSheet()->getPageSetup()->setVerticalCentered(true);
                
                $spreadsheet->getActiveSheet()->getHeaderFooter()->setOddFooter('RPage &P sur &N');
                
 
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="proclamation.xls"');
                header('Cache-Control: max-age=0');
        
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
                $writer->save('php://output');
        

	}
       /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatjury/excel_site", name="secretariatjury_tableau_excel_palmares_site")
         * 
	*/
	public function tableau_excel_palmares_site(Request $request)
	{
		$listEquipes = $this->getDoctrine()
			->getManager()
			->getRepository('App:Equipes')
			->getEquipesPalmares();
                
                $repositoryEquipes = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Equipes')
			;
                
                $nbreEquipes = $repositoryEquipes
			->createQueryBuilder('e')
                        ->select('COUNT(e)') 
		 	->getQuery()
		 	->getSingleScalarResult(); 

		$repositoryEleves = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Eleves');
                
                $repositoryEdition=$this
			->getDoctrine()
			->getManager()
			->getRepository('App:Edition');
                $ed=$repositoryEdition->findOneByEd('ed');
                $date=$ed->getDate();
                $result = $date->format('d/m/Y');
                $edition=$ed->getEdition();

		foreach ($listEquipes as $equipe) 
		{
			$lettre=$equipe->getLettre();
			$lesEleves[$lettre] = $repositoryEleves->findByLettreEquipe($lettre);
		}

                
		$spreadsheet = new Spreadsheet();
                $spreadsheet->getProperties()
                        ->setCreator("Olymphys")
                        ->setLastModifiedBy("Olymphys")
                        ->setTitle("Palmarès de la ".$edition."ème édition - ".$result)
                        ->setSubject("Palmarès")
                        ->setDescription("Palmarès avec Office 2005 XLSX, generated using PHP classes.")
                        ->setKeywords("office 2005 openxml php")
                        ->setCategory("Test result file");
 
                $sheet = $spreadsheet->getActiveSheet();
 
                $sheet->getPageSetup()
                      ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE) 
                      ->setFitToWidth(1)
                      ->setFitToHeight(0);
                   

 
                $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(6);

                $spreadsheet->getDefaultStyle()->getAlignment()->setWrapText(true);
                
                $borderArray = [
                        'borders' => [
                              'outline' => [
                                       'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['argb' => '00000000'],
                                            ],
                                     ],
                               ];
                $centerArray=[
                'horizontal'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                 'vertical'     => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'textRotation' => 0,
                'wrapText'     => TRUE
            ];  
                $vcenterArray=[
                'vertical'     => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'textRotation' => 0,
                'wrapText'     => TRUE
            ]; 
                $nblignes=$nbreEquipes*4 +3;
       
                $ligne=3;

                $sheet->setCellValue('A'.$ligne, 'Académie')
                    ->setCellValue('B'.$ligne, 'Lycée, sujet, élèves')
                    ->setCellValue('C'.$ligne, 'Professeurs')
                    ->mergeCells('D'.$ligne.':E'.$ligne)
                    ->setCellValue('D'.$ligne, 'Prix - Visite de laboratoire - Prix en matériel scientifique');
                $sheet->getStyle('A'.$ligne)->applyFromArray($borderArray);
                $sheet->getStyle('B'.$ligne)->applyFromArray($borderArray);
                $sheet->getStyle('C'.$ligne)->applyFromArray($borderArray);
                $sheet->getStyle('D'.$ligne)->applyFromArray($borderArray);
                $sheet->getStyle('E'.$ligne)->applyFromArray($borderArray);
                $sheet->getStyle('A'.$ligne.':D'.$ligne)->getAlignment()->applyFromArray($centerArray);
                $ligne +=1; 

        	foreach ($listEquipes as $equipe) 
                {
                    $lettre = $equipe->getLettre();

                    $ligne4 = $ligne + 3;
                    $sheet->mergeCells('A'.$ligne.':A'.$ligne4);
                    $sheet->setCellValue('A'.$ligne, strtoupper($equipe->getInfoequipe()->getLyceeAcademie()))
                        ->setCellValue('B'.$ligne,'LYCÉE'.' '.$equipe->getInfoequipe()->getnomLycee()." - ".$equipe->getInfoequipe()->getLyceeLocalite() )
                        ->setCellValue('C'.$ligne, $equipe->getInfoequipe()->getPrenomProf1().' '.strtoupper($equipe->getInfoequipe()->getnomProf1() ))
                        ->setCellValue('D'.$ligne, $equipe->getClassement().' '.'prix');
                     if($equipe->getPhrases()!==null)   
                    {$sheet->setCellValue('E'.$ligne, $equipe->getPhrases()->getPhrase().' '.$equipe->getLiaison()->getLiaison().' '.$equipe->getPhrases()->getPrix());}
                    else{$sheet->setCellValue('E'.$ligne,'Phrase');}
                    $sheet ->getStyle('A'.$ligne)->getFont() ->setSize(7)->setBold(2);
                    $sheet->getStyle('A'.$ligne.':A'.$ligne4)->applyFromArray($borderArray);
                    $sheet->getStyle('C'.$ligne)->getAlignment()->applyFromArray($centerArray);
                    $sheet->getStyle('D'.$ligne.':E'.$ligne)->getAlignment()->applyFromArray($vcenterArray);
                    $sheet->getStyle('A'.$ligne.':A'.$ligne4)->getAlignment()->applyFromArray($centerArray);
                    $sheet->getStyle('A'.$ligne.':E'.$ligne)->getFont()->getColor()->setRGB('000099');
                    
                    $lignes=$ligne+3;
                    $sheet->getStyle('D'.$ligne.':E'.$lignes)->applyFromArray($borderArray);
                    $sheet->getStyle('C'.$ligne.':C'.$lignes)->applyFromArray($borderArray);
                    
                    if ($equipe->getClassement()=='1er') 
                    {
                        $sheet->getStyle('D'.$ligne.':E'.$ligne)->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('ffccff');
                    }
                    elseif ($equipe->getClassement()=='2ème') 
                    {
                        $sheet->getStyle('D'.$ligne.':E'.$ligne)->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('99ffcc');
                    }
                    else
                    {
                        $sheet->getStyle('D'.$ligne.':E'.$ligne)->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('ccff99');
                    }
                    
                    $ligne = $ligne+1; 
  
                    $ligne3 = $ligne + 1; 
                    $sheet->mergeCells('B'.$ligne.':B'.$ligne3);
                    $sheet->setCellValue('B'.$ligne, $equipe->getTitreProjet())
                        ->setCellValue('C'.$ligne, $equipe->getInfoequipe()->getPrenomProf2().' '.strtoupper($equipe->getInfoequipe()->getnomProf2() ));
                     if($equipe->getPrix()!==null)
                        {$sheet->setCellValue('E'.$ligne, $equipe->getPrix()->getPrix());
                        }
                    $sheet->getStyle('B'.$ligne.':B'.$ligne3)->applyFromArray($borderArray);
                    $sheet->getStyle('B'.$ligne)->getAlignment()->applyFromArray($centerArray);
                    $sheet->getStyle('B'.$ligne.':B'.$ligne3)->getFont()->setBold(2)->getColor()->setRGB('ff0000');
                    $sheet->getStyle('C'.$ligne)->getFont()->getColor()->setRGB('000099');
                    $sheet->getStyle('C'.$ligne)->getAlignment()->applyFromArray($centerArray);
                    $sheet->getStyle('D'.$ligne.':E'.$ligne)->getAlignment()->applyFromArray($vcenterArray);

                    
                    
                    if ($equipe->getClassement()=='1er') 
                    {
                        $sheet->setCellValue('D'.$ligne, PRIX::PREMIER.'€');
                    }
                    elseif ($equipe->getClassement()=='2ème') 
                    {
                        $sheet->setCellValue('D'.$ligne, PRIX::DEUXIEME.'€' );
                    }
                    else
                    {
                        $sheet->setCellValue('D'.$ligne, PRIX::TROISIEME.'€');
                    }
                    $sheet->getStyle('D'.$ligne)->getAlignment()->applyFromArray($vcenterArray);
                    
                    
		
                    $ligne = $ligne+1; 
                    $sheet->setCellValue('D'.$ligne, 'Visite :');
                    if( $equipe->getVisite()!==null)
                    {$sheet->setCellValue('E'.$ligne,$equipe->getVisite()->getIntitule());
                    }
                    $sheet->getStyle('D'.$ligne.':E'.$ligne)->getAlignment()->applyFromArray($vcenterArray);


                    $ligne = $ligne+1; 
                    $sheet->mergeCells('D'.$ligne.':E'.$ligne);
                    if($equipe->getCadeau()!==null)
                    {
                    $sheet->setCellValue('D'.$ligne, $equipe->getCadeau()->getContenu().' offert par '.$equipe->getCadeau()->getFournisseur());
                    }
                    $sheet->getStyle('D'.$ligne.':E'.$ligne)->getAlignment()->applyFromArray($vcenterArray);
                    
                    $listeleves='';
                    $nbre = count($lesEleves[$lettre]);
                    $eleves = $lesEleves[$lettre];

                    for ($i=0; $i <= $nbre-1 ; $i++) 
                    { 
			$eleve = $eleves[$i];
			$prenom=$eleve->getPrenom();
			$nom=strtoupper($eleve->getNom());
                        if ($i<$nbre-1)
                            {$listeleves.=$prenom.' '.$nom.', ';}
                        else 
                        {$listeleves.=$prenom.' '.$nom;}
                    }

                    $sheet->setCellValue('B'.$ligne, $listeleves );
                    $sheet->getStyle('B'.$ligne)->applyFromArray($borderArray);
                    $sheet->getStyle('B'.$ligne)->getAlignment()->applyFromArray($vcenterArray);
                    $sheet->getStyle('B'.$ligne)->getFont()->getColor()->setRGB('000099');

                    $ligne = $ligne+1; 
                }

		$sheet->getColumnDimension('A')->setWidth(15);
		$sheet->getColumnDimension('B')->setWidth(80);
		$sheet->getColumnDimension('C')->setWidth(25);
		$sheet->getColumnDimension('D')->setWidth(15);
		$sheet->getColumnDimension('E')->setWidth(110);
		$spreadsheet->getActiveSheet()->getStyle('A1:F'.$nblignes)
                            ->getAlignment()->setWrapText(true);
 

 
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="palmares.xls"');
                header('Cache-Control: max-age=0');
        
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
                $writer->save('php://output');
        

	}
        /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatjury/palmares_complet", name="secretariatjury_edition_palmares_complet")
         * 
	*/
	public function tableau_palmares_complet(Request $request)
	{
		$listEquipes = $this->getDoctrine()
			->getManager()
			->getRepository('App:Equipes')
			->getEquipesPalmares();

		$repositoryEleves = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Eleves')
			;

		foreach ($listEquipes as $equipe) 
		{
			$lettre=$equipe->getLettre();
			$lesEleves[$lettre] = $repositoryEleves->findByLettreEquipe($lettre);
		}

		$content = $this->renderView('secretariatjury/edition_palmares_complet.html.twig', 
			array('listEquipes' => $listEquipes,
			      'lesEleves'=>$lesEleves));
		return new Response($content);

	}
        /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatjury/modifier_rang/{id_equipe}", name="secretariatjury_modifier_rang", requirements={"id_equipe"="\d{1}|\d{2}"}))
         * 
	*/
	public function modifier_rang(Request $request, $id_equipe)
	{
		$repositoryEquipes = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Equipes')
			;
		$equipe = $repositoryEquipes->find($id_equipe); 
		$em=$this->getDoctrine()->getManager();

		$form = $this->createForm(EquipesType::class, $equipe, 
			array(
				'Modifier_Rang'=>true,
				'Attrib_Phrases'=> false, 
				'Attrib_Cadeaux'=> false, 
				'Deja_Attrib'=>false,)
				);
		$ancien_rang = $equipe->getRang();		
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid() ) 
                    {
                    $nouveau_rang = $equipe->getRang();
                    $max=0;
                    $mod=0;
                    if ($nouveau_rang < $ancien_rang)
                        {
                        $deb = $nouveau_rang-1;
                        $max = $ancien_rang-$nouveau_rang;
                        $mod = 1;
                        }
                    elseif($ancien_rang < $nouveau_rang)
                        {                            
                        $deb = $ancien_rang;
                        $max= $nouveau_rang-$deb;
                        $mod= -1;                                                      
                        } 
                    elseif($ancien_rang == $nouveau_rang)
                        {
                        $deb = $ancien_rang;
                        $max= 0;
                        $mod=0;
                        }
                        
                    $qb = $repositoryEquipes->createQueryBuilder('e');
                    $qb ->orderBy('e.rang', 'ASC')
                        ->setFirstResult( $deb )
                         ->setMaxResults( $max );
                    $list = $qb ->getQuery()->getResult();

                    foreach($list as $eq)
                        {
                        $rang= $eq->getRang();
                        $eq ->setRang($rang+$mod);                      
                        }
                    $em->persist($equipe);			
                    $em->flush();
                    $request -> getSession()->getFlashBag()->add('notice', 'Modifications bien enregistrées');
                    return $this->redirectToroute('secretariat_palmares_ajuste');

                    }
		$content = $this->renderView('secretariat/modifier_rang.html.twig', 
			array(
				'equipe'=>$equipe,
				'form'=>$form->createView(),
				  ));
		return new Response($content);
	}

	
	/**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatjury/palmares_ajuste", name="secretariatjury_palmares_ajuste")
         * 
	*/
	public function palmares_ajuste(Request $request)
	{
		$repositoryEquipes = $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipes');

		$qb = $repositoryEquipes->createQueryBuilder('e');
		$qb ->select('COUNT(e)') ;
		$nbre_equipes = $qb->getQuery()->getSingleScalarResult(); 

		$repositoryClassement = $this->getDoctrine()
		->getManager()
		->getRepository('App:Classement');

		
		$NbrePremierPrix=$repositoryClassement
			->findOneByNiveau('1er')
			->getNbreprix(); 

		$NbreDeuxPrix = $repositoryClassement
			->findOneByNiveau('2ème')
			->getNbreprix(); 

		$NbreTroisPrix = $repositoryClassement
			->findOneByNiveau('3ème')
			->getNbreprix(); 
		
		$ListPremPrix = $repositoryEquipes->palmares(1,0, $NbrePremierPrix); 
		          
                $offset = $NbrePremierPrix  ;
		$ListDeuxPrix = $repositoryEquipes->palmares(2, $offset, $NbreDeuxPrix);
		 
                $offset = $offset + $NbreDeuxPrix  ;
                $ListTroisPrix = $repositoryEquipes->palmares(3, $offset, $NbreTroisPrix);

                $qb = $repositoryEquipes->createQueryBuilder('e');
                $qb ->orderBy('e.rang', 'ASC')
                    ->setFirstResult(  $NbrePremierPrix + $NbreDeuxPrix )
                    ->setMaxResults( $NbreTroisPrix );
                $ListTroisPrix = $qb ->getQuery()->getResult();		

		$content = $this->renderView('secretariat/palmares_ajuste.html.twig',
			array('ListPremPrix' => $ListPremPrix, 
			      'ListDeuxPrix' => $ListDeuxPrix,
			      'ListTroisPrix' => $ListTroisPrix,
			      'NbrePremierPrix' => $NbrePremierPrix, 
			      'NbreDeuxPrix' => $NbreDeuxPrix, 
			      'NbreTroisPrix' => $NbreTroisPrix)
			);
		return new Response($content);
	}
        /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatjury/accueil_jury", name="secretariatjury_accueil_jury")
         * 
	*/
	public function accueilJury(Request $request)
	{
		$repositoryEquipes = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Equipes');

		$repositoryEleves = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Eleves');

		$em=$this->getDoctrine()->getManager();

		$listEquipes=$repositoryEquipes->findAll();

		foreach ($listEquipes as $equipe) 
		{
			$lettre=$equipe->getLettre();
			$lesEleves[$lettre] = $repositoryEleves->findByLettreEquipe($lettre);
		}

		$content = $this->renderView('secretariat/accueil_jury.html.twig', 
			array('listEquipes' => $listEquipes,
				  'lesEleves'=>$lesEleves));

		return new Response($content);
	}
        /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatjury/edition_prix", name="secretariatjury_edition_prix")
         * 
	*/
	public function edition_prix(Request $request)
	{
		$listEquipes = $this->getDoctrine()
			->getManager()
			->getRepository('App:Equipes')
			->getEquipesPrix()
                        ;
		$content = $this->renderView('secretariatjury/edition_prix.html.twig', array('listEquipes' => $listEquipes));
		return new Response($content);
	}
        /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatjury/attrib_prix/{niveau}", name="secretariatjury_attrib_prix", requirements={"niveau"="\d{1}"}))
         * 
	*/
        public function attrib_prix(Request $request, $niveau)
	{
		switch ($niveau) 
		{
			case 1:
				$niveau_court = '1er'; 
				$niveau_long = 'premiers';
				break;
			
			case 2:
				$niveau_court = '2ème'; 
				$niveau_long = 'deuxièmes';
				break;
			case 3:
				$niveau_court = '3ème'; 
				$niveau_long = 'troisièmes';
				break;
		}
		$repositoryEquipes = $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipes');
		$repositoryClassement = $this->getDoctrine()
		->getManager()
		->getRepository('App:Classement');
		$repositoryPrix = $this->getDoctrine()
		->getManager()
		->getRepository('App:Prix');
		$repositoryPalmares = $this->getDoctrine()
		->getManager()
		->getRepository('App:Palmares');
                
                $ListEquipes = $repositoryEquipes->findByClassement($niveau_court); 
                
                $NbrePrix=$repositoryClassement
			->findOneByNiveau($niveau_court)
			->getNbreprix(); 
                
		$qb = $repositoryPrix->createQueryBuilder('p');
		$qb->where('p.classement=:niveau')
		    ->setParameter('niveau', $niveau_court);
                

                
                $listPrix=$repositoryPrix->findOneByClassement($niveau_court)->getPrix();
                $prix = $repositoryPalmares->findOneByCategorie('prix');
                $i=0;	
		foreach ($ListEquipes as $equipe) 
                {
                    $qb2[$i]= $repositoryPrix->createQueryBuilder('p')
                                             ->where('p.classement = :niveau')
                                             ->setParameter('niveau', $niveau_court);
                    $attribue=0;
                    $Prix_eq=$equipe->getPrix();
                    $intitule_prix='';
                    if ($Prix_eq !=null) 
                        {
                        $intitule_prix = $Prix_eq->getPrix();
                        $qb2[$i]->andwhere('p.id = :prix_sel')
                                ->setParameter('prix_sel', $Prix_eq->getId());                        
                        }
                    if (!$Prix_eq) 
                        {                                                
                        $qb2[$i]->andwhere('p.attribue = :attribue')
                                ->setParameter('attribue', $attribue);                    
                        }                           
                    $formBuilder[$i]=$this->get('form.factory')->createBuilder(FormType::class, $prix);
                    $lettre=strtoupper($equipe->getLettre());
                    $titre=$equipe->getTitreProjet();  
                    $titre_form[$i]=$lettre." : ".$titre.".  Prix :  ".$intitule_prix;
                    $formBuilder[$i]->add($lettre, EntityType::class, [
                                        'class' => 'App:Prix',
                                        'query_builder' => $qb2[$i],
                                        'choice_label'=> 'getPrix',
                                        'multiple' => false,
                                        'label' => $lettre." : ".$titre.".  Prix :  ".$intitule_prix]
                                     );
                    $formBuilder[$i]->add('Enregistrer', SubmitType::class);
                    $formBuilder[$i]->add('Effacer', SubmitType::class);
                              
                    $form[$i]=$formBuilder[$i]->getForm();
                    $formtab[$i]=$form[$i]->createView();
                   
                    if ($request->isMethod('POST') && $form[$i]->handleRequest($request)->isValid()) 
			{
                        $em=$this->getDoctrine()->getManager();
                        
			foreach (range('A','Z') as $lettre_equipe)
                            {
                            if (isset($form[$i][$lettre_equipe] ))
                                { 
                                $equipe = $repositoryEquipes->findOneByLettre($lettre_equipe);
                                if ($form[$i]->get('Enregistrer')->isClicked())
                                    {
                                        $method = 'get'.ucfirst($lettre_equipe);
                                        if (method_exists($prix, $method))
                                            {   
                                            $pprix = $prix->$method();
                                            $equipe->setPrix($pprix);
                                            $em->persist($equipe);
                                            $pprix->setAttribue(1);
                                            $em->persist($pprix);
                                            $em->flush();
                                            $request -> getSession()->getFlashBag()->add('notice', 'Prix bien enregistrés');
                                            return $this->redirectToroute('secretariat_attrib_prix', array('niveau'=> $niveau));
                                            }
                                    } 
                                                                        
                                if ($form[$i]->get('Effacer')->isClicked())
                                   {
                                        $method = 'get'.ucfirst($lettre_equipe);
                                        if (method_exists($prix, $method))
                                            {   
                                            $pprix = $prix->$method();
                                            $pprix->setAttribue(0);
                                            $em->persist($pprix);
                                            $equipe->setPrix(null);
                                            $em->persist($equipe);
                                            $em->flush();
                                            $request -> getSession()->getFlashBag()->add('notice', 'Prix bien effacé');
                                            return $this->redirectToroute('secretariat_attrib_prix', array('niveau'=> $niveau));                                                                         
                                            }        
                                    }                            
                               }
                            }
                        }
                        $i=$i+1;
                    }                                        
                    $content = $this->renderView('secretariat/attrib_prix.html.twig',
			array('ListEquipes' => $ListEquipes, 
                              'NbrePrix'=>$NbrePrix, 
                              'niveau'=>$niveau_long, 
                              'formtab'=>$formtab,
                              )
                                                               );
                    return new Response($content);      
	}

        
        
}
