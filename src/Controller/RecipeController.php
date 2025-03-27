<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\Type\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Psr\Log\LoggerInterface;
use Doctrine\Common\Collections\ArrayCollection;

final class RecipeController extends AbstractController
{
    const CLASS_NAME="Recettes";

    #[Route('/recipe', name: 'getRecipes')]
    public function findAll(RecipeRepository $recipeRepository): Response
    {
        $recipes = $recipeRepository->findAll();
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
            'className'=> RecipeController::CLASS_NAME,
            'route'=> 'getRecipes'
        ]);
    }

    #[Route('/recipe/{{id}}', name: 'getRecipe')]
    public function show(Recipe $recipe): Response
    {
        if(!$recipe){
            throw $this->createNotFoundException('No recipe found');
        }
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            'className'=> RecipeController::CLASS_NAME,
            'route'=> 'getRecipe'
        ]);
    }

    #[Route('/recipe/edit/{{id}}', name:'editRecipe')]
    public function edit(LoggerInterface $logger, Recipe $recipe, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator):Response{        
        return $this->new($logger, $recipe, $request, $entityManager, $validator, 'editRecipe');
    }

    #[Route('/recipe/create', name:'newRecipe')]
    public function new(LoggerInterface $logger, Recipe|null $recipe, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, ?string $routeName='newRecipe'):Response{
        // Create new or edit form
        $method = "";
        if(!$recipe){
            $recipe = new Recipe();
            $method = "create";
        } else {
            $initialInstructions = new ArrayCollection();
            $method = "edit";
            foreach ($recipe->instructions as $instruction) {
                $initialInstructions->add($instruction);
            }
        }

        $form =$this->createForm(RecipeType::class, $recipe); // will find buildForm method

        $form->handleRequest($request); // créé un thread qui attend un submit et écrit la data renseigné dans le recipe du form une fois submit

        if ($form->isSubmitted() && $form->isValid()) { // différencie get et post
            $recipe=$form->getData();

            // Deleting removed instructions
            if ($method == "edit") {
                foreach ($initialInstructions as $oldInstr) {
                    if (!$recipe->instructions->contains($oldInstr)) {
                        $entityManager->remove($oldInstr);
                    }
                }
            }            

            $errors = $validator->validate($recipe);
            if (count($errors)>0) {
                return new Response((string) $errors, 400);
            }

            // retrieve new image
            $file = $form['imageFile']->getData();

            // removing previous image file if a new one is set
            if ($recipe->image && $file) {
                unlink($this->getParameter("public_directory").'/images/uploads/'.$recipe->image);
                $recipe->image = null;
            }

            // uploading locally the new image file
            if($file){
                $dest = $this->getParameter('kernel.project_dir').'/public/images/uploads';
                $uniqueFileName = uniqid().'-'.$file->getClientOriginalName();
                $recipe->image = $uniqueFileName; // set the file name in db
                $file->move(
                    $dest, 
                    $uniqueFileName
                );
            }
            
            // set the recipe on the instructions
            foreach ($recipe->instructions as $instruction) {
                $instruction->recipe = $recipe;
            }

            // prepare query
            $entityManager->persist($recipe);
            // execute query
            $entityManager->flush();

            return $this->redirectToRoute('getRecipes');
        }

        return $this->render('recipe/form.html.twig', [
            "form"=>$form,
            'className'=> RecipeController::CLASS_NAME,
            'route'=> $routeName
        ]);
    }

    #[Route('/recipe/delete/{{id}}', name:'deleteRecipe')]
    public function delete(Recipe $recipe, EntityManagerInterface $entityManager):Response{
        if(!$recipe){
            throw $this->createNotFoundException('No recipe found');
        }

         // removing image file if exists
         if ($recipe->image) {
            unlink($this->getParameter("public_directory").'/images/uploads/'.$recipe->image);
        }

        // prepare query
        $entityManager->remove($recipe);
        // execute query
        $entityManager->flush();

        return $this->redirectToRoute('getRecipes');
    }
}
