<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    #[Route('/recipe', name: 'app_recipe', methods: ['GET'])]
    /**
     * This function for get all recipes
     *
     * @param PaginatorInterface $paginator
     * @param RecipeRepository $repository
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, RecipeRepository $repository, Request $request): Response
    {
        $recipesp = $paginator->paginate(
            $repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipesp' => $recipesp,
        ]);
    }

    /**
     * This function for add the recipe
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recette/nouvelle', 'nouvelle_recette', methods: ['GET', 'POST'])]

    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = new Recipe();
        $recipeForm = $this->createForm(RecipeType::class, $recipe);

        $recipeForm->handleRequest($request);

        if ($recipeForm->isSubmitted() && $recipeForm->isValid()) {
            $recipes = $recipeForm->getData();
            $manager->persist($recipes);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre recette à été créé avec succès !'
            );
            return $this->redirectToRoute('app_recipe');
        }

        return $this->render('pages/recipe/new.html.twig', [
            'recipeForm' => $recipeForm->createView()
        ]);
    }

    /**
     * This function for update the recipe
     *
     * @param RecipeRepository $repo_recipe
     * @param integer $id
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recette/edition/{id}', 'edition_recette', methods: ['GET', 'POST'])]
    public function edit(RecipeRepository $repo_recipe, int $id, Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = $repo_recipe->findOneBy(['id' => $id]);
        $recipeForm = $this->createForm(RecipeType::class, $recipe);

        $recipeForm->handleRequest($request);

        if ($recipeForm->isSubmitted() && $recipeForm->isValid()) {
            $recipes = $recipeForm->getData();
            $manager->persist($recipes);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre recette à été modifié avec succès !'
            );
            return $this->redirectToRoute('app_recipe');
        }

        return $this->render('pages/recipe/edit.html.twig', [
            'recipeForm' => $recipeForm->createView()
        ]);
    }

    /**
     * This function for delete the recipe
     *
     * @param RecipeRepository $repo_recipe
     * @param integer $id
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recette/suppression/{id}', 'suppression_recette', methods: ['GET'])]
    public function suppression(RecipeRepository $repo_recipe, int $id, EntityManagerInterface $manager): Response
    {
        $recipe = $repo_recipe->findOneBy(['id' => $id]);

        if (!$recipe) {
            $this->addFlash(
                'success',
                'La recette n\'à pas été trouvé !'
            );
            return $this->redirectToRoute('app_ingredient');
        }
        $manager->remove($recipe);
        $manager->flush();
        $this->addFlash(
            'success',
            'Votre recette ' . $recipe->getName() . ' à été supprimé avec succès !'
        );
        return $this->redirectToRoute('app_recipe');
    }
}
