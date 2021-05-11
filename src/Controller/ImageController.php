<?phpnamespace App\Controller;use App\Entity\Image;use App\Form\ImageType;use Doctrine\ORM\EntityManagerInterface;use http\Exception\BadMessageException;use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;use Symfony\Component\HttpFoundation\Request;use Symfony\Component\HttpFoundation\Response;use Symfony\Component\Routing\Annotation\Route;class ImageController extends AbstractController{    /**     * @Route("/image/add", name="image_add")     *     * @param EntityManagerInterface $entityManager     * @param Request $request     * @return Response     */    public function addImage(EntityManagerInterface $entityManager, Request $request): Response    {        // TODO: need to fix add mutiples images        $image = new Image();        $form = $this->createForm(ImageType::class, $image);        $form->handleRequest($request);        if ($form->isSubmitted() && $form->isValid()) {            $image = $form->getData();            $entityManager->persist($image);            $entityManager->flush();            return $this->redirectToRoute('home');        }        return $this->render('image/add_image.html.twig', ['form' => $form->createView()]);    }    /**     * @Route("/image/{id}/edit", name="image_edit")     *     * @param Image $image     * @param Request $request     * @param EntityManagerInterface $entityManager     * @return Response     */    public function editImage(Image $image, Request $request, EntityManagerInterface $entityManager): Response    {        $form = $this->createForm(ImageType::class, $image);        $form->handleRequest($request);        if ($form->isSubmitted() && $form->isValid()) {            $image = $form->getData();            $entityManager->persist($image);            $entityManager->flush();            return $this->redirectToRoute('home');        }        return $this->render('image/edit.html.twig', ['form' => $form->createView(), 'image' => $image]);    }    /**     * @Route("/main-image/{id}/edit", name="main_image_edit")     *     * @param Image $image     * @param Request $request     * @param EntityManagerInterface $entityManager     * @return Response     */    public function editMainImage(Image $image, Request $request, EntityManagerInterface $entityManager): Response    {        $form = $this->createForm(ImageType::class, $image);        $form->handleRequest($request);        if ($form->isSubmitted() && $form->isValid()) {            $image = $form->getData();            $entityManager->persist($image);            $entityManager->flush();            return $this->redirectToRoute('home');        }        return $this->render('image/main_image_edit.html.twig', ['form' => $form->createView(), 'image' => $image]);    }    /**     * @Route("/main-image/{id}/delete", name="main_image_delete")     *     * @param Image $image     * @param Request $request     * @param EntityManagerInterface $entityManager     * @return Response     */    public function deleteMainImage(Image $image, Request $request, EntityManagerInterface $entityManager): Response    {        $collectionOfImages = $image->getTrick()->getImages();        $nbOfImages = $collectionOfImages->count();        // TODO: personalize message and redirect        // If there is one image or less, it is impossible to delete the main image because it cannot be replaced.        if ($nbOfImages <= 1) throw new BadMessageException('can\'t delete last image');        $entityManager->remove($image);        $entityManager->flush();        // We delete the main image, we must assign a new main image        $trick = $image->getTrick();        $newMainImage = $trick->getImages()->first();        $trick->setMainImage($newMainImage);        $entityManager->persist($trick);        $entityManager->flush();        $url = $request->headers->get('referer');        return $this->redirect($url);    }    /**     * @Route("/image/{id}/delete", name="image_delete")     *     * @param Image $image     * @param Request $request     * @param EntityManagerInterface $entityManager     * @return Response     */    public function deleteImage(Image $image, Request $request, EntityManagerInterface $entityManager): Response    {        $entityManager->remove($image);        $entityManager->flush();        $url = $request->headers->get('referer');        return $this->redirect($url);    }}