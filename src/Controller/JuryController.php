<?php
// src/Controller/CoreController.php
namespace App\Controller ;

use App\Form\NotesType ;
use App\Form\EquipesType ;
use App\Entity\Equipes ;
use App\Entity\Eleves ;
use App\Entity\Edition ;
use App\Entity\Totalequipes ;
use App\Entity\Jures ;
use App\Entity\Notes ;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request ;
use Symfony\Component\HttpFoundation\Response ;



class JuryController extends AbstractController
{
    /**
    * @Route("cyberjury/accueil", name="cyberjury_accueil")
    */
    public function accueil(){
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
      /*  $repositoryMemoires = $this->getDoctrine()
                                   ->getManager()
                                   ->getRepository('App:Memoires');
       */
        $listEquipes=array();
        $progression=array();
        //$memoires=array();
        foreach ($attrib as $key => $value) {
            $equipe=$repositoryEquipes->findOneByLettre($key);
            $listEquipes[$key] = $equipe;
            $id = $equipe->getId();
            $note=$repositoryNotes->EquipeDejaNotee($id_jure ,$id);
            $progression[$key] = (!is_null($note)) ? 1 : 0 ;
            $idadm=$equipe->getInfoequipe();
            /*$memoire=$repositoryMemoires->findByEquipe($idadm);
            if($memoire !=[]) {
                if($memoire[0]->getAnnexe() == false) {
                    $memoires[$key] = $memoire[0];                  
                }
                else {
                    $memoires[$key] = $memoire[1];
                }
            }
            */
        }
        usort($listEquipes, function($a, $b) 
            {
            return $a->getOrdre() <=> $b->getOrdre();
            });
        /*$content = $this->renderView('cyberjury/accueil.html.twig', 
                array('listEquipes' => $listEquipes,'progression'=>$progression,'jure'=>$jure,'memoires'=>$memoires)
                );
         * 
         */
        $content = $this->renderView('cyberjury/accueil.html.twig', 
                array('listEquipes' => $listEquipes,'progression'=>$progression,'jure'=>$jure,'memoires'=>'')
                );
        return new Response($content);  
    }
        
    /**
    * @Security("is_granted('ROLE_JURY')")
    *
    * @Route( "/infos_equipe/{id}", name ="cyberjury_infos_equipe",requirements={"id_equipe"="\d{1}|\d{2}"}) 
    */
    public function infos_equipe(Request $request, $id)
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
        $eq= $repositoryEquipes->findOneById($id);
        $lettre=$eq->getLettre();

        $repositoryEquipesadmin = $this
                                        ->getDoctrine()
                                        ->getManager()
                                        ->getRepository('App:Equipesadmin');
        $equipe=$repositoryEquipesadmin->findOneByLettre($lettre);
        $idprof1=$equipe->getIdProf1();
        $idprof2=$equipe->getIdProf2();
        $repositoryUser= $this
                                        ->getDoctrine()
                                        ->getManager()
                                        ->getRepository('App:User');
        $prof1=$repositoryUser->findById($idprof1);

        $prof2=$repositoryUser->findById($idprof2);
      
        //dd($prof1,$prof2);
        $repositoryEleves = $this
                                ->getDoctrine()
                                ->getManager()
                                ->getRepository('App:Eleves');
        $listEleves=$repositoryEleves->findByLettreEquipe($lettre);
        
      /*  $repositoryMemoires = $this->getDoctrine()
                                   ->getManager()
                                   ->getRepository('App:Memoires');
        
       * 
       */
        $rneid=$equipe->getRneid();
        $repositoryRne= $this
                                        ->getDoctrine()
                                        ->getManager()
                                        ->getRepository('App:Rne');
        $lycee=$repositoryRne->findOneById($rneid);
        //A reprendre avec les nouvelles données d'Alain
        /*$memoire=$repositoryMemoires->findByEquipe($id);
        $memoires='';
        if($memoire !=[]) { 
            if($memoire[0]->getAnnexe() == false) {
                $memoires = $memoire[0];              
            }
            else  {
                $memoires = $memoire[1];              
            }
        }
         * 
         */

        $content = $this->renderView('cyberjury/infos.html.twig',
                array(
                        'equipe'=>$equipe, 
                        'eq'=>$eq,
                        'lycee'=>$lycee,
                        'listEleves'=>$listEleves, 
                        'id_equipe'=>$id,
                        'progression'=>$progression,
                        'jure'=>$jure,
                        'memoires'=>'',
                        'prof1'=>$prof1,
                        'prof2'=>$prof2,
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

        foreach ($ListPremPrix as $equipe) {
                $niveau = '1er'; 
                $equipe->setClassement($niveau);
                $rang = $rang + 1 ; 			
                $equipe->setRang($rang);
                $em->persist($equipe);
                $em->flush();
        }

        foreach ($ListDeuxPrix as $equipe)  {
                $niveau = '2ème';
                $equipe->setClassement($niveau);
                $rang = $rang + 1 ; 			
                $equipe->setRang($rang);
                $em->persist($equipe);
                $em->flush();
        }
        foreach ($ListTroisPrix as $equipe) {
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
    /*    $repositoryMemoires = $this->getDoctrine()
                                   ->getManager()
                                   ->getRepository('App:Memoires');
        $idadm=$equipe->getInfoequipe();
        $memoire=$repositoryMemoires->findByEquipe($idadm);
        $memoires='';
        if($memoire !=[])
                    {   if($memoire[0]->getAnnexe() == false)
                            {$memoires = $memoire[0];}
                        else 
                            {$memoires = $memoire[1];}
                    }
     * 
     */
                    
        $flag=0; 

        if(is_null($notes)){	
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
        else {
            $notes=$this->getDoctrine()
                        ->getManager()
                        ->getRepository('App:Notes')
                        ->EquipeDejaNotee($id_jure,$id); 
            $progression = 1; 

            if($attrib[$lettre]==1){
                 $form = $this->createForm(NotesType::class, $notes, array('EST_PasEncoreNotee'=> false, 'EST_Lecteur'=> true,));
                    $flag=1;
            }
            else {
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
				'jure'=>$jure,
                                'memoires'=>''
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

        $repositoryNotes = $this->getDoctrine()
                                ->getManager()
                                ->getRepository('App:Notes')
                                ;
        $em=$this->getDoctrine()->getManager();

        $MonClassement = $repositoryNotes->MonClassement($id_jure);

        $repositoryEquipes = $this->getDoctrine()
                                    ->getManager()
                                    ->getRepository('App:Equipes')
                                    ;
        $em=$this->getDoctrine()->getManager();

        $listEquipes = array();
        $j=1;
        foreach($MonClassement as $notes) {
            $id = $notes->getEquipe();
            $equipe = $repositoryEquipes->find($id);
            $listEquipes[$j]['id']= $equipe->getId();
            $listEquipes[$j]['infoequipe']= $equipe->getInfoequipe();
            $listEquipes[$j]['lettre']=$equipe->getLettre();
            $listEquipes[$j]['titre']=$equipe->getTitreProjet();
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
        $repositoryJure = $this->getDoctrine()
                                ->getManager()
                                ->getRepository('App:Jures');
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
        $repositoryEquipes = $this->getDoctrine()
                                  ->getManager()
                                  ->getRepository('App:Equipes');
       /* $repositoryMemoires = $this->getDoctrine()
                                   ->getManager()
                                   ->getRepository('App:Memoires');
        $idadm=$equipe->getInfoequipe();
        $memoire=$repositoryMemoires->findByEquipe($idadm);
        $memoires='';
        if($memoire !=[]) {   
            if($memoire[0]->getAnnexe() == false)  {
                $memoires = $memoire[0];
                
            }
            else {
                $memoires = $memoire[1];
                
            }
        }
        * 
        */
        $em=$this->getDoctrine()->getManager();
        $form = $this->createForm(EquipesType::class, $equipe, array('Attrib_Phrases'=> true, 'Attrib_Cadeaux'=> false));
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->persist($equipe);
            $em->flush();
            $request -> getSession()->getFlashBag()->add('notice', 'Phrase et prix amusants bien enregistrés');
            return $this->redirectToroute('cyberjury_accueil');
            }
        $content = $this->renderView('cyberjury\phrases.html.twig', 
                            array(
                                    'equipe'=>$equipe,
                                    'form'=>$form->createView(),
                                    'progression'=>$progression,
                                    'jure'=>$jure,
                                    'memoires'=>''
                                      ));
        return new Response($content);
    }           
}
