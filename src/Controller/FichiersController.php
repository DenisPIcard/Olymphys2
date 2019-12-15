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

use App\Form\MemoiresType;
use App\Form\ResumesType;
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
use App\Entity\Resumes;
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
//use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
//use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
//use Symfony\Component\HttpFoundation\File\File;
//use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request ;
use Symfony\Component\HttpFoundation\RedirectResponse ;
use Symfony\Component\HttpFoundation\Response ;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\Form\AbstractType;



use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ZipArchive;
    
class FichiersController extends AbstractController
{
    
    
         public $Fiche;
         public $Equipe_choisie;
         //public $Fichesecur;
         
         /**
         * @Route("memoires", name="memoires")
         * 
         */
             public function memoires(Request $request)
    {
      
        $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
        $repositoryCentrescia= $this->getDoctrine()
		->getManager()
		->getRepository('App:Centrescia');
        $repositoryMemoiresinter= $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoiresinter');
        $repositoryResumes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Resumes');
        $repositoryFichessecur= $this->getDoctrine()
		->getManager()
		->getRepository('App:Fichessecur');
        $listecentres=$repositoryCentrescia->findAll();
        if($listecentres){
            $content = $this
                 ->renderView('adminfichiers\choix_centre_jury.html.twig', array('liste_centres'=>$listecentres)
                                );
            return new Response($content);  
        }
 
    }
    
         /**
         * @Route("/fichiers/liste_equipes,{centre}", name="liste_equipes")
         * 
         */           
    public function liste_equipes(Request $request, $centre) {
        $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
        $repositoryCentrescia= $this->getDoctrine()
		->getManager()
		->getRepository('App:Centrescia');
        $repositoryMemoiresinter= $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoiresinter');
        $repositoryResumes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Resumes');
        $repositoryFichessecur= $this->getDoctrine()
		->getManager()
		->getRepository('App:Fichessecur');
        $centrecia=$repositoryCentrescia->find(['id'=>$centre]);
        $ville=$centrecia->getCentre();
        $liste_equipes= $repositoryEquipesadmin->findByCentre(['centre'=>$centrecia]); 
        if($liste_equipes){
          $i=0;
          foreach($liste_equipes as $equipe){
            $nombre_memoires= count($repositoryMemoiresinter->findByEquipe(['equipe'=>$equipe]));
            $nombre_fiche= count($repositoryFichessecur->findByEquipe(['equipe'=>$equipe]));
            $nombre_resume= count($repositoryResumes->findByEquipe(['equipe'=>$equipe]));
            $nombre_fichiers[$i] = $nombre_memoires+ $nombre_fiche+$nombre_resume;            
            $i=$i+1;
             
            }
            $content = $this
                 ->renderView('adminfichiers\equipe_liste.html.twig', array(
                     'liste_equipes'=>$liste_equipes, 
                     'nombre_fichiers'=>$nombre_fichiers,
                     'centre'=>$ville,
                     'centrecia'=>$centrecia)
                                );
            return new Response($content);  
        }
        else{ 
             $request->getSession()
                         ->getFlashBag()
                         ->add('info', 'Il n\'y a pas encore de fichier déposé pour le centre de'.$ville) ;
                   
             return $this->redirectToRoute('memoires');   
        }
    }
         
         /**
         * @Route("liste_fichiers/{numero_equipe}", name="liste_fichiers")
         * 
         */          
         public function liste_fichiers(Request $request , $numero_equipe){
             $repositoryMemoires= $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoires');
             $repositoryMemoiresinter= $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoiresinter');
             $repositoryMemoiresnat= $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoires');
             $repositoryEquipes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipes');
             $repositoryEquipesadmin= $this->getDoctrine()
                ->getManager()
		->getRepository('App:Equipesadmin');
             $repositoryResumes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Resumes');
             $repositoryFichessecur= $this->getDoctrine()
		->getManager()
		->getRepository('App:Fichessecur');
             
             $equipe_choisie= $repositoryEquipesadmin->findOneByNumero(['numero'=>$numero_equipe]);
             $memoiresinter= $repositoryMemoiresinter->findByEquipe(['equipe'=>$equipe_choisie]);
             $memoiresnat =   $repositoryMemoires->findByEquipe(['equipe'=>$equipe_choisie]);
             $fiche_securit = $repositoryFichessecur->findOneByEquipe(['equipe'=>$equipe_choisie]);    
             $resume= $repositoryResumes->findOneByEquipe(['equipe'=>$equipe_choisie]); 
             $centre=$equipe_choisie->getCentre()->getId();
           
             $i=0;
             foreach($memoiresinter as $memoireinter){
                $id=$memoireinter->getId();
                $formBuilder[$i]=$this->get('form.factory')->createNamedBuilder('Form'.$i, FormType::class,$memoireinter);  
                $formBuilder[$i] ->add('id',  HiddenType::class, ['disabled'=>true, 'label'=>false])
                                            ->add('memoire', TextType::class,['disabled'=>true,  'label'=>false])
                                            ->add('save', submitType::class);
                $Form[$i]=$formBuilder[$i]->getForm();
                $formtab[$i]=$Form[$i]->createView();
                if ($request->isMethod('POST') ) 
                {
                    if ($request->request->has('Form'.$i)) {
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
                            return $response;    
                             }            
                    }
                }
                $i=$i+1;
             }
                  
            foreach($memoiresnat as $memoirenat){
                $id=$memoirenat->getId();
                $formBuilder[$i]=$this->get('form.factory')->createNamedBuilder('Form'.$id, FormType::class,$memoirenat);  
                $formBuilder[$i] ->add('id',  HiddenType::class, ['disabled'=>true, 'label'=>false])
                                            ->add('memoire', TextType::class,['disabled'=>true,  'label'=>false])
                                            ->add('save',SubmitType::class);
                $Form[$i]=$formBuilder[$i]->getForm();
                $formtab[$i]=$Form[$i]->createView();
                   
                if ($request->isMethod('POST') ) 
                {
                     if ($request->request->has('Form'.$i)) {
                        $id=$Form[$i]->get('id')->getData();
                        $memoirenat=$repositoryMemoiresnat->find(['id'=>$i]);
                        $memoireName=$this->getParameter('repertoire_memoire_national').'/'.$memoirenat->getMemoire();
                        if(null !==$memoireName)
                        {
                            $response = new BinaryFileResponse($memoireName);
                            $disposition = HeaderUtils::makeDisposition(
                                    HeaderUtils::DISPOSITION_ATTACHMENT,
                                    $memoirenat->getMemoire()
                                          );
                            $response->headers->set('Content-Type', 'application/pdf'); 
                            $response->headers->set('Content-Disposition', $disposition);
                            return $response; 
                         }
                                 
                    }
                                                       
                }
                $i=$i+1;
            }
            if ($fiche_securit){
                $id = $fiche_securit->getId();
                $formBuilder[$i]=$this->get('form.factory')->createNamedBuilder('Form'.$i, FormType::class,$fiche_securit);  
                $formBuilder[$i] ->add('id',  HiddenType::class, ['disabled'=>true, 'label'=>false])
                                 ->add('memoire',TextType::class, ['disabled'=>true,  'label'=>false, 'data' =>$fiche_securit->getFiche(), 'mapped'=>false])
                                 ->add('save', submitType::class);
                $Form[$i]=$formBuilder[$i]->getForm();
                $formtab[$i]=$Form[$i]->createView();
                if ($request->isMethod('POST') ) 
                {
                    if ($request->request->has('Form'.$i)) {
                        $id=$Form[$i]->get('id')->getData();
                        $fiche_securit=$repositoryFichessecur->find(['id'=>$id]);
                        $FicheName=$this->getParameter('repertoire_fiches_securite').'/'.$fiche_securit->getFiche();
                        if(null !==$FicheName)
                        {  
                            $file=new File($FicheName);
                            $response = new BinaryFileResponse($FicheName);
                            $disposition = HeaderUtils::makeDisposition(
                                    HeaderUtils::DISPOSITION_ATTACHMENT,
                                    $fiche_securit->getFiche()
                                );
                            $response->headers->set('Content-Type', $file->guessExtension()); 
                            $response->headers->set('Content-Disposition', $disposition);
                            return $response; 
                         } 
                    }
                }
            $i=$i+1;}
            if ($resume){
                $id = $resume->getId();
                $formBuilder[$i]=$this->get('form.factory')->createNamedBuilder('Form'.$i, FormType::class,$resume);  
                $formBuilder[$i] ->add('id',  HiddenType::class, ['disabled'=>true, 'label'=>false])
                                            ->add('memoire', TextType::class,['disabled'=>true,  'label'=>false,'data'=>$resume->getResume(), 'mapped'=>false])
                                           ->add('save', submitType::class);
                $Form[$i]=$formBuilder[$i]->getForm();
                $formtab[$i]=$Form[$i]->createView();
                if ($request->isMethod('POST') ) 
                {
                   if ($request->request->has('Form'.$i)) {
                       $id=$Form[$i]->get('id')->getData();
                       $resume=$repositoryResumes->find(['id'=>$id]);
                       $resumeName=$this->getParameter('repertoire_resumes').'/'.$resume->getResume();
                       if(null !==$resumeName)
                       {
                           $response = new BinaryFileResponse($resumeName);
                           $disposition = HeaderUtils::makeDisposition(
                                        HeaderUtils::DISPOSITION_ATTACHMENT,
                                            $resume->getResume()
                               );
                            $response->headers->set('Content-Type', 'application/pdf'); 
                            $response->headers->set('Content-Disposition', $disposition);
                            return $response; 
                        }
                    }
                }
            $i=$i+1;
            }
            if ($request->isMethod('POST') ) 
            {
               if ($request->request->has('FormAll')) {         
                   $zipFile = new \ZipArchive();
                   $FileName= $equipe_choisie->getCentre()->getCentre().'-Fichiers-eq-'.$equipe_choisie->getNumero().'-'.date('now');
                   if ($zipFile->open($FileName, ZipArchive::CREATE) === TRUE){
                       $memoires= $repositoryMemoiresinter->findByEquipe(['equipe'=>$equipe_choisie]);
                       foreach($memoires as $memoire){
                           $Memoire=$this->getParameter('repertoire_memoire_interacademiques').'/'.$memoire->getMemoire();
                           if($Memoire){
                               $zipFile->addFromString(basename($Memoire),  file_get_contents($Memoire));}//voir https://stackoverflow.com/questions/20268025/symfony2-create-and-download-zip-file
                        }
                        $resume=$repositoryResumes->findOneByEquipe(['equipe'=>$equipe_choisie]);
                        $fichesecurit=$repositoryFichessecur->findOneByEquipe(['equipe'=>$equipe_choisie]);
                        if ($resume){
                            $Resume=$this->getParameter('repertoire_resumes').'/'.$resume->getResume();
                            if ($Resume){
                               $zipFile->addFromString(basename($Resume),  file_get_contents($Resume));}
                        }
                        if ($fichesecurit){
                            $fichesecur=$this->getParameter('repertoire_fiches_securite').'/'.$fichesecurit->getFiche();
                            if($fichesecur){
                                $zipFile->addFromString(basename($fichesecur),  file_get_contents($fichesecur));}
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
                $fichier=new Memoiresinter();
                $formBuilder=$this->get('form.factory')->createNamedBuilder('FormAll', ListmemoiresinterallType::class,$fichier);  
                $formBuilder->add('save',      SubmitType::class );
                                        $Form=$formBuilder->getForm();
                $formtab[$i]=$Form->createView();//Ajoute le bouton  tout télécharger
                ($formtab);   
                $content = $this
                        ->renderView('adminfichiers\liste_fichiers.html.twig', array('formtab'=>$formtab,
                            'infoequipe'=>$equipe_choisie->getInfoequipe(), 'centrecia' =>$equipe_choisie->getCentre())
                        ); 
                return new Response($content); 
            }
            if(!isset($formtab)){
                $request->getSession()
                         ->getFlashBag()
                         ->add('info', 'Il n\'y a pas encore de fichier déposé pour l\'equipe n°'.$numero_equipe) ;
                return $this->redirectToRoute('liste_equipes',array('centre'=>$centre)); 
            }
        }
 
    
        /**
         * @Security("is_granted('ROLE_JURYCIA')")
         * 
         * @Route("/fichiers/afficherlesmemoiresinter_orgacia", name="fichiers_afficherlesmemoiresinter_orgacia")
         * 
         */
    public function afficherlesmemoiresinter_orgacia(Request $request)
    {
      
        $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
        $repositoryCentrescia= $this->getDoctrine()
		->getManager()
		->getRepository('App:Centrescia');
        $repositoryMemoiresinter= $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoiresinter');
        $repositoryResumes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Resumes');
        $repositoryFichessecur= $this->getDoctrine()
		->getManager()
		->getRepository('App:Fichessecur');
        $user = $this->getUser();
        $organisateurcia=$user->getUserName();
        
        
         $centre = $user->getCentrecia();
         if ($centre){ //c'est un organisateur on va afficher les équipes
            $centreville = $user->getCentrecia()->getCentre();            
            $liste_equipes= $repositoryEquipesadmin->findByCentre(['centre'=>$centre]);            
            $i=0;
            foreach($liste_equipes as $equipe){
                $nombre_memoires= count($repositoryMemoiresinter->findByEquipe(['equipe'=>$equipe]));
                $nombre_fiche= count($repositoryFichessecur->findByEquipe(['equipe'=>$equipe]));
                $nombre_resume= count($repositoryResumes->findByEquipe(['equipe'=>$equipe]));
                $nombre_fichiers[$i] = $nombre_memoires+ $nombre_fiche+$nombre_resume;            
                $i=$i+1;
             
            }
                ;//Le nom des organisateurs cia et membres jury est générique : celui du centre donc  même session pour tous
        
            if($liste_equipes){
                $content = $this
                    ->renderView('adminfichiers\choix_equipe_liste_inter_orgacia.html.twig', 
                         array('liste_equipes'=>$liste_equipes,
                           'nombre_fichiers'=>$nombre_fichiers, 
                           'centre'=>$centreville)
                                );
        
                  }
            else{
                $request->getSession()
                         ->getFlashBag()
                         ->add('info', 'Il n\'y a pas encore de fichier déposé pour votre centre.') ;
                    
                return $this->redirectToRoute('fichiers_afficherlesmemoiresinter_orgacia');   
            }
            return new Response($content);  
             
         }
         if(!$centre){      //c'est un membre du comité : on lui fait choisir le centre 
             $listecentres=$repositoryCentrescia->findAll();
             if($listecentres){
                  
                 $content = $this
                 ->renderView('adminfichiers\choix_centre_liste_comite.html.twig', array('liste_centres'=>$listecentres)
                                );
               return new Response($content);  
             } 
         }   
     }
         
       /**
         * @Security("is_granted('ROLE_COMITE')")
         * 
         * @Route("/fichiers/afficher_liste_equipe_comite,{centre}", name="fichiers_afficher_liste_equipe_comite")
         * 
         */           
public function afficher_liste_equipe_comite(Request $request, $centre) {
    $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
    $repositoryCentrescia= $this->getDoctrine()
		->getManager()
		->getRepository('App:Centrescia');
    $repositoryMemoiresinter= $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoiresinter');
        $repositoryResumes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Resumes');
        $repositoryFichessecur= $this->getDoctrine()
		->getManager()
		->getRepository('App:Fichessecur');
       $centrecia=$repositoryCentrescia->find(['id'=>$centre]);
     $ville=$centrecia->getCentre();
      $liste_equipes= $repositoryEquipesadmin->findByCentre(['centre'=>$centrecia]);            
                 
                ;//Le nom des organisateurs cia et membres jury est générique : celui du centre donc  même session pour tous
        
         if($liste_equipes){
             $i=0;
         foreach($liste_equipes as $equipe){
             $nombre_memoires= count($repositoryMemoiresinter->findByEquipe(['equipe'=>$equipe]));
             $nombre_fiche= count($repositoryFichessecur->findByEquipe(['equipe'=>$equipe]));
            $nombre_resume= count($repositoryResumes->findByEquipe(['equipe'=>$equipe]));
             $nombre_fichiers[$i] = $nombre_memoires+ $nombre_fiche+$nombre_resume;            
             $i=$i+1;
             
         }
             $content = $this
                 ->renderView('adminfichiers\choix_equipe_liste_inter_orgacia.html.twig', array(
                     'liste_equipes'=>$liste_equipes, 
                     'nombre_fichiers'=>$nombre_fichiers,
                     'centre'=>$ville,
                     'centrecia'=>$centrecia)
                                );
              return new Response($content);  
                          }
         else{ 
                     $request->getSession()
                         ->getFlashBag()
                         ->add('info', 'Il n\'y a pas encore de fichier déposé pour le centre de'.$ville) ;
                   
                    return $this->redirectToRoute('fichiers_afficherlesmemoiresinter_orgacia');   
                     }
   
}
        /**
         * @Security("is_granted('ROLE_PROF')")
         * 
         * @Route("/fichiers/choix_equipe_prof,{type_fichier}", name="fichiers_choix_equipe_prof")
         * 
         */           
public function choix_equipe_prof(Request $request, $type_fichier) {
    $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
   
        $repositoryEdition= $this->getDoctrine()
		->getManager()
		->getRepository('App:Edition');
        
         $user = $this->getUser();
             $professeur=$user->getId();
        
        $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
            
            $datelimcia = $edition->getDatelimcia();
            $datelimnat=$edition->getDatelimnat();
            $dateouverturesite=$edition->getDateouverturesite();
             $dateconnect= new \datetime('now');
            
            
            
        if (($dateconnect>$datelimcia) and ($dateconnect<$datelimnat)) {
                  $phase='national';
                                 
                    $qb1 =$repositoryEquipesadmin->createQueryBuilder('t')
                             ->where('t.idProf1=:professeur')
                             ->orwhere('t.idProf2=:professeur')
	           ->setParameter('professeur', $professeur)
                            ->andWhere('t.selectionnee  = TRUE');
             $liste_equipes=$qb1->getQuery()->getResult();    
             
                 
        }
         if (($dateconnect>$dateouverturesite) and ($dateconnect<$datelimcia)) {
                         $phase= 'interacadémique';
                        
                  
                   $qb2 =$repositoryEquipesadmin->createQueryBuilder('t')
                             ->where('t.idProf1=:professeur')
                             ->orwhere('t.idProf2=:professeur')
	           ->setParameter('professeur', $professeur);
                   
                $liste_equipes=$qb2->getQuery()->getResult();   
         }
         if($liste_equipes) {
             $content = $this
                 ->renderView('adminfichiers\choix_equipe_prof.html.twig', array(
                     'liste_equipes'=>$liste_equipes, 'type_fichier'=>$type_fichier, 'phase'=>$phase, 'professeur'=>$user
                    )
                                );
              return new Response($content);  
             
             
             
             
         }   
         else{ 
                     $request->getSession()
                         ->getFlashBag()
                         ->add('info', 'Le site n\'est pas encore prêt pour une saisie des mémoires ou vous n\'avez pas d\'équipes inscrite pour le concours '. $phase.'.') ;
                   
                    return $this->redirectToRoute('core_home');   
                     }
   
}            
        
 
       /**
         * @Security("is_granted('ROLE_ORGACIA')")
         * 
         * @Route("/fichiers/depose_memoire_orgacia", name="fichiers_depose_memoire_orgacia")
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
    $repositoryEdition = $this->getDoctrine()
                                   ->getManager()
                                   ->getRepository('App:Edition');
               $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
   $user = $this->getUser();
    //
    $centrecia=$user->getCentrecia();
    if ($centrecia){
     $centre=$user->getCentrecia()->getCentre();   
        
    $qb1 =$repositoryEquipesadmin->createQueryBuilder('e')
                             ->where('e.centre=:centre')
	           ->setParameter('centre',  $centrecia);
     $equipes=$qb1->getQuery()->getResult();
    }
    if(!$centrecia){
         $qb1 =$repositoryEquipesadmin->createQueryBuilder('e')
                   ->orderBy('e.centre', 'ASC') ;
          $equipes=$qb1->getQuery()->getResult();
        $centre='comite';
    }
   
             
              $Memoire=new Memoiresinter();
              $FormBuilder1= $this->get('form.factory')->createBuilder(FormType::class, $Memoire);
              $FormBuilder1
                     ->add('Equipe',EntityType::class,[
                                       'class' => 'App:Equipesadmin',
                                         'query_builder' => $qb1,
                                       'choice_label'=>'getInfoequipe',
                                        'label' => ' Choisir une équipe .',
                                       'mapped'=>false,
                                         ])     
                ->add('memoireFile', FileType::class, [
                                'label' => 'Choisir le mémoire de votre équipe  (de type PDF de taille inférieure à 2,5 M , 20 pages maxi)',
                         // 'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // everytime you edit the Product details
                'required' => false,
                 
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                 //'constraints' => [
                 //   new File([
                   //     'maxSize' => '2600k',
                   //     'mimeTypes' => [
                  //          'application/pdf',
                  //          'application/x-pdf',
                  //      ] ,
                  //      'mimeTypesMessage' => 'Refusé : le document doit être au format pdf et de taille inférieure à 2,5M, avoir au plus 20 pages' ,
                  //      ])]
              ])
              ->add('annexe', CheckboxType::class,['label'=>'Cliquez ici si c\est une annexe' , 'required' =>false, 'mapped' => false])
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
                                $memoire->setEdition($edition);
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
                        $nouveau_memoire->setEdition($edition);
                         $nouveau_memoire->setMemoireFile($memoire_file);//utilisation de vichuploader :  le memoire ou l' annexe seront enregistrés en même temps 
                         $em->persist( $nouveau_memoire);
                        $em->flush();
                          }
                          $request->getSession()
                         ->getFlashBag()
                         ->add('info', 'Le fichier  a bien été déposé. Merci !') ;
                          
                  }
                  
                  
                         return $this->redirectToRoute('fichiers_depose_memoire_orgacia');
                  
                  
                  
                  
              }
    
    
    
    
    
    $content = $this ->renderView('adminfichiers\charge_memoire_inter_orgacia.html.twig', array('form'=>$form2->createView(),'centre'=>$centre));
	return new Response($content);   
    
    
}



       /**
         * @Security("is_granted('ROLE_PROF')")
         * 
         * @Route("/fichiers/charge_fiche_securite_resume/{type_fichier}", name="fichiers_charge_fiche_securite_resume")
         * 
         */
public function charge_fiche_securite_resume(Request $request, $type_fichier)//Pas distinction entre résumé cia et nationaux, idem pour les fiches sécurités
{           
             $repositoryEdition= $this->getDoctrine()
		->getManager()
		->getRepository('App:Edition');
             $repositoryTotalequipes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Totalequipes');
            
             $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
             
            $dates=$repositoryEdition->find(1);
            $datelimcia = $dates->getDatelimcia();
            $datelimnat=$dates->getDatelimnat();
            $dateouverturesite=$dates->getDateouverturesite();
           
          $professeur='';
          $centre='';
            //recupétation équipe(s) du prof1
           $user = $this->getUser();
           $roles=$user->getRoles();
           foreach ($roles as $role){
               if($role    =='ROLE_PROF'){
            $professeur=$user->getId();
               }
            if ($role =='ROLE_ORGACIA'){
            $centre=$user->getCentrecia()->getCentre();
            $centrecia=$user->getCentrecia();
           }}
            $fileName='';
            $lettre_equipe_choisie = '';
            $nom_equipe='';
           
           //$form=$this->CreateForm(MemoiresType::class,$Memoire);
            
              
              $dateconnect= new \datetime('now');
              if (($dateconnect>$datelimcia) and ($dateconnect<$datelimnat)) {//Pour ne sélectionner que les équipes sélectionnées d'un prof
                 $phase='national' ;
                  $equipe=new Equipesadmin(); 
                  $FormBuilder2= $this->get('form.factory')->createBuilder(FormType::class, $equipe);
                  $lettreexiste='';
                    $qb1 =$repositoryEquipesadmin->createQueryBuilder('t')
                             ->where('t.idProf1=:professeur')
                             ->orwhere('t.idProf2=:professeur')
                            ->setParameter('professeur', $professeur)
                            ->andWhere('t.selectionnee  = TRUE');
                              
	           
             $equipes_prof=$qb1->getQuery()->getResult();              
            //$Totalequipe= $qb->getQuery()->getResult();
            
            
                           
                                  
                  $FormBuilder2->add('lettre',EntityType::class,[
                                       'class' => 'App:Equipesadmin',
                                         'query_builder' => $qb1,
                                       'choice_label'=>'getInfoEquipe',
                                        'label' => 'Choisir une équipe :',
                                       
                                         ])
                                   ->add('OK', SubmitType::class);
              }
              if (($dateconnect>$dateouverturesite) and ($dateconnect<$datelimcia)) {//Poiur  ne sélectionner toutes les équipes d'un prof
                  $phase='interacadémique';
                        $equipe=new Equipesadmin(); 
                        $FormBuilder2= $this->get('form.factory')->createBuilder(FormType::class, $equipe);
                  
                  
                  
                if(   $professeur != ''){
                   $qb2 =$repositoryEquipesadmin->createQueryBuilder('t')
                             ->where('t.idProf1=:professeur')
                             ->orwhere('t.idProf2=:professeur')
	           ->setParameter('professeur', $professeur);
                $equipes_prof=$qb2->getQuery()->getResult();   
               $FormBuilder2->add('numero',EntityType::class,[
                                       'class' => 'App:Equipesadmin',
                                         'query_builder' => $qb2,
                                       'choice_label'=>'getInfoEquipe',
                                        'label' => 'Choisir cette equipe : ',
                                       
                                         ])
                                   ->add('OK', SubmitType::class);
              }
              
              if($centre != ''){
                   $qb2 =$repositoryEquipesadmin->createQueryBuilder('t')
                             ->where('t.centre =:centre')
	           ->setParameter('centre', $centrecia);
                $equipes_prof=$qb2->getQuery()->getResult();   
               $FormBuilder2->add('numero',EntityType::class,[
                                       'class' => 'App:Equipesadmin',
                                         'query_builder' => $qb2,
                                       'choice_label'=>'getInfoEquipe',
                                        'label' => 'Choisir cette equipe : ',
                                       
                                         ])
                                   ->add('OK', SubmitType::class);
                  
              }
                  
              }
               $form2=$FormBuilder2->getForm();
              if ($request->isMethod('POST') && $form2->handleRequest($request)->isValid()) 
                     { 
                        if (($dateconnect>$datelimcia) and ($dateconnect<$datelimnat)) 
                            {    if ( $form2->get('lettre')->getData()){
                                $lettre_equipe=$form2->get('lettre')->getData()->getLettre();
                                     //$idequipe=$equipe->getId();

                               $numero_equipe= $repositoryEquipesadmin->findOneBy(['lettre'=>$lettre_equipe])->getNumero();
                               $infos=$numero_equipe.'-'. $type_fichier;
                               return $this->redirectToRoute('fichiers_confirme_charge_fichessecur_resume',array('infos'=>$infos));
                               //return $this->redirectToRoute('core_home');
                            }
                            }
                        if (($dateconnect>$dateouverturesite) and ($dateconnect<$datelimcia)) 
                            {    
                            
                        
                                if ($form2->get('numero')->getData()){
                                 $numero_equipe=$form2->get('numero')->getData()->getNumero();
                                     //$idequipe=$equipe->getId();
                                $infos=$numero_equipe.'-'. $type_fichier;
                               return $this->redirectToRoute('fichiers_confirme_charge_fichessecur_resume',array('infos'=>$infos));
                                }
                                
                             }
                          $request->getSession()
                                    ->getFlashBag()
                                    ->add('info', 'Une erreur est survenue lors de cette opération. Veuilllez recommencer ou prévenir le comité  d\'un disfonctionement du site.');
                                    return $this->redirectToRoute('core_home');   
                             
                             
                     }
                     
                     
                 $content = $this
                                     ->renderView('adminfichiers\charge_fichessecur_resume.html.twig', array('form'=>$form2->createView(),'typefichier'=>$type_fichier,'phase'=>$phase));
	return new Response($content);   
    
}
 
/**
         * @Security("is_granted('ROLE_PROF')")
         * @var Symfony\Component\HttpFoundation\File\UploadedFile $file 
         * @Route("/fichiers/confirme_charge_fichessecur_resume/{infos}", name="fichiers_confirme_charge_fichessecur_resume")
         * 
         */        
         public function  confirme_charge_fichessecur_resume(Request $request, $infos){   
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
                 $repositoryResumes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Resumes');  
                 $info=explode("-",$infos);
                $numero_equipe=$info[0];
                $type_fichier=$info[1];
                 $Equipe_choisie=$repositoryEquipesadmin->findOneByNumero(['numero'=>$numero_equipe]);
                 
                 if ($type_fichier=='fichesecur'){
                 $Fiche = $repositoryFichessecur->FindBy(['equipe'=>$Equipe_choisie]);
                 }               
                 if ($type_fichier=='resume'){
                 $Fiche = $repositoryResumes->FindBy(['equipe'=>$Equipe_choisie]);
                 }    
                 $avertissement ='';
                 
                 if ($Fiche){
                             if ($type_fichier=='fichesecur'){                    
                             $avertissement= 'La fiche sécurité existe déjà. ' ;
                             }
                              if ($type_fichier=='resume'){
                                 $avertissement= 'Le résumé existe déjà. ' ; 
                              }
                     }
                     
                     if ($Equipe_choisie){
                         $lettre_equipe= $Equipe_choisie->getLettre();//on charge la lettre de l'équipe 
                         if(!$lettre_equipe){                                     // si la lettre n'est pas attribuée on est en phase interac
                                         //On cherche un mémoire et son annexe déjà déposés pour cette équipe                            }
                                        
                             $TitreProjet = $Equipe_choisie->getTitreProjet();
                                        
                         }                  
                         if($lettre_equipe){                                     // si la lettre est attribuée on est en phase  concours nationale
                                   $Equipe_choisie=$repositoryEquipesadmin->findOneBy(['lettre'=>$lettre_equipe]);//On cherche l'instance dans les équipes sélectionnées
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
                                       
                                return $this->redirectToRoute('fichiers_charge_fichessecur_resume_fichier',array('infos'=>$infos));
                            
                                        }
                                 if ($form3->get('NON')->isClicked())
                                     {
                                return $this->redirectToRoute('core_home');
                                        }
                                }
                                $request->getSession()
                                    ->getFlashBag()
                                    ->add('info', $avertissement.' Voulez-vous poursuivre et remplacer éventuellement ce fichier ? Cette opération est défintive, sans possibilité de récupération.') ;
                                $content = $this
                                                ->renderView('adminfichiers\confirm_charge_fichessecur_resume.html.twig', array(
                                                    'form'=>$form3->createView(), 
                                                    'lettre_equipe'=>$lettre_equipe,
                                                    'numero_equipe'=>$numero_equipe, 
                                                    'titre_projet' =>$TitreProjet, 
                                                    'typefichier'=>$type_fichier
                                                     )
                                                        );
                                return new Response($content);   
                                }
                                if(!$Fiche){             //Si pas de mémoire déjà déposé on redirige directement vers la page de choix du fichier à déposer
                                     return $this->redirectToRoute('fichiers_charge_fichessecur_resume_fichier',array('infos'=>$infos));
                                }
                     }
                     $request->getSession()
                                    ->getFlashBag()
                                    ->add('info', 'Une erreur est survenue lors de cette opération. Veuilllez recommencer ou prévenir le comité  d\'un disfonctionement du site.');
                                    return $this->redirectToRoute('core_home');
         }

 /**
         * @Security("is_granted('ROLE_PROF')")
         * @var Symfony\Component\HttpFoundation\File\UploadedFile $file 
         * @Route("/fichiers/charge_fichessecur_resume_fichier/{infos}", name="fichiers_charge_fichessecur_resume_fichier")
         * 
         */         
         public function   charge_fichessecur_resume_fichier(Request $request, $infos ,Mailer $mailer){
             
             
             
             $repositoryFichessecur= $this->getDoctrine()
		->getManager()
		->getRepository('App:Fichessecur');
             $repositoryResumes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Resumes');
             $repositoryEquipes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipes');
             $repositoryTotalequipes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Totalequipes');
             $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
              $repositoryEdition= $this->getDoctrine()
		->getManager()
		->getRepository('App:Edition');
              $repositoryUser= $this->getDoctrine()
		->getManager()
		->getRepository('App:User');
              $defaultData = ['message' => 'Charger le memoire'];
              $info=explode("-",$infos);
              
              $numero_equipe=$info[0];
                $type_fichier=$info[1];
             $lettre_equipe= $repositoryEquipesadmin->findOneByNumero(['numero'=>$numero_equipe])->getLettre();
             $repositoryEdition = $this->getDoctrine()->getRepository('App:Edition');
                  $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
             if($type_fichier=='fichesecur'){
             $Fiche=new Fichessecur();
             $form1=$this->createForm(FichessecurType::class,$Fiche);
                        }
              if($type_fichier=='resume'){
             $Fiche=new Resumes();
             $form1=$this->createForm(ResumesType::class,$Fiche);
                        }
             
             if($lettre_equipe){
                        
                        $nom_equipe=$repositoryEquipesadmin->findOneByLettre(['lettre'=>$lettre_equipe])->getTitreProjet();
                        $donnees_equipe=$lettre_equipe.' : '.$nom_equipe;
                        
                         
                         
             }
              if(!$lettre_equipe){
                       
                        $nom_equipe=$repositoryEquipesadmin->findOneByNumero(['numero'=>$numero_equipe])->getTitreProjet();
                        $donnees_equipe=$numero_equipe.' : '.$nom_equipe;
              }
             //$lettre_equipe=$request->get('equipe')->getData();
             //$equipe=$repositoryTotalequipes->find($idequipe);
            
                 
                                    
        
                 
                 $form1->handleRequest($request); 
                if ($form1->isSubmitted() && $form1->isValid()){
                      /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */  
                    
                    if($type_fichier=='fichesecur'){
                    $file=$form1->get('fiche')->getData();
                    
                    }
                    if($type_fichier=='resume'){
                    $file=$form1->get('fiche')->getData();
                    require_once('../vendor/autoload.php');//Pour tester si le nombre de page est inférieur à 21.
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($file);
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
                     if ($pages> 1) { //S'il y a plus de 1 page la procédure est interrompue et on return à la page d'accueil avec un message d'avertissement
                       
                         
                        $request->getSession()
                         ->getFlashBag()
                         ->add('alert', 'Votre résumé contient  '.$pages.' pages. Il n\'a pas pu être accepté, il ne doit pas dépasser 1 page !' ) ; 
                        return $this->redirectToRoute('fichiers_charge_fichessecur_resume_fichier',array('infos'=>$numero_equipe.'-'.$type_fichier));
                     }      
                    
                    
                    }
                   
                      $em=$this->getDoctrine()->getManager();
                      $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
                
                   
                   
                    if ($lettre_equipe){                                //La lettre est attribuée donc On dépose la fiche pour les épreuves nationales
                         
                        $Equipe_choisie=$repositoryEquipesadmin->findOneBy(['lettre'=>$lettre_equipe]);
                        
                      if ($Equipe_choisie !== null){// Si un mémoire a déjà été déposé on écrase le précédent
                          if($type_fichier=='fichesecur'){
                            $Fiche = $repositoryFichessecur->findOneByEquipe(['equipe'=>$Equipe_choisie]);
                          }
                          if($type_fichier=='resume'){
                            $Fiche = $repositoryResumes->findOneByEquipe(['equipe'=>$Equipe_choisie]);
                          }
                             if ($Fiche==null){       //si il n'y a pas de Fiche encore  déposés il faut ajouter la ligne correpondant à la table fichessecur
                       if($type_fichier=='fichesecur'){
                      
                        $Fiche->setEdition($edition);
                        $Fiche->setEquipe($Equipe_choisie);
                        
                         $em->persist( $Fiche);
                     
                         $em->flush();
                         $Fiche->setFicheFile($file);    
                         $em->persist( $Fiche);
                     
                         $em->flush();
                         $nom_fichier = $Fiche->getFiche();
                         
                                               }
                       if($type_fichier=='resume'){
                        $Fiche= new Resumes();
                        $Fiche->setEdition($edition);
                        $Fiche->setEquipe($Equipe_choisie);
                        $Fiche->setResumeFile($file);
                        $em->persist( $Fiche);
                     
                         $em->flush();
                         $nom_fichier = $Fiche->getResume();
                         
                         
                       }
                        
                        
                        
                        
                       
                        
                               }                    
                            if($Fiche){//si la fiche a  déjà été déposés on écrase seulement la précédente
                                
                                
                                 if($type_fichier=='fichesecur'){
                         $Fiche->setEdition($edition);
                         $Fiche->setEquipe($Equipe_choisie);
                         $Fiche->setFicheFile($file);
                         $em->persist( $Fiche);
                         
                         $em->flush();
                         $nom_fichier = $Fiche->getFiche();
                        
                        }
                        if($type_fichier=='resume'){
                         $Fiche->setEdition($edition);
                        $Fiche->setEquipe($Equipe_choisie);
                        $Fiche->setResumeFile($file);
                        $em->persist( $Fiche);
                        $em->flush();
                        $nom_fichier = $Fiche->getResume();
                        }
                                
                         }
                         }
                                      
                     
                     
                    }
                     if (!$lettre_equipe){//On dépose un fichier  interacadémique car les lettres ne sont pas attribuées
                                          $Equipe_choisie=$repositoryEquipesadmin->findOneByNumero(['numero'=>$numero_equipe]);
                                          
                                           if($type_fichier=='fichesecur'){
                                           $Fiche = $repositoryFichessecur->findOneByEquipe(['equipe'=>$Equipe_choisie]);
                                           
                                           }
                                             if($type_fichier=='resume'){
                                           $Fiche = $repositoryResumes->findOneByEquipe(['equipe'=>$Equipe_choisie]);
                                           
                                             }
                                          
                                          
                      if ($Equipe_choisie){// si l'équipe existe vraiment(si une équipe dépose son fichier avant la validation de son inscription sur le site 
                    
                    
                    
                            if (!$Fiche){       //si il n'y a pas de fiche encore  déposés il faut ajouter la ligne correpondant à la table fichesssecur ou resume
                       
                        if($type_fichier=='fichesecur'){
                        $Fiche= new Fichessecur();
                        
                        $Fiche->setEdition($edition);
                         $Fiche->setEquipe($Equipe_choisie);
                        $Fiche->setFicheFile($file);
                        
                        $em->persist( $Fiche);
                        
                        $em->flush();
                        $nom_fichier = $Fiche->getFiche();
                        
                        }
                        if($type_fichier=='resume'){
                        $Fiche= new Resumes();
                         $Fiche->setEdition($edition);
                        $Fiche->setEquipe($Equipe_choisie);
                       
                        $Fiche->setResumeFile($file);
                        
                        $em->persist( $Fiche);
                        $em->flush();
                        $nom_fichier = $Fiche->getResume();
                        }
                        
                        
                          }                    
                        else {//si la fiche a  déjà été déposée on écrase seulement la précédente
                               
                               if($type_fichier=='fichesecur'){
                         $em ->remove($Fiche);
                         $em->flush();
                         $Fiche= new Fichessecur();
                         $Fiche->setEdition($edition);
                         $Fiche->setEquipe($Equipe_choisie);
                         $Fiche->setFicheFile($file);
                         $em->persist( $Fiche);
                         
                         $em->flush();
                        $nom_fichier = $Fiche->getFiche();
                        }
                        if($type_fichier=='resume'){
                         $em ->remove($Fiche);
                         $em->flush();
                         $Fiche= new Resumes();
                         $Fiche->setEdition($edition);
                        $Fiche->setEquipe($Equipe_choisie);
                        $Fiche->setResumeFile($file);
                        $em->persist( $Fiche);
                        $em->flush();
                        $nom_fichier = $Fiche->getResume();
                        }
                         }
                         }
                       
                    }
                    
                    if($type_fichier=='fichesecur'){
                        $nom_fichier = $Fiche->getFiche();
                     $request->getSession()
                         ->getFlashBag()
                          ->add('info', 'Votre fichier renommé selon : '.$nom_fichier.' a bien été déposé. Merci !') ;}
                       if($type_fichier=='resume'){
                           
                     $request->getSession()
                         ->getFlashBag()
                    ->add('info', 'Votre fichier renommé selon : '. $nom_fichier.' a bien été déposé. Merci !') ;}  
                    

                                  
                     $user = $this->getUser();//Afin de rappeler le nom du professeur qui a envoyé le fichier dans le mail
                     
                      $bodyMail = $mailer->createBodyMail('emails/confirm_fichier.html.twig', 
                                    ['nom' => $user->getNom(),
                                    'prenom' =>$user->getPrenom(),
                                    'fichier'=>$nom_fichier,
                                    'equipe'=>$Equipe_choisie->getInfoequipe(),
                                    'typefichier' => $type_fichier]);
                    $mailer->sendMessage('webmestre2@olymphys.fr', 'info@olymphys.fr', 'Depot du '.$type_fichier.'de l\'équipe '.$Equipe_choisie->getNumero(),'L\'équipe '.'n°'.$Equipe_choisie->getNumero().':'.$Equipe_choisie->getTitreProjet().' a déposé un fichier');
                     $centre = $Equipe_choisie->getCentre();
                     if ($centre){
                     $cohorte_centre =$repositoryUser->findByCentrecia(['centrecia'=>$centre]);
                     foreach($cohorte_centre as $individu){
                         $roles = $individu->getRoles();
                         $mailorganisateur='';
                              foreach($roles as $role){
                                  if ($role=='ROLE_ORGACIA'){
                                  $mailorganisateur=$individu->getEmail();
                                 $mailer->sendMessage('webmestre2@olymphys.fr', $mailorganisateur, 'Depot du '.$type_fichier.'de l\'équipe '.$Equipe_choisie->getNumero(),'L\'équipe '.'n°'.$Equipe_choisie->getNumero().':'.$Equipe_choisie->getTitreProjet().' a déposé un fichier');
                                                                  
                              }
                         
                              }
                     }
                     }
                    
               
                   // $mailer->sendMessage('webmestre2@olymphys.fr', 'webmestre3@olymphys.fr', 'Depot du '.$type_fichier.'de l\'équipe '.$Equipe_choisie->getNumero(),'L\'équipe '.'n°'.$Equipe_choisie->getNumero().':'.$Equipe_choisie->getTitreProjet().' a déposé un fichier');
                     
                     return $this->redirectToRoute('core_home');     
               /*       $email = (new TemplatedEmail())
                                 ->htmlTemplate('emails/confirm_fichier.html.twig')
                                 // ->textTemplate('e$email = (new Email())
                                ->from('webmestre3@olymphys.fr')
                                ->to('info@olymphys.fr')
                              ->cc('alain.jouve@wanadoo.fr')
                                ->context( ['nom' => $user->getNom(),
                                    'prenom' =>$user->getPrenom(),
                                    'fichier'=>$nom_fichier,
                                    'equipe'=>$Equipe_choisie->getInfoequipe(),
                                    'typefichier' => $type_fichier])
                                ->subject('L\'équipe '.'n°'.$Equipe_choisie->getNumero().':'.$Equipe_choisie->getTitreProjet().' a déposé un fichier');
                      $mailer->send($email);
                    
       */
                   
                     }
                     
                     $content = $this
                                     ->renderView('adminfichiers\charge_fichier_fichessecur_resume.html.twig', array('form'=>$form1->createView(),'donnees_equipe'=>$donnees_equipe, 'typefichier'=>$type_fichier));
	return new Response($content);          
                     
                     
                 }
                
              /**
         * @Security("is_granted('ROLE_PROF')")
         * 
         * @Route("/fichiers/charge_memoires}", name="fichiers_charge_memoires")
         * 
         */
	public function charge_memoires(Request $request)
	{ 
             $repositoryMemoires= $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoires');
             $repositoryEquipes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipes');
             $repositoryTotalequipes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Totalequipes');
             $repositoryEdition= $this->getDoctrine()
		->getManager()
		->getRepository('App:Edition');
             $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
             
            $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
            
            $datelimcia = $edition->getDatelimcia();
            $datelimnat=$edition->getDatelimnat();
            $dateouverturesite=$edition->getDateouverturesite();
            $defaultData = ['message' => 'Charger le memoire'];
          
            //recupétation équipe(s) du prof1 ou prof 2
            $user = $this->getUser();
             $professeur=$user->getId();
             $fileName='';
             $lettre_equipe_choisie = '';
             $nom_equipe='';
            $fileName='';
            $lettre_equipe_choisie = '';
            $nom_equipe='';
           
           //$form=$this->CreateForm(MemoiresType::class,$Memoire);
            
              
              $dateconnect= new \datetime('now');
              if (($dateconnect>$datelimcia) and ($dateconnect<$datelimnat)) {
                  $phase='national';
                  $equipe=new Equipesadmin(); 
                  $FormBuilder2= $this->get('form.factory')->createBuilder(FormType::class, $equipe);
                  
                    $qb1 =$repositoryEquipesadmin->createQueryBuilder('t')
                             ->where('t.idProf1=:professeur')
                             ->orwhere('t.idProf2=:professeur')
	           ->setParameter('professeur', $professeur)
                            ->andWhere('t.selectionnee  = TRUE');
             $equipes_prof=$qb1->getQuery()->getResult();              
            //$Totalequipe= $qb->getQuery()->getResult();
            
            
                           
                                  
                  $FormBuilder2->add('lettre',EntityType::class,[
                                       'class' => 'App:Equipesadmin',
                                         'query_builder' => $qb1,
                                       'choice_label'=>'getInfoEquipe',
                                        'label' => 'Choisir une équipe : ',
                                       
                                         ])
                                   ->add('OK', SubmitType::class);
              }
              if (($dateconnect>$dateouverturesite) and ($dateconnect<$datelimcia)) {
                         $phase= 'interacadémique';
                        $equipe=new Equipesadmin(); 
                        $FormBuilder2= $this->get('form.factory')->createBuilder(FormType::class, $equipe);
                  
                  
                  
                  
                   $qb2 =$repositoryEquipesadmin->createQueryBuilder('t')
                             ->where('t.idProf1=:professeur')
                             ->orwhere('t.idProf2=:professeur')
	           ->setParameter('professeur', $professeur);
                   
                $equipes_prof=$qb2->getQuery()->getResult();   
               $FormBuilder2->add('numero',EntityType::class,[
                                       'class' => 'App:Equipesadmin',
                                         'query_builder' => $qb2,
                                       'choice_label'=>'getInfoequipe',
                                        'label' => 'Choisir une équipe : ',
                                       
                                         ])
                                   ->add('OK', SubmitType::class);
              }
               $form2=$FormBuilder2->getForm();
              if ($request->isMethod('POST') && $form2->handleRequest($request)->isValid()) 
                     { 
                        if (($dateconnect>$datelimcia) and ($dateconnect<$datelimnat)) 
                            {    if ( $form2->get('lettre')->getData()){
                                $lettre_equipe=$form2->get('lettre')->getData()->getLettre();
                                     //$idequipe=$equipe->getId();

                               $numero_equipe= $repositoryEquipesadmin->findOneBy(['lettre'=>$lettre_equipe])->getNumero();
                               return $this->redirectToRoute('fichiers_confirme_charge_memoires_fichier',array('numero_equipe'=>$numero_equipe));
                               //return $this->redirectToRoute('core_home');
                            }
                            }
                        if (($dateconnect>$dateouverturesite) and ($dateconnect<$datelimcia)) 
                            {    
                            
                        
                                if ($form2->get('numero')->getData()){
                                 $numero_equipe=$form2->get('numero')->getData()->getNumero();
                                     //$idequipe=$equipe->getId();

                               return $this->redirectToRoute('fichiers_confirme_charge_memoires_fichier',array('numero_equipe'=>$numero_equipe));
                                }
                                
                             }
                          $request->getSession()
                                    ->getFlashBag()
                                    ->add('info', 'Une erreur est survenue lors de cette opération. Veuilllez recommencer ou prévenir le comité  d\'un disfonctionement du site.');
                                    return $this->redirectToRoute('core_home');   
                             
                             
                     }
                     
                     
                 $content = $this
                                     ->renderView('adminfichiers\charge_memoire_equipe.html.twig', array('form'=>$form2->createView(),'phase'=>$phase));
	return new Response($content);          
                        
                  }
                   /**
         * @Security("is_granted('ROLE_PROF')")
         * @var Symfony\Component\HttpFoundation\File\UploadedFile $file 
         * @Route("/fichiers/confirme_charge_memoires_fichier/{numero_equipe}", name="fichiers_confirme_charge_memoires_fichier")
         * 
         */        
         public function  confirme_charge_memoires_fichier(Request $request,$numero_equipe){   
                $repositoryTotalequipes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Totalequipes');
                 $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
                $repositoryEquipes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipes');
                $repositoryMemoiresinter= $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoiresinter');      
                $repositoryMemoires= $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoires');      
                
                 $Equipe_choisie=$repositoryEquipesadmin->findOneByNumero(['numero'=>$numero_equipe]);
                 
                 if (!$Equipe_choisie->getLettre()) {   //pas de lettre donc phase interacadémique
                 $memoires = $repositoryMemoiresinter->getMemoires($Equipe_choisie);
                 }
                 if ($Equipe_choisie->getLettre()) { //une lettre donc phase nationale
                  $memoires =$repositoryMemoires->getMemoires($Equipe_choisie);
                 }
                 
                 $avertissement ='';
                 $avertissement1='';
                 $avertissement2='';
                 if ($memoires){
                     foreach($memoires as $memoire){
                         if ($memoire->getAnnexe() == 0 ){
                             
                             $avertissement1= 'Le memoire existe déjà. ' ;
                             
                             $flagmemoire=$memoire->getId();
                              }
                          if ($memoire->getAnnexe() == 1 ){
                             
                             $avertissement2= 'L\'annexe existe déjà. ' ;
                             $flagannexe=$memoire->getId();
                              }
                         $avertissement= $avertissement1.' '.$avertissement2;
                         
                     }
                     
                     
                 }
                 
                 
                 
                  
                  
                     if ($Equipe_choisie){
                         $lettre_equipe= $Equipe_choisie->getLettre();//on charge la lettre de l'équipe 
                         if(!$lettre_equipe){                                     // si la lettre n'est pas attribuée on est en phase interac
                                         //On cherche un mémoire et son annexe déjà déposés pour cette équipe                            }
                                        
                             $TitreProjet = $Equipe_choisie->getTitreProjet();
                                        
                         }                  
                         if($lettre_equipe){                                     // si la lettre est attribuée on est en phase  concours nationale
                                   $Equipe_choisie=$repositoryEquipesadmin->findOneBy(['lettre'=>$lettre_equipe]);//On cherche l'instance dans les équipes sélectionnées
                                  //On cherche un mémoire et son annexe déjà déposés pour cette équipe                            }
                                   $TitreProjet = $Equipe_choisie->getTitreProjet();
                                   }    
                         
                           
                           if($memoires){                                               //Si un mémoire est déjà dépose on demande si on veut écraser le précédent
                                   $form3 = $this->createForm(ConfirmType::class);                                  ;
                                if ($request->isMethod('POST') && $form3->handleRequest($request)->isValid()) 
                                { 
                                 //$lettreequipe=$form3->get('lettre_equipe')->getData()->getLettreEquipe();
                                 if ($form3->get('OUI')->isClicked())
                                     {
                                       
                                return $this->redirectToRoute('fichiers_charge_memoires_fichier',array('numero_equipe'=>$numero_equipe));
                            
                                        }
                                 if ($form3->get('NON')->isClicked())
                                     {
                                return $this->redirectToRoute('core_home');
                                        }
                                }
                                $request->getSession()
                                    ->getFlashBag()
                                    ->add('info', $avertissement.', Voulez-vous poursuivre et remplacer éventuellement un des  fichiers ? Cette opération est définitive, sans possibilité de récupération.') ;
                                $content = $this
                                                ->renderView('adminfichiers\confirm_charge_memoire.html.twig', array('form'=>$form3->createView(), 'lettre_equipe'=>$lettre_equipe, 'numero_equipe'=>$numero_equipe, 'titre_projet' =>$TitreProjet));
                                return new Response($content);   
                                }
                                if(!$memoires){             //Si pas de mémoire déjà déposé on redirige directement vers la page de choix du fichier à déposer
                                     return $this->redirectToRoute('fichiers_charge_memoires_fichier',array('numero_equipe'=>$numero_equipe));
                                }
                     }
                     $request->getSession()
                                    ->getFlashBag()
                                    ->add('info', 'Une erreur est survenue lors de cette opération. Veuilllez recommencer ou prévenir le comité  d\'un disfonctionement du site.');
                                    return $this->redirectToRoute('core_home');
         }      
       
          /**
         * @Security("is_granted('ROLE_PROF')")
         * @var Symfony\Component\HttpFoundation\File\UploadedFile $file 
         * @Route("/fichiers/charge_memoires_fichier/{numero_equipe}", name="fichiers_charge_memoires_fichier")
         * 
         */         
         public function   charge_memoires_fichier(Request $request, $numero_equipe, Mailer $mailer){
             $repositoryMemoires= $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoires');
             $repositoryMemoiresinter= $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoiresinter');
             $repositoryEquipes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipes');
             $repositoryTotalequipes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Totalequipes');
             $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
             $repositoryEdition = $this->getDoctrine()
                                   ->getManager()
                                   ->getRepository('App:Edition');
              $repositoryUser = $this->getDoctrine()
                                   ->getManager()
                                   ->getRepository('App:User');
               $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
              $defaultData = ['message' => 'Charger le memoire'];
             $lettre_equipe= $repositoryEquipesadmin->findOneByNumero(['numero'=>$numero_equipe])->getLettre();
              
                
               
            
             //$lettre_equipe=$request->get('equipe')->getData();
             //$equipe=$repositoryTotalequipes->find($idequipe);
             if($lettre_equipe){
                        $Memoire=new Memoires();
                        $nom_equipe=$repositoryEquipesadmin->findOneByLettre(['lettre'=>$lettre_equipe])->getTitreProjet();
                        $donnees_equipe=$lettre_equipe.' : '.$nom_equipe;
                         $fileName =$lettre_equipe;
                         $repertoire='repertoire_memoire_national';
                         $form1=$this->createForm(MemoiresType::class,$Memoire);
                         
             }
              if(!$lettre_equipe){
                        $Memoire=new Memoiresinter();
                        $nom_equipe=$repositoryEquipesadmin->findOneByNumero(['numero'=>$numero_equipe])->getTitreProjet();
                        $donnees_equipe=$numero_equipe.' : '.$nom_equipe;
                         $fileName ='eq-'.$numero_equipe;
                         $repertoire = 'repertoire_memoire_interacademiques';
                         
                         $form1=$this->createForm(MemoiresinterType::class,$Memoire);
                         
                         
                         
              }
                  $repertoiretmp='repertoire_memoire_tmp';
                                    
                                    
                        
                 
                 $form1->handleRequest($request); 
                if ($form1->isSubmitted() && $form1->isValid()){
                      /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */  
                     $file=$form1->get('memoire')->getData();
                     $annexe=$form1->get('annexe')->getData();
                     if ($annexe===true){
                         $fileName=$fileName.'-annexe.'.$file->guessExtension();
                     }
                     if ($annexe===false){
                         $fileName=$fileName.'-memoire-'.$nom_equipe.'.'.$file->guessExtension();
                     }
                     setlocale(LC_CTYPE, 'fr_FR'); 
                     $fileName = iconv('UTF-8','ASCII//TRANSLIT',$fileName);

                     
                     
                 
                    $em=$this->getDoctrine()->getManager();
                    require_once('../vendor/autoload.php');//Pour tester si le nombre de page est inférieur à 21.
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($file);
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
                     if ($pages> 20) { //S'il y a plus de 20 page la procédure est interrompue et on return à la page d'accueil avec un message d'avertissement
                       
                         
                        $request->getSession()
                         ->getFlashBag()
                         ->add('alert', 'Votre fichier contient  '.$pages.' pages. Il n\'a pas pu être accepté, il ne doit pas dépasser 20 pages !' ) ; 
                     }      
                    else{        
                            
                    if ($lettre_equipe){                                //On dépose un memore national
                         
                        $Equipe_choisie=$repositoryEquipesadmin->findOneBy(['lettre'=>$lettre_equipe]);
                       
                      if ($Equipe_choisie){// Si un mémoire a déjà été déposé on écrase le précédent
                            $memoires = $repositoryMemoires   //On uitlise la tab le memoires dédiées aux mémoires nationaux
                                        ->getMemoires($Equipe_choisie);   
                    
                     
                             if (!$memoires){       //si il n'y a pas de mémoire encore  déposé il faut ajouter la ligne correpondant à la table mémoires
                       
                        
                        $memoire= new Memoires();
                       
                        $memoire->setEquipe($Equipe_choisie);
                       //$nouveau_memoire->setUpdatedAt(new \DateTime('now'));
                        if($annexe===true){
                            $memoire->setAnnexe(true);//on place le boolean annexe à true
                        }
                        if($annexe===false){
                            $memoire->setAnnexe(false);//on place le boolean  annexe à false
                        }
                         $memoire->setEdition($edition);
                         $memoire->setMemoireFile($file);//utilisation de vichuploader 
                        
                        $em->persist( $memoire);
                        
                         $em->flush();
                        
                        
                          }                    
                        if($memoires){//si des mémoires ont déjà été déposés on écrase seulement les précédentes données
                            $flagannexe = -1;//initialisation à -1 des id des fichiers 
                            $flagmemoire =-1;
                            foreach($memoires as $memoire){// Deux fichiers (au maxi) sont attribués à une équipe on les récupère
                                if ($memoire->getAnnexe() == 0 ){//On distingue les annexes des annexes qui sont dans la même table Mémoires avec le boolean annexe de la table
                                      $flagmemoire=$memoire->getId();//On recupère l'id du fichier pour pouvoir le remplacer
                                     }
                                 if ($memoire->getAnnexe() == 1 ){ 
                                     $flagannexe=$memoire->getId();
                                     }
                          }     
                                if($annexe===true){ //Si le fichier qui va être déposé eest une annexe
                                      $annexe_fichier=1; //on place le boolean à true
                                       if($flagannexe != -1){//
                                       $memoire=$repositoryMemoires->findOneById(['id'=>$flagannexe]);
                                                }
                                       else { 
                                           $memoire=new Memoires();//on ne remplace pas un fichier existant donc nouvel enregistrement
                                           
                                       }
                                 }
                            if($annexe===false){ 
                                $annexe_fichier=0;//On place le boolean à false
                                 if($flagmemoire != -1){
                                      $memoire=$repositoryMemoires->findOneById(['id'=>$flagmemoire]);
                                 }
                                 else {
                                      $memoire=new Memoires();//on ne remplace pas un fichier existant donc nouvel enregistrement
                                 }
                                 }
                                $memoire->setEquipe($Equipe_choisie);
                                 $memoire->setEdition($edition);
                                //$memoire->setUpdatedAt(new \DateTime('now'));
                                $memoire->setAnnexe($annexe_fichier);//on injecte la valeur du boolean
                                $memoire->setMemoireFile($file);
                                
                                $em->persist($memoire);
                                $em->flush();
                                //$memoire_tmp=$repositoryMemoires->findOneBy(['equipe'=>$lettre_equipe]);;
                                 //$Equipe_choisie->setMemoire($nouveau_memoire->getId());
                                 //$em->persist($Equipe_choisie);

                                 //$em->flush();
                         }
                         }
                       $Idmemoire=$repositoryMemoires ->findOneBy([], ['id' => 'desc']);//on récupère l'id du dernier enregistrement de la table
                         $filename= $repositoryMemoires->find(['id' =>$Idmemoire])->getMemoire();//On récupère le nom du fichier enregistré pour le placer dans l'info envoyé à l'oganisateur
                                        
                     $centre='N';
                     $adressemail ='alain.jouvealb@gmail.com'; // 'sylvie@dancre.net';
                    }
                     if (!$lettre_equipe){//On dépose un mémoire interacadémique
                                          $Equipe_choisie=$repositoryEquipesadmin->findOneByNumero(['numero'=>$numero_equipe]);
                                          $memoires = $repositoryMemoiresinter->findByEquipe(['equipe'=>$Equipe_choisie]);
                                          
                                          
                      if ($Equipe_choisie){// Si un mémoire a déjà été déposé on écrase le précédent
                    
                            if (!$memoires){       //si il n'y a pas de mémoire encore  déposés il faut ajouter un nouevel enregistrement à la table mémoiresinter
                        
                        $nouveau_memoire= new Memoiresinter();
                        
                        $nouveau_memoire->setEquipe($Equipe_choisie);
                        //$nouveau_memoire->setUpdatedAt(new \DateTime('now'));
                         if($annexe===true){ //Si le fichier à déposé est une annexe
                            $nouveau_memoire->setAnnexe(1);
                        }
                        if($annexe===false){ //Si le fichier à déposé est un mémoire
                            $nouveau_memoire->setAnnexe(0);
                        }
                         $nouveau_memoire->setEdition($edition);
                        $nouveau_memoire->setMemoireFile($file);///Vicheuploader s'occupe du nom et de l'emplacement de l'enregistrement du fichier
                        $em->persist( $nouveau_memoire);
                        //$nouveau_memoire->setEquipe($lettre_equipe);
                        //$em->persist( $nouveau_memoire);
                         $em->flush();
                        //$memoire_tmp=$repositoryMemoires->findOneBy(['equipe'=>$lettre_equipe]);;
                         $Idmemoire=$repositoryMemoiresinter ->findOneBy([], ['id' => 'desc']);//on récupère l'id du dernier enregistrement de la table
                         $filename= $repositoryMemoiresinter->find(['id' =>$Idmemoire])->getMemoire();//On récupère le nom du fichier enregistré pour le placer dans l'info envoyé à l'oganisateur
                        
                          }                    
                        if($memoires){//si des mémoires ont déjà été déposés on écrase seulement les précédentes données memoire ou(inclusif) annexe
                            $flagannexe = -1;//Les deux tests sont initialisés à une valeur impossible
                            $flagmemoire =-1;
                            $nouveau=false;
                                foreach($memoires as $memoire){ //deux fichiers sont déjà enregistrés (au plus )
                         if ($memoire->getAnnexe() == 0 ){//C'est un mémoire qui déjà enregistré
                               $flagmemoire =$memoire->getId(); //on récupère l'id du fichier enregistré
                              }
                          if ($memoire->getAnnexe() == 1 ){//c'est une annexe qui est déjà enregistré
                              $flagannexe=$memoire->getId();  //on récupère l'id du fichier enregistré
                              }
                          }     
                        
                            if($annexe===true){ //C'est une annexe qu'on veut déposer
                                      $annexe_fichier=1;  // On fixe à 1 la catégorie annexe qu'on persitera ensuite
                                       if($flagannexe != -1){ //le fichier déjà enregistré est une annexe 
                                       $memoire=$repositoryMemoiresinter->findOneById(['id'=>$flagannexe]);//On récupère lle  fichier enregistré
                                       
                                       
                                       }
                                       else { 
                                           $memoire=new Memoiresinter();//Le flagannexe est -1 donc on crée en nouvel enregistrement
                                           $nouveau=true;
                                       }
                                 }
                            if($annexe===false){ //C'est le mémoire qu'on veut déposer
                                $annexe_fichier=0; // On fixe à 0 la catégorie annexe qu'on persitera ensuite
                                 if($flagmemoire != -1){ //le fichier déjà enregistré est un mémoire
                                      $memoire=$repositoryMemoiresinter->findOneById(['id'=>$flagmemoire]);//On récupère le  fichier enregistré
                                      $Idmemoire=$flagmemoire;
                                 }
                                 else {
                                      $memoire=new Memoiresinter();//Le flagmemoireest -1 donc on crée en nouvel enregistrement
                                      $nouveau= true;
                                      
                                 }
                                 }
                                 $memoire->setAnnexe($annexe_fichier);// on renseigne le champ annexe
                                //$memoire->setMemoireFile(new File($this->getParameter('repertoire_memoire_interacademiques').'/'.$fileName));
                                 
                                 //$memoire->setUpdatedAt(new \DateTime('now'));
                                  $memoire->setEdition($edition);
                                $memoire->setEquipe($Equipe_choisie);//On renseigne le champ équipe concernée
                               $memoire->setMemoireFile($file);// On utilise vichuploader qui gère l'enregistrement du fichier dans le bon répertoire et renseigne les champs de la table memoireinter
                                 $em->persist($memoire);

                                 $em->flush();
                                 if($nouveau==true){
                         $Idmemoire=$repositoryMemoiresinter ->findOneBy([], ['id' => 'desc']);//on récupère l'id du dernier enregistrement de la table
                         }
                         $filename= $repositoryMemoiresinter->find(['id' =>$Idmemoire])->getMemoire();//On récupère le nom du fichier enregistré pour le placer dans l'info envoyé à l'oganisateur
                         }
                         }
                        
                      $centre=$Equipe_choisie->getCentre(); //Pour pouvoir indiquer le centre dans le nom du mémoire
                      $adressemail=$repositoryUser->findOneByCentrecia(['centrecia'=>$centre])->getEmail(); //Pour envoyer l'information de l'envoi à l'organisateur du centre
                    }
                    $typefichier='memoire';
                    if ($annexe==true){
                     $typefichier='annexe';
                    
                    }
                     $request->getSession()
                         ->getFlashBag()
//                         ->add('info', 'Votre fichier de  '.$pages.' pages, '.$memoire->getMemoire().' a bien été déposé. Merci !') ;
                          ->add('info', 'Votre fichier de  '.$pages.' pages, '.$filename.' a bien été déposé. Merci !') ;                       
                     $user = $this->getUser();//Afin de rappeler le nom du professeur qui a envoyé le fichier dans le mail
                     
                    /** $email = (new Email())
                                
                                 // ->textTemplate('e$email = (new Email())
                                ->from('webmestre2@olymphys.fr')
                                ->to('alain.jouvealb@gmail.com')
                              ->cc('webmestre3@olymphys.fr')
                               ->subject('L\'équipe '.'n°'.$Equipe_choisie->getNumero().':'.$Equipe_choisie->getTitreProjet().' a déposé un fichier')
                                ->text('L\'équipe n°'.$Equipe_choisie->getNumero().':'.$Equipe_choisie->getTitreProjet().' a déposé son '.$typefichier);

                             $mailer->send($email);
                     */

                     $bodyMail = $mailer->createBodyMail('emails/confirm_fichier.html.twig', 
                                    ['nom' => $user->getNom(),
                                    'prenom' =>$user->getPrenom(),
                                    'fichier'=>$filename,
                                    'equipe'=>$Equipe_choisie->getInfoequipe(),
                                    'typefichier' => $typefichier]);
            $mailer->sendMessage('webmestre2@olymphys.fr', 'info@olymphys.fr', 'L\'équipe '.'n°'.$Equipe_choisie->getNumero().':'.$Equipe_choisie->getTitreProjet().' a déposé un fichier',$bodyMail);
             $centre = $Equipe_choisie->getCentre();
                      if ($centre){
                     $cohorte_centre =$repositoryUser->findByCentrecia(['centrecia'=>$centre]);
                     foreach($cohorte_centre as $individu){
                         $roles = $individu->getRoles();
                        
                              foreach($roles as $role){
                                  if ($role=='ROLE_ORGACIA'){
                                  $mailorganisateur=$individu->getEmail();
                                 $mailer->sendMessage('webmestre2@olymphys.fr', $mailorganisateur, 'Depot du '.$typefichier.'de l\'équipe '.$Equipe_choisie->getNumero(),'L\'équipe '.'n°'.$Equipe_choisie->getNumero().':'.$Equipe_choisie->getTitreProjet().' a déposé un fichier');
                                                                  
                              }
                              }
                              }
                     
                     }
                    
               
                    $mailer->sendMessage('webmestre2@olymphys.fr', 'webmestre3@olymphys.fr', 'Depot du '.$typefichier.'de l\'équipe '.$Equipe_choisie->getNumero(),'L\'équipe '.'n°'.$Equipe_choisie->getNumero().':'.$Equipe_choisie->getTitreProjet().' a déposé un fichier');
                      
                      
                                      

                                  
                    }
                     
                    return $this->redirectToRoute('core_home');
       
                   
                     }
                     
                     $content = $this
                                     ->renderView('adminfichiers\charge_memoire_fichier_prof.html.twig', array('form'=>$form1->createView(),'donnees_equipe'=>$donnees_equipe));
	return new Response($content);          
                     
                     
                 }  
             
         /**
         * @Security("is_granted('ROLE_PROF')")
         * 
         * @Route("/fichiers/voir_mesfichiers", name="fichiers_voir_mesfichiers")
         * 
         */         
         public function   voir_mesfichiers(Request $request){
             //Identification du User
             //Demander l'équipe dont il veut voir les fichiers
             //Lister les fichiers
             //lui donner la possibliter de les ouvrir
            
             $repositoryTotalequipes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Totalequipes');
             $repositoryEquipesadmin= $this->getDoctrine()
                                   ->getManager()
		->getRepository('App:Equipesadmin');
             $repositoryResumes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Resumes');
             $repositoryFichessecur= $this->getDoctrine()
		->getManager()
		->getRepository('App:Fichessecur');
              $user = $this->getUser();
            $professeur=$user->getId();
            $fileName='';
            $lettre_equipe_choisie = '';
            $nom_equipe='';
           
             $equipe=new Equipesadmin(); 
           $FormBuilder2= $this->get('form.factory')->createBuilder(FormType::class, $equipe);
            $qb2 =$repositoryEquipesadmin->createQueryBuilder('t')
                             ->where('t.idProf1=:professeur')
                             ->orwhere('t.idProf2=:professeur')
	           ->setParameter('professeur', $professeur);
             $equipes_prof=$qb2->getQuery()->getResult();   
             $FormBuilder2->add('numero',EntityType::class,[
                                       'class' => 'App:Equipesadmin',
                                         'query_builder' => $qb2,
                                       'choice_label'=>'getInfoequipe',
                                        'label' => 'Choisir une équipe .',
                                       
                                         ])
                                   ->add('Choisir cette équipe', SubmitType::class);
              
                                    $form1=$FormBuilder2->getForm();
                                    $form1->handleRequest($request); 
                if ($form1->isSubmitted() && $form1->isValid()){
                            $equipe_choisie = $form1->get('numero')->getData();
                            $numero_equipe=$equipe_choisie->getNumero();
                           return $this->redirectToRoute('fichiers_afficher_liste_fichiers_prof',array('numero_equipe'=>$numero_equipe));
                }
                
              $content = $this
                                     ->renderView('adminfichiers\liste_fichiers_prof.html.twig', array('form'=>$form1->createView()));
	return new Response($content);      
         }   
         
        /**
         * @Security("is_granted('ROLE_PROF')")
         * 
         * @Route("/fichiers/afficher_liste_fichiers_prof/{numero_equipe}", name="fichiers_afficher_liste_fichiers_prof")
         * 
         */          
         public function afficher_liste_fichiers_prof(Request $request , $numero_equipe){
              $repositoryMemoires= $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoires');
             $repositoryMemoiresinter= $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoiresinter');
             $repositoryMemoiresnat= $this->getDoctrine()
		->getManager()
		->getRepository('App:Memoires');
             $repositoryEquipes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipes');
              $repositoryEquipesadmin= $this->getDoctrine()
                                   ->getManager()
		->getRepository('App:Equipesadmin');
             $repositoryResumes= $this->getDoctrine()
		->getManager()
		->getRepository('App:Resumes');
             $repositoryFichessecur= $this->getDoctrine()
		->getManager()
		->getRepository('App:Fichessecur');
             
             $equipe_choisie= $repositoryEquipesadmin->findOneByNumero(['numero'=>$numero_equipe]);
            $memoiresinter= $repositoryMemoiresinter->findByEquipe(['equipe'=>$equipe_choisie]);
            $memoiresnat =   $repositoryMemoires->findByEquipe(['equipe'=>$equipe_choisie]);
            $fiche_securit = $repositoryFichessecur->findOneByEquipe(['equipe'=>$equipe_choisie]);    
            $resume= $repositoryResumes->findOneByEquipe(['equipe'=>$equipe_choisie]); 
           $centre=$equipe_choisie->getCentre()->getId();
           
            $user = $this->getUser();
           $roles=$user->getRoles();
           
                            $i=0;
                foreach($memoiresinter as $memoireinter){
                    $id=$memoireinter->getId();
                    $formBuilder[$i]=$this->get('form.factory')->createNamedBuilder('Form'.$i, FormType::class,$memoireinter);  
                    $formBuilder[$i] ->add('id',  HiddenType::class, ['disabled'=>true, 'label'=>false])
                                            ->add('memoire', TextType::class,['disabled'=>true,  'label'=>false])
                                            ->add('save', submitType::class);
                     $Form[$i]=$formBuilder[$i]->getForm();
                    $formtab[$i]=$Form[$i]->createView();
                   
                    if ($request->isMethod('POST') ) 
                        {
                
                
                
                if ($request->request->has('Form'.$i)) {
                      
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
                        }
                                   $i=$i+1;
                    }
                  
                 foreach($memoiresnat as $memoirenat){
                    $id=$memoirenat->getId();
                    $formBuilder[$i]=$this->get('form.factory')->createNamedBuilder('Form'.$id, FormType::class,$memoirenat);  
                    $formBuilder[$i] ->add('id',  HiddenType::class, ['disabled'=>true, 'label'=>false])
                                            ->add('memoire', TextType::class,['disabled'=>true,  'label'=>false])
                                            ->add('save',SubmitType::class);
                     $Form[$i]=$formBuilder[$i]->getForm();
                    $formtab[$i]=$Form[$i]->createView();
                   
                    if ($request->isMethod('POST') ) 
                        {
                
                
                
                if ($request->request->has('Form'.$i)) {
                      
                               //if ($Form[$i]->isSubmitted()){
                                  $id=$Form[$i]->get('id')->getData();
                                  $memoirenat=$repositoryMemoiresnat->find(['id'=>$i]);
                                  $memoireName=$this->getParameter('repertoire_memoire_national').'/'.$memoirenat->getMemoire();
                                   if(null !==$memoireName)
                                       {
                                   $response = new BinaryFileResponse($memoireName);
         
                                   $disposition = HeaderUtils::makeDisposition(
                                HeaderUtils::DISPOSITION_ATTACHMENT,
                                    $memoirenat->getMemoire()
                                          );
                                  $response->headers->set('Content-Type', 'application/pdf'); 
                                  $response->headers->set('Content-Disposition', $disposition);


                                  //$content = $this->render('secretariat\lire_memoire.html.twig', array('repertoirememoire' => $this->getParameter('repertoire_memoire_national'),'memoire'=>$memoire));
                                  return $response; 
                                  
                                        }
                                 
                                   }
                                                       
                     }
                        
                                   $i=$i+1;
                    }
                   if ($fiche_securit){
                    $id = $fiche_securit->getId();
                    $formBuilder[$i]=$this->get('form.factory')->createNamedBuilder('Form'.$i, FormType::class,$fiche_securit);  
                    $formBuilder[$i] ->add('id',  HiddenType::class, ['disabled'=>true, 'label'=>false])
                                            ->add('memoire',TextType::class, ['disabled'=>true,  'label'=>false, 'data' =>$fiche_securit->getFiche(), 'mapped'=>false])
                                            ->add('save', submitType::class);
                     $Form[$i]=$formBuilder[$i]->getForm();
                    $formtab[$i]=$Form[$i]->createView();
                    if ($request->isMethod('POST') ) 
                        {
                if ($request->request->has('Form'.$i)) {
                      
                               //if ($Form[$i]->isSubmitted()){
                                  $id=$Form[$i]->get('id')->getData();
                                  $fiche_securit=$repositoryFichessecur->find(['id'=>$id]);
                                  $FicheName=$this->getParameter('repertoire_fiches_securite').'/'.$fiche_securit->getFiche();
                                   if(null !==$FicheName)
                                       {  
                                   
                                    $file=new File($FicheName);
                    $response = new BinaryFileResponse($FicheName);
         
                    $disposition = HeaderUtils::makeDisposition(
                      HeaderUtils::DISPOSITION_ATTACHMENT,

                     $fiche_securit->getFiche()
                            );
                   $response->headers->set('Content-Type', $file->guessExtension()); 
                    $response->headers->set('Content-Disposition', $disposition);
        
                  return $response; 
                                   
                                 
                                       } 
                                 
                                   }
                        }
                   $i=$i+1;}
                   if ($resume){
                    $id = $resume->getId();
                    $formBuilder[$i]=$this->get('form.factory')->createNamedBuilder('Form'.$i, FormType::class,$resume);  
                    $formBuilder[$i] ->add('id',  HiddenType::class, ['disabled'=>true, 'label'=>false])
                                            ->add('memoire', TextType::class,['disabled'=>true,  'label'=>false,'data'=>$resume->getResume(), 'mapped'=>false])
                                           ->add('save', submitType::class);
                     $Form[$i]=$formBuilder[$i]->getForm();
                    $formtab[$i]=$Form[$i]->createView();
                    if ($request->isMethod('POST') ) 
                        {
                
                
                
                if ($request->request->has('Form'.$i)) {
                      
                               //if ($Form[$i]->isSubmitted()){
                                  $id=$Form[$i]->get('id')->getData();
                                  $resume=$repositoryResumes->find(['id'=>$id]);
                                  $resumeName=$this->getParameter('repertoire_resumes').'/'.$resume->getResume();
                                   if(null !==$resumeName)
                                       {
                                   $response = new BinaryFileResponse($resumeName);
         
                                   $disposition = HeaderUtils::makeDisposition(
                                HeaderUtils::DISPOSITION_ATTACHMENT,
                                    $resume->getResume()
                                          );
                                  $response->headers->set('Content-Type', 'application/pdf'); 
                                  $response->headers->set('Content-Disposition', $disposition);


                                  //$content = $this->render('secretariat\lire_memoire.html.twig', array('repertoirememoire' => $this->getParameter('repertoire_memoire_national'),'memoire'=>$memoire));
                                  return $response; 
                                 
                                        }
                                 
                                   }
                    
                              }
                   $i=$i+1;
                            }
                             if ($request->isMethod('POST') ) 
                        {
                             if ($request->request->has('FormAll')) {         
                          $zipFile = new \ZipArchive();
                          $FileName= $equipe_choisie->getCentre()->getCentre().'-Fichiers-eq-'.$equipe_choisie->getNumero().'-'.date('now');
                          if ($zipFile->open($FileName, ZipArchive::CREATE) === TRUE){
                          
                                        $memoires= $repositoryMemoiresinter->findByEquipe(['equipe'=>$equipe_choisie]);
                                                foreach($memoires as $memoire){
                                                        $Memoire=$this->getParameter('repertoire_memoire_interacademiques').'/'.$memoire->getMemoire();
                                                         if($Memoire){
                                                         $zipFile->addFromString(basename($Memoire),  file_get_contents($Memoire));}//voir https://stackoverflow.com/questions/20268025/symfony2-create-and-download-zip-file
                                                 }
                                                  $resume=$repositoryResumes->findOneByEquipe(['equipe'=>$equipe_choisie]);
                                                  $fichesecurit=$repositoryFichessecur->findOneByEquipe(['equipe'=>$equipe_choisie]);
                                                 if ($resume){
                                                 $Resume=$this->getParameter('repertoire_resumes').'/'.$resume->getResume();
                                                 if ($Resume){
                                                 $zipFile->addFromString(basename($Resume),  file_get_contents($Resume));}
                                                 }
                                                 if ($fichesecurit){
                                                 $fichesecur=$this->getParameter('repertoire_fiches_securite').'/'.$fichesecurit->getFiche();
                                                 if($fichesecur){
                                                 $zipFile->addFromString(basename($fichesecur),  file_get_contents($fichesecur));}
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
                                          @unlink($FileName);
                                          return $response; 
                                 }
                     
                        }
                        }
                   
                    if(isset($formtab)){      
                        $fichier=new Memoiresinter();
                        $formBuilder=$this->get('form.factory')->createNamedBuilder('FormAll', ListmemoiresinterallType::class,$fichier);  
                    $formBuilder->add('save',      SubmitType::class );
                                        $Form=$formBuilder->getForm();
                    $formtab[$i]=$Form->createView();//Ajoute le bouton  tout télécharger
                     ($formtab);   
                   $content = $this
                     ->renderView('adminfichiers\affiche_liste_fichiers_prof.html.twig', array('formtab'=>$formtab,
                     'infoequipe'=>$equipe_choisie->getInfoequipe(), 'centrecia' =>$equipe_choisie->getCentre())
                                ); 
                   return new Response($content); 
                          }
              if(!isset($formtab)){
                     $request->getSession()
                         ->getFlashBag()
                         ->add('info', 'Il n\'y a pas encore de fichier déposé pour l\'equipe n°'.$numero_equipe) ;
                    
                     foreach ($roles as $role){
                    if ($role=='ROLE_PROF'){
                    return $this->redirectToRoute('core_home');   }
                     if ($role=='ROLE_ORGACIA' || $role=='ROLE_JURYCIA'){
                    return $this->redirectToRoute('fichiers_afficherlesmemoiresinter_orgacia');   }
                     
                      if ($role=='ROLE_COMITE' || $role=='ROLE_SUPER_ADMIN'){
                       
                    return $this->redirectToRoute('fichiers_afficher_liste_equipe_comite',array('centre'=>$centre)); 
                     
                     }
                     
                     
                     }
              }     
              }
}                 
                   
  
