<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\DownloadOptions;
use App\Entity\Nomenclature;
use App\Form\CardType;
use App\Form\DownloadOptionsType;
use App\Form\NomenclatureType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Image;
use Symfony\Component\HttpFoundation\Response;
use League\Flysystem\Filesystem;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

// Include Dompdf required namespaces
use Dompdf\Dompdf;
use Dompdf\Options;

class CardController extends AbstractController
{
    /**
     * @Route("/card/", name="card_index")
     */
    public function index() {
      $nomenclatures = $this->getDoctrine()
        ->getRepository(Nomenclature::class)
        ->findAll();

        return $this->render('card/index.html.twig', [
            'controller_name' => 'CardController',
            'nomenclatures' => $nomenclatures,
        ]);
    }

    /**
     * @Route("/nomenclature/user", name="nomenclature_user")
     * @IsGranted("ROLE_USER")
     */
    public function cardsUser() {
        $user = $this->getUser()->getId();
        $nomenclatures = $this->getDoctrine()
          ->getRepository(Nomenclature::class)
          ->findByCreatedBy($user);
  
          return $this->render('card/list.html.twig', [
              'controller_name' => 'CardController',
              'nomenclatures' => $nomenclatures,
        ]);
    }

    /**
     * @Route("/nomenclature/{id}", name="nomenclature_edit")
     * @IsGranted("ROLE_USER")
     */
    public function edit(Nomenclature $nomenclature, Request $request) {    
        //$nomenclature = new Nomenclature();
        //$card = new Card();
        //$nomenclature->addCard($card);
        $form = $this->createForm(NomenclatureType::Class,$nomenclature);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nomenclature = $form->getData();

            // assign the nomenclature to the current user
            $currentUser= $this->getUser();
            $nomenclature->setCreatedBy($currentUser);

            // set card language with the same language as the nomenclature
            $nomLang = $nomenclature->getLanguage(); 
            foreach ($nomenclature->getCards() as $card){
                $card->setLanguage($nomLang);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($nomenclature);
            $entityManager->flush();
            return $this->render('card/upload-success.html.twig');
        }

        return $this->render('card/upload.html.twig', [
            'formNomenclature' => $form->createView(),
        ]);
    }
  
    /**
     * @Route("/nomenclature/upload", name="nomenclature_upload")
     * @IsGranted("ROLE_USER")
     */
    public function uploadNomenclature(Request $request) {    
        $nomenclature = new Nomenclature();
        $card = new Card();
        $nomenclature->addCard($card);
        $form = $this->createForm(NomenclatureType::Class,$nomenclature);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nomenclature = $form->getData();

            // assign the nomenclature to the current user
            $currentUser= $this->getUser();
            $nomenclature->setCreatedBy($currentUser);

            // set card language with the same language as the nomenclature
            $nomLang = $nomenclature->getLanguage(); 
            foreach ($nomenclature->getCards() as $card){
                $card->setLanguage($nomLang);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($nomenclature);
            $entityManager->flush();
            return $this->render('card/upload-success.html.twig');
        }

        return $this->render('card/upload.html.twig', [
            'formNomenclature' => $form->createView(),
        ]);
    }

    /**
     * @Route("/card/upload", name="card_upload")
     * @IsGranted("ROLE_USER")
     */
    /*
    public function uploadCard(Request $request) {
        $card = new Card();
        $form = $this->createForm(CardType::Class,$card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $card = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($card);
            $entityManager->flush();
            return $this->render('card/upload-success.html.twig');
        }

        return $this->render('card/upload.html.twig', [
            'formNomenclature' => $form->createView(),
        ]);
    }
    */

    /**
     * @Route("/card/{id}/download-options", name="card_download_options")
     * @IsGranted("ROLE_USER")
     */
    public function downloadOptions(Nomenclature $nomenclature, Request $request) {

        $options = new DownloadOptions();
        $options->setNomenclature($nomenclature->getId());
        $form = $this->createForm(DownloadOptionsType::Class,$options);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $options = $form->getData();


                $html = $this->renderView('card/print.html.twig', [
                    'nomenclature' => $nomenclature,
                ]);
          
                $pdfOptions = new Options();
                $pdfOptions->set('isRemoteEnabled', true);
                
                // Instantiate Dompdf with our options
                $dompdf = new Dompdf($pdfOptions);
          
                // Load HTML to Dompdf
                $dompdf->loadHtml($html);

                $format = $options->getFormat();
           
                // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
                switch ($format) {
                    case "A4":
                        $dompdf->setPaper('A4', 'portrait');
                        break;
                    case "US":
                        $dompdf->setPaper('letter', 'portrait');
                        break;
                    default:
                        $dompdf->setPaper('A4', 'portrait');
                        break;
                }
                
                // Render the HTML as PDF
                $dompdf->render();
                //
                // Output the generated PDF to Browser (inline view)
                $dompdf->stream("card-".$format.".pdf", [
                    "Attachment" => false
                ]);



            //return $this->render('card/upload-success.html.twig');
        }

        return $this->render('card/download-options.html.twig', [
            'formOptions' => $form->createView(),
        ]);
    }

    /**
     * @Route("/card/{id}/download", name="card_download")
     * @IsGranted("ROLE_USER")
     */
    public function download(Nomenclature $nomenclature) {
      $html = $this->renderView('card/print.html.twig', [
          'nomenclature' => $nomenclature,
      ]);

      $pdfOptions = new Options();
      $pdfOptions->set('isRemoteEnabled', true);
      //
      // Instantiate Dompdf with our options
      $dompdf = new Dompdf($pdfOptions);

      // Load HTML to Dompdf
      $dompdf->loadHtml($html);

      // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
      $dompdf->setPaper('A4', 'portrait');

      // Render the HTML as PDF
      $dompdf->render();
      //
      // Output the generated PDF to Browser (inline view)
      $dompdf->stream("card.pdf", [
          "Attachment" => false
      ]);
    }

    /**
     * @Route("/card/image/{id}", name="image")
     */
    public function image(Image $image, Filesystem $filesystem) {
      //$this->get('oneup_flysystem.image_adapter');
      $response = new Response($filesystem->read($image->getName()));
      $response->headers->set('Content-Type', $filesystem->getMimetype($image->getName()));
      return $response;
    }
}
