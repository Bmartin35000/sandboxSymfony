<?php
namespace App\Form\Type;

use App\Entity\Instruction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InstructionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options=[]): void
    {
        $builder
            ->add('description', TextareaType::class, ['label'=>false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Instruction::class,
        ]);
    }

    public function getDefaultOptions(array $options) // disabling csrf protection
    {
        return array(
            'csrf_protection' => false,
        );
    }
}