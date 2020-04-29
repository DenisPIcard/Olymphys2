<?php
namespace App\Controller ;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType ; 

use App\Service\Mailer;
use App\Form\NotesType ;
use App\Form\PhrasesType ;
use App\Form\EquipesType ;
use App\Form\JuresType ;
use App\Form\CadeauxType ;
use App\Form\ClassementType ;
use App\Form\PrixType ;
use App\Form\ToutfichiersType;
use App\Form\ConfirmType;
use App\Form\ListmemoiresinterType;
use App\Form\ListefichiersType;
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
use App\Entity\Fichiersequipes;
use App\Entity\Equipesadmin;
use App\Entity\Centrescia;


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


use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MimeTypeGuesserInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

//use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;

use Symfony\Component\Validator\Constraints as Assert;
//use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request ;
use Symfony\Component\HttpFoundation\RedirectResponse ;
use Symfony\Component\HttpFoundation\Response ;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\String\Slugger\SluggerInterface;

use Howtomakeaturn\PDFInfo\PDFInfo;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ZipArchive;
    
class FichiersController extends AbstractController
{
 /**
         * @Security("is_granted('ROLE_ORGACIA')")
         * 
         * @Route("/fichiers/choix_centre", name="fichiers_choix_centre")
         * 
         */           
public function choix_centre(Request $request) {
    $repositoryCentres=$this->getDoctrine()
		->getManager()
		->getRepository('App:Centrescia');
    $liste_centres = $repositoryCentres->findAll();
     if(isset($liste_centres)) {
                   $content = $this
                 ->renderView('adminfichiers\choix_centre.html.twig', array(
                     'liste_centres'=>$liste_centres,
                    )
                                );
        return new Response($content);  
     }
    }
 
 /**
         * @Security("is_granted('ROLE_PROF')")
         * 
         * @Route("/fichiers/choix_equipe/{choix}", name="fichiers_choix_equipe")
         * 
         */           
public function choix_equipe(Request $request,$choix) {
    $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
    $repositoryEdition= $this->getDoctrine()
		->getManager()
		->getRepository('App:Edition');
     $repositoryCentres= $this->getDoctrine()
		->getManager()
		->getRepository('App:Centrescia');   
    $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);       
    $centres=$repositoryCentres->findAll();
    $datelimcia = $edition->getDatelimcia();
    $datelimnat=$edition->getDatelimnat(); 
    $datecia=$edition->getConcourscia(); 
    $datecn=$edition->getConcourscn(); 
    $dateouverturesite=$edition->getDateouverturesite();
    $dateconnect= new \datetime('now');
    
    $user = $this->getUser();
    $id_user=$user->getId(); 
    $roles=$user->getRoles();
    $role=$roles[0];
    
         if($role=='ROLE_JURY'){
             $nom=$user->getUsername();
             
             $repositoryJures = $this->getDoctrine()
		->getManager()
		->getRepository('App:Jures');
                $jure=$repositoryJures->findOneByNomJure(['nomJure'=>$nom]);
		$id_jure = $jure->getId();
               }
    $qb1 =$repositoryEquipesadmin->createQueryBuilder('t')
                             ->andWhere('t.selectionnee=:selectionnee')
                             ->setParameter('selectionnee', TRUE)
                             ->andWhere('t.lettre >:valeur')
                             ->setParameter('valeur','')
                             ->orderBy('t.lettre', 'ASC');
    
     $qb3 =$repositoryEquipesadmin->createQueryBuilder('t')
                             ->where('t.idProf1=:professeur')
                             ->orwhere('t.idProf2=:professeur')
                             ->setParameter('professeur', $id_user);
   if ($dateconnect>$datelimcia) {
        $phase='national';
   }
    if (($dateconnect>$dateouverturesite) and ($dateconnect<=$datelimcia)) {
        $phase= 'interacadémique';
    }
   
    if ($choix=='liste_cn_comite')  {
                    if (($role=='ROLE_COMITE') or ($role=='ROLE_JURY') or ($role=='ROLE_SUPER_ADMIN')){

                        $liste_equipes=$qb1->getQuery()->getResult();    
                        if(isset($liste_equipes)) {
                           if(($role=='ROLE_COMITE') or ($role=='ROLE_SUPER_ADMIN')){

                        $content = $this
                                 ->renderView('adminfichiers\choix_equipe.html.twig', array(
                                     'liste_equipes'=>$liste_equipes,  'user'=>$user, 'phase'=>'national', 'role'=>$role,'choix'=>$choix
                                    )
                           );}
                           if($role=='ROLE_JURY'){
                        $content = $this
                                 ->renderView('adminfichiers\choix_equipe.html.twig', array(
                                     'liste_equipes'=>$liste_equipes,  'user'=>$user, 'phase'=>'national', 'role'=>$role,'choix'=>$choix,'jure'=>$jure)//Jure necessaire pour le titre 
                                       ); 
                           }
                           return new Response($content);  
                       } 
                        if(!isset($liste_equipes)) {
                           $request->getSession()
                                ->getFlashBag()
                                ->add('info', 'Pas encore d\'équipe pour le concours national de la '.$edition->getEd().'e edition') ;
                        return $this->redirectToRoute('core_home');    
                        }
                    }    
    } 
    
    foreach($centres as $Centre){
              if ($Centre->getCentre()==$choix){
                                      $centre = $Centre;
              } 
    }
    
    if (isset($centre) or ($choix=='centre'))  { //pour le jurycia, comité, superadmin liste des équipes d'un centre
                            if (($role=='ROLE_COMITE') or ($role=='ROLE_JURY') or ($role=='ROLE_SUPER_ADMIN')or ($role=='ROLE_ORGACIA') or ($role=='ROLE_JURYCIA')){  
                                  if (!isset($centre)){
                                      $centre=$this->getUser()->getCentrecia();
                                  }
                                  $qb2 =$repositoryEquipesadmin->createQueryBuilder('t')
                                                  ->where('t.centre=:centre')
                                                  ->setParameter('centre', $centre)
                                                  ->orderBy('t.numero', 'ASC');
                             $liste_equipes=$qb2->getQuery()->getResult();  

                             if(isset($liste_equipes)) {

                             $content = $this
                                      ->renderView('adminfichiers\choix_equipe.html.twig', array(
                                          'liste_equipes'=>$liste_equipes,  'user'=>$user, 'phase'=>'interacadémique', 'role'=>$role,'choix'=>'liste_prof','centre'=>$centre->getCentre()
                                         )
                                                     );
                             return new Response($content);  

                             } 
                             if(!isset($liste_equipes)) {
                                $request->getSession()
                                     ->getFlashBag()
                                     ->add('info', 'Pas encore d\'équipe pour le concours interacadémique de la '.$edition->getEd().'e edition') ;
                             return $this->redirectToRoute('core_home');    
                             }
                         }
    }
    
   if ($choix=='presentation'){//pour le dépôt des présentations
          if ($dateconnect>$datelimnat )  {
                                          $qb3->andWhere('t.selectionnee=:selectionnee')
                                                              ->setParameter('selectionnee', TRUE);

                                         $liste_equipes=$qb3->getQuery()->getResult();    
                                      }
                                         if(isset($liste_equipes)) {

                                         $content = $this
                                                  ->renderView('adminfichiers\choix_equipe.html.twig', array(
                                                      'liste_equipes'=>$liste_equipes, 'phase'=>$phase, 'user'=>$user,'choix'=>$choix,'role'=>$role
                                                     ) );

                                         return new Response($content); 
                                                  }
                                          else{ 
                                         $request->getSession()
                                                 ->getFlashBag()
                                                 ->add('info', 'Le site n\'est pas encore prêt pour une saisie des diaporamas ou vous n\'avez pas d\'équipes inscrite pour le concours national de la '.$edition->getEd().'e edition') ;
                                         return $this->redirectToRoute('core_home');
                                          }
                                       }
if ($choix=='liste_prof'){
                                          if ($phase=='interacadémique')     {
                                         $liste_equipes=$qb3->getQuery()->getResult();    
                                          }

                                         if ($dateconnect>$datelimcia) {
                                             $qb3->andWhere('t.selectionnee=:selectionnee')
                                                     ->setParameter('selectionnee', TRUE);
                                            $liste_equipes=$qb3->getQuery()->getResult();     
                                         }

                                         if(isset($liste_equipes)) {

                                         $content = $this
                                                  ->renderView('adminfichiers\choix_equipe.html.twig', array(
                                                      'liste_equipes'=>$liste_equipes,  'phase'=>$phase, 'user'=>$user,'choix'=>$choix,'role'=>$role
                                                     ) );
                                          return new Response($content);  

                                                  }
                                          else{ 
                                         $request->getSession()
                                                 ->getFlashBag()
                                                 ->add('info', 'Le site n\'est pas encore prêt pour une saisie des mémoires ou vous n\'avez pas d\'équipes inscrite pour le concours '. $phase.'de la '.$edition->getEd().'e edition') ;
                                         return $this->redirectToRoute('core_home');    
                                             }
   }
   
  if ($choix=='deposer') {//pour le dépôt des fichiers autres que les présentations
      
                                            if ($role=='ROLE_PROF') {
                                         if (($dateconnect>$datelimcia) and ($dateconnect<=$datelimnat)) {
                                             $phase='national';
                                              $qb3 ->andWhere('t.selectionnee=:selectionnee')
                                                                  ->setParameter('selectionnee', TRUE);     
                                             $liste_equipes=$qb3->getQuery()->getResult();    
                                             }
                                         if (($dateconnect>$dateouverturesite) and ($dateconnect<=$datelimcia)) {
                                             $phase= 'interacadémique';

                                             $liste_equipes=$qb3->getQuery()->getResult();   
                                         }
                                           if(isset($liste_equipes)) {

                                             $content = $this
                                                      ->renderView('adminfichiers\choix_equipe.html.twig', array(
                                                          'liste_equipes'=>$liste_equipes, 'phase'=>$phase, 'user'=>$user,'choix'=>$choix,'role'=>$role
                                                         )
                                                                     );
                                             return new Response($content);  
                                             }   
                                         else{ 
                                             $request->getSession()
                                                     ->getFlashBag()
                                                     ->add('info', 'Le site n\'est pas encore prêt pour une saisie des mémoires ou vous n\'avez pas d\'équipes inscrite pour le concours '. $phase.'de la '.$edition->getEd().'e edition') ;
                                             return $this->redirectToRoute('core_home');   
                                             }
                                            }

                                             if( $role=='ROLE_COMITE' ){
                                                if (($dateconnect>$datelimcia) and ($dateconnect<=$datelimnat)) {
                                             $phase='national';
                                               $qb4 =$repositoryEquipesadmin->createQueryBuilder('t')
                                                                  ->where('t.selectionnee=:selectionnee')
                                                                 ->setParameter('selectionnee',TRUE)
                                                                 ->andWhere('t.lettre>:valeur')
                                                                 ->setParameter('valeur', '')
                                                                 ->orderBy('t.lettre','ASC');
                                             $liste_equipes=$qb4->getQuery()->getResult();
                                                }
                                               if (($dateconnect>$dateouverturesite) and ($dateconnect<=$datelimcia)) {
                                             $phase= 'interacadémique';
                                             $qb4 =$repositoryEquipesadmin->createQueryBuilder('t')
                                                                  ->where('t.nomLycee>:vide')
                                                                 ->setParameter('vide','')
                                                                 ->orderBy('t.numero','ASC');
                                             $liste_equipes=$qb4->getQuery()->getResult();  
                                               }
                                             }
                                                if (($role=='ROLE_ORGACIA') or ($role=='ROLE_JURYCIA') ) {

                                                    $centre=$user->getCentrecia()->getCentre();
                                                    $qb5= $repositoryEquipesadmin->createQueryBuilder('t')
                                                                  ->where('t.nomLycee>:vide')
                                                                 ->setParameter('vide','')
                                                                 ->orderBy('t.numero','ASC')
                                                                ->andWhere('t.centre =:centre')
                                                                ->setParameter('centre', $user->getCentrecia());
                                                    $liste_equipes=$qb5->getQuery()->getResult();  
                                                    if ($dateconnect>$datecia){
                                                        return $this->redirectToRoute('core_home'); 
                                                        
                                                    }
                                                



                                              if(isset($liste_equipes)) {

                                             $content = $this
                                                      ->renderView('adminfichiers\choix_equipe.html.twig', array(
                                                          'liste_equipes'=>$liste_equipes, 'phase'=>$phase, 'user'=>$user,'choix'=>$choix,'role'=>$role,'centre'=>$centre
                                                         )
                                                                     );
                                             return new Response($content);  
                                             }   
                                         else{ 
                                             $request->getSession()
                                                     ->getFlashBag()
                                                     ->add('info', 'Le site n\'est pas encore prêt pour une saisie des mémoires ou vous n\'avez pas d\'équipes inscrite pour le concours '. $phase.'de la '.$edition->getEd().'e edition') ;
                                             return $this->redirectToRoute('core_home');   
                                             }
                                             }
         }            
 }
     
 
/**
         * @Security("is_granted('ROLE_PROF')")
         * @var Symfony\Component\HttpFoundation\File\UploadedFile $file 
         * @Route("/fichiers/confirme_charge_fichier/{file_equipe}", name="fichiers_confirme_charge_fichier")
         * 
         */        
public function  confirme_charge_fichier(Request $request, $file_equipe){   
    
    $repositoryFichiersequipes= $this->getDoctrine()
                                 ->getManager()
                                 ->getRepository('App:Fichiersequipes');
    $repositoryEquipesadmin= $this->getDoctrine()
                                 ->getManager()
                                 ->getRepository('App:Equipesadmin');
     
    $info=explode("::",$file_equipe);
    $nom_fichier=$info[0];
    $id_equipe=$info[2];
    $num_type_fichier=$info[1];
    $id_fichier=$info[3];
    $Equipe_choisie=$repositoryEquipesadmin->find(['id'=>$id_equipe]);
     $Fichier =$repositoryFichiersequipes->find(['id'=>$id_fichier]);
    
   $avertissement='Le '.$this->getParameter('type_fichier_lit')[$num_type_fichier].' existe déjà';
     
        $lettre_equipe= $Equipe_choisie->getLettre();//on charge la lettre de l'équipe 
        if(!$lettre_equipe){                                     // si la lettre n'est pas attribuée on est en phase interac
                                         //On cherche un mémoire et son annexe déjà déposés pour cette équipe                            }
            $numero_equipe=$Equipe_choisie->getNumero();
            $TitreProjet = $Equipe_choisie->getTitreProjet();
            }                  
        if($lettre_equipe){                                     // si la lettre est attribuée on est en phase  concours nationale
           
                                  //On cherche un mémoire et son annexe déjà déposés pour cette équipe                            }
            $TitreProjet = $Equipe_choisie->getTitreProjet();
            }    
                                                    //Si une fiche est déjà déposée on demande si on veut écraser le précédent
            $form3 = $this->createForm(ConfirmType::class);  
            $form3->handleRequest($request);
            if ($form3->isSubmitted() && $form3->isValid()) 
                {  
                $filesystem = new Filesystem();
                if ($form3->get('OUI')->isClicked())
                    {
                    $file = new UploadedFile($this->getParameter('app.path.tempdirectory').'/'.$nom_fichier, $nom_fichier,null,null,true);
                    $Fichier->setFichierFile($file);
                   $em=$this->getDoctrine()->getManager();
                    $em->persist($Fichier);
                    $em->flush();
                    
                   $nom_fichier_uploaded=$Fichier->getFichier();
                               
                    $filesystem->remove($this->getParameter('app.path.tempdirectory').'/'.$nom_fichier);        
                          $request->getSession()
                            ->getFlashBag()
                            ->add('info', 'Votre fichier renommé selon : '.$nom_fichier_uploaded.' a bien été déposé. Merci !') ;   
                            
                      return $this->redirectToRoute('core_home');
                    }
                if ($form3->get('NON')->isClicked())
                    {
                    $filesystem->remove($this->getParameter('app.path.tempdirectory').'/'.$nom_fichier);    
                    return $this->redirectToRoute('core_home');
                    }
                }
            $request->getSession()
                    ->getFlashBag()
                    ->add('info', $avertissement.' Voulez-vous poursuivre et remplacer éventuellement ce fichier ? Cette opération est défintive, sans possibilité de récupération.') ;
            $content = $this
                            ->renderView('adminfichiers\confirm_charge_fichier.html.twig', array(
                                                    'form'=>$form3->createView(), 
                                                    'equipe'=>$Equipe_choisie, 
                                                    
                                                    'typefichier'=>$num_type_fichier
                                                     )
                                                        );
            return new Response($content);   
    }

        /**
         * @Security("is_granted('ROLE_PROF')")
         * @var Symfony\Component\HttpFoundation\File\UploadedFile $file 
         * @Route("/fichiers/charge_fichiers/{infos}", name="fichiers_charge_fichiers")
         * 
         */         
public function  charge_fichiers(Request $request, $infos ,Mailer $mailer,ValidatorInterface $validator){
    $repositoryFichiersequipes= $this->getDoctrine()
                                 ->getManager()
                                 ->getRepository('App:Fichiersequipes');
     $repositoryEquipesadmin= $this->getDoctrine()
                                  ->getManager()
                                  ->getRepository('App:Equipesadmin');
    $repositoryEdition= $this->getDoctrine()
                             ->getManager()
                             ->getRepository('App:Edition');
    $repositoryUser= $this->getDoctrine()
                          ->getManager()
                          ->getRepository('App:User');
    
    $info=explode("-",$infos);
    $id_equipe=$info[0];
    //$type_fichier=$info[1];
    $phase=$info[1];
    $equipe= $repositoryEquipesadmin->find(['id'=>$id_equipe]);
    
    $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
    $datelimnat=$edition->getDatelimnat();
   
    $dateconnect= new \datetime('now');
            $form1=$this->createForm(ToutfichiersType::class);
            $nom_equipe=$equipe->getTitreProjet();
        $lettre_equipe= $equipe->getLettre();
        $donnees_equipe=$lettre_equipe.' - '.$nom_equipe;
        
    if(!$lettre_equipe){
        $numero_equipe=$equipe->getNumero();
        $nom_equipe=$repositoryEquipesadmin->findOneByNumero(['numero'=>$numero_equipe])->getTitreProjet();
        $donnees_equipe=$numero_equipe.' - '.$nom_equipe;
        }
    $form1->handleRequest($request); 
    if ($form1->isSubmitted() && $form1->isValid()){
           
       /** @var UploadedFile $file */
          $file=$form1->get('fichier')->getData();
   
        $ext=$file->guessExtension();
        
        $num_type_fichier=$form1->get('typefichier')->getData();
       
        if(($num_type_fichier==0) or ($num_type_fichier==1)){
                                        $violations = $validator->validate(
                                       $file,
                                       [
                                           new NotBlank(),
                                           new File([
                                               'maxSize' => '2600k',
                                               'mimeTypes' => [
                                                                   'application/pdf',
                                               ]
                                           ])
                                       ]
                                   );
                                       if ($violations->count() > 0) {

                                       /** @var ConstraintViolation $violation */
                                       $violation = $violations[0];
                                       $this->addFlash('alert', $violation->getMessage());
                                       return $this->redirectToRoute('fichiers_charge_fichiers', [
                                           'infos' => $infos,
                                       ]);
                                   } 
                                     $sourcefile =$file; 
                                     $stringedPDF = file_get_contents($sourcefile, true);
                                   $regex="/\/Page |\/Page\/|\/Page\n|\/Page\r\n|\/Page>>\r/";//selon l'outil de codage en pdf utilisé, les pages ne sont pas repérées de la m^me façon
                                   $pages=preg_match_all($regex, $stringedPDF, $title);

                               if($pages==0){
                                   $regex="/\/Pages /";
                                   $pages=preg_match_all($regex, $stringedPDF, $title);

                               }
                                if ($pages> 20) { //S'il y a plus de 20 pages la procédure est interrompue et on return à la page d'accueil avec un message d'avertissement
                                           $request->getSession()
                                                   ->getFlashBag()
                                                   ->add('alert', 'Votre mémoire contient  '.$pages.' pages. Il n\'a pas pu être accepté, il ne doit pas dépasser 20 page !' ) ; 
                                           return $this->redirectToRoute('fichiers_charge_fichiers',array('infos'=>$infos));
                                       }
                                    }
        if($num_type_fichier==2){
                                               $violations = $validator->validate(
                                                                    $file,
                                                                    [
                                                                        new NotBlank(),
                                                                        new File([
                                                                            'maxSize' => '1000k',
                                                                            'mimeTypes' => [
                                                                                'application/pdf',
                                                                            ],
                                                                             'mimeTypesMessage'=>'Veuillez télécharger un fichier du bon format'
                                                                        ])
                                                                    ]
                                                                );
                                                                    if ($violations->count() > 0) {
                                                                       
                                                                    /** @var ConstraintViolation $violation */
                                                                    $violation = $violations[0];
                                                                    $this->addFlash('alert', $violation->getMessage());
                                                                    return $this->redirectToRoute('fichiers_charge_fichiers', [
                                                                        'infos' => $infos,
                                                                    ]);
                                                                } 
                                                        $sourcefile =$file; //$this->getParameter('app.path.tempdirectory').'/temp.pdf';
                                                         $stringedPDF = file_get_contents($sourcefile, true);
                                                           $regex="/\/Page |\/Page\//";
                                                         $pages=preg_match_all($regex, $stringedPDF, $title);
                                                         if($pages==0){
                                                             $regex="/\/Pages /";
                                                             $pages=preg_match_all($regex, $stringedPDF, $title);
                                                             }
                                                        if ($pages> 1) { //S'il y a plus de 1 page la procédure est interrompue et on return à la page d'accueil avec un message d'avertissement
                                                                             $request->getSession()
                                                                                     ->getFlashBag()
                                                                                     ->add('alert', 'Votre résumé contient  '.$pages.' pages. Il n\'a pas pu être accepté, il ne doit pas dépasser 1 page !' ) ; 
                                                                             return $this->redirectToRoute('fichiers_charge_fichiers',array('infos'=>$infos));
                                                                             }      
                                            }
            if ($num_type_fichier==3){
                                                        if( $dateconnect>$datelimnat){
                                                           $violations = $validator->validate(
                                                                                    $file,
                                                                                    [
                                                                                        new NotBlank(),
                                                                                        new File([
                                                                                            'maxSize' => '10000k',
                                                                                            'mimeTypes' => [
                                                                                                'application/pdf',
                                                                                            ],
                                                                                             'mimeTypesMessage'=>'Veuillez télécharger un fichier du bon format'
                                                                                        ])
                                                                                    ]
                                                                                );
                                                                                    if ($violations->count() > 0) {
                                                                                                                                                                           /** @var ConstraintViolation $violation */
                                                                                    $violation = $violations[0];
                                                                                    $this->addFlash('alert', $violation->getMessage());
                                                                                    return $this->redirectToRoute('fichiers_charge_fichiers', [
                                                                                        'infos' => $infos,
                                                                                    ]);
                                                                                } 
                                                      else{
                                                       $message = 'Le dépôt des diaporamas n\'est possible qu\'après le concours national';
                                                      $request->getSession()
                                                                  ->getFlashBag()
                                                                  ->add('alert', $message ) ; 
                                                          return $this->redirectToRoute('fichiers_charge_fichiers',array('infos'=>$infos));
                                                      }
                        }
            }  
            if($num_type_fichier==4){
                     $violations = $validator->validate( $file,[        new NotBlank(),
                                                                                        new File([
                'maxSize'=> '1024k',
                'mimeTypes' =>['application/pdf', 'application/x-pdf',  "application/msword",
                        'application/octet-stream',
                           'application/vnd.oasis.opendocument.text',
                          ' image/jpeg'],
                'mimeTypesMessage'=>'Veuillez télécharger un fichier du bon format'
                                                                                  ])
                             ]
                             );
                     if ($violations->count() > 0) {
                                                                                        dd($violations);
                                                                                    /** @var ConstraintViolation $violation */
                                                                                    $violation = $violations[0];
                                                                                    $this->addFlash('alert', $violation->getMessage());
                                                                                    return $this->redirectToRoute('fichiers_charge_fichiers', [
                                                                                        'infos' => $infos,
                                                                                    ]);
                                                                                } 
             
            }
            
            $em=$this->getDoctrine()->getManager();
            $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
           
            $qb= $repositoryFichiersequipes->createQueryBuilder('f');
                 $Fichiers=$qb->where('f.equipe=:equipe')
                                                     ->setParameter('equipe',$equipe)
                                                     ->andWhere('f.typefichier =:type')
                                                    ->setParameter('type',$num_type_fichier)
                                                    ->getQuery()->getResult();
                if($Fichiers){
                     if ($file) {
                try {
                    $file->move(
                        $this->getParameter('app.path.tempdirectory'),
                        $file->getClientOriginalName());
                
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }  
                    $id_fichier=$Fichiers[0]->getId();
                    return $this->redirectToRoute('fichiers_confirme_charge_fichier',array('file_equipe'=>$file->getClientOriginalName().'::'.$num_type_fichier.'::'.$id_equipe.'::'.$id_fichier));
                }
                }
                  if (!$Fichiers){
                              
                               $fichier= new Fichiersequipes();
                              $fichier->setTypefichier($num_type_fichier);
                              $fichier->setEdition($edition);
                              $fichier->setEquipe($equipe);
                              $fichier->setNational(0);
                           if ($phase=='national'){
                          $fichier->setNational(1);
                           }
                          $fichier->setFichierFile($file);
                          $em->persist($fichier);
                            $em->flush();
                            $nom_fichier = $fichier->getFichier();
                     
                    $request->getSession()
                            ->getFlashBag()
                            ->add('info', 'Votre fichier renommé selon : '.$nom_fichier.' a bien été déposé. Merci !') ;
               
                $user = $this->getUser();//Afin de rappeler le nom du professeur qui a envoyé le fichier dans le mail
                $bodyMail = $mailer->createBodyMail('emails/confirm_fichier.html.twig', 
                                    ['nom' => $user->getNom(),
                                    'prenom' =>$user->getPrenom(),
                                    'fichier'=>$nom_fichier,
                                    'equipe'=>$equipe->getInfoequipe(),
                                    'typefichier' => $this->getParameter('type_fichier')[$num_type_fichier]]);
                //$mailer->sendMessage('alain.jouvealb@gmail.com', 'info@olymphys.fr', 'Depot du '.$type_fichier.'de l\'équipe '.$equipe->getInfoequipe(),'L\'equipe '. $equipe->getInfoequipe().' a déposé un fichier');
                $centre = $equipe->getCentre();
                if ($centre){
                    $cohorte_centre =$repositoryUser->findByCentrecia(['centrecia'=>$centre]);
                    foreach($cohorte_centre as $individu){
                        $roles = $individu->getRoles();
                        $mailorganisateur='';
                            foreach($roles as $role){
                                if ($role=='ROLE_ORGACIA'){
                                    $mailorganisateur=$individu->getEmail();
                                    //$mailer->sendMessage('webmestre2@olymphys.fr', $mailorganisateur, 'Depot du '.$type_fichier.'de l\'équipe '.$Equipe_choisie->getNumero(),'L\'équipe '.'n°'.$Equipe_choisie->getNumero().':'.$Equipe_choisie->getTitreProjet().' a déposé un fichier');
                                     }
                                }
                            }
                        }
                return $this->redirectToRoute('core_home');     
                }        
    }
             $content = $this
                             ->renderView('adminfichiers\charge_fichier_fichier.html.twig', array('form'=>$form1->createView(),'donnees_equipe'=>$donnees_equipe));
            return new Response($content);                             
 }    
         
        /**
         * @Security("is_granted('ROLE_PROF')")
         * 
         * @Route("/fichiers/afficher_liste_fichiers_prof/,{infos}", name="fichiers_afficher_liste_fichiers_prof")
         * 
         */          
public function afficher_liste_fichiers_prof(Request $request , $infos ){
    $repositoryFichiersequipes= $this->getDoctrine()
                              ->getManager()
                              ->getRepository('App:Fichiersequipes');
    
    $repositoryEquipesadmin= $this->getDoctrine()
                                  ->getManager()
                                  ->getRepository('App:Equipesadmin');
    
      $repositoryEdition= $this->getDoctrine()
                                 ->getManager()
                                 ->getRepository('App:Edition');
    $Infos=explode('-',$infos);
    
    $id_equipe=$Infos[0];
    $concours=$Infos[1];
    $choix=$Infos[2];
    $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
    $datelimcia = $edition->getDatelimcia();
    $datelimnat=$edition->getDatelimnat();
    $dateouverturesite=$edition->getDateouverturesite();
    $dateconnect= new \datetime('now');
       
    $equipe_choisie= $repositoryEquipesadmin->find(['id'=>$id_equipe]);
     $centre=$equipe_choisie->getCentre();
    
    $qb1 =$repositoryFichiersequipes->createQueryBuilder('t')
                             ->LeftJoin('t.equipe', 'e')
                             ->Where('e.id=:id_equipe')
                             ->setParameter('id_equipe', $id_equipe);
    
    $qb2 =$repositoryFichiersequipes->createQueryBuilder('t')    //pour le comité fichiers cia
                             ->LeftJoin('t.equipe', 'e')
                             ->Where('e.id=:id_equipe')
                             ->setParameter('id_equipe', $id_equipe)
                             ->andWhere('t.national =:national')
                             ->setParameter('national', FALSE) ;
    
    $qb3 =$repositoryFichiersequipes->createQueryBuilder('t')  // /pour le comité fichiers cn 
                             ->LeftJoin('t.equipe', 'e')
                             ->Where('e.id=:id_equipe')
                             ->setParameter('id_equipe', $id_equipe)
                             ->andWhere('t.typefichier <:type')
                             ->setParameter('type', 4)
                             ->andWhere('t.national =:national')
                             ->setParameter('national', TRUE) ;
       
    $roles=$this->getUser()->getRoles();
        $role=$roles[0];
                              
      if(($role=='ROLE_PROF') or($role=='ROLE_ORGACIA') or ($role=='ROLE_COMITE') or ($role=='ROLE_SUPER_ADMIN')) {               
        $liste_fichiers=$qb1->getQuery()->getResult();    
       
      }
       if ($role=='ROLE_JURYCIA'){         
           $qb1->andWhere('t.typefichier <:type')
                   ->setParameter('type', 4);
        $liste_fichiers=$qb1->getQuery()->getResult();    
      } 
    if($role=='ROLE_JURY'){
         $liste_fichiers=$qb3->getQuery()->getResult();    
        }
      
    $infoequipe=$equipe_choisie->getInfoequipe();
    if ($equipe_choisie->getSelectionnee()==true ){
        $infoequipe=$equipe_choisie->getInfoequipenat();//pour les comités et jury,inutile pour les prof , ;
     }
    $centre=$equipe_choisie->getCentre()->getCentre();
    $user = $this->getUser();
    
    $i=0;
    
    if (isset($liste_fichiers)){
    foreach($liste_fichiers as $fichier){
        $id=$fichier->getId();
        
        $formBuilder[$i]=$this->get('form.factory')->createNamedBuilder('Form'.$i, FormType::class,$fichier);  
        $formBuilder[$i] ->add('id',  HiddenType::class, ['disabled'=>true, 'label'=>false])
                         ->add('fichier', TextType::class,['disabled'=>true,  'label'=>false])
                         ->add('save', submitType::class);
        $Form[$i]=$formBuilder[$i]->getForm();
        $formtab[$i]=$Form[$i]->createView();
                
        if ($request->isMethod('POST') ) 
            {
            if ($request->request->has('Form'.$i)) {
                $id=$Form[$i]->get('id')->getData();
                $fichier=$repositoryFichiersequipes->find(['id'=>$id]);
                $fichierName=$this->getParameter('app.path.fichiers').'/'.$this->getParameter('type_fichier')[$fichier->getTypefichier()].'/'.$fichier->getFichier();
              if($fichier->getTypefichier()==1){
                  $fichierName=$this->getParameter('app.path.fichiers').'/'.$this->getParameter('type_fichier')[0].'/'.$fichier->getFichier();
              }
                if(null !==$fichierName)
                    {
                    $file=new UploadedFile($fichierName,$fichier->getFichier() ,null,null,true);
                    
                    $response = new BinaryFileResponse($fichierName);
                    $disposition = HeaderUtils::makeDisposition(
                                                    HeaderUtils::DISPOSITION_ATTACHMENT,
                                                    $fichier->getFichier()
                                                    );
                   $response->headers->set('Content-Type', $file->guessExtension());  
                    $response->headers->set('Content-Disposition', $disposition);
                    return $response; 
                    }        
                }
            }
        $i=$i+1;
        }
    }      
            
        if ($request->isMethod('POST') ) 
            {
            if ($request->request->has('FormAll')) {         
                $zipFile = new \ZipArchive();
                $FileName= $equipe_choisie->getCentre()->getCentre().'-Fichiers-eq-'.$equipe_choisie->getNumero().'-'.date('now');
                if ($zipFile->open($FileName, ZipArchive::CREATE) === TRUE){
                   $fichiers= $repositoryFichiersequipes->findByEquipe(['equipe'=>$equipe_choisie]);
                    foreach($liste_fichiers as $fichier){
                         if ($fichier->getTypefichier()==1){
                           $fichierName=$this->getParameter('app.path.fichiers').'/'.$this->getParameter('type_fichier')[0].'/'.$fichier->getFichier();   
                         }
                         else{
                         $fichierName=$this->getParameter('app.path.fichiers').'/'.$this->getParameter('type_fichier')[$fichier->getTypefichier()].'/'.$fichier->getFichier();
                         }
                        if($fichier){
                            $zipFile->addFromString(basename($fichierName),  file_get_contents($fichierName));//voir https://stackoverflow.com/questions/20268025/symfony2-create-and-download-zip-file
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
                    @unlink($FileName);
                    return $response; 
                    }
                }
            }
        if(isset($formtab)){      
            $fichier=new Fichiersequipes();
            $formBuilder=$this->get('form.factory')->createNamedBuilder('FormAll', ListefichiersType::class,$fichier);  
            $formBuilder->add('save',      SubmitType::class );
            $Form=$formBuilder->getForm();
            $formtab[$i]=$Form->createView();//Ajoute le bouton  tout télécharger
            ($formtab);   
            $content = $this
                          ->renderView('adminfichiers\affiche_liste_fichiers_prof.html.twig', array('formtab'=>$formtab,
                                                        'equipe'=>$equipe_choisie, 'centre' =>$equipe_choisie->getCentre(),'concours'=>$concours, 'edition'=>$edition, 'choix'=>$choix, 'role'=>$role)
                                            ); 
            return new Response($content); 
            }
        if(!isset($formtab)){
         
                if ($role=='ROLE_PROF'){
                    $num_equipe='n° '.$equipe_choisie->getNumero();
                    if ($concours=='national'){
                         $num_equipe=$equipe_choisie->getLettre();
                    }
                    $request->getSession()
                    ->getFlashBag()
                    ->add('info', 'Il n\'y a pas encore de fichier déposé pour l\'equipe '.$num_equipe) ;
                     return $this->redirectToRoute('core_home');   }
                
                 if ($role=='ROLE_COMITE' || $role=='ROLE_SUPER_ADMIN' || $role=='ROLE_JURY'){
                     if ($concours=='interacadémique'){
                     $request->getSession()
                    ->getFlashBag()
                    ->add('info', 'Il n\'y a pas encore de fichier déposé pour l\'equipe'.$equipe_choisie->getNumero()) ;   
                     return $this->redirectToRoute('fichiers_choix_equipe', array('choix'=>$centre)); 
                     }
                     if ($concours=='national'){
                      $request->getSession()
                    ->getFlashBag()
                    ->add('info', 'Il n\'y a pas encore de fichier déposé pour l\'equipe '.$equipe_choisie->getLettre()) ;
                         
                         
                     return $this->redirectToRoute('fichiers_choix_equipe', array('choix'=>'liste_cn_comite')); 
                 }}
                     if ($role=='ROLE_ORGACIA' || $role=='ROLE_JURYCIA'){
                         $request->getSession()
                    ->getFlashBag()
                    ->add('info', 'Il n\'y a pas encore de fichier déposé pour l\'equipe n°'.$equipe_choisie->getNumero()) ;
                     return $this->redirectToRoute('fichiers_choix_equipe', array('choix'=>'centre')  );
                             }
            }     
}
 
       /**
         * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
         * 
         * @Route("/fichiers/choixedition,{num_type_fichier}", name="fichiers_choixedition")
         * 
         */    
        public function choixedition(Request $request, $num_type_fichier)
        {
            $repositoryEdition= $this->getDoctrine()
		->getManager()
		->getRepository('App:Edition');
            $qb=$repositoryEdition->createQueryBuilder('e')
                                      ->orderBy('e.edition', 'DESC');

            
            $Editions = $qb->getQuery()->getResult();
             return $this->render('adminfichiers/choix_edition.html.twig', [
                'editions' => $Editions, 'num_type_fichier'=>$num_type_fichier]);
        }
        /**
         *@IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
         * 
         * @Route("/fichiers/voirfichiers,{editionId_concours}", name="fichiers_voirfichiers")
         * 
         */    
        public function voirfichiers(Request $request, $editionId_concours)
        {   $editionconcours=explode('-',$editionId_concours);
        
            $IdEdition = $editionconcours[0];
            $concours = $editionconcours[1];
            $num_type_fichier=$editionconcours[2];
            $repositoryEdition = $this->getDoctrine()
		->getManager()
		->getRepository('App:Edition');
             $repositoryFichiersequipes = $this->getDoctrine()
		->getManager()
		->getRepository('App:Fichiersequipes');    
              $repositoryEquipesadmin = $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');    
              
            $edition = $repositoryEdition->find(['id'=>$IdEdition]);
            
            if ($concours=='cia'){
               $qb1= $repositoryFichiersequipes->createQueryBuilder('m')
                                      ->leftJoin('m.equipe', 'e')
                                      ->where('e.selectionnee=:selectionnee')
                                      ->orderBy('e.lyceeAcademie', 'ASC')
                                     ->setParameter('selectionnee',FALSE)
                                      ->andWhere('m.edition=:edition')
                                      ->setParameter('edition', $edition)
                                      ->andWhere('m.typefichier <:type')
                                       ->setParameter('type',3);
                                
               $fichierstab=$qb1->getQuery()->getResult();
                $qb2= $repositoryEquipesadmin->createQueryBuilder('e')
                                      ->where('e.selectionnee=:selectionnee')
                                      ->setParameter('selectionnee',FALSE)
                                      ->orderBy('e.lyceeAcademie', 'ASC');
                                     
                  $listeequipe=$qb2->getQuery()->getResult();
          }
            if ($concours=='cn'){
                $qb1= $repositoryFichiersequipes->createQueryBuilder('m')
                                      ->leftJoin('m.equipe', 'e')
                                      ->orderBy('e.lettre', 'ASC')
                                      ->andWhere('m.edition=:edition')
                                      ->setParameter('edition', $edition);
                 if($num_type_fichier==0){       
                                     $qb1->andWhere('m.typefichier <:type')
                                     ->setParameter('type',3);
                 }
                  if($num_type_fichier==3){       
                                     $qb1->andWhere('m.typefichier =:type')
                                     ->setParameter('type',$num_type_fichier);
                 }
               $fichierstab=$qb1->getQuery()->getResult();
                             
               
               $qb2= $repositoryEquipesadmin->createQueryBuilder('e')
                                      ->where('e.selectionnee=:selectionnee')
                                      ->setParameter('selectionnee',TRUE)
                                      ->orderBy('e.lettre', 'ASC');
                                      
                  $listeequipe=$qb2->getQuery()->getResult();
            }
             
             if($listeequipe){
                
             $i=0;
            foreach($listeequipe as $equipe){
                if ($fichierstab){
               $j=0;
                foreach($fichierstab as $fichier){
                    
                
                    if ($fichier->getEquipe()==$equipe){
                        $fichiersEquipe[$i][$j]=$fichier;   
                            $j++;
                       }
                } 
            }
             $i++;
              }
              
            if (isset($fichiersEquipe)){
              $content = $this
                          ->renderView('adminfichiers\affiche_memoires.html.twig',
                                  array('fichiersequipes'=>$fichiersEquipe,
                                           'edition'=>$edition, 
                                            'concours'=>$concours
                                            )); 
            return new Response($content); 
            }
              else
        {$request->getSession()
                    ->getFlashBag()
                    ->add('info','Pas de fichier déposé à ce jour pour cette édition  ') ;  
       return $this->redirectToRoute('fichiers_choixedition',array('num_type_fichier'=>$num_type_fichier));  
        }
        }
}
   /**
         *@IsGranted("ROLE_SUPER_ADMIN")
         * 
         * @Route("/fichiers/transpose_donnees,", name="fichiers_transpose_donnees")
         * 
         */    
public function transpose_donnees(Request $request){
    
    $repositoryFichiersequipes = $this->getDoctrine()
		->getManager()
		->getRepository('App:Fichiersequipes'); 
    $repositoryMemoires = $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoires'); 
     $repositoryMemoiresinter = $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoiresinter');  
      $repositoryResumes = $this->getDoctrine()
		->getManager()
		->getRepository('App:Resumes');  
       $repositoryFichessecur = $this->getDoctrine()
		->getManager()
		->getRepository('App:Fichessecur');  
        $repositoryPresentations = $this->getDoctrine()
		->getManager()
		->getRepository('App:Presentation');  
    $repositoryEquipesadmin = $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
    
    $liste_memoires=$repositoryMemoires->findAll();
     $liste_memoiresinter=$repositoryMemoiresinter->findAll();
    $liste_resumes=$repositoryResumes->findAll();
    $liste_Fichessecur=$repositoryFichessecur->findAll();
    $liste_Presentations=$repositoryPresentations->findAll();
    $File_system=new FileSystem();
    $em=$this->getDoctrine()->getManager();
   if (isset($liste_memoires)){
       foreach($liste_memoires as $memoire){
           $Fichier=new Fichiersequipes();
           $Fichier->setEdition($memoire->getEdition());
           $Fichier->setNational(1);
           $Fichier->setEquipe($memoire->getEquipe());
           if ($memoire->getAnnexe()==false){
                $Fichier->setTypefichier(0);
           }else
           {$Fichier->setTypefichier(1);               
           }
         $File_system->copy($this->getParameter('app.path.memoires_nat').'/'.$memoire->getMemoire(),$this->getParameter('app.path.tempdirectory').'/'.$memoire->getMemoire()); 
           $fichier =new UploadedFile($this->getParameter('app.path.tempdirectory').'/'.$memoire->getMemoire(),$memoire->getMemoire(),null,null,true);
         
           $Fichier->setFichierFile($fichier);
           
           $em->persist($Fichier);
           $em->flush();
                
          }       
        }   
    
     if (isset($liste_memoiresinter)){
       foreach($liste_memoiresinter as $memoire){
           $Fichier=new Fichiersequipes();
           $Fichier->setEdition($memoire->getEdition());
           $Fichier->setNational(0);
           $Fichier->setEquipe($memoire->getEquipe());
           if ($memoire->getAnnexe()==false){
                $Fichier->setTypefichier(0);
           }else
           {$Fichier->setTypefichier(1);               
           }
           $File_system->copy($this->getParameter('app.path.memoires_inter').'/'.$memoire->getMemoire(),$this->getParameter('app.path.tempdirectory').'/'.$memoire->getMemoire());
           $fichier =new UploadedFile($this->getParameter('app.path.tempdirectory').'/'.$memoire->getMemoire(),$memoire->getMemoire(),null,null,true);
         
           $Fichier->setFichierFile($fichier);
           
           $em->persist($Fichier);
           $em->flush();
                
          }       
        }
    if (isset($liste_resumes)){
       foreach($liste_resumes as $resume){
           $Fichier=new Fichiersequipes();
           $Fichier->setEdition($resume->getEdition());
           $Fichier->setNational(0);
           $Fichier->setEquipe($resume->getEquipe());
           $Fichier->setTypefichier(2);          
           $File_system->copy($this->getParameter('app.path.resumes').'/'.$resume->getResume(),$this->getParameter('app.path.tempdirectory').'/'.$resume->getResume());
           $fichier =new UploadedFile($this->getParameter('app.path.tempdirectory').'/'.$resume->getResume(),$resume->getResume(),null,null,true);
         
           $Fichier->setFichierFile($fichier);
           
           $em->persist($Fichier);
           $em->flush();
                
          }       
        }    
         if (isset($liste_Fichessecur)){
       foreach($liste_Fichessecur as $fiche){
           $Fichier=new Fichiersequipes();
           $Fichier->setEdition($fiche->getEdition());
           $Fichier->setNational(0);
           $Fichier->setEquipe($fiche->getEquipe());
           $Fichier->setTypefichier(4);          
           $File_system->copy($this->getParameter('app.path.fichessecur').'/'.$fiche->getFiche(),$this->getParameter('app.path.tempdirectory').'/'.$fiche->getFiche());
           $fichier =new UploadedFile($this->getParameter('app.path.tempdirectory').'/'.$fiche->getFiche(),$fiche->getFiche(),null,null,true);
         
           $Fichier->setFichierFile($fichier);
           
           $em->persist($Fichier);
           $em->flush();
                
          }       
        }  
         if (isset($liste_Presentations)){
             
       foreach($liste_Presentations as $presentation){
           $Fichier=new Fichiersequipes();
           $Fichier->setEdition($presentation->getEdition());
           $Fichier->setNational(1);
           $Fichier->setEquipe($presentation->getEquipe());
           $Fichier->setTypefichier(3);          
           $File_system->copy($this->getParameter('app.path.presentations').'/'.$presentation->getPresentation(),$this->getParameter('app.path.tempdirectory').'/'.$presentation->getPresentation());
           $fichier =new UploadedFile($this->getParameter('app.path.tempdirectory').'/'.$presentation->getPresentation(),$presentation->getPresentation(),null,null,true);
         
           $Fichier->setFichierFile($fichier);
           
           $em->persist($Fichier);
           $em->flush();
                
          }       
          return $this->redirectToRoute('core_home');
        }  
        
        
        
    
}



}
        
                           
                   
  
