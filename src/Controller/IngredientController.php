<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    /**
     * This function display all ingredient
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'app_ingredient', methods: ['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredientsp = $paginator->paginate(
            $repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/ingredient/index.html.twig', [
            'ingredientst' => $ingredientsp
        ]);
    }

    /**
     * This function for add ingredient
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/nouveau', 'nouveau_ingredient', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre ingredient à été créé avec succès !'
            );
            return $this->redirectToRoute('app_ingredient');
        }

        return $this->render('pages/ingredient/new.html.twig', [
            'ingrediontForm' => $form->createView()
        ]);
    }

    /**
     * This function for update the ingredient
     *
     * @param IngredientRepository $repo_ingredient
     * @param integer $id
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/edition/{id}', 'edition_ingredient', methods: ['GET', 'POST'])]
    public function edit(IngredientRepository $repo_ingredient, int $id, Request $request, EntityManagerInterface $manager): Response
    {
        $ingredient = $repo_ingredient->findOneBy(['id' => $id]);
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre ingredient à été modifié avec succès !'
            );
            return $this->redirectToRoute('app_ingredient');
        }

        return $this->render('pages/ingredient/edit.html.twig', [
            'ingrediontForm' => $form->createView()
        ]);
    }

    /**
     * This function for delete the ingredient
     *
     * @param IngredientRepository $repo_ingredient
     * @param integer $id
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/suppression/{id}', 'suppression_ingredient', methods: ['GET'])]

    public function suppression(IngredientRepository $repo_ingredient, int $id, EntityManagerInterface $manager): Response
    {
        $ingredient = $repo_ingredient->findOneBy(['id' => $id]);

        if (!$ingredient) {
            $this->addFlash(
                'success',
                'L\'ingredient n\'à pas été trouvé !'
            );
            return $this->redirectToRoute('app_ingredient');
        }
        $manager->remove($ingredient);
        $manager->flush();
        $this->addFlash(
            'success',
            'Votre ingredient ' . $ingredient->getName() . ' à été supprimé avec succès !'
        );
        return $this->redirectToRoute('app_ingredient');
    }
}
