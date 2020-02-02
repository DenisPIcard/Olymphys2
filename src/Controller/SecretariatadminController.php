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

use App\Entity\User ;
use App\Entity\Equipes ;
use App\Entity\Eleves ;
use App\Entity\Elevesinter ;
use App\Entity\Edition ;
use App\Entity\Totalusers ;
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
use App\Entity\Rne;

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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\AbstractType;



use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ZipArchive;

    
class SecretariatadminController extends AbstractController
{    
   
     private $passwordEncoder;

    public   $password;
    private $em;

    private $validator;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator,UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->validator = $validator;

       
    
        
        $this->passwordEncoder = $passwordEncoder;
        

       
    }
        /**
         * @Security("is_granted('ROLE_SUPER_ADMIN')")
         * @var Symfony\Component\HttpFoundation\File\UploadedFile $file 
         * @Route("/secretariatadmin/charge_rne", name="secretariatadmin_charge_rne")
         * 
         */         
         public function   charge_rne(Request $request){         
             $defaultData = ['message' => 'Charger le fichier des élèves '];
            $form = $this->createFormBuilder($defaultData)
                            ->add('fichier',      FileType::class)
                            ->add('save',      SubmitType::class)
                            ->getForm();
            
            $repositoryRne = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Rne');
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
                   
                   $value = $worksheet->getCellByColumnAndRow(2, $row)->getValue();//On lit le rne
                   $rne=$repositoryRne->findOneByRne($value);//On vérifie si  cet rne est déjà dans la base
                   if(!$rne){ // si le rne n'existe pas, on le crée
                       $rne= new rne(); 
                    } //sinon on écrase les précédentes données
                    $rne->setRne($value) ;
                    $value = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    $rne->setNature($value);
                    $value = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                    $rne->setSigle($value);
                    $value = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                    $rne->setCommune($value);
                    $value = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                    $rne->setAcademie($value);
                    $value = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                    $rne->setPays($value);
                    $value = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                    $rne->setDepartement($value);
                    $value = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                    $rne->setDenominationPrincipale($value);
                    $value = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                    $rne->setAppellationOfficielle($value);
                    $value = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
                    $rne->setNom($value);
                    $value = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
                    $rne->setAdresse($value);
                    $value = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
                    $rne->setBoitePostale($value);
                    $value = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
                    $rne->setCodePostal($value);
                    $value = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
                    $rne->setAcheminement($value);
                    $value = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
                    $rne->setCoordonneeX($value);
                    $value = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
                    $rne->setCoordonneeY($value);
                    $em->persist($rne);
                    $em->flush();
                   
                   }
                 return $this->redirectToRoute('core_home');
                }
            $content = $this
                        ->renderView('secretariatadmin\charge_donnees_excel.html.twig', array('form'=>$form->createView(),'titre'=>'Enregistrer le RNE'));
            return new Response($content);  
 
         }

        /**
         * @Security("is_granted('ROLE_SUPER_ADMIN')")
         * @var Symfony\Component\HttpFoundation\File\UploadedFile $file 
         * @Route("/secretariatadmin/charge_eleves_inter", name="secretariatadmin_charge_eleves_inter")
         * 
         */         
         public function   charge_eleves_inter(Request $request){         
                 $defaultData = ['message' => 'Charger le fichier des élèves '];
            $form = $this->createFormBuilder($defaultData)
                            ->add('fichier',      FileType::class)
                            ->add('save',      SubmitType::class)
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
                   
                   $value = $worksheet->getCellByColumnAndRow(1, $row)->getValue();//On lit le nom de l'élève
                   $numsite=$value;//idsite est l'id du site odpf
                   $eleve=$repositoryElevesinter->findOneByNumsite(['numsite'=>$numsite]);//On vérifie si  cet élèves est déjà dans la base
                   if(!$eleve){ // si l'éleve n'existe pas, on le crée
                       $eleve= new elevesinter(); 
                       $eleve->setNumsite($numsite) ;  
                                   } //sinon on écrase les précédentes données
                                
                        $nom = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
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
                        $user=$repositoryEquipesadmin->findOneByNumero(['numero'=>$value]);
                         if($user) {                    
                         $eleve->setEquipe($user) ;
                         
                         }
                        $em->persist($eleve);


                         $em->flush();
                   
                   }
                    return $this->redirectToRoute('core_home');
                }
        $content = $this
                        ->renderView('secretariatadmin\charge_donnees_excel.html.twig', array('form'=>$form->createView(),'titre'=>'Enregistrer les élèves'));
	return new Response($content);          
        }
         /**
         * @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatadmin/charge_equipeinter", name="secretariatadmin_charge_equipeinter")
         * 
         */
	public function charge_equipeinter(Request $request)
	{ 

            $defaultData = ['message' => 'Charger le fichier '];
            $form = $this->createFormBuilder($defaultData)
                            ->add('fichier',      FileType::class)
                            ->add('save',      SubmitType::class)
                            ->getForm();
            
            $repositoryEquipesadmin = $this
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
                   
                   $value = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                   $numero=$value;
                   $equipe=$repositoryEquipesadmin->findOneByNumero($numero);
                   if(!$equipe){
                   $equipe= new equipesadmin(); }
                        $equipe->setNumero($numero) ;
                        $value = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                        $equipe->setLettre($value);
                        $value = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                        $equipe->setNomLycee($value);
                        $value = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                        $equipe->setDenominationLycee($value);
                        $value = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                        $equipe->setRne($value);
                        $value = $worksheet->getCellByColumnAndRow(11, $row)->getValue(); 
                        $equipe->setLyceeLocalite($value) ;
                        $value = $worksheet->getCellByColumnAndRow(12, $row)->getValue(); 
                        $equipe->setLyceeAcademie($value) ;
                        $value = $worksheet->getCellByColumnAndRow(3, $row)->getValue(); 
                        $equipe->setTitreProjet($value) ;
                        $prenomProf1 = $worksheet->getCellByColumnAndRow(22, $row)->getValue(); 
                        $equipe->setPrenomProf1($prenomProf1) ;
                        $nomProf1 = $worksheet->getCellByColumnAndRow(23, $row)->getValue();
                        $equipe->setNomProf1($nomProf1) ;
                        $prenomProf2 = $worksheet->getCellByColumnAndRow(24, $row)->getValue(); 
                        $equipe->setPrenomProf2($prenomProf2) ;
                        $nomProf2 = $worksheet->getCellByColumnAndRow(25, $row)->getValue();
                        $equipe->setNomProf2($nomProf2) ;
                        $repositoryUser= $this->getDoctrine()
		->getManager()
		->getRepository('App:User');
                        
                        $qb1 =$repositoryUser->createQueryBuilder('u')->select('u')
                                 ->where('u.nom=:nomprof1')
                                 ->setParameter('nomprof1', $nomProf1)
                                 ->andwhere('u.prenom=:prenomprof1')
	                ->setParameter('prenomprof1', $prenomProf1);
                        $prof1=$qb1->getQuery()->getResult();
                        foreach($prof1 as $prof){
                        $equipe->setIdProf1($prof->getId()) ;
                         }
                         $qb2 =$repositoryUser->createQueryBuilder('u')->select('u')
                                 ->where('u.nom=:nomprof2')
                                 ->setParameter('nomprof2', $nomProf2)
                                 ->andwhere('u.prenom=:prenomprof2')
	                ->setParameter('prenomprof2', $prenomProf2);
                        $prof2=$qb2->getQuery()->getResult();
                        
                        
                        foreach($prof2 as $prof){
                        $equipe->setIdProf2($prof->getId()) ;
                        }
                        
                        
                        
                        
                        
                        $equipe->setSelectionnee(0);
                        $em->persist($equipe);


                         $em->flush();
                   
                   }
                    return $this->redirectToRoute('core_home');
                }
        $content = $this
                        ->renderView('secretariatadmin\charge_donnees_excel.html.twig', array('form'=>$form->createView(),'titre'=>'Enregistrer les équipes'));
	return new Response($content);          
        }       
       
        /**
         * @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatadmin/charge_user", name="secretariatadmin_charge_user")
         * 
         */
        
        
        public function charge_user(Request $request)
        {
             $defaultData = ['message' => 'Charger le fichier '];
            $form = $this->createFormBuilder($defaultData)
                            ->add('fichier',      FileType::class)
                            ->add('save',      SubmitType::class)
                            ->getForm();
            
            $repositoryUser = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:User');
            
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
                   
                   $value = $worksheet->getCellByColumnAndRow(2, $row)->getValue();//on récupère le username
                   $username=$value;
                   $user=$repositoryUser->findOneByUsername($username);
                   if(!$user){
                   $user= new user(); 
                   
                            } //si l'user n'est pas existant on le crée sinon on écrase les anciennes valeurs pour une mise à jour 
                        $user->setUsername($username) ;
                        $value = $worksheet->getCellByColumnAndRow(3, $row)->getValue();//on récupère le role
                        ($value);
                        $user->setRoles([$value]);
                        $value = $worksheet->getCellByColumnAndRow(4, $row)->getValue();//password
                        $password= $this->passwordEncoder->encodePassword($user, $value);
                        $user->setPassword($password);
                        $value = $worksheet->getCellByColumnAndRow(5, $row)->getValue();//actif
                        $user->setIsactive($value);
                        $value = $worksheet->getCellByColumnAndRow(6, $row)->getValue();//email
                        $user->setEmail($value);
                       
                        $value = $worksheet->getCellByColumnAndRow(8, $row)->getValue(); //password request at
                        $user->setPasswordRequestedAt($value) ;
                        $value = $worksheet->getCellByColumnAndRow(9, $row)->getValue(); //rne
                        $user->setrne($value) ;
                        $value = $worksheet->getCellByColumnAndRow(10, $row)->getValue(); //adresse
                        $user->setAdresse($value) ;
                        $value = $worksheet->getCellByColumnAndRow(11, $row)->getValue(); //ville
                        $user->setVille($value) ;
                        $value = $worksheet->getCellByColumnAndRow(12, $row)->getValue();//code
                        $user->setCode($value) ;
                        $value = $worksheet->getCellByColumnAndRow(13, $row)->getValue(); //nom
                        $user->setNom($value) ;
                        $value = $worksheet->getCellByColumnAndRow(14, $row)->getValue();//prenom
                        $user->setPrenom($value) ;
                        $value = $worksheet->getCellByColumnAndRow(15, $row)->getValue();//phone
                        $user->setPhone($value) ;
                        $errors = $this->validator->validate($user);
                        if (count($errors) > 0) {
                                    $errorsString = (string) $errors;
                                    throw new \Exception($errorsString);
                                }
                        $em->persist($user);


                         $em->flush();
                   }
                   
                    return $this->redirectToRoute('core_home');
                }
        $content = $this
                        ->renderView('secretariatadmin\charge_donnees_excel.html.twig', array('form'=>$form->createView(),'titre'=>'Enregistrer les users'));
	return new Response($content);   
       
        }
         /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatadmin/charge_equipe1", name="secretariatadmin_charge_equipe1")
         * 
         */
/*	public function charge_equipe1(Request $request)
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

                  return $this->redirectToRoute('secretariat_accueil');
            }
        $content = $this
                        ->renderView('secretariat\uploadexcel.html.twig', array('form'=>$form->createView(),));
	return new Response($content);          
        }       
 */       
             
        /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatadmin/cree_equipes", name="secretariatadmin_cree_equipes")
         * 
         */
	public function cree_equipes(Request $request)
	{ 

            $defaultData = ['message' => 'Charger le fichier Équipe2'];
            $form = $this->createFormBuilder($defaultData)
                         ->add('Créer',      SubmitType::class)
                          ->getForm();
            
            $repositoryEquipesadmin = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Equipesadmin');
            $repositoryEquipes = $this
			->getDoctrine()
			->getManager()
			->getRepository('App:Equipes');
            $form->handleRequest($request);                            
            if ($form->isSubmitted() && $form->isValid()) 
                {
                $listEquipes=$repositoryEquipesadmin ->createQueryBuilder('e')
                                                     ->select('e')
                                                     ->where('e.selectionnee= TRUE')
                                                     ->orderBy('e.lettre','ASC')
                                                     ->getQuery()
                                                     ->getResult();
		$em = $this->getDoctrine()->getManager();
                foreach ($listEquipes as $equipeadm)  
                   {
                   // dd($equipeadm);
                   $lettre=$equipeadm->getLettre();
                   $equipe= new equipes(); 
                   $info=$repositoryEquipesadmin->findOneByLettre($lettre);
                   $equipe->setInfoequipe($info);
                   $lettre=$equipeadm->getLettre();
                   $equipe->setLettre($lettre) ;
                   $nomEq=$equipeadm->getTitreProjet();                   
                   $equipe->setTitreProjet($nomEq);
                   $em->persist($equipe);
                    }
                    $em->flush();
                    return $this->redirectToRoute('core_home');
                }
        $content = $this
                        ->renderView('secretariatadmin\creer_equipes.html.twig', array('form'=>$form->createView(),));
	return new Response($content);          
        }
        
        /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatadmin/charge_jures", name="secretariatadmin_charge_jures")
         * 
         */
	public function charge_jures(Request $request)
	{ 

            $defaultData = ['message' => 'Charger le fichier Jures'];
            $form = $this->createFormBuilder($defaultData)
                            ->add('fichier',      FileType::class)
                            ->add('save',      SubmitType::class)
                            ->getForm();
            
            
            $form->handleRequest($request);                            
            if ($form->isSubmitted() && $form->isValid()) 
                {
                $data=$form->getData();
                $fichier=$data['fichier'];
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fichier);
                $worksheet = $spreadsheet->getActiveSheet();
            
                $highestRow = 20;              
 
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
                    $em->flush();
                    }                     
                   
                    return $this->redirectToRoute('core_home');
                }
                $content = $this
                        ->renderView('secretariatadmin\charge_donnees_excel.html.twig', array('titre'=>'Remplissage de la table Jurés','form'=>$form->createView(),));
                return new Response($content);
        }
        /**
	* @Security("is_granted('ROLE_SUPER_ADMIN')")
         * 
         * @Route("/secretariatadmin/charge_equipe_id_rne", name="secretariatadmin_charge_equipe_id_rne")
         * 
         */
	public function charge_equipe_id_rne(Request $request)
	{ 
                    $repositoryEquipes=$this->getDoctrine()
			->getManager()
			->getRepository('App:Equipesadmin');
                   $repositoryRne=$this->getDoctrine()
			->getManager()
			->getRepository('App:Rne');
                  $equipes= $repositoryEquipes->findAll();
                  $em=$this->getDoctrine()->getManager();
                  $rnes= $repositoryRne->findAll();
                  foreach($equipes as $equipe){
                      foreach($rnes as $rne){
                          if ($rne->getRne()==$equipe->getRne()){
                          $equipe->setRneId($rne);
                          }
                      } 
                   $em->persist($equipe);
                   $em->flush();
                      
                          }
                 return $this->redirectToRoute('core_home');
       
            
          
                   }
        
}