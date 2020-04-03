<?php
// src/Controller/ComiteController.php
namespace App\Controller;

use App\Utils\ExcelCreate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response ;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class ComiteController extends AbstractController
    {
        
     /**
     * @Security("is_granted('ROLE_COMITE')")
     *
     * @Route("comite/frais_lignes", name="comite_frais_lignes")
     */
	public function frais_lignes(Request $request)
        {
       // $user=$this->getUser();
        
        $repositoryEdition=$this
			->getDoctrine()
			->getManager()
			->getRepository('App:Edition');
                
        $ed=$repositoryEdition->findOneByEd('ed');
        $edition=$ed->getEdition();
         
        $task= ['message' => '1'];
        $form = $this->createFormBuilder($task)
            ->add('nblignes', IntegerType::class, ['label' => 'De combien de lignes avez vous besoin'])
            ->add('Entrée', SubmitType::class)
            ->getForm();
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
            {
            $data=$form->getData();
            $nblig=$data['nblignes'];
            
            return $this->redirectToroute('comite_frais', ['nblig' => $nblig] );
            }
        $content = $this->get('templating')->render('comite/frais_lignes.html.twig', ['edition' =>$edition, 'form'=>$form->createView()]);		
        return new Response($content);
        }
        
     /**
     * @Security("is_granted('ROLE_COMITE')")
     *
     * @Route("comite/frais/{nblig}", name="comite_frais", requirements={"nblig"="\d{1}|\d{2}"})
     */
     public function frais(Request $request, ExcelCreate $create, $nblig)
        {
        //    Debug::enable();
        //    $user=$this->getUser();
            
            $repositoryEdition=$this
			->getDoctrine()
			->getManager()
			->getRepository('App:Edition');
                
            $ed=$repositoryEdition->findOneByEd('ed');
            $edition=$ed->getEdition();
            
            $task=['nblig' => $nblig];
            
            $formBuilder = $this->createFormBuilder($task);

            for ($i=1 ; $i<=$nblig ; $i++)
                {
                $formBuilder  ->add('date'.$i, DateType::class, [ 'widget' => 'single_text'])
                        ->add('designation'.$i, TextType::class)
                        ->add('deplacement'.$i, MoneyType::class, ['required'=>false])
                        ->add('repas'.$i, MoneyType::class, ['required'=>false])
                        ->add('fournitures'.$i, MoneyType::class, ['required'=>false])
                        ->add('poste'.$i, MoneyType::class, ['required'=>false])
                        ->add('impressions'.$i, MoneyType::class, ['required'=>false])
                        ->add('autres'.$i, MoneyType::class, ['required'=>false]);
              }
            $formBuilder->add('iban1', TextType::class, ['required'=>false]);
            for ($j=2; $j<8; $j++)
                {
                $formBuilder->add('iban'.$j, NumberType::class, ['required'=>false]);
                }
            $formBuilder->add('Entrée', SubmitType::class);
            $form=$formBuilder->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
                {
                $data=$form->getData();
                $nblig=$data['nblig'];
                dump($nblig);
                $fichier = $create->excelfrais($edition, $data, $nblig);
                dump($fichier);
                
                return $this->redirectToRoute('comite_envoi_frais',[ 'fichier'=> $fichier ]);

                }
            $content = $this->get('templating')->render('comite/frais.html.twig', ['edition' => $edition, 'nblig'=>$nblig,'form'=>$form->createView()]);		
            return new Response($content);
        
        }

     /**

     * @Route("comite/envoi_frais {fichier}", name="comite_envoi_frais")
     */
     public function envoi_frais(Request $request, MailerInterface $mailer, $fichier)
        {
      //  $user=$this->getUser();
      //  $name=$user->getLastname();   
        $task=['nblig' => 2 ];
            
        $formBuilder = $this->createFormBuilder($task);
        $formBuilder ->add('choix',   ChoiceType::class, ['choices'=>['Envoi par moi même'=>false, 'Envoi Automatique'=>true]])
                     ->add('fichier', FileType::class);
        $formBuilder->add('Entrée', SubmitType::class);
        $form=$formBuilder->getForm();
         //    dump($form);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
            {
            $email=(new TemplatedEmail())
                    ->from(new Address('info@olymphys.fr','Équipe Olymphys'))
                    ->to(new Address($user->getEmail(), $user->getNom()))
                    ->subject('Bienvenue sur Olymphys')
                    ->htmlTemplate('email/bienvenue.html.twig')
                    ->context([
                        'user' => $user,
                        ])
                    ->attach($fichier)
                            ;
            $mailer->send($email);
            $request->getSession()->getFlashBag()->add('success', "Un mail va vous être envoyé.");        
 
  
            $this->getMailer()->send($message);
            return $this->redirectToroute('core_home' );
            }
        $content = $this->get('templating')->render('comite/envoi_frais.html.twig', ['form'=>$form->createView()]);	
        return new Response($content);
        
        }



}