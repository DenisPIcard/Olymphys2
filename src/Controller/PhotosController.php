<?php
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
use App\Form\ListmemoiresinterType;
use App\Form\ListmemoiresinterallType;
use App\Form\FichessecurType;
use App\Form\PhotosinterType;
use App\Form\PhotoscnType;

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
use App\Entity\Fichessecur;
use App\Entity\Equipesadmin;
use App\Entity \Photosinter;
use App\Entity \Photosinterthumb;
use App\Entity \Photoscn;
use App\Entity \Photoscnthumb;

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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\HttpFoundation\Request ;
use Symfony\Component\HttpFoundation\RedirectResponse ;
use Symfony\Component\HttpFoundation\Response ;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use ZipArchive;

class PhotosController extends  AbstractController
{
      /**
         *  @IsGranted("ROLE_ORGACIA")
         * 
         * @Route("/photos/deposephotos,{concours}", name="photos_deposephotos")
         * 
         */
    public function deposephotos(Request $request, $concours)
            {
               
             $repositoryEdition= $this->getDoctrine()
		->getManager()
		->getRepository('App:Edition');
             $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
             $repositoryPhotosinter=$this->getDoctrine()
                                   ->getManager()
                                   ->getRepository('App:Photosinter');
             $repositoryPhotoscn=$this->getDoctrine()
                                   ->getManager()
                                   ->getRepository('App:Photoscn');
             
             $edition = $repositoryEdition->findOneBy([], ['id' => 'desc']);
            if ($concours=="inter"){
             $Photos = new Photosinter();
             //$Photos->setSession($session);
             $form = $this->createForm(PhotosinterType::class, null);
              $form->handleRequest($request);
              
            }
             if ($concours=="cn"){
             $Photos = new Photoscn();
             //$Photos->setSession($session);
             $form = $this->createForm(PhotoscnType::class, null);
              $form->handleRequest($request);
              
            }
            
            
            
            if ($form->isSubmitted() && $form->isValid()) {
                      $em=$this->getDoctrine()->getManager();
                     
                     
                     $equipe=$form->get('equipe')->getData();
                      //$equipe=$repositoryEquipesadmin->findOneBy(['id'=>$id_equipe]);
                      $nom_equipe=$equipe->getTitreProjet();
                     
                      $numero_equipe=$equipe->getNumero();
                     $files=$form->get('photoFiles')->getData();
                     
                     if($files){
                       foreach($files as $file){
                         if ($concours== 'inter')  {
                         $photo=new Photosinter();
                                      }
                         if ($concours== 'cn')  {
                         $photo=new Photoscn();
                                       }            
                         $photo->setEdition($edition);
                       $photo->setPhotoFile($file);//Vichuploader gère l'enregistrement dans le bon dossier, le renommage du fichier
                         $photo->setEquipe($equipe);
                         //$photo->setUpdatedAt(new \DateTime('now'));
                         $em->persist($photo);
                          $em->flush();
                          //dd($photo);
                          if ($concours== 'inter')  {
                          $photothumb = New Photosinterthumb();
                           $photo= $repositoryPhotosinter->findOneby(['photo'=>$photo->getPhoto()]);
                           
                          $paththumb = 'app.path.photosinterthumb';
                          $pathfile = 'app.path.photosinter';
                          }
                          if ($concours== 'cn')  {
                          $photothumb = New Photoscnthumb();
                           $photo= $repositoryPhotoscn->findOneby(['photo'=>$photo->getPhoto()]);
                           $paththumb= 'app.path.photosnatthumb';
                           $pathfile= 'app.path.photosnat';
                          }
                          //dd($photo);
                         //$filename=basename($photo->getPhoto());
                         //$fileName=$edition->getEd().'-eq-'.$numero_equipe.'-'.$nom_equipe.'-'.uniqid().'.'.$file->guessExtension();//inutile avec vichuploader
                         
                         list($width_orig, $height_orig) = getimagesize($photo->getPhotoFile());
                         $dim=max($width_orig, $height_orig);
                         if ($dim!=0){
                         $percent = 300/$dim;
                         }
                         
                         $new_width = $width_orig * $percent;
                         $new_height = $height_orig * $percent;
                          $image =imagecreatefromjpeg($photo->getPhotoFile());
                            // Resample
                            $thumb = imagecreatetruecolor($new_width, $new_height);

                            imagecopyresampled($thumb,$image, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
                            
                          //dd($thumb);
                          imagejpeg($thumb, $this->getParameter($paththumb).'/'.$photo->getPhoto()); 
                          
                          //$thumbfile=new UploadedFile($this->getParameter($paththumb).'/thumb.jpg','thumb.jpg');
                         //dd($thumbfile);
                         //$photothumb->setEdition($edition);
                         //$photothumb->setPhotoFile($thumbfile);//Vichuploader gère l'enregistrement dans le bon dossier, le renommage du fichier
                         $photothumb->setPhoto($photo->getPhoto());//enregistre le même nomque celui de la photo
                         
                         
                         //$photothumb->setEquipe($equipe);
                         //dd($photothumb);
                         //$photo->setUpdatedAt(new \DateTime('now'));
                         $em->persist($photothumb);
                         
                         $em->flush();        
                         //
                         $photo->setThumb($photothumb);
                         $em->persist($photo);
                         $em->flush();
                         
                       
                         
                          
                          
                         
                     }
                     $request->getSession()
                         ->getFlashBag()
                         ->add('info', 'Votre fichier a bien été déposé. Merci !') ;
                     }
                    if (!$files){
                         $request->getSession()
                         ->getFlashBag()
                         ->add('alert', 'Pas fichier sélectionné: aucun dépôt effectué !') ;
                    }
                
                return $this->redirectToRoute('core_home');
                
                
                
                
                
                
            }
             
             
             
             
              return $this->render('photos/deposephotos.html.twig', [
                'form' => $form->createView(),'session'=>$edition->getEd()
        ]);
        
        
            }
        
        
            //
        /**
         * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
         * 
         * @Route("/photos/choixedition", name="photos_choixedition")
         * 
         */    
        public function choixedition(Request $request)
        {
            $repositoryEdition= $this->getDoctrine()
		->getManager()
		->getRepository('App:Edition');
            $Editions = $repositoryEdition->findAll();
             return $this->render('photos/choix_edition.html.twig', [
                'editions' => $Editions]);
            
            
            
        }
        
        
        
        
        
            //
        /**
         * 
         * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
         * @Route("/photos/voirphotoscia, {edition}", name="photos_voirphotoscia")
         * 
         */    
         public function voirphotoscia(Request $request, $edition)
            {
              $repositoryEdition= $this->getDoctrine()
		->getManager()
		->getRepository('App:Edition');
              $repositoryCentrescia= $this->getDoctrine()
		->getManager()
		->getRepository('App:Centrescia');
             $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
             $repositoryPhotosinter=$this->getDoctrine()
                                   ->getManager()
                                   ->getRepository('App:Photosinter');
             $edition=$repositoryEdition->findByEd(['ed'=>27]);
             $liste_centres=$repositoryCentrescia->findAll();
             $qb =$repositoryPhotosinter->createQueryBuilder('t');
                               //->where('t.edition =: edition')
                              // ->setParameter('edition', $edition);
                               
             $liste_photos=$qb->getQuery()->getResult();
             //$liste_photos=$repositoryPhotosinter->findByEdition(['edition'=>$edition]);
             return $this->render('photos/affiche_photos_cia.html.twig', [
                'liste_photos' => $liste_photos,'edition'=>27,'liste_centres'=>$liste_centres]);
             
             
            
        }   
         /**
         * 
         * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
         * @Route("/photos/voirphotoscn, {edition}", name="photos_voirphotoscn")
         * 
         */    
         public function voirphotoscn(Request $request, $edition)
            {    $repositoryEdition= $this->getDoctrine()
		->getManager()
		->getRepository('App:Edition');
              
             $repositoryEquipesadmin= $this->getDoctrine()
		->getManager()
		->getRepository('App:Equipesadmin');
             $repositoryPhotos=$this->getDoctrine()
                                   ->getManager()
                                   ->getRepository('App:Photosinter');
             
             $repositoryPhotoscn=$this->getDoctrine()
                                   ->getManager()
                                   ->getRepository('App:Photoscn');
             $edition=$repositoryEdition->findOneByEd(['ed'=>27]);
             
             $qb1=$repositoryEquipesadmin->createQueryBuilder('e')
                     ->where('e.selectionnee = TRUE')
                     ->orderBy('e.lettre','ASC');
             $liste_equipes=$qb1->getQuery()->getResult();
             $qb2 =$repositoryPhotoscn->createQueryBuilder('p');
             $liste_photos=$qb2->getQuery()->getResult();
             //$liste_photos=$repositoryPhotosinter->findByEdition(['edition'=>$edition]);
             if ($liste_photos)
             {
             return $this->render('photos/affiche_photos_cn.html.twig', [
                'liste_photos' => $liste_photos,'edition'=>27,'liste_equipes'=>$liste_equipes]);
             
            }
             
             
              if (!$liste_photos)
              {$request->getSession()
                         ->getFlashBag()
                         ->add('info', 'Pas de photo déposée pour l\'édition '.$edition->getEd().'à ce jour') ;
             return $this->redirectToRoute('core_home');
              }
            }
}