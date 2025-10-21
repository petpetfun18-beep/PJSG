<?php
// src/Form/ShoeType.php
namespace App\Form;

use App\Entity\Shoe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShoeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('brand', TextType::class)
            ->add('colors', TextType::class, [
                'required' => false,
                'help' => 'Enter colors separated by commas, e.g., Red, Blue, Black',
            ])
            ->add('sizes', TextType::class, [
                'required' => false,
                'help' => 'Enter sizes separated by commas, e.g., 6,7,8,9',
            ])
            ->add('price', NumberType::class)
            ->add('image', FileType::class, [
                'label' => 'Shoe Image',
                'required' => false,
                'mapped' => false,
            ])
            // ✅ Category now as a dropdown (ChoiceType)
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Sneakers' => 'Sneakers',
                    'Sandals' => 'Sandals',
                    'Boots' => 'Boots',
                    'Kids' => 'Kids',
                ],
                'placeholder' => 'Select a Category',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);

        // ✅ Transform sizes/colors between string and array
        $builder->get('sizes')->addModelTransformer(new CallbackTransformer(
            fn ($sizesArray) => $sizesArray ? implode(',', $sizesArray) : '',
            fn ($sizesString) => $sizesString ? array_map('trim', explode(',', $sizesString)) : []
        ));

        $builder->get('colors')->addModelTransformer(new CallbackTransformer(
            fn ($colorsArray) => $colorsArray ? implode(',', $colorsArray) : '',
            fn ($colorsString) => $colorsString ? array_map('trim', explode(',', $colorsString)) : []
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Shoe::class]);
    }
}
