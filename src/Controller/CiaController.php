<?php
namespace App\Controller ;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 


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
use App\Form\MemoiresinterorgaciaType;
use App\Form\ConfirmType;
use App\Form\ListmemoiresinterType;

use App\Form\ListmemoiresinterallType;
use App\Form\FichessecurType;

use App\Entity\Equipes ;
use App\Entity\Eleves ;
use App\Entity\Elevesinter ;
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
use App\Entity\Fichessecur;
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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\Request ;
use Symfony\Component\HttpFoundation\RedirectResponse ;
use Symfony\Component\HttpFoundation\Response ;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
//use Symfony\Component\HttpFoundation\File\UploadedFile;
//use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\AbstractType;



use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ZipArchive;
    
class CiaController extends AbstractController
{
        /**
         * @Security("has_role('ROLE_JURYCIA')")
         * 
         * @Route("/cia/afficherlesmemoiresinter", name="cia_afficherlesmemoiresinter")
         * 
         */
    public function afficherlesmemoiresinter(Request $request)
    {
        $repositoryOrgacia= $this->getDoctrine()
		->getManager()
		->getRepository('App:Orgacia');
        $repositoryMemoiresinter= $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoiresinter');
        $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
        $repositoryCentrescia= $this->getDoctrine()
		->getManager()
		->getRepository('App:Centrescia');
        $user = $this->getUser();
        $organisateurcia=$user->getUserName()
                ;//Le nom des organisateurs cia et membres jury est générique : celui du centre donc  même session pour tous
        $centre=$repositoryCentrescia->findOneByCentre(['centre'=>$organisateurcia]);
        $liste_equipe= $repositoryEquipesadmin->findByCentre(['centre'=>$centre]);
        
        $i=0;
        
        foreach($liste_equipe as $equipe){
            
            $memoires= $repositoryMemoiresinter->findByEquipe(['equipe'=>$equipe]);
            
             foreach($memoires as $memoire){
                    $id=$memoire->getId();
                    $formBuilder[$i]=$this->get('form.factory')->createNamedBuilder('Form'.$id, ListmemoiresInterType::class,$memoire);  
                    $formBuilder[$i] ->add('id',  HiddenType::class, ['disabled'=>true, 'label'=>false])
                                            ->add('memoire', TextType::class,['disabled'=>true,  'label'=>false])
                                            ->add('equipe', EntityType::class,
                                                    [ 'class' => 'App:EquipesAdmin',
                                                                     // 'query_builder' => ,
                                                                      'choice_label'=> 'getTitreProjet',
                                                                      'multiple' => false, 
                                                                      'disabled'=>true,
                                                                      'label'=>false, 
                                                                      'expanded'=>false
                                                                   ]
                                                      )
                                           
                                            ->add('save',      SubmitType::class );
                                            

                    $Form[$i]=$formBuilder[$i]->getForm();
                    $formtab[$i]=$Form[$i]->createView();
                   // $Form[$i]= $this->createForm(ListmemoiresinterType::class, $memoire);
                    //$Form[$i]->handleRequest($request);
                    
                    
            
            if ($request->isMethod('POST') ) 
                        {
                
                
                
                if ($request->request->has('Form'.$id)) {
                      
                               //if ($Form[$i]->isSubmitted()){
                                  $id=$Form[$i]->get('id')->getData();
                                  $memoire=$repositoryMemoiresinter->find(['id'=>$id]);
                                  $memoireName=$this->getParameter('repertoire_memoire_interacademiques').'/'.$memoire->getMemoire();
                                   if(null !==$memoireName)
                                       {
                                   $response = new BinaryFileResponse($memoireName);
         
                                   $disposition = HeaderUtils::makeDisposition(
                                HeaderUtils::DISPOSITION_ATTACHMENT,
                                    $memoire->getMemoire()
                                          );
                                  $response->headers->set('Content-Type', 'application/pdf'); 
                                  $response->headers->set('Content-Disposition', $disposition);


                                  //$content = $this->render('secretariat\lire_memoire.html.twig', array('repertoirememoire' => $this->getParameter('repertoire_memoire_national'),'memoire'=>$memoire));
                                  return $response; 
                                 
                                        }
                                 
                                   }
                     if ($request->request->has('FormAll')) {         
                          $zipFile = new \ZipArchive();
                          $FileName= 'memoires'.'-'.$centre->getCentre().date('now');
                          if ($zipFile->open($FileName, ZipArchive::CREATE) === TRUE){
                          $liste_equipe= $repositoryEquipesadmin->findByCentre(['centre'=>$centre]);
                          
                                    foreach($liste_equipe as $equipe){
                                        $memoires= $repositoryMemoiresinter->findByEquipe(['equipe'=>$equipe]);

                                                foreach($memoires as $memoire){
                                                        $Memoire=$this->getParameter('repertoire_memoire_interacademiques').'/'.$memoire->getMemoire();
                                                         
                                                         $zipFile->addFromString(basename($Memoire),  file_get_contents($Memoire));//voir https://stackoverflow.com/questions/20268025/symfony2-create-and-download-zip-file
                                                 }
                                            }
                                        $zipFile->close();
                                        $response = new Response(file_get_contents($FileName));//voir https://stackoverflow.com/questions/20268025/symfony2-create-and-download-zip-file

                                        $disposition = HeaderUtils::makeDisposition(
                                            HeaderUtils::DISPOSITION_ATTACHMENT,
                                            $FileName
                                                  );
                                          $response->headers->set('Content-Type', 'application/zip'); 
                                          $response->headers->set('Content-Disposition', $disposition);


                                          //$content = $this->render('secretariat\lire_memoire.html.twig', array('repertoirememoire' => $this->getParameter('repertoire_memoire_national'),'memoire'=>$memoire));
                                          return $response; 
                                 }
                     }
                     }
                     $i=$i+1;
                    }
                        
                    }
                       
                       
                     
                        
                
              
         if(isset($formtab)){       
                    $formBuilder=$this->get('form.factory')->createNamedBuilder('FormAll', ListmemoiresinterallType::class,$memoire);  
                    $formBuilder->add('save',      SubmitType::class )
                                            ;

                    $Form=$formBuilder->getForm();
                    $formtab[$i]=$Form->createView();//Ajoute le bouton  tout télécharger
             
             
             
             
             
             
        $content = $this
                 ->render('secretariat\charge_memoires_inter.html.twig', array('formtab'=>$formtab,
                     'centre'=>$organisateurcia)
                                );
        
                          }
         else{
                     $request->getSession()
                         ->getFlashBag()
                         ->add('info', 'Il n\'y a pas encore de fichier déposé pour votre centre.') ;
                    
                    return $this->redirectToRoute('core_home');   
                     }
                  return new Response($content);  
              
}
 /**
         * @Security("has_role('ROLE_ORGACIA')")
         * 
         * @Route("/cia/depose_memoire_orgacia", name="cia_depose_memoire_orgacia")
         * 
         */
public function depose_memoire_orgacia(Request $request) //Pour les organisateurs d'un centre seulement.
{
    $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
    $repositoryMemoireinter=$this->getDoctrine()
                                    ->getManager()
                                    ->getRepository('App:Memoiresinter');
    $repositoryCentrescia=$this->getDoctrine()
                                    ->getManager()
                                    ->getRepository('App:Centrescia');
    
    $user = $this->getUser();
    $nom_user=$user->getUserName();//Le nom de l'user est celui du centre
    
    $qb1 =$repositoryEquipesadmin->createQueryBuilder('e')
                             ->where('e.centre=:centre')
	           ->setParameter('centre',  $repositoryCentrescia->findByCentre(['centre'=>$nom_user]));
    
    $equipes=$qb1->getQuery()->getResult();
    
              $Memoire=new Memoiresinter();
              $FormBuilder1= $this->get('form.factory')->createBuilder(FormType::class, $Memoire);
              $FormBuilder1
                     ->add('Equipe',EntityType::class,[
                                       'class' => 'App:Equipesadmin',
                                         'query_builder' => $qb1,
                                       'choice_label'=>'getInfoequipe',
                                        'label' => 'Choisir une équipe .',
                                       'mapped'=>false,
                                         ])     
                ->add('memoireFile', FileType::class, [
                                'label' => 'Choisir le mémoire de votre équipe  (de type PDF de taille inférieure à 2,5 M )',
                          'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // everytime you edit the Product details
                'required' => false,
                 
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                 'constraints' => [
                    new File([
                        'maxSize' => '2600k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ] ,
                        'mimeTypesMessage' => 'Refusé : le document doit être au format pdf et de taille inférieure à 2,5M' ,
                        ])
              ]])
              ->add('annexe', CheckboxType::class,['label'=>'Cliquez ici pour une annexe' , 'required' =>false, 'mapped' => false])
              ->add('save',      SubmitType::class);   
              $form2=$FormBuilder1->getForm();    
               
              if ($request->isMethod('POST') && $form2->handleRequest($request)->isValid()) {
                  $em=$this->getDoctrine()->getManager();
                  $equipe=$form2->get('Equipe')->getData();
                  $memoire_file=$form2->get('memoireFile')->getData();
                  require_once('../vendor/autoload.php');//Pour tester si le nombre de page est inférieur à 21.
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($memoire_file);
                    $details  = $pdf->getDetails();//On récupère les metadata du fichier
                     $pages='';

                    foreach ($details as $property => $value) {// On récupère le nombre de pages
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }
                        if ($property=='Pages'){
                      
                        $pages =  $value ;
                        }
                      }
                     if ($pages> 20) { //S'il y a plus de 20 page la procédure est interrompue et on return à la apge d'accuil avec un message d'avertissement
                        // $gestionfichier=new Filesystem($this->getParameter($repertoiretmp).'/'.$fileName);
                         //try {
                       // $gestionfichier->remove($this->getParameter($repertoiretmp).'/'.$fileName);
                    //} catch (IOExceptionInterface $exception) {
                        //echo "An error occurred while creating your directory at ".$exception->getPath();
                   // }
                         
                        $request->getSession()
                         ->getFlashBag()
                         ->add('alert', 'Votre fichier contient  '.$pages.' pages. Il n\'a pas pu être accepté, il ne doit pas dépasser 20 pages !' ) ; 
                     }      
                  else {
                  
                  
                  $annexe=$form2->get('annexe')->getData();
                  $memoire_precedents=$repositoryMemoireinter->findByEquipe(['equipe'=>$equipe]);
                  if($memoire_precedents){//si des mémoires(meoire ou annexe) ont déjà été déposés on écrase seulement les précédentes données
                            $flagannexe = -1;//initialisation à -1 des id des fichiers 
                            $flagmemoire =-1;
                            foreach($memoire_precedents as $memoire){
                                if ($memoire->getAnnexe() == 0 ){//On distingue les annexes des annexes qui sont dans la même table Mémoires avec le boolean annexe de la table
                                      $flagmemoire=$memoire->getId();//On recupère l'id du fichier pour pouvoir le remplacer
                                     }
                                 if ($memoire->getAnnexe() == 1 ){
                                     $flagannexe=$memoire->getId();
                                     }
                          }     
                                if($annexe===true){ 
                                      $annexe_fichier=1; //on place le boolean à true
                                       if($flagannexe != -1){//
                                       $memoire=$repositoryMemoireinter->findOneById(['id'=>$flagannexe]);
                                                }
                                       else { 
                                           $memoire=new Memoiresinter();//on ne remplace pas un fichier existant donc nouvel enregistrement
                                           
                                       }
                                 }
                            if($annexe===false){ 
                                $annexe_fichier=0;//On place le boolean à false
                                 if($flagmemoire != -1){
                                      $memoire=$repositoryMemoireinter->findOneById(['id'=>$flagmemoire]);
                                 }
                                 else {
                                      $memoire=new Memoiresinter();//on ne remplace pas un fichier existant donc nouvel enregistrement
                                 }
                                 }
                                $memoire->setEquipe($equipe);
                                //$memoire->setUpdatedAt(new \DateTime('now'));
                                $memoire->setAnnexe($annexe_fichier);//on injecte la valeur du boolean
                                $memoire->setMemoireFile($memoire_file);
                                
                                $em->persist($memoire);
                                $em->flush();
                  
                  }
                   if (!$memoire_precedents){       //si il n'y a pas de mémoire encore  déposés il faut ajouter la ligne correpondant à la table mémoires
                       
                        
                        $nouveau_memoire= new Memoiresinter();
                       
                        $nouveau_memoire->setEquipe($equipe);
                       //$nouveau_memoire->setUpdatedAt(new \DateTime('now'));
                        if($annexe===true){
                            $nouveau_memoire->setAnnexe(true);//on place le boolean annexe à true
                        }
                        if($annexe===false){
                            $nouveau_memoire->setAnnexe(false);//on place le boolean  annexe à false
                        }
                         $nouveau_memoire->setMemoireFile($memoire_file);//utilisation de vichuploader :  le memoire ou l' annexe seront enregistrés en même temps 
                         $em->persist( $nouveau_memoire);
                        $em->flush();
                          }
                          $request->getSession()
                         ->getFlashBag()
                         ->add('info', 'Le fichier  a bien été déposé. Merci !') ;
                          
                  }
                  
                  
                         return $this->redirectToRoute('cia_depose_memoire_orgacia');
                  
                  
                  
                  
              }
    
    
    
    
    
    $content = $this ->render('cia\charge_memoire_inter_orgacia.html.twig', array('form'=>$form2->createView(),'centre'=>$nom_user));
	return new Response($content);   
    
    
}



       /**
         * @Security("has_role('ROLE_PROF')")
         * 
         * @Route("/cia/charge_fiche_securite", name="cia_charge_fiche_securite")
         * 
         */
public function charge_fiche_securite(Request $request)
{           
             
             $repositoryTotalequipes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Totalequipes');
             $repositoryAdminsite= $this->getDoctrine()
		->getManager()
		->getRepository('App:Adminsite');
             $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
            $dates=$repositoryAdminsite->find(2);
            $datelimcia = $dates->getDatelimcia();
            $datelimnat=$dates->getDatelimnat();
            $dateouverturesite=$dates->getDateouverturesite();
            
          
            //recupétation équipe(s) du prof1
           $user = $this->getUser();
            $professeur=$user->getUserName();
            $fileName='';
            $lettre_equipe_choisie = '';
            $nom_equipe='';
           
           //$form=$this->CreateForm(MemoiresType::class,$Memoire);
            
              
              $dateconnect= new \datetime('now');
              if (($dateconnect>$datelimcia) and ($dateconnect<$datelimnat)) {
                  $equipe=new Totalequipes(); 
                  $FormBuilder2= $this->get('form.factory')->createBuilder(FormType::class, $equipe);
                  
                    $qb1 =$repositoryquipes->createQueryBuilder('t')
                             ->where('t.nomProf1=:professeur')
	           ->setParameter('professeur', $professeur);
             $equipes_prof=$qb1->getQuery()->getResult();              
            //$Totalequipe= $qb->getQuery()->getResult();
            
            
                           
                                  
                  $FormBuilder2->add('lettre_equipe',EntityType::class,[
                                       'class' => 'App:Totalequipes',
                                         'query_builder' => $qb1,
                                       'choice_label'=>'getLettreEquipe',
                                        'label' => 'Choisir une équipe .',
                                       
                                         ])
                                   ->add('Choisir cette equipe', SubmitType::class);
              }
              if (($dateconnect>$dateouverturesite) and ($dateconnect<$datelimcia)) {
                        $equipe=new Equipesadmin(); 
                        $FormBuilder2= $this->get('form.factory')->createBuilder(FormType::class, $equipe);
                  
                  
                  
                  
                   $qb2 =$repositoryEquipesadmin->createQueryBuilder('t')
                             ->where('t.nomProf1=:professeur')
	           ->setParameter('professeur', $professeur);
                $equipes_prof=$qb2->getQuery()->getResult();   
               $FormBuilder2->add('numero',EntityType::class,[
                                       'class' => 'App:Equipesadmin',
                                         'query_builder' => $qb2,
                                       'choice_label'=>'getNumero',
                                        'label' => 'Choisir une équipe .',
                                       
                                         ])
                                   ->add('Choisir cette équipe', SubmitType::class);
              }
               $form2=$FormBuilder2->getForm();
              if ($request->isMethod('POST') && $form2->handleRequest($request)->isValid()) 
                     { 
                        if (($dateconnect>$datelimcia) and ($dateconnect<$datelimnat)) 
                            {    if ( $form2->get('lettre_equipe')->getData()){
                                $lettre_equipe=$form2->get('lettre_equipe')->getData()->getLettreEquipe();
                                     //$idequipe=$equipe->getId();

                               $numero_equipe= $repositoryTotalequipes->findOneBy(['lettreEquipe'=>$lettre_equipe])->getNumeroEquipe();
                               return $this->redirectToRoute('cia_confirme_charge_fichessecur',array('numero_equipe'=>$numero_equipe));
                               //return $this->redirectToRoute('core_home');
                            }
                            }
                        if (($dateconnect>$dateouverturesite) and ($dateconnect<$datelimcia)) 
                            {    
                            
                        
                                if ($form2->get('numero')->getData()){
                                 $numero_equipe=$form2->get('numero')->getData()->getNumero();
                                     //$idequipe=$equipe->getId();

                               return $this->redirectToRoute('cia_confirme_charge_fichessecur',array('numero_equipe'=>$numero_equipe));
                                }
                                
                             }
                          $request->getSession()
                                    ->getFlashBag()
                                    ->add('info', 'Une erreur est survenue lors de cette opération. Veuilllez recommencer ou prévenir le comité  d\'un disfonctionement du site.');
                                    return $this->redirectToRoute('core_home');   
                             
                             
                     }
                     
                     
                 $content = $this
                                     ->render('cia\charge_fichessecur.html.twig', array('form'=>$form2->createView()));
	return new Response($content);   
    
}
 
/**
         * @Security("has_role('ROLE_PROF')")
         * @var Symfony\Component\HttpFoundation\File\UploadedFile $file 
         * @Route("/cia/confirme_charge_fichesssecur/{numero_equipe}", name="cia_confirme_charge_fichessecur")
         * 
         */        
         public function  confirme_charge_fichesscur(Request $request,$numero_equipe){   
                $repositoryTotalequipes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Totalequipes');
                 $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
                $repositoryEquipes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipes');
                 $repositoryFichessecur= $this->getDoctrine()
		->getManager()
		->getRepository('App:Fichessecur');
                    
                
                 $Equipe_choisie=$repositoryEquipesadmin->findOneByNumero(['numero'=>$numero_equipe]);
                 
                
                 $Fiche = $repositoryFichessecur->FindBy(['equipe'=>$Equipe_choisie]);
                                
                 
                 $avertissement ='';
                 
                 if ($Fiche){
                                                 
                             $avertissement= 'La fiche sécurité existe déjà. ' ;
                      
                     }
                     
                     if ($Equipe_choisie){
                         $lettre_equipe= $Equipe_choisie->getLettre();//on charge la lettre de l'équipe 
                         if(!$lettre_equipe){                                     // si la lettre n'est pas attribuée on est en phase interac
                                         //On cherche un mémoire et son annexe déjà déposés pour cette équipe                            }
                                        
                             $TitreProjet = $Equipe_choisie->getTitreProjet();
                                        
                         }                  
                         if($lettre_equipe){                                     // si la lettre est attribuée on est en phase  concours nationale
                                   $Equipe_choisie=$repositoryEquipes->findOneBy(['lettre'=>$lettre_equipe]);//On cherche l'instance dans les équipes sélectionnées
                                  //On cherche un mémoire et son annexe déjà déposés pour cette équipe                            }
                                   $TitreProjet = $Equipe_choisie->getTitreProjet();
                                   }    
                         
                           
                           if($Fiche){                                               //Si une fiche est déjà déposée on demande si on veut écraser le précédent
                                   $form3 = $this->createForm(ConfirmType::class);                                  ;
                                if ($request->isMethod('POST') && $form3->handleRequest($request)->isValid()) 
                                { 
                                 //$lettreequipe=$form3->get('lettre_equipe')->getData()->getLettreEquipe();
                                 if ($form3->get('OUI')->isClicked())
                                     {
                                       
                                return $this->redirectToRoute('cia_charge_fichessecur_fichier',array('numero_equipe'=>$numero_equipe));
                            
                                        }
                                 if ($form3->get('NON')->isClicked())
                                     {
                                return $this->redirectToRoute('core_home');
                                        }
                                }
                                $request->getSession()
                                    ->getFlashBag()
                                    ->add('info', $avertissement.', Voulez-vous poursuivre et remplacer éventuellement ce fichier ? Cette opération est défintive, sans possibilité de récupération.') ;
                                $content = $this
                                                ->render('cia\confirm_charge_fichessecur.html.twig', array('form'=>$form3->createView(), 'lettre_equipe'=>$lettre_equipe, 'numero_equipe'=>$numero_equipe, 'titre_projet' =>$TitreProjet));
                                return new Response($content);   
                                }
                                if(!$Fiche){             //Si pas de mémoire déjà déposé on redirige directement vers la page de choix du fichier à déposer
                                     return $this->redirectToRoute('cia_charge_fichessecur_fichier',array('numero_equipe'=>$numero_equipe));
                                }
                     }
                     $request->getSession()
                                    ->getFlashBag()
                                    ->add('info', 'Une erreur est survenue lors de cette opération. Veuilllez recommencer ou prévenir le comité  d\'un disfonctionement du site.');
                                    return $this->redirectToRoute('core_home');
         }

 /**
         * @Security("has_role('ROLE_PROF')")
         * @var Symfony\Component\HttpFoundation\File\UploadedFile $file 
         * @Route("/cia/charge_fichessecur_fichier/{numero_equipe}", name="cia_charge_fichessecur_fichier")
         * 
         */         
         public function   charge_fichessecur_fichier(Request $request, $numero_equipe, \Swift_Mailer $mailer){
             $repositoryFichessecur= $this->getDoctrine()
		->getManager()
		->getRepository('App:Fichessecur');
             
             $repositoryEquipes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipes');
             $repositoryTotalequipes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Totalequipes');
             $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
              $defaultData = ['message' => 'Charger le memoire'];
             $lettre_equipe= $repositoryEquipesadmin->findOneByNumero(['numero'=>$numero_equipe])->getLettre();
             $Fiche=new Fichessecur();
             
             //$lettre_equipe=$request->get('equipe')->getData();
             //$equipe=$repositoryTotalequipes->find($idequipe);
             if($lettre_equipe){
                        
                        $nom_equipe=$repositoryTotalequipes->findOneByLettreEquipe(['lettreEquipe'=>$lettre_equipe])->getnomEquipe();
                        $donnees_equipe=$lettre_equipe.' : '.$nom_equipe;
                         $fileName ='eq-'.$lettre_equipe.'-Fiche_securite-'.$nom_equipe.'.';
                         
                         
             }
              if(!$lettre_equipe){
                       
                        $nom_equipe=$repositoryEquipesadmin->findOneByNumero(['numero'=>$numero_equipe])->getTitreProjet();
                        $donnees_equipe=$numero_equipe.' : '.$nom_equipe;
                         $fileName ='eq-'.$numero_equipe.'-Fiche_securite-'.$nom_equipe.'.';
                        
                         
              }
                 $repertoire='repertoire_fiches_securite';
                 $form1=$this->createForm(FichessecurType::class,$Fiche);
                                    
        
                 
                 $form1->handleRequest($request); 
                if ($form1->isSubmitted() && $form1->isValid()){
                      /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */  
                     $file=$form1->get('fiche')->getData();
                    if ($file->guessExtension()=='bin'){
                        $extension='docx';
                    }
                    if ($file->guessExtension()!='bin'){
                        $extension=$file->guessExtension();
                    }
                     $fileName=$fileName.$extension;
                     
                     $fileName = iconv('UTF-8','ASCII//IGNORE',$fileName);
                      $em=$this->getDoctrine()->getManager();
                 
                

                   
                    if ($lettre_equipe){                                //On dépose la fiche pour les épreuves nationales
                         
                        $Equipe_choisie=$repositoryEquipes->findOneBy(['lettre'=>$lettre_equipe]);
                       
                      if ($Equipe_choisie){// Si un mémoire a déjà été déposé on écrase le précédent
                            $Fiche = $repositoryFichessecur->findOneByEquipe(['equipe'=>$Equipe_choisie]);
                            
                             if (!$Fiche){       //si il n'y a pas de Fiche encore  déposés il faut ajouter la ligne correpondant à la table fichessecur
                       
                        $nouvelle_fiche= new Fichessecur();
                        $nouvelle_fiche->setFichessecur($fileName);
                        $nouvelle_fiche->setEquipe($Equipe_choisie);
                        $nouvelle_fiche->setUpdatedAt(new \DateTime('now'));
                        $em->persist( $nouvelle_fiche);
                     
                         $em->flush();
                       
                        
                               }                    
                            if($Fiche){//si la fiche a  déjà été déposés on écrase seulement la précédente
                                
                                $NomFichier=$Fiche->getFiche();
                                $filesystem = new Filesystem();
                                try {
                                         $filesystem->remove($this->getParameter($repertoire).'/'.$NomFichier );
                                } catch (IOExceptionInterface $exception) {
                                   
                                }
                            
                                $Fiche->setEquipe($Equipe_choisie);
                                $Fiche->setUpdatedAt(new \DateTime('now'));
                                $Fiche->setFiche($fileName);
                                $em->persist($memoire);
                                $em->flush();
                                
                         }
                         }
                                      
                     
                     
                    }
                     if (!$lettre_equipe){//On dépose un mémoire interacadémique car les lettres ne sont pas attribuées
                                          $Equipe_choisie=$repositoryEquipesadmin->findOneByNumero(['numero'=>$numero_equipe]);
                                          $Fiche = $repositoryFichessecur->findOneByEquipe(['equipe'=>$Equipe_choisie]);
                                          
                                          
                      if ($Equipe_choisie){// si l'équipe existe vraiment(si une équipe dépose son fichier avant la validation de sin inscription sur le site 
                    
                    
                    
                            if (!$Fiche){       //si il n'y a pas de fiche encore  déposés il faut ajouter la ligne correpondant à la table fichesssecur
                       
                        
                        $nouvelle_fiche= new Fichessecur();
                        $nouvelle_fiche->setFiche($fileName);
                        $nouvelle_fiche->setEquipe($Equipe_choisie);
                        $nouvelle_fiche->setUpdatedAt(new \DateTime('now'));
                                               
                        $em->persist( $nouvelle_fiche);
                        $em->flush();
                        //$memoire_tmp=$repositoryMemoires->findOneBy(['equipe'=>$lettre_equipe]);;
                        
                          }                    
                        if($Fiche){//si la fiche a  déjà été déposée on écrase seulement la précédente
                                $NomFichier=$Fiche->getFiche();
                                $filesystem = new Filesystem();
                                try {
                                         $filesystem->remove($this->getParameter($repertoire).'/'.$NomFichier );
                                } catch (IOExceptionInterface $exception) {
                                   
                                }
                               $Fiche->setFiche($fileName);
                                $Fiche->setUpdatedAt(new \DateTime('now'));
                                $Fiche->setEquipe($Equipe_choisie);
                                
                                 $em->persist($Fiche);

                                 $em->flush();
                         }
                         }
                       
                    }
                    try {
                   $file->move(
                     $this->getParameter($repertoire),$fileName
                                       );
                        } catch (FileException $e) {
                //... handle exception if something happens during file upload
                    }
                   
                     $request->getSession()
                         ->getFlashBag()
                         ->add('info', 'Votre fichier '.$fileName.' a bien été déposé. Merci !') ;
                        
                     $user = $this->getUser();
                      $message = (new \Swift_Message('L\'équipe'.$Equipe_choisie->getTitreProjet().'n°'.$Equipe_choisie->getNumero().' a déposé un fichier'))
                                       ->setFrom('alain.jouve@wanadoo.fr')
                                       ->setTo('alain.jouve@wanadoo.fr')
                                       ->setBody(
                                           $this->renderView(
                                               // templates/emails/registration.html.twig
                                               'emails/confirme_fichier.html.twig',
                                               ['name' => $user->getUsername(), 'fichier'=>$fileName]
                                           ),
                                           'text/html'
                                       );

                                       // you can remove the following code if you don't define a text version for your emails
                                      

                                   $mailer->send($message); 
                     
                     
                    return $this->redirectToRoute('core_home');
       
                   
                     }
                     
                     $content = $this
                                     ->render('cia\charge_fichessecur.html.twig', array('form'=>$form1->createView(),'donnees_equipe'=>$donnees_equipe));
	return new Response($content);          
                     
                     
                 }
         /**
         * @Security("has_role('ROLE_SUPER_ADMIN')")
         * @var Symfony\Component\HttpFoundation\File\UploadedFile $file 
         * @Route("/cia/charge_eleves_inter", name="cia_charge_eleves_inter")
         * 
         */         
         public function   charge_eleves_inter(Request $request){         
                 $defaultData = ['message' => 'Charger le fichier des élèves '];
            $form = $this->createFormBuilder($defaultData)
                            ->add('fichier',      FileType::class)
                            ->add('Envoyer',      SubmitType::class)
                            ->getForm();
            
            $repositoryElevesinter = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Elevesinter');
            $repositoryEquipesadmin= $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Equipesadmin');
            $form->handleRequest($request);                            
            if ($form->isSubmitted() && $form->isValid()) 
                {
                $data=$form->getData();
                $fichier=$data['fichier'];
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fichier);
                $worksheet = $spreadsheet->getActiveSheet();
            
                $highestRow = $worksheet->getHighestRow();              
 
                $em = $this->getDoctrine()->getManager();
                 
                for ($row = 2; $row <= $highestRow; ++$row) 
                   {                       
                   
                   $value = $worksheet->getCellByColumnAndRow(2, $row)->getValue();//On lit le nom de l'élève
                   $nom=$value;
                   $eleve=$repositoryElevesinter->findOneByNom($nom);//On vérifie si  cet élèves est déjà dans la base
                   if(!$eleve){ // si l'éleve n'existe pas, on le crée
                       $eleve= new elevesinter(); 
                                   } //sinon on écrase les précédentes données
                        $eleve->setNom($nom) ;
                        $value = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                        $eleve->setPrenom($value);
                        $value = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                        $eleve->setClasse($value);
                        $value = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                        $eleve->setCourriel($value);
                        $value = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                        $eleve->setGenre($value);
                        $value = $worksheet->getCellByColumnAndRow(20, $row)->getValue(); 
                        $equipe=$repositoryEquipesadmin->findOneByNumero(['numero'=>$value]);
                         if($equipe) {                    
                         $eleve->setEquipe($equipe) ;
                         
                         }
                        $em->persist($eleve);


                         $em->flush();
                   
                   }
                    return $this->redirectToRoute('secretariat_accueil');
                }
        $content = $this
                        ->render('secretariat\uploadexcel.html.twig', array('form'=>$form->createView(),));
	return new Response($content);          
        }        
             
             
             
             
             
                
                 
                 
                 
                 

}

