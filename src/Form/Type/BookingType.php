<?php


namespace App\Form\Type;


use App\Entity\Booking;
use PhpParser\Node\Expr\BinaryOp\Greater;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends \Symfony\Component\Form\AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start_date', DateTimeType::class, [
                'required' => true,
            ])
            ->add('end_date', DateTimeType::class, [
                'required' => true,
            ])
            ->add('save', SubmitType::class, ['label' => 'Make booking!'])
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data-class' => Booking::class,
        ]);
    }
}