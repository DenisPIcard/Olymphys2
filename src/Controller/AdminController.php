<?php
namespace App\Controller;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Entity\Memoires;
use App\Entity\Memoiresinter;
use App\Entity\Equipes;
use App\Entity\Equipesadmin;
use App\Entity\Fichessecur;
use App\Entity\Photosinter;
use App\Entity\Elevesinter;
use App\Entity\Resumes;
use App\Entity\Edition;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\MakerBundle\Str;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminFiltersFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Doctrine\ORM\EntityRepository;
use ZipArchive;

class AdminController extends EasyAdminController
{
     private $passwordEncoder;
     public $password;
    
    /**
     * @Route("/", name="easyadmin")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function indexAction(Request $request)
    { dd($request);
        //$this->initialize($request);
        // if the URL doesn't include the entity name, this is the index page  // if the URL doesn't include the entity name, this is the index page
        if (null === $request->query->get('entity')) {
            // define this route in any of your own controllers
             $content = $this->renderView('Admin/content.html.twig',array());
             return new Response($content);
             
        }
        
        return parent::indexAction($request);
   }


    
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
     }
     
     public function LireAction()
     {    $fichier='';
          $class = $this->entity['class'];
         $repository = $this->getDoctrine()->getRepository($class);
         $id = $this->request->query->get('id');
         $entity = $repository->find($id);
        
         if ($class==Fichessecur::class) {
                 $fichier= $this->getParameter('repertoire_fiches_securite').'/'.$entity->getFiche();
                 $file=new File($fichier);
                    $response = new BinaryFileResponse($fichier);
         
                    $disposition = HeaderUtils::makeDisposition(
                      HeaderUtils::DISPOSITION_ATTACHMENT,

                     $entity->getFiche()
                            );
                    $response->headers->set('Content-Type', $file->guessExtension()); 
                    $response->headers->set('Content-Disposition', $disposition);
        
                  return $response; 
               
                  }
         else{
         if ($class==Memoires::class) {
         $fichier=$this->getParameter('repertoire_memoire_national').'/'.$entity->getMemoire();
          $name=$entity->getMemoire();
         $application= 'application/pdf';
         }
         if ($class==Memoiresinter::class) {
                 $fichier=$this->getParameter('repertoire_memoire_interacademiques').'/'.$entity->getMemoire();
                 $name=$entity->getMemoire();
                 $application= 'application/pdf';
                  }
         if ($class==Photosinter::class)
         {
              $fichier=$this->getParameter('repertoire_photosinter').'/'.$entity->getPhoto();
              $application= 'image/jpeg';
              $name=$entity->getPhoto();
         }
           if ($class==Resumes::class)
         {       
              $fichier=$this->getParameter('repertoire_resumes').'/'.$entity->getResume();
              $name=$entity->getResume();
              $application=  'application/pdf';
         }
         
         
         
         $response = new BinaryFileResponse($fichier);
         
         $disposition = HeaderUtils::makeDisposition(
           HeaderUtils::DISPOSITION_ATTACHMENT,
                 
           $name
                 );
         $response->headers->set('Content-Type', $application); 
         $response->headers->set('Content-Disposition', $disposition);
         
        
         //$content = $this->render('secretariat\lire_memoire.html.twig', array('repertoirememoire' => $this->getParameter('repertoire_memoire_national'),'memoire'=>$fichier));
         return $response; 
         }
    }
    public function updateMemoiresinterEntity($entity)
            {   $repositoryMemoiresinter = $this->getDoctrine()->getRepository('App:Memoiresinter');
                 $repositoryEdition = $this->getDoctrine()->getRepository('App:Edition');
                if(!$entity->getEdition()){
                 
                  $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
                  $entity->setEdition($edition);
                }
                 $equipe = $entity->getEquipe();
                 
                 $memoires= $repositoryMemoiresinter->findByEquipe(['equipe' =>$equipe]);
                 if ($memoires){
                          foreach($memoires as $memoire) {
                              
                              if($memoire->getAnnexe() ==true and $entity->getAnnexe() ==true){
                                  
                                  $memoire->setMemoireFile($entity->getMemoireFile());
                                  
                              }
                              if($memoire->getAnnexe() ==false and $entity->getAnnexe() ==false){
                                  $memoire->setMemoireFile($entity->getMemoireFile());
                                  
                              }
                              parent::persistEntity($entity);
                          }              
                     
                 }
                 if(!$memoires){
                     parent::persistEntity($entity);
                 }
                 
            }
    
    
    public function persistMemoiresinterEntity($entity)
            {   $repositoryMemoiresinter = $this->getDoctrine()->getRepository('App:Memoiresinter');
                 $repositoryEdition = $this->getDoctrine()->getRepository('App:Edition');
                  $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
                  $entity->setEdition($edition);
                 $equipe = $entity->getEquipe();
                 
                 $memoires= $repositoryMemoiresinter->findByEquipe(['equipe' =>$equipe]);
                 if ($memoires){
                          foreach($memoires as $memoire) {
                              
                              if($memoire->getAnnexe() ==true and $entity->getAnnexe() ==true){
                                  $memoire->setMemoireFile($entity->getMemoireFile());
                                  $idmemoire=$memoire->getId();
                                  
                              }
                              if($memoire->getAnnexe() ==false and $entity->getAnnexe() ==false){
                                  $memoire->setMemoireFile($entity->getMemoireFile());
                                   $idmemoire=$memoire->getId();
                              }
                              parent::persistEntity($memoire);
                          }              
                     
                 }
                 if(!$memoires){
                     parent::persistEntity($entity);
                 }
                 
            }
    public function persistFichessecurEntity($entity)
            {     $repositoryEdition = $this->getDoctrine()->getRepository('App:Edition');
                  $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
                  $entity->setEdition($edition);
                parent::persistEntity($entity);
            }
            
       
    public function persistResumesEntity($entity)
            { $repositoryEdition = $this->getDoctrine()->getRepository('App:Edition');
                  $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
                  $entity->setEdition($edition);
                            
              parent::persistEntity($entity);
         } 
    
   public function persistPhotosinterEntity($entity)
            {     $edition=$entity->getEdition();
            if(!$edition){
                  $repositoryEdition = $this->getDoctrine()->getRepository('App:Edition');
                  $edition=$repositoryEdition->findOneBy([], ['id' => 'desc']);
            }
                  $entity->setEdition($edition);
                            
              parent::persistEntity($entity);
         } 
    
    public function persistUserEntity($entity)
    {
        $this->updateUserPassword($entity);
        parent::persistEntity($entity);
    }

    public function updateUserEntity($entity)
    {
        $this->updateUserPassword($entity);
        parent::updateEntity($entity);
    }

    private function updateUserPassword($entity)
    {  
         $password=$entity->getPassword();
        if (method_exists($entity, 'setPassword') ) {
            $entity->setPassword($this->passwordEncoder->encodePassword($entity, $password));
        }
    }
    
    public function telechargerBatchAction(array $ids)
    {
        $class = $this->entity['class'];
        
        $repository = $this->getDoctrine()->getRepository($class);
        $zipFile = new \ZipArchive();
         if ($class==Fichessecur::class) { 
         $FileName= 'Fiches_securite'.date('now');    
         }
        if ($class==Memoiresinterr::class) { 
         $FileName= 'memoires'.date('now');
            
        }
        if ($class==Resumes::class) { 
            $FileName= 'resumes'.date('now');
        }
        if ($class==Photosinter::class) { 
            $FileName= 'photos'.date('now');
        }
        
        
        if ($zipFile->open($FileName, ZipArchive::CREATE) === TRUE)
        {
            foreach ($ids as $id) 
                {


                    $entity = $repository->find($id);
                    if ($class==Fichessecur::class) { 
                       
                    $fichier= $this->getParameter('repertoire_fiches_securite').'/'.$entity->getFiche();}
                    if ($class==Memoiresinter::class) {
                    $fichier=$this->getParameter('repertoire_memoire_interacademiques').'/'.$entity->getMemoire();}
                     if ($class==Resumes::class) {
                    $fichier=$this->getParameter('repertoire_resumes').'/'.$entity->getResume();}
                    if ($class==Photosinter::class) {
                    $fichier=$this->getParameter('repertoire_photosinter').'/'.$entity->getPhoto();}
                    //$nom_memoire=$entity->getMemoire();
                    //$filenameFallback = iconv('UTF-8','ASCII//TRANSLIT',$nom_memoire);
                    $zipFile->addFromString(basename($fichier),  file_get_contents($fichier));//voir https://stackoverflow.com/questions/20268025/symfony2-create-and-download-zip-file

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
                //$content = $this->render('secretariat\lire_memoire.html.twig', array('repertoirememoire' => $this->getParameter('repertoire_memoire_national'),'memoire'=>$fichier));
                return $response; 
    } 
            
   }
                
     

    
    
     public function deleteAction()
    {  $class = $this->entity['class'];    
         
         $entityManager = $this->getDoctrine()->getManager();
        if ($class== 'App\Entity\Equipesadmin') //Pour effacer les élèves d'une équipe qui est supprimée
        {
         
           $repositoryElevesinter = $this->getDoctrine()->getRepository('App:Elevesinter');
           $repositoryEquipesadmin = $this->getDoctrine()->getRepository('App:Equipesadmin');
          $repositoryFichessecur = $this->getDoctrine()->getRepository('App:Fichessecur');
           $repositoryMemoiresinter = $this->getDoctrine()->getRepository('App:Memoiresinter');
           $repositoryResumes = $this->getDoctrine()->getRepository('App:Resumes');
           $id = $this->request->query->get('id');
          $equipe = $repositoryEquipesadmin->find($id);
          $equipe->setCentre(null);
           $eleves= $repositoryElevesinter->findBy(['equipe'=>$equipe]);
           If ($eleves){
               foreach($eleves as $eleve)
               {   $eleve->setEquipe(null);
                   $entityManager->remove($eleve);
                   $entityManager->flush();
                   
               }
           }
           $fichessecur= $repositoryFichessecur->findOneBy(['equipe'=>$equipe]);
           if($fichessecur){
               $fichessecur->setEquipe(null);
              $entityManager->remove($fichessecur);
               $entityManager->flush();
           }
             $memoires= $repositoryMemoiresinter->findByEquipe(['equipe'=>$equipe]);
             
             
           if($memoires){
               foreach($memoires as $memoire)
              
              $memoire->setEquipe(null);
              $entityManager->remove($memoire);
               $entityManager->flush();
          }        
           $resume= $repositoryResumes->findOneByEquipe(['equipe'=>$equipe]);
           if($resume){
               $resume->setEquipe(null);
              $entityManager->remove($resume);
               $entityManager->flush();
        
          }
        }
        if ($class== 'App\Entity\Centrescia')
        {
            $repositoryCentrescia = $this->getDoctrine()->getRepository('App:Centrescia');
            $id = $this->request->query->get('id');
            $centre=$repositoryCentrescia->find($id);   
             $repositoryEquipesadmin = $this->getDoctrine()->getRepository('App:Equipesadmin');
             $equipes=$repositoryEquipesadmin->findByCentre(['centre'=>$centre]);
          foreach($equipes as $equipe){
              $equipe->setCentre(null);
              
              $entityManager->persist($equipe);
               $entityManager->flush();
          }
          $repositoryUser = $this->getDoctrine()->getRepository('App:User');
          
          $users=$repositoryUser->findByCentrecia(['centrecia'=>$centre]);
          foreach($users as $user){
              $user->setCentre(null);
              $entityManager->persist($user);
              $entityManager->flush();
          }
        }
        
      return parent::deleteAction();
    }
    
    public function deleteBatchAction(array $ids):void
    {         $class = $this->entity['class']; 
              $entityManager = $this->getDoctrine()->getManager();
    
              if ($class== 'App\Entity\Equipesadmin'){
             $repositoryEquipesadmin = $this->getDoctrine()->getRepository('App:Equipesadmin');
          $repositoryFichessecur = $this->getDoctrine()->getRepository('App:Fichessecur');
           $repositoryMemoiresinter = $this->getDoctrine()->getRepository('App:Memoiresinter');
           $repositoryResumes = $this->getDoctrine()->getRepository('App:Resumes');     
           $repositoryElevesinter = $this->getDoctrine()->getRepository('App:Elevesinter'); 
           $repositoryMemoires = $this->getDoctrine()->getRepository('App:Memoires');
            foreach($ids as $id){
           $equipe = $repositoryEquipesadmin->find($id);
           $equipe->setCentre(null);
           
           ($equipe);
           $eleves= $repositoryElevesinter->findBy(['equipe'=>$equipe]);
           If ($eleves){
               foreach($eleves as $eleve)
               {   $eleve->setEquipe(null);
                   $entityManager->remove($eleve);
                   $entityManager->flush();
                   
               }
           }
           $fichessecur= $repositoryFichessecur->findOneBy(['equipe'=>$equipe]);
           if($fichessecur){
               $fichessecur->setEquipe(null);
              $entityManager->remove($fichessecur);
               $entityManager->flush();
           }
             $memoires= $repositoryMemoiresinter->findByEquipe(['equipe'=>$equipe]);
             
             
           if($memoires){
               foreach($memoires as $memoire)
              
              $memoire->setEquipe(null);
              $entityManager->remove($memoire);
               $entityManager->flush();
          }        
           $resume= $repositoryResumes->findOneByEquipe(['equipe'=>$equipe]);
           if($resume){
               $resume->setEquipe(null);
              $entityManager->remove($resume);
               $entityManager->flush();
        
          }
            $memoiresnat= $repositoryMemoires->findByEquipe(['equipe'=>$equipe]);
           if($memoiresnat){
               foreach($memoiresnat as $memoire){
               $memoire->setEquipe(null);
              $entityManager->remove($memoire);
               $entityManager->flush();
               
               }
        
          }
          
          
          $entityManager->remove($equipe);
               $entityManager->flush();
        }
         
            }
            if ($class== 'App\Entity\Elevesinter') //Pour effacer les élèves
        {$repositoryElevesinter = $this->getDoctrine()->getRepository('App:Elevesinter');
       
             
           foreach($ids as $id){
               $eleve= $repositoryElevesinter->find(['id'=>$id]);
                  $eleve->setEquipe(null);
                   $entityManager->remove($eleve);
                   $entityManager->flush();
                   
               }
           }
           
            
            
              
        }
        
    
   // public function updateMemoiresEntity($entity)
     //{ 
     // $equipe = $entity->getEquipe();
       //$equipe->setMemoire($entity);  
      // parent::persistEntity($entity);
       
     
    //}
    
    
     
     
}
