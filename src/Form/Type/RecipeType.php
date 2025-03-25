<?php
namespace App\Form\Type;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options=[]): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextType::class,['required'=>false])
            ->add('imageFile', FileType::class,[
                'required'=>false,
                'mapped'=>false,
                'constraints' => [
                    new File([
                        'maxSize' => '5024k',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image document',
                    ])]])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }

    public function getDefaultOptions(array $options) // disabling csrf protection
    {
        return array(
            'csrf_protection' => false,
        );
    }
}