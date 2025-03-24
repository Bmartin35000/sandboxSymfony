<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\Type\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RecipeController extends AbstractController
{
    #[Route('/recipe', name: 'app_recipe')]
    public function findAll(RecipeRepository $recipeRepository): Response
    {
        $recipes = $recipeRepository->findAll();
        if(!$recipes){
            throw $this->createNotFoundException('No recipes found');
        }
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recipe/create', name:'getCreateRecipeForm', methods:["GET"])]
    public function getCreateRecipeForm(RecipeType $recipeType):Response{
        $recipe = new Recipe();
        $formBuilder = $this->createFormBuilder($recipe);
        $recipeType->buildForm($formBuilder);
        $form = $formBuilder-> getForm();
        return $this->render('recipe/form.html.twig', [
            "form"=>$form
        ]);
    }

    #[Route('/recipe/create', name:'create_recipe', methods:["POST"])]
    public function createRecipe(EntityManagerInterface $entityManager, ValidatorInterface $validator):Response{
        $recipe = new Recipe();
        $recipe->setTitle('test');
        $recipe->setDescription('test');

        $errors = $validator->validate($recipe);
        if (count($errors)>0) {
            return new Response((string) $errors, 400);
        }
        // prepare query
        $entityManager->persist($recipe);
        // execute query
        $entityManager->flush();
        return new Response('Saved new Recipe with id : '.$recipe->getId());
    }
}
