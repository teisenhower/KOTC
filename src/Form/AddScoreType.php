<?php

namespace App\Form;

use App\Entity\Stats;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddScoreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentUser = $options['current_user'];
        $builder
            ->add(
                'playerHit',
                EntityType::class,
                [
                    'class' => User::class,
                    /*
                    query building to return all users except the currently logged in user
                    and only the other players in their league
                    */
                    'query_builder' => function (UserRepository $user) use ($currentUser) {
                        return $user->createQueryBuilder('u')
                            ->where('u.league = :id')
                            ->andWhere('u.username != :currentUser')
                            ->setParameters(
                                [
                                    'currentUser' => $currentUser->getUsername(),
                                    'id' => $currentUser->getLeague()
                                ]
                            )
                            ->orderBy('u.username', 'ASC');
                    }
                ]
            )
            ->add('date')
            ->add(
                'type',
                ChoiceType::class,
                [
                    'choices' => [
                        'Battle' => 'Battle',
                        'Stealth' => 'Stealth',
                        'Assist' => 'Assist'
                    ]
                ]
            )
            ->add('Submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Stats::class,
                // supplies us with a custom option that we can pass data through
                'current_user' => null,
            ]
        );
    }
}
