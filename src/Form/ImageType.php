<?php


namespace App\Form;


use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('caption', TextType::class, ["label" => "Description of image",
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'form-group'],
                'constraints' => new Length(['min' => 5, 'max' => 700])
            ])
            ->add('file', FileType::class, ["label" => "File of image",
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'form-group'],
                ],
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}